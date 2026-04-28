@extends('layouts.app')

@section('title', $cultureName . ' Techniques — Maternal Wellness')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-3 d-none d-lg-block">
            @include('maternal.partials.sidebar')
        </div>
        <div class="col-lg-9">
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
            <p class="text-muted mb-4">{{ $cultureDescription }}</p>

            {{-- Content --}}
            @if($content->isNotEmpty())
            <h5 class="mb-3" style="font-family:'Baloo 2',sans-serif;">Guides & Techniques</h5>
            <div class="row g-3 mb-4">
                @foreach($content as $item)
                <div class="col-md-6">
                    <div class="card h-100 border-0" style="background:rgba(255,255,255,0.82); border-radius:1rem; box-shadow:4px 4px 12px rgba(124,58,237,0.06);">
                        @if($item->thumbnail_url)
                            <img src="{{ $item->thumbnail_url }}" class="card-img-top" alt="{{ $item->title }}" style="border-radius:1rem 1rem 0 0; height:140px; object-fit:cover;">
                        @endif
                        <div class="card-body p-3">
                            <span class="badge rounded-pill mb-2" style="background:var(--nn-primary-soft); color:var(--nn-primary); font-size:0.7rem;">{{ ucfirst($item->content_type) }}</span>
                            <h6 class="mb-1" style="font-family:'Baloo 2',sans-serif;">{{ $item->title }}</h6>
                            <p class="small text-muted mb-2">{{ Str::limit($item->benefit_explanation, 100) }}</p>
                            <a href="{{ route('maternal.content.show', $item) }}" class="btn btn-sm rounded-pill fw-semibold" style="background:var(--nn-primary-soft); color:var(--nn-primary);">View →</a>
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
            <div class="row g-3">
                @foreach($exercises as $plan)
                <div class="col-md-6">
                    <div class="card h-100 border-0" style="background:rgba(255,255,255,0.82); border-radius:1rem;">
                        <div class="card-body p-3">
                            <h6 class="mb-1" style="font-family:'Baloo 2',sans-serif;">{{ $plan->title }}</h6>
                            <p class="small text-muted mb-2">{{ Str::limit($plan->benefit_explanation, 100) }}</p>
                            @if($plan->duration_minutes)
                                <span class="small text-muted"><i class="bi bi-clock me-1"></i>{{ $plan->duration_minutes }} min</span>
                            @endif
                            <div class="mt-2">
                                <a href="{{ route('maternal.exercises.show', $plan) }}" class="btn btn-sm rounded-pill fw-semibold" style="background:var(--nn-primary-soft); color:var(--nn-primary);">Start →</a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            @if($content->isEmpty() && $exercises->isEmpty())
                <p class="text-muted text-center py-4">{{ $cultureName }} content for your current stage coming soon!</p>
            @endif
        </div>
    </div>
</div>
@endsection
