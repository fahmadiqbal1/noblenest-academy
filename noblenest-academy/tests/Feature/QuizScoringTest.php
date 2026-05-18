<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Regression guards for Q2 (essay answers must not be scored a false 0%)
 * and Q3 (reload must not create a duplicate attempt).
 */
class QuizScoringTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function essay_only_quiz_reports_pending_not_zero_percent(): void
    {
        $quiz = Quiz::create(['title' => 'Essay Quiz', 'description' => 'x']);
        Question::create([
            'quiz_id' => $quiz->id,
            'question_text' => 'Describe your day.',
            'type' => 'long',
        ]);

        $res = $this->post("/quizzes/{$quiz->id}/submit", [
            'answers' => [],
        ])->assertOk();

        // total (auto-gradable) must be 0 -> no misleading "0 / 1 (0%)".
        $res->assertSee('pending review');
        $res->assertDontSee('(0%)');
    }

    #[Test]
    public function reload_does_not_create_duplicate_attempt(): void
    {
        $user = User::factory()->create(['role' => 'Parent']);
        $quiz = Quiz::create(['title' => 'Dup Quiz', 'description' => 'x']);
        Question::create([
            'quiz_id' => $quiz->id,
            'question_text' => '1+1?',
            'type' => 'short',
        ]);

        $this->actingAs($user)->post("/quizzes/{$quiz->id}/submit", ['answers' => []])->assertOk();
        $this->actingAs($user)->post("/quizzes/{$quiz->id}/submit", ['answers' => []])->assertOk();

        $this->assertSame(
            1,
            QuizAttempt::where('quiz_id', $quiz->id)->where('user_id', $user->id)->count(),
            'a quick re-submit must reuse the prior attempt, not duplicate it'
        );
    }
}
