@extends('layouts.marketing')

@section('title', $shareCard->childProfile->name . ' — Noble Nest Academy')

@section('head')
{{-- Open Graph tags for viral sharing --}}
<meta property="og:title" content="{{ $shareCard->childProfile->name }} just completed a learning activity! 🎉">
<meta property="og:description" content="Join Noble Nest Academy — learning made magical for children everywhere.">
<meta property="og:image" content="{{ $shareCard->image_url }}">
<meta property="og:url" content="{{ request()->url() }}">
<meta property="og:type" content="website">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:image" content="{{ $shareCard->image_url }}">
<meta name="twitter:title" content="{{ $shareCard->childProfile->name }} is learning with Noble Nest Academy!">
@endsection

@section('content')
<div class="container py-5 text-center">
    <div class="mb-4">
        <img src="{{ $shareCard->image_url }}" alt="Share Card" class="img-fluid rounded shadow" style="max-width: 600px; width: 100%;">
    </div>

    <h2 class="font-bold mb-2">{{ $shareCard->childProfile->name }} is on a learning adventure! 🚀</h2>
    <p class="text-lg leading-relaxed text-[var(--color-text-muted)] mb-4">Join Noble Nest Academy — where every child discovers the joy of learning</p>

    <div class="flex justify-center gap-3">
        <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-violet-600 text-white hover:bg-violet-700 px-5 py-3 text-lg">
            Start Free Today
        </a>
        <a href="https://wa.me/?text={{ urlencode('Look what ' . $shareCard->childProfile->name . ' achieved! Join me on Noble Nest Academy: ' . request()->url()) }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-emerald-600 text-white hover:bg-emerald-700 px-5 py-3 text-lg" target="_blank" rel="noopener noreferrer">
            📱 Share
        </a>
    </div>
</div>
@endsection
