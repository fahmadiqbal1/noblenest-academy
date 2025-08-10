@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Edit Question</h1>
    <form action="{{ route('admin.questions.update', [$quiz, $question]) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.questions._form', ['question' => $question])
        <button type="submit" class="btn btn-success">Update Question</button>
        <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="btn btn-secondary">Back to Quiz</a>
    </form>
</div>
@endsection

