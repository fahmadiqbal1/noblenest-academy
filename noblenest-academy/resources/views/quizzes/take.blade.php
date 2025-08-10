@extends('layouts.app')
@section('content')
<div class="container py-4">
    <h1 class="h4 mb-3">{{ $quiz->title }}</h1>
    @if(!empty($quiz->description))
        <p class="text-muted">{{ $quiz->description }}</p>
    @endif

    @if(($quiz->questions ?? collect())->count() === 0)
        <div class="alert alert-info">No questions available for this quiz yet.</div>
    @else
        <form method="POST" action="{{ route('quizzes.submit', $quiz) }}">
            @csrf
            @foreach($quiz->questions as $q)
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="fw-semibold mb-2">{!! $q->question_text !!}</div>
                        @if(in_array($q->type, ['single','multiple']))
                            @php $name = "answers[{$q->id}]"; @endphp
                            @foreach($q->options as $opt)
                                <div class="form-check mb-1">
                                    @if($q->type === 'single')
                                        <input class="form-check-input" type="radio" name="{{ $name }}" id="q{{ $q->id }}o{{ $opt->id }}" value="{{ $opt->id }}">
                                    @else
                                        <input class="form-check-input" type="checkbox" name="{{ $name }}[]" id="q{{ $q->id }}o{{ $opt->id }}" value="{{ $opt->id }}">
                                    @endif
                                    <label class="form-check-label" for="q{{ $q->id }}o{{ $opt->id }}">{{ $opt->option_text }}</label>
                                </div>
                            @endforeach
                        @elseif($q->type === 'short')
                            <input type="text" name="answers[{{ $q->id }}]" class="form-control" placeholder="Your answer">
                        @elseif($q->type === 'long')
                            <textarea name="answers[{{ $q->id }}]" class="form-control" rows="4" placeholder="Your answer"></textarea>
                        @endif
                    </div>
                </div>
            @endforeach
            <div class="d-flex justify-content-between">
                <a href="{{ route('quizzes.index') }}" class="btn btn-secondary">Back</a>
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    @endif
</div>
@endsection
