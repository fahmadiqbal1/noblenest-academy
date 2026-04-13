@extends('layouts.app')

@section('title', 'Wellness Content — Maternal Wellness')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-3 d-none d-lg-block">
            @include('maternal.partials.sidebar')
        </div>
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0" style="font-family:'Baloo 2',sans-serif; color:var(--nn-text);">
                    <i class="bi bi-collection-play me-2" style="color:#7C3AED;"></i> Wellness Content
                </h3>
            </div>

            {{-- Filters --}}
            <form method="GET" class="card border-0 mb-4 p-3" style="background:rgba(255,255,255,0.82); border-radius:1rem;">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Category</label>
                        <select name="category" class="form-select form-select-sm rounded-3">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $cat)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Culture</label>
                        <select name="culture" class="form-select form-select-sm rounded-3">
                            <option value="">All</option>
                            <option value="chinese" {{ request('culture') === 'chinese' ? 'selected' : '' }}>Chinese</option>
                            <option value="japanese" {{ request('culture') === 'japanese' ? 'selected' : '' }}>Japanese</option>
                            <option value="ayurvedic" {{ request('culture') === 'ayurvedic' ? 'selected' : '' }}>Ayurvedic</option>
                            <option value="general" {{ request('culture') === 'general' ? 'selected' : '' }}>General</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Search</label>
                        <input type="text" name="q" class="form-control form-control-sm rounded-3" value="{{ request('q') }}" placeholder="Search...">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-sm w-100 rounded-pill fw-semibold" style="background:var(--nn-primary); color:#fff;">Filter</button>
                    </div>
                </div>
            </form>

            {{-- Content grid --}}
            <div class="row g-3">
                @forelse($contents as $item)
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 border-0" style="background:rgba(255,255,255,0.82); border-radius:1rem; box-shadow:4px 4px 12px rgba(124,58,237,0.06);">
                        @if($item->thumbnail_url)
                            <img src="{{ $item->thumbnail_url }}" class="card-img-top" alt="{{ $item->title }}" style="border-radius:1rem 1rem 0 0; height:140px; object-fit:cover;">
                        @endif
                        <div class="card-body p-3">
                            <div class="d-flex gap-2 mb-2">
                                <span class="badge rounded-pill" style="background:var(--nn-primary-soft); color:var(--nn-primary); font-size:0.7rem;">{{ ucfirst($item->content_type) }}</span>
                                @if($item->cultural_origin)
                                    <span class="badge rounded-pill" style="background:#FEF3C7; color:#92400E; font-size:0.7rem;">{{ ucfirst($item->cultural_origin) }}</span>
                                @endif
                            </div>
                            <h6 class="mb-1" style="font-family:'Baloo 2',sans-serif;">{{ $item->title }}</h6>
                            <p class="small text-muted mb-2">{{ Str::limit($item->benefit_explanation, 80) }}</p>
                            <a href="{{ route('maternal.content.show', $item) }}" class="btn btn-sm rounded-pill fw-semibold" style="background:var(--nn-primary-soft); color:var(--nn-primary);">
                                View <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <p class="text-muted text-center py-4">No content found matching your filters.</p>
                </div>
                @endforelse
            </div>

            <div class="mt-4">{{ $contents->withQueryString()->links() }}</div>
        </div>
    </div>
</div>
@endsection
