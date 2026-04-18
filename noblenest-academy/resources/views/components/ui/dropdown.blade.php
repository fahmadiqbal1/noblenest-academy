@props([
    'align' => 'left',
    'width' => '48',
])

@php
    $alignClass = $align === 'right' ? 'right-0' : 'left-0';
@endphp

<div
    x-data="{ open: false }"
    @keydown.escape.window="open = false"
    @click.outside="open = false"
    class="relative inline-block"
    {{ $attributes }}
>
    {{-- Trigger --}}
    <div @click="open = !open">
        {{ $trigger }}
    </div>

    {{-- Menu panel --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute top-full mt-2 {{ $alignClass }} z-30 w-{{ $width }} min-w-[12rem] rounded-[var(--radius-sm)] border-[2px] border-[var(--color-border)] bg-[var(--color-surface-strong)] shadow-[var(--shadow-clay)] py-1"
        role="menu"
        @keydown.down.prevent="$focus.wrap().next()"
        @keydown.up.prevent="$focus.wrap().previous()"
    >
        {{ $slot }}
    </div>
</div>
