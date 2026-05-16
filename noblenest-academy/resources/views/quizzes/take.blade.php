@extends('layouts.child')
@section('content')
<div class="container py-4">
    <h1 class="h4 mb-3">{{ $quiz->title }}</h1>
    @if(!empty($quiz->description))
        <p class="text-[var(--color-text-muted)]">{{ $quiz->description }}</p>
    @endif

    @if(($quiz->questions ?? collect())->count() === 0)
        <div class="flex items-start gap-3 p-4 rounded-lg border bg-sky-50 border-sky-200 text-sky-800">No questions available for this quiz yet.</div>
    @else
        <form method="POST" action="{{ route('quizzes.submit', $quiz) }}">
            @csrf
            @foreach($quiz->questions as $q)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-3">
                    <div class="p-5">
                        <div class="font-semibold mb-2">{!! $q->question_text !!}</div>
                        @if(in_array($q->type, ['single','multiple']))
                            @php $name = "answers[{$q->id}]"; @endphp
                            @foreach($q->options as $opt)
                                <div class="flex items-center gap-2 mb-1">
                                    @if($q->type === 'single')
                                        <input class="w-4 h-4 rounded border-gray-300 text-violet-600 focus:ring-violet-500" type="radio" name="{{ $name }}" id="q{{ $q->id }}o{{ $opt->id }}" value="{{ $opt->id }}">
                                    @else
                                        <input class="w-4 h-4 rounded border-gray-300 text-violet-600 focus:ring-violet-500" type="checkbox" name="{{ $name }}[]" id="q{{ $q->id }}o{{ $opt->id }}" value="{{ $opt->id }}">
                                    @endif
                                    <label class="text-sm" for="q{{ $q->id }}o{{ $opt->id }}">{{ $opt->option_text }}</label>
                                </div>
                            @endforeach
                        @elseif($q->type === 'short')
                            <input type="text" name="answers[{{ $q->id }}]" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" placeholder="Your answer">
                        @elseif($q->type === 'long')
                            <textarea name="answers[{{ $q->id }}]" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" rows="4" placeholder="Your answer"></textarea>
                        @endif
                    </div>
                </div>
            @endforeach
            <div class="flex justify-between">
                <a href="{{ route('quizzes.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-gray-500 text-white hover:bg-gray-600">Back</a>
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-violet-600 text-white hover:bg-violet-700">Submit</button>
            </div>
        </form>
    @endif
</div>
@endsection
