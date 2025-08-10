@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Add Question</h1>
    <form action="{{ route('admin.questions.store', $quiz) }}" method="POST">
        @csrf
        @include('admin.questions._form', ['question' => null])
        <button type="submit" class="btn btn-success">Add Question</button>
        <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="btn btn-secondary">Back to Quiz</a>
    </form>
</div>
@endsection

