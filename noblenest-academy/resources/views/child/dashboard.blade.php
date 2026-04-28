@extends('layouts.child')

@section('title', 'Hi, ' . $child->name . '! — Noble Nest Academy')

@section('content')
@php
    $ageMonths = $child->age_months ?? 0;
    $tier = match(true) {
        $ageMonths < 24  => 'baby',
        $ageMonths < 48  => 'toddler',
        $ageMonths < 72  => 'preschool',
        default          => 'school',
    };
    $mascots = [
        'baby'     => ['emoji' => '🐣', 'name' => 'Pip',    'greeting' => 'Hello, little star!', 'from' => 'var(--color-tier-baby)',      'to' => '#FBBF24'],
        'toddler'  => ['emoji' => '🦊', 'name' => 'Finn',   'greeting' => 'Ready to discover?',  'from' => 'var(--color-tier-toddler)',   'to' => '#34D399'],
        'preschool'=> ['emoji' => '🐢', 'name' => 'Shelly', 'greeting' => "Let's explore today!",'from' => 'var(--color-tier-preschool)', 'to' => 'var(--color-brand-300)'],
        'school'   => ['emoji' => '🦉', 'name' => 'Ollie',  'greeting' => 'Knowledge awaits!',   'from' => 'var(--color-tier-primary)',   'to' => '#3B82F6'],
    ];
    $mascot = $mascots[$tier];
    $streak = $child->streak_days ?? 0;
    $progressPct = $totalCompleted > 0 ? min(round(($totalCompleted / max($totalCompleted + 3, 10)) * 100), 100) : 0;
@endphp

{{-- ── Hero banner ── --}}
<div class="relative overflow-hidden pb-10 pt-6 px-4" aria-label="Greeting">
    {{-- Gradient bg --}}
    <div class="absolute inset-0 -z-10" style="background: linear-gradient(135deg, {{ $mascot['from'] }}, {{ $mascot['to'] }});"></div>
    {{-- Decorative circles --}}
    <div class="absolute top-0 start-0 w-40 h-40 rounded-full bg-white/10 -translate-x-1/2 -translate-y-1/2 pointer-events-none" aria-hidden="true"></div>
    <div class="absolute bottom-0 end-0 w-32 h-32 rounded-full bg-white/10 translate-x-1/4 translate-y-1/4 pointer-events-none" aria-hidden="true"></div>
    <div class="absolute top-4 end-8 pointer-events-none" aria-hidden="true" style="animation: nn-float 3s ease-in-out infinite;">⭐</div>
    <div class="absolute top-12 start-12 pointer-events-none" aria-hidden="true" style="animation: nn-float 4s ease-in-out infinite 1s;">✨</div>

    <div class="max-w-lg mx-auto">
        <div class="flex items-center gap-4 mb-3">
            {{-- Mascot --}}
            <div class="text-6xl leading-none shrink-0 drop-shadow-lg" aria-hidden="true" style="animation: nn-float 3s ease-in-out infinite;">
                {{ $mascot['emoji'] }}
            </div>
            <div>
                <div class="text-xs font-bold text-white/75 uppercase tracking-widest mb-0.5">{{ $mascot['name'] }} says</div>
                <h1 class="font-display font-black text-2xl sm:text-3xl text-white leading-tight" style="text-shadow: 0 2px 12px rgba(0,0,0,0.2);">
                    {{ $mascot['greeting'] }}
                </h1>
                <p class="text-white/90 font-bold text-sm mt-0.5">
                    Hi <strong class="text-white">{{ $child->name }}</strong>!
                    @if($child->age_display)
                        You're <strong class="text-white">{{ $child->age_display }}</strong> old.
                    @endif
                </p>
            </div>
        </div>

        {{-- Streak chip --}}
        @if($streak > 0)
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full mb-1"
             style="background: rgba(255,255,255,0.25); backdrop-filter: blur(8px); border: 1.5px solid rgba(255,255,255,0.35);">
            <span aria-hidden="true" style="animation: flicker 1.8s ease-in-out infinite; display:inline-block;">🔥</span>
            <span class="font-display font-black text-white text-sm">{{ $streak }}-day streak!</span>
        </div>
        @endif
    </div>
</div>

