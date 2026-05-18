@extends('layouts.child')
@section('content')
<div class="container py-4">
    <h1>Quiz Results: {{ $quiz->title }}</h1>
    <div class="flex items-start gap-3 p-4 rounded-lg border bg-sky-50 border-sky-200 text-sky-800 mb-4">
        @if($total > 0)
            You scored <strong>{{ $score }}</strong> out of <strong>{{ $total }}</strong>
            <span class="ms-2">({{ round($score/$total*100) }}%)</span> on the auto-graded questions.
        @else
            Your answers were submitted.
        @endif
        @if(($ungraded ?? 0) > 0)
            <span class="ms-2">{{ $ungraded }} open-ended {{ \Illuminate\Support\Str::plural('answer', $ungraded) }} pending review.</span>
        @endif
    </div>
    <h3>Review</h3>
    <ol>
    @foreach($quiz->questions as $q)
        <li class="mb-3">
            <div class="font-bold">{!! $q->question_text !!}</div>
            @php
                $ans = $attempt->answers->where('question_id', $q->id)->first();
                $isCorrect = false;
                $userAns = [];
                if(in_array($q->type, ['single','multiple'])) {
                    $correct = $q->options->where('is_correct', true)->pluck('id')->sort()->values();
                    // Always get user answer from QuizAnswer for this attempt
                    if ($ans) {
                        if ($q->type == 'single') {
                            $userAns = $ans->option_id ? [$ans->option_id] : [];
                        } else {
                            // For multiple, store as array in answer_text if needed
                            $userAns = $ans->answer_text ? json_decode($ans->answer_text, true) : [];
                        }
                    }
                    $userAns = collect($userAns)->map(function($v){ return (int)$v; })->sort()->values();
                    $isCorrect = $userAns->count() && $userAns->toArray() === $correct->toArray();
                }
            @endphp
            @if($q->type=='single' || $q->type=='multiple')
                <ul>
                @foreach($q->options as $opt)
                    <li>
                        @if($opt->is_correct)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-600">Correct</span>
                        @endif
                        {{ $opt->option_text }}
                        @if($ans && ((is_array($userAns) && in_array($opt->id, $userAns)) || $ans->option_id == $opt->id))
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-[var(--color-primary)]">Your Answer</span>
                        @endif
                    </li>
                @endforeach
                </ul>
                @if($isCorrect)
                    <span class="text-emerald-600">Correct!</span>
                @else
                    <span class="text-red-600">Incorrect.</span>
                @endif
            @elseif($q->type=='short' || $q->type=='long')
                <div><strong>Your Answer:</strong> {{ $ans->answer_text ?? '-' }}</div>
            @endif
        </li>
    @endforeach
    </ol>
    <a href="{{ route('activities.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-gray-500 text-white hover:bg-gray-600">Back to Activities</a>
</div>
@endsection
