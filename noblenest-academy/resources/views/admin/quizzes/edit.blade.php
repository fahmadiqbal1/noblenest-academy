@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Edit Quiz</h1>
    <form action="{{ route('admin.quizzes.update', $quiz) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.quizzes._form', ['quiz' => $quiz, 'modules' => $modules])
        <button type="submit" class="btn btn-success">Update</button>
        <a href="{{ route('admin.quizzes.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
    <hr>
    <h2>Questions</h2>
    <a href="{{ route('admin.questions.create', $quiz) }}" class="btn btn-primary mb-3">Add Question</a>
    @if($quiz->questions->isEmpty())
        <div class="alert alert-info">No questions yet.</div>
    @else
    <table class="table table-bordered table-striped">
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
                        <div>{{ $option->option_text }} @if($option->is_correct)<span class="badge bg-success">Correct</span>@endif</div>
                    @endforeach
                </td>
                <td>
                    <a href="{{ route('admin.questions.edit', [$quiz, $question]) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('admin.questions.destroy', [$quiz, $question]) }}" method="POST" style="display:inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this question?')">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @endif
</div>
@endsection

