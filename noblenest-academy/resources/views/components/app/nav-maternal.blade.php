@php $user = auth()->user(); @endphp

<header class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
    <div class="container mx-auto px-4">
        <nav class="flex items-center h-14 gap-4" aria-label="Maternal navigation">
            <a href="{{ route('noble.home') }}" class="flex items-center gap-2 shrink-0 focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2 rounded">
                <img src="{{ asset('brand/noblenest-logo.svg') }}" alt="" class="w-8 h-8 rounded-lg" loading="eager">
                <span class="font-bold text-sm text-[var(--color-text)]">NobleNest <span class="text-[var(--color-text-muted)] font-medium">Maternal</span></span>
            </a>

            <ul class="hidden lg:flex items-center gap-0.5 ml-4 text-sm" role="list">
                <li><a href="{{ route('maternal.dashboard') }}" class="px-3 py-1.5 rounded font-semibold text-[var(--color-text-muted)] hover:bg-gray-100 hover:text-[var(--color-text)] transition-colors {{ request()->routeIs('maternal.dashboard') ? 'bg-gray-100 text-[var(--color-text)]' : '' }}">Dashboard</a></li>
                <li><a href="{{ route('maternal.profile') }}" class="px-3 py-1.5 rounded font-semibold text-[var(--color-text-muted)] hover:bg-gray-100 hover:text-[var(--color-text)] transition-colors {{ request()->routeIs('maternal.profile') ? 'bg-gray-100 text-[var(--color-text)]' : '' }}">My Profile</a></li>
            </ul>

            {{-- Emergency CTA --}}
            <a href="tel:911" class="hidden lg:inline-flex items-center gap-1.5 ml-auto px-3 py-1.5 rounded-[var(--radius-sm)] text-xs font-bold text-[var(--color-coral-700)] bg-[var(--color-coral-50)] border border-[var(--color-coral-200)] hover:bg-[var(--color-coral-100)] transition-colors">
                <x-ui.icon name="alert-circle" class="w-3.5 h-3.5" />
                Emergency
            </a>

            <div class="flex items-center gap-2 ml-auto lg:ml-2">
                <x-app.locale-switcher />
                <x-ui.avatar :name="$user->name" size="sm" />
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <x-ui.button type="submit" variant="ghost" size="sm" icon="log-out">Logout</x-ui.button>
                </form>
            </div>
        </nav>
    </div>
</header>
