<?php

namespace App\Http\Controllers\Practitioner;

use App\Http\Controllers\Controller;
use App\Models\PractitionerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function setup(Request $request)
    {
        if ($request->user()->hasPractitionerProfile()) {
            return redirect()->route('practitioner.dashboard');
        }

        return view('practitioner.setup');
    }

    public function storeSetup(Request $request)
    {
        $validated = $request->validate([
            'license_number'   => 'required|string|max:100',
            'license_type'     => 'required|in:medical_doctor,nurse_practitioner,midwife,nutritionist,herbalist,physiotherapist,ayurvedic_practitioner,tcm_practitioner,other',
            'credential_body'  => 'required|string|max:255',
            'specialization'   => 'required|string|max:255',
            'years_experience' => 'required|integer|min:0|max:80',
            'bio'              => 'nullable|string|max:2000',
            'certificate'      => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $certificatePath = null;
        if ($request->hasFile('certificate')) {
            $certificatePath = $request->file('certificate')->store('practitioner-certs', 'local');
        }

        PractitionerProfile::create([
            'user_id'            => $request->user()->id,
            'license_number'     => $validated['license_number'],
            'license_type'       => $validated['license_type'],
            'credential_body'    => $validated['credential_body'],
            'specialization'     => $validated['specialization'],
            'years_experience'   => $validated['years_experience'],
            'bio'                => $validated['bio'] ?? null,
            'certificate_path'   => $certificatePath,
            'verification_status' => 'active',
        ]);

        return redirect()->route('practitioner.dashboard')
            ->with('success', 'Profile created successfully. You can now review content.');
    }

    public function edit(Request $request)
    {
        $profile = $request->user()->practitionerProfile;

        return view('practitioner.profile.edit', compact('profile'));
    }

    public function update(Request $request)
    {
        $profile = $request->user()->practitionerProfile;

        $validated = $request->validate([
            'license_number'   => 'required|string|max:100',
            'license_type'     => 'required|in:medical_doctor,nurse_practitioner,midwife,nutritionist,herbalist,physiotherapist,ayurvedic_practitioner,tcm_practitioner,other',
            'credential_body'  => 'required|string|max:255',
            'specialization'   => 'required|string|max:255',
            'years_experience' => 'required|integer|min:0|max:80',
            'bio'              => 'nullable|string|max:2000',
            'certificate'      => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('certificate')) {
            if ($profile->certificate_path) {
                Storage::disk('local')->delete($profile->certificate_path);
            }
            $validated['certificate_path'] = $request->file('certificate')->store('practitioner-certs', 'local');
        }

        unset($validated['certificate']);
        $profile->update($validated);

        return redirect()->route('practitioner.profile.edit')
            ->with('success', 'Profile updated successfully.');
    }
}
