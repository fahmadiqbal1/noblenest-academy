@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>{{ I18n::get('login') }}</h2>
    <form method="POST" action="{{ url('/login') }}">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">{{ I18n::get('email') }}</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">{{ I18n::get('password') }}</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">{{ I18n::get('login') }}</button>
    </form>
    <div class="mt-3">
        <a href="{{ route('register') }}">{{ I18n::get('dont_have_account') }}</a>
    </div>
</div>
@endsection
