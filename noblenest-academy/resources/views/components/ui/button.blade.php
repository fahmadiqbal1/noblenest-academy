@props([
    'variant'   => 'primary',
    'size'      => 'md',
    'as'        => 'button',
    'href'      => null,
    'loading'   => false,
    'icon'      => null,
    'iconRight' => null,
    'type'      => 'button',
    'disabled'  => false,
])

@php
    $tag = $href ? 'a' : $as;

    $base = 'inline-flex items-center justify-center gap-2 font-bold rounded-[var(--radius-sm)] border-[3px] transition-all duration-[var(--duration-base)] cursor-pointer select-none focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2 disabled:opacity-50 disabled:pointer-events-none';

    $sizes = [
        'sm' => 'text-sm px-3 py-1.5 min-h-[2rem]',
        'md' => 'text-base px-5 py-2.5 min-h-[2.5rem]',
        'lg' => 'text-lg px-7 py-3 min-h-[3rem]',
    ];

    $variants = [
        'primary'   => 'bg-gradient-to-br from-[var(--color-brand-600)] to-[var(--color-brand-400)] border-[var(--color-brand-600)] text-white shadow-[var(--shadow-clay)] hover:-translate-y-[2px] hover:shadow-[var(--shadow-clay-hover)] active:scale-95 active:shadow-[var(--shadow-clay-pressed)]',
        'secondary' => 'bg-[var(--color-surface-strong)] border-[var(--color-border)] text-[var(--color-text)] shadow-[var(--shadow-clay)] hover:-translate-y-[2px] hover:border-[var(--color-brand-400)] active:scale-95',
        'ghost'     => 'bg-transparent border-transparent text-[var(--color-text-muted)] hover:bg-[var(--color-primary-soft)] hover:text-[var(--color-primary)] active:scale-95',
        'danger'    => 'bg-gradient-to-br from-[var(--color-coral-500)] to-[var(--color-coral-400)] border-[var(--color-coral-500)] text-white shadow-[var(--shadow-clay)] hover:-translate-y-[2px] active:scale-95',
        'link'      => 'bg-transparent border-transparent text-[var(--color-primary)] underline-offset-4 hover:underline p-0 min-h-0 shadow-none',
    ];

    $classes = $base . ' ' . ($sizes[$size] ?? $sizes['md']) . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

<{{ $tag }}
    @if($tag === 'a') href="{{ $href }}" @endif
    @if($tag === 'button') type="{{ $type }}" @endif
    @if($disabled || $loading) disabled aria-disabled="true" @endif
    @if($loading) aria-busy="true" @endif
    {{ $attributes->class([$classes]) }}
>
    @if($loading)
        <svg class="animate-spin w-4 h-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
        </svg>
    @elseif($icon)
        <x-ui.icon :name="$icon" class="w-4 h-4 shrink-0" />
    @endif

    {{ $slot }}

    @if($iconRight && !$loading)
        <x-ui.icon :name="$iconRight" class="w-4 h-4 shrink-0" />
    @endif
</{{ $tag }}>
