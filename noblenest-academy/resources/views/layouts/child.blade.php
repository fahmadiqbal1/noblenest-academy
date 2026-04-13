@php
    $lang = session('lang', auth()->user()->preferred_language ?? 'en');
    $rtlLangs = ['ar', 'ur'];
    $dir = in_array($lang, $rtlLangs) ? 'rtl' : 'ltr';
    // Resolve age-tier from the $child variable if provided
    $ageTier = isset($child) ? ($child->age_tier ?? 'school') : 'school';
    $tierCss = [
        'baby'      => asset('css/tier-baby.css'),
        'toddler'   => asset('css/tier-toddler.css'),
        'preschool' => asset('css/tier-preschool.css'),
        'school'    => asset('css/tier-school.css'),
    ][$ageTier] ?? asset('css/tier-school.css');
    $tierEmoji = ['baby' => '👶', 'toddler' => '🐣', 'preschool' => '🌱', 'school' => '🚀'][$ageTier] ?? '📚';
@endphp
<!doctype html>
<html lang="{{ $lang }}" dir="{{ $dir }}" data-age-tier="{{ $ageTier }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Noble Nest Academy')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ $tierCss }}">
    <link rel="stylesheet" href="{{ asset('css/playful.css') }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    @stack('head')
</head>
<body class="child-layout age-{{ $ageTier }}">

    {{-- Child-friendly top nav --}}
    <nav class="navbar navbar-expand-lg child-nav py-2">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="{{ route('parent.dashboard') }}">
                {{ $tierEmoji }} Noble Nest
            </a>
            <div class="d-flex align-items-center gap-3">
                @isset($child)
                <span class="badge child-badge fs-6">
                    {{ $child->name }}
                    @if($child->streak_days) · 🔥 {{ $child->streak_days }} @endif
                </span>
                @endisset
                <a href="{{ route('parent.dashboard') }}" class="btn btn-sm btn-outline-light">
                    ← Parent
                </a>
            </div>
        </div>
    </nav>

    <main>
        @if(session('status'))
        <div class="container pt-3">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
        @endif
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
