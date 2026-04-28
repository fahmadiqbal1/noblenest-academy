@props([
    'id'             => 'modal',
    'title'          => null,
    'size'           => 'md',
    'closeOnOverlay' => true,
])

@php
    $sizes = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-lg',
        'lg' => 'max-w-2xl',
        'xl' => 'max-w-4xl',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

<div
    x-data="{ open: false }"
    x-on:open-modal-{{ $id }}.window="open = true"
    x-on:close-modal-{{ $id }}.window="open = false"
    @keydown.escape.window="open = false"
    id="{{ $id }}"
>
    {{-- Backdrop --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm"
        @if($closeOnOverlay) @click="open = false" @endif
        aria-hidden="true"
    ></div>

    {{-- Panel --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        x-trap.inert.noscroll="open"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        role="dialog"
        aria-modal="true"
        @if($title) aria-labelledby="{{ $id }}-title" @endif
    >
        <div
            class="relative w-full {{ $sizeClass }} bg-[var(--color-surface-strong)] rounded-[var(--radius-card)] border-[3px] border-[var(--color-border)] shadow-[var(--shadow-clay-hover)] overflow-hidden"
            @click.stop
        >
            {{-- Header --}}
            @if($title)
                <div class="flex items-center justify-between px-6 py-4 border-b border-[var(--color-border)]">
                    <h2 id="{{ $id }}-title" class="text-lg font-bold text-[var(--color-text)]">{{ $title }}</h2>
                    <button
                        type="button"
                        @click="open = false"
                        class="rounded p-1.5 text-[var(--color-text-muted)] hover:bg-[var(--color-primary-soft)] hover:text-[var(--color-primary)] focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-1 transition-colors"
                        aria-label="Close dialog"
                    >
                        <x-ui.icon name="x" class="w-5 h-5" />
                    </button>
                </div>
            @endif

            {{-- Body --}}
            <div class="px-6 py-5">
                {{ $slot }}
            </div>

            {{-- Footer actions --}}
            @isset($actions)
                <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-[var(--color-border)] bg-gray-50/50">
                    {{ $actions }}
                </div>
            @endisset
        </div>
    </div>
</div>
