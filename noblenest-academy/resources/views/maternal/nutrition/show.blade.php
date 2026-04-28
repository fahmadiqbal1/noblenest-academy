@extends('layouts.app')

@section('title', $maternalMealPlan->title . ' — Nutrition')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-3 d-none d-lg-block">
            @include('maternal.partials.sidebar')
        </div>
        <div class="col-lg-9">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('maternal.nutrition.index') }}">Nutrition</a></li>
                    <li class="breadcrumb-item active">{{ $maternalMealPlan->title }}</li>
                </ol>
            </nav>

            <div class="card border-0 mb-4" style="background:rgba(255,255,255,0.88); border-radius:1.25rem; box-shadow:8px 8px 16px rgba(124,58,237,0.08);">
                <div class="card-body p-4">
                    <h2 style="font-family:'Baloo 2',sans-serif; color:var(--nn-text);">{{ $maternalMealPlan->title }}</h2>

                    <div class="alert border-0 mb-4" style="background:linear-gradient(135deg, #ECFDF5, #D1FAE5); border-radius:1rem;">
                        <h6 class="mb-1"><i class="bi bi-lightbulb me-1" style="color:#059669;"></i> Nutritional Benefit</h6>
                        <p class="mb-0">{{ $maternalMealPlan->benefit_explanation }}</p>
                    </div>

                    {{-- Key Nutrients --}}
                    @if($maternalMealPlan->key_nutrients)
                    <div class="mb-4">
                        <h6 class="fw-semibold">Key Nutrients</h6>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($maternalMealPlan->key_nutrients as $nutrient)
                                <span class="badge rounded-pill px-3 py-2" style="background:#FEF3C7; color:#92400E;">{{ $nutrient }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Herb Tea --}}
                    @if($maternalMealPlan->herb_tea_recommendation)
                    <div class="alert border-0 mb-4" style="background:#F0FDF4; border-radius:1rem; border-left:4px solid #22C55E !important;">
                        <h6 class="mb-1"><i class="bi bi-cup-hot me-1" style="color:#16A34A;"></i> Herbal Tea Recommendation</h6>
                        <p class="mb-0">{{ $maternalMealPlan->herb_tea_recommendation }}</p>
                    </div>
                    @endif

                    {{-- Meals --}}
                    @if($maternalMealPlan->meals)
                    <h5 class="fw-semibold mb-3">Daily Meal Plan</h5>
                    @foreach(['breakfast' => '🌅 Breakfast', 'morning_snack' => '🍎 Morning Snack', 'lunch' => '☀️ Lunch', 'afternoon_snack' => '🥤 Afternoon Snack', 'dinner' => '🌙 Dinner'] as $mealKey => $mealLabel)
                        @if(isset($maternalMealPlan->meals[$mealKey]))
                        <div class="card border-0 mb-2" style="background:rgba(124,58,237,0.04); border-radius:1rem;">
                            <div class="card-body p-3">
                                <h6 class="mb-1">{{ $mealLabel }}</h6>
                                <p class="mb-0 small">{{ $maternalMealPlan->meals[$mealKey] }}</p>
                            </div>
                        </div>
                        @endif
                    @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
