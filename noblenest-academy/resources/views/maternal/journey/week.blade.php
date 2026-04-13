@extends('layouts.app')

@section('title', "Week $week — Maternal Journey")

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-3 d-none d-lg-block">
            @include('maternal.partials.sidebar')
        </div>
        <div class="col-lg-9">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('maternal.journey') }}">Journey</a></li>
                    <li class="breadcrumb-item active">Week {{ $week }}</li>
                </ol>
            </nav>

            <div class="card border-0 mb-4" style="background:linear-gradient(135deg, #EC4899, #F472B6); border-radius:1.25rem; color:#fff;">
                <div class="card-body p-4">
                    <h3 class="mb-1" style="font-family:'Baloo 2',sans-serif;">Week {{ $week }}</h3>
                    <p class="mb-0 opacity-80">{{ $profile->stage }} · {{ $content->count() }} activities available</p>
                </div>
            </div>

            {{-- Emergency signs for this stage --}}
            @if($emergencySigns->isNotEmpty())
            <div class="alert border-0 mb-4" style="background:#FEF2F2; border-radius:1rem;">
                <h6 class="text-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i> Watch for these signs this week</h6>
                <ul class="mb-0 small">
                    @foreach($emergencySigns as $sign)
                        <li><strong>{{ $sign->symptom }}</strong> ({{ $sign->severity }}) — {{ $sign->action_text }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Content for this week --}}
            <div class="row g-3">
                @forelse($content as $item)
                <div class="col-md-6">
                    <div class="card h-100 border-0" style="background:rgba(255,255,255,0.82); border-radius:1rem; box-shadow:4px 4px 12px rgba(124,58,237,0.06);">
                        @if($item->thumbnail_url)
                            <img src="{{ $item->thumbnail_url }}" class="card-img-top" alt="{{ $item->title }}" style="border-radius:1rem 1rem 0 0; height:140px; object-fit:cover;">
                        @endif
                        <div class="card-body p-3">
                            <div class="d-flex gap-2 mb-2">
                                <span class="badge rounded-pill" style="background:var(--nn-primary-soft); color:var(--nn-primary); font-size:0.7rem;">{{ ucfirst($item->content_type) }}</span>
                                @if($item->cultural_origin)
                                    <span class="badge rounded-pill" style="background:#FEF3C7; color:#92400E; font-size:0.7rem;">{{ ucfirst($item->cultural_origin) }}</span>
                                @endif
                            </div>
                            <h6 class="mb-1" style="font-family:'Baloo 2',sans-serif;">{{ $item->title }}</h6>
                            <p class="small text-muted mb-2">{{ Str::limit($item->benefit_explanation, 100) }}</p>
                            @if($item->skills_improved)
                                <div class="d-flex flex-wrap gap-1 mb-2">
                                    @foreach(array_slice($item->skills_improved, 0, 3) as $skill)
                                        <span class="badge rounded-pill" style="background:#ECFDF5; color:#065F46; font-size:0.65rem;">{{ $skill }}</span>
                                    @endforeach
                                </div>
                            @endif
                            <a href="{{ route('maternal.content.show', $item) }}" class="btn btn-sm rounded-pill fw-semibold" style="background:var(--nn-primary-soft); color:var(--nn-primary);">
                                View <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <p class="text-muted">No specific content for this week yet. Browse <a href="{{ route('maternal.content.index') }}">all content</a>.</p>
                </div>
                @endforelse
            </div>

            {{-- Week navigation --}}
            <div class="d-flex justify-content-between mt-4">
                @if($week > 1)
                    <a href="{{ route('maternal.journey.week', $week - 1) }}" class="btn btn-outline-secondary rounded-pill">
                        <i class="bi bi-arrow-left me-1"></i> Week {{ $week - 1 }}
                    </a>
                @else
                    <span></span>
                @endif
                @if($week < 52)
                    <a href="{{ route('maternal.journey.week', $week + 1) }}" class="btn btn-outline-secondary rounded-pill">
                        Week {{ $week + 1 }} <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
