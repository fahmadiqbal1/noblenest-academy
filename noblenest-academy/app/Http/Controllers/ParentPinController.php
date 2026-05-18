<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Phase 5 — Parent PIN entry/verification.
 *
 * Pairs with `RequireParentPin` middleware. Throttles failed attempts to
 * 3 / minute per user; on lockout, the user must wait 60s.
 */
class ParentPinController extends Controller
{
    private const MAX_ATTEMPTS = 3;

    private const DECAY_SECONDS = 60;

    public function show()
    {
        $user = Auth::user();

        return view('parent.pin', [
            'has_pin' => $user !== null && ! empty($user->parent_pin_hash),
        ]);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'pin' => ['required', 'regex:/^\d{4}$/'],
        ]);

        /** @var User $user */
        $user = Auth::user();
        $key = 'parent-pin:'.$user->id;

        // First-time setup: a parent with no PIN sets it here. This is the
        // recovery path that lets RequireParentPin fail closed without
        // permanently locking legacy / incomplete-onboarding users out.
        if (empty($user->parent_pin_hash)) {
            $user->forceFill(['parent_pin_hash' => Hash::make($request->input('pin'))])->save();
            RateLimiter::clear($key);
            $request->session()->put('parent_pin_verified_at', Carbon::now()->toIso8601String());
            $intended = $request->session()->pull('parent_pin_intended');

            return redirect($intended ?: route('parent.dashboard'))
                ->with('status', __('Parent PIN set.'));
        }

        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($key);

            return back()->withErrors([
                'pin' => __('Too many attempts. Try again in :seconds seconds.', ['seconds' => $seconds]),
            ])->withInput();
        }

        // $user->parent_pin_hash is guaranteed non-empty here (the no-PIN
        // case returned above via first-time setup).
        if (! Hash::check($request->input('pin'), $user->parent_pin_hash)) {
            RateLimiter::hit($key, self::DECAY_SECONDS);

            return back()->withErrors(['pin' => __('Incorrect PIN.')])->withInput();
        }

        RateLimiter::clear($key);
        $request->session()->put('parent_pin_verified_at', Carbon::now()->toIso8601String());

        $intended = $request->session()->pull('parent_pin_intended');

        return redirect($intended ?: route('parent.dashboard'));
    }
}
