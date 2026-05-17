@extends('layouts.parent')

@section('title', 'Parent Dashboard — Noble Nest Academy')

@section('content')
@php
    $hour = (int) now()->format('H');
    $greeting = match(true) {
        $hour < 12 => 'Good morning',
        $hour < 17 => 'Good afternoon',
        default     => 'Good evening',
    };
    $greetEmoji = match(true) {
        $hour < 12 => '☀️',
        $hour < 17 => '🌤️',
        default     => '🌙',
    };
@endphp

{{-- ── Page header ── --}}
<x-ui.page-header
    title="{{ $greeting }}, {{ Auth::user()->name }} {{ $greetEmoji }}"
    subtitle="Here's what your children have been up to"
>
    <x-slot name="actions">
        @unless($hasSubscription)
            <x-ui.button variant="primary" href="{{ route('pricing') }}" icon="sparkles" size="sm">
                Upgrade to Premium
            </x-ui.button>
        @endunless
        <x-ui.button variant="secondary" href="{{ route('children.create') }}" icon="person-add" size="sm">
            Add Child
        </x-ui.button>
    </x-slot>
</x-ui.page-header>

{{-- ── Stat bar ── --}}
<div class="grid grid-cols-3 gap-4 mb-8">
    <x-ui.stat
        label="Children"
        :value="$children->count()"
        icon="users"
    />
    <x-ui.stat
        label="Activities this week"
        :value="$recentActivity->count()"
        icon="zap"
    />
    <x-ui.stat
        label="Best streak"
        :value="$children->max('streak_days') ?? 0"
        delta="days"
        deltaTone="positive"
        icon="flame"
    />
</div>

{{-- ── Children cards ── --}}
<x-ui.section title="Your Children">
    <x-slot name="actions">
        <x-ui.button variant="ghost" href="{{ route('children.create') }}" icon="plus" size="sm">
            Add Child
        </x-ui.button>
    </x-slot>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($children as $child)
        @php
            $tone = match($child->age_bracket ?? 'learner') {
                'baby'       => 'baby',
                'toddler'    => 'toddler',
                'preschool'  => 'preschool',
                'school-age' => 'primary',
                default      => 'primary',
            };
            $tierIcon = match($tone) {
                'baby'      => '🐣',
                'toddler'   => '🦊',
                'preschool' => '🐢',
                default     => '🦉',
            };
        @endphp
        <x-ui.card :tone="$tone" variant="clay" padding="none" class="flex flex-col h-full overflow-hidden">
            {{-- Tier-coloured header --}}
            <div class="px-4 py-3 bg-[var(--color-tier-{{ $tone }})] text-white">
                <div class="flex items-center gap-3">
                    <x-ui.avatar :name="$child->name" size="lg" ring />
                    <div class="min-w-0">
                        <h3 class="font-display font-bold text-white truncate">{{ $child->name }}</h3>
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <x-ui.badge tone="neutral" size="sm" class="bg-white/25 text-white border-white/30">
                                <span aria-hidden="true">{{ $tierIcon }}</span>
                                {{ ucfirst($child->age_bracket ?? 'learner') }}
                            </x-ui.badge>
                            @if($child->age_display)
                                <x-ui.badge tone="neutral" size="sm" class="bg-white/25 text-white border-white/30">
                                    {{ $child->age_display }}
                                </x-ui.badge>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stats row --}}
            <div class="grid grid-cols-3 divide-x divide-[var(--color-border)] border-b border-[var(--color-border)] text-center">
                <div class="py-3 px-2">
                    <div class="text-xl font-bold font-[var(--font-sans)] text-[var(--color-primary)]">{{ $child->activity_progress_count }}</div>
                    <div class="text-xs text-[var(--color-text-muted)] font-medium">Activities</div>
                </div>
                <div class="py-3 px-2">
                    <div class="text-xl font-bold font-[var(--font-sans)] text-amber-500">{{ $child->streak_days ?? 0 }}</div>
                    <div class="text-xs text-[var(--color-text-muted)] font-medium">Streak</div>
                </div>
                <div class="py-3 px-2">
                    <div class="text-xl font-bold font-[var(--font-sans)] text-emerald-600">{{ $child->age_months ? floor($child->age_months / 12) : '?' }}</div>
                    <div class="text-xs text-[var(--color-text-muted)] font-medium">Age (yrs)</div>
                </div>
            </div>

            @if($hasSubscription && isset($subscription))
            <div class="px-4 py-3 border-b border-[var(--color-border)]">
                <div class="text-xs font-semibold text-[var(--color-text-muted)] mb-1.5">
                    <x-ui.icon name="calendar" class="w-3.5 h-3.5 inline me-1 text-[var(--color-primary)]" aria-hidden="true" />
                    Week {{ $subscription->currentWeek() }} of 4
                </div>
                <x-ui.progress :value="($subscription->currentWeek() / 4) * 100" size="sm" />
            </div>
            @endif

            {{-- Actions --}}
            <div class="p-3 mt-auto flex gap-2 flex-wrap">
                <x-ui.button variant="primary" href="{{ route('child.dashboard', $child) }}" size="sm" icon="sparkles" class="flex-1">
                    Dashboard
                </x-ui.button>
                <x-ui.button variant="secondary" href="{{ route('child.activities', $child) }}" size="sm" icon="grid" class="flex-1">
                    Activities
                </x-ui.button>
                <x-ui.button variant="ghost" href="{{ route('parent.child', $child) }}" size="sm" icon="bar-chart">
                    Progress
                </x-ui.button>
            </div>
        </x-ui.card>

        @empty
        <div class="col-span-full">
            <x-ui.empty-state
                icon="users"
                title="Add your first child"
                description="Create a child profile to start their learning journey."
            >
                <x-slot name="actions">
                    <x-ui.button variant="primary" href="{{ route('children.create') }}" icon="plus">
                        Add Child
                    </x-ui.button>
                </x-slot>
            </x-ui.empty-state>
        </div>
        @endforelse
    </div>
