@extends('layouts.app')

@section('content')
@php
    $user = auth()->user();
    $role = $user->role ?? null;
    $theme = session('theme', $role === 'Parent' ? 'professional' : ($role === 'Student' ? 'playful' : 'professional'));
    $isPlayful = $theme === 'playful';
    $tier = app(\App\Services\PricingService::class)->resolve(request());
    $monthlyPrice = $tier['price_monthly'] ?? 4.99;
    $yearlyPrice = $tier['price_yearly'] ?? 47.90;
    $monthlyCents = (int)($monthlyPrice * 100);
    $yearlyCents = (int)($yearlyPrice * 100);
    $currency = $tier['currency_code'] ?? 'USD';
    $region = $tier['region_label'] ?? 'Global';
    $yearlySavings = round((1 - ($yearlyPrice / ($monthlyPrice * 12))) * 100);
@endphp
<div class="container py-5">
    <h2 class="mb-2 text-center {{ $isPlayful ? 'playful-font text-pink' : 'professional-font text-primary' }}">
        <i class="bi bi-heart-fill"></i> Invest in Your Child's Future
    </h2>
    <p class="text-center text-muted mb-4">Weekly learning packs adapted for your child's age — fresh content every week.</p>

    @if(session('error'))
        <div class="alert alert-danger text-center">{{ session('error') }}</div>
    @endif
    @if(session('status'))
        <div class="alert alert-success text-center">{{ session('status') }}</div>
    @endif

    <div class="row row-cols-1 row-cols-md-2 g-4 mb-4 justify-content-center" style="max-width: 700px; margin: 0 auto;">
        {{-- Monthly Plan --}}
        <div class="col">
            <div class="card h-100 shadow-sm border-0 {{ $isPlayful ? 'playful-font' : 'professional-font' }}">
                <div class="card-body text-center">
                    <h4 class="card-title mb-2"><i class="bi bi-calendar-month text-primary"></i> Monthly</h4>
                    <p class="card-text small text-muted">All children, weekly drip packs, cancel anytime</p>
                    <div class="display-6 fw-bold mb-1">${{ number_format($monthlyPrice, 2) }}</div>
                    <div class="text-muted small mb-3">per month</div>
                    <form method="POST" action="{{ route('checkout.stripe') }}" class="d-grid gap-2">
                        @csrf
                        <input type="hidden" name="amount" value="{{ $monthlyCents }}">
                        <input type="hidden" name="currency" value="{{ $currency }}">
                        <input type="hidden" name="description" value="Noble Nest Monthly Plan">
                        <button type="submit" class="btn btn-lg btn-primary"><i class="bi bi-credit-card"></i> Pay with Stripe</button>
                    </form>
                    <form method="POST" action="{{ route('checkout.paypal') }}" class="d-grid gap-2 mt-2">
                        @csrf
                        <input type="hidden" name="amount" value="{{ $monthlyCents }}">
                        <input type="hidden" name="currency" value="{{ $currency }}">
                        <input type="hidden" name="description" value="Noble Nest Monthly Plan">
                        <button type="submit" class="btn btn-lg btn-outline-info"><i class="bi bi-paypal"></i> Pay with PayPal</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Annual Plan --}}
        <div class="col">
            <div class="card h-100 shadow border-2 border-primary {{ $isPlayful ? 'playful-font' : 'professional-font' }}">
                <div class="card-body text-center">
                    <h4 class="card-title mb-2"><i class="bi bi-calendar-heart text-danger"></i> Annual</h4>
                    <p class="card-text small text-muted">Everything in Monthly + save {{ $yearlySavings }}%</p>
                    <div class="display-6 fw-bold mb-1">${{ number_format($yearlyPrice, 2) }}</div>
                    <div class="text-muted small mb-3">per year (~${{ number_format($yearlyPrice / 12, 2) }}/mo)</div>
                    <form method="POST" action="{{ route('checkout.stripe') }}" class="d-grid gap-2">
                        @csrf
                        <input type="hidden" name="amount" value="{{ $yearlyCents }}">
                        <input type="hidden" name="currency" value="{{ $currency }}">
                        <input type="hidden" name="description" value="Noble Nest Annual Plan">
                        <button type="submit" class="btn btn-lg btn-primary"><i class="bi bi-credit-card"></i> Pay with Stripe</button>
                    </form>
                    <form method="POST" action="{{ route('checkout.paypal') }}" class="d-grid gap-2 mt-2">
                        @csrf
                        <input type="hidden" name="amount" value="{{ $yearlyCents }}">
                        <input type="hidden" name="currency" value="{{ $currency }}">
                        <input type="hidden" name="description" value="Noble Nest Annual Plan">
                        <button type="submit" class="btn btn-lg btn-outline-info"><i class="bi bi-paypal"></i> Pay with PayPal</button>
                    </form>
                </div>
                <div class="card-footer text-center bg-primary text-white fw-bold">Best Value — Save {{ $yearlySavings }}%</div>
            </div>
        </div>
    </div>

    <div class="text-center text-muted small mt-3 mb-4">
        <i class="bi bi-geo-alt"></i> Pricing for <strong>{{ $region }}</strong> region. All prices in {{ $currency }}.
    </div>

    <div class="text-center mt-2">
        <a href="/" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back to Home</a>
    </div>
</div>
@endsection
@section('scripts')
<script>
// Animated feedback for payment buttons
[...document.querySelectorAll('form[action*="checkout"] button')].forEach(btn => {
    btn.addEventListener('click', function() {
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
        btn.disabled = true;
        setTimeout(()=>btn.disabled=false, 6000);
    });
});
</script>
@endsection
