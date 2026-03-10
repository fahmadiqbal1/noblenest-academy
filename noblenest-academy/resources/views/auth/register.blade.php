@extends('layouts.app')

@section('meta_title', 'Create Your Account | NobleNest Global Academy')
@section('meta_description', 'Create a NobleNest Global Academy account as a student, teacher, parent, or admin and enter the right learning experience from your first session.')
@section('meta_image', asset('og-register.png'))

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
        <div class="col-xl-11">
            <div class="auth-shell">
                <div class="row g-0 align-items-stretch">
                    <div class="col-lg-5 p-4 p-lg-5 text-white" style="background:linear-gradient(135deg,#0d5c63,#1f7a8c 58%, #f2a541);">
                        <img src="{{ asset('brand/noblenest-logo.svg') }}" alt="NobleNest Global Academy logo" class="auth-brand mb-4">
                        <div class="text-uppercase fw-bold small mb-3" style="letter-spacing:0.14em;">Create your account</div>
                        <h2 class="fw-bold mb-3">{{ I18n::get('register') }}</h2>
                        <p class="mb-4 opacity-75">Start as a student, teacher, parent, or admin and land in the right experience from the first session.</p>
                        <div class="d-grid gap-3">
                            <div class="bg-white bg-opacity-10 rounded-4 p-3">
                                <div class="fw-semibold">Students</div>
                                <div class="small opacity-75">Discover and join live teacher-led courses.</div>
                            </div>
                            <div class="bg-white bg-opacity-10 rounded-4 p-3">
                                <div class="fw-semibold">Teachers</div>
                                <div class="small opacity-75">Publish courses, schedule classes, and manage invitations.</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7 p-4 p-lg-5">
    <h2 class="mb-4">{{ I18n::get('register') }}</h2>
    <form method="POST" action="{{ url('/register') }}">
        @csrf
        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif
        <div class="mb-3">
            <label for="name" class="form-label">{{ I18n::get('name') }}</label>
            <input type="text" class="form-control auth-field" id="name" name="name" value="{{ old('name') }}" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">{{ I18n::get('email') }}</label>
            <input type="email" class="form-control auth-field" id="email" name="email" value="{{ old('email') }}" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">{{ I18n::get('password') }}</label>
            <input type="password" class="form-control auth-field" id="password" name="password" required>
        </div>
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">{{ I18n::get('confirm_password') }}</label>
            <input type="password" class="form-control auth-field" id="password_confirmation" name="password_confirmation" required>
        </div>
        <div class="mb-3">
            <label for="role" class="form-label">{{ I18n::get('register_as') }}</label>
            <select class="form-select auth-field" id="role" name="role" required>
                @php
                    $defaultRole = old('role', str_contains((string) session('url.intended', ''), '/invite/') ? 'Student' : request('role'));
                @endphp
                <option value="Parent" {{ $defaultRole == 'Parent' ? 'selected' : '' }}>{{ I18n::get('parent') }} — Monitor my child's learning</option>
                <option value="Teacher" {{ $defaultRole == 'Teacher' ? 'selected' : '' }}>Teacher — Offer online courses</option>
                <option value="Student" {{ $defaultRole == 'Student' ? 'selected' : '' }}>Student — Find &amp; join courses</option>
                <option value="Admin" {{ old('role') == 'Admin' ? 'selected' : '' }}>{{ I18n::get('admin') }}</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary px-4">{{ I18n::get('register') }}</button>
    </form>
    <div class="mt-3">
        <a href="{{ route('login') }}">{{ I18n::get('already_have_account') }}</a>
    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