</x-ui.section>

{{-- ── Recent activity ── --}}
@if($recentActivity->isNotEmpty())
<x-ui.section title="Recent Activity">
    <div class="rounded-[var(--radius-card)] border-[3px] border-[var(--color-border)] shadow-[var(--shadow-clay)] divide-y divide-[var(--color-border)] overflow-hidden bg-[var(--color-surface-strong)]">
        @foreach($recentActivity as $item)
        <div class="flex items-center gap-3 px-4 py-3">
            <div class="w-10 h-10 min-w-[2.5rem] rounded-[var(--radius-sm)] bg-[var(--color-primary-soft)] text-[var(--color-primary)] flex items-center justify-center" aria-hidden="true">
                <x-ui.icon name="book-open" class="w-5 h-5" />
            </div>
            <div class="flex-1 min-w-0">
                <div class="font-semibold text-[var(--color-text)] truncate">{{ $item->activity->title ?? 'Activity' }}</div>
                <div class="text-sm text-[var(--color-text-muted)]">
                    {{ $item->childProfile->name ?? '' }}
                    @if($item->completed_at)
                        &middot; {{ $item->completed_at->diffForHumans() }}
                    @endif
                </div>
            </div>
            @if(isset($item->childProfile->share_card_url) && $item->childProfile->share_card_url)
            <x-ui.button variant="ghost" href="{{ $item->childProfile->share_card_url }}" target="_blank" rel="noopener" size="sm" icon="share-2">
                Share
            </x-ui.button>
            @endif
        </div>
        @endforeach
    </div>
</x-ui.section>
@endif

{{-- ── Phase 5: AI suggestions widget (Groq, no PII) ── --}}
@php
    $firstChild   = $children->first();
    $aiSuggestions = [];
    if ($firstChild) {
        try {
            $aiSuggestions = app(\App\Services\AIAssistantService::class)->suggestForChild($firstChild);
        } catch (\Throwable $e) {
            $aiSuggestions = [];
        }
    }
@endphp
@if($firstChild)
<x-ui.section title="AI suggestions for {{ $firstChild->name }}">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        @forelse($aiSuggestions as $s)
            <div class="rounded-[var(--radius-sm)] border border-[var(--color-border)] p-4 bg-[var(--color-surface-strong)]">
                <h4 class="font-bold text-sm mb-1">{{ $s['title'] ?? '' }}</h4>
                @if(!empty($s['why']))
                    <p class="text-xs text-[var(--color-text-muted)]">{{ $s['why'] }}</p>
                @endif
            </div>
        @empty
            <div class="col-span-3 text-xs text-[var(--color-text-muted)]">
                AI suggestions unavailable in this environment.
            </div>
        @endforelse
    </div>
</x-ui.section>
@endif

{{-- ── Quick actions ── --}}
<x-ui.section title="Quick Actions">
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
        <x-ui.button variant="secondary" href="{{ route('children.create') }}" icon="person-add" class="justify-start">
            Add Child
        </x-ui.button>
        <x-ui.button variant="secondary" href="{{ route('parent.milestones') }}" icon="trophy" class="justify-start">
            View Milestones
        </x-ui.button>
        @if($children->isNotEmpty())
        <x-ui.button variant="secondary" href="{{ route('parent.share-card', $children->first()) }}" icon="share-2" class="justify-start">
            Share Card
        </x-ui.button>
        @endif
    </div>
</x-ui.section>
@endsection
