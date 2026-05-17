<?php

namespace Database\Factories;

use App\Models\Badge;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Badge> */
class BadgeFactory extends Factory
{
    protected $model = Badge::class;

    public function definition(): array
    {
        return [
            'slug' => $this->faker->unique()->slug(2),
            'name' => $this->faker->words(2, true),
            'emoji' => '🏅',
            'description' => $this->faker->sentence(),
            'icon_url' => $this->faker->optional()->url(),
            'badge_type' => $this->faker->randomElement(['milestone', 'streak', 'course', 'social', 'special', 'subject']),
            'criteria' => ['type' => 'activities_completed', 'value' => 5],
            'required_value' => $this->faker->numberBetween(1, 10),
            'is_active' => true,
        ];
    }
}
