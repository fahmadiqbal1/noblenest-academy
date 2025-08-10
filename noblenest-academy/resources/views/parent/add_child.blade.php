@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>{{ I18n::get('add_child') }}</h2>
    <form method="POST" action="{{ url('/children') }}">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">{{ I18n::get('child_name') }}</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="mb-3">
            <label for="age" class="form-label">{{ I18n::get('child_age') }}</label>
            <input type="number" class="form-control" id="age" name="age" min="0" max="10" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">{{ I18n::get('child_email') }}</label>
            <input type="email" class="form-control" id="email" name="email">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">{{ I18n::get('child_password') }}</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>
        <button type="submit" class="btn btn-success">{{ I18n::get('add_child') }}</button>
    </form>
</div>
@endsection
