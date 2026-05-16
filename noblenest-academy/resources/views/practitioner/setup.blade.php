@extends('layouts.practitioner')

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

<div class="flex flex-wrap justify-center">
    <div class="lg:w-8/12">
        <div class="setup-shell">
            <div class="setup-header">
                <h2 class="font-bold mb-1"><x-ui.icon name="shield-check" class="me-2" />Practitioner Credential Setup</h2>
                <p class="mb-0 opacity-75">Complete your professional profile to start reviewing maternal wellness content.</p>
            </div>
            <div class="p-4 p-lg-5">
                @if($errors->any())
                    <div class="flex items-start gap-3 p-4 rounded-lg border bg-red-50 border-red-200 text-red-800">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('practitioner.profile.storeSetup') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="flex flex-wrap gap-3 mb-3">
                        <div class="md:w-6/12">
                            <label for="license_type" class="block text-sm font-medium text-gray-700 mb-1 font-semibold">License Type <span class="text-red-600">*</span></label>
                            <select class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" id="license_type" name="license_type" required>
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
                        <div class="md:w-6/12">
                            <label for="license_number" class="block text-sm font-medium text-gray-700 mb-1 font-semibold">License / Registration Number <span class="text-red-600">*</span></label>
                            <input type="text" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" id="license_number" name="license_number" value="{{ old('license_number') }}" required>
                            <div class="mt-1 text-sm text-[var(--color-text-muted)]"><x-ui.icon name="lock" /> Encrypted at rest — never displayed publicly.</div>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3 mb-3">
                        <div class="md:w-6/12">
                            <label for="credential_body" class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Issuing Body / Organization <span class="text-red-600">*</span></label>
                            <input type="text" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" id="credential_body" name="credential_body" value="{{ old('credential_body') }}" placeholder="e.g. General Medical Council" required>
                        </div>
                        <div class="md:w-6/12">
                            <label for="specialization" class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Specialization <span class="text-red-600">*</span></label>
                            <input type="text" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" id="specialization" name="specialization" value="{{ old('specialization') }}" placeholder="e.g. Obstetrics & Gynaecology" required>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3 mb-3">
                        <div class="md:w-6/12">
                            <label for="years_experience" class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Years of Experience <span class="text-red-600">*</span></label>
                            <input type="number" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" id="years_experience" name="years_experience" value="{{ old('years_experience') }}" min="0" max="80" required>
                        </div>
                        <div class="md:w-6/12">
                            <label for="certificate" class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Upload Certificate (optional)</label>
                            <input type="file" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" id="certificate" name="certificate" accept=".pdf,.jpg,.jpeg,.png">
                            <div class="mt-1 text-sm text-[var(--color-text-muted)]">PDF, JPG, or PNG. Max 5 MB.</div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="bio" class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Professional Bio (optional)</label>
                        <textarea class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" id="bio" name="bio" rows="3" maxlength="2000" placeholder="Brief description of your practice and expertise...">{{ old('bio') }}</textarea>
                    </div>

                    <div class="grid">
                        <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-violet-600 text-white hover:bg-violet-700 px-5 py-3 text-lg">
                            <x-ui.icon name="check-circle" class="me-2" />Complete Setup & Start Reviewing
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
