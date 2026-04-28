@props([
    'name'        => null,
    'value'       => null,
    'placeholder' => null,
    'rows'        => 4,
    'disabled'    => false,
    'invalid'     => false,
    'size'        => 'md',
])

@php
    $sizes = [
        'sm' => 'text-sm py-1.5 px-3',
        'md' => 'text-base py-2.5 px-4',
        'lg' => 'text-lg py-3 px-5',
    ];

    $base = 'block w-full rounded-[var(--radius-sm)] border-[2px] bg-[var(--color-surface-strong)] text-[var(--color-text)] placeholder-[var(--color-text-muted)] transition-colors duration-[var(--duration-fast)] focus:outline-none focus:border-[var(--color-brand-500)] focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed resize-y';

    $borderClass = $invalid
        ? 'border-[var(--color-coral-500)]'
        : 'border-[var(--color-border)]';

    $classes = $base . ' ' . ($sizes[$size] ?? $sizes['md']) . ' ' . $borderClass;
@endphp

<textarea
    @if($name) name="{{ $name }}" id="{{ $name }}" @endif
    rows="{{ $rows }}"
    @if($placeholder) placeholder="{{ $placeholder }}" @endif
    @if($disabled) disabled @endif
    @if($invalid) aria-invalid="true" @endif
    {{ $attributes->class([$classes]) }}
>{{ $value }}</textarea>
