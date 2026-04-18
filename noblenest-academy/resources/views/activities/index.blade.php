@extends('layouts.app')

@section('content')
@php
    $subjectEmojis = [
        'arabic' => '🕌', 'art' => '🎨', 'character' => '💪', 'coding' => '💻',
        'cognitive' => '🧠', 'creative' => '✨', 'cultural' => '🌍', 'engineering' => '⚙️',
        'etiquette' => '🎩', 'geography' => '🗺️', 'history' => '📜', 'islamic_studies' => '☪️',
        'language' => '🗣️', 'literacy' => '📚', 'math' => '🔢', 'motor' => '🏃',
        'numeracy' => '🔟', 'quran' => '📖', 'robotics' => '🤖', 'routine' => '⏰',
        'science' => '🔬', 'sensory' => '👐', 'social' => '🤝', 'stem' => '🧪', 'technology' => '📱',
    ];
    $subjectColors = [
        'arabic' => '#7C3AED', 'art' => '#F43F5E', 'character' => '#8B5CF6', 'coding' => '#6366F1',
        'cognitive' => '#3B82F6', 'creative' => '#EC4899', 'cultural' => '#F59E0B', 'engineering' => '#64748B',
        'etiquette' => '#A855F7', 'geography' => '#14B8A6', 'history' => '#D97706', 'islamic_studies' => '#7C3AED',
        'language' => '#3B82F6', 'literacy' => '#10B981', 'math' => '#EF4444', 'motor' => '#EC4899',
        'numeracy' => '#F97316', 'quran' => '#059669', 'robotics' => '#6366F1', 'routine' => '#F59E0B',
        'science' => '#10B981', 'sensory' => '#8B5CF6', 'social' => '#06B6D4', 'stem' => '#10B981', 'technology' => '#3B82F6',
    ];
    $ageEmojis = ['0–1 year' => '👶', '1–2 years' => '🍼', '2–3 years' => '🧸', '3–4 years' => '🎈', '4–5 years' => '🦋', '5–6 years' => '🌟', '7–8 years' => '🚀', '8–9 years' => '💡', '9–10 years' => '🏆'];
    $ageColors = ['0–1 year' => '#EC4899', '1–2 years' => '#F97316', '2–3 years' => '#F59E0B', '3–4 years' => '#10B981', '4–5 years' => '#3B82F6', '5–6 years' => '#8B5CF6', '7–8 years' => '#7C3AED', '8–9 years' => '#6366F1', '9–10 years' => '#059669'];
    $funCtas = ['Let\'s Go! 🚀', 'Play Now! 🎮', 'Explore! 🔍', 'Let\'s Learn! 📚', 'Start Fun! 🎉', 'Jump In! 🐸', 'Ready? Go! ⚡'];
@endphp

<div class="container py-4">

    {{-- Playful Hero Header --}}
    <div class="text-center mb-5 nn-hero-bounce">
        <div class="nn-hero-emoji">🎓</div>
        <h1 class="nn-playful-title">Hey Explorer! <span class="nn-wave">👋</span></h1>
        <p class="nn-playful-subtitle">What adventure shall we go on today?</p>
        <div class="nn-floating-shapes">
            <span class="nn-shape nn-shape-1">⭐</span>
            <span class="nn-shape nn-shape-2">🌈</span>
            <span class="nn-shape nn-shape-3">✨</span>
            <span class="nn-shape nn-shape-4">🎯</span>
        </div>
    </div>

    {{-- Dynamic Activity Library --}}
    @if($activities->count())
    <div class="mb-5">
        <h2 class="nn-section-title"><span class="nn-section-emoji">🗃️</span> Activity Treasure Chest</h2>

        {{-- Playful Filter Bar --}}
        <form method="GET" action="{{ route('activities.index') }}" class="nn-filter-bar mb-4">
            <div class="nn-filter-group">
                <label class="nn-filter-label">🎂 Age</label>
                <input type="number" name="age" class="nn-filter-input" placeholder="years" value="{{ request('age') }}" min="0" max="12">
            </div>
            <div class="nn-filter-group nn-filter-subjects">
                <label class="nn-filter-label">🎯 Subject</label>
                <div class="nn-subject-pills">
                    <a href="{{ route('activities.index', array_merge(request()->except('subject','page'), [])) }}"
                       class="nn-pill {{ !request('subject') ? 'nn-pill-active' : '' }}">🌈 All</a>
                    @foreach($skills as $s)
                    <a href="{{ route('activities.index', array_merge(request()->except('page'), ['subject' => $s])) }}"
                       class="nn-pill {{ request('subject')===$s ? 'nn-pill-active' : '' }}"
                       style="{{ request('subject')===$s ? 'background:'.($subjectColors[$s] ?? '#7C3AED').';color:#fff;border-color:'.($subjectColors[$s] ?? '#7C3AED') : '' }}">
                        {{ $subjectEmojis[$s] ?? '📌' }} {{ ucfirst(str_replace('_', ' ', $s)) }}
                    </a>
                    @endforeach
                </div>
            </div>
            <div class="nn-filter-group">
                <label class="nn-filter-label">⏱️ Max Time</label>
                <input type="number" name="duration_minutes" class="nn-filter-input" placeholder="min" value="{{ request('duration_minutes') }}" min="1">
            </div>
            <div class="nn-filter-actions">
                <button type="submit" class="nn-btn nn-btn-filter">🔍 Find Adventures</button>
                <a href="{{ route('activities.index') }}" class="nn-btn nn-btn-reset">🔄 Fresh Start</a>
            </div>
        </form>

        {{-- Playful Activity Cards --}}
        <div class="row g-4">
            @foreach($activities as $idx => $act)
            @php
                $color = $subjectColors[$act->subject] ?? '#A78BFA';
                $emoji = $subjectEmojis[$act->subject] ?? ($act->emoji ?? '🎯');
                $cta = $funCtas[$idx % count($funCtas)];
            @endphp
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('activities.show', $act) }}" class="text-decoration-none">
                    <div class="nn-activity-card" style="--card-color: {{ $color }};">
                        <div class="nn-card-ribbon" style="background: {{ $color }};"></div>
                        <div class="nn-card-emoji">{{ $emoji }}</div>
                        <h5 class="nn-card-title">{{ $act->title }}</h5>
                        @if($act->description)
                        <p class="nn-card-desc">{{ Str::limit($act->description, 80) }}</p>
                        @endif
                        <div class="nn-card-badges">
                            @if($act->subject)
                            <span class="nn-badge" style="background: {{ $color }}15; color: {{ $color }}; border-color: {{ $color }}30;">
                                {{ $subjectEmojis[$act->subject] ?? '' }} {{ ucfirst(str_replace('_', ' ', $act->subject)) }}
                            </span>
                            @endif
                            @if($act->duration_minutes)
                            <span class="nn-badge nn-badge-time">⏱️ {{ $act->duration_minutes }}m</span>
                            @endif
                            @if(isset($act->age_min, $act->age_max))
                            <span class="nn-badge nn-badge-age">🎂 {{ $act->age_min }}–{{ $act->age_max }}y</span>
                            @endif
                            @if($act->is_free)
                            <span class="nn-badge nn-badge-free">🎁 Free</span>
                            @endif
                        </div>
                        <div class="nn-card-cta" style="background: {{ $color }};">{{ $cta }}</div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $activities->withQueryString()->links() }}</div>
    </div>
    @else
    <div class="text-center py-5 mb-5">
        <div style="font-size:3rem;margin-bottom:1rem;">🔍</div>
        <h4 class="fw-bold" style="color:var(--nn-text,#1F2937);font-family:'Baloo 2',sans-serif;">No activities found</h4>
        <p class="mb-0" style="color:var(--nn-text-muted,#6B7280);font-family:'Comic Neue',sans-serif;">Try adjusting your filters or age range.</p>
    </div>
    @endif

    {{-- Curriculum Roadmap --}}
    <h2 class="nn-section-title"><span class="nn-section-emoji">🗺️</span> Learning Roadmap</h2>
    <p class="text-center text-muted mb-4" style="font-family:'Comic Neue',sans-serif;font-size:1.1rem;">Follow the path and unlock new skills at every age!</p>

    <div class="nn-roadmap" id="curriculumAccordion">
        @forelse($roadmap as $i => $ageActivities)
        <div class="accordion-item mb-3 shadow-sm rounded-3">
            <h2 class="accordion-header" id="heading{{ $loop->index }}">
                <button class="accordion-button fw-bold fs-5 {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $loop->index }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="collapse{{ $loop->index }}">
                    <span class="badge bg-info me-2" style="font-size:1rem;">{{ $i }}</span>
                    <span>{{ $ageActivities->first()->subject ? ucfirst($ageActivities->first()->subject) : 'Activities' }} & more</span>
                </button>
            </h2>
            <div id="collapse{{ $loop->index }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" aria-labelledby="heading{{ $loop->index }}" data-bs-parent="#curriculumAccordion">
                <div class="accordion-body bg-light rounded-bottom-3">
                    <div class="row g-4">
                        @foreach($ageActivities->groupBy('subject') as $subjectName => $subjectActivities)
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 border-0 shadow-sm curriculum-skill-card">
                                <div class="card-body">
                                    <h5 class="card-title text-primary fw-bold mb-2"><i class="bi bi-lightbulb"></i> {{ $subjectName ? ucfirst($subjectName) : 'General' }}</h5>
                                    <ul class="list-unstyled mb-2">
                                        @foreach($subjectActivities as $roadmapActivity)
                                        <li>
                                            <i class="bi bi-check-circle text-success me-1"></i>
                                            <a href="{{ route('activities.show', $roadmapActivity) }}" class="text-decoration-none">{{ $roadmapActivity->title }}</a>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="card-footer bg-white border-0 text-end">
                                    <a href="{{ route('activities.show', $subjectActivities->first()) }}" class="btn btn-outline-primary btn-sm">View Activity</a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-4">
            <p class="text-muted">No roadmap activities found yet.</p>
        </div>
        @endforelse
    </div>
    <div class="mt-5 text-center">
        <a href="/profile" class="nn-btn nn-btn-reset"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
    </div>
</div>
@endsection
