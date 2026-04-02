@extends('layouts.app')

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

    <h2 class="fw-bold mb-2">{{ $shareCard->childProfile->name }} is on a learning adventure! 🚀</h2>
    <p class="lead text-muted mb-4">Join Noble Nest Academy — where every child discovers the joy of learning</p>

    <div class="d-flex justify-content-center gap-3">
        <a href="{{ route('register') }}" class="btn btn-primary btn-lg fw-semibold px-5">
            Start Free Today
        </a>
        <a href="https://wa.me/?text={{ urlencode('Look what ' . $shareCard->childProfile->name . ' achieved! Join me on Noble Nest Academy: ' . request()->url()) }}"
           class="btn btn-success btn-lg" target="_blank" rel="noopener noreferrer">
            📱 Share
        </a>
    </div>
</div>
@endsection
