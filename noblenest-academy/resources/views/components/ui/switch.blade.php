@props([
    'name'    => null,
    'label'   => null,
    'checked' => false,
])

<div
    x-data="{ on: {{ $checked ? 'true' : 'false' }} }"
    class="inline-flex items-center gap-3"
>
    {{-- Hidden input for form submission --}}
    @if($name)
        <input type="hidden" :name="'{{ $name }}'" :value="on ? '1' : '0'">
    @endif

    <button
        type="button"
        role="switch"
        :aria-checked="on.toString()"
        @click="on = !on"
        :class="on ? 'bg-[var(--color-brand-600)]' : 'bg-[var(--color-text-muted)]'"
        class="relative inline-flex h-6 w-11 shrink-0 rounded-full border-2 border-transparent transition-colors duration-[var(--duration-fast)] focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2 cursor-pointer"
        {{ $attributes }}
    >
        <span
            :class="on ? 'translate-x-5' : 'translate-x-0'"
            class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-md transition-transform duration-[var(--duration-fast)]"
        ></span>
    </button>

    @if($label)
        <span class="text-sm font-medium text-[var(--color-text)] select-none">{{ $label }}</span>
    @endif
</div>
