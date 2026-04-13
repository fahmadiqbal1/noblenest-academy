@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>{{ I18n::get('add_child') }}</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('children.store') }}">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">{{ I18n::get('child_name') }}</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
        </div>
        <div class="mb-3">
            <label for="date_of_birth" class="form-label">Date of Birth</label>
            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" max="{{ now()->toDateString() }}" required>
        </div>
        <div class="mb-3">
            <label for="gender" class="form-label">Gender</label>
            <select class="form-select" id="gender" name="gender" required>
                <option value="">Select...</option>
                <option value="male" @if(old('gender')==='male') selected @endif>Boy</option>
                <option value="female" @if(old('gender')==='female') selected @endif>Girl</option>
                <option value="other" @if(old('gender')==='other') selected @endif>Other</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="preferred_language" class="form-label">{{ I18n::get('preferred_language') }}</label>
            <select class="form-select" id="preferred_language" name="preferred_language">
                <option value="">{{ I18n::get('select_language') }}</option>
                @foreach(['en'=>'English','fr'=>'French','ru'=>'Russian','zh'=>'Mandarin','es'=>'Spanish','ko'=>'Korean','ur'=>'Urdu','ar'=>'Arabic'] as $code=>$lang)
                    <option value="{{ $code }}" @if(old('preferred_language')===$code) selected @endif>{{ $lang }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Is your family Muslim?</label>
            <div class="d-flex gap-3">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="is_muslim" id="muslim_yes" value="1" @if(old('is_muslim')==='1') checked @endif>
                    <label class="form-check-label" for="muslim_yes">Yes</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="is_muslim" id="muslim_no" value="0" @if(old('is_muslim', '0')==='0') checked @endif>
                    <label class="form-check-label" for="muslim_no">No</label>
                </div>
            </div>
            <small class="text-muted">This helps us include Quran and Islamic studies content tailored for your child.</small>
        </div>
        <button type="submit" class="btn btn-success">{{ I18n::get('add_child') }}</button>
        <a href="{{ route('children.index') }}" class="btn btn-secondary">{{ I18n::get('cancel') ?? 'Cancel' }}</a>
    </form>
</div>
@endsection
