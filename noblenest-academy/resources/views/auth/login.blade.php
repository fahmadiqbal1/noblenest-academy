@extends('layouts.app')

@section('meta_title', 'Login | NobleNest Global Academy')
@section('meta_description', 'Log in to NobleNest Global Academy to continue learning, manage courses, monitor children, or access admin workflows from one secure account.')
@section('meta_image', asset('og-login.png'))

@section('content')
<style>
    .auth-shell,
    .auth-panel {
        background: rgba(255,255,255,0.86);
        border: 1px solid rgba(24,34,47,0.08);
        box-shadow: 0 28px 60px rgba(24,34,47,0.12);
        border-radius: 1.75rem;
    }
    .auth-shell {
        overflow: hidden;
        background:
            radial-gradient(circle at 16% 18%, rgba(242,165,65,0.18), transparent 22%),
            radial-gradient(circle at 85% 14%, rgba(13,92,99,0.18), transparent 25%),
            linear-gradient(145deg, rgba(255,255,255,0.96), rgba(238,244,246,0.94));
    }
    .auth-panel { padding: 1.6rem; }
    .auth-field { min-height: 50px; border-radius: 1rem; }
    .auth-brand {
        width: 88px;
        height: 88px;
        border-radius: 1.4rem;
        box-shadow: 0 20px 40px rgba(0,0,0,0.18);
    }
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="auth-shell">
                <div class="row g-0 align-items-stretch">
                    <div class="col-lg-5 p-4 p-lg-5 text-white" style="background:linear-gradient(135deg,#0d5c63,#1f7a8c 58%, #f2a541);">
                        <img src="{{ asset('brand/noblenest-logo.svg') }}" alt="NobleNest Global Academy logo" class="auth-brand mb-4">
                        <div class="text-uppercase fw-bold small mb-3" style="letter-spacing:0.14em;">Welcome back</div>
                        <h2 class="fw-bold mb-3">{{ I18n::get('login') }}</h2>
                        <p class="mb-4 opacity-75">Resume learning, manage classrooms, or continue your admin workflow from one secure account.</p>
                        <div class="d-grid gap-3">
                            <div class="bg-white bg-opacity-10 rounded-4 p-3">
                                <div class="fw-semibold">Students</div>
                                <div class="small opacity-75">Jump back into enrolled courses and live sessions.</div>
                            </div>
                            <div class="bg-white bg-opacity-10 rounded-4 p-3">
                                <div class="fw-semibold">Parents and admins</div>
                                <div class="small opacity-75">Monitor learning and manage curriculum operations.</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7 p-4 p-lg-5">
    <h2 class="mb-4">{{ I18n::get('login') }}</h2>
    <form method="POST" action="{{ url('/login') }}">
        @csrf
        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif
        <div class="mb-3">
            <label for="email" class="form-label">{{ I18n::get('email') }}</label>
            <input type="email" class="form-control auth-field" id="email" name="email" value="{{ old('email') }}" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">{{ I18n::get('password') }}</label>
            <input type="password" class="form-control auth-field" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary px-4">{{ I18n::get('login') }}</button>
    </form>
    <div class="mt-3">
        <a href="{{ route('register') }}">{{ I18n::get('dont_have_account') }}</a>
    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
