<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    /**
     * Allowed roles for self-registration.
     * NOTE: Admin is intentionally excluded - Admin accounts must be
     * created via seeder or artisan command for security.
     */
    protected const ALLOWED_REGISTRATION_ROLES = ['Parent', 'Teacher', 'Student'];

    // Show registration form
    public function showRegister()
    {
        return view('auth.register');
    }

    // Handle registration
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

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => $validated['role'],
        ]);

        Auth::login($user);

        $intended = session()->pull('url.intended');
        if ($intended) {
            return redirect()->to($intended);
        }

        // Role-based redirect after registration
        if ($user->role === 'Teacher') {
            return redirect()->route('teacher.dashboard');
        }
        if ($user->role === 'Student') {
            return redirect()->route('marketplace.index');
        }

        return redirect('/');
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

            if ($user->role === 'Teacher') {
                return redirect()->route('teacher.dashboard');
            }
            if ($user->role === 'Student') {
                return redirect()->route('marketplace.index');
            }
            return redirect('/');
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

