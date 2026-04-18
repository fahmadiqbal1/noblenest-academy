{{--
 * Beautiful Activity Card Partial
 * Usage: @include('child._activity_card', ['activity' => $activity, 'child' => $child])
 *
 * Supports: subject colour coding, skill tags, duration chips,
 *           Islamic/Quran green theme, baby parent-led banners,
 *           premium lock overlay, hover lift animation.
--}}
@php
    $subjectConfig = [
        'quran'           => ['color' => '#065f46', 'light' => '#d1fae5', 'border' => '#10b981', 'icon' => '☪️',  'label' => 'Quran'],
        'islamic_studies' => ['color' => '#1e3a5f', 'light' => '#dbeafe', 'border' => '#3b82f6', 'icon' => '🕌',  'label' => 'Islamic Studies'],
        'arabic'          => ['color' => '#4c1d95', 'light' => '#ede9fe', 'border' => '#8b5cf6', 'icon' => '📜',  'label' => 'Arabic'],
        'math'            => ['color' => '#1e3a8a', 'light' => '#dbeafe', 'border' => '#3b82f6', 'icon' => '🔢',  'label' => 'Maths'],
        'literacy'        => ['color' => '#7c2d12', 'light' => '#ffedd5', 'border' => '#f97316', 'icon' => '📖',  'label' => 'Literacy'],
        'science'         => ['color' => '#134e4a', 'light' => '#ccfbf1', 'border' => '#14b8a6', 'icon' => '🔬',  'label' => 'Science'],
        'technology'      => ['color' => '#312e81', 'light' => '#e0e7ff', 'border' => '#6366f1', 'icon' => '💻',  'label' => 'Technology'],
        'engineering'     => ['color' => '#1c1917', 'light' => '#f5f5f4', 'border' => '#78716c', 'icon' => '⚙️',  'label' => 'Engineering'],
        'art'             => ['color' => '#831843', 'light' => '#fce7f3', 'border' => '#ec4899', 'icon' => '🎨',  'label' => 'Art & Craft'],
        'motor'           => ['color' => '#14532d', 'light' => '#dcfce7', 'border' => '#22c55e', 'icon' => '🏃',  'label' => 'Motor Skills'],
        'sensory'         => ['color' => '#581c87', 'light' => '#f3e8ff', 'border' => '#a855f7', 'icon' => '🖐️', 'label' => 'Sensory'],
        'social'          => ['color' => '#064e3b', 'light' => '#d1fae5', 'border' => '#10b981', 'icon' => '🤝',  'label' => 'Social'],
        'cognitive'       => ['color' => '#3b1e07', 'light' => '#fef3c7', 'border' => '#f59e0b', 'icon' => '🧩',  'label' => 'Cognitive'],
        'character'       => ['color' => '#7f1d1d', 'light' => '#fee2e2', 'border' => '#ef4444', 'icon' => '🌟',  'label' => 'Character'],
        'geography'       => ['color' => '#3b2a1a', 'light' => '#fef3c7', 'border' => '#d97706', 'icon' => '🗺️', 'label' => 'Geography'],
        'history'         => ['color' => '#7c1d03', 'light' => '#ffedd5', 'border' => '#ea580c', 'icon' => '📜',  'label' => 'History'],
        'coding'          => ['color' => '#0f172a', 'light' => '#e0e7ff', 'border' => '#6366f1', 'icon' => '👨‍💻','label' => 'Coding'],
        'routine'         => ['color' => '#14532d', 'light' => '#dcfce7', 'border' => '#16a34a', 'icon' => '🌙',  'label' => 'Routine'],
        'creative'        => ['color' => '#134e4a', 'light' => '#ccfbf1', 'border' => '#0d9488', 'icon' => '🎭',  'label' => 'Creative Play'],
        'language'        => ['color' => '#14532d', 'light' => '#dcfce7', 'border' => '#16a34a', 'icon' => '🗣️', 'label' => 'Language Arts'],
    ];

    $cfg = $subjectConfig[$activity->subject ?? ''] ?? [
        'color'  => '#374151',
        'light'  => '#f3f4f6',
        'border' => '#9ca3af',
        'icon'   => '📚',
        'label'  => ucfirst(str_replace('_', ' ', $activity->subject ?? 'Activity')),
    ];

    $locked     = $activity->locked ?? false;
    $isParentLed = ($activity->age_tier ?? '') === 'baby';
    $isIslamic  = in_array($activity->subject ?? '', ['quran', 'islamic_studies', 'arabic']);
    $isPreschool = ($activity->age_tier ?? '') === 'preschool';

    $typeIcons = [
        'hands_on'    => ['icon' => '🖐️', 'label' => 'Hands-on'],
        'worksheet'   => ['icon' => '📝', 'label' => 'Worksheet'],
        'reading'     => ['icon' => '📖', 'label' => 'Reading'],
        'game'        => ['icon' => '🎮', 'label' => 'Game'],
        'outdoor'     => ['icon' => '🌿', 'label' => 'Outdoor'],
        'craft'       => ['icon' => '✂️', 'label' => 'Craft'],
        'sensory'     => ['icon' => '🌈', 'label' => 'Sensory'],
        'vocal'       => ['icon' => '🎵', 'label' => 'Vocal'],
        'observation' => ['icon' => '🔍', 'label' => 'Observe'],
        'discussion'  => ['icon' => '💬', 'label' => 'Discuss'],
        'experiment'  => ['icon' => '🧪', 'label' => 'Experiment'],
        'project'     => ['icon' => '🏗️', 'label' => 'Project'],
        'routine'     => ['icon' => '⏰', 'label' => 'Routine'],
    ];
    $typeInfo = $typeIcons[$activity->activity_type ?? ''] ?? ['icon' => '🎯', 'label' => 'Activity'];

    $ctaLabel = $isParentLed ? '🌱 Open Guide' : ($isPreschool ? '🎉 Let\'s Play!' : '▶ Start');
@endphp

<div class="nn-card {{ $isIslamic ? 'nn-card--islamic' : '' }} {{ $locked ? 'nn-card--locked' : '' }}"
     style="--c: {{ $cfg['color'] }}; --cl: {{ $cfg['light'] }}; --cb: {{ $cfg['border'] }};">

    {{-- Coloured top stripe --}}
    <div class="nn-card__stripe"></div>

    {{-- Lock overlay --}}
    @if($locked)
    <div class="nn-card__lock-overlay">
        <div class="text-center text-white">
            <div class="nn-card__lock-icon">🔒</div>
            <div class="fw-bold small">Premium</div>
        </div>
    </div>
    @endif

    <div class="nn-card__body">

        {{-- Row 1: Emoji + badges --}}
        <div class="d-flex align-items-start justify-content-between mb-2">
            <span class="nn-card__emoji">{{ $activity->emoji ?? '📚' }}</span>
            <div class="d-flex flex-column align-items-end gap-1 ms-2">
                <span class="nn-card__subject-badge">{{ $cfg['icon'] }} {{ $cfg['label'] }}</span>
                @if($isParentLed)
                    <span class="nn-card__pill nn-card__pill--green">👩 Parent Guide</span>
                @endif
                @if($activity->is_free && !$locked)
                    <span class="nn-card__pill nn-card__pill--yellow">Free</span>
                @endif
            </div>
        </div>

        {{-- Title --}}
        <h6 class="nn-card__title">{{ $activity->title }}</h6>

        {{-- Description --}}
        @if($activity->description)
        <p class="nn-card__desc">{{ Str::limit($activity->description, 110) }}</p>
        @endif

        {{-- Meta chips --}}
        <div class="nn-card__chips">
            @if($activity->duration)
                <span class="nn-card__chip">⏱ {{ $activity->duration }}min</span>
            @endif
            @if($activity->activity_type)
                <span class="nn-card__chip">{{ $typeInfo['icon'] }} {{ $typeInfo['label'] }}</span>
            @endif
            @if($activity->skill)
                <span class="nn-card__chip nn-card__chip--skill" title="{{ $activity->skill }}">
                    🧠 {{ Str::limit($activity->skill, 22) }}
                </span>
            @endif
        </div>

        {{-- CTA --}}
        @if($locked)
            @if(in_array(auth()->user()->role ?? '', ['Parent', 'Student']))
            <a href="{{ route('pricing') }}" class="nn-card__btn nn-card__btn--lock">
                ✨ Unlock Premium
            </a>
            @else
            <span class="nn-card__btn nn-card__btn--lock" style="cursor:default;">
                🔒 Locked (preview)
            </span>
            @endif
        @else
            <form action="{{ route('child.activity.complete', [$child, $activity]) }}" method="POST">
                @csrf
                <button type="submit" class="nn-card__btn">{{ $ctaLabel }}</button>
            </form>
        @endif

    </div>
