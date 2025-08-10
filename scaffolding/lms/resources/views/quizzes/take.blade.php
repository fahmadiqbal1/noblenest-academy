@extends('layouts.app')
@section('content')
<div class="container py-4">
    <h1>Take Quiz: {{ $quiz->title }}</h1>
    <form action="{{ route('quizzes.submit', $quiz) }}" method="POST">
        @csrf
        @foreach($quiz->questions as $q)
            <div class="mb-4">
                <div class="fw-bold">Q{{ $loop->iteration }}. {!! $q->question_text !!}</div>
                @if(in_array($q->type, ['single','multiple']))
                    @foreach($q->options as $opt)
                        <div class="form-check">
                            <input class="form-check-input" type="{{ $q->type=='single' ? 'radio' : 'checkbox' }}" name="answers[{{ $q->id }}]{{ $q->type=='multiple' ? '[]' : '' }}" value="{{ $opt->id }}" id="q{{ $q->id }}_opt{{ $opt->id }}">
                            <label class="form-check-label" for="q{{ $q->id }}_opt{{ $opt->id }}">{{ $opt->option_text }}</label>
                        </div>
                    @endforeach
                @elseif($q->type=='short')
                    <input type="text" class="form-control" name="answers[{{ $q->id }}]">
                @elseif($q->type=='long')
                    <textarea class="form-control" name="answers[{{ $q->id }}]" rows="3"></textarea>
                @endif
            </div>
        @endforeach
        <button type="submit" class="btn btn-primary">Submit Quiz</button>
    </form>
</div>
@endsection

