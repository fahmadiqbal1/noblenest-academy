@php
    $user = auth()->user();
    $role = $user->role ?? 'guest';
@endphp

<header class="sticky top-0 z-30 bg-white/85 backdrop-blur-xl border-b-[3px] border-[var(--color-border)] shadow-[var(--shadow-soft)]">
    <div class="container mx-auto px-4">
        <nav class="flex items-center h-16 gap-4" aria-label="Main navigation">
            {{-- Brand --}}
            <a href="{{ route('noble.home') }}" class="flex items-center gap-2.5 shrink-0 focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2 rounded">
                <img src="{{ asset('brand/noblenest-logo.svg') }}" alt="NobleNest Global Academy logo" class="w-9 h-9 rounded-[var(--radius-sm)] shadow-[var(--shadow-clay)]" loading="eager">
                <span class="brand-grad font-bold hidden sm:block">NobleNest</span>
            </a>

            {{-- Spacer --}}
            <div class="flex-1"></div>

            {{-- Right side --}}
            <div class="flex items-center gap-2">
                <x-app.locale-switcher />
                <x-app.theme-toggle />

                <x-ui.avatar :name="$user->name" size="sm" class="cursor-pointer" />
                <x-ui.badge tone="brand" size="sm">{{ $role }}</x-ui.badge>

                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <x-ui.button type="submit" variant="ghost" size="sm" icon="log-out">
                        <span class="hidden sm:inline">Logout</span>
                    </x-ui.button>
                </form>
            </div>
        </nav>
    </div>
</header>
