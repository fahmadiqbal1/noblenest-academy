@props([
    'items'  => [],
    'active' => null,
])

@php
    // If no active set, use first item
    $firstId = count($items) > 0 ? (is_array($items[0]) ? ($items[0]['id'] ?? '') : $items[0]) : '';
    $activeId = $active ?? $firstId;
@endphp

<div
    x-data="{ active: '{{ $activeId }}' }"
    {{ $attributes }}
>
    {{-- Tab list --}}
    <div role="tablist" aria-label="Tabs" class="flex border-b-[2px] border-[var(--color-border)] overflow-x-auto gap-0.5">
        @foreach($items as $item)
            @php
                $id    = is_array($item) ? ($item['id']    ?? $loop->index) : $item;
                $label = is_array($item) ? ($item['label'] ?? $id) : $item;
                $icon  = is_array($item) ? ($item['icon']  ?? null) : null;
            @endphp
            <button
                type="button"
                role="tab"
                :aria-selected="active === '{{ $id }}'"
                :tabindex="active === '{{ $id }}' ? 0 : -1"
                @click="active = '{{ $id }}'"
                @keydown.arrow-right.prevent="$focus.next()"
                @keydown.arrow-left.prevent="$focus.previous()"
                :class="active === '{{ $id }}' ? 'border-[var(--color-brand-600)] text-[var(--color-brand-700)] font-bold' : 'border-transparent text-[var(--color-text-muted)] hover:text-[var(--color-text)] hover:border-[var(--color-border)]'"
                class="inline-flex items-center gap-1.5 shrink-0 px-4 py-2.5 text-sm font-medium border-b-[2px] -mb-[2px] transition-colors duration-[var(--duration-fast)] focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-0 cursor-pointer"
                id="tab-{{ $id }}"
                aria-controls="panel-{{ $id }}"
            >
                @if($icon)
                    <x-ui.icon :name="$icon" class="w-4 h-4" />
                @endif
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- Panels slot --}}
    <div class="mt-4">
        {{ $slot }}
    </div>
</div>
