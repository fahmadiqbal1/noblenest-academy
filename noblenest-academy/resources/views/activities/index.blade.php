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
        @php
        $curriculum = [
            [
                'age' => '0–1 year',
                'skills' => [
                    ['name' => 'Sensory & Motor Development', 'activities' => ['Guided tummy‑time videos', 'Soothing songs (multi-language)', 'Baby sign language sessions'], 'objective' => 'Develop sensory awareness and basic motor skills.'],
                    ['name' => 'Parent–Infant Bonding', 'activities' => ['Parent modules on reading cues', 'Gentle lullabies (EN, Mandarin, Spanish)'], 'objective' => 'Strengthen emotional connection and trust.'],
                    ['name' => 'Early Language Exposure', 'activities' => ['Soothing songs (multi-language)', 'Baby sign language sessions'], 'objective' => 'Familiarize infants with sounds and rhythm of language.'],
                ],
            ],
            [
                'age' => '1–2 years',
                'skills' => [
                    ['name' => 'Language & Mobility', 'activities' => ['Interactive videos: first words', 'Tracing simple shapes/lines'], 'objective' => 'Encourage first words and movement.'],
                    ['name' => 'Imitation', 'activities' => ['Music‑and‑movement games'], 'objective' => 'Develop imitation and coordination.'],
                    ['name' => 'Routine Awareness', 'activities' => ['Social play tips for parents'], 'objective' => 'Build awareness of daily routines and social rules.'],
                ],
            ],
            [
                'age' => '2–3 years',
                'skills' => [
                    ['name' => 'Basic Etiquette', 'activities' => [
                        'Polite Greetings for Toddlers',
                        'Polite Words Tracing',
                        'Polite Greetings Quiz',
                    ], 'objective' => 'Introduce polite greetings and basic etiquette in multiple languages.'],
                    ['name' => 'Vocabulary Expansion', 'activities' => ['Storytelling sessions', 'Songs/dances (Japan, China, Scandinavia)'], 'objective' => 'Grow vocabulary and cultural awareness.'],
                    ['name' => 'Fine Motor Skills', 'activities' => ['Tracing numbers/letters', 'Simple art projects'], 'objective' => 'Improve hand control and creativity.'],
                ],
            ],
            [
                'age' => '3–4 years',
                'skills' => [
                    ['name' => 'Manners', 'activities' => [
                        'Table Manners Role-play',
                        'Table Setting Puzzle',
                        'Manners Matching Game',
                    ], 'objective' => 'Practice table manners and polite responses in daily life.'],
                    ['name' => 'Early Literacy', 'activities' => ['Tracing alphabets (multi-script)'], 'objective' => 'Lay foundation for reading and writing.'],
                    ['name' => 'Numeracy', 'activities' => ['Basic counting games'], 'objective' => 'Develop number sense.'],
                    ['name' => 'Emotional Expression', 'activities' => ['Parent modules: emotional coaching'], 'objective' => 'Help children name and manage feelings.'],
                ],
            ],
            [
                'age' => '4���5 years',
                'skills' => [
                    ['name' => 'Chivalry & Etiquette', 'activities' => [
                        'Chivalry and Kindness',
                        'Chivalry Choices Quiz',
                        'Kindness Drawing',
                    ], 'objective' => 'Learn chivalrous actions, kindness, and respect for elders.'],
                    ['name' => 'Multilingual Literacy', 'activities' => ['Tracing complex letters', 'Simple words/phrases (FR, RU, KO)'], 'objective' => 'Expand literacy in multiple languages.'],
                    ['name' => 'Problem Solving', 'activities' => ['Puzzles and sorting games'], 'objective' => 'Boost logical thinking.'],
                    ['name' => 'Mindfulness', 'activities' => ['Basic mindfulness exercises'], 'objective' => 'Foster self-regulation and calm.'],
                ],
            ],
            [
                'age' => '5–6 years',
                'skills' => [
                    ['name' => 'Royal Etiquette', 'activities' => [
                        'Royal Etiquette for Kids',
                        'Royal Table Setting Puzzle',
                        'Royal Etiquette Quiz',
                        'Royal Bow Drawing',
                    ], 'objective' => 'Master formal dining, royal courtesy, and sportsmanship.'],
                    ['name' => 'Advanced Etiquette', 'activities' => [
                        'Global Etiquette Quiz',
                        'Formal Invitations Puzzle',
                        'Diplomatic Greetings Video',
                        'Advanced Table Manners Drawing',
                    ], 'objective' => 'Explore etiquette for international, formal, and multicultural settings.'],
                    ['name' => 'Pre‑Academic Skills', 'activities' => ['Storytelling, science experiments, arithmetic'], 'objective' => 'Prepare for school academics.'],
                    ['name' => 'Creativity', 'activities' => ['Art styles: origami, calligraphy, weaving'], 'objective' => 'Encourage creative expression.'],
                    ['name' => 'Moral Reasoning', 'activities' => ['Etiquette: dining, courtesy, sportsmanship'], 'objective' => 'Instill values and fair play.'],
                    ['name' => 'Cultural Activities', 'activities' => ['Family tree, cultural scrapbook'], 'objective' => 'Celebrate heritage and diversity.'],
                ],
            ],
            [
                'age' => '7–8 years',
                'skills' => [
                    ['name' => 'Robotics & Coding Basics', 'activities' => ['Block-based programming', 'DIY robot kits'], 'objective' => 'Spark interest in technology.'],
                    ['name' => 'Science Exploration', 'activities' => ['Videos: mechanical concepts', 'Plant/insect experiments'], 'objective' => 'Promote scientific curiosity.'],
                ],
            ],
            [
                'age' => '8–9 years',
                'skills' => [
                    ['name' => 'Intermediate Coding', 'activities' => ['Python/JS game challenges'], 'objective' => 'Advance programming skills.'],
                    ['name' => 'Engineering Design', 'activities' => ['3D printing simple objects'], 'objective' => 'Develop design thinking.'],
                    ['name' => 'Math Exploration', 'activities' => ['Fractions/geometry puzzles'], 'objective' => 'Deepen math understanding.'],
                    ['name' => 'Collaborative Coding', 'activities' => ['Team coding projects'], 'objective' => 'Build teamwork and coding skills.'],
                ],
            ],
            [
                'age' => '9–10 years',
                'skills' => [
                    ['name' => 'Advanced STEM Projects', 'activities' => ['Programmable robots with sensors'], 'objective' => 'Apply STEM knowledge to real-world tasks.'],
                    ['name' => 'Data & AI Awareness', 'activities' => ['AI: image recognition, chatbots'], 'objective' => 'Introduce AI concepts.'],
                    ['name' => 'Problem Solving', 'activities' => ['Environmental science projects', 'Statistics/probability modules'], 'objective' => 'Sharpen analytical skills.'],
                ],
            ],
        ];

        $activityLinks = [
            // 0–1 year
            'Guided tummy‑time videos' => url('/activities/1/video'),
            'Soothing songs (multi-language)' => url('/activities/2/video'),
            'Baby sign language sessions' => url('/activities/3/video'),
            'Parent modules on reading cues' => url('/activities/4/video'),
            'Gentle lullabies (EN, Mandarin, Spanish)' => url('/activities/5/video'),
            // 1–2 years
            'Interactive videos: first words' => url('/activities/6/video'),
            'Tracing simple shapes/lines' => url('/activities/7/tracing'),
            'Music‑and‑movement games' => url('/activities/8/video'),
            'Social play tips for parents' => url('/activities/9/video'),
            // 2–3 years
            'Storytelling sessions' => url('/activities/10/video'),
            'Songs/dances (Japan, China, Scandinavia)' => url('/activities/11/video'),
            'Tracing numbers/letters' => url('/activities/12/tracing'),
            'Simple art projects' => url('/activities/13/drawing'),
            'Videos: polite greetings' => url('/activities/14/video'),
            // 3–4 years
            'Tracing alphabets (multi-script)' => url('/activities/15/tracing'),
            'Basic counting games' => url('/activities/16/puzzle'),
            'Parent modules: emotional coaching' => url('/activities/17/video'),
            'Role-play: table manners, sharing' => url('/activities/18/video'),
            // 4–5 years
            'Tracing complex letters' => url('/activities/19/tracing'),
            'Simple words/phrases (FR, RU, KO)' => url('/activities/20/video'),
            'Puzzles and sorting games' => url('/activities/21/puzzle'),
            'Animated scenarios: kindness, respect' => url('/activities/22/video'),
            'Basic mindfulness exercises' => url('/activities/23/video'),
            // 5–6 years
            'Storytelling, science experiments, arithmetic' => url('/activities/24/video'),
            'Art styles: origami, calligraphy, weaving' => url('/activities/25/drawing'),
            'Etiquette: dining, courtesy, sportsmanship' => url('/activities/26/video'),
            'Family tree, cultural scrapbook' => url('/activities/27/drawing'),
            'Royal Etiquette for Kids' => url('/activities/43/video'),
            'Royal Table Setting Puzzle' => url('/activities/50/puzzle'),
            'Royal Etiquette Quiz' => url('/activities/51/quiz'),
            'Royal Bow Drawing' => url('/activities/52/drawing'),
            // Etiquette, Manners, Chivalry, Royal, Advanced
            'Polite Greetings for Toddlers' => url('/activities/40/video'),
            'Polite Words Tracing' => url('/activities/44/tracing'),
            'Polite Greetings Quiz' => url('/activities/45/quiz'),
            'Table Manners Role-play' => url('/activities/41/video'),
            'Table Setting Puzzle' => url('/activities/46/puzzle'),
            'Manners Matching Game' => url('/activities/47/quiz'),
            'Chivalry and Kindness' => url('/activities/42/video'),
            'Chivalry Choices Quiz' => url('/activities/48/quiz'),
            'Kindness Drawing' => url('/activities/49/drawing'),
            // Advanced Etiquette
            'Global Etiquette Quiz' => url('/activities/53/quiz'),
            'Formal Invitations Puzzle' => url('/activities/54/puzzle'),
            'Diplomatic Greetings Video' => url('/activities/55/video'),
            'Advanced Table Manners Drawing' => url('/activities/56/drawing'),
            // 7–8 years
            'Block-based programming' => url('/activities/28/video'),
            'DIY robot kits' => url('/activities/29/video'),
            'Videos: mechanical concepts' => url('/activities/30/video'),
            'Plant/insect experiments' => url('/activities/31/video'),
            // 8–9 years
            'Python/JS game challenges' => url('/activities/32/quiz'),
            '3D printing simple objects' => url('/activities/33/video'),
            'Fractions/geometry puzzles' => url('/activities/34/puzzle'),
            'Team coding projects' => url('/activities/35/video'),
            // 9–10 years
            'Programmable robots with sensors' => url('/activities/36/video'),
            'AI: image recognition, chatbots' => url('/activities/37/video'),
            'Environmental science projects' => url('/activities/38/video'),
            'Statistics/probability modules' => url('/activities/39/puzzle'),
        ];
        @endphp
        @foreach($curriculum as $i => $stage)
        <div class="accordion-item mb-3 shadow-sm rounded-3">
            <h2 class="accordion-header" id="heading{{ $i }}">
                <button class="accordion-button fw-bold fs-5 {{ $i ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $i }}" aria-expanded="{{ $i ? 'false' : 'true' }}" aria-controls="collapse{{ $i }}">
                    <span class="badge bg-info me-2" style="font-size:1rem;">{{ $stage['age'] }}</span>
                    <span>{{ $stage['skills'][0]['name'] }} & more</span>
                </button>
            </h2>
            <div id="collapse{{ $i }}" class="accordion-collapse collapse {{ $i ? '' : 'show' }}" aria-labelledby="heading{{ $i }}" data-bs-parent="#curriculumAccordion">
                <div class="accordion-body bg-light rounded-bottom-3">
                    <div class="row g-4">
                        @foreach($stage['skills'] as $skill)
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 border-0 shadow-sm curriculum-skill-card">
                                <div class="card-body">
                                    <h5 class="card-title text-primary fw-bold mb-2"><i class="bi bi-lightbulb"></i> {{ $skill['name'] }}</h5>
                                    <p class="mb-1"><span class="fw-semibold text-secondary">Objective:</span> {{ $skill['objective'] }}</p>
                                    <ul class="list-unstyled mb-2">
                                        @foreach($skill['activities'] as $activity)
                                        <li>
                                            <i class="bi bi-check-circle text-success me-1"></i>
                                            <a href="{{ $activityLinks[$activity] ?? '#' }}" class="text-decoration-none" target="_blank">{{ $activity }}</a>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="card-footer bg-white border-0 text-end">
                                    @php $firstActivity = $skill['activities'][0]; @endphp
                                    <a href="{{ $activityLinks[$firstActivity] ?? '#' }}" class="btn btn-outline-primary btn-sm" target="_blank">View Interactive Material</a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-5 text-center">
        <a href="/profile" class="nn-btn nn-btn-reset"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
    </div>
</div>
@endsection
