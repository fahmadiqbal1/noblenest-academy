@props([
    'variant' => 'clay',
    'tone'    => null,
    'padding' => 'md',
    'as'      => 'div',
    'href'    => null,
])

@php
    $tag = $href ? 'a' : $as;

    $base = 'rounded-[var(--radius-card)] transition-all duration-[var(--duration-base)]';

    $variants = [
        'flat'     => 'bg-[var(--color-surface-strong)]',
        'clay'     => 'bg-[var(--color-surface-strong)] border-[3px] border-[var(--color-border)] shadow-[var(--shadow-clay)] hover:-translate-y-1 hover:shadow-[var(--shadow-clay-hover)]',
        'outlined' => 'bg-transparent border-[2px] border-[var(--color-border)]',
        'gradient' => 'bg-gradient-to-br from-[var(--color-brand-50)] to-[var(--color-accent-50)] border-[3px] border-[var(--color-border)] shadow-[var(--shadow-clay)]',
    ];

    $paddings = [
        'none' => '',
        'sm'   => 'p-3',
        'md'   => 'p-5',
        'lg'   => 'p-8',
    ];

    $tones = [
        'baby'      => 'border-[var(--color-tier-baby)] bg-[var(--color-tier-baby-bg)]',
        'toddler'   => 'border-[var(--color-tier-toddler)] bg-[var(--color-tier-toddler-bg)]',
        'preschool' => 'border-[var(--color-tier-preschool)] bg-[var(--color-tier-preschool-bg)]',
        'primary'   => 'border-[var(--color-tier-primary)] bg-[var(--color-tier-primary-bg)]',
        'math'      => 'border-[var(--color-subject-math)] bg-[var(--color-cream)]',
        'science'   => 'border-[var(--color-subject-science)] bg-[var(--color-mint)]',
        'reading'   => 'border-[var(--color-subject-reading)] bg-[var(--color-baby-blue)]',
        'art'       => 'border-[var(--color-subject-art)] bg-[var(--color-peach)]',
        'music'     => 'border-[var(--color-subject-music)] bg-[var(--color-lilac)]',
    ];

    $toneClass = $tone && isset($tones[$tone]) ? $tones[$tone] : '';
    $classes = $base . ' ' . ($variants[$variant] ?? $variants['clay']) . ' ' . ($paddings[$padding] ?? $paddings['md']) . ' ' . $toneClass;

    if ($href) {
        $classes .= ' block focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2';
    }
@endphp

<{{ $tag }}
    @if($href) href="{{ $href }}" @endif
    {{ $attributes->class([$classes]) }}
>
    {{ $slot }}
</{{ $tag }}>
