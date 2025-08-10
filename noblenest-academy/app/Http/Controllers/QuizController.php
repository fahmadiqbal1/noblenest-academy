<?php
namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAnswer;
use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => $user ? $user->id : null,
            'completed_at' => now(),
        ]);
        $score = 0;
        $total = 0;
        foreach ($quiz->questions as $q) {
            $ans = $data['answers'][$q->id] ?? null;
            if (in_array($q->type, ['single','multiple'])) {
                $correct = $q->options->where('is_correct', true)->pluck('id')->sort()->values();
                $userAns = collect((array)$ans)->map(fn($v)=>(int)$v)->sort()->values();
                $isCorrect = $userAns->count() && $userAns->toArray() === $correct->toArray();
                QuizAnswer::create([
                    'quiz_attempt_id' => $attempt->id,
                    'question_id' => $q->id,
                    'option_id' => $q->type=='single' ? ($ans ?? null) : null,
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
                $total++;
            }
        }
        $attempt->score = $score;
        $attempt->save();
        return view('quizzes.result', compact('quiz', 'attempt', 'score', 'total'));
    }
}

