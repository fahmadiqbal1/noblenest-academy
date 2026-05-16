@extends('layouts.practitioner')

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
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h2 class="font-bold mb-1"><x-ui.icon name="shield-check" class="me-2" />Practitioner Dashboard</h2>
            <p class="mb-0 opacity-75">Welcome back, {{ auth()->user()->name }}. {{ $profile->formattedLicenseType() }} — {{ $profile->specialization }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('practitioner.reviews.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-gray-100 text-gray-900 hover:bg-gray-200"><x-ui.icon name="clipboard-check" class="me-1" /> Review Queue</a>
            <a href="{{ route('practitioner.profile.edit') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-gray-200 text-gray-100 hover:bg-gray-100 hover:text-gray-900"><x-ui.icon name="pencil" class="me-1" /> Edit Profile</a>
        </div>
    </div>
</div>

<div class="flex flex-wrap gap-4 mb-4">
    <div class="md:w-4/12">
        <div class="prac-stat">
            <div class="prac-stat__number" style="color: var(--nn-primary);">{{ $reviewedCount }}</div>
            <div class="prac-stat__label">Total Reviews</div>
        </div>
    </div>
    <div class="md:w-4/12">
        <div class="prac-stat">
            <div class="prac-stat__number" style="color: var(--nn-success);">{{ $approvedCount }}</div>
            <div class="prac-stat__label">Approved</div>
        </div>
    </div>
    <div class="md:w-4/12">
        <div class="prac-stat">
            <div class="prac-stat__number" style="color: var(--nn-accent);">{{ $pendingQueue }}</div>
            <div class="prac-stat__label">Pending Queue</div>
        </div>
    </div>
</div>

@if($pendingQueue > 0)
<div class="glass-panel p-4">
    <h5 class="font-bold mb-3"><x-ui.icon name="hourglass" class="me-2" />Content Awaiting Review</h5>
    <p class="text-[var(--color-text-muted)]">There {{ $pendingQueue === 1 ? 'is' : 'are' }} <strong>{{ $pendingQueue }}</strong> piece{{ $pendingQueue !== 1 ? 's' : '' }} of maternal wellness content waiting for professional review.</p>
    <a href="{{ route('practitioner.reviews.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-violet-600 text-white hover:bg-violet-700">
        <x-ui.icon name="arrow-right" class="me-1" /> Go to Review Queue
    </a>
</div>
@else
<div class="glass-panel p-4 text-center">
    <x-ui.icon name="check-circle" class="text-emerald-600" style="font-size: 3rem;" />
    <h5 class="font-bold mt-3">All Caught Up!</h5>
    <p class="text-[var(--color-text-muted)]">No pending content needs your review right now.</p>
</div>
@endif
@endsection
