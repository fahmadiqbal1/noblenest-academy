<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class QuizController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::orderBy('created_at', 'desc')->paginate(12);

        return view('quizzes.index', compact('quizzes'));
    }

    public function show(Quiz $quiz)
    {
        $quiz->load('questions.options');

        return view('quizzes.take', compact('quiz'));
    }

    public function submit(Request $request, Quiz $quiz)
    {
        $quiz->load('questions.options');
        $user = Auth::user();
        $data = $request->validate([
            'answers' => 'array',
        ]);

        // Q3 — idempotency: a reload / double-click must not create a second
        // attempt. If this user submitted the same quiz seconds ago, return
        // that result instead of inserting a duplicate. (Guests have no
        // stable key, so the guard only applies to authenticated users.)
        if ($user) {
            $recent = QuizAttempt::where('quiz_id', $quiz->id)
                ->where('user_id', $user->id)
                ->where('created_at', '>=', now()->subSeconds(10))
                ->latest('id')
                ->first();
            if ($recent) {
                return $this->resultView($quiz, $recent);
            }
        }

        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => $user ? $user->id : null,
            'completed_at' => now(),
        ]);
        $score = 0;
        $total = 0;       // auto-gradable questions only
        $ungraded = 0;    // short/long answers — pending manual review
        foreach ($quiz->questions as $q) {
            $ans = $data['answers'][$q->id] ?? null;
            if (in_array($q->type, ['single', 'multiple'])) {
                $correct = $q->options->where('is_correct', true)->pluck('id')->sort()->values();
                $userAns = collect((array) $ans)->map(fn ($v) => (int) $v)->sort()->values();
                $isCorrect = $userAns->count() && $userAns->toArray() === $correct->toArray();
                QuizAnswer::create([
                    'quiz_attempt_id' => $attempt->id,
                    'question_id' => $q->id,
                    'option_id' => $q->type == 'single' ? ($ans ?? null) : null,
                    'answer_text' => null,
                ]);
                $score += $isCorrect ? 1 : 0;
                $total++;
            } elseif ($q->type == 'short' || $q->type == 'long') {
                QuizAnswer::create([
                    'quiz_attempt_id' => $attempt->id,
                    'question_id' => $q->id,
                    'option_id' => null,
                    'answer_text' => $ans,
                ]);
                // Q2 — do NOT inflate $total with manually-graded questions:
                // an essay quiz must read "pending review", not a false "0%".
                $ungraded++;
            }
        }
        $attempt->score = $score;
        $attempt->save();

        return $this->resultView($quiz, $attempt, $score, $total, $ungraded);
    }

    private function resultView(Quiz $quiz, QuizAttempt $attempt, ?int $score = null, ?int $total = null, ?int $ungraded = null): View
    {
        // Recompute from persisted answers when re-displaying a prior attempt.
        if ($score === null) {
            $attempt->loadMissing('answers');
            $quiz->loadMissing('questions.options');
            $score = (int) $attempt->score;
            $total = $quiz->questions->whereIn('type', ['single', 'multiple'])->count();
            $ungraded = $quiz->questions->whereIn('type', ['short', 'long'])->count();
        }

        return view('quizzes.result', compact('quiz', 'attempt', 'score', 'total', 'ungraded'));
    }
}
