@extends('layouts.child')

@section('title', $child->name . "'s Learning Adventures — Noble Nest Academy")

@section('content')
@php
    $tier = match($child->age_bracket ?? 'learner') {
        'baby'       => 'baby',
        'toddler'    => 'toddler',
        'preschool'  => 'preschool',
        'school-age' => 'school',
        default      => 'school',
    };
    $tierFrom = match($tier) {
        'baby'      => 'var(--color-tier-baby)',
        'toddler'   => 'var(--color-tier-toddler)',
        'preschool' => 'var(--color-tier-preschool)',
        default     => 'var(--color-tier-primary)',
    };
    $tierMascot = match($tier) {
        'baby'      => '👶',
        'toddler'   => '🐣',
        'preschool' => '🌱',
        default     => '🚀',
    };
    $subjects = [
        ''          => ['label' => 'All',      'emoji' => '✨'],
        'islamic'   => ['label' => 'Islamic',  'emoji' => '🌙'],
        'art'       => ['label' => 'Art',      'emoji' => '🎨'],
        'language'  => ['label' => 'Language', 'emoji' => '🗣️'],
        'stem'      => ['label' => 'STEM',     'emoji' => '🔬'],
        'stories'   => ['label' => 'Stories',  'emoji' => '📖'],
        'motor'     => ['label' => 'Motor',    'emoji' => '🖐️'],
    ];
@endphp

