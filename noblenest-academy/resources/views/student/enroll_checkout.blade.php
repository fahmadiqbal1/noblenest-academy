@extends('layouts.student')

@section('title', ($course->isFree() ? 'Enrol for Free' : 'Complete Enrolment') . ' — Noble Nest Academy')

@section('content')

{{-- ── Back link ── --}}
<x-ui.button variant="ghost" href="{{ route('marketplace.show', $course->slug) }}" icon="arrow-left" size="sm" class="mb-5">
    Back to Course
</x-ui.button>

{{-- ── Error flash ── --}}
@if(session('error'))
    <x-ui.alert tone="danger" class="mb-4">{{ session('error') }}</x-ui.alert>
@endif

<div class="max-w-lg mx-auto">
    <x-ui.card variant="clay" padding="lg">

        <h1 class="font-display font-black text-2xl text-[var(--color-text)] mb-1">
            {{ $course->isFree() ? 'Enrol for Free' : 'Complete Enrolment' }}
        </h1>
        <p class="text-[var(--color-text-muted)] mb-5">{{ $course->title }}</p>

        {{-- Course summary card --}}
        <div class="flex items-center gap-3 rounded-[var(--radius-sm)] border-[2px] border-[var(--color-border)] bg-[var(--color-surface)] p-3 mb-6">
            @if($course->thumbnail)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($course->thumbnail) }}"
                     class="w-16 h-16 rounded-[var(--radius-sm)] object-cover shrink-0"
                     alt="{{ $course->title }}" loading="lazy" decoding="async">
            @else
                <div class="w-16 h-16 rounded-[var(--radius-sm)] bg-gradient-to-br from-[var(--color-brand-600)] to-[var(--color-accent)] flex items-center justify-center shrink-0">
                    <x-ui.icon name="book" class="w-7 h-7 text-white/80" aria-hidden="true" />
                </div>
            @endif
            <div class="flex-1 min-w-0">
                <div class="font-semibold text-[var(--color-text)] truncate">{{ $course->title }}</div>
                <div class="text-sm text-[var(--color-text-muted)]">by {{ $course->teacher->name ?? 'Teacher' }}</div>
            </div>
            <div class="font-black text-xl text-emerald-600 shrink-0">
                {{ $course->price > 0 ? '$' . $course->price : 'Free' }}
            </div>
        </div>

        @if($course->isFree())
            {{-- ── Free enrolment form ── --}}
            <form method="POST" action="{{ route('student.enroll', $course->slug) }}">
                @csrf
                <input type="hidden" name="provider" value="free">
                <x-ui.button type="submit" variant="primary" icon="check" size="lg" class="w-full justify-center">
                    Confirm Free Enrolment
                </x-ui.button>
            </form>

        @else
            {{-- ── Paid enrolment form ── --}}
            {{-- NOTE: Payment form — preserve all hidden fields, radio names, and JS submit hook for Stripe integration --}}
            <form method="POST" action="{{ route('student.enroll', $course->slug) }}" id="paymentForm">
                @csrf

                <x-ui.field label="Payment Method" name="provider" class="mb-4">
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-2 rounded-[var(--radius-sm)] border-[2px] border-[var(--color-border)] p-3 cursor-pointer has-[:checked]:border-[var(--color-brand-500)] has-[:checked]:bg-[var(--color-brand-50)] transition-colors">
                            <input type="radio" name="provider" value="stripe" id="payStripe" checked
                                   class="accent-[var(--color-brand-600)]">
                            <x-ui.icon name="credit-card" class="w-4 h-4 text-[var(--color-primary)]" aria-hidden="true" />
                            <span class="text-sm font-semibold text-[var(--color-text)]">Card (Stripe)</span>
                        </label>
                        <label class="flex items-center gap-2 rounded-[var(--radius-sm)] border-[2px] border-[var(--color-border)] p-3 cursor-pointer has-[:checked]:border-[var(--color-brand-500)] has-[:checked]:bg-[var(--color-brand-50)] transition-colors">
                            <input type="radio" name="provider" value="paypal" id="payPaypal"
                                   class="accent-[var(--color-brand-600)]">
                            <x-ui.icon name="external-link" class="w-4 h-4 text-[var(--color-primary)]" aria-hidden="true" />
                            <span class="text-sm font-semibold text-[var(--color-text)]">PayPal</span>
                        </label>
                    </div>
                </x-ui.field>

                <div id="stripeFields" class="space-y-4 mb-5">
                    <x-ui.field label="Card Number" name="card_number">
                        <x-ui.input
                            type="text"
                            name="card_number"
                            placeholder="4242 4242 4242 4242"
                            maxlength="19"
                            autocomplete="cc-number"
                        />
                        <p class="text-xs text-[var(--color-text-muted)] mt-1">Demo only — enter any 16-digit number.</p>
                    </x-ui.field>

                    <div class="grid grid-cols-2 gap-3">
                        <x-ui.field label="Expiry" name="expiry">
                            <x-ui.input
                                type="text"
                                name="expiry"
                                placeholder="MM / YY"
                                maxlength="7"
                                autocomplete="cc-exp"
                            />
                        </x-ui.field>
                        <x-ui.field label="CVC" name="cvc">
                            <x-ui.input
                                type="text"
                                name="cvc"
                                placeholder="123"
                                maxlength="3"
                                autocomplete="cc-csc"
                            />
                        </x-ui.field>
                    </div>
                </div>

                {{-- Simulated payment reference — preserved for controller --}}
                <input type="hidden" name="payment_ref" value="DEMO-{{ strtoupper(Str::random(10)) }}">

                <x-ui.button
                    type="submit"
                    variant="primary"
                    icon="shield"
                    size="lg"
                    class="w-full justify-center"
                    id="paySubmitBtn"
                    onclick="this.innerHTML='<span class=\'animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full me-2\'></span>Processing...';this.disabled=true;this.form.submit();"
                >
                    Pay ${{ $course->price }} {{ $course->currency }}
                </x-ui.button>

                <p class="text-center text-xs text-[var(--color-text-muted)] mt-3 flex items-center justify-center gap-1">
                    <x-ui.icon name="shield" class="w-3.5 h-3.5" aria-hidden="true" />Secure payment &middot; 30-day refund policy
                </p>
            </form>
        @endif

    </x-ui.card>
</div>

@endsection
