@extends('layouts.app')

@section('title', 'Edit Maternal Profile')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-3 d-none d-lg-block">
            @include('maternal.partials.sidebar')
        </div>
        <div class="col-lg-9">
            <h3 class="mb-4" style="font-family:'Baloo 2',sans-serif; color:var(--nn-text);">
                <i class="bi bi-gear me-2" style="color:#7C3AED;"></i> Maternal Profile
            </h3>

            @if(session('success'))
                <div class="alert alert-success border-0 rounded-3 mb-4">{{ session('success') }}</div>
            @endif

            <div class="card border-0 mb-4" style="background:rgba(255,255,255,0.88); border-radius:1.25rem; box-shadow:8px 8px 16px rgba(124,58,237,0.08);">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('maternal.profile.update') }}">
                        @csrf @method('PUT')

                        <div class="mb-3">
                            <label for="due_date" class="form-label fw-semibold">Expected Due Date</label>
                            <input type="date" name="due_date" id="due_date" class="form-control rounded-3" value="{{ old('due_date', $profile->due_date?->format('Y-m-d')) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Health Conditions</label>
                            <div class="row g-2">
                                @foreach(['gestational_diabetes', 'hypertension', 'preeclampsia', 'anemia', 'thyroid_disorder', 'placenta_previa', 'multiple_pregnancy'] as $condition)
                                    <div class="col-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="health_conditions[]" value="{{ $condition }}" id="cond_{{ $condition }}" {{ in_array($condition, old('health_conditions', $profile->health_conditions ?? [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="cond_{{ $condition }}">{{ str_replace('_', ' ', ucfirst($condition)) }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Dietary Restrictions</label>
                            <div class="row g-2">
                                @foreach(['vegetarian', 'vegan', 'gluten_free', 'dairy_free', 'nut_allergy', 'halal', 'kosher'] as $diet)
                                    <div class="col-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="dietary_restrictions[]" value="{{ $diet }}" id="diet_{{ $diet }}" {{ in_array($diet, old('dietary_restrictions', $profile->dietary_restrictions ?? [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="diet_{{ $diet }}">{{ str_replace('_', ' ', ucfirst($diet)) }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <button type="submit" class="btn rounded-pill fw-semibold px-4" style="background:var(--nn-primary); color:#fff;">
                            Save Changes
                        </button>
                    </form>
                </div>
            </div>

            {{-- Status actions --}}
            <div class="card border-0" style="background:rgba(255,255,255,0.88); border-radius:1.25rem;">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-3">Journey Status: <span class="badge" style="background:{{ $profile->status === 'active' ? '#ECFDF5' : '#FEF3C7' }}; color:{{ $profile->status === 'active' ? '#065F46' : '#92400E' }};">{{ ucfirst($profile->status) }}</span></h6>

                    <div class="d-flex flex-wrap gap-2">
                        @if($profile->status === 'active')
                            <form method="POST" action="{{ route('maternal.profile.pause') }}">
                                @csrf
                                <button type="submit" class="btn btn-outline-warning btn-sm rounded-pill" onclick="return confirm('Are you sure you want to pause your journey?')">
                                    <i class="bi bi-pause-circle me-1"></i> Pause Journey
                                </button>
                            </form>
                        @elseif($profile->status === 'paused')
                            <form method="POST" action="{{ route('maternal.profile.resume') }}">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm rounded-pill">
                                    <i class="bi bi-play-circle me-1"></i> Resume Journey
                                </button>
                            </form>
                        @endif

                        @if(in_array($profile->status, ['active', 'paused']))
                            <form method="POST" action="{{ route('maternal.profile.loss') }}">
                                @csrf
                                <button type="submit" class="btn btn-outline-secondary btn-sm rounded-pill" onclick="return confirm('We are deeply sorry. This will pause your journey. You can resume at any time. Are you sure?')">
                                    <i class="bi bi-heart me-1"></i> Report Loss
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
