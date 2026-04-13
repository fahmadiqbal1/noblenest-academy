@extends('layouts.app')

@section('title', $maternalContent->title . ' — Maternal Wellness')
@section('meta_description', Str::limit($maternalContent->benefit_explanation, 160))

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-3 d-none d-lg-block">
            @include('maternal.partials.sidebar')
        </div>
        <div class="col-lg-9">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('maternal.content.index') }}">Content</a></li>
                    <li class="breadcrumb-item active">{{ $maternalContent->title }}</li>
                </ol>
            </nav>

            <div class="card border-0 mb-4" style="background:rgba(255,255,255,0.88); border-radius:1.25rem; box-shadow:8px 8px 16px rgba(124,58,237,0.08);">
                {{-- Header image/video --}}
                @if($maternalContent->video_url)
                    <div class="ratio ratio-16x9" style="border-radius:1.25rem 1.25rem 0 0; overflow:hidden;">
                        <video controls preload="metadata" poster="{{ $maternalContent->thumbnail_url }}">
                            <source src="{{ $maternalContent->video_url }}" type="video/mp4">
                        </video>
                    </div>
                @elseif($maternalContent->thumbnail_url)
                    <img src="{{ $maternalContent->thumbnail_url }}" class="card-img-top" alt="{{ $maternalContent->title }}" style="border-radius:1.25rem 1.25rem 0 0; max-height:300px; object-fit:cover;">
                @endif

                <div class="card-body p-4">
                    {{-- Badges --}}
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge rounded-pill" style="background:var(--nn-primary-soft); color:var(--nn-primary);">{{ ucfirst($maternalContent->content_type) }}</span>
                        <span class="badge rounded-pill" style="background:#E0E7FF; color:#4338CA;">{{ ucfirst(str_replace('_', ' ', $maternalContent->stage)) }}</span>
                        @if($maternalContent->cultural_origin)
                            <span class="badge rounded-pill" style="background:#FEF3C7; color:#92400E;">{{ ucfirst($maternalContent->cultural_origin) }}</span>
                        @endif
                        <span class="badge rounded-pill" style="background:#ECFDF5; color:#065F46;">{{ ucfirst(str_replace('_', ' ', $maternalContent->category)) }}</span>
                    </div>

                    <h2 style="font-family:'Baloo 2',sans-serif; color:var(--nn-text);">{{ $maternalContent->title }}</h2>

                    {{-- Benefit explanation (always visible) --}}
                    <div class="alert border-0 mb-4" style="background:linear-gradient(135deg, #ECFDF5, #D1FAE5); border-radius:1rem;">
                        <h6 class="mb-1"><i class="bi bi-lightbulb me-1" style="color:#059669;"></i> Why This Matters</h6>
                        <p class="mb-0">{{ $maternalContent->benefit_explanation }}</p>
                    </div>

                    {{-- Practitioner side notes (warnings visible to parents) --}}
                    @if($maternalContent->sideNotes->isNotEmpty())
                    <div class="mb-4">
                        @foreach($maternalContent->sideNotes as $review)
                        <div class="alert border-0 mb-2" style="background:linear-gradient(135deg, #FEF3C7, #FDE68A); border-radius:1rem; border-left: 4px solid #F59E0B !important;">
                            <h6 class="mb-1 fw-bold" style="color:#92400E;"><i class="bi bi-exclamation-triangle me-1"></i> Practitioner Note</h6>
                            <p class="mb-1">{{ $review->side_notes }}</p>
                            <small class="text-muted">— {{ ucfirst(str_replace('_', ' ', $review->credential_used)) }}, reviewed {{ $review->reviewed_at?->format('M j, Y') }}</small>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    {{-- Skills improved --}}
                    @if($maternalContent->skills_improved)
                    <div class="mb-4">
                        <h6 class="fw-semibold"><i class="bi bi-graph-up me-1"></i> Skills & Benefits</h6>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($maternalContent->skills_improved as $skill)
                                <span class="badge rounded-pill px-3 py-2" style="background:#F3E8FF; color:#7C3AED;">{{ $skill }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Audio player --}}
                    @if($maternalContent->audio_url)
                    <div class="mb-4">
                        <h6 class="fw-semibold"><i class="bi bi-headphones me-1"></i> Listen</h6>
                        <audio controls class="w-100" preload="metadata">
                            <source src="{{ $maternalContent->audio_url }}" type="audio/mpeg">
                        </audio>
                    </div>
                    @endif

                    {{-- Main description --}}
                    <div class="mb-4 content-body">
                        {!! nl2br(e($maternalContent->description)) !!}
                    </div>

                    {{-- Step Player (animated slideshow) --}}
                    @if($maternalContent->steps->isNotEmpty() && $maternalContent->steps->contains(fn($s) => $s->visual_url || $s->audio_url))
                    <div class="mb-4">
                        <h5 class="fw-semibold mb-3"><i class="bi bi-play-circle me-1"></i> Guided Walkthrough</h5>
                        <x-step-player :steps="$maternalContent->steps" />
                    </div>
                    @endif

                    {{-- Steps --}}
                    @if($maternalContent->steps->isNotEmpty())
                    <div class="mb-4">
                        <h5 class="fw-semibold mb-3"><i class="bi bi-list-ol me-1"></i> Steps</h5>
                        @foreach($maternalContent->steps as $step)
                        <div class="card border-0 mb-2" style="background:rgba(124,58,237,0.04); border-radius:1rem;">
                            <div class="card-body p-3">
                                <div class="d-flex gap-3">
                                    <div class="flex-shrink-0 d-flex align-items-center justify-content-center" style="width:32px;height:32px;border-radius:50%;background:var(--nn-primary);color:#fff;font-weight:700;font-size:0.85rem;">
                                        {{ $step->step_number }}
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $step->title }}</h6>
                                        <p class="mb-0 small text-muted">{{ $step->instruction }}</p>
                                        @if($step->image_url)
                                            <img src="{{ $step->image_url }}" class="mt-2 rounded-3" style="max-height:200px;" alt="Step {{ $step->step_number }}">
                                        @endif
                                        @if($step->video_url)
                                            <video controls class="mt-2 rounded-3 w-100" preload="metadata" style="max-height:200px;">
                                                <source src="{{ $step->video_url }}" type="video/mp4">
                                            </video>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    {{-- Progress actions --}}
                    <div class="border-top pt-3">
                        @if(!$progress)
                            <form method="POST" action="{{ route('maternal.content.start', $maternalContent) }}">
                                @csrf
                                <button type="submit" class="btn rounded-pill fw-semibold px-4" style="background:linear-gradient(135deg, #EC4899, #F472B6); color:#fff;">
                                    <i class="bi bi-play-fill me-1"></i> Start Activity
                                </button>
                            </form>
                        @elseif(!$progress->completed_at)
                            <form method="POST" action="{{ route('maternal.content.complete', $maternalContent) }}">
                                @csrf
                                <div class="d-flex gap-3 align-items-end">
                                    <div>
                                        <label class="form-label small fw-semibold">Rate this content</label>
                                        <select name="rating" class="form-select form-select-sm rounded-3" style="width:120px;">
                                            <option value="">Skip</option>
                                            @for($i = 5; $i >= 1; $i--)
                                                <option value="{{ $i }}">{{ str_repeat('★', $i) }}{{ str_repeat('☆', 5-$i) }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <button type="submit" class="btn rounded-pill fw-semibold px-4" style="background:linear-gradient(135deg, #10B981, #34D399); color:#fff;">
                                        <i class="bi bi-check-circle me-1"></i> Mark Complete
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge rounded-pill px-3 py-2" style="background:#ECFDF5; color:#065F46; font-size:0.85rem;">
                                    <i class="bi bi-check-circle-fill me-1"></i> Completed {{ $progress->completed_at->diffForHumans() }}
                                </span>
                                @if($progress->rating)
                                    <span class="text-warning">{{ str_repeat('★', $progress->rating) }}{{ str_repeat('☆', 5-$progress->rating) }}</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Related content --}}
            @if($related->isNotEmpty())
            <h5 class="mb-3" style="font-family:'Baloo 2',sans-serif;">Related Content</h5>
            <div class="row g-3">
                @foreach($related as $item)
                <div class="col-md-6 col-xl-3">
                    <div class="card h-100 border-0" style="background:rgba(255,255,255,0.82); border-radius:1rem;">
                        <div class="card-body p-3">
                            <h6 class="mb-1" style="font-family:'Baloo 2',sans-serif; font-size:0.9rem;">{{ $item->title }}</h6>
                            <p class="small text-muted mb-2">{{ Str::limit($item->benefit_explanation, 60) }}</p>
                            <a href="{{ route('maternal.content.show', $item) }}" class="small fw-semibold" style="color:var(--nn-primary);">View →</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
