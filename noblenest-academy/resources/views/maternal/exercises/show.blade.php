@extends('layouts.app')

@section('title', $maternalExercisePlan->title . ' — Maternal Exercises')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-3 d-none d-lg-block">
            @include('maternal.partials.sidebar')
        </div>
        <div class="col-lg-9">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('maternal.exercises.index') }}">Exercises</a></li>
                    <li class="breadcrumb-item active">{{ $maternalExercisePlan->title }}</li>
                </ol>
            </nav>

            {{-- Contraindication warning --}}
            @if(!empty($blocked))
            <div class="alert alert-danger border-0 rounded-3 mb-4">
                <h6 class="mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Health Notice</h6>
                <p class="mb-0">This exercise may not be suitable for you due to: <strong>{{ implode(', ', array_map(fn($c) => str_replace('_', ' ', $c), $blocked)) }}</strong>. Please consult your healthcare provider before proceeding.</p>
            </div>
            @endif

            <div class="card border-0 mb-4" style="background:rgba(255,255,255,0.88); border-radius:1.25rem; box-shadow:8px 8px 16px rgba(124,58,237,0.08);">
                @if($maternalExercisePlan->video_url)
                    <div class="ratio ratio-16x9" style="border-radius:1.25rem 1.25rem 0 0; overflow:hidden;">
                        <video controls preload="metadata" poster="{{ $maternalExercisePlan->thumbnail_url }}">
                            <source src="{{ $maternalExercisePlan->video_url }}" type="video/mp4">
                        </video>
                    </div>
                @endif

                <div class="card-body p-4">
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge rounded-pill" style="background:#FEF3C7; color:#92400E;">{{ ucfirst($maternalExercisePlan->cultural_origin ?? 'General') }}</span>
                        <span class="badge rounded-pill" style="background:#E0E7FF; color:#4338CA;">{{ ucfirst(str_replace('_', ' ', $maternalExercisePlan->stage)) }}</span>
                        @if($maternalExercisePlan->duration_minutes)
                            <span class="badge rounded-pill" style="background:#ECFDF5; color:#065F46;"><i class="bi bi-clock me-1"></i>{{ $maternalExercisePlan->duration_minutes }} min</span>
                        @endif
                    </div>

                    <h2 style="font-family:'Baloo 2',sans-serif; color:var(--nn-text);">{{ $maternalExercisePlan->title }}</h2>

                    {{-- Benefit --}}
                    <div class="alert border-0 mb-4" style="background:linear-gradient(135deg, #ECFDF5, #D1FAE5); border-radius:1rem;">
                        <h6 class="mb-1"><i class="bi bi-lightbulb me-1" style="color:#059669;"></i> Why This Helps</h6>
                        <p class="mb-0">{{ $maternalExercisePlan->benefit_explanation }}</p>
                    </div>

                    {{-- Exercises list --}}
                    @if($maternalExercisePlan->exercises)
                    <h5 class="fw-semibold mb-3">Exercises</h5>
                    @foreach($maternalExercisePlan->exercises as $i => $exercise)
                    <div class="card border-0 mb-2" style="background:rgba(124,58,237,0.04); border-radius:1rem;">
                        <div class="card-body p-3">
                            <div class="d-flex gap-3">
                                <div class="flex-shrink-0 d-flex align-items-center justify-content-center" style="width:36px;height:36px;border-radius:50%;background:var(--nn-primary);color:#fff;font-weight:700;">
                                    {{ $i + 1 }}
                                </div>
                                <div>
                                    <h6 class="mb-1">{{ $exercise['name'] ?? 'Exercise ' . ($i + 1) }}</h6>
                                    @if(isset($exercise['description']))
                                        <p class="small text-muted mb-1">{{ $exercise['description'] }}</p>
                                    @endif
                                    @if(isset($exercise['reps']))
                                        <span class="badge rounded-pill" style="background:var(--nn-primary-soft); color:var(--nn-primary); font-size:0.75rem;">{{ $exercise['reps'] }}</span>
                                    @endif
                                    @if(isset($exercise['duration']))
                                        <span class="badge rounded-pill" style="background:#FEF3C7; color:#92400E; font-size:0.75rem;">{{ $exercise['duration'] }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
