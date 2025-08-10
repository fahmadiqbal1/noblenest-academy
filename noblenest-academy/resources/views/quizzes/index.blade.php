@extends('layouts.app')
@section('content')
<div class="container py-4">
    <h1 class="h3 mb-3">Quizzes</h1>
    @if($quizzes->count() === 0)
        <div class="alert alert-info">No quizzes available yet.</div>
    @else
        <div class="list-group">
            @foreach($quizzes as $quiz)
                <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="{{ route('quizzes.show', $quiz) }}">
                    <span>
                        <strong>{{ $quiz->title }}</strong>
                        @if(!empty($quiz->description))
                            <div class="text-muted small">{{ $quiz->description }}</div>
                        @endif
                    </span>
                    <i class="bi bi-chevron-right"></i>
                </a>
            @endforeach
        </div>
        <div class="mt-3">{{ $quizzes->links() }}</div>
    @endif
</div>
@endsection
