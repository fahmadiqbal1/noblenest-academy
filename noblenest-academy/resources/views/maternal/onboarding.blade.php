@extends('layouts.app')

@section('title', 'Maternal Wellness Onboarding — Noble Nest Academy')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center mb-3" style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg, #EC4899, #F472B6);color:#fff;font-size:2rem;">
                    🤰
                </div>
                <h2 style="font-family:'Baloo 2',sans-serif; color:var(--nn-text);">Begin Your Maternal Wellness Journey</h2>
                <p class="text-muted">Ancient wisdom meets modern care. Tell us about your pregnancy so we can personalize your experience.</p>
            </div>

            <div class="card border-0" style="background:rgba(255,255,255,0.88); border-radius:1.25rem; box-shadow:8px 8px 16px rgba(124,58,237,0.08), -4px -4px 12px rgba(255,255,255,0.6);">
                <div class="card-body p-4">
                    @if($errors->any())
                        <div class="alert alert-danger border-0 rounded-3 mb-4">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('maternal.onboarding.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="due_date" class="form-label fw-semibold">Expected Due Date</label>
                            <input type="date" name="due_date" id="due_date" class="form-control rounded-3" value="{{ old('due_date') }}" required min="{{ now()->format('Y-m-d') }}">
                        </div>

                        <div class="mb-3">
                            <label for="pregnancy_week" class="form-label fw-semibold">Current Pregnancy Week</label>
                            <input type="number" name="pregnancy_week" id="pregnancy_week" class="form-control rounded-3" value="{{ old('pregnancy_week') }}" required min="1" max="42" placeholder="e.g. 12">
                            <small class="text-muted">How many weeks pregnant are you?</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Health Conditions <small class="text-muted">(if any)</small></label>
                            <div class="row g-2">
                                @foreach(['gestational_diabetes', 'hypertension', 'preeclampsia', 'anemia', 'thyroid_disorder', 'placenta_previa', 'multiple_pregnancy'] as $condition)
                                    <div class="col-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="health_conditions[]" value="{{ $condition }}" id="cond_{{ $condition }}" {{ in_array($condition, old('health_conditions', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="cond_{{ $condition }}">{{ str_replace('_', ' ', ucfirst($condition)) }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <small class="text-muted mt-1 d-block">This helps us filter content that may not be suitable for your situation.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Dietary Restrictions <small class="text-muted">(optional)</small></label>
                            <div class="row g-2">
                                @foreach(['vegetarian', 'vegan', 'gluten_free', 'dairy_free', 'nut_allergy', 'halal', 'kosher'] as $diet)
                                    <div class="col-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="dietary_restrictions[]" value="{{ $diet }}" id="diet_{{ $diet }}" {{ in_array($diet, old('dietary_restrictions', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="diet_{{ $diet }}">{{ str_replace('_', ' ', ucfirst($diet)) }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="consent_accepted" id="consent_accepted" value="1" required>
                                <label class="form-check-label" for="consent_accepted">
                                    I understand that the content provided is <strong>educational only</strong> and does not replace professional medical advice. I consent to the storage of my health data, which is encrypted and never shared with third parties.
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn w-100 fw-semibold rounded-pill py-2" style="background:linear-gradient(135deg, #EC4899, #F472B6); color:#fff; font-size:1.05rem;">
                            Start My Journey <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
