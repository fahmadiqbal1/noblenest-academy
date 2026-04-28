@props([
    'tone'    => 'neutral',
    'size'    => 'md',
    'variant' => 'soft',
])

@php
    $tones = [
        'neutral'      => ['solid' => 'bg-gray-700 text-white',             'soft' => 'bg-gray-100 text-gray-700',               'outline' => 'border border-gray-400 text-gray-700'],
        'brand'        => ['solid' => 'bg-[var(--color-brand-600)] text-white', 'soft' => 'bg-[var(--color-brand-100)] text-[var(--color-brand-700)]', 'outline' => 'border border-[var(--color-brand-400)] text-[var(--color-brand-700)]'],
        'success'      => ['solid' => 'bg-emerald-600 text-white',           'soft' => 'bg-emerald-50 text-emerald-700',          'outline' => 'border border-emerald-400 text-emerald-700'],
        'warning'      => ['solid' => 'bg-amber-500 text-white',             'soft' => 'bg-amber-50 text-amber-700',              'outline' => 'border border-amber-400 text-amber-700'],
        'danger'       => ['solid' => 'bg-[var(--color-coral-500)] text-white', 'soft' => 'bg-[var(--color-coral-50)] text-[var(--color-coral-700)]', 'outline' => 'border border-[var(--color-coral-400)] text-[var(--color-coral-700)]'],
        'info'         => ['solid' => 'bg-blue-600 text-white',              'soft' => 'bg-blue-50 text-blue-700',                'outline' => 'border border-blue-400 text-blue-700'],
        'tier-baby'    => ['solid' => 'bg-[var(--color-tier-baby)] text-white', 'soft' => 'bg-[var(--color-tier-baby-bg)] text-[var(--color-tier-baby-text)]', 'outline' => 'border border-[var(--color-tier-baby)] text-[var(--color-tier-baby-text)]'],
        'tier-toddler' => ['solid' => 'bg-[var(--color-tier-toddler)] text-white', 'soft' => 'bg-[var(--color-tier-toddler-bg)] text-[var(--color-tier-toddler-text)]', 'outline' => 'border border-[var(--color-tier-toddler)] text-[var(--color-tier-toddler-text)]'],
        'subject-math' => ['solid' => 'bg-amber-400 text-amber-900',         'soft' => 'bg-[var(--color-cream)] text-amber-900',  'outline' => 'border border-amber-400 text-amber-900'],
    ];

    $sizes = [
        'sm' => 'text-xs px-2 py-0.5 rounded-full',
        'md' => 'text-sm px-2.5 py-1 rounded-full',
    ];

    $toneVariant = $tones[$tone][$variant] ?? $tones['neutral']['soft'];
    $classes = 'inline-flex items-center gap-1 font-semibold ' . ($sizes[$size] ?? $sizes['md']) . ' ' . $toneVariant;
@endphp

<span {{ $attributes->class([$classes]) }}>
    {{ $slot }}
</span>
