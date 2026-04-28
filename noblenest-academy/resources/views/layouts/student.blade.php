@php
    $lang = session('lang', 'en');
    $rtlLangs = ['ar', 'ur'];
    $dir = in_array($lang, $rtlLangs) ? 'rtl' : 'ltr';
    if (!class_exists('I18n')) { class_alias(\App\Helpers\I18n::class, 'I18n'); }
@endphp
<!doctype html>
<html lang="{{ $lang }}" dir="{{ $dir }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NobleNest Global Academy')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#7C3AED">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="{{ asset('css/playful.css') }}" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    @stack('head')
    @stack('styles')
</head>
<body class="min-h-screen flex flex-col font-[var(--font-sans)]">

    <x-app.nav-student />

    <main class="flex-1 py-6">
        <div class="container mx-auto px-4">
            @yield('content')
        </div>
    </main>

    <x-app.footer />

    <x-ui.toast />
    <x-app.ai-bubble />
    @include('partials.assistant')
    <x-app.pwa-installer />
    <x-app.flash-messages />
    @stack('scripts')
</body>
</html>
