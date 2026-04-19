@php
    $lang = session('lang', 'en');
    $rtlLangs = ['ar', 'ur'];
    $dir = in_array($lang, $rtlLangs) ? 'rtl' : 'ltr';
    $metaTitle       = trim($__env->yieldContent('meta_title'))       ?: 'NobleNest Global Academy';
    $metaDescription = trim($__env->yieldContent('meta_description')) ?: 'Family-first learning with courses, onboarding, and AI guidance in one beautifully designed academy experience.';
    $metaImage       = trim($__env->yieldContent('meta_image'))       ?: asset('og-image.png');
    $metaUrl         = url()->current();
@endphp
<!doctype html>
<html lang="{{ $lang }}" dir="{{ $dir }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $metaTitle)</title>
    <meta name="application-name" content="NobleNest Global Academy">
    <meta name="description" content="{{ $metaDescription }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="NobleNest Global Academy">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ $metaUrl }}">
    <meta property="og:image" content="{{ $metaImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    <meta name="twitter:image" content="{{ $metaImage }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#7C3AED">
    {{-- Critical font preloads — fonts must exist in public/fonts/ (see resources/fonts/README.md) --}}
    <link rel="preload" as="font" type="font/woff2" crossorigin href="/fonts/Baloo2-Regular.woff2">
    <link rel="preload" as="font" type="font/woff2" crossorigin href="/fonts/Baloo2-Bold.woff2">
    <link rel="preload" as="font" type="font/woff2" crossorigin href="/fonts/Nunito-Regular.woff2">
    <link rel="preload" as="font" type="font/woff2" crossorigin href="/fonts/Inter-Regular.woff2">
    <link rel="preload" as="font" type="font/woff2" crossorigin href="/fonts/Inter-SemiBold.woff2">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="{{ asset('css/playful.css') }}" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    @stack('head')
    @stack('styles')
</head>
<body class="min-h-screen flex flex-col font-[var(--font-sans)]">

    <x-app.nav-marketing />

    <main class="flex-1 py-8 lg:py-12">
        <div class="container mx-auto px-4">
            @yield('content')
        </div>
    </main>

    <x-app.footer />

    {{-- Toast viewport --}}
    <x-ui.toast />

    {{-- AI bubble --}}
    <x-app.ai-bubble />
    @include('partials.assistant')

    {{-- PWA --}}
    <x-app.pwa-installer />

    {{-- Flash messages via toast --}}
    <x-app.flash-messages />

    @stack('scripts')
</body>
</html>
