@extends('layouts.child')

@section('title', $child->name . "'s Learning Adventures")

@section('content')
@php
    $tierColor = match($child->age_bracket ?? 'learner') {
        'baby'       => '#F43F5E',
        'toddler'    => '#F59E0B',
        'preschool'  => '#7C3AED',
        'school-age' => '#10B981',
        default      => '#7C3AED',
    };
    $tierGrad = match($child->age_bracket ?? 'learner') {
        'baby'       => 'linear-gradient(135deg,#FF6B8A,#FF8FA3)',
        'toddler'    => 'linear-gradient(135deg,#F59E0B,#FBBF24)',
        'preschool'  => 'linear-gradient(135deg,#7C3AED,#A78BFA)',
        'school-age' => 'linear-gradient(135deg,#10B981,#34D399)',
        default      => 'linear-gradient(135deg,#7C3AED,#A78BFA)',
    };
    $tierMascot = match($child->age_bracket ?? 'learner') {
        'baby'   => '👶', 'toddler' => '🐣', 'preschool' => '🌱', 'school-age' => '🚀', default => '🌟',
    };
    $subjects = [
        ''          => ['label' => 'All',      'emoji' => '✨', 'color' => '#7C3AED'],
        'islamic'   => ['label' => 'Islamic',  'emoji' => '🌙', 'color' => '#7C3AED'],
        'art'       => ['label' => 'Art',      'emoji' => '🎨', 'color' => '#F43F5E'],
        'language'  => ['label' => 'Language', 'emoji' => '🗣️', 'color' => '#3B82F6'],
        'stem'      => ['label' => 'STEM',     'emoji' => '🔬', 'color' => '#10B981'],
        'stories'   => ['label' => 'Stories',  'emoji' => '📖', 'color' => '#F59E0B'],
        'motor'     => ['label' => 'Motor',    'emoji' => '🖐️', 'color' => '#EC4899'],
    ];
