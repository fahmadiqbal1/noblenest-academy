@props([
    'title'       => '',
    'subtitle'    => null,
    'breadcrumbs' => [],
])

<div {{ $attributes->class(['mb-8']) }}>
    {{-- Breadcrumbs --}}
    @if(count($breadcrumbs) > 0)
        <nav aria-label="Breadcrumb" class="mb-3">
            <ol class="flex flex-wrap items-center gap-1 text-sm text-[var(--color-text-muted)]">
                @foreach($breadcrumbs as $crumb)
                    @php
                        $url   = is_array($crumb) ? ($crumb['url'] ?? null) : null;
                        $label = is_array($crumb) ? ($crumb['label'] ?? $crumb[0] ?? '') : $crumb;
                        $isLast = $loop->last;
                    @endphp
                    <li class="flex items-center gap-1">
                        @if(!$isLast && $url)
                            <a href="{{ $url }}" class="hover:text-[var(--color-primary)] transition-colors focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-1 rounded">
                                {{ $label }}
                            </a>
                            <x-ui.icon name="chevron-right" class="w-3 h-3" />
                        @else
                            <span @if($isLast) aria-current="page" class="font-semibold text-[var(--color-text)]" @endif>
                                {{ $label }}
                            </span>
                        @endif
                    </li>
                @endforeach
            </ol>
        </nav>
    @endif

    {{-- Title row --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-[var(--color-text)]">{{ $title }}</h1>
            @if($subtitle)
                <p class="text-[var(--color-text-muted)] mt-1">{{ $subtitle }}</p>
            @endif
        </div>

        @isset($actions)
            <div class="flex items-center gap-2 shrink-0">{{ $actions }}</div>
        @endisset
    </div>
</div>
