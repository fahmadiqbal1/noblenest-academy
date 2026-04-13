@extends('layouts.app')

@section('meta_title', 'Login | NobleNest Global Academy')
@section('meta_description', 'Log in to NobleNest Global Academy to continue learning, manage courses, monitor children, or access admin workflows from one secure account.')
@section('meta_image', asset('og-login.png'))

@section('content')
<style>
    .auth-shell {
        background: rgba(255,255,255,0.92);
        border: 2px solid rgba(124,58,237,0.10);
        box-shadow: 8px 8px 24px rgba(124,58,237,0.10), -4px -4px 14px rgba(255,255,255,0.7);
        border-radius: 1.75rem;
        overflow: hidden;
    }
    .auth-field {
        min-height: 52px;
        border-radius: 0.85rem;
        border: 2px solid rgba(124,58,237,0.14);
        font-size: 1rem;
        transition: border-color 0.2s, box-shadow 0.2s;
        background: rgba(255,255,255,0.95);
    }
    .auth-field:focus {
        border-color: var(--nn-primary, #7C3AED);
        box-shadow: 0 0 0 3px rgba(124,58,237,0.14);
        outline: none;
        background: #fff;
    }
    .input-group .auth-field {
        border-right: none;
        border-top-right-radius: 0 !important;
        border-bottom-right-radius: 0 !important;
    }
    .input-group .auth-field:focus { z-index: 3; }
    .pw-toggle-btn {
        min-width: 52px;
        border: 2px solid rgba(124,58,237,0.14);
        border-left: none;
        border-top-right-radius: 0.85rem !important;
        border-bottom-right-radius: 0.85rem !important;
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
        background: rgba(255,255,255,0.95);
        color: var(--nn-text-muted, #6B7280);
        transition: color 0.15s, background 0.15s;
    }
    .pw-toggle-btn:hover { background: var(--nn-primary-soft, rgba(124,58,237,0.08)); color: var(--nn-primary, #7C3AED); }
    .auth-brand { width: 88px; height: 88px; border-radius: 1.4rem; box-shadow: 0 20px 40px rgba(0,0,0,0.18); }
    .auth-submit-btn { min-height: 52px; font-size: 1.05rem; letter-spacing: 0.01em; }
    .auth-form-label { font-weight: 600; font-size: 0.92rem; color: var(--nn-text, #1E1B4B); }
    .auth-divider { border: none; height: 1px; background: linear-gradient(to right, transparent, rgba(124,58,237,0.12), transparent); margin: 1.5rem 0; }
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="auth-shell">
                <div class="row g-0 align-items-stretch">
                    {{-- Brand / marketing panel --}}
                    <div class="col-lg-5 p-4 p-lg-5 text-white" style="background:linear-gradient(145deg,#6D28D9 0%,#7C3AED 40%,#A78BFA 75%, #F59E0B 100%);">
                        <img src="{{ asset('brand/noblenest-logo.svg') }}" alt="NobleNest Global Academy logo" class="auth-brand mb-4">
                        <div class="text-uppercase fw-bold small mb-3" style="letter-spacing:0.14em; opacity:0.8;">Welcome back</div>
                        <h2 class="fw-bold mb-3" style="font-family:'Baloo 2',sans-serif; font-size:1.9rem;">{{ I18n::get('login') }}</h2>
                        <p class="mb-4" style="opacity:0.82; line-height:1.6;">Resume learning, manage classrooms, or continue your admin workflow from one secure account.</p>
                        <div class="d-grid gap-3">
                            <div class="rounded-3 p-3" style="background:rgba(255,255,255,0.12); border:1px solid rgba(255,255,255,0.18);">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="bi bi-book-open-fill"></i>
                                    <span class="fw-semibold">Students</span>
                                </div>
                                <div class="small" style="opacity:0.78;">Jump back into enrolled courses and live sessions.</div>
                            </div>
                            <div class="rounded-3 p-3" style="background:rgba(255,255,255,0.12); border:1px solid rgba(255,255,255,0.18);">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="bi bi-people-fill"></i>
                                    <span class="fw-semibold">Parents &amp; admins</span>
                                </div>
                                <div class="small" style="opacity:0.78;">Monitor learning and manage curriculum operations.</div>
                            </div>
                        </div>
                    </div>

                    {{-- Form panel --}}
                    <div class="col-lg-7 p-4 p-lg-5" x-data="{ showPw: false, loading: false }">
                        <h2 class="mb-1" style="font-family:'Baloo 2',sans-serif; font-size:1.7rem; color:var(--nn-text, #1E1B4B);">Sign in to your account</h2>
                        <p class="text-muted mb-4" style="font-size:0.92rem;">Enter your credentials below to continue.</p>

                        <form method="POST" action="{{ url('/login') }}" @submit="loading = true">
                            @csrf
                            @if($errors->any())
                                <div class="alert alert-danger border-0 d-flex align-items-center gap-2 rounded-3 mb-3" role="alert">
                                    <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
                                    <span>{{ $errors->first() }}</span>
                                </div>
                            @endif

                            <div class="mb-3">
                                <label for="email" class="form-label auth-form-label">{{ I18n::get('email') }}</label>
                                <input type="email" class="form-control auth-field" id="email" name="email"
                                       value="{{ old('email') }}" required autocomplete="email"
                                       placeholder="you@example.com">
                            </div>

                            <div class="mb-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label for="password" class="form-label auth-form-label mb-0">{{ I18n::get('password') }}</label>
                                    <a href="{{ route('password.request') }}" class="text-decoration-none fw-semibold"
                                       style="font-size:0.84rem; color:var(--nn-primary);">Forgot password?</a>
                                </div>
                                <div class="input-group">
                                    <input :type="showPw ? 'text' : 'password'" class="form-control auth-field"
                                           id="password" name="password" required autocomplete="current-password">
                                    <button class="btn pw-toggle-btn" type="button"
                                            @click="showPw = !showPw"
                                            :aria-label="showPw ? 'Hide password' : 'Show password'">
                                        <i :class="showPw ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary auth-submit-btn" :disabled="loading">
                                    <span x-show="!loading">{{ I18n::get('login') }}</span>
                                    <span x-show="loading" class="d-inline-flex align-items-center justify-content-center gap-2">
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        Signing in&hellip;
                                    </span>
                                </button>
                            </div>
                        </form>

                        <hr class="auth-divider">
                        <p class="text-center text-muted mb-0" style="font-size:0.92rem;">
                            Don&rsquo;t have an account?
                            <a href="{{ route('register') }}" class="fw-bold text-decoration-none ms-1" style="color:var(--nn-primary);">Create one free</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
