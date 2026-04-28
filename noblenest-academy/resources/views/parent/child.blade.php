@extends('layouts.parent')

@section('title', $child->name . "'s Progress — Noble Nest Academy")

@section('content')
@php
    $tone = match($child->age_tier ?? $child->age_bracket ?? 'learner') {
        'baby'                  => 'baby',
        'toddler'               => 'toddler',
        'preschool'             => 'preschool',
        'school-age', 'school' => 'primary',
        default                 => 'primary',
    };
    $tierIcon = match($tone) {
        'baby'      => '🐣',
        'toddler'   => '🦊',
        'preschool' => '🐢',
        default     => '🦉',
    };
    $tierLabel = match($tone) {
        'baby'      => 'Baby',
        'toddler'   => 'Toddler',
        'preschool' => 'Preschool',
        default     => 'School Age',
    };
    $completedCount = $progress->total();
    $progressPct = $completedCount > 0 ? min(round(($completedCount / max($completedCount + 5, 10)) * 100), 100) : 0;
@endphp

{{-- ── Breadcrumb ── --}}
<nav aria-label="Breadcrumb" class="mb-6">
    <ol class="flex items-center gap-2 text-sm text-[var(--color-text-muted)]">
        <li>
            <a href="{{ route('parent.dashboard') }}" class="flex items-center gap-1 hover:text-[var(--color-primary)] transition-colors focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] rounded">
                <x-ui.icon name="home" class="w-4 h-4" />
                Dashboard
            </a>
        </li>
        <li aria-hidden="true"><x-ui.icon name="chevron-right" class="w-4 h-4" /></li>
        <li class="font-semibold text-[var(--color-text)]" aria-current="page">{{ $child->name }}</li>
    </ol>
</nav>

{{-- ── Child profile hero ── --}}
<x-ui.card :tone="$tone" variant="clay" padding="none" class="mb-6 overflow-hidden">
    {{-- Tier-coloured hero strip --}}
    <div class="px-6 py-5 bg-[var(--color-tier-{{ $tone }})] text-white">
        <div class="flex items-center gap-4">
            <x-ui.avatar :name="$child->name" size="xl" ring class="shrink-0" />
            <div class="flex-1 min-w-0">
                <h1 class="font-display font-black text-2xl text-white leading-tight truncate">{{ $child->name }}</h1>
                <div class="flex flex-wrap items-center gap-2 mt-1">
                    <x-ui.badge tone="neutral" class="bg-white/25 text-white border-white/30">
                        <span aria-hidden="true">{{ $tierIcon }}</span> {{ $tierLabel }}
                    </x-ui.badge>
                    @if($child->age_months)
                        <x-ui.badge tone="neutral" class="bg-white/25 text-white border-white/30">
                            <x-ui.icon name="calendar" class="w-3 h-3" aria-hidden="true" />
                            {{ $child->age_display ?? (floor($child->age_months/12).'y '.($child->age_months % 12).'m') }}
                        </x-ui.badge>
                    @endif
                </div>
            </div>
            {{-- Progress ring --}}
            <div class="shrink-0 hidden sm:block">
                <x-ui.progress variant="ring" :value="$progressPct" size="lg" tone="brand" label="Progress" />
            </div>
        </div>
    </div>

    {{-- Stats row --}}
    <div class="grid grid-cols-3 divide-x divide-[var(--color-border)] border-b border-[var(--color-border)]">
        <div class="py-4 px-4 text-center">
            <div class="text-2xl font-bold font-[var(--font-sans)] text-[var(--color-primary)]">{{ $completedCount }}</div>
            <div class="text-xs text-[var(--color-text-muted)] font-medium mt-0.5">Completed</div>
        </div>
        <div class="py-4 px-4 text-center">
            <div class="text-2xl font-bold font-[var(--font-sans)] text-amber-500 flex items-center justify-center gap-1">
                <x-ui.icon name="flame" class="w-5 h-5" aria-hidden="true" />{{ $child->streak_days ?? 0 }}
            </div>
            <div class="text-xs text-[var(--color-text-muted)] font-medium mt-0.5">Day Streak</div>
        </div>
        <div class="py-4 px-4 text-center">
            <div class="text-2xl font-bold font-[var(--font-sans)] text-[var(--color-text)]">
                {{ $child->age_months ? floor($child->age_months/12).'y '.($child->age_months % 12).'m' : '—' }}
            </div>
            <div class="text-xs text-[var(--color-text-muted)] font-medium mt-0.5">Age</div>
        </div>
    </div>

    {{-- CTAs --}}
    <div class="flex gap-2 p-4 flex-wrap">
        <x-ui.button variant="primary" href="{{ route('child.activities', $child) }}" icon="sparkles" class="flex-1">
            Start Learning
        </x-ui.button>
        <x-ui.button variant="secondary" href="{{ route('child.dashboard', $child) }}" icon="home" class="flex-1">
            Child Dashboard
        </x-ui.button>
        <x-ui.button variant="ghost" href="{{ route('children.edit', $child) }}" icon="edit">
            Edit
        </x-ui.button>
    </div>
</x-ui.card>

{{-- ── Activity history ── --}}
<x-ui.section>
    <x-slot name="title">
        Activity History
        <x-ui.badge tone="brand" size="sm" class="ms-2">{{ $completedCount }}</x-ui.badge>
    </x-slot>

    @if($progress->isEmpty())
        <x-ui.empty-state
            icon="book-open"
            title="No activities completed yet"
            description="Start a learning adventure to see progress here."
        >
            <x-slot name="actions">
                <x-ui.button variant="primary" href="{{ route('child.activities', $child) }}" icon="sparkles">
                    Start now!
                </x-ui.button>
            </x-slot>
        </x-ui.empty-state>
    @else
        <div class="rounded-[var(--radius-card)] border-[3px] border-[var(--color-border)] shadow-[var(--shadow-clay)] divide-y divide-[var(--color-border)] overflow-hidden bg-[var(--color-surface-strong)]">
            @foreach($progress as $item)
            <div class="flex items-center gap-3 px-4 py-3">
                <div class="w-10 h-10 min-w-[2.5rem] rounded-[var(--radius-sm)] bg-[var(--color-primary-soft)] text-[var(--color-primary)] flex items-center justify-center text-lg" aria-hidden="true">
                    @if(!empty($item->activity->emoji))
                        {{ $item->activity->emoji }}
                    @else
                        <x-ui.icon name="book-open" class="w-5 h-5" />
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-semibold text-[var(--color-text)] truncate">{{ $item->activity->title ?? 'Activity' }}</div>
                    <div class="text-sm text-[var(--color-text-muted)] flex items-center gap-2 flex-wrap">
                        @if($item->completed_at)
                            <span class="flex items-center gap-1">
                                <x-ui.icon name="calendar" class="w-3.5 h-3.5" aria-hidden="true" />
                                {{ $item->completed_at->format('M d, Y') }}
                            </span>
                            <span class="flex items-center gap-1">
                                <x-ui.icon name="clock" class="w-3.5 h-3.5" aria-hidden="true" />
                                {{ $item->completed_at->format('g:i A') }}
                            </span>
                        @endif
                    </div>
                </div>
                <x-ui.badge tone="success" size="sm">
                    <x-ui.icon name="check" class="w-3 h-3" aria-hidden="true" />
                    Done
                </x-ui.badge>
            </div>
            @endforeach
        </div>

        <div class="mt-4 flex justify-center">
            {{ $progress->links() }}
        </div>
    @endif
</x-ui.section>
@endsection
