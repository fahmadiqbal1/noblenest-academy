@extends('layouts.app')

@section('content')
@php
    $user = auth()->user();
    $role = $user->role ?? null;
    $theme = session('theme', $role === 'Parent' ? 'professional' : ($role === 'Student' ? 'playful' : 'professional'));
    $isPlayful = $theme === 'playful';

    $supportedLanguages = [
        'en' => 'English',
        'fr' => 'Français',
        'ru' => 'Русский',
        'zh' => '中文',
        'es' => 'Español',
        'ko' => '한국어',
        'ur' => 'اردو',
        'ar' => 'العربية',
    ];
    $currentLang = session('locale', 'en');
    $isRTL = in_array($currentLang, ['ar', 'ur']);
@endphp

<div class="container py-5" @if($isRTL) dir="rtl" class="rtl" @endif>
    <!-- Language Switcher -->
    <div class="d-flex justify-content-end mb-3">
        <form method="POST" action="/set-language" class="d-flex align-items-center gap-2">
            @csrf
            <label for="lang" class="form-label mb-0">🌐</label>
            <select name="lang" id="lang" class="form-select form-select-sm" onchange="this.form.submit()">
                @foreach($supportedLanguages as $code => $label)
                    <option value="{{ $code }}" @if($currentLang === $code) selected @endif>{{ $label }}</option>
                @endforeach
            </select>
        </form>
    </div>
    <!-- Onboarding Modal (First Login) -->
    @if(session('show_onboarding', true))
    <div class="modal fade show" id="onboardingModal" tabindex="-1" style="display:block; background:rgba(0,0,0,0.3);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">👋 {{ I18n::get('welcome_to_academy') ?? 'Welcome to NobleNest Academy!' }}</h5>
                    <button type="button" class="btn-close" onclick="document.getElementById('onboardingModal').style.display='none';"></button>
                </div>
                <div class="modal-body">
                    <ul>
                        <li>{{ I18n::get('onboarding_tip_1') ?? 'Switch languages anytime using the globe icon.' }}</li>
                        <li>{{ I18n::get('onboarding_tip_2') ?? 'Track your child’s progress and get recommendations here.' }}</li>
                        <li>{{ I18n::get('onboarding_tip_3') ?? 'Explore interactive activities and lessons tailored to your child.' }}</li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <form method="POST" action="/dismiss-onboarding">
                        @csrf
                        <button type="submit" class="btn btn-primary">{{ I18n::get('get_started') ?? 'Get Started' }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0 {{ $isPlayful ? 'playful-font' : 'professional-font' }}">
                <div class="card-body text-center">
                    <img src="https://api.dicebear.com/7.x/bottts/svg?seed={{ $user->id }}" class="avatar mb-3" style="width:80px;height:80px;">
                    <h2 class="mb-1 {{ $isPlayful ? 'text-pink' : 'text-primary' }}">{{ $user->name }}</h2>
                    <div class="mb-3 text-muted">{{ $user->email }}</div>
                    @if($role === 'Parent')
                        <h4 class="mt-4 mb-3 text-primary"><i class="bi bi-people"></i> {{ I18n::get('your_children') }}</h4>
                        <ul class="list-group mb-3">
                            @forelse($user->children ?? [] as $child)
                                <li class="list-group-item d-flex align-items-center justify-content-between">
                                    <span class="d-flex align-items-center gap-2">
                                        <img src="https://api.dicebear.com/7.x/bottts/svg?seed={{ $child->id }}" style="width:36px;height:36px;border-radius:50%;border:1.5px solid #eee;">
                                        <span>{{ $child->name }}</span>
                                    </span>
                                    <span class="badge bg-success">{{ $child->progress ?? '0%' }}</span>
                                    <a href="/children/{{ $child->id }}/edit" class="btn btn-sm btn-outline-secondary ms-2"><i class="bi bi-pencil"></i></a>
                                </li>
                            @empty
                                <li class="list-group-item text-muted">{{ I18n::get('no_children') ?? 'No children added yet.' }}</li>
                            @endforelse
                        </ul>
                        <a href="/children/create" class="btn btn-outline-primary mb-2"><i class="bi bi-plus-circle"></i> {{ I18n::get('add_child') }}</a>
                        <a href="/children" class="btn btn-outline-secondary mb-2"><i class="bi bi-gear"></i> {{ I18n::get('manage_children') }}</a>
                        <div class="mt-4">
                            <h5 class="text-info"><i class="bi bi-bar-chart-line"></i> {{ I18n::get('analytics') }}</h5>
                            <div class="progress mb-2" style="height:1.2rem;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width:{{ $user->progress ?? 0 }}%">{{ $user->progress ?? 0 }}%</div>
                            </div>
                            <span class="text-muted">{{ I18n::get('overall_progress') }}</span>
                        </div>
                        <div class="mt-4">
                            <h5 class="text-secondary"><i class="bi bi-person-badge"></i> {{ I18n::get('customize_avatar') ?? 'Customize Avatar' }}</h5>
                            <div class="d-flex gap-2 justify-content-center flex-wrap">
                                @foreach(['bottts','adventurer','micah','identicon'] as $style)
                                    <a href="?avatar_style={{ $style }}" class="avatar-link">
                                        <img src="https://api.dicebear.com/7.x/{$style}/svg?seed={{ $user->id }}" style="width:48px;height:48px;border-radius:50%;border:2px solid #eee;">
                                    </a>
                                @endforeach
                            </div>
                        </div>
                        <div class="mt-4">
                            <h5 class="text-success"><i class="bi bi-lightbulb"></i> {{ I18n::get('recommendations') ?? 'Recommendations' }}</h5>
                            <ul class="list-group mb-2">
                                <li class="list-group-item">{{ I18n::get('weekly_summary') ?? 'Your child completed 3 new activities this week!' }}</li>
                                <li class="list-group-item">{{ I18n::get('suggested_module') ?? 'Try the new tracing activity for fine motor skills.' }}</li>
                                <li class="list-group-item">{{ I18n::get('parent_tip') ?? 'Check out the parent resources for emotional coaching.' }}</li>
                            </ul>
                        </div>
                    @elseif($role === 'Student')
                        <div class="mb-4">
                            <span class="badge bg-pink fs-5">{{ $user->progress ?? '0%' }} Complete</span>
                            <div class="progress mt-2" style="height:1.5rem;">
                                <div class="progress-bar bg-pink" role="progressbar" style="width:{{ $user->progress ?? 0 }}%">{{ $user->progress ?? 0 }}%</div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <span class="badge bg-warning text-dark me-2"><i class="bi bi-star-fill"></i> {{ $user->badges ?? 0 }} Badges</span>
                            <span class="badge bg-info text-dark"><i class="bi bi-trophy"></i> {{ $user->achievements ?? 0 }} Achievements</span>
                        </div>
                        <div class="mb-4">
                            <h5 class="text-pink"><i class="bi bi-map"></i> {{ I18n::get('progress_map') }}</h5>
                            <img src="https://api.dicebear.com/7.x/icons/svg?seed={{ $user->id }}" alt="progress map" style="width:120px;">
                        </div>
                        <div class="mb-4">
                            <h5 class="text-secondary"><i class="bi bi-person-badge"></i> {{ I18n::get('customize_avatar') ?? 'Customize Avatar' }}</h5>
                            <div class="d-flex gap-2 justify-content-center flex-wrap">
                                @foreach(['bottts','adventurer','micah','identicon'] as $style)
                                    <a href="?avatar_style={{ $style }}" class="avatar-link">
                                        <img src="https://api.dicebear.com/7.x/{$style}/svg?seed={{ $user->id }}" style="width:48px;height:48px;border-radius:50%;border:2px solid #eee;">
                                    </a>
                                @endforeach
                            </div>
                        </div>
                        <a href="/activities" class="btn btn-lg btn-pink mt-2"><i class="bi bi-controller"></i> {{ I18n::get('continue_learning') }}</a>
                    @endif
                    <div class="mt-4">
                        <form id="logout-form-profile" action="{{ route('logout') }}" method="POST" class="d-inline">@csrf</form>
                        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form-profile').submit();" class="btn btn-outline-danger"><i class="bi bi-box-arrow-right"></i> {{ I18n::get('logout') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
