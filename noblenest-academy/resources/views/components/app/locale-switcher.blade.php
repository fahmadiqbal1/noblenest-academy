@php
    $currentLang = session('lang', 'en');
    $languages = App\Helpers\I18n::availableLanguages();
@endphp

<x-ui.dropdown align="right" width="32">
    <x-slot:trigger>
        <button
            type="button"
            class="inline-flex items-center gap-1.5 px-3 py-2 rounded-[var(--radius-sm)] border-[2px] border-[var(--color-border)] bg-[var(--color-surface-strong)] text-sm font-semibold text-[var(--color-text)] hover:border-[var(--color-brand-400)] transition-colors focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2"
            aria-label="Select language"
        >
            <x-ui.icon name="settings" class="w-4 h-4 text-[var(--color-text-muted)]" />
            {{ strtoupper($currentLang) }}
            <x-ui.icon name="chevron-down" class="w-3 h-3 text-[var(--color-text-muted)]" />
        </button>
    </x-slot:trigger>

    @foreach($languages as $lang)
        <x-ui.menu-item href="/lang/{{ $lang }}">
            {{ strtoupper($lang) }}
        </x-ui.menu-item>
    @endforeach
</x-ui.dropdown>
