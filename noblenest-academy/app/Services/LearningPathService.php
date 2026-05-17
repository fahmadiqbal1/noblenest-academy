<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\ChildActivityProgress;
use App\Models\ChildJourneyEnrollment;
use App\Models\ChildProfile;
use App\Models\ChildSkillState;
use App\Models\ThematicJourney;
use App\Models\ThemeActivity;
use App\Services\EmotionalRegulationService;
use Illuminate\Support\Collection;

/**
 * LearningPathService
 *
 * Generates a personalised learning path for a child based on:
 * - Age tier
 * - Completed vs. pending activities
 * - Subject performance gaps (where ≤ 50% completion rate)
 * - Streak momentum (prefer subjects with activity)
 * - Thematic journey enrollment (cross-curricular daily path)
 * - Kumon-style mastery tracking (repeat until ≥80% quality)
 * - Cognitive domain prioritisation (executive function weighting)
 */
class LearningPathService
{
    private const DAILY_ACTIVITY_TARGET = 3;

    /** Mastery threshold — child must hit 80% before advancing (Kumon method) */
    private const MASTERY_THRESHOLD = 0.8;

    /** Executive function domains that get extra scoring weight */
    private const EF_DOMAINS = [
        'working_memory', 'inhibitory_control', 'cognitive_flexibility',
        'attention', 'metacognition', 'sequential_thinking',
    ];

    /**
     * Build a sequenced learning path of up to $limit activities for today.
     *
     * If the child is enrolled in a thematic journey, the first activities
     * are pulled from the journey's current week. Remaining slots are filled
     * from the general pool with gap/mastery-aware scoring.
     */
    public function buildDailyPath(ChildProfile $child, int $limit = 6): Collection
    {
        $ageMonths = $child->age_months ?? 0;
        $ageTier   = $this->ageTier($ageMonths);

        // 1. Check for active thematic journey enrollment
        $journeyActivities = $this->buildThematicDailyPath($child, $ageTier);

        // Reserve slots for journey activities, fill the rest from general pool
        $journeySlots  = min($journeyActivities->count(), (int) ceil($limit * 0.6));
        $journeyPick   = $journeyActivities->take($journeySlots);
        $journeyIds    = $journeyPick->pluck('id')->toArray();
        $generalSlots  = $limit - $journeyPick->count();

        // 2. Activities the child has already completed
        $completedIds = ChildActivityProgress::where('child_profile_id', $child->id)
            ->whereNotNull('completed_at')
            ->pluck('activity_id')
            ->toArray();

        // 3. Candidate pool: age-appropriate, not yet completed, not in journey selection
        $excludeIds = array_merge($completedIds, $journeyIds);
        $candidates = Activity::where('age_tier', $ageTier)
            ->whereNotIn('id', $excludeIds)
            ->get();

        if ($candidates->isEmpty() && $generalSlots > 0) {
            // Allow repeats but still exclude today's journey activities
            $candidates = Activity::where('age_tier', $ageTier)
                ->whereNotIn('id', $journeyIds)
                ->get();
        }

        // 4. Score each candidate, with adaptive adjustments based on ChildSkillState
        $gaps = $this->subjectGaps($child, $ageMonths);
        $skillStates = $this->getChildSkillStates($child);

        $scored = $candidates->map(function (Activity $activity) use ($gaps, $child, $skillStates) {
            $score = 0;

            // Prioritise subjects with gaps
            if (in_array($activity->subject, $gaps, true)) {
                $score += 10;
            }

            // Adaptive: check ChildSkillState for this cognitive domain
            if ($activity->cognitive_domain && isset($skillStates[$activity->cognitive_domain])) {
                $skillState = $skillStates[$activity->cognitive_domain];

                // Struggling (EMA < 0.5): high priority to repeat
                if ($skillState->isStruggling()) {
                    $score += 20;
                } // Mastered (EMA >= 0.8): lower priority (challenge next)
                elseif (!$skillState->isMastered()) {
                    // Middle ground: still need work
                    $score += 10;
                }

                // Streak detection: if child has a struggle streak, boost remedial activities
                if ($skillState->hasStruggleStreak()) {
                    $score += 15;
                }
            } else {
                // No skill state yet: use legacy Kumon method
                if ($activity->cognitive_domain) {
                    $masteryScore = $this->getMasteryScore($child, $activity);
                    if ($masteryScore < self::MASTERY_THRESHOLD) {
                        $score += 15;
                    }
                }
            }

            // Executive function domain boost
            if (in_array($activity->cognitive_domain, self::EF_DOMAINS, true)) {
                $score += 5;
            }

            // Prioritise free activities first (lower friction)
            if ($activity->is_free) {
                $score += 3;
            }

            // Add slight randomness for variety
            $score += rand(0, 4);

            return ['activity' => $activity, 'score' => $score];
        })->sortByDesc('score');

        $generalPick = $scored->take($generalSlots)->pluck('activity');

        // 5. Check for emotional regulation triggers
        if (config('features.phase1_emotional_intel', false)) {
            $eiService = app(EmotionalRegulationService::class);
            if ($eiService->shouldInjectEIActivity($child)) {
                $eiActivity = $eiService->getRecommendedEIActivity($child);
                if ($eiActivity) {
                    // Inject EI activity as first general activity (after journey activities)
                    $generalPick = collect([$eiActivity])->concat($generalPick->take($generalSlots - 1));
                }
            }
        }

        // 6. Merge: journey activities first, then general pool (including optional EI)
        return $journeyPick->concat($generalPick)->values();
    }

