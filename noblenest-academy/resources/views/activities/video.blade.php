@extends(isset($child) && $child ? 'layouts.child' : 'layouts.app')

@section('title', $activity->title . ' — Video')

@push('head')
<style>
.nn-vp-page { background: #0D0A1E; min-height: 100vh; padding-bottom: 2rem; }
.nn-vp-header {
    display: flex; align-items: center; gap: 1rem;
    padding: 1rem 1.25rem;
    background: rgba(15,10,40,0.95);
    border-bottom: 1px solid rgba(124,58,237,0.25);
}
.nn-vp-back {
    width: 40px; height: 40px; border-radius: 50%;
    background: rgba(124,58,237,0.2); border: 1.5px solid rgba(124,58,237,0.35);
    color: #A78BFA; display:inline-flex; align-items:center; justify-content:center;
    text-decoration:none; font-size:1rem; flex-shrink:0;
    transition: all 0.2s;
}
.nn-vp-back:hover { background: rgba(124,58,237,0.35); color:#fff; }
.nn-vp-header-title {
    font-family: 'Baloo 2', sans-serif; font-weight:800;
    color: #fff; font-size: 1.1rem; line-height: 1.3; flex:1; min-width:0;
    white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
}
.nn-vp-header-badge {
    flex-shrink:0;
    display:inline-flex; align-items:center; gap:0.3rem;
    padding: 0.3rem 0.75rem; border-radius:999px;
    background:rgba(124,58,237,0.2); border:1.5px solid rgba(124,58,237,0.35);
    color:#C4B5FD; font-size:0.78rem; font-weight:700;
}

/* Video container */
.nn-vp-stage {
    position: relative; width:100%;
    background: #000;
    max-height: 72vh;
    overflow: hidden;
}
.nn-vp-stage video {
    width: 100%; display:block;
    max-height: 72vh; object-fit: contain;
    background: #000;
}

/* Info panel beneath video */
.nn-vp-info {
    padding: 1.5rem 1.25rem;
    max-width: 860px; margin: 0 auto;
}
.nn-vp-info h2 {
    font-family: 'Baloo 2', sans-serif; font-weight: 900;
    font-size: 1.5rem; color: #fff; margin-bottom: 0.5rem;
}
.nn-vp-info p {
    color: rgba(255,255,255,0.72); line-height: 1.75; font-weight: 500;
    margin-bottom: 1rem;
}

/* Benefits box */
.nn-vp-benefit {
    background: rgba(16,185,129,0.1); border-radius:14px;
    border-left: 4px solid #10B981; padding: 1rem 1.25rem; margin-bottom: 1.25rem;
}
.nn-vp-benefit p { color: rgba(255,255,255,0.82); margin:0; font-size:0.9rem; }

/* Skills */
.nn-vp-skill-chip {
    display:inline-flex; align-items:center; gap:0.25rem;
    padding: 0.3rem 0.7rem; border-radius:999px;
    background: rgba(124,58,237,0.2); border:1.5px solid rgba(124,58,237,0.3);
    color:#C4B5FD; font-size:0.78rem; font-weight:700;
}

/* Steps accordion */
.nn-vp-steps { margin-top: 1.5rem; }
.nn-vp-step {
    display:flex; gap:1rem; align-items:flex-start;
    padding: 1rem 0;
    border-bottom: 1px solid rgba(255,255,255,0.07);
}
.nn-vp-step:last-child { border-bottom: none; }
.nn-vp-step__num {
    width:36px; height:36px; border-radius:50%; flex-shrink:0;
    background: rgba(124,58,237,0.25); border:2px solid rgba(124,58,237,0.4);
    color:#C4B5FD; font-weight:800; font-size:0.9rem;
    display:flex; align-items:center; justify-content:center;
}
.nn-vp-step__body { flex:1; min-width:0; }
.nn-vp-step__title { font-weight:800; color:#fff; margin-bottom:0.25rem; font-size:0.95rem; }
.nn-vp-step__text  { color:rgba(255,255,255,0.72); font-size:0.88rem; line-height:1.65; }

/* Complete btn */
.nn-vp-complete-btn {
    width:100%; padding:1.1rem; border-radius:18px;
    background: linear-gradient(135deg,#059669,#10B981);
    border: 3px solid #059669; color:#fff;
    font-family:'Baloo 2',sans-serif; font-weight:900; font-size:1.1rem;
    cursor:pointer; display:flex; align-items:center; justify-content:center; gap:0.75rem;
    transition: all 0.25s cubic-bezier(0.34,1.56,0.64,1);
    box-shadow: 0 6px 20px rgba(5,150,105,0.3);
    text-decoration:none;
}
.nn-vp-complete-btn:hover {
    transform: translateY(-2px) scale(1.01);
    color:#fff; text-decoration:none;
    box-shadow: 0 10px 28px rgba(5,150,105,0.4);
}
</style>
@endpush

@section('content')
@php
    $videoSrc = $activity->video_url ?: $activity->media_url;
    $subjectColor = match($activity->subject ?? '') {
        'islamic','quran','arabic' => '#7C3AED',
        'art'    => '#F43F5E', 'language' => '#3B82F6',
        'stem','science','coding'  => '#10B981',
        'stories','literacy'       => '#F59E0B',
        'motor'  => '#EC4899',
        default  => '#A78BFA',
    };
    $childQuery = isset($child) && $child ? '?child=' . $child->id : '';
    $backUrl = isset($child) && $child
        ? route('activities.show', $activity) . $childQuery
        : route('activities.show', $activity);
@endphp

<div class="nn-vp-page">
    {{-- Top bar --}}
    <div class="nn-vp-header">
        <a href="{{ $backUrl }}" class="nn-vp-back" aria-label="Back">
            <x-ui.icon name="arrow-left" />
        </a>
        <div class="nn-vp-header-title">{{ $activity->emoji ?? '🎬' }} {{ $activity->title }}</div>
        <span class="nn-vp-header-badge">
            <x-ui.icon name="circle-play" /> Video
        </span>
    </div>

    {{-- Video stage --}}
    <div class="nn-vp-stage">
        @if($videoSrc)
            @php
                $isYoutube = str_contains($videoSrc, 'youtube.com') || str_contains($videoSrc, 'youtu.be');
                $isVimeo   = str_contains($videoSrc, 'vimeo.com');
            @endphp
            @if($isYoutube)
                @php
                    preg_match('/(?:v=|youtu\.be\/)([a-zA-Z0-9_\-]+)/', $videoSrc, $m);
                    $ytId = $m[1] ?? '';
                @endphp
                <div style="aspect-ratio:16/9;width:100%;">
                    <iframe
                        src="https://www.youtube.com/embed/{{ $ytId }}?rel=0&modestbranding=1&playsinline=1"
                        width="100%" height="100%"
                        style="border:none;display:block;aspect-ratio:16/9;"
                        allowfullscreen
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        title="{{ e($activity->title) }}">
                    </iframe>
                </div>
            @elseif($isVimeo)
                @php
                    preg_match('/vimeo\.com\/(\d+)/', $videoSrc, $m);
                    $vimeoId = $m[1] ?? '';
                @endphp
                <div style="aspect-ratio:16/9;width:100%;">
                    <iframe
                        src="https://player.vimeo.com/video/{{ $vimeoId }}?badge=0&autopause=0&playsinline=1"
                        width="100%" height="100%"
                        style="border:none;display:block;aspect-ratio:16/9;"
                        allowfullscreen
                        allow="autoplay; fullscreen; picture-in-picture"
                        title="{{ e($activity->title) }}">
                    </iframe>
                </div>
            @else
                <video
                    controls
                    playsinline
                    preload="metadata"
                    poster="{{ $activity->thumbnail_url ?? '' }}"
                    style="width:100%;display:block;max-height:72vh;object-fit:contain;background:#000;">
                    <source src="{{ $videoSrc }}" type="video/mp4">
                    <source src="{{ $videoSrc }}" type="video/webm">
                    <p style="padding:2rem;color:#fff;text-align:center;">Your browser doesn't support video playback.
                        <a href="{{ $videoSrc }}" download style="color:#A78BFA;">Download the video</a>
                    </p>
                </video>
            @endif
        @else
            <div style="aspect-ratio:16/9;width:100%;display:flex;align-items:center;justify-content:center;background:#1E1B4B;">
                <div style="text-align:center;color:rgba(255,255,255,0.6);">
                    <div style="font-size:4rem;margin-bottom:1rem;">🎬</div>
                    <p style="font-family:'Baloo 2',sans-serif;font-weight:800;font-size:1.3rem;color:#fff;">Video Coming Soon</p>
                    <p style="font-size:0.9rem;">This activity's video is being prepared.</p>
                </div>
            </div>
        @endif
    </div>

    {{-- Info panel --}}
    <div class="nn-vp-info">
        <div class="flex flex-wrap gap-2 mb-3">
            @if($activity->subject)
            <span class="nn-vp-skill-chip" style="background:{{ $subjectColor }}22;border-color:{{ $subjectColor }}44;color:{{ $subjectColor }};">
                {{ ucfirst($activity->subject) }}
            </span>
            @endif
            @if($activity->duration_minutes)
            <span class="nn-vp-skill-chip">⏱️ {{ $activity->duration_minutes }} min</span>
            @endif
            @if($activity->difficulty)
            <span class="nn-vp-skill-chip">{{ ucfirst($activity->difficulty) }}</span>
            @endif
        </div>

        <h2>{{ $activity->emoji ?? '🎬' }} {{ $activity->title }}</h2>

        @if($activity->description)
        <p>{{ $activity->description }}</p>
        @endif

        @if($activity->benefit_explanation)
        <div class="nn-vp-benefit">
            <p><strong style="color:#10B981;">💡 Why this matters:</strong> {{ $activity->benefit_explanation }}</p>
        </div>
        @endif

        @if($activity->skills_improved && count($activity->skills_improved))
        <div class="flex flex-wrap gap-2 mb-4">
            <span style="font-size:0.82rem;font-weight:700;color:rgba(255,255,255,0.55);me-1">🌟 Skills:</span>
            @foreach($activity->skills_improved as $skill)
            <span class="nn-vp-skill-chip">{{ ucwords(str_replace('_', ' ', $skill)) }}</span>
            @endforeach
        </div>
        @endif

        {{-- Step walkthrough (below video) --}}
        @if($activity->steps && $activity->steps->count())
        <h5 style="font-family:'Baloo 2',sans-serif;font-weight:800;color:#fff;margin-bottom:1rem;">
            <span style="margin-right:0.5rem;">📋</span>Follow Along — Step by Step
        </h5>
        <div class="nn-vp-steps mb-4">
            @foreach($activity->steps as $step)
            <div class="nn-vp-step">
                <div class="nn-vp-step__num">{{ $step->step_number }}</div>
                <div class="nn-vp-step__body">
                    @if($step->title)
                    <div class="nn-vp-step__title">{{ $step->title }}</div>
                    @endif
                    <div class="nn-vp-step__text">{{ $step->instruction }}</div>
                    @if($step->benefit_note)
                    <div style="font-size:0.78rem;color:rgba(16,185,129,0.9);margin-top:0.35rem;font-weight:600;">
                        💡 {{ $step->benefit_note }}
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Complete button --}}
        @if(isset($child) && $child)
        <form action="{{ route('child.activity.complete', [$child, $activity]) }}" method="POST">
            @csrf
            <button type="submit" class="nn-vp-complete-btn">
                <span style="font-size:1.75rem;">🎉</span>
                <span>
                    <strong style="display:block;font-size:1.05rem;">I Watched This!</strong>
                    <small style="opacity:0.85;font-size:0.82rem;">Mark complete &amp; earn your badge</small>
                </span>
            </button>
        </form>
        @else
        <a href="{{ route('activities.show', $activity) }}" class="nn-vp-complete-btn" style="background:linear-gradient(135deg,#7C3AED,#A78BFA);border-color:#7C3AED;">
            <x-ui.icon name="arrow-left" style="font-size:1.1rem;" />
            Back to Activity Details
        </a>
        @endif
    </div>
</div>
@endsection
