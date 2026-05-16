@php
    $lang = session('lang', 'en');
    $rtlLangs = ['ar', 'ur'];
    $dir = in_array($lang, $rtlLangs) ? 'rtl' : 'ltr';
@endphp
<!doctype html>
<html lang="{{ $lang }}" dir="{{ $dir }}">
<head>
    @include('layouts.partials.head')
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-[var(--color-brand-50)] via-[var(--color-bg)] to-[var(--color-accent-50)] py-12 px-4">

    <div class="w-full max-w-md">
        {{-- Logo --}}
        <div class="text-center mb-8">
            <a href="{{ route('noble.home') }}" class="inline-flex flex-col items-center gap-2 focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2 rounded">
                <img src="{{ asset('brand/noblenest-logo.svg') }}" alt="NobleNest Global Academy" class="w-16 h-16 rounded-[var(--radius-sm)] shadow-[var(--shadow-clay)]">
                <span class="brand-grad text-2xl font-bold">NobleNest</span>
            </a>
        </div>

        {{-- Card --}}
        <x-ui.card variant="clay" padding="lg">
            @yield('content')
        </x-ui.card>
    </div>

    @include('layouts.partials.scripts')
</body>
</html>
