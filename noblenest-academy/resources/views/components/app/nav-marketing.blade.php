@php
    $lang = session('lang', 'en');
@endphp

<header class="sticky top-0 z-30 bg-white/85 backdrop-blur-xl border-b-[3px] border-[var(--color-border)] shadow-[var(--shadow-soft)]">
    <div class="container mx-auto px-4">
        <nav class="flex items-center h-16 gap-4" aria-label="Main navigation">
            {{-- Brand --}}
            <a href="{{ route('noble.home') }}" class="flex items-center gap-2.5 shrink-0 focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2 rounded">
                <img src="{{ asset('brand/noblenest-logo.svg') }}" alt="NobleNest Global Academy logo" class="w-10 h-10 rounded-[var(--radius-sm)] shadow-[var(--shadow-clay)]" loading="eager">
                <span class="brand-grad text-lg font-bold hidden sm:block">{{ __('common.brand') }}</span>
            </a>

            {{-- Desktop nav --}}
            <ul class="hidden lg:flex items-center gap-1 ml-6 flex-1" role="list">
                <li>
                    <a href="{{ route('noble.home') }}" class="px-3 py-2 rounded-[var(--radius-sm)] text-sm font-bold text-[var(--color-text-muted)] hover:bg-[var(--color-primary-soft)] hover:text-[var(--color-primary)] transition-colors {{ request()->routeIs('noble.home') ? 'bg-[var(--color-primary-soft)] text-[var(--color-primary)]' : '' }}">
                        Home
                    </a>
                </li>
                <li>
                    <a href="{{ route('pricing') }}" class="px-3 py-2 rounded-[var(--radius-sm)] text-sm font-bold text-[var(--color-text-muted)] hover:bg-[var(--color-primary-soft)] hover:text-[var(--color-primary)] transition-colors {{ request()->routeIs('pricing') ? 'bg-[var(--color-primary-soft)] text-[var(--color-primary)]' : '' }}">
                        Pricing
                    </a>
                </li>
            </ul>

            {{-- Actions --}}
            <div class="flex items-center gap-2 ml-auto">
                <x-app.locale-switcher />
                <a href="/login" class="hidden sm:inline-flex items-center px-4 py-2 rounded-[var(--radius-sm)] border-[2px] border-[var(--color-border)] text-sm font-bold text-[var(--color-text)] hover:border-[var(--color-brand-400)] transition-colors focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2">
                    Sign In
                </a>
                <x-ui.button as="a" href="/register" variant="primary" size="sm">Get Started</x-ui.button>
            </div>
        </nav>
    </div>
</header>
