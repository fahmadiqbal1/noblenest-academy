@extends('layouts.app')

@section('content')
@php
    $user = auth()->user();
    $role = $user->role ?? null;
    $theme = session('theme', $role === 'Parent' ? 'professional' : ($role === 'Student' ? 'playful' : 'professional'));
    $isPlayful = $theme === 'playful';
@endphp
<div class="container py-5">
    <h2 class="mb-4 text-center {{ $isPlayful ? 'playful-font text-pink' : 'professional-font text-primary' }}">
        <i class="bi {{ $isPlayful ? 'bi-gift' : 'bi-credit-card' }}"></i> {{ I18n::get('choose_plan') ?? 'Choose Your Plan' }}
    </h2>
    @if(session('error'))
        <div class="alert alert-danger text-center">{{ session('error') }}</div>
    @endif
    @if(session('success'))
        <div class="alert alert-success text-center">{{ session('success') }}</div>
    @endif
    <div class="row row-cols-1 row-cols-md-3 g-4 mb-4 justify-content-center">
        <div class="col">
            <div class="card h-100 shadow-sm border-0 {{ $isPlayful ? 'playful-font' : 'professional-font' }}">
                <div class="card-body text-center">
                    <h4 class="card-title mb-2"><i class="bi bi-person-circle text-primary"></i> Individual</h4>
                    <p class="card-text">1 child, all features</p>
                    <div class="display-6 fw-bold mb-2">$10 <span class="fs-6">/mo</span></div>
                    <form method="POST" action="{{ route('checkout.stripe') }}" class="d-grid gap-2">
                        @csrf
                        <input type="hidden" name="amount" value="1000">
                        <input type="hidden" name="currency" value="USD">
                        <input type="hidden" name="description" value="Noble Nest Individual Plan">
                        <button type="submit" class="btn btn-lg btn-primary"><i class="bi bi-credit-card"></i> Pay with Stripe</button>
                    </form>
                    <form method="POST" action="{{ route('checkout.paypal') }}" class="d-grid gap-2 mt-2">
                        @csrf
                        <input type="hidden" name="amount" value="1000">
                        <input type="hidden" name="currency" value="USD">
                        <input type="hidden" name="description" value="Noble Nest Individual Plan">
                        <button type="submit" class="btn btn-lg btn-info"><i class="bi bi-paypal"></i> Pay with PayPal</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 shadow border-2 border-primary {{ $isPlayful ? 'playful-font' : 'professional-font' }}">
                <div class="card-body text-center">
                    <h4 class="card-title mb-2"><i class="bi bi-people-fill text-success"></i> Family</h4>
                    <p class="card-text">Up to 3 children, family dashboard</p>
                    <div class="display-6 fw-bold mb-2">$25 <span class="fs-6">/mo</span></div>
                    <form method="POST" action="{{ route('checkout.stripe') }}" class="d-grid gap-2">
                        @csrf
                        <input type="hidden" name="amount" value="2500">
                        <input type="hidden" name="currency" value="USD">
                        <input type="hidden" name="description" value="Noble Nest Family Plan">
                        <button type="submit" class="btn btn-lg btn-primary"><i class="bi bi-credit-card"></i> Pay with Stripe</button>
                    </form>
                    <form method="POST" action="{{ route('checkout.paypal') }}" class="d-grid gap-2 mt-2">
                        @csrf
                        <input type="hidden" name="amount" value="2500">
                        <input type="hidden" name="currency" value="USD">
                        <input type="hidden" name="description" value="Noble Nest Family Plan">
                        <button type="submit" class="btn btn-lg btn-info"><i class="bi bi-paypal"></i> Pay with PayPal</button>
                    </form>
                </div>
                <div class="card-footer text-center bg-primary text-white fw-bold">Most Popular</div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 shadow-sm border-0 {{ $isPlayful ? 'playful-font' : 'professional-font' }}">
                <div class="card-body text-center">
                    <h4 class="card-title mb-2"><i class="bi bi-calendar-heart text-danger"></i> Annual</h4>
                    <p class="card-text">1 child, save 20%</p>
                    <div class="display-6 fw-bold mb-2">$99 <span class="fs-6">/yr</span></div>
                    <form method="POST" action="{{ route('checkout.stripe') }}" class="d-grid gap-2">
                        @csrf
                        <input type="hidden" name="amount" value="9900">
                        <input type="hidden" name="currency" value="USD">
                        <input type="hidden" name="description" value="Noble Nest Annual Plan">
                        <button type="submit" class="btn btn-lg btn-primary"><i class="bi bi-credit-card"></i> Pay with Stripe</button>
                    </form>
                    <form method="POST" action="{{ route('checkout.paypal') }}" class="d-grid gap-2 mt-2">
                        @csrf
                        <input type="hidden" name="amount" value="9900">
                        <input type="hidden" name="currency" value="USD">
                        <input type="hidden" name="description" value="Noble Nest Annual Plan">
                        <button type="submit" class="btn btn-lg btn-info"><i class="bi bi-paypal"></i> Pay with PayPal</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="text-center mt-4">
        <a href="/" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back to Home</a>
        <a href="#" class="btn btn-outline-success ms-2"><i class="bi bi-gift"></i> Gift a Subscription</a>
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
