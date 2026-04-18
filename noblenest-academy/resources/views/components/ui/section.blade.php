@props([
    'title'    => null,
    'subtitle' => null,
])

<section {{ $attributes->class(['py-6']) }}>
    @if($title || isset($actions))
        <div class="flex flex-wrap items-start justify-between gap-4 mb-5">
            <div>
                @if($title)
                    <h2 class="text-2xl font-bold text-[var(--color-text)]">{{ $title }}</h2>
                @endif
                @if($subtitle)
                    <p class="text-[var(--color-text-muted)] mt-1">{{ $subtitle }}</p>
                @endif
            </div>
            @isset($actions)
                <div class="flex items-center gap-2 shrink-0">{{ $actions }}</div>
            @endisset
        </div>
    @endif

    {{ $slot }}
</section>
