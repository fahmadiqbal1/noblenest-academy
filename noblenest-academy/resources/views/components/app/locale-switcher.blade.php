@php
    use App\Helpers\I18n;
    $currentLang = app()->getLocale();
    $names = I18n::SUPPORTED_LANGUAGES; // ['en' => 'English', 'fr' => 'Français', ...]
@endphp

<div
    x-data="{ open: false }"
    @keydown.escape.window="open = false"
    @click.outside="open = false"
    class="relative inline-block text-left"
>
    <button
        type="button"
        @click="open = !open"
        :aria-expanded="open"
        aria-haspopup="true"
        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border-2 border-[var(--color-border)] bg-white text-sm font-semibold text-[var(--color-text)] hover:border-[var(--color-brand-400)] transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--color-brand-600)]"
        aria-label="Select language"
    >
        <x-ui.icon name="settings" class="w-4 h-4 text-[var(--color-text-muted)]" />
        <span>{{ strtoupper($currentLang) }}</span>
        <svg class="w-3 h-3 text-[var(--color-text-muted)] transition-transform" :class="open && 'rotate-180'" viewBox="0 0 12 12" fill="none">
            <path d="M3 4.5 6 7.5 9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>

    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-1"
        class="absolute right-0 top-full mt-2 z-50 w-44 max-h-80 overflow-y-auto rounded-xl border-2 border-[var(--color-border)] bg-white shadow-lg py-1"
        role="menu"
        aria-label="Languages"
    >
        @foreach($names as $code => $label)
            <a
                href="/lang/{{ $code }}"
                role="menuitem"
                @class([
                    'flex items-center justify-between gap-3 px-4 py-2 text-sm transition-colors',
                    'bg-[var(--color-primary-soft)] text-[var(--color-primary)] font-bold' => $code === $currentLang,
                    'text-[var(--color-text)] font-medium hover:bg-[var(--color-primary-soft)] hover:text-[var(--color-primary)]' => $code !== $currentLang,
                ])
                @if($code === $currentLang) aria-current="true" @endif
                @if(in_array($code, ['ur','ar'], true)) dir="rtl" @endif
            >
                <span>{{ $label }}</span>
                <span class="text-xs uppercase tracking-wide text-[var(--color-text-muted)]">{{ strtoupper($code) }}</span>
            </a>
        @endforeach
    </div>
</div>
