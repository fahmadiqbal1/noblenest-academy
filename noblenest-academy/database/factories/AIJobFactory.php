<?php

namespace Database\Factories;

use App\Models\AIJob;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AIJobFactory extends Factory
{
    protected $model = AIJob::class;

    protected static array $types = [
        'lesson_plan', 'activity', 'translation', 'video_lesson',
        'tts', 'image', 'quiz', 'curriculum_review', 'github_extract',
    ];

    public function definition(): array
    {
        return [
            'type'              => $this->faker->randomElement(static::$types),
            'status'            => 'queued',
            'provider'          => 'mock',
            'locale'            => $this->faker->randomElement(['en', 'ar', 'fr']),
            'user_id'           => User::factory(),
            'payload'           => ['prompt' => $this->faker->sentence()],
            'result'            => null,
            'moderation_status' => 'pending',
            'error_message'     => null,
            'started_at'        => null,
            'completed_at'      => null,
        ];
    }

    public function queued(): static
    {
        return $this->state(fn () => ['status' => 'queued']);
    }

    public function running(): static
    {
        return $this->state(fn () => ['status' => 'running', 'started_at' => now()]);
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status'       => 'completed',
            'result'       => ['content' => 'Generated content'],
            'started_at'   => now()->subMinutes(2),
            'completed_at' => now(),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn () => [
            'status'        => 'failed',
            'error_message' => 'Provider returned 500',
            'started_at'    => now()->subMinutes(1),
            'completed_at'  => now(),
        ]);
    }

    public function pendingModeration(): static
    {
        return $this->state(fn () => [
            'status'            => 'completed',
            'moderation_status' => 'pending',
            'result'            => ['content' => 'Awaiting review'],
            'completed_at'      => now(),
        ]);
    }
}
