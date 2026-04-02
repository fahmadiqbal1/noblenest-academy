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
        $dueMilestones = Milestone::where('typical_age_months', '<=', $ageMonths)
            ->orderBy('typical_age_months')
            ->get();

        // Already achieved milestone IDs
        $achieved = $child->achievements()
            ->where('achievable_type', Milestone::class)
            ->pluck('achievable_id')
            ->toArray();

        $newlyUnlocked = [];

        foreach ($dueMilestones as $milestone) {
            if (in_array($milestone->id, $achieved, true)) {
                continue;
            }

            // Auto-award milestones where activity completion threshold is met
            if ($this->hasMet($child, $milestone)) {
                $child->achievements()->create([
                    'achievable_type' => Milestone::class,
                    'achievable_id'   => $milestone->id,
                    'achieved_at'     => now(),
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
     * Simple heuristic: count completed activities in the milestone's domain.
     */
    private function hasMet(ChildProfile $child, Milestone $milestone): bool
    {
        // If milestone has no domain, just age-gate it
        if (empty($milestone->domain)) {
            return true;
        }

        $completed = ChildActivityProgress::where('child_profile_id', $child->id)
            ->whereHas('activity', function ($q) use ($milestone) {
                $q->where('subject', $milestone->domain);
            })
            ->where('completed', true)
            ->count();

        return $completed >= 3; // threshold: 3 completed activities per domain
    }

    /**
     * Award streak / activity-count badges.
     */
    private function checkBadges(ChildProfile $child): array
    {
        $totalCompleted = ChildActivityProgress::where('child_profile_id', $child->id)
            ->where('completed', true)
            ->count();

        $streak = $child->streak_days ?? 0;

        $newBadges = [];

        $candidates = Badge::all();

        $alreadyHas = $child->achievements()
            ->where('achievable_type', Badge::class)
            ->pluck('achievable_id')
            ->toArray();

        foreach ($candidates as $badge) {
            if (in_array($badge->id, $alreadyHas, true)) {
                continue;
            }

            $criteria = $badge->criteria ?? [];

            $earned = match ($badge->badge_type) {
                'streak'    => isset($criteria['days']) && $streak >= $criteria['days'],
                'activity'  => isset($criteria['count']) && $totalCompleted >= $criteria['count'],
                'milestone' => false, // handled by milestone flow above
                default     => false,
            };

            if ($earned) {
                $child->achievements()->create([
                    'achievable_type' => Badge::class,
                    'achievable_id'   => $badge->id,
                    'achieved_at'     => now(),
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

        $achieved = $child->achievements()
            ->where('achievable_type', Milestone::class)
            ->pluck('achievable_id');

        return Milestone::whereNotIn('id', $achieved)
            ->where('typical_age_months', '>=', $ageMonths)
            ->orderBy('typical_age_months')
            ->limit(3)
            ->get();
    }
}
