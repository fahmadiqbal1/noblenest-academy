<?php

namespace App\Http\Middleware;

use App\Models\ChildProfile;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

/**
 * Phase 5 — block child-facing routes for under-13 profiles without recorded
 * parental consent. COPPA / GDPR-K compliant.
 *
 * Applies when the matched route has a {child} parameter (resolved to a
 * `ChildProfile`). If the child's age in months is < 156 (≈13y) AND
 * `parental_consent_at` is null, redirect to the consent confirmation page.
 *
 * Usage:
 *   Route::get('/child/{child}/...', ...)->middleware('parental.consent');
 */
class RequireParentalConsent
{
    public function handle(Request $request, Closure $next): Response
    {
        $child = $request->route('child');
        if (! $child instanceof ChildProfile) {
            return $next($request);
        }

        // Compute age in months. ChildProfile may carry either `date_of_birth`
        // or `age_months` — handle both.
        $ageMonths = $this->ageInMonths($child);
        if ($ageMonths === null || $ageMonths >= 156) {
            return $next($request);
        }

        if ($child->parental_consent_at) {
            return $next($request);
        }

        return redirect()->route('privacy.parental-consent', ['child' => $child->id])
            ->with('error', 'Parental consent is required before this child can use Noble Nest Academy.');
    }

    private function ageInMonths(ChildProfile $child): ?int
    {
        if (isset($child->age_months) && is_numeric($child->age_months)) {
            return (int) $child->age_months;
        }
        if (! empty($child->date_of_birth)) {
            try {
                return (int) Carbon::parse($child->date_of_birth)->diffInMonths(now());
            } catch (\Throwable $e) {
                return null;
            }
        }

        return null;
    }
}
