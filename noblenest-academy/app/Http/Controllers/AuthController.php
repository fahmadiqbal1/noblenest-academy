<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    /**
     * Allowed roles for self-registration.
     * NOTE: Admin is intentionally excluded - Admin accounts must be
     * created via seeder or artisan command for security.
     */
    protected const ALLOWED_REGISTRATION_ROLES = ['Parent', 'Teacher', 'Student', 'Practitioner'];

    // Show registration form
    public function showRegister()
    {
        return view('auth.register');
    }

    // Handle registration — MiroFish 3-field fast onboarding
    // Fields: email, password, role only. Child details captured in onboarding.
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role'     => ['required', Rule::in(self::ALLOWED_REGISTRATION_ROLES)],
        ]);

        // Double-check Admin is never self-registered (defense in depth)
        if (strtolower($validated['role']) === 'admin') {
            abort(403, 'Admin accounts cannot be self-registered.');
        }

        // Detect country from Cloudflare header (safe — client cannot spoof on CF-proxied traffic)
        $countryCode = strtoupper(substr(preg_replace('/[^A-Z]/', '', $request->header('CF-IPCountry', '')), 0, 2)) ?: null;

        $user = User::create([
            'name'          => $validated['name'],
            'email'         => $validated['email'],
            'password'      => Hash::make($validated['password']),
            'role'          => $validated['role'],
            'country_code'  => $countryCode,
            'referral_code' => Str::upper(Str::random(8)),
        ]);

        // Apply referred-by tracking if referral code present
        if ($request->filled('ref')) {
            $referrer = User::where('referral_code', strtoupper($request->ref))->first();
            if ($referrer && $referrer->id !== $user->id) {
                \App\Models\Referral::create([
                    'referrer_id' => $referrer->id,
                    'referred_id' => $user->id,
                    'code'        => strtoupper($request->ref),
                    'status'      => 'signed_up',
                    'signed_up_at' => now(),
                ]);
            }
        }

        Auth::login($user);

        $intended = session()->pull('url.intended');
        if ($intended) {
            return redirect()->to($intended);
        }

        // Teachers go to profile setup, others go to onboarding wizard
        if ($user->role === 'Teacher') {
            return redirect()->route('teacher.dashboard');
        }

        if ($user->role === 'Practitioner') {
            return redirect()->route('practitioner.profile.setup');
        }

        return redirect()->route('onboarding');
    }

    // Show login form
    public function showLogin()
    {
        return view('auth.login');
    }

    // Handle login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();
            $intended = session()->pull('url.intended');

            if ($intended) {
                return redirect()->to($intended);
            }

            return match ($user->role) {
                'Parent'       => redirect()->route('parent.dashboard'),
                'Teacher'      => redirect()->route('teacher.dashboard'),
                'Student'      => redirect()->route('marketplace.index'),
                'Practitioner' => redirect()->route('practitioner.dashboard'),
                'Admin'        => redirect()->route('home'),
                default        => redirect('/'),
            };
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    // Handle logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}

