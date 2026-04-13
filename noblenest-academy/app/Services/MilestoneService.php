<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\ChildActivityProgress;
use App\Models\ChildProfile;
use App\Models\Milestone;
use Illuminate\Support\Collection;

class MilestoneService
{
    /**
     * Evaluate a child's progress and auto-award any newly achieved milestones.
     * Returns newly unlocked milestones and badges.
     */
    public function evaluate(ChildProfile $child): array
    {
        $ageMonths = $child->age_months ?? 0;

        // Milestones due at or before child's current age
        $dueMilestones = Milestone::where('age_months_min', '<=', $ageMonths)
            ->orderBy('age_months_min')
            ->get();

        // Already achieved milestone IDs (via child_milestone_progress pivot)
        $achieved = $child->milestones()->wherePivot('status', 'achieved')->pluck('milestones.id')->toArray();

        $newlyUnlocked = [];

        foreach ($dueMilestones as $milestone) {
            if (in_array($milestone->id, $achieved, true)) {
                continue;
            }

            // Auto-award milestones where activity completion threshold is met
            if ($this->hasMet($child, $milestone)) {
                $child->milestones()->syncWithoutDetaching([
                    $milestone->id => [
                        'status'      => 'achieved',
                        'achieved_at' => now()->toDateTimeString(),
                    ],
                ]);
                $newlyUnlocked[] = $milestone;
            }
        }

        // Check for streak/activity badges
        $newBadges = $this->checkBadges($child);

        return [
            'milestones' => $newlyUnlocked,
            'badges'     => $newBadges,
        ];
    }

    /**
     * Determine if a child has met the threshold for a given milestone.
     *
     * Domain-specific thresholds (research-backed):
     * - executive_function: 5 completions (strongest predictor of academic success — Diamond, 2013)
     * - emotional_regulation: 5 completions (critical for social development)
     * - metacognition: 5 completions (advanced cognitive skill, needs repeated practice)
     * - Standard domains: 3 completions
     */
    private function hasMet(ChildProfile $child, Milestone $milestone): bool
    {
        // If milestone has no domain, just age-gate it
        if (empty($milestone->domain)) {
            return true;
        }

        // Domain-specific thresholds
        $threshold = match ($milestone->domain) {
            'executive_function',
            'emotional_regulation',
            'metacognition',
            'mental_arithmetic',
            'focus_attention'       => 5,
            default                 => 3,
        };

        // First: try matching by subject (traditional path)
        $completedBySubject = ChildActivityProgress::where('child_profile_id', $child->id)
            ->whereHas('activity', function ($q) use ($milestone) {
                $q->where('subject', $milestone->domain);
            })
            ->whereNotNull('completed_at')
            ->count();

        if ($completedBySubject >= $threshold) {
            return true;
        }

        // Second: try matching by cognitive_domain (Phase 5 enhanced path)
        // This catches executive function/emotional regulation activities tagged
        // with cognitive_domain rather than subject
        $completedByCognitiveDomain = ChildActivityProgress::where('child_profile_id', $child->id)
            ->whereHas('activity', function ($q) use ($milestone) {
                $q->where('cognitive_domain', $milestone->domain);
            })
            ->whereNotNull('completed_at')
            ->count();

        if ($completedByCognitiveDomain >= $threshold) {
            return true;
        }

        // Third: try matching by developmental_domains JSON array
        // Activities can target multiple domains (e.g., an activity tagged
        // with developmental_domains: ["executive_function", "fine_motor"])
        $completedByDevDomains = ChildActivityProgress::where('child_profile_id', $child->id)
            ->whereHas('activity', function ($q) use ($milestone) {
                $q->whereJsonContains('developmental_domains', $milestone->domain);
            })
            ->whereNotNull('completed_at')
            ->count();

        return ($completedBySubject + $completedByCognitiveDomain + $completedByDevDomains) >= $threshold;
    }

    /**
     * Award streak / activity-count badges.
     */
    private function checkBadges(ChildProfile $child): array
    {
        $totalCompleted = ChildActivityProgress::where('child_profile_id', $child->id)
            ->whereNotNull('completed_at')
            ->count();

        $streak = $child->streak_days ?? 0;

        $newBadges = [];

        $candidates = Badge::all();

        // Already earned badge IDs (via child_badges pivot)
        $alreadyHas = $child->badges()->pluck('badges.id')->toArray();

        foreach ($candidates as $badge) {
            if (in_array($badge->id, $alreadyHas, true)) {
                continue;
            }

            $criteria = $badge->criteria ?? [];

            $earned = match ($badge->badge_type) {
                'streak'    => isset($criteria['days']) && $streak >= $criteria['days'],
                'activity'  => isset($criteria['count']) && $totalCompleted >= $criteria['count'],
                'milestone' => false,
                default     => false,
            };

            if ($earned) {
                $child->badges()->syncWithoutDetaching([
                    $badge->id => ['awarded_at' => now()->toDateTimeString()],
                ]);
                $newBadges[] = $badge;
            }
        }

        return $newBadges;
    }

    /**
     * Return the next 3 milestone targets for a given child (for display in parent dashboard).
     */
    public function nextTargets(ChildProfile $child): Collection
    {
        $ageMonths = $child->age_months ?? 0;

        $achieved = $child->milestones()->pluck('milestones.id');

        return Milestone::whereNotIn('id', $achieved)
            ->where('age_months_min', '>=', $ageMonths)
            ->orderBy('age_months_min')
            ->limit(3)
            ->get();
    }
}
