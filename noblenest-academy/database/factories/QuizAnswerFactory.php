<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\QuizAnswer;
use App\Models\QuizAttempt;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<QuizAnswer> */
class QuizAnswerFactory extends Factory
{
    protected $model = QuizAnswer::class;

    public function definition(): array
    {
        return [
            'quiz_attempt_id' => QuizAttempt::factory(),
            'question_id' => Question::factory(),
            'option_id' => null,
            'answer_text' => $this->faker->sentence(),
        ];
    }
}
