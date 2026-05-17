<?php
namespace Database\Factories;
use App\Models\ChildSkillState;
use App\Models\ChildProfile;
use Illuminate\Database\Eloquent\Factories\Factory;
/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ChildSkillState> */
class ChildSkillStateFactory extends Factory
{
    protected $model = ChildSkillState::class;
    public function definition(): array
    {
        return [
            'child_profile_id'    => ChildProfile::factory(),
            'cognitive_domain'    => $this->faker->randomElement(['math', 'language', 'science', 'art', 'music']),
            'developmental_domain' => $this->faker->randomElement(['gross_motor', 'fine_motor', 'cognitive', 'social_emotional']),
            'ema_score'           => $this->faker->randomFloat(3, 0, 1),
            'ema_confidence'      => $this->faker->randomFloat(3, 0, 1),
            'streak_success'      => $this->faker->numberBetween(0, 5),
            'streak_struggle'     => $this->faker->numberBetween(0, 5),
            'max_streak_struggle' => $this->faker->numberBetween(0, 5),
            'total_attempts'      => $this->faker->numberBetween(0, 20),
            'successful_attempts' => $this->faker->numberBetween(0, 20),
            'last_success'        => now()->subDay(),
            'last_struggle'       => now()->subDays(2),
            'last_updated'        => now(),
        ];
    }
}
