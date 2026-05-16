@extends('layouts.maternal')

@section('title', 'Nutrition & Meal Plans — Maternal Wellness')

@section('content')
<div class="container py-4">
    <div class="flex flex-wrap">
        <div class="lg:w-3/12 hidden lg:block">
            @include('maternal.partials.sidebar')
        </div>
        <div class="lg:w-9/12">
            <h3 class="mb-4" style="font-family:'Baloo 2',sans-serif; color:var(--nn-text);">
                <x-ui.icon name="egg" class="me-2" style="color:#F59E0B;" /> Nutrition & Meal Plans
            </h3>

            <div class="flex gap-2 mb-4">
                <a href="{{ route('maternal.nutrition.index') }}" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium px-3 py-2 no-underline {{ !request('culture') ? 'text-white' : '' }}" style="{{ !request('culture') ? 'background:var(--nn-primary);' : 'background:var(--nn-primary-soft); color:var(--nn-primary);' }}">All</a>
                @foreach(['chinese' => 'Chinese', 'japanese' => 'Japanese', 'ayurvedic' => 'Ayurvedic', 'general' => 'General'] as $key => $label)
                    <a href="{{ route('maternal.nutrition.index', ['culture' => $key]) }}" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium px-3 py-2 no-underline {{ request('culture') === $key ? 'text-white' : '' }}" style="{{ request('culture') === $key ? 'background:var(--nn-primary);' : 'background:var(--nn-primary-soft); color:var(--nn-primary);' }}">{{ $label }}</a>
                @endforeach
            </div>

            <div class="flex flex-wrap gap-3">
                @forelse($mealPlans as $plan)
                <div class="md:w-6/12">
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm h-full border-0" style="background:rgba(255,255,255,0.82); border-radius:1rem; box-shadow:4px 4px 12px rgba(124,58,237,0.06);">
                        <div class="p-5 p-4">
                            <div class="flex gap-2 mb-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" style="background:#FEF3C7; color:#92400E; font-size:0.7rem;">{{ ucfirst($plan->cultural_origin ?? 'General') }}</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" style="background:#ECFDF5; color:#065F46; font-size:0.7rem;">{{ ucfirst(str_replace('_', ' ', $plan->stage)) }}</span>
                            </div>
                            <h5 class="mb-2" style="font-family:'Baloo 2',sans-serif;">{{ $plan->title }}</h5>
                            <p class="text-sm text-[var(--color-text-muted)] mb-2">{{ Str::limit($plan->benefit_explanation, 120) }}</p>

                            @if($plan->key_nutrients)
                                <div class="flex flex-wrap gap-1 mb-2">
                                    @foreach(array_slice($plan->key_nutrients, 0, 4) as $nutrient)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" style="background:#FEF3C7; color:#92400E; font-size:0.65rem;">{{ $nutrient }}</span>
                                    @endforeach
                                </div>
                            @endif

                            @if($plan->herb_tea_recommendation)
                                <p class="text-sm mb-2"><x-ui.icon name="coffee" class="me-1" style="color:#059669;" /> {{ $plan->herb_tea_recommendation }}</p>
                            @endif

                            <a href="{{ route('maternal.nutrition.show', $plan) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm rounded-full" style="background:var(--nn-primary-soft); color:var(--nn-primary);">
                                View Plan <x-ui.icon name="arrow-right" class="ms-1" />
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="w-full">
                    <p class="text-[var(--color-text-muted)] text-center py-4">No meal plans for this stage yet.</p>
                </div>
                @endforelse
            </div>

            <div class="mt-4">{{ $mealPlans->withQueryString()->links() }}</div>
        </div>
    </div>
</div>
@endsection