    /**
     * Build a daily path from the child's active thematic journey.
     *
     * Pulls activities from the current week of the enrolled journey,
     * filtered to today's day-of-week slot.
     */
    public function buildThematicDailyPath(ChildProfile $child, ?string $ageTier = null): Collection
    {
        $ageTier = $ageTier ?? $this->ageTier($child->age_months ?? 0);

        // Find active enrollment
        $enrollment = ChildJourneyEnrollment::where('child_profile_id', $child->id)
            ->where('is_active', true)
            ->whereHas('journey', fn ($q) => $q->where('age_tier', $ageTier)->where('is_published', true))
            ->with('journey')
            ->first();

        if (!$enrollment) {
            return collect();
        }

        $journey     = $enrollment->journey;
        $currentWeek = $enrollment->current_week ?? 1;
        $dayOfWeek   = (int) now()->format('N'); // 1=Mon, 7=Sun

        // Get the weekly theme for the current week
        $weeklyTheme = $journey->weeklyThemes()
            ->where('week_number', $currentWeek)
            ->first();

        if (!$weeklyTheme) {
            return collect();
        }

        // Get activities scheduled for today in this theme
        $themeActivities = ThemeActivity::where('weekly_theme_id', $weeklyTheme->id)
            ->where('day_of_week', $dayOfWeek)
            ->orderBy('sort_order')
            ->with('activity')
            ->get()
            ->pluck('activity')
            ->filter(); // Remove nulls if activity was deleted

        return $themeActivities;
    }

    /**
     * Calculate a child's mastery score for a given activity (0.0 – 1.0).
     *
     * Mastery is measured as: (successful completions) / (total attempts).
     * A score below MASTERY_THRESHOLD triggers re-presentation (Kumon method).
     */
    public function getMasteryScore(ChildProfile $child, Activity $activity): float
    {
        $progress = ChildActivityProgress::where('child_profile_id', $child->id)
            ->where('activity_id', $activity->id)
            ->first();

        if (!$progress || ($progress->attempts ?? 0) === 0) {
            return 0.0; // Never attempted — needs presentation
        }

        // Score-based mastery: normalize to 0-1 range
        // If score is recorded (0-100), use it directly
        if ($progress->score !== null && $progress->score >= 0) {
            return min($progress->score / 100, 1.0);
        }

        // Completion-based fallback: completed_at is set = 1 attempt success
        $completed = $progress->completed_at ? 1 : 0;

        return $completed / max($progress->attempts, 1);
    }

