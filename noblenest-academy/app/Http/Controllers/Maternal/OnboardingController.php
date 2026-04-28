<?php

namespace App\Http\Controllers\Maternal;

use App\Http\Controllers\Controller;
use App\Models\MaternalProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnboardingController extends Controller
{
    public function create()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->hasMaternalProfile()) {
            return redirect()->route('maternal.dashboard');
        }

        return view('maternal.onboarding');
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->hasMaternalProfile()) {
            return redirect()->route('maternal.dashboard');
        }

        $validated = $request->validate([
            'due_date'            => 'required|date|after:today',
            'pregnancy_week'      => 'required|integer|min:1|max:42',
            'health_conditions'   => 'nullable|array',
            'health_conditions.*' => 'string|max:100',
            'dietary_restrictions' => 'nullable|array',
            'dietary_restrictions.*' => 'string|max:100',
            'consent_accepted'    => 'accepted',
        ]);

        MaternalProfile::create([
            'user_id'              => $user->id,
            'due_date'             => $validated['due_date'],
            'pregnancy_week_at_registration' => $validated['pregnancy_week'],
            'health_conditions'    => $validated['health_conditions'] ?? [],
            'dietary_restrictions' => $validated['dietary_restrictions'] ?? [],
            'status'               => 'active',
            'consent_accepted_at'  => now(),
        ]);

        return redirect()->route('maternal.dashboard')
            ->with('success', 'Welcome to the Maternal Wellness journey!');
    }
}
