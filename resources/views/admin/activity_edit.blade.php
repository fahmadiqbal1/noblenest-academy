@extends('admin.layout')
@section('title', __('Edit Activity'))
@section('content')
<div class="container py-4">
    <h1 class="mb-4">{{ __('Edit Activity') }}</h1>
    <form method="POST" action="{{ route('admin.activities.update', $activity) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">{{ __('Title') }}</label>
            <input type="text" name="title" class="form-control" required value="{{ old('title', $activity->title) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('Description') }}</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description', $activity->description) }}</textarea>
        </div>
        <div class="row mb-3">
            <div class="col">
                <label class="form-label">{{ __('Age Min') }}</label>
                <input type="number" name="age_min" class="form-control" min="0" max="10" required value="{{ old('age_min', $activity->age_min) }}">
            </div>
            <div class="col">
                <label class="form-label">{{ __('Age Max') }}</label>
                <input type="number" name="age_max" class="form-control" min="0" max="10" required value="{{ old('age_max', $activity->age_max) }}">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('Skill') }}</label>
            <select name="skill" class="form-select" required>
                <option value="">{{ __('Select Skill') }}</option>
                <option value="sensory" @if(old('skill', $activity->skill)=='sensory') selected @endif>{{ __('Sensory & Motor') }}</option>
                <option value="language" @if(old('skill', $activity->skill)=='language') selected @endif>{{ __('Language') }}</option>
                <option value="cognitive" @if(old('skill', $activity->skill)=='cognitive') selected @endif>{{ __('Cognitive') }}</option>
                <option value="social" @if(old('skill', $activity->skill)=='social') selected @endif>{{ __('Social-Emotional') }}</option>
                <option value="creativity" @if(old('skill', $activity->skill)=='creativity') selected @endif>{{ __('Creativity') }}</option>
                <option value="etiquette" @if(old('skill', $activity->skill)=='etiquette') selected @endif>{{ __('Etiquette') }}</option>
                <option value="stem" @if(old('skill', $activity->skill)=='stem') selected @endif>{{ __('STEM') }}</option>
                <option value="chivalry" @if(old('skill', $activity->skill)=='chivalry') selected @endif>{{ __('Chivalry') }}</option>
                <option value="manners" @if(old('skill', $activity->skill)=='manners') selected @endif>{{ __('Manners') }}</option>
                <option value="royal_etiquette" @if(old('skill', $activity->skill)=='royal_etiquette') selected @endif>{{ __('Royal Etiquette') }}</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('Language') }}</label>
            <select name="language" class="form-select" required>
                <option value="">{{ __('Select Language') }}</option>
                <option value="en" @if(old('language', $activity->language)=='en') selected @endif>{{ __('English') }}</option>
                <option value="fr" @if(old('language', $activity->language)=='fr') selected @endif>{{ __('French') }}</option>
                <option value="ru" @if(old('language', $activity->language)=='ru') selected @endif>{{ __('Russian') }}</option>
                <option value="zh" @if(old('language', $activity->language)=='zh') selected @endif>{{ __('Mandarin') }}</option>
                <option value="es" @if(old('language', $activity->language)=='es') selected @endif>{{ __('Spanish') }}</option>
                <option value="ko" @if(old('language', $activity->language)=='ko') selected @endif>{{ __('Korean') }}</option>
                <option value="ur" @if(old('language', $activity->language)=='ur') selected @endif>{{ __('Urdu') }}</option>
                <option value="ar" @if(old('language', $activity->language)=='ar') selected @endif>{{ __('Arabic') }}</option>
            </select>
        </div>
        <button class="btn btn-primary">{{ __('Update Activity') }}</button>
        <a href="{{ route('admin.curriculum') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
    </form>
</div>
@endsection

