@extends('layouts.app')

@section('title', 'Exercise Plans — Maternal Wellness')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-3 d-none d-lg-block">
            @include('maternal.partials.sidebar')
        </div>
        <div class="col-lg-9">
            <h3 class="mb-4" style="font-family:'Baloo 2',sans-serif; color:var(--nn-text);">
                <i class="bi bi-person-arms-up me-2" style="color:#EC4899;"></i> Exercise Plans
                <small class="text-muted fs-6">for {{ ucfirst(str_replace('_', ' ', $profile->stage)) }}</small>
            </h3>

            {{-- Culture filter --}}
            <div class="d-flex gap-2 mb-4">
                <a href="{{ route('maternal.exercises.index') }}" class="badge rounded-pill px-3 py-2 text-decoration-none {{ !request('culture') ? 'text-white' : '' }}" style="{{ !request('culture') ? 'background:var(--nn-primary);' : 'background:var(--nn-primary-soft); color:var(--nn-primary);' }}">All</a>
                @foreach(['chinese' => 'Chinese', 'japanese' => 'Japanese', 'ayurvedic' => 'Ayurvedic', 'general' => 'General'] as $key => $label)
                    <a href="{{ route('maternal.exercises.index', ['culture' => $key]) }}" class="badge rounded-pill px-3 py-2 text-decoration-none {{ request('culture') === $key ? 'text-white' : '' }}" style="{{ request('culture') === $key ? 'background:var(--nn-primary);' : 'background:var(--nn-primary-soft); color:var(--nn-primary);' }}">{{ $label }}</a>
                @endforeach
            </div>

            <div class="row g-3">
                @forelse($exercises as $plan)
                <div class="col-md-6">
                    <div class="card h-100 border-0" style="background:rgba(255,255,255,0.82); border-radius:1rem; box-shadow:4px 4px 12px rgba(124,58,237,0.06);">
                        <div class="card-body p-4">
                            <div class="d-flex gap-2 mb-2">
                                <span class="badge rounded-pill" style="background:#FEF3C7; color:#92400E; font-size:0.7rem;">{{ ucfirst($plan->cultural_origin ?? 'General') }}</span>
                                <span class="badge rounded-pill" style="background:#E0E7FF; color:#4338CA; font-size:0.7rem;">{{ $plan->difficulty ?? 'Beginner' }}</span>
                            </div>
                            <h5 class="mb-2" style="font-family:'Baloo 2',sans-serif;">{{ $plan->title }}</h5>
                            <p class="small text-muted mb-3">{{ Str::limit($plan->benefit_explanation, 120) }}</p>

                            @if($plan->duration_minutes)
                                <p class="small mb-2"><i class="bi bi-clock me-1"></i> {{ $plan->duration_minutes }} minutes</p>
                            @endif

                            @if($plan->contraindications)
                                <div class="small text-danger mb-2">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    Not for: {{ implode(', ', array_map(fn($c) => str_replace('_', ' ', $c), $plan->contraindications)) }}
                                </div>
                            @endif

                            <a href="{{ route('maternal.exercises.show', $plan) }}" class="btn btn-sm rounded-pill fw-semibold" style="background:var(--nn-primary-soft); color:var(--nn-primary);">
                                View Plan <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <p class="text-muted text-center py-4">No exercise plans for this stage yet. Check back soon!</p>
                </div>
                @endforelse
            </div>

            <div class="mt-4">{{ $exercises->withQueryString()->links() }}</div>
        </div>
    </div>
</div>
@endsection
