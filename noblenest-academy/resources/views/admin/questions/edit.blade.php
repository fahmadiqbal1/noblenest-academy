@extends('layouts.admin')
@section('content')
<div class="container">
    <h1>Edit Question</h1>
    <form action="{{ route('admin.questions.update', [$quiz, $question]) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.questions._form', ['question' => $question])
        <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-emerald-600 text-white hover:bg-emerald-700">Update Question</button>
        <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-gray-500 text-white hover:bg-gray-600">Back to Quiz</a>
    </form>
</div>
@endsection

