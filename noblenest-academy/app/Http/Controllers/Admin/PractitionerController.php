<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PractitionerProfile;
use Illuminate\Http\Request;

class PractitionerController extends Controller
{
    public function index(Request $request)
    {
        $query = PractitionerProfile::with('user');

        if ($request->filled('status')) {
            $query->where('verification_status', $request->input('status'));
        }

        $practitioners = $query->latest()->paginate(20);

        return view('admin.practitioners.index', compact('practitioners'));
    }

    public function suspend(Request $request, PractitionerProfile $practitionerProfile)
    {
        $validated = $request->validate([
            'suspended_reason' => 'required|string|max:1000',
        ]);

        $practitionerProfile->update([
            'verification_status' => 'suspended',
            'suspended_reason'    => $validated['suspended_reason'],
        ]);

        return redirect()->route('admin.practitioners.index')
            ->with('success', 'Practitioner suspended.');
    }

    public function unsuspend(PractitionerProfile $practitionerProfile)
    {
        $practitionerProfile->update([
            'verification_status' => 'active',
            'suspended_reason'    => null,
        ]);

        return redirect()->route('admin.practitioners.index')
            ->with('success', 'Practitioner reactivated.');
    }
}
