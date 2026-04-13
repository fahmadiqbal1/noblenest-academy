{{-- Maternal module sidebar/navigation partial --}}
@php
    $profile = auth()->user()->maternalProfile;
    $currentRoute = request()->route()->getName();
@endphp

<div class="card border-0 mb-4" style="background:rgba(255,255,255,0.82); border-radius:1.25rem; box-shadow:8px 8px 16px rgba(124,58,237,0.08), -4px -4px 12px rgba(255,255,255,0.6);">
    <div class="card-body p-3">
        {{-- Profile summary --}}
        <div class="text-center mb-3 pb-3 border-bottom">
            <div class="d-inline-flex align-items-center justify-content-center mb-2" style="width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg, #EC4899, #F472B6);color:#fff;font-size:1.5rem;">
                🤰
            </div>
            @if($profile)
                <p class="mb-0 fw-semibold" style="font-family:'Baloo 2',sans-serif;">Week {{ $profile->current_week }}</p>
                <small class="text-muted">Trimester {{ $profile->trimester }}</small>
            @endif
        </div>

        {{-- Nav links --}}
        <nav class="nav flex-column gap-1">
            <a class="nav-link rounded-3 px-3 py-2 {{ str_starts_with($currentRoute, 'maternal.dashboard') ? 'active fw-semibold' : '' }}" href="{{ route('maternal.dashboard') }}" style="{{ str_starts_with($currentRoute, 'maternal.dashboard') ? 'background:var(--nn-primary-soft);color:var(--nn-primary);' : 'color:var(--nn-text);' }}">
                <i class="bi bi-house-heart me-2"></i> Dashboard
            </a>
            <a class="nav-link rounded-3 px-3 py-2 {{ str_starts_with($currentRoute, 'maternal.journey') ? 'active fw-semibold' : '' }}" href="{{ route('maternal.journey') }}" style="{{ str_starts_with($currentRoute, 'maternal.journey') ? 'background:var(--nn-primary-soft);color:var(--nn-primary);' : 'color:var(--nn-text);' }}">
                <i class="bi bi-calendar-week me-2"></i> My Journey
            </a>
            <a class="nav-link rounded-3 px-3 py-2 {{ str_starts_with($currentRoute, 'maternal.content') ? 'active fw-semibold' : '' }}" href="{{ route('maternal.content.index') }}" style="{{ str_starts_with($currentRoute, 'maternal.content') ? 'background:var(--nn-primary-soft);color:var(--nn-primary);' : 'color:var(--nn-text);' }}">
                <i class="bi bi-collection-play me-2"></i> All Content
            </a>
            <a class="nav-link rounded-3 px-3 py-2 {{ str_starts_with($currentRoute, 'maternal.exercises') ? 'active fw-semibold' : '' }}" href="{{ route('maternal.exercises.index') }}" style="{{ str_starts_with($currentRoute, 'maternal.exercises') ? 'background:var(--nn-primary-soft);color:var(--nn-primary);' : 'color:var(--nn-text);' }}">
                <i class="bi bi-person-arms-up me-2"></i> Exercises
            </a>
            <a class="nav-link rounded-3 px-3 py-2 {{ str_starts_with($currentRoute, 'maternal.nutrition') ? 'active fw-semibold' : '' }}" href="{{ route('maternal.nutrition.index') }}" style="{{ str_starts_with($currentRoute, 'maternal.nutrition') ? 'background:var(--nn-primary-soft);color:var(--nn-primary);' : 'color:var(--nn-text);' }}">
                <i class="bi bi-egg-fried me-2"></i> Nutrition
            </a>
            <a class="nav-link rounded-3 px-3 py-2 {{ str_starts_with($currentRoute, 'maternal.herbs') ? 'active fw-semibold' : '' }}" href="{{ route('maternal.herbs.index') }}" style="{{ str_starts_with($currentRoute, 'maternal.herbs') ? 'background:var(--nn-primary-soft);color:var(--nn-primary);' : 'color:var(--nn-text);' }}">
                <i class="bi bi-flower1 me-2"></i> Herbs & Remedies
            </a>
            <a class="nav-link rounded-3 px-3 py-2 {{ str_starts_with($currentRoute, 'maternal.breastfeeding') ? 'active fw-semibold' : '' }}" href="{{ route('maternal.breastfeeding.index') }}" style="{{ str_starts_with($currentRoute, 'maternal.breastfeeding') ? 'background:var(--nn-primary-soft);color:var(--nn-primary);' : 'color:var(--nn-text);' }}">
                <i class="bi bi-heart-pulse me-2"></i> Breastfeeding
            </a>
            <a class="nav-link rounded-3 px-3 py-2 {{ str_starts_with($currentRoute, 'maternal.newborn') ? 'active fw-semibold' : '' }}" href="{{ route('maternal.newborn.index') }}" style="{{ str_starts_with($currentRoute, 'maternal.newborn') ? 'background:var(--nn-primary-soft);color:var(--nn-primary);' : 'color:var(--nn-text);' }}">
                <i class="bi bi-emoji-smile me-2"></i> Newborn Care
            </a>
            <a class="nav-link rounded-3 px-3 py-2 {{ str_starts_with($currentRoute, 'maternal.journal') ? 'active fw-semibold' : '' }}" href="{{ route('maternal.journal.index') }}" style="{{ str_starts_with($currentRoute, 'maternal.journal') ? 'background:var(--nn-primary-soft);color:var(--nn-primary);' : 'color:var(--nn-text);' }}">
                <i class="bi bi-journal-richtext me-2"></i> Journal
            </a>

            <div class="mt-2 mb-1 px-3"><small class="text-muted fw-semibold text-uppercase" style="font-size:0.7rem;letter-spacing:0.05em;">Cultural Techniques</small></div>
            <a class="nav-link rounded-3 px-3 py-2 {{ $currentRoute === 'maternal.techniques.index' && request('culture') === 'chinese' ? 'active fw-semibold' : '' }}" href="{{ route('maternal.techniques.index', 'chinese') }}" style="color:var(--nn-text);">
                <i class="bi bi-yin-yang me-2"></i> Chinese
            </a>
            <a class="nav-link rounded-3 px-3 py-2 {{ $currentRoute === 'maternal.techniques.index' && request('culture') === 'japanese' ? 'active fw-semibold' : '' }}" href="{{ route('maternal.techniques.index', 'japanese') }}" style="color:var(--nn-text);">
                <i class="bi bi-flower2 me-2"></i> Japanese
            </a>
            <a class="nav-link rounded-3 px-3 py-2 {{ $currentRoute === 'maternal.techniques.index' && request('culture') === 'ayurvedic' ? 'active fw-semibold' : '' }}" href="{{ route('maternal.techniques.index', 'ayurvedic') }}" style="color:var(--nn-text);">
                <i class="bi bi-sun me-2"></i> Ayurvedic
            </a>

            <hr class="my-2">
            <a class="nav-link rounded-3 px-3 py-2 text-danger {{ str_starts_with($currentRoute, 'maternal.emergency') ? 'fw-bold' : '' }}" href="{{ route('maternal.emergency-signs') }}">
                <i class="bi bi-exclamation-triangle me-2"></i> Emergency Signs
            </a>
            <a class="nav-link rounded-3 px-3 py-2" href="{{ route('maternal.profile.edit') }}" style="color:var(--nn-text);">
                <i class="bi bi-gear me-2"></i> Profile
            </a>
        </nav>
    </div>
</div>
