<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Option;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function create(Quiz $quiz)
    {
        return view('admin.questions.create', compact('quiz'));
    }

    public function store(Request $request, Quiz $quiz)
    {
        $data = $request->validate([
            'question_text' => 'required|string',
            'type' => 'required|in:single,multiple,short,long',
            'order' => 'nullable|integer|min:0',
            'options' => 'array',
            'options.*.option_text' => 'required_with:options|string',
            'options.*.is_correct' => 'boolean',
        ]);
        $question = $quiz->questions()->create($data);
        if (!empty($data['options'])) {
            foreach ($data['options'] as $opt) {
                $question->options()->create([
                    'option_text' => $opt['option_text'],
                    'is_correct' => $opt['is_correct'] ?? false,
                ]);
            }
        }
        return redirect()->route('admin.quizzes.edit', $quiz)->with('success', 'Question added.');
    }

    public function edit(Quiz $quiz, Question $question)
    {
        $question->load('options');
        return view('admin.questions.edit', compact('quiz', 'question'));
    }

    public function update(Request $request, Quiz $quiz, Question $question)
    {
        $data = $request->validate([
            'question_text' => 'required|string',
            'type' => 'required|in:single,multiple,short,long',
            'order' => 'nullable|integer|min:0',
            'options' => 'array',
            'options.*.id' => 'nullable|integer|exists:options,id',
            'options.*.option_text' => 'required_with:options|string',
            'options.*.is_correct' => 'boolean',
        ]);
        $question->update($data);
        // Update or create options
        $existing = $question->options->keyBy('id');
        $ids = [];
        if (!empty($data['options'])) {
            foreach ($data['options'] as $opt) {
                if (!empty($opt['id']) && $existing->has($opt['id'])) {
                    $existing[$opt['id']]->update([
                        'option_text' => $opt['option_text'],
                        'is_correct' => $opt['is_correct'] ?? false,
                    ]);
                    $ids[] = $opt['id'];
                } else {
                    $new = $question->options()->create([
                        'option_text' => $opt['option_text'],
                        'is_correct' => $opt['is_correct'] ?? false,
                    ]);
                    $ids[] = $new->id;
                }
            }
        }
        // Delete removed options
        $question->options()->whereNotIn('id', $ids)->delete();
        return redirect()->route('admin.quizzes.edit', $quiz)->with('success', 'Question updated.');
    }

    public function destroy(Quiz $quiz, Question $question)
    {
        $question->delete();
        return redirect()->route('admin.quizzes.edit', $quiz)->with('success', 'Question deleted.');
    }
}

