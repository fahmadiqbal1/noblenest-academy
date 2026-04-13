@extends('layouts.app')

@section('meta_title', 'Choose Your Language — Noble Nest Academy')

@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center"
     style="background: linear-gradient(135deg, #F5F0FF 0%, #FFFBF0 50%, #FFF7ED 100%);">
    <div class="card border-0 rounded-4" style="max-width: 520px; width: 100%; background:rgba(255,255,255,0.82); border:2px solid rgba(124,58,237,0.10) !important; box-shadow:8px 8px 16px rgba(124,58,237,0.08), -4px -4px 12px rgba(255,255,255,0.6);">
        <div class="card-body p-5">

            {{-- Progress orbs --}}
            <div class="d-flex justify-content-center gap-3 mb-4">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;background:var(--nn-primary, #7C3AED);color:#fff;font-weight:700;font-size:0.85rem;">1</div>
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;background:rgba(124,58,237,0.10);color:var(--nn-primary, #7C3AED);font-weight:700;font-size:0.85rem;">2</div>
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;background:rgba(124,58,237,0.10);color:var(--nn-primary, #7C3AED);font-weight:700;font-size:0.85rem;">3</div>
            </div>

            <h1 class="h3 text-center fw-bold mb-2" style="font-family:'Baloo 2',sans-serif; color:var(--nn-text, #1E1B4B);">Welcome to Noble Nest 🌟</h1>
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

                <button type="submit" class="btn fw-bold w-100 py-3 rounded-3 fs-5 text-white" style="background:linear-gradient(135deg, #7C3AED, #A78BFA); border:none;">
                    Continue →
                </button>
            </form>

        </div>
    </div>
</div>
@endsection
