@extends('layouts.maternal')

@section('title', __('Maternal Wellness') . ' — Noble Nest Academy')
@section('meta_description', __('Your personalized maternal wellness journey with ancient techniques, nutrition plans, and guided exercises.'))

@section('content')
<div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    {{-- Emergency Signs — always checked first, visually dominant when present --}}
    @if($emergencySigns->isNotEmpty())
        <x-ui.alert tone="danger" :title="__('Know Your Emergency Signs')">
            <ul class="mt-1 space-y-1">
                @foreach($emergencySigns->take(3) as $sign)
                    <li>{{ $sign->symptom }} — <strong>{{ $sign->action_text }}</strong></li>
                @endforeach
            </ul>
            <a href="{{ route('maternal.emergency-signs') }}" class="mt-2 inline-flex items-center gap-1 font-semibold underline underline-offset-2 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--color-coral-500)]">
                {{ __('View all emergency signs') }}
                <x-ui.icon name="arrow-right" class="w-3.5 h-3.5" />
            </a>
        </x-ui.alert>
    @else
        {{-- Subtle persistent safety link when no urgent signs --}}
        <div class="flex items-center gap-2 text-sm text-[var(--color-text-muted)]">
            <x-ui.icon name="alert-circle" class="w-4 h-4 shrink-0" />
            <a href="{{ route('maternal.emergency-signs') }}" class="underline underline-offset-2 hover:text-[var(--color-text)] focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-[var(--color-brand-600)] rounded">
                {{ __('Review emergency warning signs') }}
            </a>
        </div>
    @endif

    {{-- Stage Banner --}}
    <x-ui.card variant="clay" padding="lg">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-[var(--color-text-muted)] mb-1">
                    {{ __('Your Journey') }}
                </p>
                <h1 class="text-2xl font-bold text-[var(--color-text)] leading-snug">
                    {{ __('Welcome back') }}
                </h1>
                <p class="text-[var(--color-text-muted)] mt-1 leading-relaxed">
                    {{ __('Week :week', ['week' => $profile->current_week]) }}
                    &middot; {{ __('Trimester :t', ['t' => $profile->trimester]) }}
                    @if($profile->due_date)
                        &middot; {{ __('Due :date', ['date' => \Carbon\Carbon::parse($profile->due_date)->format('M j, Y')]) }}
                    @endif
                </p>
                <p class="text-sm text-[var(--color-text-muted)] mt-1 flex items-center gap-1">
                    <x-ui.icon name="check-circle" class="w-4 h-4 text-emerald-600 shrink-0" />
                    {{ $completedCount }} {{ __('activities completed') }}
                </p>
            </div>
            <x-ui.button variant="secondary" size="sm" icon="calendar" href="{{ route('maternal.journey') }}">
                {{ __('View Journey') }}
            </x-ui.button>
        </div>
    </x-ui.card>

    {{-- Recommended Content --}}
    <x-ui.section :title="__('Recommended for You')">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            @forelse($recommended as $item)
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
                            {{ Str::limit($item->benefit_explanation, 80) }}
                        </p>
                        <x-ui.button variant="secondary" size="sm" icon-right="arrow-right" href="{{ route('maternal.content.show', $item) }}">
                            {{ __('View') }}
                        </x-ui.button>
                    </div>
                </x-ui.card>
            @empty
                <div class="col-span-full">
                    <x-ui.empty-state
                        icon="book-open"
                        :title="__('Content coming soon')"
                        :description="__('Content for your current stage will appear here shortly. Check back soon!')"
                    />
                </div>
            @endforelse
        </div>
    </x-ui.section>

    {{-- Quick Action Cards --}}
    <x-ui.section :title="__('Quick Actions')">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            @foreach([
                ['label' => __('Exercises'),  'icon' => 'heart',    'href' => route('maternal.exercises.index')],
                ['label' => __('Nutrition'),   'icon' => 'apple',    'href' => '#'],
                ['label' => __('Herbs'),       'icon' => 'leaf',     'href' => '#'],
                ['label' => __('Journal'),     'icon' => 'file-text','href' => route('maternal.journal.create')],
            ] as $action)
                <x-ui.card
                    variant="clay"
                    padding="md"
                    :href="$action['href']"
                    class="flex flex-col items-center gap-2 text-center hover:scale-[1.02] transition-transform"
                >
                    <x-ui.icon :name="$action['icon']" class="w-7 h-7 text-[var(--color-primary)]" />
                    <span class="text-sm font-semibold text-[var(--color-text)]">{{ $action['label'] }}</span>
                </x-ui.card>
            @endforeach
        </div>
    </x-ui.section>

    {{-- Recent Journal Entries --}}
    <x-ui.section :title="__('Recent Journal')">
        <x-slot:actions>
            <x-ui.button variant="primary" size="sm" icon="plus" href="{{ route('maternal.journal.create') }}">
                {{ __('New Entry') }}
            </x-ui.button>
        </x-slot:actions>

        @forelse($recentJournals as $entry)
            <x-ui.card variant="flat" padding="sm" class="mb-2 last:mb-0">
                <div class="flex items-center justify-between gap-3">
                    <div class="text-sm leading-relaxed">
                        <span class="font-semibold text-[var(--color-text)]">
                            {{ \Carbon\Carbon::parse($entry->entry_date)->format('M j') }}
                        </span>
                        <span class="mx-2 text-[var(--color-border)]">&middot;</span>
                        <span class="text-[var(--color-text-muted)]">{{ __('Mood') }}: {{ ucfirst($entry->mood) }}</span>
                        <span class="mx-2 text-[var(--color-border)]">&middot;</span>
                        <span class="text-[var(--color-text-muted)]">{{ __('Energy') }}: {{ $entry->energy_level }}/5</span>
                        @if($entry->baby_kicks)
                            <span class="mx-2 text-[var(--color-border)]">&middot;</span>
                            <span class="text-[var(--color-text-muted)]">{{ $entry->baby_kicks }} {{ __('kicks') }}</span>
                        @endif
                    </div>
                    <a
                        href="{{ route('maternal.journal.show', $entry) }}"
                        class="text-[var(--color-primary)] hover:text-[var(--color-brand-700)] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--color-brand-600)] rounded shrink-0"
                        :aria-label="__('View journal entry')"
                    >
                        <x-ui.icon name="arrow-right" class="w-4 h-4" />
                    </a>
                </div>
            </x-ui.card>
        @empty
            <p class="text-sm text-[var(--color-text-muted)]">
                {{ __('No journal entries yet. Start tracking your wellness today!') }}
            </p>
        @endforelse
    </x-ui.section>

</div>
@endsection
