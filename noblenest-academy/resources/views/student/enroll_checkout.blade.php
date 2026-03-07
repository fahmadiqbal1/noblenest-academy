@extends('layouts.app')

@section('content')
<div class="container py-4">
    <a href="{{ route('marketplace.show', $course->slug) }}" class="btn btn-sm btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Back to Course
    </a>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow border-0">
                <div class="card-body p-4">
                    <h2 class="fw-bold mb-1">{{ $course->isFree() ? 'Enrol for Free' : 'Complete Enrolment' }}</h2>
                    <p class="text-muted mb-4">{{ $course->title }}</p>

                    <div class="d-flex align-items-center gap-3 mb-4 p-3 bg-light rounded">
                        @if($course->thumbnail)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($course->thumbnail) }}" style="width:64px;height:64px;object-fit:cover;border-radius:8px">
                        @else
                            <div style="width:64px;height:64px;border-radius:8px;background:linear-gradient(135deg,#6f42c1,#0d6efd)" class="d-flex align-items-center justify-content-center">
                                <i class="bi bi-book-half text-white fs-3"></i>
                            </div>
                        @endif
                        <div>
                            <div class="fw-semibold">{{ $course->title }}</div>
                            <div class="text-muted small">by {{ $course->teacher->name ?? 'Teacher' }}</div>
                        </div>
                        <div class="ms-auto fs-4 fw-bold text-success">
                            {{ $course->price > 0 ? '$' . $course->price : 'Free' }}
                        </div>
                    </div>

                    @if($course->isFree())
                        <form method="POST" action="{{ route('student.enroll', $course->slug) }}">
                            @csrf
                            <input type="hidden" name="provider" value="free">
                            <button type="submit" class="btn btn-success btn-lg w-100">
                                <i class="bi bi-check-circle"></i> Confirm Free Enrolment
                            </button>
                        </form>
                    @else
                        {{-- Simulated payment form (replace with Stripe.js in production) --}}
                        <form method="POST" action="{{ route('student.enroll', $course->slug) }}" id="paymentForm">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Payment Method</label>
                                <div class="d-flex gap-2">
                                    <div class="form-check border rounded p-3 flex-fill">
                                        <input class="form-check-input" type="radio" name="provider" value="stripe" id="payStripe" checked>
                                        <label class="form-check-label" for="payStripe"><i class="bi bi-credit-card"></i> Card (Stripe)</label>
                                    </div>
                                    <div class="form-check border rounded p-3 flex-fill">
                                        <input class="form-check-input" type="radio" name="provider" value="paypal" id="payPaypal">
                                        <label class="form-check-label" for="payPaypal"><i class="bi bi-paypal"></i> PayPal</label>
                                    </div>
                                </div>
                            </div>

                            <div id="stripeFields">
                                <div class="mb-3">
                                    <label class="form-label">Card Number</label>
                                    <input type="text" class="form-control" placeholder="4242 4242 4242 4242" maxlength="19" pattern="[\d ]{13,19}">
                                    <div class="form-text">Demo only — enter any 16-digit number.</div>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col">
                                        <label class="form-label">Expiry</label>
                                        <input type="text" class="form-control" placeholder="MM / YY" maxlength="7">
                                    </div>
                                    <div class="col">
                                        <label class="form-label">CVC</label>
                                        <input type="text" class="form-control" placeholder="123" maxlength="3">
                                    </div>
                                </div>
                            </div>

                            {{-- Simulated payment reference --}}
                            <input type="hidden" name="payment_ref" value="DEMO-{{ strtoupper(Str::random(10)) }}">

                            <button type="submit" class="btn btn-primary btn-lg w-100" onclick="this.innerHTML='<span class=\'spinner-border spinner-border-sm me-2\'></span>Processing...';this.disabled=true;this.form.submit();">
                                <i class="bi bi-lock-fill"></i> Pay ${{ $course->price }} {{ $course->currency }}
                            </button>
                            <p class="text-center text-muted small mt-2">
                                <i class="bi bi-shield-lock"></i> Secure payment · 30-day refund policy
                            </p>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
