@extends('admin.layout')
@section('title', __('Curriculum Explorer'))
@section('content')
<div class="container py-4">
    <h1 class="mb-4">{{ __('Curriculum Explorer') }}</h1>
    <form class="row g-3 mb-4" method="GET" action="">
        <div class="col-md-3">
            <label class="form-label">{{ __('Age Range') }}</label>
            <select class="form-select" name="age">
                <option value="">{{ __('All') }}</option>
                @for($i=0; $i<=10; $i++)
                    <option value="{{ $i }}" @if(request('age') == $i) selected @endif>{{ $i }}</option>
                @endfor
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('Skill') }}</label>
            <select class="form-select" name="skill">
                <option value="">{{ __('All') }}</option>
                <option value="sensory" @if(request('skill')=='sensory') selected @endif>{{ __('Sensory & Motor') }}</option>
                <option value="language" @if(request('skill')=='language') selected @endif>{{ __('Language') }}</option>
                <option value="cognitive" @if(request('skill')=='cognitive') selected @endif>{{ __('Cognitive') }}</option>
                <option value="social" @if(request('skill')=='social') selected @endif>{{ __('Social-Emotional') }}</option>
                <option value="creativity" @if(request('skill')=='creativity') selected @endif>{{ __('Creativity') }}</option>
                <option value="etiquette" @if(request('skill')=='etiquette') selected @endif>{{ __('Etiquette') }}</option>
                <option value="stem" @if(request('skill')=='stem') selected @endif>{{ __('STEM') }}</option>
                <option value="chivalry" @if(request('skill')=='chivalry') selected @endif>{{ __('Chivalry') }}</option>
                <option value="manners" @if(request('skill')=='manners') selected @endif>{{ __('Manners') }}</option>
                <option value="royal_etiquette" @if(request('skill')=='royal_etiquette') selected @endif>{{ __('Royal Etiquette') }}</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('Language') }}</label>
            <select class="form-select" name="language">
                <option value="">{{ __('All') }}</option>
                <option value="en" @if(request('language')=='en') selected @endif>{{ __('English') }}</option>
                <option value="fr" @if(request('language')=='fr') selected @endif>{{ __('French') }}</option>
                <option value="ru" @if(request('language')=='ru') selected @endif>{{ __('Russian') }}</option>
                <option value="zh" @if(request('language')=='zh') selected @endif>{{ __('Mandarin') }}</option>
                <option value="es" @if(request('language')=='es') selected @endif>{{ __('Spanish') }}</option>
                <option value="ko" @if(request('language')=='ko') selected @endif>{{ __('Korean') }}</option>
                <option value="ur" @if(request('language')=='ur') selected @endif>{{ __('Urdu') }}</option>
                <option value="ar" @if(request('language')=='ar') selected @endif>{{ __('Arabic') }}</option>
            </select>
        </div>
        <div class="col-md-3 align-self-end">
            <button class="btn btn-primary w-100">{{ __('Filter') }}</button>
        </div>
    </form>
    <!-- Add advanced filter chips for quick access -->
    <div class="mb-3">
        <span class="badge bg-info me-1" onclick="window.location='?skill=etiquette'" style="cursor:pointer">{{ __('Etiquette') }}</span>
        <span class="badge bg-warning text-dark me-1" onclick="window.location='?skill=chivalry'" style="cursor:pointer">{{ __('Chivalry') }}</span>
        <span class="badge bg-success me-1" onclick="window.location='?skill=manners'" style="cursor:pointer">{{ __('Manners') }}</span>
        <span class="badge bg-primary me-1" onclick="window.location='?skill=royal_etiquette'" style="cursor:pointer">{{ __('Royal Etiquette') }}</span>
    </div>
    <div class="row">
        @forelse($activities as $activity)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $activity->title }}</h5>
                        <p class="card-text small">{{ __('Age') }}: {{ $activity->age_min }}-{{ $activity->age_max }}<br>
                            {{ __('Skill') }}: {{ __(ucfirst($activity->skill)) }}<br>
                            {{ __('Language') }}: {{ strtoupper($activity->language) }}
                        </p>
                        <a href="{{ route('admin.activities.edit', $activity) }}" class="btn btn-outline-secondary btn-sm">{{ __('Edit') }}</a>
                        <form action="{{ route('admin.activities.destroy', $activity) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm" onclick="return confirm('{{ __('Delete this activity?') }}')">{{ __('Delete') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">{{ __('No activities found for the selected filters.') }}</div>
            </div>
        @endforelse
    </div>
    <div class="mt-4">
        <a href="{{ route('admin.activities.create') }}" class="btn btn-success">{{ __('Add New Activity') }}</a>
    </div>
</div>
@endsection
