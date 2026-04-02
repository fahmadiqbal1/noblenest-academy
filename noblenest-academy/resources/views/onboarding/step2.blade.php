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

                {{-- Faith-based curriculum (optional) --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold">Will your child be taught Islamic studies? <span class="text-muted fw-normal">(optional)</span></label>
                    <p class="text-muted small mb-2">If yes, we'll include Quran memorisation, Arabic alphabet, duas, and Islamic character activities. Non-Muslim families still have full access to all other activities.</p>
                    <div class="d-flex gap-2 flex-wrap">
                        <div class="flex-fill">
                            <input type="radio" class="btn-check" name="is_muslim" id="faith_yes" value="yes"
                                   {{ old('is_muslim') === 'yes' ? 'checked' : '' }}>
                            <label class="btn btn-outline-success w-100 rounded-3" for="faith_yes">
                                ☪️ Yes, Muslim household
                            </label>
                        </div>
                        <div class="flex-fill">
                            <input type="radio" class="btn-check" name="is_muslim" id="faith_no" value="no"
                                   {{ old('is_muslim') === 'no' ? 'checked' : '' }}>
                            <label class="btn btn-outline-secondary w-100 rounded-3" for="faith_no">
                                🌍 Non-Muslim
                            </label>
                        </div>
                        <div class="flex-fill">
                            <input type="radio" class="btn-check" name="is_muslim" id="faith_skip" value="skip"
                                   {{ old('is_muslim', 'skip') === 'skip' ? 'checked' : '' }}>
                            <label class="btn btn-outline-light text-muted border w-100 rounded-3" for="faith_skip">
                                Skip for now
                            </label>
                        </div>
                    </div>
                    <div class="form-text mt-1">You can change this anytime in your child's profile settings.</div>
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
