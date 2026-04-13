@extends('layouts.app')

@section('title', 'Herbs & Natural Remedies — Maternal Wellness')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-3 d-none d-lg-block">
            @include('maternal.partials.sidebar')
        </div>
        <div class="col-lg-9">
            <h3 class="mb-2" style="font-family:'Baloo 2',sans-serif; color:var(--nn-text);">
                <i class="bi bi-flower1 me-2" style="color:#22C55E;"></i> Herbs & Natural Remedies
            </h3>
            <p class="text-muted mb-4">Safe, traditional herbal remedies from Chinese, Japanese, and Ayurvedic traditions. Always consult your healthcare provider before starting any new herb.</p>

            <div class="row g-3">
                @forelse($herbs as $herb)
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 border-0" style="background:rgba(255,255,255,0.82); border-radius:1rem; box-shadow:4px 4px 12px rgba(124,58,237,0.06);">
                        @if($herb->thumbnail_url)
                            <img src="{{ $herb->thumbnail_url }}" class="card-img-top" alt="{{ $herb->title }}" style="border-radius:1rem 1rem 0 0; height:140px; object-fit:cover;">
                        @endif
                        <div class="card-body p-3">
                            @if($herb->cultural_origin)
                                <span class="badge rounded-pill mb-2" style="background:#FEF3C7; color:#92400E; font-size:0.7rem;">{{ ucfirst($herb->cultural_origin) }}</span>
                            @endif
                            <h6 class="mb-1" style="font-family:'Baloo 2',sans-serif;">{{ $herb->title }}</h6>
                            <p class="small text-muted mb-2">{{ Str::limit($herb->benefit_explanation, 100) }}</p>
                            <a href="{{ route('maternal.content.show', $herb) }}" class="btn btn-sm rounded-pill fw-semibold" style="background:#ECFDF5; color:#065F46;">
                                Learn More <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <p class="text-muted text-center py-4">Herb guides coming soon for your stage.</p>
                </div>
                @endforelse
            </div>

            <div class="mt-4">{{ $herbs->links() }}</div>
        </div>
    </div>
</div>
@endsection
