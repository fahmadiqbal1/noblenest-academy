@extends(isset($child) && $child ? 'layouts.child' : 'layouts.parent')

@section('title', $activity->title . ' — Noble Nest Academy')

@section('content')
@php
    $subjectColor = match($activity->subject ?? '') {
        'islamic', 'quran'    => '#059669',
        'arabic'              => '#7C3AED',
        'art'                 => '#EC4899',
        'language'            => '#3B82F6',
        'stem', 'science'     => '#10B981',
        'stories', 'literacy' => '#F59E0B',
        'motor'               => '#22C55E',
        'coding'              => '#6366F1',
        'math'                => '#EF4444',
        'etiquette'           => '#A855F7',
        'social'              => '#06B6D4',
        'sensory'             => '#8B5CF6',
        default               => '#7C3AED',
    };
    $childQuery = isset($child) && $child ? '?child=' . $child->id : '';
@endphp

<div class="max-w-2xl mx-auto">

    {{-- ── Back button ── --}}
    @if(isset($child) && $child)
        <x-ui.button variant="ghost" href="{{ route('child.activities', $child) }}" icon="arrow-left" size="sm" class="mb-5">
            {{ $child->name }}'s Activities
        </x-ui.button>
    @else
        <x-ui.button variant="ghost" href="{{ route('activities.index') }}" icon="arrow-left" size="sm" class="mb-5">
            Browse Curriculum
        </x-ui.button>
    @endif

    {{-- ── Activity hero ── --}}
    <x-ui.card variant="clay" padding="none" class="mb-5 overflow-hidden">
        {{-- Gradient banner --}}
        <div class="relative px-5 pt-5 pb-4"
             style="background: linear-gradient(135deg, color-mix(in oklab, {{ $subjectColor }}, white 85%), color-mix(in oklab, {{ $subjectColor }}, white 92%));">
            @if($activity->thumbnail_url)
                <img src="{{ $activity->thumbnail_url }}" alt="{{ $activity->title }}"
                     class="w-full rounded-[var(--radius-sm)] mb-4 max-h-72 object-cover" loading="lazy" decoding="async">
            @endif
            <div class="text-5xl mb-2 leading-none" aria-hidden="true">{{ $activity->emoji ?? '🎯' }}</div>
            <h1 class="font-display font-black text-2xl text-[var(--color-text)] leading-tight mb-3">{{ $activity->title }}</h1>

            {{-- Badge row --}}
            <div class="flex flex-wrap gap-2 mt-3">
                @if($activity->subject)
                    <x-ui.badge tone="neutral" class="text-white border-0" style="background: {{ $subjectColor }};">
                        {{ ucfirst($activity->subject) }}
                    </x-ui.badge>
                @endif
                @if($activity->difficulty)
                    <x-ui.badge tone="warning">{{ ucfirst($activity->difficulty) }}</x-ui.badge>
                @endif
                @if($activity->duration_minutes)
                    <x-ui.badge tone="brand">
                        <x-ui.icon name="clock" class="w-3 h-3" aria-hidden="true" />{{ $activity->duration_minutes }} min
                    </x-ui.badge>
                @endif
                @if(isset($activity->age_min, $activity->age_max))
                    <x-ui.badge tone="success">
                        🎂 Age {{ $activity->age_min }}–{{ $activity->age_max }}
                    </x-ui.badge>
                @endif
                @if($activity->is_free)
                    <x-ui.badge tone="success">🎁 Free</x-ui.badge>
                @else
                    <x-ui.badge tone="brand">⭐ Premium</x-ui.badge>
                @endif
            </div>
        </div>

        <div class="px-5 py-4 space-y-4">
            @if($activity->description)
                <p class="text-[var(--color-text-muted)] font-semibold leading-relaxed">{{ $activity->description }}</p>
            @endif

            {{-- Why this activity matters --}}
            @if($activity->benefit_explanation)
                <div class="rounded-[var(--radius-sm)] bg-emerald-50 border-[2px] border-emerald-200 p-3">
                    <h2 class="font-bold text-emerald-800 text-sm mb-1">💡 Why This Activity Matters</h2>
                    <p class="text-sm text-emerald-700">{{ $activity->benefit_explanation }}</p>
                </div>
            @endif

            {{-- Skills --}}
            @if($activity->skills_improved && count($activity->skills_improved))
                <div class="flex flex-wrap gap-1.5 items-center">
                    <span class="text-xs font-bold text-[var(--color-text-muted)]">
                        <x-ui.icon name="sparkles" class="w-3.5 h-3.5 inline me-0.5 text-[var(--color-primary)]" aria-hidden="true" />Skills:
                    </span>
                    @foreach($activity->skills_improved as $skill)
                        <x-ui.badge tone="brand" size="sm">{{ ucwords(str_replace('_', ' ', $skill)) }}</x-ui.badge>
                    @endforeach
                </div>
            @endif

            {{-- Learning modality --}}
            @if($activity->primary_modality)
            @php
                $modalityIcon = match($activity->primary_modality) {
                    'visual' => '👁️', 'auditory' => '👂', 'kinesthetic' => '🤲', 'reading' => '📖', default => '📚'
                };
            @endphp
                <x-ui.badge tone="info">
                    {{ $modalityIcon }} {{ ucfirst($activity->primary_modality) }} Learning
                </x-ui.badge>
            @endif
        </div>
    </x-ui.card>

    {{-- ── Audio player ── --}}
    @if($activity->audio_url)
    <x-ui.card variant="clay" padding="md" class="mb-4">
        <h2 class="font-bold text-[var(--color-text)] mb-3 flex items-center gap-2">
            <span aria-hidden="true">🎧</span> Listen &amp; Learn
        </h2>
        <audio controls class="w-full" preload="metadata">
            <source src="{{ $activity->audio_url }}" type="audio/mpeg">
            Your browser does not support audio playback.
        </audio>
    </x-ui.card>
    @endif

    {{-- ── Video player ── --}}
    @if($activity->video_url)
    <x-ui.card variant="clay" padding="md" class="mb-4">
        <h2 class="font-bold text-[var(--color-text)] mb-3 flex items-center gap-2">
            <span aria-hidden="true">🎬</span> Watch &amp; Discover
        </h2>
        <video controls class="w-full rounded-[var(--radius-sm)]" preload="metadata" style="max-height:420px;">
            <source src="{{ $activity->video_url }}" type="video/mp4">
            Your browser does not support video playback.
        </video>
    </x-ui.card>
    @endif

    {{-- ── Generic media fallback ── --}}
    @if($activity->media_url && !$activity->video_url)
        @php $ext = strtolower(pathinfo($activity->media_url, PATHINFO_EXTENSION)); @endphp
        @if(in_array($ext, ['mp4','webm','mov']))
        <x-ui.card variant="clay" padding="md" class="mb-4">
            <video controls class="w-full rounded-[var(--radius-sm)]" preload="metadata">
                <source src="{{ $activity->media_url }}">
            </video>
        </x-ui.card>
        @elseif(in_array($ext, ['mp3','wav','ogg']))
        <x-ui.card variant="clay" padding="md" class="mb-4">
            <audio controls class="w-full" preload="metadata">
                <source src="{{ $activity->media_url }}">
            </audio>
        </x-ui.card>
        @elseif(in_array($ext, ['jpg','jpeg','png','gif','webp','svg']))
        <x-ui.card variant="clay" padding="none" class="mb-4 overflow-hidden">
            <img src="{{ $activity->media_url }}" alt="{{ $activity->title }}"
                 class="w-full max-h-96 object-cover" loading="lazy" decoding="async">
        </x-ui.card>
        @endif
    @endif

    {{-- ── Instructions ── --}}
    @if($activity->instructions)
    <x-ui.card variant="clay" padding="md" class="mb-4">
        <h2 class="font-bold text-[var(--color-text)] mb-3 flex items-center gap-2">
            <span aria-hidden="true">📋</span> What To Do
        </h2>
        @if(is_array($activity->instructions))
            <ol class="space-y-2 list-none ps-0">
                @foreach($activity->instructions as $idx => $step)
                <li class="flex items-start gap-3">
                    <span class="shrink-0 w-6 h-6 rounded-full flex items-center justify-center text-xs font-black text-white"
                          style="background: {{ $subjectColor }};">{{ $idx + 1 }}</span>
                    <span class="text-[var(--color-text)] font-medium leading-relaxed">{{ $step }}</span>
                </li>
                @endforeach
            </ol>
        @else
            <p class="text-[var(--color-text)] font-medium leading-relaxed">{{ $activity->instructions }}</p>
        @endif
    </x-ui.card>
    @endif

    {{-- ── Step player ── --}}
    @if($activity->steps && $activity->steps->count())
    <x-ui.card variant="clay" padding="md" class="mb-4">
        <h2 class="font-bold text-[var(--color-text)] mb-3 flex items-center gap-2">
            <span aria-hidden="true">🎬</span> Guided Walkthrough
        </h2>
        <x-step-player
            :steps="$activity->steps"
            :subject="$activity->subject ?? 'default'"
            :activityEmoji="$activity->emoji ?? '🎯'"
        />
    </x-ui.card>
    @endif

    {{-- ── Materials ── --}}
    @if(is_array($activity->materials_needed) && count((array)$activity->materials_needed))
    <x-ui.card variant="clay" padding="md" class="mb-4">
        <h2 class="font-bold text-[var(--color-text)] mb-3 flex items-center gap-2">
            <span aria-hidden="true">🎒</span> Gather Your Supplies
        </h2>
        <ul class="space-y-2">
            @foreach($activity->materials_needed as $material)
            <li class="flex items-center gap-2 text-[var(--color-text)]">
                <x-ui.icon name="check-circle" class="w-4 h-4 text-emerald-500 shrink-0" aria-hidden="true" />
                {{ $material }}
            </li>
            @endforeach
        </ul>
    </x-ui.card>
    @endif

    {{-- ── Learning objectives ── --}}
    @if(is_array($activity->learning_objectives) && count((array)$activity->learning_objectives))
    <x-ui.card variant="clay" padding="md" class="mb-4">
        <h2 class="font-bold text-[var(--color-text)] mb-3 flex items-center gap-2">
            <span aria-hidden="true">🏆</span> What You'll Master
        </h2>
        <ul class="space-y-2">
            @foreach($activity->learning_objectives as $obj)
            <li class="flex items-center gap-2 text-[var(--color-text)]">
                <x-ui.icon name="star" class="w-4 h-4 text-amber-400 shrink-0" aria-hidden="true" />
                {{ $obj }}
            </li>
            @endforeach
        </ul>
    </x-ui.card>
    @endif

    {{-- ── Completion zone ── --}}
    @php $actType = $activity->activity_type ?? ''; @endphp
    <div class="mb-8 space-y-3">
        {{-- Activity type CTA --}}
        @if($actType === 'tracing')
            <x-ui.button variant="primary" href="{{ route('activities.tracing', $activity) . $childQuery }}" icon="pencil" size="lg" class="w-full justify-center">
                Start Tracing ✏️
            </x-ui.button>
        @elseif($actType === 'drawing')
            <x-ui.button variant="primary" href="{{ route('activities.drawing', $activity) . $childQuery }}" icon="brush" size="lg" class="w-full justify-center">
                Start Drawing 🎨
            </x-ui.button>
        @elseif($actType === 'puzzle')
            <x-ui.button variant="primary" href="{{ route('activities.puzzle', $activity) . $childQuery }}" icon="puzzle-piece" size="lg" class="w-full justify-center">
                Start Puzzle 🧩
            </x-ui.button>
        @elseif($actType === 'quiz' && ($activity->quiz_id ?? false))
            <x-ui.button variant="primary" href="{{ route('quizzes.show', $activity->quiz_id) . $childQuery }}" icon="target" size="lg" class="w-full justify-center">
                Take Quiz 🧠
            </x-ui.button>
        @elseif($actType === 'video' || $activity->video_url)
            <x-ui.button variant="primary" href="{{ route('activities.video', $activity) . $childQuery }}" icon="play" size="lg" class="w-full justify-center">
                Watch Video 🎬
            </x-ui.button>
        @elseif(in_array($actType, ['slides', 'simulation']))
            <x-ui.button variant="primary" href="{{ route('activities.slides', $activity) . $childQuery }}" icon="layers" size="lg" class="w-full justify-center">
                Start Lesson Slides 📖
            </x-ui.button>
        @elseif($activity->steps && $activity->steps->count() > 0)
            <x-ui.button variant="primary" href="{{ route('activities.slides', $activity) . $childQuery }}" icon="layers" size="lg" class="w-full justify-center">
                Interactive Lesson View 🎯
            </x-ui.button>
            <x-ui.card variant="clay" padding="md" class="text-center">
                <div class="text-3xl mb-2" aria-hidden="true">🌟</div>
                <h3 class="font-display font-bold text-[var(--color-text)] mb-1">Or Work Through the Steps Above</h3>
                <p class="text-sm text-[var(--color-text-muted)]">Read each step, then come back when you're done!</p>
            </x-ui.card>
        @else
            <x-ui.card variant="clay" padding="md" class="text-center">
                <div class="text-3xl mb-2" aria-hidden="true">🌟</div>
                <h3 class="font-display font-bold text-[var(--color-text)] mb-1">You're Ready to Begin!</h3>
                <p class="text-sm text-[var(--color-text-muted)]">Work through the steps above and come back when you're done.</p>
            </x-ui.card>
        @endif

        {{-- Mark complete --}}
        @if(isset($child) && $child)
        <form action="{{ route('child.activity.complete', [$child, $activity]) }}" method="POST" id="completeForm">
            @csrf
            <button
                type="submit"
                id="completeBtn"
                class="w-full flex items-center gap-4 px-5 py-4 min-h-[4rem] rounded-[var(--radius-card)] border-[3px] border-emerald-500 bg-gradient-to-br from-emerald-500 to-emerald-400 text-white shadow-[var(--shadow-clay)] hover:-translate-y-[2px] hover:shadow-[var(--shadow-clay-hover)] active:scale-95 transition-all focus-visible:outline-2 focus-visible:outline-emerald-600 focus-visible:outline-offset-2 cursor-pointer"
            >
                <span class="text-3xl shrink-0" aria-hidden="true">🎉</span>
                <span class="flex-1 text-start">
                    <strong class="block font-display font-black text-base leading-tight">I Finished This Activity!</strong>
                    <small class="text-emerald-100 text-sm">Mark complete &amp; earn your badge</small>
                </span>
                <x-ui.icon name="check-circle" class="w-6 h-6 shrink-0" aria-hidden="true" />
            </button>
        </form>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('completeBtn');
    if (btn) {
        btn.addEventListener('click', function() {
            btn.style.transform = 'scale(0.96)';
            btn.style.opacity = '0.85';
        });
    }
});
</script>
@endpush
