@extends('layouts.child')

@section('page_title', 'Hello, ' . $child->name . '!')

@push('head')
<style>
/* ── Dashboard gradient hero ── */
.nn-dash-hero {
    position: relative;
    padding: 2rem 1.25rem 3.5rem;
    overflow: hidden;
    margin-bottom: -2rem;
}
.nn-dash-hero__bg {
    position: absolute; inset: 0;
    background: var(--tier-hero-from, linear-gradient(135deg,#7C3AED,#A78BFA));
    z-index: 0;
}
.nn-dash-hero__bg::after {
    content: '';
    position: absolute; inset: 0;
    background: url("data:image/svg+xml,%3Csvg viewBox='0 0 400 180' xmlns='http://www.w3.org/2000/svg'%3E%3Ccircle cx='50' cy='40' r='60' fill='rgba(255,255,255,0.06)'/%3E%3Ccircle cx='370' cy='160' r='80' fill='rgba(255,255,255,0.05)'/%3E%3Ccircle cx='200' cy='20' r='40' fill='rgba(255,255,255,0.04)'/%3E%3C/svg%3E") no-repeat center/cover;
}
.nn-dash-hero::after {
    content: '';
    position: absolute; bottom: 0; left: 0; right: 0;
    height: 2.5rem;
    background: #F8F7FF;
    border-radius: 2rem 2rem 0 0;
    z-index: 1;
}
.nn-dash-hero__content {
    position: relative; z-index: 2;
}
/* Stars floating in hero */
.nn-dash-star {
    position: absolute;
    font-size: 1.5rem;
    opacity: 0.3;
    animation: nn-drift 7s ease-in-out infinite;
    z-index: 2;
    pointer-events: none;
}
/* Body background */
.child-layout.age-baby    { background: #FFF8F0; }
.child-layout.age-toddler { background: #F0FFF8; }
.child-layout.age-preschool { background: #F5F0FF; }
.child-layout.age-school  { background: #F0F0FF; }

/* Streak flame animation */
@keyframes flicker {
    0%,100% { transform: scale(1) rotate(-5deg); }
    50% { transform: scale(1.15) rotate(5deg); }
}
.nn-flame { animation: flicker 1.8s ease-in-out infinite; display:inline-block; }
</style>
@endpush

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
        'baby'     => ['emoji' => '🐣', 'name' => 'Pip',    'greeting' => 'Hello, little star!', 'bg' => 'linear-gradient(135deg,#FF8C42,#FBBF24)', 'badge' => 'rgba(255,255,255,0.25)'],
        'toddler'  => ['emoji' => '🦊', 'name' => 'Finn',   'greeting' => 'Ready to discover?',  'bg' => 'linear-gradient(135deg,#10B981,#34D399)', 'badge' => 'rgba(255,255,255,0.25)'],
        'preschool'=> ['emoji' => '🐢', 'name' => 'Shelly', 'greeting' => "Let's explore today!", 'bg' => 'linear-gradient(135deg,#7C3AED,#A78BFA)', 'badge' => 'rgba(255,255,255,0.25)'],
        'school'   => ['emoji' => '🦉', 'name' => 'Ollie',  'greeting' => 'Knowledge awaits!',   'bg' => 'linear-gradient(135deg,#1E40AF,#3B82F6)', 'badge' => 'rgba(255,255,255,0.25)'],
    ];
    $mascot = $mascots[$tier];
    $streak = $child->streak_days ?? 0;
    $progressPct = $totalCompleted > 0 ? min(round(($totalCompleted / max($totalCompleted + 3, 10)) * 100), 100) : 0;
@endphp

{{-- ══════════════════════════════════════════════
     HERO BANNER
══════════════════════════════════════════════ --}}
<div class="nn-dash-hero">
    <div class="nn-dash-hero__bg" style="background:{{ $mascot['bg'] }};"></div>
    {{-- Floating stars --}}
    <span class="nn-dash-star" style="top:10%;left:5%;">⭐</span>
    <span class="nn-dash-star" style="top:20%;right:8%;animation-delay:2s;">✨</span>
    <span class="nn-dash-star" style="top:55%;left:80%;animation-delay:4s;font-size:1rem;">🌟</span>

    <div class="nn-dash-hero__content">
        <div class="d-flex align-items-center gap-3 mb-3">
            {{-- Mascot bubble --}}
            <div style="font-size:4rem;line-height:1;filter:drop-shadow(0 4px 14px rgba(0,0,0,0.2));animation:nn-float 3s ease-in-out infinite;">
                {{ $mascot['emoji'] }}
            </div>
            <div>
                <div style="font-size:0.75rem;font-weight:700;color:rgba(255,255,255,0.75);text-transform:uppercase;letter-spacing:0.08em;margin-bottom:0.2rem;">
                    {{ $mascot['name'] }} says
                </div>
                <h2 style="font-family:'Baloo 2',sans-serif;font-weight:900;font-size:clamp(1.3rem,5vw,1.75rem);color:#fff;margin:0;text-shadow:0 2px 12px rgba(0,0,0,0.2);">
                    {{ $mascot['greeting'] }}
                </h2>
                <div style="color:rgba(255,255,255,0.85);font-weight:700;font-size:0.9rem;margin-top:0.2rem;">
                    Hi <strong style="color:#fff;">{{ $child->name }}</strong>!
                    @if($child->age_display) You're <strong style="color:#fff;">{{ $child->age_display }}</strong> old. @endif
                </div>
            </div>
        </div>

        {{-- Streak chip --}}
        @if($streak > 0)
        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-1"
             style="background:{{ $mascot['badge'] }};backdrop-filter:blur(8px);border:1.5px solid rgba(255,255,255,0.35);">
            <span class="nn-flame">🔥</span>
            <span style="font-family:'Baloo 2',sans-serif;font-weight:800;color:#fff;font-size:0.9rem;">
                {{ $streak }}-day streak!
            </span>
        </div>
        @endif
    </div>
</div>

{{-- ══════════════════════════════════════════════
     MAIN CONTENT CARD
══════════════════════════════════════════════ --}}
<div style="background:#F8F7FF;padding:0 0.75rem 2rem;min-height:60vh;">
    <div style="max-width:600px;margin:0 auto;padding-top:2.5rem;">

        {{-- Stats row --}}
        <div class="row g-2 mb-4">
            <div class="col-4">
                <div class="stat-chip">
                    <i class="bi bi-fire stat-chip__icon" aria-hidden="true"></i>
                    <span class="stat-chip__val">{{ $streak }}</span>
                    <span class="stat-chip__label">Streak</span>
                </div>
            </div>
            <div class="col-4">
                <div class="stat-chip">
                    <i class="bi bi-star-fill stat-chip__icon" aria-hidden="true"></i>
                    <span class="stat-chip__val">{{ $totalCompleted }}</span>
                    <span class="stat-chip__label">Done</span>
                </div>
            </div>
            <div class="col-4">
                <div class="stat-chip">
                    <i class="bi bi-award-fill stat-chip__icon" aria-hidden="true"></i>
                    <span class="stat-chip__val">{{ $badgeCount }}</span>
                    <span class="stat-chip__label">Badges</span>
                </div>
            </div>
        </div>

        {{-- XP Progress bar --}}
        @if($totalCompleted > 0)
        <div class="mb-4 px-1">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span style="font-family:'Baloo 2',sans-serif;font-weight:800;font-size:0.85rem;color:#6B7280;">
                    <i class="bi bi-lightning-charge-fill me-1" style="color:#F59E0B;"></i>Learning XP
                </span>
                <span style="font-family:'Baloo 2',sans-serif;font-weight:800;font-size:0.82rem;color:#7C3AED;">
                    {{ $totalCompleted }} / {{ $totalCompleted + 3 }} ⭐
                </span>
            </div>
            <div style="height:14px;background:rgba(124,58,237,0.10);border-radius:7px;overflow:hidden;">
                <div style="height:100%;width:{{ $progressPct }}%;background:linear-gradient(90deg,#7C3AED,#A78BFA,#F59E0B);border-radius:7px;transition:width 1s ease;position:relative;">
                    <div style="position:absolute;inset:0;background:linear-gradient(90deg,transparent,rgba(255,255,255,0.3),transparent);animation:nn-shimmer 2s infinite;"></div>
                </div>
            </div>
        </div>
        @endif

        {{-- Today's Activities --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="section-title">
                <span aria-hidden="true">🎯</span> Today's Adventures
            </h2>
            <a href="{{ route('child.activities', $child) }}" class="btn-tier" style="font-size:0.82rem;padding:0.45rem 1rem;">
                <i class="bi bi-grid-fill me-1"></i>All
            </a>
        </div>

        @if($todayActivities->isEmpty())
            <div class="empty-state mb-4">
                <span class="empty-state__icon">🌈</span>
                <p>🎉 All done for today!<br><small style="font-size:0.9rem;opacity:0.8;">Come back tomorrow for more fun.</small></p>
                <a href="{{ route('child.activities', $child) }}" class="btn-tier">
                    <i class="bi bi-stars me-1"></i>Explore More
                </a>
            </div>
        @else
            <div class="activity-grid mb-4">
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
                       class="activity-card-child {{ $isDone ? 'activity-card-child--done' : '' }}"
                       aria-label="{{ $activity->title }}{{ $isDone ? ' (completed)' : '' }}">
                        <div class="activity-card-child__emoji" aria-hidden="true">{{ $activity->emoji ?? '📚' }}</div>
                        <div class="activity-card-child__title">{{ $activity->title }}</div>
                        <div class="activity-card-child__subject">{{ ucfirst($activity->subject ?? '') }}</div>
                        @if($isDone)
                            <div class="activity-card-child__done-badge" aria-label="Completed">
                                <i class="bi bi-check-lg" aria-hidden="true"></i>
                            </div>
                        @endif
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Next Milestone --}}
        @if($nextMilestone)
            <a href="{{ route('parent.milestones') }}" class="milestone-teaser mb-4 d-flex text-decoration-none" style="color:inherit;">
                <span style="font-size:1.75rem;flex-shrink:0;" aria-hidden="true">🏆</span>
                <div style="margin-left:1rem;">
                    <div class="milestone-teaser__label">Next Milestone</div>
                    <div class="milestone-teaser__name">{{ $nextMilestone->title }}</div>
                    <div style="font-size:0.75rem;color:#92400E;margin-top:0.15rem;font-weight:600;">
                        Keep going — you're almost there!
                    </div>
                </div>
                <i class="bi bi-chevron-right ms-auto align-self-center" style="color:#F59E0B;font-size:1rem;"></i>
            </a>
        @endif

        {{-- See all activities CTA --}}
        <div class="text-center mt-3 mb-2">
            <a href="{{ route('child.activities', $child) }}" class="btn-tier" style="font-size:1rem;padding:0.75rem 2rem;">
                <i class="bi bi-stars me-2" aria-hidden="true"></i>Explore All Adventures
            </a>
        </div>

        {{-- Paywall nudge (after 7 completions without subscription) --}}
        @if($totalCompleted >= 7 && !$child->parent->subscriptions()->where('active', true)->exists())
        <div class="nn-paywall-nudge mt-4">
            <span class="nn-paywall-nudge__icon">✨</span>
            <div class="nn-paywall-nudge__title">Unlock 100+ More Adventures!</div>
            <p class="nn-paywall-nudge__text">{{ $child->name }} is on a roll! Unlock the full curriculum to keep the momentum going.</p>
            <a href="{{ route('pricing') }}" class="nn-paywall-nudge__btn">
                <i class="bi bi-stars me-1"></i>See Plans — from $3/mo
            </a>
        </div>
        @endif

    </div>
</div>
@endsection
