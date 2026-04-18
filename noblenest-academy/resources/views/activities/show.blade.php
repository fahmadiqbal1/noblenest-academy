@extends(isset($child) && $child ? 'layouts.child' : 'layouts.app')

@section('content')
<div class="container py-5" style="max-width:900px;">

    {{-- Context-aware back nav --}}
    @if(isset($child) && $child)
    <a href="{{ route('child.activities', $child) }}" class="nn-btn nn-btn-reset mb-4">
        <i class="bi bi-arrow-left"></i> {{ $child->name }}'s Activities
    </a>
    @else
    <a href="{{ route('activities.index') }}" class="nn-btn nn-btn-reset mb-4">
        <i class="bi bi-arrow-left"></i> Browse Curriculum
    </a>
    @endif

    {{-- Hero card with gradient banner --}}
    @php
        $subjectColor = match($activity->subject ?? '') {
            'islamic' => '#7C3AED', 'art' => '#F43F5E', 'language' => '#3B82F6',
            'stem' => '#10B981', 'stories' => '#F59E0B', 'motor' => '#EC4899',
            'coding' => '#6366F1', 'math' => '#EF4444', 'science' => '#10B981',
            'quran' => '#059669', 'arabic' => '#7C3AED', 'etiquette' => '#A855F7',
            'literacy' => '#10B981', 'social' => '#06B6D4', 'sensory' => '#8B5CF6',
            default => '#A78BFA',
        };
    @endphp
    <div class="nn-show-hero mb-4">
        {{-- Gradient banner --}}
        <div class="nn-show-banner" style="background:linear-gradient(135deg, {{ $subjectColor }}22, {{ $subjectColor }}08);">
            @if($activity->thumbnail_url)
            <img src="{{ $activity->thumbnail_url }}" alt="{{ $activity->title }}"
                 class="w-100 rounded-3 mb-3" style="max-height:360px; object-fit:cover;">
            @endif
            <div class="nn-show-emoji mb-2">{{ $activity->emoji ?? '🎯' }}</div>
            <h1 class="nn-show-title">{{ $activity->title }}</h1>
        </div>

        <div class="p-4 pt-2">
            {{-- Badge row --}}
            <div class="nn-show-badges mb-3">
                @if($activity->subject)
                <span class="nn-show-badge" style="background:{{ $subjectColor }}12;color:{{ $subjectColor }};border-color:{{ $subjectColor }}30;">
                    {{ ucfirst($activity->subject) }}
                </span>
                @endif
                @if($activity->difficulty)
                <span class="nn-show-badge" style="background:#FEF3C7;color:#92400E;border-color:#FDE68A;">
                    {{ ucfirst($activity->difficulty) }}
                </span>
                @endif
                @if($activity->duration_minutes)
                <span class="nn-show-badge" style="background:rgba(124,58,237,0.08);color:#7C3AED;border-color:rgba(124,58,237,0.15);">
                    ⏱️ {{ $activity->duration_minutes }} min
                </span>
                @endif
                @if(isset($activity->age_min, $activity->age_max))
                <span class="nn-show-badge" style="background:rgba(16,185,129,0.08);color:#059669;border-color:rgba(16,185,129,0.15);">
                    🎂 Age {{ $activity->age_min }}–{{ $activity->age_max }}
                </span>
                @endif
                @if($activity->is_free)
                <span class="nn-show-badge" style="background:rgba(16,185,129,0.12);color:#059669;border-color:#10B981;">🎁 Free</span>
                @else
                <span class="nn-show-badge" style="background:rgba(124,58,237,0.12);color:#7C3AED;border-color:#7C3AED;">⭐ Premium</span>
                @endif
            </div>

            @if($activity->description)
            <p class="lead" style="color:#6B7280; font-family:'Comic Neue',sans-serif; font-weight:700;">{{ $activity->description }}</p>
            @endif

            {{-- Benefit Explanation --}}
            @if($activity->benefit_explanation)
            <div class="nn-benefit-box mb-3">
                <h6 class="fw-bold mb-1" style="color:#059669;">💡 Why This Activity Matters</h6>
                <p class="mb-0 small" style="color:#6B7280;">{{ $activity->benefit_explanation }}</p>
            </div>
            @endif

            {{-- Skills Improved --}}
            @if($activity->skills_improved && count($activity->skills_improved))
            <div class="d-flex flex-wrap gap-2 mb-3">
                <span class="small fw-bold me-1" style="color:#6B7280;"><i class="bi bi-stars"></i> Skills:</span>
                @foreach($activity->skills_improved as $skill)
                <span class="nn-skill-chip">{{ ucwords(str_replace('_', ' ', $skill)) }}</span>
                @endforeach
            </div>
            @endif

            {{-- Learning Modality --}}
            @if($activity->primary_modality)
            @php
                $modalityIcon = match($activity->primary_modality) {
                    'visual' => '👁️', 'auditory' => '👂', 'kinesthetic' => '🤲', 'reading' => '📖', default => '📚'
                };
            @endphp
            <span class="nn-show-badge mb-2" style="background:rgba(236,72,153,0.08);color:#DB2777;border-color:rgba(236,72,153,0.15);">
                {{ $modalityIcon }} {{ ucfirst($activity->primary_modality) }} Learning
            </span>
            @endif
        </div>
    </div>

    {{-- Audio Player --}}
    @if($activity->audio_url)
    <div class="nn-content-card mb-4">
        <div class="card-body">
            <h5 class="fw-bold mb-3"><span class="nn-card-header-icon">🎧</span> Listen & Learn</h5>
            <audio controls class="w-100" preload="metadata">
                <source src="{{ $activity->audio_url }}" type="audio/mpeg">
                Your browser does not support audio playback.
            </audio>
        </div>
    </div>
    @endif

    {{-- Video Player --}}
    @if($activity->video_url)
    <div class="nn-content-card mb-4">
        <div class="card-body">
            <h5 class="fw-bold mb-3"><span class="nn-card-header-icon">🎬</span> Watch & Discover</h5>
            <video controls class="w-100 rounded-3" preload="metadata" style="max-height:420px;">
                <source src="{{ $activity->video_url }}" type="video/mp4">
                Your browser does not support video playback.
            </video>
        </div>
    </div>
    @endif

    {{-- Media URL (fallback generic media) --}}
    @if($activity->media_url && !$activity->video_url)
        @php $ext = strtolower(pathinfo($activity->media_url, PATHINFO_EXTENSION)); @endphp
        @if(in_array($ext, ['mp4','webm','mov']))
        <div class="nn-content-card mb-4">
            <div class="card-body">
                <video controls class="w-100 rounded-3" preload="metadata">
                    <source src="{{ $activity->media_url }}">
                </video>
            </div>
        </div>
        @elseif(in_array($ext, ['mp3','wav','ogg']))
        <div class="nn-content-card mb-4">
            <div class="card-body">
                <audio controls class="w-100" preload="metadata">
                    <source src="{{ $activity->media_url }}">
                </audio>
            </div>
        </div>
        @elseif(in_array($ext, ['jpg','jpeg','png','gif','webp','svg']))
        <div class="nn-content-card mb-3">
            <img src="{{ $activity->media_url }}" alt="{{ $activity->title }}" class="w-100 rounded-3" style="max-height:400px; object-fit:cover;">
        </div>
        @endif
    @endif

    {{-- Instructions --}}
    @if($activity->instructions)
    <div class="nn-content-card mb-4">
        <div class="card-body">
            <h5 class="fw-bold mb-3"><span class="nn-card-header-icon">📋</span> What To Do</h5>
            @if(is_array($activity->instructions))
                <ol class="nn-ordered-steps mb-0">
                    @foreach($activity->instructions as $step)
                    <li class="mb-2">{{ $step }}</li>
                    @endforeach
                </ol>
            @else
                <p class="mb-0" style="font-weight:600;color:#374151;line-height:1.75;">{{ $activity->instructions }}</p>
            @endif
        </div>
    </div>
    @endif

    {{-- Step Player (animated guided walkthrough — always show when steps exist) --}}
    @if($activity->steps && $activity->steps->count())
    <div class="nn-content-card mb-4">
        <div class="card-body">
            <h5 class="fw-bold mb-3"><span class="nn-card-header-icon">🎬</span> Guided Walkthrough</h5>
            <x-step-player
                :steps="$activity->steps"
                :subject="$activity->subject ?? 'default'"
                :activityEmoji="$activity->emoji ?? '🎯'"
            />
        </div>
    </div>
    @endif

    {{-- Materials Needed --}}
    @if(is_array($activity->materials_needed) && count((array)$activity->materials_needed))
    <div class="nn-content-card mb-4">
        <div class="card-body">
            <h5 class="fw-bold mb-3"><span class="nn-card-header-icon">🎒</span> Gather Your Supplies</h5>
            <ul class="nn-list-playful nn-list-materials">
                @foreach($activity->materials_needed as $material)
                <li>{{ $material }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    {{-- Learning Objectives --}}
    @if(is_array($activity->learning_objectives) && count((array)$activity->learning_objectives))
    <div class="nn-content-card mb-4">
        <div class="card-body">
            <h5 class="fw-bold mb-3"><span class="nn-card-header-icon">🏆</span> What You'll Master</h5>
            <ul class="nn-list-playful nn-list-objectives">
                @foreach($activity->learning_objectives as $obj)
                <li>{{ $obj }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    {{-- Start Activity / Completion Zone --}}
    @php $childQuery = isset($child) && $child ? '?child=' . $child->id : ''; @endphp
    <div class="nn-completion-zone mb-5">
        {{-- Activity type CTA --}}
        @php $actType = $activity->activity_type ?? ''; @endphp
        @if($actType === 'tracing')
            <a href="{{ route('activities.tracing', $activity) . $childQuery }}"
               class="nn-cta-btn nn-cta-primary w-100 justify-content-center">
                <i class="bi bi-pencil-fill"></i> Start Tracing ✏️
            </a>
        @elseif($actType === 'drawing')
            <a href="{{ route('activities.drawing', $activity) . $childQuery }}"
               class="nn-cta-btn nn-cta-primary w-100 justify-content-center">
                <i class="bi bi-palette-fill"></i> Start Drawing 🎨
            </a>
        @elseif($actType === 'puzzle')
            <a href="{{ route('activities.puzzle', $activity) . $childQuery }}"
               class="nn-cta-btn nn-cta-primary w-100 justify-content-center">
                <i class="bi bi-puzzle-fill"></i> Start Puzzle 🧩
            </a>
        @elseif($actType === 'quiz' && ($activity->quiz_id ?? false))
            <a href="{{ route('quizzes.show', $activity->quiz_id) . $childQuery }}"
               class="nn-cta-btn nn-cta-primary w-100 justify-content-center">
                <i class="bi bi-question-circle-fill"></i> Take Quiz 🧠
            </a>
        @elseif($actType === 'video' || $activity->video_url || (isset($activity->media_url) && str_ends_with((string)$activity->media_url, '.mp4')))
            <a href="{{ route('activities.video', $activity) . $childQuery }}"
               class="nn-cta-btn w-100 justify-content-center"
               style="background:linear-gradient(135deg,#1E40AF,#3B82F6);border-color:#1E40AF;">
                <i class="bi bi-play-circle-fill"></i> Watch Video 🎬
            </a>
        @elseif($actType === 'slides' || $actType === 'simulation')
            <a href="{{ route('activities.slides', $activity) . $childQuery }}"
               class="nn-cta-btn w-100 justify-content-center"
               style="background:linear-gradient(135deg,#0E7490,#06B6D4);border-color:#0E7490;">
                <i class="bi bi-collection-play-fill"></i> Start Lesson Slides 📖
            </a>
        @elseif($activity->steps && $activity->steps->count() > 0)
            {{-- Activities with steps: offer a slides view --}}
            <a href="{{ route('activities.slides', $activity) . $childQuery }}"
               class="nn-cta-btn w-100 justify-content-center mb-3"
               style="background:linear-gradient(135deg,#7C3AED,#A78BFA);border-color:#7C3AED;">
                <i class="bi bi-collection-play-fill"></i> Interactive Lesson View 🎯
            </a>
            <div class="nn-ready-card">
                <div class="nn-ready-icon">🌟</div>
                <h4 class="nn-ready-title">Or Work Through the Steps Above</h4>
                <p class="nn-ready-text">Read each step, then come back when you're done!</p>
            </div>
        @else
            <div class="nn-ready-card">
                <div class="nn-ready-icon">🌟</div>
                <h4 class="nn-ready-title">You're Ready to Begin!</h4>
                <p class="nn-ready-text">Work through the steps above and come back when you're done.</p>
            </div>
        @endif

        {{-- Mark Complete --}}
        @if(isset($child) && $child)
        <form action="{{ route('child.activity.complete', [$child, $activity]) }}" method="POST" class="mt-4" id="completeForm">
            @csrf
            <button type="submit" class="nn-complete-zone-btn" id="completeBtn">
                <span class="nn-complete-zone-icon">🎉</span>
                <span class="nn-complete-zone-label">
                    <strong>I Finished This Activity!</strong>
                    <small>Mark complete &amp; earn your badge</small>
                </span>
                <i class="bi bi-check-circle-fill ms-auto"></i>
            </button>
        </form>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animate complete button on click
    const btn = document.getElementById('completeBtn');
    if (btn) {
        btn.addEventListener('click', function() {
            btn.style.transform = 'scale(0.96)';
            btn.style.opacity = '0.85';
        });
    }
});
</script>
@endsection
