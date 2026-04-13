@extends('layouts.app')

@section('title', $child->name . "'s Progress — Noble Nest Academy")

@section('content')
@php
    $tierKey = match($child->age_tier ?? $child->age_bracket ?? 'learner') {
        'baby'       => 'baby',
        'toddler'    => 'toddler',
        'preschool'  => 'preschool',
        'school-age', 'school' => 'school',
        default      => 'default',
    };
    $tierIcon = match($tierKey) {
        'baby'     => 'bi-balloon-heart-fill',
        'toddler'  => 'bi-stars',
        'preschool'=> 'bi-flower1',
        'school'   => 'bi-mortarboard-fill',
        default    => 'bi-person-fill',
    };
    $tierLabel = match($tierKey) {
        'baby'     => 'Baby',
        'toddler'  => 'Toddler',
        'preschool'=> 'Preschool',
        'school'   => 'School Age',
        default    => ucfirst($child->age_tier ?? 'Learner'),
    };
@endphp

<div class="container py-4" style="max-width:860px;">

    {{-- ══════  BREADCRUMB  ══════ --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb align-items-center mb-0">
            <li class="breadcrumb-item">
                <a href="{{ route('parent.dashboard') }}" class="d-inline-flex align-items-center gap-1 text-decoration-none" style="color:var(--nn-primary,#7C3AED);">
                    <i class="bi bi-speedometer2" aria-hidden="true"></i>Dashboard
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ $child->name }}</li>
        </ol>
    </nav>

    {{-- ══════  CHILD PROFILE CARD  ══════ --}}
    <div class="nn-child-card mb-4">
        <div class="nn-child-card__header nn-tier-{{ $tierKey }}">
            <div class="nn-child-card__avatar" aria-hidden="true">
                {{ mb_substr($child->name, 0, 1) }}
            </div>
            <div class="flex-fill min-w-0">
                <h2 class="nn-child-card__name mb-0">{{ $child->name }}</h2>
                <div class="d-flex align-items-center gap-2 flex-wrap mt-1">
                    <span class="nn-child-card__tier-badge">
                        <i class="bi {{ $tierIcon }} me-1" aria-hidden="true"></i>{{ $tierLabel }}
                    </span>
                    @if($child->age_months)
                    <span class="nn-child-card__tier-badge">
                        <i class="bi bi-calendar3 me-1" aria-hidden="true"></i>{{ $child->age_display ?? (floor($child->age_months/12).'y '.($child->age_months % 12).'m') }}
                    </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="nn-child-card__stats">
            <div class="nn-child-card__stat">
                <span class="nn-child-card__stat-value">{{ $progress->total() }}</span>
                <span class="nn-child-card__stat-label">Completed</span>
            </div>
            <div class="nn-child-card__stat">
                <span class="nn-child-card__stat-value d-flex align-items-center justify-content-center gap-1">
                    <i class="bi bi-fire" aria-hidden="true"></i>{{ $child->streak_days ?? 0 }}
                </span>
                <span class="nn-child-card__stat-label">Day Streak</span>
            </div>
            <div class="nn-child-card__stat">
                <span class="nn-child-card__stat-value">
                    {{ $child->age_months ? floor($child->age_months/12).'y '.($child->age_months % 12).'m' : '—' }}
                </span>
                <span class="nn-child-card__stat-label">Age</span>
            </div>
        </div>
        <div class="nn-child-card__actions">
            <a href="{{ route('child.activities', $child) }}" class="nn-child-card__cta">
                <i class="bi bi-controller me-1" aria-hidden="true"></i>Start Learning
            </a>
            <a href="{{ route('child.dashboard', $child) }}" class="nn-child-card__cta" style="background:rgba(255,255,255,0.15);">
                <i class="bi bi-house me-1" aria-hidden="true"></i>Child Dashboard
            </a>
        </div>
    </div>

    {{-- ══════  ACTIVITY HISTORY  ══════ --}}
    <div class="nn-section-title mb-3">
        <i class="bi bi-clock-history nn-section-emoji" aria-hidden="true"></i>
        Activity History
        <span class="nn-section-count">{{ $progress->total() }}</span>
    </div>

    @if($progress->isEmpty())
    <div class="text-center py-5 rounded-4" style="background:rgba(255,255,255,0.7); border:2px dashed var(--nn-border,rgba(124,58,237,0.15));">
        <i class="bi bi-controller display-4 mb-3 d-block" style="color:var(--nn-primary,#7C3AED); opacity:0.4;" aria-hidden="true"></i>
        <p class="fw-semibold mb-1" style="color:var(--nn-text,#1E1B4B);">No activities completed yet.</p>
        <a href="{{ route('child.activities', $child) }}" class="btn btn-sm mt-2 fw-bold px-4" style="background:var(--nn-primary,#7C3AED); color:#fff; border:none; border-radius:999px;">
            <i class="bi bi-play-fill me-1" aria-hidden="true"></i>Start now!
        </a>
    </div>
    @else
    <div class="nn-history-list mb-4">
        @foreach($progress as $item)
        <div class="nn-history-item">
            <div class="nn-history-item__icon" aria-hidden="true">
                @if(!empty($item->activity->emoji))
                <span style="font-size:1.25rem;">{{ $item->activity->emoji }}</span>
                @else
                <i class="bi bi-book-open-fill"></i>
                @endif
            </div>
            <div class="nn-history-item__body">
                <div class="nn-history-item__title">{{ $item->activity->title ?? 'Activity' }}</div>
                <div class="nn-history-item__meta">
                    <i class="bi bi-calendar3" aria-hidden="true"></i>
                    {{ $item->completed_at?->format('M d, Y') }}
                    @if($item->completed_at)
                    <span aria-hidden="true">&middot;</span>
                    <i class="bi bi-clock" aria-hidden="true"></i>
                    {{ $item->completed_at->format('g:i A') }}
                    @endif
                </div>
            </div>
            <span class="nn-history-item__badge">
                <i class="bi bi-check-circle-fill" aria-hidden="true"></i>Done
            </span>
        </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-center">
        {{ $progress->links() }}
    </div>
    @endif

</div>
@endsection
