@extends('layouts.app')

@section('meta_title', 'Choose Your Language — Noble Nest Academy')

@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center"
     style="background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);">
    <div class="card shadow-lg border-0 rounded-4" style="max-width: 520px; width: 100%;">
        <div class="card-body p-5">

            {{-- Step indicator --}}
            <div class="d-flex justify-content-center gap-2 mb-4">
                <span class="badge rounded-pill bg-warning text-dark px-3 py-2">Step 1 of 3</span>
                <span class="badge rounded-pill bg-light text-muted px-3 py-2">Step 2</span>
                <span class="badge rounded-pill bg-light text-muted px-3 py-2">Step 3</span>
            </div>

            <h1 class="h3 text-center fw-bold mb-2">Welcome to Noble Nest 🌟</h1>
            <p class="text-center text-muted mb-4">What language do you prefer?</p>

            <form action="{{ route('onboarding.step1.store') }}" method="POST">
                @csrf
                @if(request()->filled('ref'))
                    <input type="hidden" name="ref" value="{{ e(request('ref')) }}">
                @endif

                <div class="row g-2 mb-4">
                    @php
                    $locales = [
                        'en' => ['label' => 'English',  'flag' => '🇬🇧'],
                        'ar' => ['label' => 'العربية',   'flag' => '🇸🇦'],
                        'fr' => ['label' => 'Français',  'flag' => '🇫🇷'],
                        'ur' => ['label' => 'اردو',       'flag' => '🇵🇰'],
                        'ru' => ['label' => 'Русский',   'flag' => '🇷🇺'],
                        'zh' => ['label' => '中文',       'flag' => '🇨🇳'],
                        'es' => ['label' => 'Español',   'flag' => '🇪🇸'],
                        'ko' => ['label' => '한국어',     'flag' => '🇰🇷'],
                    ];
                    @endphp

                    @foreach($locales as $code => $info)
                    <div class="col-6">
                        <input type="radio" class="btn-check" name="preferred_language"
                               id="lang_{{ $code }}" value="{{ $code }}"
                               {{ old('preferred_language', 'en') === $code ? 'checked' : '' }}>
                        <label class="btn btn-outline-secondary w-100 py-3 rounded-3 text-start"
                               for="lang_{{ $code }}">
                            <span class="fs-4 me-2">{{ $info['flag'] }}</span>
                            <span class="fw-semibold">{{ $info['label'] }}</span>
                        </label>
                    </div>
                    @endforeach
                </div>

                @error('preferred_language')
                    <div class="alert alert-danger py-2 mb-3">{{ $message }}</div>
                @enderror

                <button type="submit" class="btn btn-warning fw-bold w-100 py-3 rounded-3 fs-5">
                    Continue →
                </button>
            </form>

        </div>
    </div>
</div>
@endsection
