@props([
    'icon'        => null,
    'title'       => 'Nothing here yet',
    'description' => null,
])

<div {{ $attributes->class(['flex flex-col items-center justify-center text-center py-16 px-6']) }}>
    @isset($illustration)
        <div class="mb-6">{{ $illustration }}</div>
    @elseif($icon)
        <div class="mb-6 w-20 h-20 rounded-full bg-[var(--color-primary-soft)] flex items-center justify-center">
            <x-ui.icon :name="$icon" class="w-10 h-10 text-[var(--color-primary)]" />
        </div>
    @else
        <div class="mb-6 text-6xl select-none" aria-hidden="true">📭</div>
    @endif

    <h3 class="text-xl font-bold text-[var(--color-text)] mb-2">{{ $title }}</h3>

    @if($description)
        <p class="text-[var(--color-text-muted)] max-w-sm mb-6">{{ $description }}</p>
    @endif

    @isset($actions)
        <div class="flex flex-wrap gap-3 justify-center">
            {{ $actions }}
        </div>
    @endisset

    {{ $slot }}
</div>
