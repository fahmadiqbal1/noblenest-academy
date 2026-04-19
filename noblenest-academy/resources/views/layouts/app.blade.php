@php
    /*
     * layouts/app.blade.php — COMPATIBILITY SHIM (Phase 2)
     *
     * Existing views use @extends('layouts.app') + @section('content').
     * This shim renders the correct role-specific chrome inline so those
     * views keep working during Phase 3 migration without any changes.
     *
     * Once all surfaces are migrated to their role layout in Phase 3,
     * this file is retired.
     */
    if (!class_exists('I18n')) { class_alias(\App\Helpers\I18n::class, 'I18n'); }

    $lang    = session('lang', auth()->user()->preferred_language ?? 'en');
    $rtlLangs = ['ar', 'ur'];
    $dir     = in_array($lang, $rtlLangs) ? 'rtl' : 'ltr';
    $user    = auth()->user();
    $role    = $user->role ?? null;
    $theme   = session('theme', $role === 'Parent' ? 'professional' : ($role === 'Student' ? 'playful' : 'professional'));
    $isPlayful = $theme === 'playful';

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
    <title>{{ $metaTitle }}</title>
    <meta name="application-name" content="NobleNest Global Academy">
    <meta name="description" content="{{ $metaDescription }}">
    <meta property="og:type" content="website">
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
    @stack('styles')
    @stack('head')
</head>
<body class="min-h-screen flex flex-col {{ $isPlayful ? 'font-[var(--font-display)]' : 'font-[var(--font-body)]' }}">

    {{-- Role-appropriate nav --}}
    @if(!auth()->check())
        <x-app.nav-marketing />
    @elseif($role === 'Admin')
        <x-app.nav-admin />
    @elseif($role === 'Teacher')
        <x-app.nav-teacher />
    @elseif($role === 'Parent')
        <x-app.nav-parent />
    @elseif($role === 'Student')
        <x-app.nav-student />
    @elseif($role === 'Practitioner')
        <x-app.nav-practitioner />
    @else
        <x-app.nav-authed />
    @endif

    <main class="flex-1 py-6 @if($role === 'Parent') pb-24 lg:pb-6 @endif">
        <div class="container mx-auto px-4">
            @yield('content')
        </div>
    </main>

    @if(!in_array($role, ['Admin', 'Teacher', 'Practitioner']))
        <x-app.footer />
    @endif

    {{-- Toast viewport --}}
    <x-ui.toast />

    {{-- AI assistant bubble --}}
    @if(auth()->check())
        <x-app.ai-bubble />
        @include('partials.assistant')
    @endif

    {{-- PWA installer --}}
    <x-app.pwa-installer />

    {{-- Flash messages via toast --}}
    <x-app.flash-messages />

    @yield('scripts')
    @stack('scripts')
</body>
</html>
