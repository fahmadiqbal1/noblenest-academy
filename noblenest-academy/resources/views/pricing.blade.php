@extends('layouts.app')

@section('title', 'Pricing — Noble Nest Academy')

@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <div class="fs-1 mb-2">💎</div>
        <h1 class="fw-bold" style="font-family:'Baloo 2',sans-serif; color:var(--nn-text, #1E1B4B);">Simple, Fair Pricing</h1>
        <p class="lead text-muted">Automatically adjusted for your region. Learning should be accessible everywhere.</p>
    </div>

    <div class="row justify-content-center g-4">
        {{-- Free Tier --}}
        <div class="col-md-4">
            <div class="h-100 p-4" style="background:rgba(255,255,255,0.82); border-radius:1.25rem; border:2px solid rgba(124,58,237,0.10); box-shadow:8px 8px 16px rgba(124,58,237,0.06), -4px -4px 12px rgba(255,255,255,0.5);">
                <h5 class="text-muted text-uppercase small mb-1" style="letter-spacing:0.1em;">Free</h5>
                <div class="d-flex align-items-end gap-1 mb-1">
                    <span class="display-5 fw-bold" style="font-family:'Baloo 2',sans-serif; color:var(--nn-text, #1E1B4B);">$0</span>
                </div>
                <p class="text-muted small mb-4">Always free, forever</p>
                <ul class="list-unstyled mb-4">
                    <li class="mb-2">✅ 7 activities per module</li>
                    <li class="mb-2">✅ 1 child profile</li>
                    <li class="mb-2">✅ Progress tracking</li>
                    <li class="mb-2">✅ Share cards</li>
                    <li class="mb-2 text-muted">❌ Weekly content packs</li>
                    <li class="mb-2 text-muted">❌ Full module access</li>
                </ul>
                @auth
                <a href="{{ route('parent.dashboard') }}" class="btn w-100 rounded-pill" style="background:rgba(124,58,237,0.08);color:var(--nn-primary, #7C3AED);border:2px solid rgba(124,58,237,0.15);">Current Plan</a>
                @else
                <a href="{{ route('register') }}" class="btn w-100 rounded-pill" style="background:rgba(124,58,237,0.08);color:var(--nn-primary, #7C3AED);border:2px solid rgba(124,58,237,0.15);">Get Started Free</a>
                @endauth
            </div>
        </div>

        {{-- Monthly Tier (Featured) --}}
        <div class="col-md-4">
            <div class="h-100 p-4 position-relative text-white" style="background:linear-gradient(135deg, #7C3AED, #A78BFA 60%, #C4B5FD); border-radius:1.25rem; box-shadow:12px 12px 24px rgba(124,58,237,0.15), -4px -4px 12px rgba(255,255,255,0.3);">
                <span class="badge rounded-pill position-absolute top-0 end-0 m-3 px-3 py-2" style="background:rgba(255,255,255,0.25);backdrop-filter:blur(4px);">⭐ Most Popular</span>
                <h5 class="text-uppercase small mb-1" style="letter-spacing:0.1em;opacity:0.9;">Monthly</h5>
                <div class="d-flex align-items-end gap-1 mb-1">
                    <span class="display-5 fw-bold" style="font-family:'Baloo 2',sans-serif;">${{ $tier['price_monthly'] }}</span>
                    <span class="mb-2" style="opacity:0.8;">/mo</span>
                </div>
                <p class="small mb-1" style="opacity:0.85;">New activities unlocked every week</p>
                <p class="badge mb-4 px-2 py-1" style="background:rgba(255,255,255,0.15);font-size:0.72rem;">{{ $tier['region_label'] }} pricing · {{ $tier['currency_code'] }}</p>
                <ul class="list-unstyled mb-4">
                    <li class="mb-2">✅ Weekly content packs (5 activities/week)</li>
                    <li class="mb-2">✅ Up to 5 child profiles</li>
                    <li class="mb-2">✅ All modules &amp; subjects</li>
                    <li class="mb-2">✅ Age-adaptive content</li>
                    <li class="mb-2">✅ Progress milestones</li>
                    <li class="mb-2">✅ Priority support</li>
                </ul>
                <a href="{{ route('checkout') }}?plan=monthly" class="btn w-100 fw-semibold rounded-pill" style="background:rgba(255,255,255,0.95);color:#7C3AED;border:none;">
                    Subscribe Monthly
                </a>
            </div>
        </div>

        {{-- Annual Tier --}}
        <div class="col-md-4">
            <div class="h-100 p-4 position-relative" style="background:rgba(255,255,255,0.82); border-radius:1.25rem; border:2px solid rgba(16,185,129,0.15); box-shadow:8px 8px 16px rgba(124,58,237,0.06), -4px -4px 12px rgba(255,255,255,0.5);">
                <span class="badge rounded-pill bg-success position-absolute top-0 end-0 m-3 px-3 py-2">Save 20%</span>
                <h5 class="text-uppercase small mb-1" style="letter-spacing:0.1em;color:#059669;">Annual</h5>
                <div class="d-flex align-items-end gap-1 mb-1">
                    <span class="display-5 fw-bold" style="font-family:'Baloo 2',sans-serif; color:var(--nn-text, #1E1B4B);">${{ $tier['price_yearly'] }}</span>
                    <span class="text-muted mb-2">/yr</span>
                </div>
                <p class="text-muted small mb-1">That's ${{ number_format($tier['price_yearly'] / 12, 2) }}/month</p>
                <p class="badge bg-light text-muted border mb-4" style="font-size:0.72rem;">{{ $tier['region_label'] }} pricing · {{ $tier['currency_code'] }}</p>
                <ul class="list-unstyled mb-4">
                    <li class="mb-2">✅ Everything in Monthly</li>
                    <li class="mb-2">✅ 20% savings</li>
                    <li class="mb-2">✅ Up to 5 child profiles</li>
                    <li class="mb-2">✅ All modules &amp; subjects</li>
                    <li class="mb-2">✅ Priority support</li>
                    <li class="mb-2">✅ Offline downloads</li>
                </ul>
                <a href="{{ route('checkout') }}?plan=annual" class="btn w-100 fw-semibold rounded-pill text-white" style="background:#10B981;border:none;">
                    Subscribe Annually
                </a>
            </div>
        </div>
    </div>

    <div class="text-center mt-5">
        <p class="text-muted small">
            Prices shown are for your region ({{ $tier['region_label'] }}).
            All plans include a 7-day money-back guarantee.
        </p>
    </div>
</div>
@endsection
