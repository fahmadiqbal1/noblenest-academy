@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>{{ I18n::get('edit_child') }}</h2>
    <form method="POST" action="{{ route('children.update', $child) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label">{{ I18n::get('child_name') }}</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $child->name) }}" required>
        </div>
        <div class="mb-3">
            <label for="age" class="form-label">{{ I18n::get('child_age') }}</label>
            <input type="number" class="form-control" id="age" name="age" min="0" max="10" value="{{ old('age', $child->age) }}" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">{{ I18n::get('child_email') }}</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $child->email) }}">
        </div>
        <div class="mb-3">
            <label for="preferred_language" class="form-label">{{ I18n::get('preferred_language') }}</label>
            <select class="form-select" id="preferred_language" name="preferred_language">
                <option value="">{{ I18n::get('select_language') }}</option>
                @foreach(['en'=>'English','fr'=>'French','ru'=>'Russian','zh'=>'Mandarin','es'=>'Spanish','ko'=>'Korean','ur'=>'Urdu','ar'=>'Arabic'] as $code=>$lang)
                    <option value="{{ $code }}" @if(old('preferred_language', $child->preferred_language)==$code) selected @endif>{{ $lang }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-success">{{ I18n::get('update_child') }}</button>
        <a href="{{ route('children.index') }}" class="btn btn-secondary">{{ I18n::get('cancel') }}</a>
    </form>
</div>
@endsection

