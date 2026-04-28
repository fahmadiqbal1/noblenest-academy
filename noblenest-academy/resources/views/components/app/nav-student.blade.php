@php $user = auth()->user(); @endphp

<header class="sticky top-0 z-30 bg-white/88 backdrop-blur-xl border-b-[3px] border-[var(--color-border)] shadow-[var(--shadow-soft)]">
    <div class="container mx-auto px-4">
        <nav class="flex items-center h-16 gap-4" aria-label="Student navigation">
            <a href="{{ route('noble.home') }}" class="flex items-center gap-2.5 shrink-0 focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2 rounded">
                <img src="{{ asset('brand/noblenest-logo.svg') }}" alt="NobleNest logo" class="w-10 h-10 rounded-[var(--radius-sm)] shadow-[var(--shadow-clay)]" loading="eager">
                <span class="brand-grad font-bold hidden sm:block">NobleNest</span>
            </a>

            <ul class="hidden lg:flex items-center gap-1 ml-4 flex-1" role="list">
                <li><a href="{{ route('marketplace.index') }}" class="px-3 py-2 rounded-[var(--radius-sm)] text-sm font-bold text-[var(--color-text-muted)] hover:bg-[var(--color-primary-soft)] hover:text-[var(--color-primary)] transition-colors {{ request()->routeIs('marketplace.*') ? 'bg-[var(--color-primary-soft)] text-[var(--color-primary)]' : '' }}">Marketplace</a></li>
                <li><a href="{{ route('student.my-courses') }}" class="px-3 py-2 rounded-[var(--radius-sm)] text-sm font-bold text-[var(--color-text-muted)] hover:bg-[var(--color-primary-soft)] hover:text-[var(--color-primary)] transition-colors {{ request()->routeIs('student.my-courses') ? 'bg-[var(--color-primary-soft)] text-[var(--color-primary)]' : '' }}">My Learning</a></li>
            </ul>

            <div class="flex items-center gap-2 ml-auto">
                <x-app.locale-switcher />
                <x-ui.avatar :name="$user->name" size="sm" />
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
