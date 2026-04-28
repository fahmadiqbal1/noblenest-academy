@props([
    'variant' => 'line',
    'width'   => null,
    'height'  => null,
    'count'   => 1,
])

@php
    $base = 'animate-pulse bg-[var(--color-border)] rounded';

    $shapes = [
        'line'   => 'h-4 rounded',
        'card'   => 'h-32 rounded-[var(--radius-card)]',
        'avatar' => 'w-10 h-10 rounded-full',
        'circle' => 'rounded-full',
    ];

    $shapeClass = $shapes[$variant] ?? $shapes['line'];

    $style = '';
    if ($width)  $style .= "width:{$width};";
    if ($height) $style .= "height:{$height};";
@endphp

@for($i = 0; $i < $count; $i++)
    <div
        {{ $attributes->class([$base, $shapeClass]) }}
        @if($style) style="{{ $style }}" @endif
        aria-hidden="true"
    ></div>
@endfor
