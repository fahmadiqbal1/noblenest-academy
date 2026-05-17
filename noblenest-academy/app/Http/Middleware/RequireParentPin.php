<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

/**
 * Phase 5 — Parent-mode re-entry gate.
 *
 * Sensitive parent routes (settings, billing, privacy export/delete) require
 * a fresh PIN confirmation within the last `WINDOW_MINUTES`. If stale or
 * absent, redirect to the PIN entry screen and remember the intended URL.
 */
class RequireParentPin
{
    public const WINDOW_MINUTES = 10;

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // If the parent never set a PIN, bypass (they were created pre-Phase-5
        // or onboarding hasn't reached step 2 yet).
        if (! $user || empty($user->parent_pin_hash)) {
            return $next($request);
        }

        $verifiedAt = $request->session()->get('parent_pin_verified_at');
        if ($verifiedAt) {
            $when = $verifiedAt instanceof Carbon ? $verifiedAt : Carbon::parse((string) $verifiedAt);
            if ($when->diffInMinutes(now()) < self::WINDOW_MINUTES) {
                return $next($request);
            }
        }

        $request->session()->put('parent_pin_intended', $request->fullUrl());

        return redirect()->route('parent.pin.show');
    }
}
