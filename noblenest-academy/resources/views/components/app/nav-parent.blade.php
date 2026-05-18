@php
    $user = auth()->user();
@endphp

<header class="sticky top-0 z-30 bg-white/88 backdrop-blur-xl border-b-[3px] border-[var(--color-border)] shadow-[var(--shadow-soft)]">
    <div class="container mx-auto px-4">
        <nav class="flex items-center h-16 gap-4" aria-label="Parent navigation">
            <a href="{{ route('noble.home') }}" class="flex items-center gap-2.5 shrink-0 focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2 rounded">
                <img src="{{ asset('brand/noblenest-logo.svg') }}" alt="NobleNest logo" class="w-10 h-10 rounded-[var(--radius-sm)] shadow-[var(--shadow-clay)]" loading="eager">
                <span class="brand-grad text-lg font-bold hidden sm:block">{{ __('common.brand') }}</span>
            </a>

            <ul class="hidden lg:flex items-center gap-1 ml-4 flex-1" role="list">
                <li><a href="{{ route('parent.dashboard') }}" class="px-3 py-2 rounded-[var(--radius-sm)] text-sm font-bold text-[var(--color-text-muted)] hover:bg-[var(--color-primary-soft)] hover:text-[var(--color-primary)] transition-colors {{ request()->routeIs('parent.dashboard') ? 'bg-[var(--color-primary-soft)] text-[var(--color-primary)]' : '' }}">{{ __('common.nav_dashboard') }}</a></li>
                <li><a href="{{ route('children.index') }}" class="px-3 py-2 rounded-[var(--radius-sm)] text-sm font-bold text-[var(--color-text-muted)] hover:bg-[var(--color-primary-soft)] hover:text-[var(--color-primary)] transition-colors {{ request()->routeIs('children.*') ? 'bg-[var(--color-primary-soft)] text-[var(--color-primary)]' : '' }}">{{ __('common.nav_children') }}</a></li>
                <li><a href="{{ route('activities.index') }}" class="px-3 py-2 rounded-[var(--radius-sm)] text-sm font-bold text-[var(--color-text-muted)] hover:bg-[var(--color-primary-soft)] hover:text-[var(--color-primary)] transition-colors">{{ __('common.nav_activities') }}</a></li>
            </ul>

            <div class="flex items-center gap-2 ml-auto">
                <x-app.locale-switcher />
                <x-app.theme-toggle />
                <x-ui.avatar :name="$user->name" size="sm" />
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <x-ui.button type="submit" variant="ghost" size="sm" icon="log-out">
                        <span class="hidden sm:inline">{{ __('common.logout') }}</span>
                    </x-ui.button>
                </form>
            </div>
        </nav>
    </div>
</header>

{{-- Mobile bottom tabbar --}}
<nav class="lg:hidden fixed bottom-0 inset-x-0 z-30 bg-white/92 backdrop-blur-xl border-t-[3px] border-[var(--color-border)] shadow-[0_-4px_16px_rgba(30,41,59,0.06)]" aria-label="Mobile navigation">
    <div class="flex items-center justify-around py-2">
        <a href="{{ route('parent.dashboard') }}" class="flex flex-col items-center gap-0.5 px-4 py-1 text-xs font-bold {{ request()->routeIs('parent.dashboard') ? 'text-[var(--color-primary)]' : 'text-[var(--color-text-muted)]' }} transition-colors hover:text-[var(--color-primary)]">
            <x-ui.icon name="home" class="w-5 h-5" />
            Home
        </a>
        <a href="{{ route('children.index') }}" class="flex flex-col items-center gap-0.5 px-4 py-1 text-xs font-bold {{ request()->routeIs('children.*') ? 'text-[var(--color-primary)]' : 'text-[var(--color-text-muted)]' }} transition-colors hover:text-[var(--color-primary)]">
            <x-ui.icon name="users" class="w-5 h-5" />
            Children
        </a>
        <a href="{{ route('activities.index') }}" class="flex flex-col items-center gap-0.5 px-4 py-1 text-xs font-bold {{ request()->routeIs('activities.*') ? 'text-[var(--color-primary)]' : 'text-[var(--color-text-muted)]' }} transition-colors hover:text-[var(--color-primary)]">
            <x-ui.icon name="book-open" class="w-5 h-5" />
            Learn
        </a>
        <a href="{{ route('profile') }}" class="flex flex-col items-center gap-0.5 px-4 py-1 text-xs font-bold {{ request()->routeIs('profile') ? 'text-[var(--color-primary)]' : 'text-[var(--color-text-muted)]' }} transition-colors hover:text-[var(--color-primary)]">
            <x-ui.icon name="user" class="w-5 h-5" />
            Me
        </a>
    </div>
</nav>
