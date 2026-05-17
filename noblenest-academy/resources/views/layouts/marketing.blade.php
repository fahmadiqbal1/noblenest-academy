@php
    $lang = session('lang', 'en');
    $dir  = in_array($lang, ['ar', 'ur']) ? 'rtl' : 'ltr';
@endphp
<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ \App\Helpers\I18n::direction() }}">
<head>
    @include('layouts.partials.head')
</head>
<body class="min-h-screen flex flex-col font-[var(--font-sans)]">

    <x-app.nav-marketing />

    <main class="flex-1 py-8 lg:py-12">
        <div class="container mx-auto px-4">
            @yield('content')
        </div>
    </main>

    <x-app.footer />

    @include('layouts.partials.scripts')
</body>
</html>
