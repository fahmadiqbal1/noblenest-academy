{{-- Shared head partial — replaces the chrome that layouts/app.blade.php used to render inline. --}}
@php
    // Defensive defaults: trim(null) errors under PHP 8.4. Cast all
    // yieldContent results to string before trimming, then fall back.
    $metaTitle       = $metaTitle       ?? null;
    $metaDescription = $metaDescription ?? null;
    $metaImage       = $metaImage       ?? null;
    $metaUrl         = $metaUrl         ?? null;
    if (! $metaTitle)       { $metaTitle       = trim((string) $__env->yieldContent('meta_title'))       ?: 'NobleNest Global Academy'; }
    if (! $metaDescription) { $metaDescription = trim((string) $__env->yieldContent('meta_description')) ?: 'Family-first learning with courses, onboarding, and AI guidance in one beautifully designed academy experience.'; }
    if (! $metaImage)       { $metaImage       = trim((string) $__env->yieldContent('meta_image'))       ?: asset('og-image.png'); }
    if (! $metaUrl)         { $metaUrl         = url()->current(); }
@endphp
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
{{-- Critical font preloads — files live in public/fonts/, served at /fonts/*.woff2 --}}
<link rel="preload" as="font" type="font/woff2" crossorigin href="/fonts/Baloo2-Regular.woff2">
<link rel="preload" as="font" type="font/woff2" crossorigin href="/fonts/Baloo2-Bold.woff2">
<link rel="preload" as="font" type="font/woff2" crossorigin href="/fonts/Nunito-Regular.woff2">
<link rel="preload" as="font" type="font/woff2" crossorigin href="/fonts/Inter-Regular.woff2">
<link rel="preload" as="font" type="font/woff2" crossorigin href="/fonts/Inter-SemiBold.woff2">
{{-- Phase 6: structured data + sitemap hint. --}}
<x-seo.organization-jsonld />
<link rel="sitemap" type="application/xml" href="/sitemap.xml">
@vite(['resources/css/app.css', 'resources/js/app.js'])
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
@stack('head')
@stack('styles')
