@extends('layouts.app')

@section('meta_title', 'Tell Us About Your Child — Noble Nest Academy')

@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center"
     style="background: linear-gradient(135deg, #d4fc79 0%, #96e6a1 100%);">
    <div class="card shadow-lg border-0 rounded-4" style="max-width: 480px; width: 100%;">
        <div class="card-body p-5">

            {{-- Step indicator --}}
            <div class="d-flex justify-content-center gap-2 mb-4">
                <span class="badge rounded-pill bg-success text-white px-3 py-2">✓ Step 1</span>
                <span class="badge rounded-pill bg-warning text-dark px-3 py-2">Step 2 of 3</span>
                <span class="badge rounded-pill bg-light text-muted px-3 py-2">Step 3</span>
            </div>

            <div class="text-center mb-4">
                <div class="fs-1 mb-2">👶</div>
                <h1 class="h3 fw-bold mb-1">Tell us about your child</h1>
                <p class="text-muted small">We use this to personalise the learning journey.</p>
            </div>

            <form action="{{ route('onboarding.step2.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="child_name" class="form-label fw-semibold">Child's first name</label>
                    <input type="text" class="form-control form-control-lg rounded-3 @error('child_name') is-invalid @enderror"
                           id="child_name" name="child_name"
                           value="{{ old('child_name') }}"
                           placeholder="e.g. Aisha"
                           maxlength="100" required>
                    @error('child_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="date_of_birth" class="form-label fw-semibold">Date of birth</label>
                    <input type="date" class="form-control form-control-lg rounded-3 @error('date_of_birth') is-invalid @enderror"
                           id="date_of_birth" name="date_of_birth"
                           value="{{ old('date_of_birth') }}"
                           max="{{ now()->subDay()->format('Y-m-d') }}"
                           min="{{ now()->subYears(11)->format('Y-m-d') }}"
                           required>
                    @error('date_of_birth')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">We calculate the age automatically — no year-based tracking.</div>
                </div>

                <button type="submit" class="btn btn-success fw-bold w-100 py-3 rounded-3 fs-5">
                    Next →
                </button>

                <div class="text-center mt-3">
                    <a href="{{ route('onboarding.step3') }}" class="text-muted small">
                        Skip for now
                    </a>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection
