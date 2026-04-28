@php
    $user = auth()->user();
@endphp

<header class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
    <div class="container mx-auto px-4">
        <nav class="flex items-center h-14 gap-4" aria-label="Admin navigation">
            <a href="{{ route('noble.home') }}" class="flex items-center gap-2 shrink-0 focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2 rounded">
                <img src="{{ asset('brand/noblenest-logo.svg') }}" alt="" class="w-7 h-7 rounded-lg" loading="eager">
                <span class="font-bold text-sm text-[var(--color-text)]">NobleNest <span class="text-[var(--color-text-muted)] font-medium">Admin</span></span>
            </a>

            <ul class="hidden lg:flex items-center gap-0.5 ml-4 text-sm" role="list">
                <li><a href="{{ route('admin.courses.index') }}"    class="px-3 py-1.5 rounded font-semibold text-[var(--color-text-muted)] hover:bg-gray-100 hover:text-[var(--color-text)] transition-colors {{ request()->routeIs('admin.courses.*') ? 'bg-gray-100 text-[var(--color-text)]' : '' }}">Courses</a></li>
                <li><a href="{{ route('admin.users.index') }}"      class="px-3 py-1.5 rounded font-semibold text-[var(--color-text-muted)] hover:bg-gray-100 hover:text-[var(--color-text)] transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-gray-100 text-[var(--color-text)]' : '' }}">Users</a></li>
                <li><a href="{{ route('admin.children.index') }}"   class="px-3 py-1.5 rounded font-semibold text-[var(--color-text-muted)] hover:bg-gray-100 hover:text-[var(--color-text)] transition-colors {{ request()->routeIs('admin.children.*') ? 'bg-gray-100 text-[var(--color-text)]' : '' }}">Children</a></li>
                <li><a href="{{ route('admin.analytics.index') }}"  class="px-3 py-1.5 rounded font-semibold text-[var(--color-text-muted)] hover:bg-gray-100 hover:text-[var(--color-text)] transition-colors {{ request()->routeIs('admin.analytics.*') ? 'bg-gray-100 text-[var(--color-text)]' : '' }}">Analytics</a></li>
                <li><a href="{{ route('admin.orchestrator.index') }}" class="px-3 py-1.5 rounded font-semibold text-[var(--color-text-muted)] hover:bg-gray-100 hover:text-[var(--color-text)] transition-colors {{ request()->routeIs('admin.orchestrator.*') ? 'bg-gray-100 text-[var(--color-text)]' : '' }}">Orchestrator</a></li>
            </ul>

            <div class="flex items-center gap-2 ml-auto">
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
