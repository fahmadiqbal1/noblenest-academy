<?php

namespace App\Http\Controllers\Maternal;

use App\Http\Controllers\Controller;
use App\Models\MaternalProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function edit()
    {
        $profile = Auth::user()->maternalProfile;
        $this->authorize('update', $profile);

        return view('maternal.profile.edit', compact('profile'));
    }

    public function update(Request $request)
    {
        $profile = Auth::user()->maternalProfile;
        $this->authorize('update', $profile);

        $validated = $request->validate([
            'due_date'              => 'required|date',
            'health_conditions'     => 'nullable|array',
            'health_conditions.*'   => 'string|max:100',
            'dietary_restrictions'  => 'nullable|array',
            'dietary_restrictions.*' => 'string|max:100',
        ]);

        $profile->update($validated);

        return redirect()->route('maternal.profile.edit')
            ->with('success', 'Profile updated successfully.');
    }

    public function pause()
    {
        $profile = Auth::user()->maternalProfile;
        $this->authorize('update', $profile);
        $profile->pause();

        return redirect()->route('maternal.dashboard')
            ->with('info', 'Your maternal journey has been paused. Resume any time.');
    }

    public function resume()
    {
        $profile = Auth::user()->maternalProfile;
        $this->authorize('update', $profile);
        $profile->resume();

        return redirect()->route('maternal.dashboard')
            ->with('success', 'Welcome back to your maternal journey!');
    }

    public function markLoss()
    {
        $profile = Auth::user()->maternalProfile;
        $this->authorize('update', $profile);
        $profile->markLoss();

        return redirect()->route('maternal.dashboard')
            ->with('info', 'We are deeply sorry for your loss. Your data has been preserved privately. Take all the time you need.');
    }
}
