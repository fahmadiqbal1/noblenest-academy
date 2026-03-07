<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnboardingController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        // If already onboarded, redirect home
        if (session('onboarding_done') || ($user && $user->preferred_language && $user->age)) {
            return redirect()->route('home');
        }

        $locales = [
            'en' => 'English', 'fr' => 'French', 'ru' => 'Russian',
            'zh' => 'Mandarin', 'es' => 'Spanish', 'ko' => 'Korean',
        ];

        return view('onboarding', compact('user', 'locales'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'preferred_language' => 'required|in:en,fr,ru,zh,es,ko',
            'child_name'         => 'nullable|string|max:100',
            'child_age'          => 'nullable|integer|min:0|max:12',
            'daily_minutes'      => 'nullable|integer|min:5|max:120',
            'goals'              => 'nullable|array',
        ]);

        $user = Auth::user();

        if ($user) {
            $user->update([
                'preferred_language' => $data['preferred_language'],
            ]);
        }

        // Persist preferences in session
        session([
            'lang'             => $data['preferred_language'],
            'onboarding_done'  => true,
            'daily_minutes'    => $data['daily_minutes'] ?? 30,
            'onboarding_goals' => $data['goals'] ?? [],
        ]);

        return redirect()->route('home')->with('status', 'Welcome! Your preferences have been saved.');
    }
}