@endphp
<style>:root { --tier-color: {{ $tierColor }}; --tier-grad: {{ $tierGrad }}; }</style>
<div class="nn-activities-page">

    {{-- ══════════  PAGE HEADER  ══════════ --}}
    <div class="nn-activities-header">
        <div class="container" style="max-width:860px;">
            <div class="d-flex align-items-center gap-3">
                <span class="nn-activities-header__mascot" aria-hidden="true">{{ $tierMascot }}</span>
                <div class="flex-fill min-w-0">
                    <h1 class="nn-activities-header__title mb-0">{{ $child->name }}'s Adventures!</h1>
                    <div class="d-flex align-items-center gap-2 flex-wrap mt-1">
                        <span class="nn-activities-header__tier-badge">{{ ucfirst($child->age_bracket ?? 'learner') }}@if($child->age_months) &middot; Age {{ floor($child->age_months/12) }}@endif</span>
                        @if($child->streak_days)
                        <span class="nn-activities-header__stat">
                            <i class="bi bi-fire" aria-hidden="true"></i> {{ $child->streak_days }}d streak
                        </span>
                        @endif
                        <span class="nn-activities-header__stat">
                            <i class="bi bi-star-fill" aria-hidden="true"></i> {{ $completedCount }} done
                        </span>
                    </div>
                </div>
                @if($child->share_card_url)
                <a href="{{ $child->share_card_url }}" target="_blank" rel="noopener" class="nn-activities-header__share-btn flex-shrink-0">
                    <i class="bi bi-share-fill me-1" aria-hidden="true"></i>Share
                </a>
                @endif
            </div>
        </div>
    </div>

    <div class="container py-3" style="max-width:860px;">

        {{-- ══════════  NEXT ADVENTURE CARD  ══════════ --}}
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
        <div class="nn-next-adventure mb-4">
            <span class="nn-next-adventure__emoji" aria-hidden="true">{{ $nextActivity->emoji ?? '🎯' }}</span>
            <div class="nn-next-adventure__body flex-fill min-w-0">
                <div class="nn-next-adventure__label">Next Adventure</div>
                <div class="nn-next-adventure__title">{{ $nextActivity->title }}</div>
                @if($nextActivity->description)
                <div class="nn-next-adventure__desc d-none d-sm-block">{{ Str::limit($nextActivity->description, 70) }}</div>
                @endif
            </div>
            <a href="{{ $nextUrl }}" class="nn-next-adventure__cta flex-shrink-0" aria-label="Play {{ $nextActivity->title }}">
                <i class="bi bi-play-fill me-1" aria-hidden="true"></i>Play Now
            </a>
        </div>
        @endif

        {{-- ══════════  SUBJECT FILTER  ══════════ --}}
        <nav class="nn-subject-filter mb-4" aria-label="Filter by subject">
            @foreach($subjects as $key => $s)
            @php $isActive = ($activeSubject ?? '') === $key; @endphp
            <a href="{{ route('child.activities', $child) }}{{ $key ? '?subject='.$key : '' }}"
               class="nn-subject-pill {{ $isActive ? 'nn-subject-pill--active' : '' }}"
               style="--pill-color:{{ $s['color'] }};"
               @if($isActive) aria-current="page" @endif>
                <span aria-hidden="true">{{ $s['emoji'] }}</span>{{ $s['label'] }}
            </a>
            @endforeach
        </nav>

        {{-- ══════════  SUBSCRIPTION / DRIP PROGRESS  ══════════ --}}
        @if($hasSubscription)
        <div class="nn-week-progress mb-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-bold" style="font-family:'Baloo 2',sans-serif;">
                    <i class="bi bi-calendar3 me-1" aria-hidden="true"></i>Week {{ $currentWeek }} of {{ $totalWeeks }}
                </span>
                <span class="nn-section-count">{{ $maxOrder }} unlocked</span>
            </div>
            <div class="progress mb-1" style="height:10px; border-radius:999px;">
                <div class="progress-bar" role="progressbar"
                     style="width:{{ ($currentWeek / max($totalWeeks,1)) * 100 }}%; background:var(--tier-grad); border-radius:999px;"
                     aria-valuenow="{{ $currentWeek }}" aria-valuemin="0" aria-valuemax="{{ $totalWeeks }}"></div>
            </div>
            <div class="d-flex justify-content-between small text-muted mt-1">
                <span><i class="bi bi-star-fill me-1" aria-hidden="true"></i>{{ $completedCount }} done</span>
                @if($currentWeek < $totalWeeks && $daysToNextWeek > 0)
                <span><i class="bi bi-unlock me-1" aria-hidden="true"></i>5 more unlock in {{ $daysToNextWeek }}d</span>
                @elseif($currentWeek >= $totalWeeks)
                <span><i class="bi bi-check-circle-fill me-1" aria-hidden="true"></i>All unlocked!</span>
                @endif
            </div>
        </div>
        @endif

        {{-- ══════════  ACTIVITY GRID  ══════════ --}}
        <div class="nn-section-title mb-3">
            <i class="bi bi-grid-fill nn-section-emoji" aria-hidden="true"></i>
            @if($activeSubject) {{ ucfirst($activeSubject) }} Activities @else All Activities @endif
            <span class="nn-section-count">{{ $activities->total() }}</span>
        </div>

        <div class="row g-3">
            @forelse($activities as $activity)
            @php
                $subjectColor = match($activity->subject ?? '') {
                    'quran', 'arabic', 'islamic_studies', 'islamic' => '#7C3AED',
                    'art'                                           => '#F43F5E',
                    'language'                                      => '#3B82F6',
                    'stem', 'science', 'numeracy', 'coding'        => '#10B981',
                    'literacy', 'stories'                          => '#F59E0B',
                    'motor'                                        => '#EC4899',
                    default                                        => '#A78BFA',
                };
                $isLocked    = $activity->locked ?? false;
                $isDone      = $activity->is_completed ?? false;
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
            <div class="col-6 col-md-4">
                <div class="nn-activity-card {{ $isLocked ? 'nn-activity-card--locked' : '' }}"
                     style="--card-color:{{ $subjectColor }};">
                    <div class="nn-card-ribbon" style="background:{{ $subjectColor }};"></div>

                    {{-- Top badges --}}
                    <div class="nn-card-top-badges">
                        <span class="nn-badge" style="background:{{ $subjectColor }}18; color:{{ $subjectColor }}; border-color:{{ $subjectColor }}33;">
                            {{ ucfirst($activity->subject ?? 'general') }}
                        </span>
                        @if($isDone)
                        <span class="nn-badge nn-badge--done">
                            <i class="bi bi-check-circle-fill me-1" aria-hidden="true"></i>Done
                        </span>
                        @elseif($isLocked && $hasSubscription)
                        <span class="nn-badge nn-badge--locked-week">
                            <i class="bi bi-lock-fill me-1" aria-hidden="true"></i>Wk {{ $activity->unlock_week ?? '?' }}
                        </span>
                        @elseif($isLocked)
                        <span class="nn-badge nn-badge--locked">
                            <i class="bi bi-lock-fill me-1" aria-hidden="true"></i>Premium
                        </span>
                        @elseif($activity->is_free)
                        <span class="nn-badge nn-badge-free">
                            <i class="bi bi-stars me-1" aria-hidden="true"></i>Free
                        </span>
                        @endif
                    </div>

                    <div class="nn-card-emoji" aria-hidden="true">{{ $activity->emoji ?? '📚' }}</div>
                    <div class="nn-card-title">{{ $activity->title }}</div>

                    @if($activity->description)
                    <div class="nn-card-desc">{{ Str::limit($activity->description, 75) }}</div>
                    @endif

                    <div class="nn-card-badges">
                        @if($activity->duration_minutes)
                        <span class="nn-badge nn-badge-time">
                            <i class="bi bi-clock me-1" aria-hidden="true"></i>{{ $activity->duration_minutes }}min
                        </span>
                        @endif
                        @if($activity->age_min !== null)
                        <span class="nn-badge nn-badge-age">
                            <i class="bi bi-person-fill me-1" aria-hidden="true"></i>{{ $activity->age_min }}–{{ $activity->age_max }}yr
                        </span>
                        @endif
                        @if($activity->activity_type)
                        <span class="nn-badge nn-badge--type">{{ ucfirst($activity->activity_type) }}</span>
                        @endif
                    </div>

                    {{-- CTA button --}}
                    @if($isLocked)
                        @if($hasSubscription)
                        <div class="nn-card-cta nn-card-cta--locked" aria-label="Unlocks in week {{ $activity->unlock_week ?? '?' }}">
                            <i class="bi bi-lock-fill me-1" aria-hidden="true"></i>Unlocks Week {{ $activity->unlock_week ?? '?' }}
                        </div>
                        @else
                        <a href="{{ route('pricing') }}" class="nn-card-cta nn-card-cta--premium d-block text-decoration-none">
                            <i class="bi bi-stars me-1" aria-hidden="true"></i>Unlock Premium
                        </a>
                        @endif
                    @elseif($isDone)
                    <a href="{{ $activityUrl }}" class="nn-card-cta nn-card-cta--replay d-block text-decoration-none">
                        <i class="bi bi-arrow-repeat me-1" aria-hidden="true"></i>Play Again
                    </a>
                    @else
                    <a href="{{ $activityUrl }}" class="nn-card-cta nn-card-cta--play d-block text-decoration-none">
                        <i class="bi bi-play-fill me-1" aria-hidden="true"></i>Play Now
                    </a>
                    @endif
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-star-fill display-3 text-warning" aria-hidden="true"></i>
                <p class="nn-playful-title mt-3" style="font-size:1.5rem;">No adventures found!</p>
                <p class="text-muted">Try a different subject, or check back soon — we add new content every week!</p>
                @if($activeSubject)
                <a href="{{ route('child.activities', $child) }}" class="nn-btn nn-btn-filter mt-2">
                    <i class="bi bi-stars me-1" aria-hidden="true"></i>Show All
                </a>
                @endif
            </div>
            @endforelse
        </div>

        {{-- ══════════  PAGINATION  ══════════ --}}
        <div class="mt-4 d-flex justify-content-center">
            {{ $activities->appends(request()->query())->links() }}
        </div>

        {{-- ══════════  UPGRADE NUDGE (free tier only)  ══════════ --}}
        @unless($hasSubscription)
        <div class="nn-upgrade-nudge mt-4">
            <i class="bi bi-stars nn-upgrade-nudge__icon" aria-hidden="true"></i>
            <h5 class="nn-upgrade-nudge__title">Unlock All Weekly Packs!</h5>
            <p class="nn-upgrade-nudge__text">Fresh adventures every week tailored to {{ $child->name }}'s age. Keep the spark going!</p>
            <a href="{{ route('pricing') }}" class="nn-upgrade-nudge__btn">
                <i class="bi bi-arrow-right-circle-fill me-2" aria-hidden="true"></i>From $3/month
            </a>
        </div>
        @endunless

    </div>
</div>
@endsection
