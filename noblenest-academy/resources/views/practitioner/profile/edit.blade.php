@extends('layouts.practitioner')

@section('meta_title', 'Edit Practitioner Profile | NobleNest Global Academy')

@section('content')
<div class="flex flex-wrap justify-center">
    <div class="lg:w-8/12">
        <div class="glass-panel p-4 p-lg-5">
            <h2 class="font-bold mb-4"><x-ui.icon name="edit" class="me-2" />Edit Practitioner Profile</h2>

            @if(session('success'))
                <div class="flex items-start gap-3 p-4 rounded-lg border bg-emerald-50 border-emerald-200 text-emerald-800">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="flex items-start gap-3 p-4 rounded-lg border bg-red-50 border-red-200 text-red-800">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('practitioner.profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="flex flex-wrap gap-3 mb-3">
                    <div class="md:w-6/12">
                        <label for="license_type" class="block text-sm font-medium text-gray-700 mb-1 font-semibold">License Type</label>
                        <select class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" id="license_type" name="license_type" required>
                            @foreach(['medical_doctor' => 'Medical Doctor', 'nurse_practitioner' => 'Nurse Practitioner', 'midwife' => 'Certified Midwife', 'nutritionist' => 'Nutritionist / Dietitian', 'herbalist' => 'Certified Herbalist', 'physiotherapist' => 'Physiotherapist', 'ayurvedic_practitioner' => 'Ayurvedic Practitioner', 'tcm_practitioner' => 'TCM Practitioner', 'other' => 'Other'] as $val => $label)
                                <option value="{{ $val }}" {{ old('license_type', $profile->license_type) == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:w-6/12">
                        <label for="license_number" class="block text-sm font-medium text-gray-700 mb-1 font-semibold">License Number</label>
                        <input type="text" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" id="license_number" name="license_number" value="{{ old('license_number', $profile->license_number) }}" required>
                        <div class="mt-1 text-sm text-[var(--color-text-muted)]"><x-ui.icon name="lock" /> Encrypted at rest.</div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3 mb-3">
                    <div class="md:w-6/12">
                        <label for="credential_body" class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Issuing Body</label>
                        <input type="text" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" id="credential_body" name="credential_body" value="{{ old('credential_body', $profile->credential_body) }}" required>
                    </div>
                    <div class="md:w-6/12">
                        <label for="specialization" class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Specialization</label>
                        <input type="text" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" id="specialization" name="specialization" value="{{ old('specialization', $profile->specialization) }}" required>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3 mb-3">
                    <div class="md:w-6/12">
                        <label for="years_experience" class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Years of Experience</label>
                        <input type="number" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" id="years_experience" name="years_experience" value="{{ old('years_experience', $profile->years_experience) }}" min="0" max="80" required>
                    </div>
                    <div class="md:w-6/12">
                        <label for="certificate" class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Replace Certificate</label>
                        <input type="file" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" id="certificate" name="certificate" accept=".pdf,.jpg,.jpeg,.png">
                        @if($profile->certificate_path)
                            <div class="mt-1 text-sm text-[var(--color-text-muted)] text-emerald-600"><x-ui.icon name="check-circle" /> Certificate on file.</div>
                        @endif
                    </div>
                </div>

                <div class="mb-4">
                    <label for="bio" class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Professional Bio</label>
                    <textarea class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" id="bio" name="bio" rows="3" maxlength="2000">{{ old('bio', $profile->bio) }}</textarea>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-violet-600 text-white hover:bg-violet-700"><x-ui.icon name="check" class="me-1" /> Save Changes</button>
                    <a href="{{ route('practitioner.dashboard') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-gray-300 text-gray-700 hover:bg-gray-100">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
