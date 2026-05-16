@php
    $lang = session('lang', auth()->user()->preferred_language ?? 'en');
    $dir  = in_array($lang, ['ar', 'ur']) ? 'rtl' : 'ltr';
    if (!class_exists('I18n')) { class_alias(\App\Helpers\I18n::class, 'I18n'); }
@endphp
<!doctype html>
<html lang="{{ $lang }}" dir="{{ $dir }}">
<head>
    @include('layouts.partials.head')
</head>
<body class="min-h-screen flex flex-col font-[var(--font-sans)] text-[var(--color-text)]">

    <x-app.nav-parent />

    <main class="flex-1 py-6">
        <div class="container mx-auto px-4">
            @yield('content')
        </div>
    </main>

    <x-app.footer />

    @include('layouts.partials.scripts')
</body>
</html>
