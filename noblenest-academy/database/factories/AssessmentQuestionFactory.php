<?php

namespace Database\Factories;

use App\Models\AssessmentQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<AssessmentQuestion> */
class AssessmentQuestionFactory extends Factory
{
    protected $model = AssessmentQuestion::class;

    public function definition(): array
    {
        return [
            'battery' => 'discovery',
            'sequence' => $this->faker->unique()->numberBetween(1, 1000),
            'age_min_months' => $this->faker->optional()->numberBetween(0, 60),
            'age_max_months' => $this->faker->optional()->numberBetween(60, 120),
            'prompt' => $this->faker->sentence(),
            'options' => [
                ['label' => 'Building things', 'dimensions' => ['kinetic' => 2, 'creative' => 1]],
                ['label' => 'Reading stories', 'dimensions' => ['linguistic' => 2]],
            ],
        ];
    }
}
