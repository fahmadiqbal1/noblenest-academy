@props([
    'striped'    => false,
    'compact'    => false,
    'responsive' => true,
])

@php
    $wrapClass = $responsive ? 'overflow-x-auto rounded-[var(--radius-card)] border-[2px] border-[var(--color-border)] shadow-[var(--shadow-soft)]' : '';
    $tableClass = 'w-full text-sm text-left text-[var(--color-text)]';
    if ($striped) $tableClass .= ' [&_tbody_tr:nth-child(even)]:bg-[var(--color-brand-50)/30]';
    if ($compact) $tableClass .= ' [&_th]:py-2 [&_td]:py-2';
@endphp

<div class="{{ $wrapClass }}">
    <table {{ $attributes->class([$tableClass]) }}>
        @isset($head)
            <thead class="bg-[var(--color-brand-50)] text-[var(--color-text-muted)] uppercase text-xs tracking-wider">
                {{ $head }}
            </thead>
        @endisset
        <tbody class="divide-y divide-[var(--color-border)] bg-[var(--color-surface-strong)]">
            {{ $slot }}
        </tbody>
    </table>
</div>

@once
    @push('styles')
    <style>
    @layer components {
        .nn-table th { padding: 0.75rem 1rem; font-weight: 600; }
        .nn-table td { padding: 0.875rem 1rem; }
    }
    </style>
    @endpush
@endonce
