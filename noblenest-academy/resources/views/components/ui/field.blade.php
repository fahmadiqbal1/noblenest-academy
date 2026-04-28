@props([
    'label'    => null,
    'name'     => null,
    'help'     => null,
    'error'    => null,
    'required' => false,
])

@php
    $helpId  = $name ? $name . '_help'  : null;
    $errorId = $name ? $name . '_error' : null;
@endphp

<div {{ $attributes->class(['space-y-1']) }}>
    @if($label)
        <label
            @if($name) for="{{ $name }}" @endif
            class="block text-sm font-semibold text-[var(--color-text)]"
        >
            {{ $label }}
            @if($required)
                <span class="text-[var(--color-coral-500)] ml-0.5" aria-hidden="true">*</span>
            @endif
        </label>
    @endif

    {{-- The slotted input/select/textarea —
         pass aria-describedby and aria-invalid via attributes on the child component --}}
    <div
        @if($helpId || $errorId)
            data-describedby="{{ implode(' ', array_filter([$error ? $errorId : null, $helpId])) }}"
        @endif
    >
        {{ $slot }}
    </div>

    @if($error)
        <p id="{{ $errorId }}" class="text-sm text-[var(--color-coral-500)] flex items-center gap-1" role="alert">
            <x-ui.icon name="alert-circle" class="w-3.5 h-3.5 shrink-0" />
            {{ $error }}
        </p>
    @elseif($help)
        <p id="{{ $helpId }}" class="text-sm text-[var(--color-text-muted)]">{{ $help }}</p>
    @endif
</div>