</div>

{{-- Styles (loaded once per page via @once) --}}
@once
@push('styles')
<style>
/* ── Noble Nest Activity Cards ─────────────────────────────── */
.nn-card {
    position: relative;
    background: var(--nn-surface-strong);
    border-radius: var(--nn-radius-sm);
    border: var(--nn-border-w) solid var(--nn-border);
    overflow: hidden;
    box-shadow: var(--nn-shadow);
    transition: transform .22s var(--nn-bounce), box-shadow .22s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}
.nn-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 32px rgba(124,58,237,0.15);
}
.nn-card__stripe {
    height: 6px;
    background: var(--cb, #6b7280);
    flex-shrink: 0;
}
.nn-card__body {
    padding: 1rem;
    display: flex;
    flex-direction: column;
    flex: 1;
}
.nn-card__emoji {
    font-size: 2.4rem;
    line-height: 1;
    flex-shrink: 0;
}
.nn-card__subject-badge {
    background: var(--c, #374151);
    color: #fff;
    border-radius: 20px;
    padding: 3px 9px;
    font-size: .65rem;
    font-weight: 600;
    letter-spacing: .3px;
    white-space: nowrap;
}
.nn-card__pill {
    border-radius: 20px;
    padding: 2px 8px;
    font-size: .62rem;
    font-weight: 600;
}
.nn-card__pill--green  { background: #d1fae5; color: #065f46; }
.nn-card__pill--yellow { background: #fef9c3; color: #713f12; }

.nn-card__title {
    font-size: .92rem;
    font-weight: 700;
    color: var(--nn-text);
    line-height: 1.35;
    margin-bottom: 4px;
}
.nn-card__desc {
    font-size: .77rem;
    color: var(--nn-text-muted);
    line-height: 1.45;
    margin-bottom: 8px;
    flex: 1;
}
.nn-card__chips {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    margin-bottom: 10px;
}
.nn-card__chip {
    background: var(--nn-primary-soft);
    color: var(--nn-text-muted);
    border-radius: 20px;
    padding: 2px 9px;
    font-size: .68rem;
    white-space: nowrap;
}
.nn-card__chip--skill {
    background: var(--cl, #f3f4f6);
    color: var(--c, #374151);
}
.nn-card__btn {
    display: block;
    width: 100%;
    padding: 9px;
    background: var(--c, #374151);
    color: #fff !important;
    border: none;
    border-radius: 11px;
    font-size: .83rem;
    font-weight: 700;
    text-align: center;
    text-decoration: none;
    cursor: pointer;
    transition: opacity .18s ease, transform .15s ease;
    margin-top: auto;
}
.nn-card__btn:hover { opacity: .85; transform: scale(1.01); }
.nn-card__btn--lock {
    background: #e5e7eb;
    color: #374151 !important;
}
/* Islam / Quran special theme */
.nn-card--islamic {
    background: linear-gradient(145deg, #f0fdf4 0%, #ecfdf5 100%);
}
.nn-card--islamic .nn-card__title { color: #065f46; }
.nn-card--islamic::before {
    content: '';
    position: absolute;
    top: 0; right: 0;
    width: 80px; height: 80px;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ccircle cx='50' cy='50' r='40' fill='none' stroke='%2310b981' stroke-width='1' stroke-dasharray='4 4'/%3E%3Ccircle cx='50' cy='50' r='25' fill='none' stroke='%2310b981' stroke-width='1'/%3E%3Cpolygon points='50,15 55,40 80,40 60,57 67,82 50,67 33,82 40,57 20,40 45,40' fill='none' stroke='%2310b981' stroke-width='0.8' opacity='0.4'/%3E%3C/svg%3E");
    background-size: contain;
    background-repeat: no-repeat;
    opacity: .08;
    pointer-events: none;
    z-index: 0;
}
/* Lock overlay */
.nn-card--locked { opacity: .88; }
.nn-card__lock-overlay {
    position: absolute;
    inset: 0;
    background: rgba(30,27,75,.48);
    backdrop-filter: blur(3px);
    border-radius: var(--nn-radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
}
.nn-card__lock-icon {
    font-size: 2rem;
    margin-bottom: 4px;
}
/* ── Responsive tightening ───────────────────────────── */
@media (max-width: 576px) {
    .nn-card__emoji { font-size: 1.9rem; }
    .nn-card__title { font-size: .86rem; }
}
</style>
@endpush
@endonce
