@extends('layouts.app')

@section('meta_title', 'Edit Practitioner Profile | NobleNest Global Academy')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="glass-panel p-4 p-lg-5">
            <h2 class="fw-bold mb-4"><i class="bi bi-pencil-square me-2"></i>Edit Practitioner Profile</h2>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('practitioner.profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="license_type" class="form-label fw-semibold">License Type</label>
                        <select class="form-select" id="license_type" name="license_type" required>
                            @foreach(['medical_doctor' => 'Medical Doctor', 'nurse_practitioner' => 'Nurse Practitioner', 'midwife' => 'Certified Midwife', 'nutritionist' => 'Nutritionist / Dietitian', 'herbalist' => 'Certified Herbalist', 'physiotherapist' => 'Physiotherapist', 'ayurvedic_practitioner' => 'Ayurvedic Practitioner', 'tcm_practitioner' => 'TCM Practitioner', 'other' => 'Other'] as $val => $label)
                                <option value="{{ $val }}" {{ old('license_type', $profile->license_type) == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="license_number" class="form-label fw-semibold">License Number</label>
                        <input type="text" class="form-control" id="license_number" name="license_number" value="{{ old('license_number', $profile->license_number) }}" required>
                        <div class="form-text"><i class="bi bi-lock-fill"></i> Encrypted at rest.</div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="credential_body" class="form-label fw-semibold">Issuing Body</label>
                        <input type="text" class="form-control" id="credential_body" name="credential_body" value="{{ old('credential_body', $profile->credential_body) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="specialization" class="form-label fw-semibold">Specialization</label>
                        <input type="text" class="form-control" id="specialization" name="specialization" value="{{ old('specialization', $profile->specialization) }}" required>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="years_experience" class="form-label fw-semibold">Years of Experience</label>
                        <input type="number" class="form-control" id="years_experience" name="years_experience" value="{{ old('years_experience', $profile->years_experience) }}" min="0" max="80" required>
                    </div>
                    <div class="col-md-6">
                        <label for="certificate" class="form-label fw-semibold">Replace Certificate</label>
                        <input type="file" class="form-control" id="certificate" name="certificate" accept=".pdf,.jpg,.jpeg,.png">
                        @if($profile->certificate_path)
                            <div class="form-text text-success"><i class="bi bi-check-circle"></i> Certificate on file.</div>
                        @endif
                    </div>
                </div>

                <div class="mb-4">
                    <label for="bio" class="form-label fw-semibold">Professional Bio</label>
                    <textarea class="form-control" id="bio" name="bio" rows="3" maxlength="2000">{{ old('bio', $profile->bio) }}</textarea>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1"></i> Save Changes</button>
                    <a href="{{ route('practitioner.dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
