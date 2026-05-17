<?php

namespace App\Http\Controllers;

use App\Models\ChildProfile;
use App\Models\ConsentReceipt;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Phase 5 — 5-step parent onboarding.
 *
 * 1. language       → User.preferred_language
 * 2. parent profile → name + country_code + 4-digit PIN
 * 3. COPPA consent  → writes ConsentReceipt row (audit trail)
 * 4. add child      → creates ChildProfile + stamps parental_consent_*
 * 5. age-tier walkthrough → sample activities + CTA into child dashboard.
 */
class OnboardingController extends Controller
{
    private const SUPPORTED = ['en', 'ar', 'fr', 'ur', 'ru', 'zh', 'es', 'ko'];
    private const CONSENT_DOC_VERSION = '2026-05';

    // ------------------------------------------------------------------
    // Legacy shim — keep `GET /onboarding` working.
    // ------------------------------------------------------------------
    public function show(Request $request)
    {
        return redirect()->route('onboarding.step1');
    }

    public function store(Request $request)
    {
        return $this->storeStep1($request);
    }

    // ------------------------------------------------------------------
    // STEP 1 — language picker
    // ------------------------------------------------------------------
    public function showStep1(Request $request)
    {
        $user = Auth::user();
        $locales = $this->locales();

        if ($user && empty($user->preferred_language)) {
            $detected = $this->detectLocaleFromRequest($request, array_keys($locales));
            if ($detected) {
                $user->update(['preferred_language' => $detected]);
                $request->session()->put('lang', $detected);
            }
        }

        return view('onboarding.step1', ['locales' => $locales, 'step' => 1]);
    }

