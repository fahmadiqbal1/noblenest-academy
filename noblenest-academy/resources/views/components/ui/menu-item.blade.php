@props([
    'href'   => null,
    'icon'   => null,
    'danger' => false,
    'as'     => 'a',
])

@php
    $tag = $href ? 'a' : $as;
    $colorClass = $danger
        ? 'text-[var(--color-coral-600)] hover:bg-[var(--color-coral-50)] hover:text-[var(--color-coral-700)]'
        : 'text-[var(--color-text)] hover:bg-[var(--color-primary-soft)] hover:text-[var(--color-primary)]';
@endphp

<{{ $tag }}
    @if($href) href="{{ $href }}" @endif
    role="menuitem"
    {{ $attributes->class([
        'flex items-center gap-2.5 w-full px-4 py-2 text-sm font-semibold rounded transition-colors duration-[var(--duration-fast)] focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-1 cursor-pointer',
        $colorClass,
    ]) }}
>
    @if($icon)
        <x-ui.icon :name="$icon" class="w-4 h-4 shrink-0" />
    @endif
    {{ $slot }}
</{{ $tag }}>
