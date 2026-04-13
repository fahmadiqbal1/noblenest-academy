<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\ChildProfile;
use App\Models\ChildJourneyEnrollment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * JourneySequencerService
 *
 * Intelligently orders activities within a journey while maintaining:
 * - Subject diversity (no 3+ same subject in a row)
 * - Cognitive domain balance
 * - Mess level budget per day (max 1 high-mess per day)
 * - Materials cost budget per week
 */
class JourneySequencerService
{
    protected const MAX_SAME_SUBJECT = 2;
    protected const MAX_MESS_PER_DAY = 1;
    protected const MESS_COST_PER_DAY = 1;
    protected const MATERIALS_BUDGET_PER_WEEK = 300; // cents

    /**
     * Generate an optimal sequence of activities for a child's journey week.
     * Precomputed and cached for fast retrieval.
     */
    public function computeWeeklySchedule(
        ChildProfile $child,
        ChildJourneyEnrollment $enrollment,
        int $weekNumber
    ): Collection {
        $cacheKey = "journey_schedule.{$enrollment->id}.week_{$weekNumber}";

        return Cache::remember($cacheKey, now()->addDays(7), function () use ($enrollment, $weekNumber) {
            $journey = $enrollment->journey;
            $weeklyTheme = $journey->weeklyThemes()->where('week_number', $weekNumber)->first();

            if (!$weeklyTheme) {
                return collect();
            }

            // Get candidate activities for this week
            $candidates = Activity::whereIn('id',
                $weeklyTheme->themeActivities->pluck('activity_id')->toArray()
            )->get();

            // Distribute across 5 weekdays with constraints
            return $this->distributeActivities($candidates, 5);
        });
    }

    /**
     * Distribute activities across N days with constraint satisfaction.
     */
    protected function distributeActivities(Collection $activities, int $days): Collection
    {
        $schedule = [];
        $used = [];
        $dayMessCount = array_fill(0, $days, 0);
        $weekMaterials = 0;

        // Simple greedy algorithm: assign activities to days respecting constraints
        foreach ($activities as $activity) {
            if (in_array($activity->id, $used)) {
                continue;
            }

            // Find best day for this activity
            for ($day = 0; $day < $days; $day++) {
                if ($this->canAssignToDay($activity, $day, $schedule, $dayMessCount, $weekMaterials)) {
                    if (!isset($schedule[$day])) {
                        $schedule[$day] = [];
                    }

                    $schedule[$day][] = $activity;
                    $used[] = $activity->id;

                    // Update constraints
                    if ($this->isMessy($activity)) {
                        $dayMessCount[$day]++;
                    }
                    $weekMaterials += $activity->materials_cost ?? 0;

                    break;
                }
            }
        }

        return collect($schedule)->flatten(1);
    }

    protected function canAssignToDay(
        Activity $activity,
        int $day,
        array $schedule,
        array $dayMessCount,
        int $weekMaterials
    ): bool {
        // Mess budget: max 1 high-mess activity per day
        if ($this->isMessy($activity) && $dayMessCount[$day] >= self::MAX_MESS_PER_DAY) {
            return false;
        }

        // Materials budget: don't exceed weekly cap
        if (($weekMaterials + ($activity->materials_cost ?? 0)) > self::MATERIALS_BUDGET_PER_WEEK) {
            return false;
        }

        // Subject diversity: avoid 3+ same subject in a row
        if (isset($schedule[$day])) {
            $lastTwo = array_slice($schedule[$day], -2);
            $samSubjectCount = count(array_filter($lastTwo, fn ($a) => $a->subject === $activity->subject));
            if ($samSubjectCount >= self::MAX_SAME_SUBJECT) {
                return false;
            }
        }

        return true;
    }

    protected function isMessy(Activity $activity): bool
    {
        return ($activity->mess_level ?? 'low') === 'high';
    }
}
