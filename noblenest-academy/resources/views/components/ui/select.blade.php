@props([
    'name'        => null,
    'options'     => [],
    'value'       => null,
    'placeholder' => null,
    'disabled'    => false,
    'invalid'     => false,
    'size'        => 'md',
])

@php
    $sizes = [
        'sm' => 'text-sm py-1.5 pl-3 pr-8 min-h-[2rem]',
        'md' => 'text-base py-2.5 pl-4 pr-10 min-h-[2.5rem]',
        'lg' => 'text-lg py-3 pl-5 pr-12 min-h-[3rem]',
    ];

    $base = 'block w-full rounded-[var(--radius-sm)] border-[2px] bg-[var(--color-surface-strong)] text-[var(--color-text)] transition-colors duration-[var(--duration-fast)] focus:outline-none focus:border-[var(--color-brand-500)] focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed appearance-none cursor-pointer';

    $borderClass = $invalid
        ? 'border-[var(--color-coral-500)]'
        : 'border-[var(--color-border)]';

    $classes = $base . ' ' . ($sizes[$size] ?? $sizes['md']) . ' ' . $borderClass;
@endphp

<div class="relative">
    <select
        @if($name) name="{{ $name }}" id="{{ $name }}" @endif
        @if($disabled) disabled @endif
        @if($invalid) aria-invalid="true" @endif
        {{ $attributes->class([$classes]) }}
    >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach($options as $optVal => $optLabel)
            @if(is_array($optLabel))
                <option value="{{ $optLabel['value'] }}" @selected($value == $optLabel['value'])>
                    {{ $optLabel['label'] }}
                </option>
            @else
                <option value="{{ $optVal }}" @selected($value == $optVal)>
                    {{ $optLabel }}
                </option>
            @endif
        @endforeach
    </select>

    <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-[var(--color-text-muted)]">
        <x-ui.icon name="chevron-down" class="w-4 h-4" />
    </span>
</div>
