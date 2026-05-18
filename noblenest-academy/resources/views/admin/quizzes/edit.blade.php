@extends('layouts.admin')
@section('content')
<div class="container">
    <h1>Edit Quiz</h1>
    <form action="{{ route('admin.quizzes.update', $quiz) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.quizzes._form', ['quiz' => $quiz, 'modules' => $modules])
        <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-emerald-600 text-white hover:bg-emerald-700">Update</button>
        <a href="{{ route('admin.quizzes.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-gray-500 text-white hover:bg-gray-600">Cancel</a>
    </form>
    <hr>
    <h2>Questions</h2>
    <a href="{{ route('admin.quizzes.questions.create', $quiz) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-violet-600 text-white hover:bg-violet-700 mb-3">Add Question</a>
    @if($quiz->questions->isEmpty())
        <div class="flex items-start gap-3 p-4 rounded-lg border bg-sky-50 border-sky-200 text-sky-800">No questions yet.</div>
    @else
    <table class="w-full text-sm border-collapse border border-gray-200 table-striped-tw">
        <thead>
            <tr>
                <th>Order</th>
                <th>Question</th>
                <th>Type</th>
                <th>Options</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($quiz->questions as $question)
            <tr>
                <td>{{ $question->order }}</td>
                <td>{{ $question->question_text }}</td>
                <td>{{ ucfirst($question->type) }}</td>
                <td>
                    @foreach($question->options as $option)
                        <div>{{ $option->option_text }} @if($option->is_correct)<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-600">Correct</span>@endif</div>
                    @endforeach
                </td>
                <td>
                    <a href="{{ route('admin.quizzes.questions.edit', [$quiz, $question]) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm bg-amber-500 text-gray-900 hover:bg-amber-600">Edit</a>
                    <form action="{{ route('admin.quizzes.questions.destroy', [$quiz, $question]) }}" method="POST" style="display:inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm bg-red-600 text-white hover:bg-red-700" onclick="return confirm('Delete this question?')">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @endif
</div>
@endsection

