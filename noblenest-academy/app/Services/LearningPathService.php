<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\ChildActivityProgress;
use App\Models\ChildProfile;
use Illuminate\Support\Collection;

/**
 * LearningPathService
 *
 * Generates a personalised learning path for a child based on:
 * - Age tier
 * - Completed vs. pending activities
 * - Subject performance gaps (where ≤ 50% completion rate)
 * - Streak momentum (prefer subjects with activity)
 */
class LearningPathService
{
    private const DAILY_ACTIVITY_TARGET = 3;

    /**
     * Build a sequenced learning path of up to $limit activities for today.
     */
    public function buildDailyPath(ChildProfile $child, int $limit = 6): Collection
    {
        $ageMonths = $child->age_months ?? 0;
        $ageTier   = $this->ageTier($ageMonths);

        // Activities the child has already completed
        $completedIds = ChildActivityProgress::where('child_profile_id', $child->id)
            ->where('completed', true)
            ->pluck('activity_id')
            ->toArray();

        // Candidate pool: age-appropriate, not yet completed, published
        $candidates = Activity::where('age_tier', $ageTier)
            ->where('published', true)
            ->whereNotIn('id', $completedIds)
            ->get();

        if ($candidates->isEmpty()) {
            // Fall back to all age-appropriate (allow repeats)
            $candidates = Activity::where('age_tier', $ageTier)
                ->where('published', true)
                ->get();
        }

        // Score each activity
        $gaps = $this->subjectGaps($child, $ageMonths);

        $scored = $candidates->map(function (Activity $activity) use ($gaps) {
            $score = 0;

            // Prioritise subjects with gaps
            if (in_array($activity->subject, $gaps, true)) {
                $score += 10;
            }

            // Prioritise free activities first (lower friction)
            if ($activity->is_free) {
                $score += 3;
            }

            // Add slight randomness for variety
            $score += rand(0, 4);

            return ['activity' => $activity, 'score' => $score];
        })->sortByDesc('score');

        return $scored->take($limit)->pluck('activity')->values();
    }

    /**
     * Return subjects where the child's completion rate is below 50%.
     */
    public function subjectGaps(ChildProfile $child, ?int $ageMonths = null): array
    {
        $ageMonths = $ageMonths ?? $child->age_months ?? 0;
        $tier      = $this->ageTier($ageMonths);

        $subjectCounts = Activity::where('age_tier', $tier)
            ->where('published', true)
            ->selectRaw('subject, COUNT(*) as total')
            ->groupBy('subject')
            ->pluck('total', 'subject');

        $completedCounts = ChildActivityProgress::where('child_profile_id', $child->id)
            ->where('completed', true)
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

        $total = Activity::where('age_tier', $tier)->where('published', true)->count();
        $completed = ChildActivityProgress::where('child_profile_id', $child->id)
            ->where('completed', true)
            ->count();

        $subjectBreakdown = ChildActivityProgress::where('child_profile_id', $child->id)
            ->where('completed', true)
            ->join('activities', 'activities.id', '=', 'child_activity_progress.activity_id')
            ->selectRaw('activities.subject, COUNT(*) as count')
            ->groupBy('activities.subject')
            ->pluck('count', 'subject')
            ->toArray();

        return [
            'total'             => $total,
            'completed'         => $completed,
            'pct'               => $total > 0 ? round(($completed / $total) * 100) : 0,
            'streak'            => $child->streak_days ?? 0,
            'subject_breakdown' => $subjectBreakdown,
            'gaps'              => $this->subjectGaps($child, $ageMonths),
        ];
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
