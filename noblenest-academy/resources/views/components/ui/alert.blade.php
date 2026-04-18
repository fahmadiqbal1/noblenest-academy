@props([
    'tone'        => 'info',
    'title'       => null,
    'dismissible' => false,
])

@php
    $tones = [
        'info'    => ['bg' => 'bg-blue-50',                     'border' => 'border-blue-200',   'text' => 'text-blue-800',   'icon' => 'info'],
        'success' => ['bg' => 'bg-emerald-50',                  'border' => 'border-emerald-200','text' => 'text-emerald-800','icon' => 'check-circle'],
        'warn'    => ['bg' => 'bg-amber-50',                    'border' => 'border-amber-200',  'text' => 'text-amber-800',  'icon' => 'alert-triangle'],
        'danger'  => ['bg' => 'bg-[var(--color-coral-50)]',    'border' => 'border-[var(--color-coral-200)]', 'text' => 'text-[var(--color-coral-800)]', 'icon' => 'alert-circle'],
    ];

    $t = $tones[$tone] ?? $tones['info'];
@endphp

<div
    @if($dismissible) x-data="{ show: true }" x-show="show" @endif
    role="alert"
    {{ $attributes->class([
        'flex gap-3 rounded-[var(--radius-sm)] border-[2px] p-4',
        $t['bg'], $t['border'], $t['text'],
    ]) }}
>
    <x-ui.icon :name="$t['icon']" class="w-5 h-5 shrink-0 mt-0.5" />

    <div class="flex-1 min-w-0">
        @if($title)
            <p class="font-bold text-sm mb-1">{{ $title }}</p>
        @endif
        <div class="text-sm">{{ $slot }}</div>
    </div>

    @if($dismissible)
        <button
            type="button"
            @click="show = false"
            class="shrink-0 ml-auto -mt-0.5 rounded p-1 hover:bg-black/10 focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-1 transition-colors"
            aria-label="Dismiss"
        >
            <x-ui.icon name="x" class="w-4 h-4" />
        </button>
    @endif
</div>
