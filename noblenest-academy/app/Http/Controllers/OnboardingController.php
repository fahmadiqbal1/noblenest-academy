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
     *
     * Phase 5: auto-detect from `Accept-Language` and skip step 1 entirely if
     * the browser's preference matches one of our supported locales and the
     * user hasn't explicitly set one yet. Saves one tap for ~80% of visitors.
     */
    public function show(?Request $request = null)
    {
        $request ??= request();
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

        // Auto-detect: only fire if the user hasn't set a preference yet AND
        // the Accept-Language header has a clear top pick we support.
        if ($user && empty($user->preferred_language)) {
            $detected = $this->detectLocaleFromRequest($request, array_keys($locales));
            if ($detected) {
                $user->update(['preferred_language' => $detected]);
                $request->session()->put('lang', $detected);
                $request->session()->flash('status', __('Language set automatically based on your browser preference.'));
                return redirect()->route('onboarding.step2');
            }
        }

        return view('onboarding.step1', compact('user', 'locales'));
    }

    /**
     * Parse `Accept-Language` and return the first base-locale match in our
     * supported list, or null if no confident match is found.
     */
    private function detectLocaleFromRequest(Request $request, array $supported): ?string
    {
        $header = (string) $request->header('Accept-Language', '');
        if ($header === '') return null;

        // Build a ranked list: each entry is "<tag>;q=<weight>" or "<tag>".
        $entries = [];
        foreach (explode(',', $header) as $part) {
            $part = trim($part);
            if ($part === '') continue;
            [$tag, $qPart] = array_pad(explode(';', $part, 2), 2, null);
            $q = 1.0;
            if ($qPart && preg_match('/q=([0-9.]+)/', $qPart, $m)) {
                $q = (float) $m[1];
            }
            $entries[] = ['tag' => strtolower(trim((string) $tag)), 'q' => $q];
        }
        // Highest q first.
        usort($entries, fn ($a, $b) => $b['q'] <=> $a['q']);

        foreach ($entries as $entry) {
            $base = preg_split('/[-_]/', $entry['tag'])[0] ?? '';
            if ($base && in_array($base, $supported, true)) {
                return $base;
            }
        }
        return null;
    }

    /**
     * Step 1 submit: save language, redirect to child profile creation.
     */
    public function storeStep1(Request $request)
    {
        $data = $request->validate([
            'preferred_language' => 'required|in:en,ar,fr,ur,ru,zh,es,ko',
        ]);

        /** @var \App\Models\User $user */
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
            'child_name'    => 'required|string|max:100',
            'date_of_birth' => 'required|date|before:today|after:' . now()->subYears(11)->format('Y-m-d'),
            'is_muslim'     => 'nullable|in:yes,no,skip',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $dob = \Carbon\Carbon::parse($data['date_of_birth']);
        $ageMonths = $dob->diffInMonths(now());

        $ageTier = match(true) {
            $ageMonths < 24  => 'baby',
            $ageMonths < 48  => 'toddler',
            $ageMonths < 72  => 'preschool',
            default          => 'school',
        };

        // Convert radio answer to nullable boolean (null = not answered / skip)
        $isMuslim = match($data['is_muslim'] ?? 'skip') {
            'yes'   => true,
            'no'    => false,
            default => null,
        };

        ChildProfile::create([
            'parent_id'          => $user->id,
            'name'               => $data['child_name'],
            'date_of_birth'      => $data['date_of_birth'],
            'age_tier'           => $ageTier,
            'is_muslim'          => $isMuslim,
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

        /** @var \App\Models\User $user */
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

        $route = match ($user->role ?? '') {
            'Parent'  => 'parent.dashboard',
            'Teacher' => 'teacher.dashboard',
            default   => 'home',
        };

        return redirect()->route($route)->with('status', 'Welcome to Noble Nest Academy! 🎉');
    }

    /**
     * Legacy store — redirect to step1 for backward compatibility.
     */
    public function store(Request $request)
    {
        return $this->storeStep1($request);
    }
}
