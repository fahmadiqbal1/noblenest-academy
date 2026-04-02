@extends('layouts.app')

@section('meta_title', 'Apply for a Scholarship – NobleNest Global Academy')
@section('meta_description', 'Apply for a NobleNest Academy scholarship. We offer need-based and merit scholarships globally to ensure every child has access to world-class education.')

@section('content')
<div class="container py-5" style="max-width:680px">
    <div class="text-center mb-5">
        <div style="font-size:3rem;line-height:1">🎓</div>
        <h1 class="fw-bold mt-2 mb-1">Apply for a Scholarship</h1>
        <p class="text-muted">We believe every child deserves quality education regardless of financial circumstance.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-3 shadow-sm">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger rounded-3">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4 p-md-5">
            <form method="POST" action="{{ route('scholarship.apply') }}" autocomplete="on">
                @csrf

                <div class="mb-4">
                    <label class="form-label fw-semibold">Parent / Guardian Name <span class="text-danger">*</span></label>
                    <input type="text" name="parent_name" class="form-control rounded-3 @error('parent_name') is-invalid @enderror"
                           value="{{ old('parent_name', auth()->user()->name ?? '') }}" required>
                    @error('parent_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control rounded-3 @error('email') is-invalid @enderror"
                           value="{{ old('email', auth()->user()->email ?? '') }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Child's Name <span class="text-danger">*</span></label>
                    <input type="text" name="child_name" class="form-control rounded-3 @error('child_name') is-invalid @enderror"
                           value="{{ old('child_name') }}" required>
                    @error('child_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Child's Age (months) <span class="text-danger">*</span></label>
                    <input type="number" name="child_age_months" min="0" max="180" class="form-control rounded-3 @error('child_age_months') is-invalid @enderror"
                           value="{{ old('child_age_months') }}" required>
                    @error('child_age_months')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Country <span class="text-danger">*</span></label>
                    <input type="text" name="country" class="form-control rounded-3 @error('country') is-invalid @enderror"
                           value="{{ old('country') }}" required placeholder="e.g. Nigeria, Pakistan, Indonesia">
                    @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Why does your family need financial assistance?</label>
                    <textarea name="need_statement" rows="4" class="form-control rounded-3 @error('need_statement') is-invalid @enderror"
                              placeholder="Please describe your household situation in a few sentences.">{{ old('need_statement') }}</textarea>
                    @error('need_statement')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Why should your child receive this scholarship?</label>
                    <textarea name="merit_statement" rows="4" class="form-control rounded-3 @error('merit_statement') is-invalid @enderror"
                              placeholder="Tell us about your child's curiosity, progress, or learning goals.">{{ old('merit_statement') }}</textarea>
                    @error('merit_statement')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="agree_terms" id="agree_terms" required>
                    <label class="form-check-label small" for="agree_terms">
                        I confirm this information is accurate and I agree to the
                        <a href="{{ route('terms') }}" target="_blank">Terms of Service</a>.
                    </label>
                </div>

                <button type="submit" class="btn btn-dark rounded-pill px-5 py-2 fw-bold">
                    Submit Application
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
