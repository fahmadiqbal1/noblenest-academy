<?php

namespace Database\Factories;

use App\Models\ChildProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChildProfileFactory extends Factory
{
    protected $model = ChildProfile::class;

    public function definition(): array
    {
        $ageMonths = $this->faker->numberBetween(0, 120);

        return [
            'parent_id' => User::factory(),
            'name' => $this->faker->firstName(),
            'nickname' => $this->faker->optional(0.5)->firstName(),
            'date_of_birth' => now()->subMonths($ageMonths)->startOfDay(),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'is_muslim' => $this->faker->boolean(70),
            'preferred_language' => $this->faker->randomElement(['en', 'ar', 'fr', 'ur']),
            'avatar_url' => null,
            'preferences' => [],
            'learning_goals' => [],
        ];
    }

    /** Age 0-12 months. */
    public function infant(): static
    {
        return $this->state(fn () => [
            'date_of_birth' => now()->subMonths($this->faker->numberBetween(0, 12)),
        ]);
    }

    /** Age 1-3 years. */
    public function toddler(): static
    {
        return $this->state(fn () => [
            'date_of_birth' => now()->subMonths($this->faker->numberBetween(12, 36)),
        ]);
    }

    /** Age 3-5 years. */
    public function preschool(): static
    {
        return $this->state(fn () => [
            'date_of_birth' => now()->subMonths($this->faker->numberBetween(36, 60)),
        ]);
    }

    /** Age 5-10 years. */
    public function schoolAge(): static
    {
        return $this->state(fn () => [
            'date_of_birth' => now()->subMonths($this->faker->numberBetween(60, 120)),
        ]);
    }
}
