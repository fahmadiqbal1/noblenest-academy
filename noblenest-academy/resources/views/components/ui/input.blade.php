@props([
    'type'        => 'text',
    'name'        => null,
    'value'       => null,
    'placeholder' => null,
    'disabled'    => false,
    'icon'        => null,
    'size'        => 'md',
    'invalid'     => false,
])

@php
    $sizes = [
        'sm' => 'text-sm py-1.5 px-3 min-h-[2rem]',
        'md' => 'text-base py-2.5 px-4 min-h-[2.5rem]',
        'lg' => 'text-lg py-3 px-5 min-h-[3rem]',
    ];

    $base = 'block w-full rounded-[var(--radius-sm)] border-[2px] bg-[var(--color-surface-strong)] text-[var(--color-text)] placeholder-[var(--color-text-muted)] transition-colors duration-[var(--duration-fast)] focus:outline-none focus:border-[var(--color-brand-500)] focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

    $borderClass = $invalid
        ? 'border-[var(--color-coral-500)]'
        : 'border-[var(--color-border)]';

    $iconPad = $icon ? 'pl-10' : '';

    $classes = $base . ' ' . ($sizes[$size] ?? $sizes['md']) . ' ' . $borderClass . ' ' . $iconPad;
@endphp

<div class="relative">
    @if($icon)
        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-[var(--color-text-muted)]">
            <x-ui.icon :name="$icon" class="w-4 h-4" />
        </span>
    @endif

    <input
        type="{{ $type }}"
        @if($name) name="{{ $name }}" id="{{ $name }}" @endif
        @if($value !== null) value="{{ $value }}" @endif
        @if($placeholder) placeholder="{{ $placeholder }}" @endif
        @if($disabled) disabled @endif
        @if($invalid) aria-invalid="true" @endif
        {{ $attributes->class([$classes]) }}
    />
</div>
