@extends('layouts.app')

@section('title', 'Newborn Care — Maternal Wellness')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-3 d-none d-lg-block">
            @include('maternal.partials.sidebar')
        </div>
        <div class="col-lg-9">
            <h3 class="mb-2" style="font-family:'Baloo 2',sans-serif; color:var(--nn-text);">
                <i class="bi bi-emoji-smile me-2" style="color:#F59E0B;"></i> Newborn Care & Training
            </h3>
            <p class="text-muted mb-4">Everything you need to know about caring for your newborn — from bathing and swaddling to grooming and sleep training.</p>

            <div class="row g-3">
                @forelse($content as $item)
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 border-0" style="background:rgba(255,255,255,0.82); border-radius:1rem; box-shadow:4px 4px 12px rgba(124,58,237,0.06);">
                        @if($item->thumbnail_url)
                            <img src="{{ $item->thumbnail_url }}" class="card-img-top" alt="{{ $item->title }}" style="border-radius:1rem 1rem 0 0; height:140px; object-fit:cover;">
                        @endif
                        <div class="card-body p-3">
                            <h6 class="mb-1" style="font-family:'Baloo 2',sans-serif;">{{ $item->title }}</h6>
                            <p class="small text-muted mb-2">{{ Str::limit($item->benefit_explanation, 100) }}</p>
                            @if($item->skills_improved)
                                <div class="d-flex flex-wrap gap-1 mb-2">
                                    @foreach(array_slice($item->skills_improved, 0, 2) as $skill)
                                        <span class="badge rounded-pill" style="background:#ECFDF5; color:#065F46; font-size:0.65rem;">{{ $skill }}</span>
                                    @endforeach
                                </div>
                            @endif
                            <a href="{{ route('maternal.content.show', $item) }}" class="btn btn-sm rounded-pill fw-semibold" style="background:var(--nn-primary-soft); color:var(--nn-primary);">
                                Learn More <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <p class="text-muted text-center py-4">Newborn care content coming soon.</p>
                </div>
                @endforelse
            </div>

            <div class="mt-4">{{ $content->links() }}</div>
        </div>
    </div>
</div>
@endsection
