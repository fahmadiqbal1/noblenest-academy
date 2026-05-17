@extends('layouts.parent')

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
        <x-ui.icon name="heart" /> {{ __('billing.checkout_title') }}
    </h2>
    <p class="text-center text-[var(--color-text-muted)] mb-4">{{ __('billing.checkout_subtitle') }}</p>

    @if(session('error'))
        <div class="flex items-start gap-3 p-4 rounded-lg border bg-red-50 border-red-200 text-red-800 text-center">{{ session('error') }}</div>
    @endif
    @if(session('status'))
        <div class="flex items-start gap-3 p-4 rounded-lg border bg-emerald-50 border-emerald-200 text-emerald-800 text-center">{{ session('status') }}</div>
    @endif

    <div class="flex flex-wrap row-cols-1 row-cols-md-2 gap-4 mb-4 justify-center" style="max-width: 700px; margin: 0 auto;">
        {{-- Monthly Plan --}}
        <div class="flex-1">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm h-full border-0 {{ $isPlayful ? 'playful-font' : 'professional-font' }}">
                <div class="p-5 text-center">
                    <h4 class="text-lg font-bold mb-2"><x-ui.icon name="calendar" class="text-[var(--color-primary)]" /> {{ __('billing.checkout_monthly') }}</h4>
                    <p class="text-sm text-[var(--color-text-muted)]">{{ __('billing.checkout_monthly_tagline') }}</p>
                    <div class="text-2xl font-bold mb-1">${{ number_format($monthlyPrice, 2) }}</div>
                    <div class="text-[var(--color-text-muted)] text-sm mb-3">{{ __('billing.checkout_per_month') }}</div>
                    <form method="POST" action="{{ route('checkout.stripe') }}" class="grid gap-2">
                        @csrf
                        <input type="hidden" name="plan" value="monthly">
                        <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-5 py-3 text-lg bg-violet-600 text-white hover:bg-violet-700"><x-ui.icon name="credit-card" /> {{ __('billing.checkout_subscribe_monthly') }}</button>
                        <p class="text-xs text-[var(--color-text-muted)] mt-1">{{ __('billing.checkout_monthly_note') }}</p>
                    </form>
                </div>
            </div>
        </div>

        {{-- Annual Plan --}}
        <div class="flex-1">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm h-full shadow border-2 border-primary {{ $isPlayful ? 'playful-font' : 'professional-font' }}">
                <div class="p-5 text-center">
                    <h4 class="text-lg font-bold mb-2"><x-ui.icon name="calendar" class="text-red-600" /> {{ __('billing.checkout_annual') }}</h4>
                    <p class="text-sm text-[var(--color-text-muted)]">{{ __('billing.checkout_annual_tagline', ['percent' => $yearlySavings]) }}</p>
                    <div class="text-2xl font-bold mb-1">${{ number_format($yearlyPrice, 2) }}</div>
                    <div class="text-[var(--color-text-muted)] text-sm mb-3">{{ __('billing.checkout_per_year', ['amount' => '$'.number_format($yearlyPrice / 12, 2)]) }}</div>
                    <form method="POST" action="{{ route('checkout.stripe') }}" class="grid gap-2">
                        @csrf
                        <input type="hidden" name="plan" value="annual">
                        <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-5 py-3 text-lg bg-violet-600 text-white hover:bg-violet-700"><x-ui.icon name="credit-card" /> {{ __('billing.checkout_subscribe_annual') }}</button>
                        <p class="text-xs text-[var(--color-text-muted)] mt-1">{{ __('billing.checkout_annual_note', ['percent' => $yearlySavings]) }}</p>
                    </form>
                </div>
                <div class="px-5 py-3 border-t border-gray-200 text-center bg-[var(--color-primary)] text-white font-bold">{{ __('billing.checkout_best_value', ['percent' => $yearlySavings]) }}</div>
            </div>
        </div>
    </div>

    <div class="text-center text-[var(--color-text-muted)] text-sm mt-3 mb-4">
        <x-ui.icon name="map-pin" /> {!! __('billing.checkout_region_note', ['region' => '<strong>'.e($region).'</strong>', 'currency' => e($currency)]) !!}
    </div>

    <div class="text-center mt-2">
        <a href="/" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-gray-500 text-white hover:bg-gray-600"><x-ui.icon name="arrow-left" /> {{ __('billing.checkout_back_home') }}</a>
    </div>
</div>
@endsection
@section('scripts')
<script>
// Animated feedback for payment buttons
[...document.querySelectorAll('form[action*="checkout"] button')].forEach(btn => {
    btn.addEventListener('click', function() {
        btn.innerHTML = '<span class="inline-block w-6 h-6 border-2 border-current border-t-transparent rounded-full animate-spin w-4 h-4 me-2"></span>{{ __('billing.checkout_processing') }}';
        btn.disabled = true;
        setTimeout(()=>btn.disabled=false, 6000);
    });
});
</script>
@endsection
