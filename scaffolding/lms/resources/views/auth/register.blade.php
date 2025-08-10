@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>{{ I18n::get('register') }}</h2>
    <form method="POST" action="{{ url('/register') }}">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">{{ I18n::get('name') }}</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">{{ I18n::get('email') }}</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">{{ I18n::get('password') }}</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">{{ I18n::get('confirm_password') }}</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
        </div>
        <div class="mb-3">
            <label for="role" class="form-label">{{ I18n::get('register_as') }}</label>
            <select class="form-select" id="role" name="role" required>
                <option value="Parent" {{ old('role') == 'Parent' ? 'selected' : '' }}>{{ I18n::get('parent') }}</option>
                <option value="Admin" {{ old('role') == 'Admin' ? 'selected' : '' }}>{{ I18n::get('admin') }}</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">{{ I18n::get('register') }}</button>
    </form>
    <div class="mt-3">
        <a href="{{ route('login') }}">{{ I18n::get('already_have_account') }}</a>
    </div>
</div>
@endsection
