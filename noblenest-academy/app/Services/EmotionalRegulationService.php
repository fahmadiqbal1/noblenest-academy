<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\ChildProfile;
use App\Models\ChildSkillState;
use Illuminate\Support\Collection;

/**
 * EmotionalRegulationService
 *
 * Detects when a child needs emotional regulation support and recommends
 * calming/regulation activities (breathing, naming feelings, co-regulation, etc.)
 */
class EmotionalRegulationService
{
    protected const STRUGGLE_THRESHOLD = 2;  // Struggle streak >= 2
    protected const TIME_SINCE_SUCCESS = 120; // Minutes since last success

    /**
     * Should we inject an EI activity for this child?
     *
     * Returns true if child shows signs of frustration/struggle.
     */
    public function shouldInjectEIActivity(ChildProfile $child): bool
    {
        // Check for struggle streaks
        $strugglingSkills = ChildSkillState::where('child_profile_id', $child->id)
            ->where('streak_struggle', '>=', self::STRUGGLE_THRESHOLD)
            ->count();

        if ($strugglingSkills > 0) {
            return true;
        }

        // Check for recent abandonment (no success in last 2 hours)
        $recentSuccess = ChildSkillState::where('child_profile_id', $child->id)
            ->where('last_success', '>=', now()->subMinutes(self::TIME_SINCE_SUCCESS))
            ->count();

        return $recentSuccess === 0;
    }

    /**
     * Get recommended EI activity for this child.
     * Picks a calming/regulation activity appropriate to their age.
     */
    public function getRecommendedEIActivity(ChildProfile $child): ?Activity
    {
        $ageTier = $this->getAgeTier($child->age_months ?? 0);

        // Find EI activities matching age tier
        $eiActivity = Activity::where('cognitive_domain', 'emotional_regulation')
            ->where('age_tier', $ageTier)
            ->where('difficulty', '!=', 'hard') // Prefer easier regulation activities
            ->inRandomOrder()
            ->first();

        return $eiActivity;
    }

    /**
     * Get a collection of EI activities for a given age tier.
     */
    public function getEIActivitiesByTier(string $ageTier, int $limit = 5): Collection
    {
        return Activity::where('cognitive_domain', 'emotional_regulation')
            ->where('age_tier', $ageTier)
            ->limit($limit)
            ->get();
    }

    private function getAgeTier(int $ageMonths): string
    {
        return match (true) {
            $ageMonths < 24 => 'baby',
            $ageMonths < 48 => 'toddler',
            $ageMonths < 72 => 'preschool',
            default         => 'school',
        };
    }
}
