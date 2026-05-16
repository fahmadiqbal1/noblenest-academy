@extends('layouts.maternal')

@section('title', $maternalMealPlan->title . ' — Nutrition')

@section('content')
<div class="container py-4">
    <div class="flex flex-wrap">
        <div class="lg:w-3/12 hidden lg:block">
            @include('maternal.partials.sidebar')
        </div>
        <div class="lg:w-9/12">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="flex items-center gap-2 text-sm flex-wrap">
                    <li class=""><a href="{{ route('maternal.nutrition.index') }}">Nutrition</a></li>
                    <li class="active">{{ $maternalMealPlan->title }}</li>
                </ol>
            </nav>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0 mb-4" style="background:rgba(255,255,255,0.88); border-radius:1.25rem; box-shadow:8px 8px 16px rgba(124,58,237,0.08);">
                <div class="p-5 p-4">
                    <h2 style="font-family:'Baloo 2',sans-serif; color:var(--nn-text);">{{ $maternalMealPlan->title }}</h2>

                    <div class="flex items-start gap-3 p-4 rounded-lg border border-0 mb-4" style="background:linear-gradient(135deg, #ECFDF5, #D1FAE5); border-radius:1rem;">
                        <h6 class="mb-1"><x-ui.icon name="lightbulb" class="me-1" style="color:#059669;" /> Nutritional Benefit</h6>
                        <p class="mb-0">{{ $maternalMealPlan->benefit_explanation }}</p>
                    </div>

                    {{-- Key Nutrients --}}
                    @if($maternalMealPlan->key_nutrients)
                    <div class="mb-4">
                        <h6 class="font-semibold">Key Nutrients</h6>
                        <div class="flex flex-wrap gap-2">
                            @foreach($maternalMealPlan->key_nutrients as $nutrient)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium px-3 py-2" style="background:#FEF3C7; color:#92400E;">{{ $nutrient }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Herb Tea --}}
                    @if($maternalMealPlan->herb_tea_recommendation)
                    <div class="flex items-start gap-3 p-4 rounded-lg border border-0 mb-4" style="background:#F0FDF4; border-radius:1rem; border-left:4px solid #22C55E !important;">
                        <h6 class="mb-1"><x-ui.icon name="coffee" class="me-1" style="color:#16A34A;" /> Herbal Tea Recommendation</h6>
                        <p class="mb-0">{{ $maternalMealPlan->herb_tea_recommendation }}</p>
                    </div>
                    @endif

                    {{-- Meals --}}
                    @if($maternalMealPlan->meals)
                    <h5 class="font-semibold mb-3">Daily Meal Plan</h5>
                    @foreach(['breakfast' => '🌅 Breakfast', 'morning_snack' => '🍎 Morning Snack', 'lunch' => '☀️ Lunch', 'afternoon_snack' => '🥤 Afternoon Snack', 'dinner' => '🌙 Dinner'] as $mealKey => $mealLabel)
                        @if(isset($maternalMealPlan->meals[$mealKey]))
                        <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0 mb-2" style="background:rgba(124,58,237,0.04); border-radius:1rem;">
                            <div class="p-5 p-3">
                                <h6 class="mb-1">{{ $mealLabel }}</h6>
                                <p class="mb-0 text-sm">{{ $maternalMealPlan->meals[$mealKey] }}</p>
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
