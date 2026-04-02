@extends('layouts.child')

@section('page_title', 'Hello, ' . $child->name . '!')

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
        'baby'     => ['emoji' => '🐣', 'name' => 'Pip',    'greeting' => 'Hello, little explorer!'],
        'toddler'  => ['emoji' => '🦊', 'name' => 'Finn',   'greeting' => 'Ready to discover today?'],
        'preschool'=> ['emoji' => '🐢', 'name' => 'Shelly', 'greeting' => 'Let\'s learn something new!'],
        'school'   => ['emoji' => '🦉', 'name' => 'Ollie',  'greeting' => 'Knowledge awaits, Scholar!'],
    ];
    $mascot = $mascots[$tier];
    $streak = $child->streak_days ?? 0;
@endphp

<div class="child-dashboard" data-tier="{{ $tier }}">

    {{-- Mascot Greeting --}}
    <div class="mascot-card mb-4">
        <div class="mascot-emoji">{{ $mascot['emoji'] }}</div>
        <div>
            <div class="mascot-name">{{ $mascot['name'] }} says:</div>
            <div class="mascot-greeting">{{ $mascot['greeting'] }}</div>
            <div class="mascot-sub">Hi <strong>{{ $child->name }}</strong>! You're <strong>{{ $child->age_display }}</strong> old.</div>
        </div>
    </div>

    {{-- Streak + Stats Bar --}}
    <div class="row g-3 mb-4">
        <div class="col-4">
            <div class="stat-chip">
                <span class="stat-chip__icon">🔥</span>
                <span class="stat-chip__val">{{ $streak }}</span>
                <span class="stat-chip__label">Day Streak</span>
            </div>
        </div>
        <div class="col-4">
            <div class="stat-chip">
                <span class="stat-chip__icon">⭐</span>
                <span class="stat-chip__val">{{ $totalCompleted }}</span>
                <span class="stat-chip__label">Done</span>
            </div>
        </div>
        <div class="col-4">
            <div class="stat-chip">
                <span class="stat-chip__icon">🏅</span>
                <span class="stat-chip__val">{{ $badgeCount }}</span>
                <span class="stat-chip__label">Badges</span>
            </div>
        </div>
    </div>

    {{-- Today's 3 Activities --}}
    <h2 class="section-title mb-3">Today's Adventures</h2>

    @if($todayActivities->isEmpty())
        <div class="empty-state">
            <div class="empty-state__icon">🌈</div>
            <p>All done for today! Come back tomorrow.</p>
            <a href="{{ route('child.activities', $child) }}" class="btn-tier">All Activities</a>
        </div>
    @else
        <div class="activity-grid">
            @foreach($todayActivities as $activity)
                <a href="{{ route('child.activities', $child) }}" class="activity-card-child @if($activity->pivot->completed ?? false) activity-card-child--done @endif">
                    <div class="activity-card-child__emoji">{{ $activity->emoji ?? '📚' }}</div>
                    <div class="activity-card-child__title">{{ $activity->title }}</div>
                    <div class="activity-card-child__subject">{{ ucfirst($activity->subject ?? '') }}</div>
                    @if($activity->pivot->completed ?? false)
                        <div class="activity-card-child__done-badge">✓ Done!</div>
                    @endif
                </a>
            @endforeach
        </div>
    @endif

    {{-- Next Milestone --}}
    @if($nextMilestone)
        <div class="milestone-teaser mt-4">
            <span class="milestone-teaser__icon">🎯</span>
            <div>
                <div class="milestone-teaser__label">Next milestone</div>
                <div class="milestone-teaser__name">{{ $nextMilestone->name }}</div>
            </div>
        </div>
    @endif

    {{-- Free activity paywall (after 7 completions) --}}
    @if($totalCompleted >= 7 && !$child->parent->subscriptions()->where('active', true)->exists())
        @include('partials.paywall-nudge', ['child' => $child])
    @endif
</div>

<style>
.child-dashboard { max-width: 480px; margin: 0 auto; padding: 1rem; }
.mascot-card { display: flex; align-items: center; gap: 1rem; background: rgba(255,255,255,0.9); border-radius: 1.5rem; padding: 1.25rem 1.5rem; box-shadow: 0 4px 20px rgba(0,0,0,0.07); }
.mascot-emoji { font-size: 3rem; line-height: 1; flex-shrink: 0; }
.mascot-name { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.08em; color: #6b7280; font-weight: 600; }
.mascot-greeting { font-size: 1.1rem; font-weight: 700; line-height: 1.3; }
.mascot-sub { font-size: 0.85rem; color: #6b7280; margin-top: 2px; }
.stat-chip { background: rgba(255,255,255,0.85); border-radius: 1rem; padding: 0.75rem 0.5rem; text-align: center; box-shadow: 0 2px 12px rgba(0,0,0,0.06); }
.stat-chip__icon { display: block; font-size: 1.4rem; }
.stat-chip__val { display: block; font-size: 1.5rem; font-weight: 800; line-height: 1.1; }
.stat-chip__label { display: block; font-size: 0.65rem; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.06em; }
.section-title { font-size: 1rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #374151; }
.activity-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; }
.activity-card-child { display: flex; flex-direction: column; align-items: center; background: rgba(255,255,255,0.9); border-radius: 1.25rem; padding: 1rem 0.5rem; text-align: center; text-decoration: none; color: inherit; box-shadow: 0 2px 12px rgba(0,0,0,0.06); transition: transform 0.15s; position: relative; }
.activity-card-child:hover { transform: translateY(-2px); }
.activity-card-child--done { opacity: 0.6; }
.activity-card-child__emoji { font-size: 2.2rem; }
.activity-card-child__title { font-size: 0.75rem; font-weight: 600; margin-top: 4px; line-height: 1.2; }
.activity-card-child__subject { font-size: 0.6rem; color: #9ca3af; text-transform: uppercase; margin-top: 2px; }
.activity-card-child__done-badge { position: absolute; top: 6px; right: 6px; background: #22c55e; color: #fff; font-size: 0.55rem; font-weight: 700; border-radius: 50px; padding: 2px 6px; }
.empty-state { text-align: center; padding: 2rem; background: rgba(255,255,255,0.7); border-radius: 1.5rem; }
.empty-state__icon { font-size: 3rem; }
.btn-tier { display: inline-block; margin-top: 0.75rem; padding: 0.6rem 1.5rem; background: var(--tier-color, #0d5c63); color: #fff; border-radius: 50px; font-size: 0.85rem; font-weight: 600; text-decoration: none; }
.milestone-teaser { display: flex; align-items: center; gap: 0.75rem; background: rgba(255,255,255,0.85); border-radius: 1rem; padding: 0.9rem 1.2rem; }
.milestone-teaser__icon { font-size: 1.8rem; }
.milestone-teaser__label { font-size: 0.65rem; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.06em; }
.milestone-teaser__name { font-size: 0.95rem; font-weight: 700; }
</style>
@endsection
