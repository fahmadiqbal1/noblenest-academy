@props(['name', 'class' => 'w-5 h-5'])
@php
    /** @var \App\View\Components\Ui\Icon $component */
    $paths = $component->svgPaths();
    $known = $component->isKnown();
@endphp
@if($known)
<svg
    xmlns="http://www.w3.org/2000/svg"
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    stroke-width="2"
    stroke-linecap="round"
    stroke-linejoin="round"
    aria-hidden="true"
    focusable="false"
    {{ $attributes->merge(['class' => $class]) }}
>{!! $paths !!}</svg>
@else
{{-- Unknown icon: render a small placeholder square for visibility during dev --}}
<span
    title="Unknown icon: {{ $name }}"
    {{ $attributes->merge(['class' => $class . ' inline-block bg-gray-200 rounded']) }}
></span>
@endif
