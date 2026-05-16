@extends('layouts.parent')

@section('title', $child->name . "'s Milestones — Noble Nest Academy")

@section('content')

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
        <li>
            <a href="{{ route('parent.child', $child) }}" class="hover:text-[var(--color-primary)] transition-colors focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] rounded">
                {{ $child->name }}
            </a>
        </li>
        <li aria-hidden="true"><x-ui.icon name="chevron-right" class="w-4 h-4" /></li>
        <li class="font-semibold text-[var(--color-text)]" aria-current="page">Milestones</li>
    </ol>
</nav>

<x-ui.page-header
    title="🌱 Developmental Milestones"
    subtitle="Track {{ $child->name }}'s growth and celebrate every achievement"
/>

@forelse($milestones->groupBy('domain') as $domain => $items)
    <x-ui.section :title="ucfirst($domain)" class="mb-2">
        <x-ui.card variant="clay" padding="none" class="overflow-hidden">
            <ul class="divide-y divide-[var(--color-border)]">
                @foreach($items as $milestone)
                <li class="flex items-start gap-4 px-5 py-4">
                    {{-- Toggle form --}}
                    <form
                        action="{{ route('parent.milestone.toggle', [$child, $milestone]) }}"
                        method="POST"
                        class="mt-0.5 shrink-0"
                    >
                        @csrf
                        <button
                            type="submit"
                            class="w-8 h-8 min-h-[2rem] rounded-full border-[2px] flex items-center justify-center transition-all focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2 cursor-pointer {{ $milestone->completed ? 'bg-emerald-500 border-emerald-500 text-white hover:bg-emerald-600' : 'bg-[var(--color-surface-strong)] border-[var(--color-border)] text-[var(--color-text-muted)] hover:border-emerald-400 hover:text-emerald-600' }}"
                            title="{{ $milestone->completed ? 'Mark incomplete' : 'Mark complete' }}"
                            aria-label="{{ $milestone->completed ? 'Mark ' . $milestone->title . ' incomplete' : 'Mark ' . $milestone->title . ' complete' }}"
                        >
                            @if($milestone->completed)
                                <x-ui.icon name="check" class="w-4 h-4" />
                            @else
                                <span class="w-2 h-2 rounded-full bg-current opacity-30"></span>
                            @endif
                        </button>
                    </form>

                    {{-- Milestone content --}}
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-[var(--color-text)] {{ $milestone->completed ? 'line-through opacity-60' : '' }}">
                            {{ $milestone->title }}
                        </div>
                        @if($milestone->description)
                            <p class="text-sm text-[var(--color-text-muted)] mt-0.5">{{ $milestone->description }}</p>
                        @endif
                        <div class="mt-2">
                            <x-ui.badge tone="neutral" size="sm">
                                {{ $milestone->age_months_min }}–{{ $milestone->age_months_max }} months
                            </x-ui.badge>
                        </div>
                    </div>

                    @if($milestone->completed)
                        <x-ui.badge tone="success" size="sm" class="shrink-0 mt-0.5">
                            <x-ui.icon name="check-circle" class="w-3.5 h-3.5" aria-hidden="true" />
                            Done
                        </x-ui.badge>
                    @endif
                </li>
                @endforeach
            </ul>
        </x-ui.card>
    </x-ui.section>

@empty
    <x-ui.empty-state
        title="No milestones available yet"
        description="We're always adding more milestones for your child's age. Check back soon!"
        icon="trophy"
    />
@endforelse

@endsection