{{-- ── Main content (card lifts over hero) ── --}}
<div class="relative -mt-6 rounded-t-3xl bg-[var(--color-surface)] min-h-64 pb-10">
    <div class="max-w-lg mx-auto px-4 pt-8">

        {{-- Stat chips --}}
        <div class="grid grid-cols-3 gap-2 mb-6">
            <div class="rounded-[var(--radius-card)] border-[3px] border-[var(--color-border)] shadow-[var(--shadow-clay)] bg-[var(--color-surface-strong)] p-3 text-center">
                <div class="text-amber-500 flex justify-center mb-1" aria-hidden="true">
                    <x-ui.icon name="flame" class="w-5 h-5" />
                </div>
                <div class="font-display font-black text-xl text-[var(--color-text)] leading-none">{{ $streak }}</div>
                <div class="text-xs text-[var(--color-text-muted)] font-semibold mt-0.5">Streak</div>
            </div>
            <div class="rounded-[var(--radius-card)] border-[3px] border-[var(--color-border)] shadow-[var(--shadow-clay)] bg-[var(--color-surface-strong)] p-3 text-center">
                <div class="text-[var(--color-primary)] flex justify-center mb-1" aria-hidden="true">
                    <x-ui.icon name="sparkles" class="w-5 h-5" />
                </div>
                <div class="font-display font-black text-xl text-[var(--color-text)] leading-none">{{ $totalCompleted }}</div>
                <div class="text-xs text-[var(--color-text-muted)] font-semibold mt-0.5">Done</div>
            </div>
            <div class="rounded-[var(--radius-card)] border-[3px] border-[var(--color-border)] shadow-[var(--shadow-clay)] bg-[var(--color-surface-strong)] p-3 text-center">
                <div class="text-[var(--color-accent)] flex justify-center mb-1" aria-hidden="true">
                    <x-ui.icon name="trophy" class="w-5 h-5" />
                </div>
                <div class="font-display font-black text-xl text-[var(--color-text)] leading-none">{{ $badgeCount }}</div>
                <div class="text-xs text-[var(--color-text-muted)] font-semibold mt-0.5">Badges</div>
            </div>
        </div>

        {{-- XP progress bar --}}
        @if($totalCompleted > 0)
        <div class="mb-6">
            <div class="flex justify-between items-center mb-2">
                <span class="font-display font-bold text-sm text-[var(--color-text-muted)]">
                    <x-ui.icon name="zap" class="w-4 h-4 inline me-1 text-[var(--color-accent)]" aria-hidden="true" />
                    Learning XP
                </span>
                <span class="font-display font-bold text-sm text-[var(--color-primary)]">
                    {{ $totalCompleted }} / {{ $totalCompleted + 3 }} ⭐
                </span>
            </div>
            <div class="relative h-3.5 bg-[var(--color-border)] rounded-full overflow-hidden"
                 role="progressbar" aria-valuenow="{{ $progressPct }}" aria-valuemin="0" aria-valuemax="100"
                 aria-label="Learning progress">
                <div class="h-full rounded-full transition-all duration-[var(--duration-slow)]"
                     style="width: {{ $progressPct }}%; background: linear-gradient(90deg, var(--color-brand-600), var(--color-brand-300), var(--color-accent));"></div>
            </div>
        </div>
        @endif

        {{-- Today's adventures heading --}}
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-display font-black text-xl text-[var(--color-text)] flex items-center gap-2">
                <span aria-hidden="true">🎯</span> Today's Adventures
            </h2>
            <a href="{{ route('child.activities', $child) }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-[var(--radius-sm)] border-[2px] border-[var(--color-border)] text-sm font-bold text-[var(--color-text-muted)] hover:border-[var(--color-brand-400)] hover:text-[var(--color-primary)] transition-all focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2 min-h-[2rem]">
                <x-ui.icon name="grid" class="w-4 h-4" aria-hidden="true" />All
            </a>
        </div>

        {{-- Activity grid --}}
        @if($todayActivities->isEmpty())
            <div class="rounded-[var(--radius-card)] border-[3px] border-[var(--color-border)] shadow-[var(--shadow-clay)] bg-[var(--color-surface-strong)] p-8 text-center mb-6">
                <div class="text-5xl mb-3" aria-hidden="true">🌈</div>
                <p class="font-display font-bold text-[var(--color-text)] mb-1">All done for today!</p>
                <p class="text-sm text-[var(--color-text-muted)] mb-4">Come back tomorrow for more fun.</p>
                <a href="{{ route('child.activities', $child) }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 min-h-[2.75rem] rounded-[var(--radius-sm)] border-[3px] border-[var(--color-brand-600)] bg-gradient-to-br from-[var(--color-brand-600)] to-[var(--color-brand-400)] text-white font-bold shadow-[var(--shadow-clay)] hover:-translate-y-[2px] transition-all focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2">
                    <x-ui.icon name="sparkles" class="w-4 h-4" aria-hidden="true" />Explore More
                </a>
            </div>
        @else
            <div class="grid grid-cols-2 gap-3 mb-6">
                @foreach($todayActivities as $activity)
                @php
                    $actUrl = match($activity->activity_type ?? '') {
                        'tracing'    => route('activities.tracing', $activity) . '?child=' . $child->id,
                        'drawing'    => route('activities.drawing',  $activity) . '?child=' . $child->id,
                        'puzzle'     => route('activities.puzzle',   $activity) . '?child=' . $child->id,
                        'video'      => route('activities.video',    $activity) . '?child=' . $child->id,
                        'slides'     => route('activities.slides',   $activity) . '?child=' . $child->id,
                        'simulation' => route('activities.slides',   $activity) . '?child=' . $child->id,
                        default      => route('activities.show',     $activity) . '?child=' . $child->id,
                    };
                    $isDone = $activity->pivot->completed ?? false;
                @endphp
                <a href="{{ $actUrl }}"
                   class="relative flex flex-col items-center text-center rounded-[var(--radius-card)] border-[3px] border-[var(--color-border)] shadow-[var(--shadow-clay)] bg-[var(--color-surface-strong)] p-4 min-h-[6rem] gap-1 hover:-translate-y-1 hover:shadow-[var(--shadow-clay-hover)] transition-all focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2 {{ $isDone ? 'opacity-70' : '' }}"
                   aria-label="{{ $activity->title }}{{ $isDone ? ' (completed)' : '' }}">
                    @if($isDone)
                    <span class="absolute top-2 end-2 w-5 h-5 rounded-full bg-emerald-500 text-white flex items-center justify-center" aria-hidden="true">
                        <x-ui.icon name="check" class="w-3 h-3" />
                    </span>
                    @endif
                    <div class="text-3xl leading-none" aria-hidden="true">{{ $activity->emoji ?? '📚' }}</div>
                    <div class="font-display font-bold text-sm text-[var(--color-text)] leading-tight">{{ $activity->title }}</div>
                    @if($activity->subject)
                        <div class="text-xs text-[var(--color-text-muted)]">{{ ucfirst($activity->subject) }}</div>
                    @endif
                </a>
                @endforeach
            </div>
        @endif

        {{-- Next milestone teaser --}}
        @if($nextMilestone)
        <a href="{{ route('parent.milestones') }}"
           class="flex items-center gap-4 rounded-[var(--radius-card)] border-[3px] border-[var(--color-accent)] bg-amber-50 shadow-[var(--shadow-clay)] px-4 py-3 mb-6 text-[var(--color-text)] no-underline hover:-translate-y-0.5 transition-all focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2">
            <span class="text-3xl shrink-0" aria-hidden="true">🏆</span>
            <div class="flex-1 min-w-0">
                <div class="text-xs font-bold text-[var(--color-accent)] uppercase tracking-wide">Next Milestone</div>
                <div class="font-display font-bold text-sm text-[var(--color-text)] truncate">{{ $nextMilestone->title }}</div>
                <div class="text-xs text-amber-600 font-semibold mt-0.5">Keep going — you're almost there!</div>
            </div>
            <x-ui.icon name="chevron-right" class="w-5 h-5 shrink-0 text-[var(--color-accent)]" aria-hidden="true" />
        </a>
        @endif

        {{-- See all CTA --}}
        <div class="text-center mb-4">
            <a href="{{ route('child.activities', $child) }}"
               class="inline-flex items-center gap-2 px-6 py-3 min-h-[2.75rem] rounded-[var(--radius-sm)] border-[3px] border-[var(--color-brand-600)] bg-gradient-to-br from-[var(--color-brand-600)] to-[var(--color-brand-400)] text-white font-display font-black text-base shadow-[var(--shadow-clay)] hover:-translate-y-[2px] hover:shadow-[var(--shadow-clay-hover)] transition-all focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2">
                <x-ui.icon name="sparkles" class="w-5 h-5" aria-hidden="true" />Explore All Adventures
            </a>
        </div>

        {{-- Paywall nudge --}}
        @if($totalCompleted >= 7 && !$child->parent->subscriptions()->where('active', true)->exists() && in_array(auth()->user()->role ?? '', ['Parent', 'Student']))
        <x-ui.card variant="gradient" padding="md" class="mt-4 text-center">
            <div class="text-3xl mb-2" aria-hidden="true">✨</div>
            <h3 class="font-display font-black text-[var(--color-text)] mb-1">Unlock 100+ More Adventures!</h3>
            <p class="text-sm text-[var(--color-text-muted)] mb-4">{{ $child->name }} is on a roll! Unlock the full curriculum to keep the momentum going.</p>
            <x-ui.button variant="primary" href="{{ route('pricing') }}" icon="sparkles">
                See Plans — from $3/mo
            </x-ui.button>
        </x-ui.card>
        @endif

    </div>
</div>

@push('head')
<style>
@keyframes flicker {
    0%,100% { transform: scale(1) rotate(-5deg); }
    50% { transform: scale(1.15) rotate(5deg); }
}
@keyframes nn-float {
    0%,100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}
</style>
@endpush
@endsection