{{-- ── Page header ── --}}
<div class="relative overflow-hidden pb-8 pt-5 px-4">
    <div class="absolute inset-0 -z-10" style="background: linear-gradient(135deg, {{ $tierFrom }}, color-mix(in oklab, {{ $tierFrom }}, white 30%));"></div>
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center gap-3">
            <span class="text-4xl leading-none" aria-hidden="true">{{ $tierMascot }}</span>
            <div class="flex-1 min-w-0">
                <h1 class="font-display font-black text-2xl text-white leading-tight truncate">
                    {{ $child->name }}'s Adventures!
                </h1>
                <div class="flex items-center gap-2 flex-wrap mt-1">
                    <x-ui.badge tone="neutral" size="sm" class="bg-white/25 text-white border-white/30">
                        {{ ucfirst($child->age_bracket ?? 'learner') }}@if($child->age_months) &middot; Age {{ floor($child->age_months/12) }}@endif
                    </x-ui.badge>
                    @if($child->streak_days)
                    <x-ui.badge tone="neutral" size="sm" class="bg-white/25 text-white border-white/30">
                        🔥 {{ $child->streak_days }}d streak
                    </x-ui.badge>
                    @endif
                    <x-ui.badge tone="neutral" size="sm" class="bg-white/25 text-white border-white/30">
                        ⭐ {{ $completedCount }} done
                    </x-ui.badge>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="relative -mt-5 rounded-t-3xl bg-[var(--color-surface)] min-h-64 pb-10">
    <div class="max-w-2xl mx-auto px-4 pt-6">

        {{-- Next adventure --}}
        @if(isset($nextActivity) && $nextActivity)
        @php
            $nextUrl = match($nextActivity->activity_type ?? 'default') {
                'tracing' => route('activities.tracing', $nextActivity) . '?child=' . $child->id,
                'drawing' => route('activities.drawing', $nextActivity) . '?child=' . $child->id,
                'puzzle'  => route('activities.puzzle',  $nextActivity) . '?child=' . $child->id,
                'quiz'    => ($nextActivity->quiz_id
                    ? route('quizzes.show', $nextActivity->quiz_id) . '?child=' . $child->id
                    : route('activities.show', $nextActivity) . '?child=' . $child->id),
                default   => route('activities.show', $nextActivity) . '?child=' . $child->id,
            };
        @endphp
        <div class="flex items-center gap-3 rounded-[var(--radius-card)] border-[3px] border-[var(--color-brand-400)] bg-[var(--color-brand-50)] shadow-[var(--shadow-clay)] px-4 py-3 mb-5">
            <span class="text-3xl shrink-0" aria-hidden="true">{{ $nextActivity->emoji ?? '🎯' }}</span>
            <div class="flex-1 min-w-0">
                <div class="text-xs font-bold text-[var(--color-brand-700)] uppercase tracking-wide">Next Adventure</div>
                <div class="font-display font-bold text-[var(--color-text)] truncate">{{ $nextActivity->title }}</div>
                @if($nextActivity->description)
                    <div class="text-xs text-[var(--color-text-muted)] truncate hidden sm:block">{{ Str::limit($nextActivity->description, 70) }}</div>
                @endif
            </div>
            <a href="{{ $nextUrl }}"
               class="shrink-0 inline-flex items-center gap-1.5 px-4 py-2 min-h-[2.5rem] rounded-[var(--radius-sm)] border-[3px] border-[var(--color-brand-600)] bg-gradient-to-br from-[var(--color-brand-600)] to-[var(--color-brand-400)] text-white font-display font-bold text-sm shadow-[var(--shadow-clay)] hover:-translate-y-[2px] transition-all focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2"
               aria-label="Play {{ $nextActivity->title }}">
                <x-ui.icon name="play" class="w-4 h-4" aria-hidden="true" />Play Now
            </a>
        </div>
        @endif

        {{-- Subject filter --}}
        <nav class="flex gap-2 overflow-x-auto pb-1 mb-5 scrollbar-none snap-x" aria-label="Filter by subject">
            @foreach($subjects as $key => $s)
            @php $isActive = ($activeSubject ?? '') === $key; @endphp
            <a href="{{ route('child.activities', $child) }}{{ $key ? '?subject='.$key : '' }}"
               class="shrink-0 snap-start inline-flex items-center gap-1.5 px-4 py-2 min-h-[2.5rem] rounded-full font-display font-bold text-sm border-[2px] transition-all focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2 whitespace-nowrap {{ $isActive ? 'bg-[var(--color-brand-600)] border-[var(--color-brand-600)] text-white shadow-[var(--shadow-clay)]' : 'bg-[var(--color-surface-strong)] border-[var(--color-border)] text-[var(--color-text-muted)] hover:border-[var(--color-brand-400)] hover:text-[var(--color-primary)]' }}"
               @if($isActive) aria-current="page" @endif>
                <span aria-hidden="true">{{ $s['emoji'] }}</span>{{ $s['label'] }}
            </a>
            @endforeach
        </nav>

        {{-- Subscription / drip progress --}}
        @if($hasSubscription)
        <div class="rounded-[var(--radius-card)] border-[3px] border-[var(--color-border)] shadow-[var(--shadow-clay)] bg-[var(--color-surface-strong)] px-4 py-3 mb-5">
            <div class="flex justify-between items-center mb-2">
                <span class="font-display font-bold text-sm text-[var(--color-text)]">
                    <x-ui.icon name="calendar" class="w-4 h-4 inline me-1 text-[var(--color-primary)]" aria-hidden="true" />
                    Week {{ $currentWeek }} of {{ $totalWeeks }}
                </span>
                <x-ui.badge tone="brand" size="sm">{{ $maxOrder }} unlocked</x-ui.badge>
            </div>
            <x-ui.progress
                :value="($currentWeek / max($totalWeeks, 1)) * 100"
                size="md"
                :label="$completedCount . ' done' . ($currentWeek < $totalWeeks && $daysToNextWeek > 0 ? ' · ' . $daysToNextWeek . 'd to next unlock' : '')"
            />
        </div>
        @endif

        {{-- Activity grid heading --}}
        <div class="flex items-center gap-2 mb-3">
            <x-ui.icon name="grid" class="w-5 h-5 text-[var(--color-primary)]" aria-hidden="true" />
            <h2 class="font-display font-bold text-lg text-[var(--color-text)]">
                @if($activeSubject) {{ ucfirst($activeSubject) }} Activities @else All Activities @endif
            </h2>
            <x-ui.badge tone="brand" size="sm">{{ $activities->total() }}</x-ui.badge>
        </div>

        {{-- Activity grid --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            @forelse($activities as $activity)
            @php
                $isLocked = $activity->locked ?? false;
                $isDone   = $activity->is_completed ?? false;
                $activityUrl = match($activity->activity_type ?? 'default') {
                    'tracing' => route('activities.tracing', $activity) . '?child=' . $child->id,
                    'drawing' => route('activities.drawing', $activity) . '?child=' . $child->id,
                    'puzzle'  => route('activities.puzzle',  $activity) . '?child=' . $child->id,
                    'video'   => route('activities.video',   $activity) . '?child=' . $child->id,
                    'slides', 'simulation' => route('activities.slides', $activity) . '?child=' . $child->id,
                    'quiz'    => ($activity->quiz_id
                        ? route('quizzes.show', $activity->quiz_id) . '?child=' . $child->id
                        : route('activities.show', $activity) . '?child=' . $child->id),
                    default   => ($activity->video_url
                        ? route('activities.video',  $activity) . '?child=' . $child->id
                        : ($activity->steps_count > 0
                            ? route('activities.slides', $activity) . '?child=' . $child->id
                            : route('activities.show',   $activity) . '?child=' . $child->id)),
                };
            @endphp
            @include('child._activity_card', ['activity' => $activity, 'child' => $child, 'activityUrl' => $activityUrl, 'isLocked' => $isLocked, 'isDone' => $isDone])
            @empty
            <div class="col-span-full">
                <x-ui.empty-state
                    icon="sparkles"
                    title="No adventures found!"
                    description="Try a different subject, or check back soon — we add new content every week!"
                >
                    @if($activeSubject)
                    <x-slot name="actions">
                        <x-ui.button variant="primary" href="{{ route('child.activities', $child) }}" icon="sparkles">
                            Show All
                        </x-ui.button>
                    </x-slot>
                    @endif
                </x-ui.empty-state>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-5 flex justify-center">
            {{ $activities->appends(request()->query())->links() }}
        </div>

        {{-- Upgrade nudge --}}
        @unless($hasSubscription || !in_array(auth()->user()->role ?? '', ['Parent', 'Student']))
        <x-ui.card variant="gradient" padding="md" class="mt-6 text-center">
            <x-ui.icon name="sparkles" class="w-8 h-8 text-[var(--color-brand-600)] mx-auto mb-2" aria-hidden="true" />
            <h3 class="font-display font-black text-lg text-[var(--color-text)] mb-1">Unlock All Weekly Packs!</h3>
            <p class="text-sm text-[var(--color-text-muted)] mb-4">Fresh adventures every week tailored to {{ $child->name }}'s age.</p>
            <x-ui.button variant="primary" href="{{ route('pricing') }}" icon="arrow-right">
                From $3/month
            </x-ui.button>
        </x-ui.card>
        @endunless

    </div>
</div>
@endsection
