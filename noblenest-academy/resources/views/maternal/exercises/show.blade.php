@extends('layouts.maternal')

@section('title', $maternalExercisePlan->title . ' — Maternal Exercises')

@section('content')
<div class="container py-4">
    <div class="flex flex-wrap">
        <div class="lg:w-3/12 hidden lg:block">
            @include('maternal.partials.sidebar')
        </div>
        <div class="lg:w-9/12">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="flex items-center gap-2 text-sm flex-wrap">
                    <li class=""><a href="{{ route('maternal.exercises.index') }}">Exercises</a></li>
                    <li class="active">{{ $maternalExercisePlan->title }}</li>
                </ol>
            </nav>

            {{-- Contraindication warning --}}
            @if(!empty($blocked))
            <div class="flex items-start gap-3 p-4 rounded-lg border bg-red-50 border-red-200 text-red-800 border-0 mb-4">
                <h6 class="mb-1"><x-ui.icon name="alert-triangle" class="me-1" /> Health Notice</h6>
                <p class="mb-0">This exercise may not be suitable for you due to: <strong>{{ implode(', ', array_map(fn($c) => str_replace('_', ' ', $c), $blocked)) }}</strong>. Please consult your healthcare provider before proceeding.</p>
            </div>
            @endif

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0 mb-4" style="background:rgba(255,255,255,0.88); border-radius:1.25rem; box-shadow:8px 8px 16px rgba(124,58,237,0.08);">
                @if($maternalExercisePlan->video_url)
                    <div class="ratio ratio-16x9" style="border-radius:1.25rem 1.25rem 0 0; overflow:hidden;">
                        <video controls preload="metadata" poster="{{ $maternalExercisePlan->thumbnail_url }}">
                            <source src="{{ $maternalExercisePlan->video_url }}" type="video/mp4">
                        </video>
                    </div>
                @endif

                <div class="p-5 p-4">
                    <div class="flex flex-wrap gap-2 mb-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" style="background:#FEF3C7; color:#92400E;">{{ ucfirst($maternalExercisePlan->cultural_origin ?? 'General') }}</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" style="background:#E0E7FF; color:#4338CA;">{{ ucfirst(str_replace('_', ' ', $maternalExercisePlan->stage)) }}</span>
                        @if($maternalExercisePlan->duration_minutes)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" style="background:#ECFDF5; color:#065F46;"><x-ui.icon name="clock" class="me-1" />{{ $maternalExercisePlan->duration_minutes }} min</span>
                        @endif
                    </div>

                    <h2 style="font-family:'Baloo 2',sans-serif; color:var(--nn-text);">{{ $maternalExercisePlan->title }}</h2>

                    {{-- Benefit --}}
                    <div class="flex items-start gap-3 p-4 rounded-lg border border-0 mb-4" style="background:linear-gradient(135deg, #ECFDF5, #D1FAE5); border-radius:1rem;">
                        <h6 class="mb-1"><x-ui.icon name="lightbulb" class="me-1" style="color:#059669;" /> Why This Helps</h6>
                        <p class="mb-0">{{ $maternalExercisePlan->benefit_explanation }}</p>
                    </div>

                    {{-- Exercises list --}}
                    @if($maternalExercisePlan->exercises)
                    <h5 class="font-semibold mb-3">Exercises</h5>
                    @foreach($maternalExercisePlan->exercises as $i => $exercise)
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0 mb-2" style="background:rgba(124,58,237,0.04); border-radius:1rem;">
                        <div class="p-5 p-3">
                            <div class="flex gap-3">
                                <div class="flex-shrink-0 flex items-center justify-center" style="width:36px;height:36px;border-radius:50%;background:var(--nn-primary);color:#fff;font-weight:700;">
                                    {{ $i + 1 }}
                                </div>
                                <div>
                                    <h6 class="mb-1">{{ $exercise['name'] ?? 'Exercise ' . ($i + 1) }}</h6>
                                    @if(isset($exercise['description']))
                                        <p class="text-sm text-[var(--color-text-muted)] mb-1">{{ $exercise['description'] }}</p>
                                    @endif
                                    @if(isset($exercise['reps']))
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" style="background:var(--nn-primary-soft); color:var(--nn-primary); font-size:0.75rem;">{{ $exercise['reps'] }}</span>
                                    @endif
                                    @if(isset($exercise['duration']))
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" style="background:#FEF3C7; color:#92400E; font-size:0.75rem;">{{ $exercise['duration'] }}</span>
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
