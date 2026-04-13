@extends('layouts.app')

@section('meta_title', 'Practitioner Setup | NobleNest Global Academy')

@section('content')
<style>
    .setup-shell {
        background: var(--nn-surface);
        border: var(--nn-border-w) solid var(--nn-border);
        border-radius: var(--nn-radius);
        box-shadow: var(--nn-shadow);
    }
    .setup-header {
        background: linear-gradient(135deg, #059669, #34D399 58%, #6EE7B7);
        border-radius: var(--nn-radius) var(--nn-radius) 0 0;
        color: #fff;
        padding: 2rem;
    }
</style>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="setup-shell">
            <div class="setup-header">
                <h2 class="fw-bold mb-1"><i class="bi bi-shield-check me-2"></i>Practitioner Credential Setup</h2>
                <p class="mb-0 opacity-75">Complete your professional profile to start reviewing maternal wellness content.</p>
            </div>
            <div class="p-4 p-lg-5">
                @if($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('practitioner.profile.storeSetup') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="license_type" class="form-label fw-semibold">License Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="license_type" name="license_type" required>
                                <option value="">Select your credential type...</option>
                                <option value="medical_doctor" {{ old('license_type') == 'medical_doctor' ? 'selected' : '' }}>Medical Doctor (MD/MBBS)</option>
                                <option value="nurse_practitioner" {{ old('license_type') == 'nurse_practitioner' ? 'selected' : '' }}>Nurse Practitioner</option>
                                <option value="midwife" {{ old('license_type') == 'midwife' ? 'selected' : '' }}>Certified Midwife</option>
                                <option value="nutritionist" {{ old('license_type') == 'nutritionist' ? 'selected' : '' }}>Nutritionist / Dietitian</option>
                                <option value="herbalist" {{ old('license_type') == 'herbalist' ? 'selected' : '' }}>Certified Herbalist</option>
                                <option value="physiotherapist" {{ old('license_type') == 'physiotherapist' ? 'selected' : '' }}>Physiotherapist</option>
                                <option value="ayurvedic_practitioner" {{ old('license_type') == 'ayurvedic_practitioner' ? 'selected' : '' }}>Ayurvedic Practitioner</option>
                                <option value="tcm_practitioner" {{ old('license_type') == 'tcm_practitioner' ? 'selected' : '' }}>TCM Practitioner</option>
                                <option value="other" {{ old('license_type') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="license_number" class="form-label fw-semibold">License / Registration Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="license_number" name="license_number" value="{{ old('license_number') }}" required>
                            <div class="form-text"><i class="bi bi-lock-fill"></i> Encrypted at rest — never displayed publicly.</div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="credential_body" class="form-label fw-semibold">Issuing Body / Organization <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="credential_body" name="credential_body" value="{{ old('credential_body') }}" placeholder="e.g. General Medical Council" required>
                        </div>
                        <div class="col-md-6">
                            <label for="specialization" class="form-label fw-semibold">Specialization <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="specialization" name="specialization" value="{{ old('specialization') }}" placeholder="e.g. Obstetrics & Gynaecology" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="years_experience" class="form-label fw-semibold">Years of Experience <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="years_experience" name="years_experience" value="{{ old('years_experience') }}" min="0" max="80" required>
                        </div>
                        <div class="col-md-6">
                            <label for="certificate" class="form-label fw-semibold">Upload Certificate (optional)</label>
                            <input type="file" class="form-control" id="certificate" name="certificate" accept=".pdf,.jpg,.jpeg,.png">
                            <div class="form-text">PDF, JPG, or PNG. Max 5 MB.</div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="bio" class="form-label fw-semibold">Professional Bio (optional)</label>
                        <textarea class="form-control" id="bio" name="bio" rows="3" maxlength="2000" placeholder="Brief description of your practice and expertise...">{{ old('bio') }}</textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-circle me-2"></i>Complete Setup & Start Reviewing
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
