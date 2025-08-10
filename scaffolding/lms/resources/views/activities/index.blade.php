@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h1 class="mb-4 text-center text-primary fw-bold" style="font-size:2.5rem;letter-spacing:1px;">Curriculum Explorer</h1>
    <p class="lead text-center mb-5">Browse our interactive curriculum by age, skill, and activity. Click a skill to see suggested activities and objectives!</p>
    <div class="accordion" id="curriculumAccordion">
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
        <a href="/profile" class="btn btn-lg btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
    </div>
</div>
<style>
.curriculum-skill-card {
    transition: box-shadow 0.2s, transform 0.2s;
}
.curriculum-skill-card:hover {
    box-shadow: 0 8px 32px rgba(44,62,80,0.12);
    transform: translateY(-4px) scale(1.03);
}
</style>
@endsection
