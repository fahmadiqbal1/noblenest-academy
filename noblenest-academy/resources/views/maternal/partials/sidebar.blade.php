{{-- Maternal module sidebar/navigation partial --}}
@php
    $profile = auth()->user()->maternalProfile;
    $currentRoute = request()->route()->getName();
@endphp

<div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0 mb-4" style="background:rgba(255,255,255,0.82); border-radius:1.25rem; box-shadow:8px 8px 16px rgba(124,58,237,0.08), -4px -4px 12px rgba(255,255,255,0.6);">
    <div class="p-5 p-3">
        {{-- Profile summary --}}
        <div class="text-center mb-3 pb-3 border-b">
            <div class="inline-flex items-center justify-center mb-2" style="width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg, #EC4899, #F472B6);color:#fff;font-size:1.5rem;">
                🤰
            </div>
            @if($profile)
                <p class="mb-0 font-semibold" style="font-family:'Baloo 2',sans-serif;">Week {{ $profile->current_week }}</p>
                <small class="text-[var(--color-text-muted)]">Trimester {{ $profile->trimester }}</small>
            @endif
        </div>

        {{-- Nav links --}}
        <nav class="nav flex-col gap-1">
            <a class="px-4 py-2 rounded-md hover:bg-gray-50 text-sm font-medium rounded-lg px-3 {{ str_starts_with($currentRoute, 'maternal.dashboard') ? 'font-semibold' : '' }}" href="{{ route('maternal.dashboard') }}" style="{{ str_starts_with($currentRoute, 'maternal.dashboard') ? 'background:var(--nn-primary-soft);color:var(--nn-primary);' : 'color:var(--nn-text);' }}">
                <x-ui.icon name="home" class="me-2" /> Dashboard
            </a>
            <a class="px-4 py-2 rounded-md hover:bg-gray-50 text-sm font-medium rounded-lg px-3 {{ str_starts_with($currentRoute, 'maternal.journey') ? 'font-semibold' : '' }}" href="{{ route('maternal.journey') }}" style="{{ str_starts_with($currentRoute, 'maternal.journey') ? 'background:var(--nn-primary-soft);color:var(--nn-primary);' : 'color:var(--nn-text);' }}">
                <x-ui.icon name="calendar" class="me-2" /> My Journey
            </a>
            <a class="px-4 py-2 rounded-md hover:bg-gray-50 text-sm font-medium rounded-lg px-3 {{ str_starts_with($currentRoute, 'maternal.content') ? 'font-semibold' : '' }}" href="{{ route('maternal.content.index') }}" style="{{ str_starts_with($currentRoute, 'maternal.content') ? 'background:var(--nn-primary-soft);color:var(--nn-primary);' : 'color:var(--nn-text);' }}">
                <x-ui.icon name="circle-play" class="me-2" /> All Content
            </a>
            <a class="px-4 py-2 rounded-md hover:bg-gray-50 text-sm font-medium rounded-lg px-3 {{ str_starts_with($currentRoute, 'maternal.exercises') ? 'font-semibold' : '' }}" href="{{ route('maternal.exercises.index') }}" style="{{ str_starts_with($currentRoute, 'maternal.exercises') ? 'background:var(--nn-primary-soft);color:var(--nn-primary);' : 'color:var(--nn-text);' }}">
                <x-ui.icon name="user-round" class="me-2" /> Exercises
            </a>
            <a class="px-4 py-2 rounded-md hover:bg-gray-50 text-sm font-medium rounded-lg px-3 {{ str_starts_with($currentRoute, 'maternal.nutrition') ? 'font-semibold' : '' }}" href="{{ route('maternal.nutrition.index') }}" style="{{ str_starts_with($currentRoute, 'maternal.nutrition') ? 'background:var(--nn-primary-soft);color:var(--nn-primary);' : 'color:var(--nn-text);' }}">
                <x-ui.icon name="egg" class="me-2" /> Nutrition
            </a>
            <a class="px-4 py-2 rounded-md hover:bg-gray-50 text-sm font-medium rounded-lg px-3 {{ str_starts_with($currentRoute, 'maternal.herbs') ? 'font-semibold' : '' }}" href="{{ route('maternal.herbs.index') }}" style="{{ str_starts_with($currentRoute, 'maternal.herbs') ? 'background:var(--nn-primary-soft);color:var(--nn-primary);' : 'color:var(--nn-text);' }}">
                <x-ui.icon name="flower" class="me-2" /> Herbs & Remedies
            </a>
            <a class="px-4 py-2 rounded-md hover:bg-gray-50 text-sm font-medium rounded-lg px-3 {{ str_starts_with($currentRoute, 'maternal.breastfeeding') ? 'font-semibold' : '' }}" href="{{ route('maternal.breastfeeding.index') }}" style="{{ str_starts_with($currentRoute, 'maternal.breastfeeding') ? 'background:var(--nn-primary-soft);color:var(--nn-primary);' : 'color:var(--nn-text);' }}">
                <x-ui.icon name="activity" class="me-2" /> Breastfeeding
            </a>
            <a class="px-4 py-2 rounded-md hover:bg-gray-50 text-sm font-medium rounded-lg px-3 {{ str_starts_with($currentRoute, 'maternal.newborn') ? 'font-semibold' : '' }}" href="{{ route('maternal.newborn.index') }}" style="{{ str_starts_with($currentRoute, 'maternal.newborn') ? 'background:var(--nn-primary-soft);color:var(--nn-primary);' : 'color:var(--nn-text);' }}">
                <x-ui.icon name="smile" class="me-2" /> Newborn Care
            </a>
            <a class="px-4 py-2 rounded-md hover:bg-gray-50 text-sm font-medium rounded-lg px-3 {{ str_starts_with($currentRoute, 'maternal.journal') ? 'font-semibold' : '' }}" href="{{ route('maternal.journal.index') }}" style="{{ str_starts_with($currentRoute, 'maternal.journal') ? 'background:var(--nn-primary-soft);color:var(--nn-primary);' : 'color:var(--nn-text);' }}">
                <x-ui.icon name="notebook-pen" class="me-2" /> Journal
            </a>

            <div class="mt-2 mb-1 px-3"><small class="text-[var(--color-text-muted)] font-semibold uppercase" style="font-size:0.7rem;letter-spacing:0.05em;">Cultural Techniques</small></div>
            <a class="px-4 py-2 rounded-md hover:bg-gray-50 text-sm font-medium rounded-lg px-3 {{ $currentRoute === 'maternal.techniques.index' && request('culture') 'chinese' ? 'font-semibold' : '' }}" href="{{ route('maternal.techniques.index', 'chinese') }}" style="color:var(--nn-text);">
                <x-ui.icon name="sparkles" class="me-2" /> Chinese
            </a>
            <a class="px-4 py-2 rounded-md hover:bg-gray-50 text-sm font-medium rounded-lg px-3 {{ $currentRoute === 'maternal.techniques.index' && request('culture') 'japanese' ? 'font-semibold' : '' }}" href="{{ route('maternal.techniques.index', 'japanese') }}" style="color:var(--nn-text);">
                <x-ui.icon name="flower-2" class="me-2" /> Japanese
            </a>
            <a class="px-4 py-2 rounded-md hover:bg-gray-50 text-sm font-medium rounded-lg px-3 {{ $currentRoute === 'maternal.techniques.index' && request('culture') 'ayurvedic' ? 'font-semibold' : '' }}" href="{{ route('maternal.techniques.index', 'ayurvedic') }}" style="color:var(--nn-text);">
                <x-ui.icon name="sun" class="me-2" /> Ayurvedic
            </a>

            <hr class="my-2">
            <a class="px-4 py-2 rounded-md hover:bg-gray-50 text-sm font-medium rounded-lg px-3 text-red-600 {{ str_starts_with($currentRoute, 'maternal.emergency') ? 'font-bold' : '' }}" href="{{ route('maternal.emergency-signs') }}">
                <x-ui.icon name="alert-triangle" class="me-2" /> Emergency Signs
            </a>
            <a class="px-4 py-2 rounded-md hover:bg-gray-50 text-sm font-medium rounded-lg px-3" href="{{ route('maternal.profile.edit') }}" style="color:var(--nn-text);">
                <x-ui.icon name="settings" class="me-2" /> Profile
            </a>
        </nav>
    </div>
</div>