    public function storeStep1(Request $request)
    {
        $data = $request->validate([
            'preferred_language' => 'required|in:' . implode(',', self::SUPPORTED),
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->update(['preferred_language' => $data['preferred_language']]);
        session(['lang' => $data['preferred_language']]);

        return redirect()->route('onboarding.step2');
    }

    // ------------------------------------------------------------------
    // STEP 2 — parent profile + 4-digit PIN
    // ------------------------------------------------------------------
    public function showStep2()
    {
        return view('onboarding.step2', ['step' => 2]);
    }

    public function storeStep2(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:120',
            'country_code' => 'nullable|string|size:2',
            'parent_pin'   => ['required', 'regex:/^\d{4}$/'],
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->update([
            'name'            => $data['name'],
            'country_code'    => strtoupper((string) ($data['country_code'] ?? '')) ?: null,
            'parent_pin_hash' => Hash::make($data['parent_pin']),
        ]);

        return redirect()->route('onboarding.step3');
    }

    // ------------------------------------------------------------------
    // STEP 3 — COPPA consent (stages receipt; persisted at step 4)
    // ------------------------------------------------------------------
    public function showStep3()
    {
        return view('onboarding.step3', [
            'step' => 3,
            'document_version' => self::CONSENT_DOC_VERSION,
        ]);
    }

    public function storeStep3(Request $request)
    {
        $request->validate(['agree' => 'accepted']);

        // child_profile_id is NOT NULL on consent_receipts so we stage the
        // receipt fields in session and write the row when the parent adds
        // their first child in step 4.
        session([
            'onboarding.consent_signed_at' => now()->toIso8601String(),
            'onboarding.consent_ip'        => substr((string) $request->ip(), 0, 45),
            'onboarding.consent_ua'        => substr((string) $request->userAgent(), 0, 512),
            'onboarding.consent_version'   => self::CONSENT_DOC_VERSION,
        ]);

        return redirect()->route('onboarding.step4');
    }

    // ------------------------------------------------------------------
    // STEP 4 — add child + persist consent receipt + stamp parental_consent_at
    // ------------------------------------------------------------------
    public function showStep4()
    {
        return view('onboarding.step4', ['step' => 4]);
    }

    public function storeStep4(Request $request)
    {
        $data = $request->validate([
            'child_name'    => 'required|string|max:100',
            'date_of_birth' => 'required|date|before:today',
            'gender'        => 'nullable|in:male,female,other',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $dob       = Carbon::parse($data['date_of_birth']);
        $ageMonths = (int) $dob->diffInMonths(now());
        $ageTier   = $this->ageTier($ageMonths);

        $child = ChildProfile::create([
            'parent_id'                   => $user->id,
            'name'                        => $data['child_name'],
            'date_of_birth'               => $data['date_of_birth'],
            'gender'                      => $data['gender'] ?? null,
            'age_tier'                    => $ageTier,
            'preferred_language'          => $user->preferred_language ?? 'en',
            'parental_consent_at'         => now(),
            'parental_consent_ip'         => substr((string) $request->ip(), 0, 45),
            'parental_consent_user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);

        ConsentReceipt::create([
            'parent_user_id'   => $user->id,
            'child_profile_id' => $child->id,
            'document_version' => session('onboarding.consent_version', self::CONSENT_DOC_VERSION),
            'ip'               => session('onboarding.consent_ip', substr((string) $request->ip(), 0, 45)),
            'user_agent'       => session('onboarding.consent_ua', substr((string) $request->userAgent(), 0, 512)),
            'signed_at'        => session('onboarding.consent_signed_at')
                                    ? Carbon::parse(session('onboarding.consent_signed_at'))
                                    : now(),
            'withdrawn_at'     => null,
        ]);

        session()->forget([
            'onboarding.consent_signed_at',
            'onboarding.consent_ip',
            'onboarding.consent_ua',
            'onboarding.consent_version',
        ]);

        return redirect()->route('onboarding.step5', ['child' => $child->id]);
    }

    // ------------------------------------------------------------------
    // STEP 5 — age-tier walkthrough → first activity / dashboard
    // ------------------------------------------------------------------
    public function showStep5(Request $request, ChildProfile $child)
    {
        if ($child->parent_id !== Auth::id()) {
            abort(403);
        }

        $tier = $child->age_tier ?? $this->ageTier((int) ($child->age_months ?? 0));

        $samples = \App\Models\Activity::query()
            ->where('published', true)
            ->limit(3)
            ->get(['id', 'title', 'description']);

        return view('onboarding.step5', [
            'step'    => 5,
            'child'   => $child,
            'tier'    => $tier,
            'samples' => $samples,
        ]);
    }

    public function completeStep5(Request $request, ChildProfile $child)
    {
        if ($child->parent_id !== Auth::id()) {
            abort(403);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->update(['is_onboarded' => true]);

        $first = \App\Models\Activity::where('published', true)->first();
        if ($first) {
            return redirect()->route('activities.show', $first)
                ->with('status', __('onboarding.welcome_message'));
        }

        return redirect()->route('child.dashboard', $child)
            ->with('status', __('onboarding.welcome_message'));
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    /** @return array<string, string> */
    private function locales(): array
    {
        return [
            'en' => 'English',  'ar' => 'العربية',
            'fr' => 'Français', 'ur' => 'اردو',
            'ru' => 'Русский',  'zh' => '中文',
            'es' => 'Español',  'ko' => '한국어',
        ];
    }

    private function ageTier(int $ageMonths): string
    {
        return match (true) {
            $ageMonths < 24 => 'baby',
            $ageMonths < 48 => 'toddler',
            $ageMonths < 72 => 'preschool',
            default         => 'school',
        };
    }

    /** @param array<int, string> $supported */
    private function detectLocaleFromRequest(Request $request, array $supported): ?string
    {
        $header = (string) $request->header('Accept-Language', '');
        if ($header === '') {
            return null;
        }

        $entries = [];
        foreach (explode(',', $header) as $part) {
            $part = trim($part);
            if ($part === '') {
                continue;
            }
            [$tag, $qPart] = array_pad(explode(';', $part, 2), 2, null);
            $q = 1.0;
            if ($qPart && preg_match('/q=([0-9.]+)/', $qPart, $m)) {
                $q = (float) $m[1];
            }
            $entries[] = ['tag' => strtolower(trim((string) $tag)), 'q' => $q];
        }
        usort($entries, fn ($a, $b) => $b['q'] <=> $a['q']);

        foreach ($entries as $entry) {
            $base = preg_split('/[-_]/', $entry['tag'])[0] ?? '';
            if ($base && in_array($base, $supported, true)) {
                return $base;
            }
        }
        return null;
    }
}
