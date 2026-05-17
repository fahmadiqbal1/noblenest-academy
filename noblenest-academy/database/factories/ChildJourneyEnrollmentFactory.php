<?php
namespace Database\Factories;
use App\Models\ChildJourneyEnrollment;
use App\Models\ChildProfile;
use App\Models\ThematicJourney;
use Illuminate\Database\Eloquent\Factories\Factory;
/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ChildJourneyEnrollment> */
class ChildJourneyEnrollmentFactory extends Factory
{
    protected $model = ChildJourneyEnrollment::class;
    public function definition(): array
    {
        return [
            'child_profile_id' => ChildProfile::factory(),
            'journey_id'       => ThematicJourney::factory(),
            'current_week'     => $this->faker->numberBetween(1, 4),
            'started_at'       => now()->subDays(7),
            'completed_at'     => null,
            'is_active'        => true,
        ];
    }
}
