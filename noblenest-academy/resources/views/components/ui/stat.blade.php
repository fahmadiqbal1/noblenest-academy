@props([
    'label'     => '',
    'value'     => '',
    'delta'     => null,
    'deltaTone' => 'neutral',
    'icon'      => null,
])

@php
    $deltaColors = [
        'positive' => 'text-emerald-600',
        'negative' => 'text-[var(--color-coral-500)]',
        'neutral'  => 'text-[var(--color-text-muted)]',
    ];
    $deltaIcon = match($deltaTone) {
        'positive' => '▲',
        'negative' => '▼',
        default    => '–',
    };
    $deltaClass = $deltaColors[$deltaTone] ?? $deltaColors['neutral'];
@endphp

<div {{ $attributes->class(['rounded-[var(--radius-card)] border-[3px] border-[var(--color-border)] bg-[var(--color-surface-strong)] shadow-[var(--shadow-clay)] p-5 flex flex-col gap-2']) }}>
    <div class="flex items-start justify-between gap-2">
        <p class="text-xs font-semibold uppercase tracking-wider text-[var(--color-text-muted)]">{{ $label }}</p>
        @if($icon)
            <span class="text-[var(--color-primary)] opacity-70">
                <x-ui.icon :name="$icon" class="w-5 h-5" />
            </span>
        @endif
    </div>

    <p class="text-3xl font-bold tabular-nums font-[var(--font-sans)] text-[var(--color-text)] leading-none">{{ $value }}</p>

    @if($delta !== null)
        <p class="text-xs font-semibold {{ $deltaClass }}">
            {{ $deltaIcon }} {{ $delta }}
        </p>
    @endif
</div>
