@props([
    'name'     => null,
    'label'    => null,
    'checked'  => false,
    'value'    => null,
    'disabled' => false,
])

<label class="inline-flex items-center gap-2.5 cursor-pointer select-none {{ $disabled ? 'opacity-50 cursor-not-allowed' : '' }}">
    <input
        type="radio"
        @if($name) name="{{ $name }}" @endif
        @if($value !== null) value="{{ $value }}" id="{{ $name }}_{{ $value }}" @endif
        @checked($checked)
        @if($disabled) disabled @endif
        {{ $attributes->class([
            'w-4 h-4 border-[2px] border-[var(--color-border)] text-[var(--color-primary)] bg-[var(--color-surface-strong)] focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2 transition-colors cursor-pointer'
        ]) }}
    />
    @if($label)
        <span class="text-sm font-medium text-[var(--color-text)]">{{ $label }}</span>
    @endif
    {{ $slot }}
</label>
