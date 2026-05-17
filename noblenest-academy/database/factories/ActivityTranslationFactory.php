<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\ActivityTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ActivityTranslation> */
class ActivityTranslationFactory extends Factory
{
    protected $model = ActivityTranslation::class;

    public function definition(): array
    {
        return [
            'activity_id' => Activity::factory(),
            'locale' => $this->faker->randomElement(['ar', 'fr', 'ur', 'es']),
            'field' => $this->faker->randomElement(['title', 'description']),
            'value' => $this->faker->sentence(),
        ];
    }
}
