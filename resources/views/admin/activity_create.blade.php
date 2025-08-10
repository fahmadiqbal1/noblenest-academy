@extends('admin.layout')
@section('title', __('Add Activity'))
@section('content')
<div class="container py-4">
    <h1 class="mb-4">{{ __('Add New Activity') }}</h1>
    <form method="POST" action="{{ route('admin.activities.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">{{ __('Title') }}</label>
            <input type="text" name="title" class="form-control" required value="{{ old('title') }}">
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('Description') }}</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
        </div>
        <div class="row mb-3">
            <div class="col">
                <label class="form-label">{{ __('Age Min') }}</label>
                <input type="number" name="age_min" class="form-control" min="0" max="10" required value="{{ old('age_min') }}">
            </div>
            <div class="col">
                <label class="form-label">{{ __('Age Max') }}</label>
                <input type="number" name="age_max" class="form-control" min="0" max="10" required value="{{ old('age_max') }}">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('Skill') }}</label>
            <select name="skill" class="form-select" required>
                <option value="">{{ __('Select Skill') }}</option>
                <option value="sensory">{{ __('Sensory & Motor') }}</option>
                <option value="language">{{ __('Language') }}</option>
                <option value="cognitive">{{ __('Cognitive') }}</option>
                <option value="social">{{ __('Social-Emotional') }}</option>
                <option value="creativity">{{ __('Creativity') }}</option>
                <option value="etiquette">{{ __('Etiquette') }}</option>
                <option value="stem">{{ __('STEM') }}</option>
                <option value="chivalry">{{ __('Chivalry') }}</option>
                <option value="manners">{{ __('Manners') }}</option>
                <option value="royal_etiquette">{{ __('Royal Etiquette') }}</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('Language') }}</label>
            <select name="language" class="form-select" required>
                <option value="">{{ __('Select Language') }}</option>
                <option value="en">{{ __('English') }}</option>
                <option value="fr">{{ __('French') }}</option>
                <option value="ru">{{ __('Russian') }}</option>
                <option value="zh">{{ __('Mandarin') }}</option>
                <option value="es">{{ __('Spanish') }}</option>
                <option value="ko">{{ __('Korean') }}</option>
                <option value="ur">{{ __('Urdu') }}</option>
                <option value="ar">{{ __('Arabic') }}</option>
            </select>
        </div>
        <button class="btn btn-success">{{ __('Create Activity') }}</button>
        <a href="{{ route('admin.curriculum') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
    </form>
</div>
@endsection

