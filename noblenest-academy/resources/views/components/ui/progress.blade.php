@props([
    'value'   => 0,
    'label'   => null,
    'tone'    => 'brand',
    'variant' => 'linear',
    'size'    => 'md',
])

@php
    $clamped = max(0, min(100, (int) $value));

    $toneColors = [
        'brand'   => 'var(--color-brand-600)',
        'success' => 'var(--color-success)',
        'warning' => 'var(--color-warning)',
        'danger'  => 'var(--color-danger)',
    ];
    $color = $toneColors[$tone] ?? $toneColors['brand'];

    $barSizes = [
        'sm' => 'h-1.5',
        'md' => 'h-2.5',
        'lg' => 'h-4',
    ];
    $barSize = $barSizes[$size] ?? $barSizes['md'];

    // Ring dimensions
    $ringSize = match($size) { 'sm' => 48, 'lg' => 96, default => 64 };
    $stroke   = match($size) { 'sm' => 4,  'lg' => 8,  default => 6  };
    $r        = ($ringSize / 2) - $stroke;
    $circ     = round(2 * M_PI * $r, 2);
    $dash     = round($circ * $clamped / 100, 2);
@endphp

@if($variant === 'ring')
    <div {{ $attributes->class(['inline-flex flex-col items-center gap-1']) }}>
        <svg width="{{ $ringSize }}" height="{{ $ringSize }}" viewBox="0 0 {{ $ringSize }} {{ $ringSize }}" aria-hidden="true">
            {{-- Track --}}
            <circle
                cx="{{ $ringSize / 2 }}"
                cy="{{ $ringSize / 2 }}"
                r="{{ $r }}"
                fill="none"
                stroke="var(--color-border)"
                stroke-width="{{ $stroke }}"
            />
            {{-- Progress --}}
            <circle
                cx="{{ $ringSize / 2 }}"
                cy="{{ $ringSize / 2 }}"
                r="{{ $r }}"
                fill="none"
                stroke="{{ $color }}"
                stroke-width="{{ $stroke }}"
                stroke-linecap="round"
                stroke-dasharray="{{ $dash }} {{ $circ }}"
                transform="rotate(-90 {{ $ringSize / 2 }} {{ $ringSize / 2 }})"
            />
            <text
                x="{{ $ringSize / 2 }}"
                y="{{ $ringSize / 2 }}"
                text-anchor="middle"
                dominant-baseline="central"
                font-size="{{ $ringSize * 0.22 }}"
                font-weight="700"
                fill="var(--color-text)"
            >{{ $clamped }}%</text>
        </svg>
        @if($label)
            <span class="text-xs text-[var(--color-text-muted)] font-medium">{{ $label }}</span>
        @endif
    </div>
@else
    <div {{ $attributes->class(['space-y-1']) }}>
        @if($label)
            <div class="flex justify-between items-center text-xs font-medium text-[var(--color-text-muted)]">
                <span>{{ $label }}</span>
                <span>{{ $clamped }}%</span>
            </div>
        @endif
        <div class="w-full {{ $barSize }} bg-[var(--color-border)] rounded-full overflow-hidden" role="progressbar" aria-valuenow="{{ $clamped }}" aria-valuemin="0" aria-valuemax="100">
            <div
                class="{{ $barSize }} rounded-full transition-all duration-[var(--duration-slow)]"
                style="width: {{ $clamped }}%; background-color: {{ $color }};"
            ></div>
        </div>
    </div>
@endif
