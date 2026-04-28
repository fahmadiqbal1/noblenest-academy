@extends('layouts.app')

@section('title', 'New Journal Entry — Maternal Wellness')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-3 d-none d-lg-block">
            @include('maternal.partials.sidebar')
        </div>
        <div class="col-lg-9">
            <h3 class="mb-4" style="font-family:'Baloo 2',sans-serif; color:var(--nn-text);">
                <i class="bi bi-pencil me-2" style="color:#7C3AED;"></i> New Journal Entry
            </h3>

            <div class="card border-0" style="background:rgba(255,255,255,0.88); border-radius:1.25rem; box-shadow:8px 8px 16px rgba(124,58,237,0.08);">
                <div class="card-body p-4">
                    @if($errors->any())
                        <div class="alert alert-danger border-0 rounded-3 mb-4">
                            <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('maternal.journal.store') }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="entry_date" class="form-label fw-semibold">Date</label>
                                <input type="date" name="entry_date" id="entry_date" class="form-control rounded-3" value="{{ old('entry_date', now()->format('Y-m-d')) }}" required max="{{ now()->format('Y-m-d') }}">
                            </div>

                            <div class="col-md-6">
                                <label for="mood" class="form-label fw-semibold">Mood</label>
                                <select name="mood" id="mood" class="form-select rounded-3" required>
                                    <option value="">Select...</option>
                                    @foreach(['great' => '😊 Great', 'good' => '🙂 Good', 'okay' => '😐 Okay', 'low' => '😔 Low', 'bad' => '😢 Bad'] as $val => $label)
                                        <option value="{{ $val }}" {{ old('mood') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="energy_level" class="form-label fw-semibold">Energy Level (1-5)</label>
                                <input type="range" name="energy_level" id="energy_level" class="form-range" min="1" max="5" value="{{ old('energy_level', 3) }}">
                                <div class="d-flex justify-content-between small text-muted">
                                    <span>Low</span><span>High</span>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label for="baby_kicks" class="form-label fw-semibold">Baby Kicks <small class="text-muted">(optional)</small></label>
                                <input type="number" name="baby_kicks" id="baby_kicks" class="form-control rounded-3" value="{{ old('baby_kicks') }}" min="0" placeholder="Count today">
                            </div>

                            <div class="col-md-4">
                                <label for="weight_kg" class="form-label fw-semibold">Weight (kg) <small class="text-muted">(optional)</small></label>
                                <input type="number" name="weight_kg" id="weight_kg" class="form-control rounded-3" value="{{ old('weight_kg') }}" step="0.1" min="30" max="200">
                            </div>

                            <div class="col-md-6">
                                <label for="blood_pressure" class="form-label fw-semibold">Blood Pressure <small class="text-muted">(optional, e.g. 120/80)</small></label>
                                <input type="text" name="blood_pressure" id="blood_pressure" class="form-control rounded-3" value="{{ old('blood_pressure') }}" placeholder="120/80" pattern="\d{2,3}/\d{2,3}">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Symptoms <small class="text-muted">(if any)</small></label>
                                <div class="row g-2">
                                    @foreach(['nausea', 'headache', 'back_pain', 'fatigue', 'swelling', 'heartburn', 'insomnia', 'cramping', 'dizziness', 'bleeding'] as $symptom)
                                        <div class="col-6 col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="symptoms[]" value="{{ $symptom }}" id="sym_{{ $symptom }}" {{ in_array($symptom, old('symptoms', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label {{ in_array($symptom, ['bleeding', 'dizziness']) ? 'text-danger fw-semibold' : '' }}" for="sym_{{ $symptom }}">{{ ucfirst(str_replace('_', ' ', $symptom)) }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="notes" class="form-label fw-semibold">Notes <small class="text-muted">(optional)</small></label>
                                <textarea name="notes" id="notes" rows="3" class="form-control rounded-3" placeholder="How are you feeling today?">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <div class="mt-4 d-flex gap-2">
                            <button type="submit" class="btn rounded-pill fw-semibold px-4" style="background:linear-gradient(135deg, #EC4899, #F472B6); color:#fff;">
                                Save Entry <i class="bi bi-check-lg ms-1"></i>
                            </button>
                            <a href="{{ route('maternal.journal.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
