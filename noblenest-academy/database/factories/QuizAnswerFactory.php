<?php
namespace Database\Factories;
use App\Models\QuizAnswer;
use App\Models\QuizAttempt;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;
/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QuizAnswer> */
class QuizAnswerFactory extends Factory
{
    protected $model = QuizAnswer::class;
    public function definition(): array
    {
        return [
            'quiz_attempt_id' => QuizAttempt::factory(),
            'question_id'     => Question::factory(),
            'option_id'       => null,
            'answer_text'     => $this->faker->sentence(),
        ];
    }
}
