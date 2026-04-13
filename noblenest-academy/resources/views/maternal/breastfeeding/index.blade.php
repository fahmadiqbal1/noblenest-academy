@extends('layouts.app')

@section('title', 'Breastfeeding Education — Maternal Wellness')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-3 d-none d-lg-block">
            @include('maternal.partials.sidebar')
        </div>
        <div class="col-lg-9">
            <h3 class="mb-2" style="font-family:'Baloo 2',sans-serif; color:var(--nn-text);">
                <i class="bi bi-heart-pulse me-2" style="color:#EC4899;"></i> Breastfeeding Education
            </h3>
            <p class="text-muted mb-4">Evidence-based guidance on breastfeeding — its profound benefits for both mother and child, including reduced breast cancer risk, immune strength transfer, and bonding.</p>

            {{-- Key benefits banner --}}
            <div class="card border-0 mb-4" style="background:linear-gradient(135deg, #FDF2F8, #FCE7F3); border-radius:1rem;">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-3">Why Breastfeed?</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="mb-2" style="font-size:1.5rem;">🛡️</div>
                                <h6 class="small fw-semibold">Immune Transfer</h6>
                                <p class="small text-muted mb-0">Antibodies pass directly to baby, building immunity from day one.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="mb-2" style="font-size:1.5rem;">💖</div>
                                <h6 class="small fw-semibold">Reduced Cancer Risk</h6>
                                <p class="small text-muted mb-0">Every 12 months of breastfeeding reduces breast cancer risk by ~4.3%.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="mb-2" style="font-size:1.5rem;">🤱</div>
                                <h6 class="small fw-semibold">Bonding</h6>
                                <p class="small text-muted mb-0">Oxytocin release strengthens the mother-child emotional bond.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                @forelse($content as $item)
                <div class="col-md-6">
                    <div class="card h-100 border-0" style="background:rgba(255,255,255,0.82); border-radius:1rem; box-shadow:4px 4px 12px rgba(124,58,237,0.06);">
                        @if($item->thumbnail_url)
                            <img src="{{ $item->thumbnail_url }}" class="card-img-top" alt="{{ $item->title }}" style="border-radius:1rem 1rem 0 0; height:140px; object-fit:cover;">
                        @endif
                        <div class="card-body p-3">
                            <h6 class="mb-1" style="font-family:'Baloo 2',sans-serif;">{{ $item->title }}</h6>
                            <p class="small text-muted mb-2">{{ Str::limit($item->benefit_explanation, 100) }}</p>
                            <a href="{{ route('maternal.content.show', $item) }}" class="btn btn-sm rounded-pill fw-semibold" style="background:var(--nn-primary-soft); color:var(--nn-primary);">
                                Read More <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <p class="text-muted text-center py-4">Breastfeeding guides coming soon.</p>
                </div>
                @endforelse
            </div>

            <div class="mt-4">{{ $content->links() }}</div>
        </div>
    </div>
</div>
@endsection
