@extends('layouts.app')
@section('content')
<div class="container py-4">
    <h1>Quiz Results: {{ $quiz->title }}</h1>
    <div class="alert alert-info mb-4">
        You scored <strong>{{ $score }}</strong> out of <strong>{{ $total }}</strong>.
        @if($total > 0)
            <span class="ms-2">({{ round($score/$total*100) }}%)</span>
        @endif
    </div>
    <h3>Review</h3>
    <ol>
    @foreach($quiz->questions as $q)
        <li class="mb-3">
            <div class="fw-bold">{!! $q->question_text !!}</div>
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
                            <span class="badge bg-success">Correct</span>
                        @endif
                        {{ $opt->option_text }}
                        @if($ans && ((is_array($userAns) && in_array($opt->id, $userAns)) || $ans->option_id == $opt->id))
                            <span class="badge bg-primary">Your Answer</span>
                        @endif
                    </li>
                @endforeach
                </ul>
                @if($isCorrect)
                    <span class="text-success">Correct!</span>
                @else
                    <span class="text-danger">Incorrect.</span>
                @endif
            @elseif($q->type=='short' || $q->type=='long')
                <div><strong>Your Answer:</strong> {{ $ans->answer_text ?? '-' }}</div>
            @endif
        </li>
    @endforeach
    </ol>
    <a href="{{ route('activities.index') }}" class="btn btn-secondary">Back to Activities</a>
</div>
@endsection
