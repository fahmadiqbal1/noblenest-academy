<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\ActivityStep;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ActivityStep> */
class ActivityStepFactory extends Factory
{
    protected $model = ActivityStep::class;

    public function definition(): array
    {
        return [
            'activity_id' => Activity::factory(),
            'step_number' => $this->faker->numberBetween(1, 10),
            'title' => $this->faker->sentence(3),
            'instruction' => $this->faker->paragraph(),
            'visual_url' => $this->faker->optional()->url(),
            'video_url' => $this->faker->optional()->url(),
            'audio_url' => $this->faker->optional()->url(),
            'duration_seconds' => $this->faker->numberBetween(10, 300),
            'benefit_note' => $this->faker->optional()->sentence(),
        ];
    }
}
