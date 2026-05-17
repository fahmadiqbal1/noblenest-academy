<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\ChildActivityProgress;
use App\Models\ChildProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ChildActivityProgress> */
class ChildActivityProgressFactory extends Factory
{
    protected $model = ChildActivityProgress::class;

    public function definition(): array
    {
        return [
            'child_profile_id' => ChildProfile::factory(),
            'activity_id' => Activity::factory(),
            'status' => $this->faker->randomElement(['not_started', 'in_progress', 'completed']),
            'score' => $this->faker->numberBetween(0, 100),
            'time_spent' => $this->faker->numberBetween(0, 600),
            'attempts' => $this->faker->numberBetween(0, 5),
            'trace_data' => [],
            'drawing_path' => null,
            'started_at' => now()->subMinutes(10),
            'completed_at' => null,
        ];
    }
}
