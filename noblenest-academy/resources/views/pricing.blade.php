@extends('layouts.app')

@section('title', 'Pricing — Noble Nest Academy')

@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold">Simple, Fair Pricing</h1>
        <p class="lead text-muted">Automatically adjusted for your region. Learning should be accessible everywhere.</p>
    </div>

    <div class="row justify-content-center g-4">
        {{-- Free Tier --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <h5 class="text-muted text-uppercase small tracking-wide mb-1">Free</h5>
                    <div class="d-flex align-items-end gap-1 mb-1">
                        <span class="display-5 fw-bold">$0</span>
                    </div>
                    <p class="text-muted small mb-4">Always free, forever</p>
                    <ul class="list-unstyled mb-4">
                        <li class="mb-2">✅ 30 curated activities</li>
                        <li class="mb-2">✅ 1 child profile</li>
                        <li class="mb-2">✅ Progress tracking</li>
                        <li class="mb-2">✅ Share cards</li>
                        <li class="mb-2 text-muted">❌ Unlimited activities</li>
                        <li class="mb-2 text-muted">❌ Live classes</li>
                    </ul>
                    @auth
                    <a href="{{ route('parent.dashboard') }}" class="btn btn-outline-secondary w-100">Current Plan</a>
                    @else
                    <a href="{{ route('register') }}" class="btn btn-outline-primary w-100">Get Started Free</a>
                    @endauth
                </div>
            </div>
        </div>

        {{-- Premium Tier --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-lg h-100 border-primary" style="border-width: 2px !important;">
                <div class="card-body p-4 position-relative">
                    <span class="badge bg-primary position-absolute top-0 end-0 m-3">Most Popular</span>
                    <h5 class="text-primary text-uppercase small tracking-wide mb-1">Premium</h5>
                    <div class="d-flex align-items-end gap-1 mb-1">
                        <span class="display-5 fw-bold">${{ $tier['price_monthly'] }}</span>
                        <span class="text-muted mb-2">/mo</span>
                    </div>
                    <p class="text-muted small mb-1">or ${{ $tier['price_yearly'] }}/year <span class="text-success fw-semibold">(save 20%)</span></p>
                    <p class="badge bg-light text-muted border mb-4">{{ $tier['region_label'] }} pricing · {{ $tier['currency_code'] }}</p>
                    <ul class="list-unstyled mb-4">
                        <li class="mb-2">✅ Unlimited activities</li>
                        <li class="mb-2">✅ Up to 5 child profiles</li>
                        <li class="mb-2">✅ Live classes access</li>
                        <li class="mb-2">✅ Age-adaptive content</li>
                        <li class="mb-2">✅ Progress milestones</li>
                        <li class="mb-2">✅ Priority support</li>
                        <li class="mb-2">✅ Offline downloads</li>
                    </ul>
                    <a href="{{ route('register') }}?plan=premium" class="btn btn-primary w-100 fw-semibold">
                        Start 7-day Free Trial
                    </a>
                    <p class="text-center text-muted small mt-2">No credit card required</p>
                </div>
            </div>
        </div>

        {{-- School Tier --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <h5 class="text-muted text-uppercase small tracking-wide mb-1">School</h5>
                    <div class="d-flex align-items-end gap-1 mb-1">
                        <span class="display-5 fw-bold">Custom</span>
                    </div>
                    <p class="text-muted small mb-4">For institutions &amp; NGOs</p>
                    <ul class="list-unstyled mb-4">
                        <li class="mb-2">✅ Everything in Premium</li>
                        <li class="mb-2">✅ Multiple classrooms</li>
                        <li class="mb-2">✅ Bulk student accounts</li>
                        <li class="mb-2">✅ Analytics dashboard</li>
                        <li class="mb-2">✅ Scholarship pool</li>
                        <li class="mb-2">✅ Dedicated support</li>
                    </ul>
                    <a href="{{ route('for-schools') }}" class="btn btn-outline-secondary w-100">Contact Sales</a>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mt-5">
        <p class="text-muted">
            Need a scholarship? <a href="{{ route('for-schools') }}">We offer free access for qualifying families</a>.
        </p>
    </div>
</div>
@endsection
