<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Module;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::with('module')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.quizzes.index', compact('quizzes'));
    }

    public function create()
    {
        $modules = Module::all();
        return view('admin.quizzes.create', compact('modules'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'module_id' => 'nullable|exists:modules,id',
        ]);
        $quiz = Quiz::create($data);
        return redirect()->route('admin.quizzes.edit', $quiz)->with('success', 'Quiz created. Now add questions.');
    }

    public function edit(Quiz $quiz)
    {
        $modules = Module::all();
        $quiz->load('questions.options');
        return view('admin.quizzes.edit', compact('quiz', 'modules'));
    }

    public function update(Request $request, Quiz $quiz)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'module_id' => 'nullable|exists:modules,id',
        ]);
        $quiz->update($data);
        return redirect()->route('admin.quizzes.index')->with('success', 'Quiz updated.');
    }

    public function destroy(Quiz $quiz)
    {
        $quiz->delete();
        return redirect()->route('admin.quizzes.index')->with('success', 'Quiz deleted.');
    }
}

