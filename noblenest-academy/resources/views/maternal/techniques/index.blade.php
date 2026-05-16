@extends('layouts.maternal')

@section('title', $cultureName . ' Techniques — Maternal Wellness')

@section('content')
<div class="container py-4">
    <div class="flex flex-wrap">
        <div class="lg:w-3/12 hidden lg:block">
            @include('maternal.partials.sidebar')
        </div>
        <div class="lg:w-9/12">
            @php
                $cultureIcon = match($culture) {
                    'chinese'   => '☯️',
                    'japanese'  => '🌸',
                    'ayurvedic' => '🕉️',
                    default     => '🌿',
                };
                $cultureDescription = match($culture) {
                    'chinese'   => 'Ancient Chinese maternal practices emphasize balance of Qi, warming foods, herbal teas, and gentle movements like Tai Chi and Qigong for pregnancy wellness.',
                    'japanese'  => 'Japanese satogaeri bunben traditions focus on postpartum rest, nourishing soups, gentle body recovery, and a community support system for new mothers.',
                    'ayurvedic' => 'Ayurvedic maternal care balances the three doshas through customized diet, yoga, meditation, herbal remedies, and oil massage (Abhyanga) for mother and baby.',
                    default     => 'General wellness techniques combining the best of traditional practices for a healthy pregnancy and postpartum recovery.',
                };
            @endphp

            <h3 class="mb-2" style="font-family:'Baloo 2',sans-serif; color:var(--nn-text);">
                {{ $cultureIcon }} {{ $cultureName }} Techniques
            </h3>
            <p class="text-[var(--color-text-muted)] mb-4">{{ $cultureDescription }}</p>

            {{-- Content --}}
            @if($content->isNotEmpty())
            <h5 class="mb-3" style="font-family:'Baloo 2',sans-serif;">Guides & Techniques</h5>
            <div class="flex flex-wrap gap-3 mb-4">
                @foreach($content as $item)
                <div class="md:w-6/12">
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm h-full border-0" style="background:rgba(255,255,255,0.82); border-radius:1rem; box-shadow:4px 4px 12px rgba(124,58,237,0.06);">
                        @if($item->thumbnail_url)
                            <img src="{{ $item->thumbnail_url }}" class="w-full rounded-t-xl" alt="{{ $item->title }}" style="border-radius:1rem 1rem 0 0; height:140px; object-fit:cover;">
                        @endif
                        <div class="p-5 p-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium mb-2" style="background:var(--nn-primary-soft); color:var(--nn-primary); font-size:0.7rem;">{{ ucfirst($item->content_type) }}</span>
                            <h6 class="mb-1" style="font-family:'Baloo 2',sans-serif;">{{ $item->title }}</h6>
                            <p class="text-sm text-[var(--color-text-muted)] mb-2">{{ Str::limit($item->benefit_explanation, 100) }}</p>
                            <a href="{{ route('maternal.content.show', $item) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm rounded-full" style="background:var(--nn-primary-soft); color:var(--nn-primary);">View →</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mb-4">{{ $content->links() }}</div>
            @endif

            {{-- Exercises for this culture --}}
            @if($exercises->isNotEmpty())
            <h5 class="mb-3" style="font-family:'Baloo 2',sans-serif;">Exercise Plans</h5>
            <div class="flex flex-wrap gap-3">
                @foreach($exercises as $plan)
                <div class="md:w-6/12">
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm h-full border-0" style="background:rgba(255,255,255,0.82); border-radius:1rem;">
                        <div class="p-5 p-3">
                            <h6 class="mb-1" style="font-family:'Baloo 2',sans-serif;">{{ $plan->title }}</h6>
                            <p class="text-sm text-[var(--color-text-muted)] mb-2">{{ Str::limit($plan->benefit_explanation, 100) }}</p>
                            @if($plan->duration_minutes)
                                <span class="text-sm text-[var(--color-text-muted)]"><x-ui.icon name="clock" class="me-1" />{{ $plan->duration_minutes }} min</span>
                            @endif
                            <div class="mt-2">
                                <a href="{{ route('maternal.exercises.show', $plan) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm rounded-full" style="background:var(--nn-primary-soft); color:var(--nn-primary);">Start →</a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            @if($content->isEmpty() && $exercises->isEmpty())
                <p class="text-[var(--color-text-muted)] text-center py-4">{{ $cultureName }} content for your current stage coming soon!</p>
            @endif
        </div>
    </div>
</div>
@endsection
