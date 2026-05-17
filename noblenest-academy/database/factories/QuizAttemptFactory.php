<?php
namespace Database\Factories;
use App\Models\QuizAttempt;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QuizAttempt> */
class QuizAttemptFactory extends Factory
{
    protected $model = QuizAttempt::class;
    public function definition(): array
    {
        return [
            'quiz_id'      => Quiz::factory(),
            'user_id'      => User::factory(),
            'score'        => $this->faker->numberBetween(0, 100),
            'completed_at' => now(),
        ];
    }
}
