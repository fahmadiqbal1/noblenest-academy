@extends('layouts.parent')

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
    <div class="flex justify-end mb-3">
        <form method="POST" action="/set-language" class="flex items-center gap-2">
            @csrf
            <label for="lang" class="block text-sm font-medium text-gray-700 mb-1 mb-0">🌐</label>
            <select name="lang" id="lang" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 px-2 py-1 text-sm" onchange="this.form.submit()">
                @foreach($supportedLanguages as $code => $label)
                    <option value="{{ $code }}" @if($currentLang === $code) selected @endif>{{ $label }}</option>
                @endforeach
            </select>
        </form>
    </div>
    <!-- Onboarding Modal (First Login) -->
    @if(session('show_onboarding', true))
    <div class="fixed inset-0 z-50 hidden" id="onboardingModal" tabindex="-1" style="display:block; background:rgba(0,0,0,0.3);">
        <div class="relative w-full max-w-lg mx-auto mt-12 flex items-center min-h-full">
            <div class="bg-white rounded-xl shadow-xl border border-gray-200">
                <div class="px-5 py-3 border-b border-gray-200 font-semibold flex items-center justify-between">
                    <h5 class="text-lg font-bold">👋 {{ I18n::get('welcome_to_academy') ?? 'Welcome to NobleNest Academy!' }}</h5>
                    <button type="button" class="" onclick="document.getElementById('onboardingModal').style.display='none';"></button>
                </div>
                <div class="p-5">
                    <ul>
                        <li>{{ I18n::get('onboarding_tip_1') ?? 'Switch languages anytime using the globe icon.' }}</li>
                        <li>{{ I18n::get('onboarding_tip_2') ?? 'Track your child’s progress and get recommendations here.' }}</li>
                        <li>{{ I18n::get('onboarding_tip_3') ?? 'Explore interactive activities and lessons tailored to your child.' }}</li>
                    </ul>
                </div>
                <div class="px-5 py-3 border-t border-gray-200 flex justify-end gap-2">
                    <form method="POST" action="/dismiss-onboarding">
                        @csrf
                        <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-violet-600 text-white hover:bg-violet-700">{{ I18n::get('get_started') ?? 'Get Started' }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="flex flex-wrap justify-center">
        <div class="md:w-8/12">
            <div class="glass-panel p-4 {{ $isPlayful ? 'playful-font' : 'professional-font' }}">
                <div class="text-center">
                    <img src="https://api.dicebear.com/7.x/bottts/svg?seed={{ $user->id }}" class="avatar mb-3" style="width:80px;height:80px;">
                    <h2 class="mb-1" style="color:var(--nn-primary);">{{ $user->name }}</h2>
                    <div class="mb-3" style="color:var(--nn-text-muted);">{{ $user->email }}</div>
                    @if($role === 'Parent')
                        <h4 class="mt-4 mb-3" style="color:var(--nn-primary);"><x-ui.icon name="users" /> {{ I18n::get('your_children') }}</h4>
                        <ul class="divide-y divide-gray-200 border border-gray-200 rounded-lg bg-white mb-3">
                            @forelse($user->children ?? [] as $child)
                                <li class="px-4 py-3 flex items-center justify-between"
                                    style="border-color:var(--nn-border);border-radius:var(--nn-radius-sm);margin-bottom:4px;">
                                    <span class="flex items-center gap-2">
                                        <img src="https://api.dicebear.com/7.x/bottts/svg?seed={{ $child->id }}"
                                             style="width:36px;height:36px;border-radius:50%;border:1.5px solid var(--nn-border);">
                                        <span>{{ $child->name }}</span>
                                    </span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" style="background:var(--nn-success);">{{ $child->progress ?? '0%' }}</span>
                                    <a href="/children/{{ $child->id }}/edit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-gray-300 text-gray-700 hover:bg-gray-100 ms-2"><x-ui.icon name="pencil" /></a>
                                </li>
                            @empty
                                <li class="px-4 py-3" style="color:var(--nn-text-muted);">{{ I18n::get('no_children') ?? 'No children added yet.' }}</li>
                            @endforelse
                        </ul>
                        <a href="/children/create" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-violet-600 text-violet-600 hover:bg-violet-600 hover:text-white mb-2"><x-ui.icon name="circle-plus" /> {{ I18n::get('add_child') }}</a>
                        <a href="/children" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-gray-300 text-gray-700 hover:bg-gray-100 mb-2"><x-ui.icon name="settings" /> {{ I18n::get('manage_children') }}</a>
                        <div class="mt-4">
                            <h5 style="color:var(--nn-primary);"><x-ui.icon name="bar-chart" /> {{ I18n::get('analytics') }}</h5>
                            <div class="h-2 w-full bg-gray-200 rounded-full overflow-hidden mb-2" style="height:1.2rem;border-radius:var(--nn-radius-sm);">
                                <div class="h-full bg-violet-600 transition-all" role="progressbar"
                                     style="width:{{ $user->progress ?? 0 }}%;background:var(--nn-primary);">{{ $user->progress ?? 0 }}%</div>
                            </div>
                            <span style="color:var(--nn-text-muted);">{{ I18n::get('overall_progress') }}</span>
                        </div>
                        <div class="mt-4">
                            <h5 style="color:var(--nn-text-muted);"><x-ui.icon name="badge-check" /> {{ I18n::get('customize_avatar') ?? 'Customize Avatar' }}</h5>
                            <div class="flex gap-2 justify-center flex-wrap">
                                @foreach(['bottts','adventurer','micah','identicon'] as $style)
                                    <a href="?avatar_style={{ $style }}" class="avatar-link">
                                        <img src="https://api.dicebear.com/7.x/{$style}/svg?seed={{ $user->id }}"
                                             style="width:48px;height:48px;border-radius:50%;border:2px solid var(--nn-border);">
                                    </a>
                                @endforeach
                            </div>
                        </div>
                        <div class="mt-4">
                            <h5 style="color:var(--nn-success);"><x-ui.icon name="lightbulb" /> {{ I18n::get('recommendations') ?? 'Recommendations' }}</h5>
                            <ul class="divide-y divide-gray-200 border border-gray-200 rounded-lg bg-white mb-2">
                                <li class="px-4 py-3" style="border-color:var(--nn-border);">{{ I18n::get('weekly_summary') ?? 'Your child completed 3 new activities this week!' }}</li>
                                <li class="px-4 py-3" style="border-color:var(--nn-border);">{{ I18n::get('suggested_module') ?? 'Try the new tracing activity for fine motor skills.' }}</li>
                                <li class="px-4 py-3" style="border-color:var(--nn-border);">{{ I18n::get('parent_tip') ?? 'Check out the parent resources for emotional coaching.' }}</li>
                            </ul>
                        </div>
                    @elseif($role === 'Student')
                        <div class="mb-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium text-xl" style="background:var(--nn-primary);">{{ $user->progress ?? '0%' }} Complete</span>
                            <div class="h-2 w-full bg-gray-200 rounded-full overflow-hidden mt-2" style="height:1.5rem;border-radius:var(--nn-radius-sm);">
                                <div class="h-full bg-violet-600 transition-all" role="progressbar"
                                     style="width:{{ $user->progress ?? 0 }}%;background:var(--nn-primary);">{{ $user->progress ?? 0 }}%</div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-600 text-gray-900 me-2"><x-ui.icon name="star" /> {{ $user->badges ?? 0 }} Badges</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" style="background:var(--nn-primary);"><x-ui.icon name="trophy" /> {{ $user->achievements ?? 0 }} Achievements</span>
                        </div>
                        <div class="mb-4">
                            <h5 style="color:var(--nn-primary);"><x-ui.icon name="map" /> {{ I18n::get('progress_map') }}</h5>
                            <img src="https://api.dicebear.com/7.x/icons/svg?seed={{ $user->id }}" alt="progress map" style="width:120px;">
                        </div>
                        <div class="mb-4">
                            <h5 style="color:var(--nn-text-muted);"><x-ui.icon name="badge-check" /> {{ I18n::get('customize_avatar') ?? 'Customize Avatar' }}</h5>
                            <div class="flex gap-2 justify-center flex-wrap">
                                @foreach(['bottts','adventurer','micah','identicon'] as $style)
                                    <a href="?avatar_style={{ $style }}" class="avatar-link">
                                        <img src="https://api.dicebear.com/7.x/{$style}/svg?seed={{ $user->id }}"
                                             style="width:48px;height:48px;border-radius:50%;border:2px solid var(--nn-border);">
                                    </a>
                                @endforeach
                            </div>
                        </div>
                        <a href="/activities" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-violet-600 text-white hover:bg-violet-700 px-5 py-3 text-lg mt-2"><x-ui.icon name="gamepad-2" /> {{ I18n::get('continue_learning') }}</a>
                    @endif
                    <div class="mt-4">
                        <form id="logout-form-profile" action="{{ route('logout') }}" method="POST" class="inline">@csrf</form>
                        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form-profile').submit();" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-red-600 text-red-600 hover:bg-red-600 hover:text-white"><x-ui.icon name="log-out" /> {{ I18n::get('logout') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
