@extends('layouts.child')
@section('content')
<div class="container py-4">
    <h1 class="h3 mb-3">Quizzes</h1>
    @if($quizzes->count() === 0)
        <div class="flex items-start gap-3 p-4 rounded-lg border bg-sky-50 border-sky-200 text-sky-800">No quizzes available yet.</div>
    @else
        <div class="divide-y divide-gray-200 border border-gray-200 rounded-lg bg-white">
            @foreach($quizzes as $quiz)
                <a class="px-4 py-3 cursor-pointer hover:bg-gray-50 flex justify-between items-center" href="{{ route('quizzes.show', $quiz) }}">
                    <span>
                        <strong>{{ $quiz->title }}</strong>
                        @if(!empty($quiz->description))
                            <div class="text-[var(--color-text-muted)] text-sm">{{ $quiz->description }}</div>
                        @endif
                    </span>
                    <x-ui.icon name="chevron-right" />
                </a>
            @endforeach
        </div>
        <div class="mt-3">{{ $quizzes->links() }}</div>
    @endif
</div>
@endsection
