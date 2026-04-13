@extends('layouts.app')

@section('meta_title', 'Create Your Account | NobleNest Global Academy')
@section('meta_description', 'Create a NobleNest Global Academy account as a student, teacher, parent, or admin and enter the right learning experience from your first session.')
@section('meta_image', asset('og-register.png'))

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
    .role-card {
        cursor: pointer;
        border: 2px solid rgba(124,58,237,0.14);
        border-radius: 0.85rem;
        padding: 0.9rem 0.5rem;
        text-align: center;
        transition: border-color 0.18s, background 0.18s, transform 0.18s;
        background: rgba(255,255,255,0.95);
        user-select: none;
    }
    .role-card:hover { border-color: var(--nn-primary, #7C3AED); background: rgba(124,58,237,0.04); }
    .role-card.selected {
        border-color: var(--nn-primary, #7C3AED);
        background: rgba(124,58,237,0.07);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(124,58,237,0.14);
    }
    .role-card .role-icon { font-size: 1.7rem; margin-bottom: 0.25rem; color: #9CA3AF; transition: color 0.18s; }
    .role-card.selected .role-icon { color: var(--nn-primary, #7C3AED); }
    .role-card .role-name { font-weight: 700; font-size: 0.88rem; color: var(--nn-text, #1E1B4B); }
    .role-card .role-desc { font-size: 0.72rem; color: var(--nn-text-muted, #6B7280); margin-top: 0.15rem; }
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-xl-11">
            <div class="auth-shell">
                <div class="row g-0 align-items-stretch">
                    {{-- Brand / marketing panel --}}
                    <div class="col-lg-5 p-4 p-lg-5 text-white" style="background:linear-gradient(145deg,#6D28D9 0%,#7C3AED 40%,#A78BFA 75%, #F59E0B 100%);">
                        <img src="{{ asset('brand/noblenest-logo.svg') }}" alt="NobleNest Global Academy logo" class="auth-brand mb-4">
                        <div class="text-uppercase fw-bold small mb-3" style="letter-spacing:0.14em; opacity:0.8;">Create your account</div>
                        <h2 class="fw-bold mb-3" style="font-family:'Baloo 2',sans-serif; font-size:1.9rem;">{{ I18n::get('register') }}</h2>
                        <p class="mb-4" style="opacity:0.82; line-height:1.6;">Start as a student, teacher, parent, or admin and land in the right experience from your first session.</p>
                        <div class="d-grid gap-3">
                            <div class="rounded-3 p-3" style="background:rgba(255,255,255,0.12); border:1px solid rgba(255,255,255,0.18);">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="bi bi-mortarboard-fill"></i>
                                    <span class="fw-semibold">Teachers</span>
                                </div>
                                <div class="small" style="opacity:0.78;">Publish courses, schedule classes, and manage invitations.</div>
                            </div>
                            <div class="rounded-3 p-3" style="background:rgba(255,255,255,0.12); border:1px solid rgba(255,255,255,0.18);">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="bi bi-people-fill"></i>
                                    <span class="fw-semibold">Parents</span>
                                </div>
                                <div class="small" style="opacity:0.78;">Give your child a personalised learning journey.</div>
                            </div>
                        </div>
                    </div>

                    {{-- Form panel --}}
                    <div class="col-lg-7 p-4 p-lg-5"
                         x-data="{
                            showPw: false,
                            showPwC: false,
                            loading: false,
                            role: '{{ old('role', str_contains((string) session('url.intended', ''), '/invite/') ? 'Student' : (request('role') ?? 'Parent')) }}'
                         }">
                        <h2 class="mb-1" style="font-family:'Baloo 2',sans-serif; font-size:1.7rem; color:var(--nn-text, #1E1B4B);">Create your account</h2>
                        <p class="text-muted mb-4" style="font-size:0.92rem;">Fill in the details below to get started.</p>

                        <form method="POST" action="{{ url('/register') }}" @submit="loading = true">
                            @csrf
                            @if($errors->any())
                                <div class="alert alert-danger border-0 d-flex align-items-center gap-2 rounded-3 mb-3" role="alert">
                                    <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
                                    <span>{{ $errors->first() }}</span>
                                </div>
                            @endif

                            {{-- Hidden role field bound to Alpine state --}}
                            <input type="hidden" name="role" :value="role">

                            <div class="mb-3">
                                <label for="name" class="form-label auth-form-label">{{ I18n::get('name') }}</label>
                                <input type="text" class="form-control auth-field" id="name" name="name"
                                       value="{{ old('name') }}" required autocomplete="name"
                                       placeholder="Your full name">
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label auth-form-label">{{ I18n::get('email') }}</label>
                                <input type="email" class="form-control auth-field" id="email" name="email"
                                       value="{{ old('email') }}" required autocomplete="email"
                                       placeholder="you@example.com">
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label auth-form-label">{{ I18n::get('password') }}</label>
                                <div class="input-group">
                                    <input :type="showPw ? 'text' : 'password'" class="form-control auth-field"
                                           id="password" name="password" required autocomplete="new-password"
                                           placeholder="Min. 8 characters">
                                    <button class="btn pw-toggle-btn" type="button"
                                            @click="showPw = !showPw"
                                            :aria-label="showPw ? 'Hide password' : 'Show password'">
                                        <i :class="showPw ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label auth-form-label">{{ I18n::get('confirm_password') }}</label>
                                <div class="input-group">
                                    <input :type="showPwC ? 'text' : 'password'" class="form-control auth-field"
                                           id="password_confirmation" name="password_confirmation"
                                           required autocomplete="new-password">
                                    <button class="btn pw-toggle-btn" type="button"
                                            @click="showPwC = !showPwC"
                                            :aria-label="showPwC ? 'Hide password' : 'Show password'">
                                        <i :class="showPwC ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- Visual role selector cards --}}
                            <div class="mb-4">
                                <label class="form-label auth-form-label d-block mb-2">{{ I18n::get('register_as') }}</label>
                                <div class="row g-2">
                                    <div class="col-6 col-sm-3">
                                        <div class="role-card" :class="role === 'Parent' ? 'selected' : ''"
                                             @click="role = 'Parent'" role="button" tabindex="0"
                                             @keydown.enter="role = 'Parent'" @keydown.space.prevent="role = 'Parent'"
                                             aria-pressed="{{ old('role', 'Parent') === 'Parent' ? 'true' : 'false' }}">
                                            <i class="bi bi-people-fill role-icon d-block"></i>
                                            <div class="role-name">Parent</div>
                                            <div class="role-desc">Monitor learning</div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <div class="role-card" :class="role === 'Teacher' ? 'selected' : ''"
                                             @click="role = 'Teacher'" role="button" tabindex="0"
                                             @keydown.enter="role = 'Teacher'" @keydown.space.prevent="role = 'Teacher'">
                                            <i class="bi bi-mortarboard-fill role-icon d-block"></i>
                                            <div class="role-name">Teacher</div>
                                            <div class="role-desc">Create courses</div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <div class="role-card" :class="role === 'Student' ? 'selected' : ''"
                                             @click="role = 'Student'" role="button" tabindex="0"
                                             @keydown.enter="role = 'Student'" @keydown.space.prevent="role = 'Student'">
                                            <i class="bi bi-book-fill role-icon d-block"></i>
                                            <div class="role-name">Student</div>
                                            <div class="role-desc">Join courses</div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <div class="role-card" :class="role === 'Practitioner' ? 'selected' : ''"
                                             @click="role = 'Practitioner'" role="button" tabindex="0"
                                             @keydown.enter="role = 'Practitioner'" @keydown.space.prevent="role = 'Practitioner'">
                                            <i class="bi bi-shield-check-fill role-icon d-block"></i>
                                            <div class="role-name">Practitioner</div>
                                            <div class="role-desc">Wellness review</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary auth-submit-btn" :disabled="loading">
                                    <span x-show="!loading">{{ I18n::get('register') }}</span>
                                    <span x-show="loading" class="d-inline-flex align-items-center justify-content-center gap-2">
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        Creating account&hellip;
                                    </span>
                                </button>
                            </div>
                        </form>

                        <hr class="auth-divider">
                        <p class="text-center text-muted mb-0" style="font-size:0.92rem;">
                            Already have an account?
                            <a href="{{ route('login') }}" class="fw-bold text-decoration-none ms-1" style="color:var(--nn-primary);">Sign in</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
