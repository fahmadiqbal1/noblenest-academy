<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\TeacherProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        $profile = TeacherProfile::firstOrCreate(
            ['user_id' => Auth::id()],
            ['status' => 'pending', 'payout_rate' => 0.70]
        );

        return view('teacher.profile', compact('profile'));
    }

    public function edit()
    {
        $profile = TeacherProfile::firstOrCreate(
            ['user_id' => Auth::id()],
            ['status' => 'pending', 'payout_rate' => 0.70]
        );

        return view('teacher.profile-edit', compact('profile'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'bio'              => 'required|string|max:2000',
            'qualifications'   => 'required|string|max:1000',
            'intro_video_url'  => 'nullable|url|max:500',
            'credentials'      => 'nullable|array',
            'credentials.*'    => 'string|max:200',
            'preferred_currency' => 'nullable|string|size:3',
            'payout_method'    => 'nullable|string|in:stripe,wise,paypal',
        ]);

        $profile = TeacherProfile::firstOrCreate(['user_id' => Auth::id()]);
        $profile->fill($validated);

        // Re-submit for vetting if previously rejected
        if ($profile->status === 'rejected') {
            $profile->status = 'pending';
        }

        $profile->save();

        return redirect()->route('teacher.profile')
            ->with('status', 'Profile updated. Changes will be reviewed within 48 hours.');
    }
}
