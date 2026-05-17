<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Question> */
class QuestionFactory extends Factory
{
    protected $model = Question::class;

    public function definition(): array
    {
        return [
            'quiz_id' => Quiz::factory(),
            'question_text' => $this->faker->sentence().'?',
            'type' => $this->faker->randomElement(['single', 'multiple', 'short', 'long']),
            'order' => $this->faker->numberBetween(0, 20),
        ];
    }
}
