@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow border-0">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <span class="display-4">🌟</span>
                        <h2 class="fw-bold mt-2">Welcome to Noble Nest Academy!</h2>
                        <p class="text-muted">Let's personalise your experience in 30 seconds.</p>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('onboarding.store') }}">
                        @csrf

                        {{-- Step 1: Language --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Preferred Language</label>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($locales as $code => $name)
                                    <div>
                                        <input type="radio" class="btn-check" name="preferred_language"
                                            id="lang_{{ $code }}" value="{{ $code }}"
                                            {{ old('preferred_language', 'en') === $code ? 'checked' : '' }}>
                                        <label class="btn btn-outline-primary" for="lang_{{ $code }}">{{ $name }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Step 2: Child info --}}
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Child's Name <span class="text-muted small">(optional)</span></label>
                                <input type="text" name="child_name" class="form-control"
                                    placeholder="e.g. Emma" value="{{ old('child_name') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Child's Age</label>
                                <select name="child_age" class="form-select">
                                    <option value="">Select age...</option>
                                    @for($i = 0; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ old('child_age') == $i ? 'selected' : '' }}>{{ $i }} years</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        {{-- Step 3: Daily time --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Daily Learning Time: <span id="minutesLabel">30</span> minutes
                            </label>
                            <input type="range" name="daily_minutes" class="form-range"
                                min="5" max="120" step="5" value="{{ old('daily_minutes', 30) }}"
                                oninput="document.getElementById('minutesLabel').textContent = this.value">
                        </div>

                        {{-- Step 4: Goals --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Learning Goals</label>
                            <div class="row g-2">
                                @php
                                    $goals = [
                                        'language' => '🗣️ Language Skills',
                                        'numeracy' => '🔢 Numeracy',
                                        'creativity' => '🎨 Creativity & Arts',
                                        'stem' => '🤖 STEM / Coding',
                                        'social' => '🤝 Social & Emotional',
                                        'culture' => '🌍 Cultural Awareness',
                                    ];
                                @endphp
                                @foreach($goals as $val => $label)
                                    <div class="col-6 col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="goals[]" value="{{ $val }}" id="goal_{{ $val }}"
                                                {{ in_array($val, old('goals', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="goal_{{ $val }}">{{ $label }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-rocket-takeoff"></i> Let's Start Learning!
                            </button>
                            <a href="{{ route('home') }}" class="btn btn-link text-muted">Skip for now</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
