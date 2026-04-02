<?php

namespace App\Http\Controllers;

use App\Models\ChildProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnboardingController extends Controller
{
    /**
     * Step 1: Language selection — fastest possible first impression.
     * MiroFish: first-time mothers abandon if onboarding > 3 fields on screen 1.
     */
    public function show()
    {
        $user = Auth::user();

        if ($user && $user->is_onboarded) {
            return redirect()->route('home');
        }

        $locales = [
            'en' => 'English',    'ar' => 'العربية',
            'fr' => 'Français',   'ur' => 'اردو',
            'ru' => 'Русский',    'zh' => '中文',
            'es' => 'Español',    'ko' => '한국어',
        ];

        return view('onboarding.step1', compact('user', 'locales'));
    }

    /**
     * Step 1 submit: save language, redirect to child profile creation.
     */
    public function storeStep1(Request $request)
    {
        $data = $request->validate([
            'preferred_language' => 'required|in:en,ar,fr,ur,ru,zh,es,ko',
        ]);

        $user = Auth::user();
        if ($user) {
            $user->update(['preferred_language' => $data['preferred_language']]);
        }

        session(['lang' => $data['preferred_language']]);

        return redirect()->route('onboarding.step2');
    }

    /**
     * Step 2: Child name + date of birth — one screen, two fields.
     */
    public function showStep2()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        return view('onboarding.step2');
    }

    /**
     * Step 2 submit: create child profile with COPPA-compliant data.
     */
    public function storeStep2(Request $request)
    {
        $data = $request->validate([
            'child_name'   => 'required|string|max:100',
            'date_of_birth' => 'required|date|before:today|after:' . now()->subYears(11)->format('Y-m-d'),
        ]);

        $user = Auth::user();

        $dob = \Carbon\Carbon::parse($data['date_of_birth']);
        $ageMonths = $dob->diffInMonths(now());

        $ageTier = match(true) {
            $ageMonths < 24  => 'baby',
            $ageMonths < 48  => 'toddler',
            $ageMonths < 72  => 'preschool',
            default          => 'school',
        };

        ChildProfile::create([
            'parent_id'     => $user->id,
            'name'          => $data['child_name'],
            'date_of_birth' => $data['date_of_birth'],
            'age_tier'      => $ageTier,
            'preferred_language' => $user->preferred_language ?? 'en',
        ]);

        return redirect()->route('onboarding.step3');
    }

    /**
     * Step 3: Learning goals — optional, skippable.
     */
    public function showStep3()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        return view('onboarding.step3');
    }

    /**
     * Step 3 submit: save goals, mark onboarding complete.
     */
    public function storeStep3(Request $request)
    {
        $data = $request->validate([
            'daily_minutes' => 'nullable|integer|min:5|max:120',
            'goals'         => 'nullable|array',
        ]);

        $user = Auth::user();
        if ($user) {
            $user->update([
                'is_onboarded'  => true,
            ]);
        }

        session([
            'onboarding_done'  => true,
            'daily_minutes'    => $data['daily_minutes'] ?? 15,
            'onboarding_goals' => $data['goals'] ?? [],
        ]);

        return redirect()->route('home')->with('status', 'Welcome to Noble Nest Academy! 🎉');
    }

    /**
     * Legacy store — redirect to step1 for backward compatibility.
     */
    public function store(Request $request)
    {
        return $this->storeStep1($request);
    }
}
