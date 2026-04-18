@php
    $lang = session('lang', auth()->user()->preferred_language ?? 'en');
    $rtlLangs = ['ar', 'ur'];
    $dir = in_array($lang, $rtlLangs) ? 'rtl' : 'ltr';
    $ageTier = isset($child) ? ($child->age_tier ?? 'school') : 'school';
    $tierEmoji = ['baby' => '👶', 'toddler' => '🐣', 'preschool' => '🌱', 'school' => '🚀'][$ageTier] ?? '📚';

    $tierNavColor = [
        'baby'      => 'var(--color-tier-baby)',
        'toddler'   => 'var(--color-tier-toddler)',
        'preschool' => 'var(--color-tier-preschool)',
        'school'    => 'var(--color-tier-primary)',
    ][$ageTier] ?? 'var(--color-tier-primary)';
@endphp
<!doctype html>
<html lang="{{ $lang }}" dir="{{ $dir }}" data-age-tier="{{ $ageTier }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Noble Nest Academy')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/playful.css') }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    @stack('head')
</head>
<body class="child-layout age-{{ $ageTier }} min-h-screen flex flex-col">

    {{-- Child-friendly top nav --}}
    <nav
        class="sticky top-0 z-30 py-2 shadow-[var(--shadow-soft)]"
        style="background-color: {{ $tierNavColor }}; color: #fff;"
        aria-label="Child navigation"
    >
        <div class="container mx-auto px-4 flex items-center justify-between h-12">
            <a
                href="{{ route('parent.dashboard') }}"
                class="flex items-center gap-2 font-bold text-xl text-white focus-visible:outline-2 focus-visible:outline-white focus-visible:outline-offset-2 rounded"
            >
                <span aria-hidden="true">{{ $tierEmoji }}</span>
                Noble Nest
            </a>

            <div class="flex items-center gap-3">
                @isset($child)
                    <span class="child-badge inline-flex items-center gap-1.5 rounded-full bg-white/30 px-3 py-1 text-sm font-bold text-white">
                        {{ $child->name }}
                        @if($child->streak_days)
                            <span>🔥 {{ $child->streak_days }}</span>
                        @endif
                    </span>
                @endisset
                <a
                    href="{{ route('parent.dashboard') }}"
                    class="inline-flex items-center gap-1 rounded-[var(--radius-sm)] border-2 border-white/60 px-3 py-1 text-sm font-bold text-white hover:bg-white/20 transition-colors focus-visible:outline-2 focus-visible:outline-white focus-visible:outline-offset-1"
                >
                    ← Parent
                </a>
            </div>
        </div>
    </nav>

    <main class="flex-1">
        @if(session('status'))
            <div class="container mx-auto px-4 pt-4">
                <x-ui.alert tone="success" dismissible>{{ session('status') }}</x-ui.alert>
            </div>
        @endif
        @yield('content')
    </main>

    <x-ui.toast />
    <x-app.flash-messages />
    @stack('scripts')
</body>
</html>
