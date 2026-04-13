@extends('layouts.app')

@section('meta_title', 'Practitioner Dashboard | NobleNest Global Academy')

@section('content')
<style>
    .prac-stat {
        background: var(--nn-surface);
        border: var(--nn-border-w) solid var(--nn-border);
        border-radius: var(--nn-radius);
        box-shadow: var(--nn-shadow);
        padding: 1.5rem;
        text-align: center;
    }
    .prac-stat__number {
        font-size: 2.5rem;
        font-weight: 800;
        font-family: 'Baloo 2', sans-serif;
        line-height: 1;
    }
    .prac-stat__label {
        color: var(--nn-text-muted);
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .prac-header {
        background: linear-gradient(135deg, #059669, #34D399 58%, #6EE7B7);
        border-radius: var(--nn-radius);
        color: #fff;
        padding: 2rem;
        margin-bottom: 1.5rem;
    }
</style>

<div class="prac-header">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-shield-check me-2"></i>Practitioner Dashboard</h2>
            <p class="mb-0 opacity-75">Welcome back, {{ auth()->user()->name }}. {{ $profile->formattedLicenseType() }} — {{ $profile->specialization }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('practitioner.reviews.index') }}" class="btn btn-light"><i class="bi bi-clipboard-check me-1"></i> Review Queue</a>
            <a href="{{ route('practitioner.profile.edit') }}" class="btn btn-outline-light"><i class="bi bi-pencil me-1"></i> Edit Profile</a>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="prac-stat">
            <div class="prac-stat__number" style="color: var(--nn-primary);">{{ $reviewedCount }}</div>
            <div class="prac-stat__label">Total Reviews</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="prac-stat">
            <div class="prac-stat__number" style="color: var(--nn-success);">{{ $approvedCount }}</div>
            <div class="prac-stat__label">Approved</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="prac-stat">
            <div class="prac-stat__number" style="color: var(--nn-accent);">{{ $pendingQueue }}</div>
            <div class="prac-stat__label">Pending Queue</div>
        </div>
    </div>
</div>

@if($pendingQueue > 0)
<div class="glass-panel p-4">
    <h5 class="fw-bold mb-3"><i class="bi bi-hourglass-split me-2"></i>Content Awaiting Review</h5>
    <p class="text-muted">There {{ $pendingQueue === 1 ? 'is' : 'are' }} <strong>{{ $pendingQueue }}</strong> piece{{ $pendingQueue !== 1 ? 's' : '' }} of maternal wellness content waiting for professional review.</p>
    <a href="{{ route('practitioner.reviews.index') }}" class="btn btn-primary">
        <i class="bi bi-arrow-right me-1"></i> Go to Review Queue
    </a>
</div>
@else
<div class="glass-panel p-4 text-center">
    <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
    <h5 class="fw-bold mt-3">All Caught Up!</h5>
    <p class="text-muted">No pending content needs your review right now.</p>
</div>
@endif
@endsection
