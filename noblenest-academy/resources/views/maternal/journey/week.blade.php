@extends('layouts.maternal')

@section('title', __('Week :week — Maternal Journey', ['week' => $week]))

@section('content')
<div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <x-ui.page-header
        :title="__('Week :week', ['week' => $week])"
        :subtitle="$profile->stage . ' · ' . $content->count() . ' ' . __('activities available')"
        :breadcrumbs="[
            ['label' => __('Journey'), 'url' => route('maternal.journey')],
            ['label' => __('Week :week', ['week' => $week])],
        ]"
    />

    {{-- Emergency Signs — dominant alert if present --}}
    @if($emergencySigns->isNotEmpty())
        <x-ui.alert tone="danger" :title="__('Watch for these signs this week')">
            <ul class="mt-1 space-y-1">
                @foreach($emergencySigns as $sign)
                    <li>
                        <strong>{{ $sign->symptom }}</strong>
                        @if($sign->severity)
                            <x-ui.badge tone="danger" size="sm" class="ms-1">{{ $sign->severity }}</x-ui.badge>
                        @endif
                        — {{ $sign->action_text }}
                    </li>
                @endforeach
            </ul>
            <a href="{{ route('maternal.emergency-signs') }}" class="mt-2 inline-flex items-center gap-1 font-semibold underline underline-offset-2 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--color-coral-500)]">
                {{ __('All emergency signs') }}
                <x-ui.icon name="arrow-right" class="w-3.5 h-3.5" />
            </a>
        </x-ui.alert>
    @else
        <div class="flex items-center gap-2 text-sm text-[var(--color-text-muted)]">
            <x-ui.icon name="alert-circle" class="w-4 h-4 shrink-0" />
            <a href="{{ route('maternal.emergency-signs') }}" class="underline underline-offset-2 hover:text-[var(--color-text)] focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-[var(--color-brand-600)] rounded">
                {{ __('Review emergency warning signs') }}
            </a>
        </div>
    @endif

    {{-- Weekly content grid --}}
    <x-ui.section :title="__('This Week\'s Content')">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @forelse($content as $item)
                <x-ui.card variant="clay" padding="none" class="flex flex-col">
                    @if($item->thumbnail_url)
                        <x-ui.img
                            src="{{ $item->thumbnail_url }}"
                            alt="{{ $item->title }}"
                            class="w-full h-36 object-cover rounded-t-[var(--radius-card)]"
                        />
                    @endif
                    <div class="p-4 flex flex-col flex-1">
                        <div class="flex flex-wrap gap-1.5 mb-2">
                            <x-ui.badge tone="brand" size="sm">{{ ucfirst($item->content_type) }}</x-ui.badge>
                            @if($item->cultural_origin)
                                <x-ui.badge tone="warning" size="sm">{{ ucfirst($item->cultural_origin) }}</x-ui.badge>
                            @endif
                        </div>
                        <h3 class="font-bold text-[var(--color-text)] text-base leading-snug mb-1">{{ $item->title }}</h3>
                        <p class="text-sm text-[var(--color-text-muted)] mb-3 flex-1 leading-relaxed">
                            {{ Str::limit($item->benefit_explanation, 100) }}
                        </p>
                        @if($item->skills_improved)
                            <div class="flex flex-wrap gap-1 mb-3">
                                @foreach(array_slice($item->skills_improved, 0, 3) as $skill)
                                    <x-ui.badge tone="success" size="sm">{{ $skill }}</x-ui.badge>
                                @endforeach
                            </div>
                        @endif
                        <x-ui.button variant="secondary" size="sm" icon-right="arrow-right" href="{{ route('maternal.content.show', $item) }}">
                            {{ __('View') }}
                        </x-ui.button>
                    </div>
                </x-ui.card>
            @empty
                <div class="col-span-full">
                    <x-ui.empty-state
                        icon="book-open"
                        :title="__('No content for this week yet')"
                        :description="__('Browse all content to find something for your stage.')"
                    >
                        <x-slot:actions>
                            <x-ui.button variant="secondary" icon="book" href="{{ route('maternal.content.index') }}">
                                {{ __('Browse All Content') }}
                            </x-ui.button>
                        </x-slot:actions>
                    </x-ui.empty-state>
                </div>
            @endforelse
        </div>
    </x-ui.section>

    {{-- Week navigation --}}
    <nav aria-label="{{ __('Week navigation') }}" class="flex items-center justify-between pt-2">
        @if($week > 1)
            <x-ui.button variant="secondary" size="sm" icon="arrow-left" href="{{ route('maternal.journey.week', $week - 1) }}">
                {{ __('Week :week', ['week' => $week - 1]) }}
            </x-ui.button>
        @else
            <span></span>
        @endif

        @if($week < 52)
            <x-ui.button variant="secondary" size="sm" icon-right="arrow-right" href="{{ route('maternal.journey.week', $week + 1) }}">
                {{ __('Week :week', ['week' => $week + 1]) }}
            </x-ui.button>
        @endif
    </nav>

</div>
@endsection