    /**
     * Return subjects where the child's completion rate is below 50%.
     */
    public function subjectGaps(ChildProfile $child, ?int $ageMonths = null): array
    {
        $ageMonths = $ageMonths ?? $child->age_months ?? 0;
        $tier      = $this->ageTier($ageMonths);

        $subjectCounts = Activity::where('age_tier', $tier)
            ->selectRaw('subject, COUNT(*) as total')
            ->groupBy('subject')
            ->pluck('total', 'subject');

        $completedCounts = ChildActivityProgress::where('child_profile_id', $child->id)
            ->whereNotNull('completed_at')
            ->whereHas('activity', fn ($q) => $q->where('age_tier', $tier))
            ->join('activities', 'activities.id', '=', 'child_activity_progress.activity_id')
            ->selectRaw('activities.subject, COUNT(*) as done')
            ->groupBy('activities.subject')
            ->pluck('done', 'subject');

        $gaps = [];
        foreach ($subjectCounts as $subject => $total) {
            $done = $completedCounts[$subject] ?? 0;
            if ($total > 0 && ($done / $total) < 0.5) {
                $gaps[] = $subject;
            }
        }

        return $gaps;
    }

    /**
     * Generate a progress summary for the parent dashboard.
     */
    public function progressSummary(ChildProfile $child): array
    {
        $ageMonths = $child->age_months ?? 0;
        $tier      = $this->ageTier($ageMonths);

        $total = Activity::where('age_tier', $tier)->count();
        $completed = ChildActivityProgress::where('child_profile_id', $child->id)
            ->whereNotNull('completed_at')
            ->count();

        $subjectBreakdown = ChildActivityProgress::where('child_profile_id', $child->id)
            ->whereNotNull('completed_at')
            ->join('activities', 'activities.id', '=', 'child_activity_progress.activity_id')
            ->selectRaw('activities.subject, COUNT(*) as count')
            ->groupBy('activities.subject')
            ->pluck('count', 'subject')
            ->toArray();

        // Cognitive domain breakdown
        $cognitiveDomainBreakdown = ChildActivityProgress::where('child_profile_id', $child->id)
            ->whereNotNull('completed_at')
            ->join('activities', 'activities.id', '=', 'child_activity_progress.activity_id')
            ->whereNotNull('activities.cognitive_domain')
            ->selectRaw('activities.cognitive_domain, COUNT(*) as count')
            ->groupBy('activities.cognitive_domain')
            ->pluck('count', 'cognitive_domain')
            ->toArray();

        // Active journey info
        $activeJourney = ChildJourneyEnrollment::where('child_profile_id', $child->id)
            ->where('is_active', true)
            ->with('journey:id,title,emoji,total_weeks')
            ->first();

        return [
            'total'                    => $total,
            'completed'                => $completed,
            'pct'                      => $total > 0 ? round(($completed / $total) * 100) : 0,
            'streak'                   => $child->streak_days ?? 0,
            'subject_breakdown'        => $subjectBreakdown,
            'cognitive_domain_breakdown' => $cognitiveDomainBreakdown,
            'gaps'                     => $this->subjectGaps($child, $ageMonths),
            'active_journey'           => $activeJourney ? [
                'title'        => $activeJourney->journey->title ?? null,
                'emoji'        => $activeJourney->journey->emoji ?? null,
                'current_week' => $activeJourney->current_week ?? 1,
                'total_weeks'  => $activeJourney->journey->total_weeks ?? 0,
            ] : null,
        ];
    }

    /**
     * Get all ChildSkillState records for this child, indexed by cognitive_domain.
     * Used to make adaptive recommendations based on mastery and streaks.
     *
     * @return \Illuminate\Support\Collection<string, ChildSkillState>
     */
    private function getChildSkillStates(ChildProfile $child): \Illuminate\Support\Collection
    {
        return ChildSkillState::where('child_profile_id', $child->id)
            ->get()
            ->keyBy('cognitive_domain');
    }

    private function ageTier(int $ageMonths): string
    {
        return match (true) {
            $ageMonths < 24 => 'baby',
            $ageMonths < 48 => 'toddler',
            $ageMonths < 72 => 'preschool',
            default         => 'school',
        };
    }
}
