@extends('layouts.app')

@section('meta_title', 'NobleNest Global Academy | Family-First Learning Platform')
@section('meta_description', 'Explore NobleNest Global Academy: a family-first learning platform with adaptive courses, onboarding guidance, and AI support for parents, students, teachers, and admins.')
@section('meta_image', asset('og-home.png'))

@section('content')
<style>
  .hero-stage {
    position: relative;
    overflow: hidden;
    border-radius: var(--nn-radius);
    padding: clamp(1.75rem, 3vw, 3rem);
    background: linear-gradient(145deg, #F5F0FF 0%, rgba(255,255,255,0.92) 50%, #FFF7ED 100%);
    border: var(--nn-border-w) solid var(--nn-border);
    box-shadow: var(--nn-shadow);
  }
  .hero-stage::after {
    content: '';
    position: absolute;
    inset: auto -8% -22% auto;
    width: 320px;
    height: 320px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(124,58,237,0.10), transparent 70%);
  }
  .hero-metric,
  .spotlight-card,
  .dashboard-card {
    background: rgba(255,255,255,0.88);
    border: var(--nn-border-w) solid var(--nn-border);
    box-shadow: var(--nn-shadow);
  }
  .hero-metric {
    border-radius: var(--nn-radius);
    padding: 1rem 1.1rem;
    transition: transform 0.2s var(--nn-bounce);
  }
  .hero-metric:hover { transform: translateY(-4px); }
  .hero-orbit {
    position: relative;
    min-height: 100%;
    display: grid;
    place-items: center;
  }
  .hero-orbit__core {
    width: 280px;
    aspect-ratio: 1;
    border-radius: 50%;
    background: rgba(255,255,255,0.94);
    color: var(--nn-text);
    border: var(--nn-border-w) solid var(--nn-border);
    box-shadow: var(--nn-shadow);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 2rem;
  }
  .hero-orbit__ring,
  .hero-orbit__dot {
    position: absolute;
    border-radius: 50%;
  }
  .hero-orbit__ring {
    inset: 7%;
    border: 2px dashed rgba(37, 99, 235, 0.18);
  }
  .hero-orbit__dot {
    width: 68px;
    height: 68px;
    display: grid;
    place-items: center;
    background: rgba(255,255,255,0.94);
    box-shadow: var(--nn-shadow);
    border: var(--nn-border-w) solid var(--nn-border);
    font-size: 1.35rem;
    color: var(--nn-primary);
    transition: transform 0.3s var(--nn-bounce);
  }
  .hero-orbit__dot:hover { transform: scale(1.12); }
  .hero-orbit__dot--one { top: 8%; left: 12%; }
  .hero-orbit__dot--two { top: 18%; right: 2%; }
  .hero-orbit__dot--three { bottom: 12%; left: 4%; }
  .hero-orbit__dot--four { bottom: 4%; right: 14%; }
  .hero-brand-mark {
    width: 116px;
    height: 116px;
    border-radius: var(--nn-radius);
    box-shadow: var(--nn-shadow);
    margin-bottom: 1rem;
  }
  .spotlight-card,
  .dashboard-card {
    border-radius: var(--nn-radius);
    padding: 1.25rem;
    height: 100%;
    transition: transform 0.2s var(--nn-bounce);
  }
  .spotlight-card:hover,
  .dashboard-card:hover { transform: translateY(-4px); }
  .spotlight-icon {
    width: 56px;
    height: 56px;
    border-radius: var(--nn-radius-sm);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: var(--nn-primary-soft);
    color: var(--nn-primary);
    font-size: 1.4rem;
  }
  .section-eyebrow {
    text-transform: uppercase;
    letter-spacing: 0.14em;
    font-size: 0.78rem;
    font-weight: 800;
    color: var(--nn-primary);
    font-family: 'Baloo 2', sans-serif;
  }
  .dashboard-kpi {
    font-size: clamp(2rem, 4vw, 2.8rem);
    font-weight: 800;
    line-height: 1;
    font-family: 'Baloo 2', sans-serif;
  }
  .subscription-shell {
    border-radius: var(--nn-radius);
    border: var(--nn-border-w) solid var(--nn-border);
    box-shadow: var(--nn-shadow);
  }
  .clay-stat {
    background: rgba(255,255,255,0.88);
    border: var(--nn-border-w) solid var(--nn-border);
    border-radius: var(--nn-radius);
    box-shadow: var(--nn-shadow);
    padding: 1.5rem 1rem;
    transition: transform 0.2s var(--nn-bounce);
  }
  .clay-stat:hover { transform: translateY(-4px); }
  .clay-step {
    background: rgba(255,255,255,0.88);
    border: var(--nn-border-w) solid var(--nn-border);
    border-radius: var(--nn-radius);
    box-shadow: var(--nn-shadow);
    transition: transform 0.2s var(--nn-bounce);
  }
  .clay-step:hover { transform: translateY(-4px); }
  .clay-testimonial {
    background: rgba(255,255,255,0.88);
    border: var(--nn-border-w) solid var(--nn-border);
    border-radius: var(--nn-radius);
    box-shadow: var(--nn-shadow);
  }
  .clay-cta {
    background: linear-gradient(135deg, #7C3AED, #A78BFA);
    border: var(--nn-border-w) solid #7C3AED;
    border-radius: var(--nn-radius);
    box-shadow: var(--nn-shadow);
    color: #fff;
  }
  @media (max-width: 991.98px) {
    .hero-orbit { margin-top: 2rem; }
    .hero-orbit__core { width: 220px; }
  }
  @media (prefers-reduced-motion: reduce) {
    .hero-metric:hover, .spotlight-card:hover, .dashboard-card:hover,
    .clay-stat:hover, .clay-step:hover, .hero-orbit__dot:hover { transform: none; }
  }
</style>

<div class="hero-stage hero mb-5">
  <div class="row align-items-center g-4">
    <div class="col-lg-7 position-relative">
      <div class="section-eyebrow mb-3">Adaptive learning platform</div>
      <h1 class="display-4 fw-bold mb-2" style="font-family:'Baloo 2',sans-serif; color:var(--nn-text);">{{ I18n::get('home_title') }}</h1>
      <div class="mb-3" style="font-size:1.6rem; letter-spacing:0.2em;">🎨 📖 🔬 🎵 🧩 ✍️</div>
      <p class="col-lg-10 fs-5 text-muted mt-2 mb-4">{{ I18n::get('home_subtitle') }}</p>
      <div class="d-flex gap-2 mt-3 flex-wrap">
      <button class="btn btn-primary btn-lg" type="button" onclick="openAIModal()">{{ I18n::get('get_weekly_plan') }}</button>
      @auth
        @if(auth()->user()->role === 'Admin')
          <a class="btn btn-outline-secondary btn-lg" href="{{ route('admin.courses.index') }}">{{ I18n::get('manage_courses') }}</a>
        @else
          <a class="btn btn-outline-secondary btn-lg" href="{{ route('onboarding') }}"><i class="bi bi-rocket-takeoff"></i> Get Started</a>
        @endif
      @else
        <a class="btn btn-outline-secondary btn-lg" href="{{ route('register') }}"><i class="bi bi-person-plus"></i> Register Free</a>
      @endauth
      </div>
      <div class="row g-3 mt-4">
        <div class="col-sm-4">
          <div class="hero-metric">
            <div class="section-eyebrow mb-2">Coverage</div>
            <div class="dashboard-kpi">1200+</div>
            <div class="text-muted small">activities across early years, language, and STEM tracks</div>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="hero-metric">
            <div class="section-eyebrow mb-2">Languages</div>
            <div class="dashboard-kpi">6</div>
            <div class="text-muted small">locales supported for child-friendly delivery and parent guidance</div>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="hero-metric">
            <div class="section-eyebrow mb-2">AI loop</div>
            <div class="dashboard-kpi">Live</div>
            <div class="text-muted small">assistant, job orchestration, review, and publishing in one workflow</div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-5">
      <div class="hero-orbit">
        <div class="hero-orbit__ring"></div>
        <div class="hero-orbit__core">
          <img src="{{ asset('brand/noblenest-logo.svg') }}" alt="NobleNest Global Academy logo" class="hero-brand-mark">
          <div class="section-eyebrow mb-2">NobleNest Global Academy</div>
          <h3 class="fw-bold mb-2">Next-gen family learning</h3>
          <p class="mb-0 small text-muted">A single workspace for curriculum, onboarding, AI support, and role-aware learning journeys.</p>
        </div>
        <div class="hero-orbit__dot hero-orbit__dot--one"><i class="bi bi-translate"></i></div>
        <div class="hero-orbit__dot hero-orbit__dot--two"><i class="bi bi-robot"></i></div>
        <div class="hero-orbit__dot hero-orbit__dot--three"><i class="bi bi-palette2"></i></div>
        <div class="hero-orbit__dot hero-orbit__dot--four"><i class="bi bi-puzzle"></i></div>
      </div>
    </div>
  </div>
</div>

@php
  $user = Auth::user();
  $role = $user->role ?? null;
  $theme = session('theme', $role === 'Parent' ? 'professional' : ($role === 'Student' ? 'playful' : 'professional'));
  $isPlayful = $theme === 'playful';
@endphp

{{-- Admin Dashboard --}}
@if($user && $role === 'Admin')
  <div class="row g-4 mb-4">
    @php
      $courseCount = \App\Models\Course::count();
      $activityCount = \App\Models\Activity::count();
      $userCount = \App\Models\User::where('role', '!=', 'Admin')->count();
      $jobCount = \App\Models\AIJob::where('status', 'queued')->count();
      $pendingJobs = \App\Models\AIJob::where('moderation_status', 'pending')->where('status', 'completed')->count();
    @endphp
    <div class="col-12 mb-2">
      <h4 class="fw-bold text-primary"><i class="bi bi-speedometer2"></i> Admin Dashboard</h4>
    </div>
    <div class="col-6 col-md-3">
      <a href="{{ route('admin.courses.index') }}" class="text-decoration-none">
        <div class="dashboard-card text-center h-100">
          <div>
            <div class="fs-1 text-primary fw-bold">{{ $courseCount }}</div>
            <div class="text-muted small">Courses</div>
          </div>
        </div>
      </a>
    </div>
    <div class="col-6 col-md-3">
      <a href="/admin/activities" class="text-decoration-none">
        <div class="dashboard-card text-center h-100">
          <div>
            <div class="fs-1 text-success fw-bold">{{ $activityCount }}</div>
            <div class="text-muted small">Activities</div>
          </div>
        </div>
      </a>
    </div>
    <div class="col-6 col-md-3">
      <a href="{{ route('admin.analytics.index') }}" class="text-decoration-none">
        <div class="dashboard-card text-center h-100">
          <div>
            <div class="fs-1 text-info fw-bold">{{ $userCount }}</div>
            <div class="text-muted small">Users</div>
          </div>
        </div>
      </a>
    </div>
    <div class="col-6 col-md-3">
      <a href="{{ route('admin.orchestrator.index') }}" class="text-decoration-none">
        <div class="dashboard-card text-center h-100">
          <div>
            <div class="fs-1 fw-bold {{ $pendingJobs > 0 ? 'text-warning' : 'text-secondary' }}">{{ $jobCount + $pendingJobs }}</div>
            <div class="text-muted small">AI Jobs{{ $pendingJobs > 0 ? ' ('.$pendingJobs.' need review)' : '' }}</div>
          </div>
        </div>
      </a>
    </div>
    <div class="col-12">
      <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('admin.orchestrator.index') }}" class="btn btn-primary"><i class="bi bi-robot"></i> AI Orchestrator</a>
        <a href="{{ route('admin.analytics.index') }}" class="btn btn-outline-info"><i class="bi bi-bar-chart-line"></i> Analytics</a>
        <a href="{{ route('admin.courses.create') }}" class="btn btn-outline-success"><i class="bi bi-plus-circle"></i> New Course</a>
        <a href="{{ route('admin.curriculum') }}" class="btn btn-outline-secondary"><i class="bi bi-diagram-3"></i> Curriculum Map</a>
      </div>
    </div>
  </div>
@endif

{{-- Parent Dashboard --}}
@if($user && $role === 'Parent')
  <div class="row g-4 mb-4">
    @php
      $children = \App\Models\ChildProfile::where('parent_id', $user->id)->get();
    @endphp
    <div class="col-md-6">
      <div class="dashboard-card h-100">
        <div>
          <h4 class="card-title mb-3"><i class="bi bi-people text-primary"></i> My Children</h4>
          @forelse($children as $child)
            <div class="d-flex align-items-center justify-content-between mb-2 p-2 bg-light rounded">
              <div>
                <img src="https://api.dicebear.com/7.x/bottts/svg?seed={{ $child->id }}" style="width:36px;height:36px;border-radius:50%;" class="me-2">
                <span class="fw-semibold">{{ $child->name }}</span>
                <span class="text-muted small ms-1">· {{ $child->age_display ?? 'Age unknown' }} · {{ strtoupper($child->preferred_language ?? 'en') }}</span>
              </div>
              <div class="d-flex gap-1">
                <a href="{{ route('children.edit', $child) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
              </div>
            </div>
          @empty
            <p class="text-muted small">No children added yet.</p>
          @endforelse
          <div class="mt-3 d-flex gap-2">
            <a href="{{ route('children.create') }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-plus-circle"></i> Add Child</a>
            <a href="{{ route('children.index') }}" class="btn btn-outline-secondary btn-sm">Manage All</a>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="dashboard-card h-100">
        <div>
          <h4 class="card-title mb-3"><i class="bi bi-bell text-info"></i> What's New</h4>
          <ul class="list-unstyled mb-0">
            <li class="mb-2"><i class="bi bi-star-fill text-warning me-2"></i> New STEM robotics activities added!</li>
            <li class="mb-2"><i class="bi bi-translate text-primary me-2"></i> Korean language pack now available.</li>
            <li class="mb-2"><i class="bi bi-gift text-success me-2"></i> Invite friends and earn rewards.</li>
            <li class="mb-2"><i class="bi bi-robot text-info me-2"></i> AI-generated activity plans ready.</li>
          </ul>
          <a href="{{ route('onboarding') }}" class="btn btn-outline-primary btn-sm mt-3"><i class="bi bi-sliders"></i> Update Preferences</a>
        </div>
      </div>
    </div>
  </div>
@endif

{{-- Student/Child Dashboard --}}
@if($user && ($role === 'Student' || $role === 'Child'))
  <div class="row g-4 mb-4">
    <div class="col-12">
      <div class="dashboard-card h-100 text-center" style="background:linear-gradient(120deg, #F5F0FF 0%, #FFF7ED 100%);">
        <div>
          <h2 class="mb-3"><i class="bi bi-emoji-smile text-warning"></i> Welcome, {{ $user->name }}!</h2>
          <div class="mb-3">
            <span class="badge bg-warning text-dark me-2"><i class="bi bi-star-fill"></i> Keep going!</span>
          </div>
          <a href="{{ route('activities.index') }}" class="btn btn-lg btn-primary mt-2"><i class="bi bi-controller"></i> Start Learning</a>
        </div>
      </div>
    </div>
  </div>
@endif

{{-- Subscription status --}}
@if(Auth::check())
  @php
    $subscription = null;
    try {
      if (class_exists(\App\Models\Subscription::class)) {
        $subscription = \App\Models\Subscription::where('user_id', Auth::id())
          ->where('active', true)
          ->where('ends_at', '>', now())
          ->first();
      }
    } catch (\Throwable $e) { }
  @endphp
  <div class="alert {{ $subscription ? 'alert-success' : 'alert-warning' }} subscription-shell mt-4">
    @if($subscription)
      <i class="bi bi-check-circle-fill me-2"></i> Subscription active until <strong>{{ optional($subscription->ends_at)->format('F j, Y') }}</strong>.
    @else
      <i class="bi bi-exclamation-circle-fill me-2"></i> No active subscription.
      <a href="/checkout" class="btn btn-sm btn-primary ms-2">Subscribe now</a>
    @endif
  </div>
@endif

{{-- Course Category Cards (visible to all) --}}
@if(!$user || in_array($role, ['Parent', 'Admin', null]))
<div class="row g-4 mt-4">
  <div class="col-12">
    <div class="section-eyebrow mb-2">Platform highlights</div>
    <h4 class="fw-bold" style="font-family:'Baloo 2',sans-serif; color:var(--nn-text);">Built to feel alive, guided, and role-aware</h4>
  </div>
  <div class="col-md-4">
    <div class="spotlight-card text-center">
      <div class="spotlight-icon mx-auto mb-3"><i class="bi bi-people-fill"></i></div>
        <h5 class="card-title">Parent Academy</h5>
        <p class="text-muted small">{{ I18n::get('parent_academy_desc') }}</p>
        <span class="badge bg-light text-dark border">6 courses · 57 modules</span>
    </div>
  </div>
  <div class="col-md-4">
    <div class="spotlight-card text-center">
      <div class="spotlight-icon mx-auto mb-3"><i class="bi bi-flower1"></i></div>
        <h5 class="card-title">Early Years (0–6)</h5>
        <p class="text-muted small">{{ I18n::get('early_years_desc') }}</p>
        <span class="badge bg-light text-dark border">1200+ activities · 72 monthly units</span>
    </div>
  </div>
  <div class="col-md-4">
    <div class="spotlight-card text-center">
      <div class="spotlight-icon mx-auto mb-3"><i class="bi bi-cpu-fill"></i></div>
        <h5 class="card-title">STEM (7–10)</h5>
        <p class="text-muted small">{{ I18n::get('stem_desc') }}</p>
        <span class="badge bg-light text-dark border">Robotics · Coding · Web</span>
    </div>
  </div>
</div>
@endif

{{-- ============================================================
     VIRAL MARKETING SECTIONS (public-facing, below the fold)
     ============================================================ --}}
@guest
<div class="mt-5 pt-2">

  {{-- Social Proof Numbers --}}
  <div class="row g-3 text-center mb-5">
    <div class="col-6 col-md-3">
      <div class="clay-stat">
        <div class="dashboard-kpi" style="color:var(--nn-primary)">50K+</div>
        <div class="text-muted small">Children Learning</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="clay-stat">
        <div class="dashboard-kpi" style="color:#7c3aed">120+</div>
        <div class="text-muted small">Countries</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="clay-stat">
        <div class="dashboard-kpi" style="color:var(--nn-accent)">4.9&#9733;</div>
        <div class="text-muted small">Parent Rating</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="clay-stat">
        <div class="dashboard-kpi" style="color:var(--nn-success)">8</div>
        <div class="text-muted small">Languages</div>
      </div>
    </div>
  </div>

  {{-- How It Works --}}
  <h2 class="fw-bold text-center mb-4" style="font-size:1.5rem;font-family:'Baloo 2',sans-serif">Start in 3 Simple Steps</h2>
  <div class="row g-3 mb-5">
    <div class="col-md-4">
      <div class="clay-step d-flex gap-3 align-items-start p-3">
        <div class="spotlight-icon flex-shrink-0"><i class="bi bi-pencil-square"></i></div>
        <div>
          <div class="fw-bold">Create Account</div>
          <div class="text-muted small">Sign up in 30 seconds. No credit card to start.</div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="clay-step d-flex gap-3 align-items-start p-3">
        <div class="spotlight-icon flex-shrink-0"><i class="bi bi-person-hearts"></i></div>
        <div>
          <div class="fw-bold">Add Your Child</div>
          <div class="text-muted small">Date of birth → instant age-adapted curriculum.</div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="clay-step d-flex gap-3 align-items-start p-3">
        <div class="spotlight-icon flex-shrink-0"><i class="bi bi-rocket-takeoff"></i></div>
        <div>
          <div class="fw-bold">Start Learning</div>
          <div class="text-muted small">7 free activities daily. Upgrade anytime.</div>
        </div>
      </div>
    </div>
  </div>

  {{-- Testimonials --}}
  <h2 class="fw-bold text-center mb-4" style="font-size:1.5rem;font-family:'Baloo 2',sans-serif">What Families Say</h2>
  <div class="row g-3 mb-5">
    @foreach([
      ['quote' => 'My daughter went from refusing to read to asking for more activities every day. NobleNest changed everything.', 'author' => 'Amara O.', 'country' => '🇳🇬 Nigeria', 'stars' => 5],
      ['quote' => 'Finally a platform that works in Arabic and adapts to my 3-year-old\'s pace. Incredible!', 'author' => 'Fatima A.', 'country' => '🇵🇰 Pakistan', 'stars' => 5],
      ['quote' => 'The teacher marketplace is fantastic. My son\'s tutor is from Indonesia and he loves the sessions.', 'author' => 'Li Wei', 'country' => '🇨🇳 China', 'stars' => 5],
    ] as $t)
      <div class="col-md-4">
        <div class="clay-testimonial h-100 p-4">
          <div style="color:var(--nn-accent);font-size:0.8rem;margin-bottom:0.5rem">{{ str_repeat('★', $t['stars']) }}</div>
          <p class="mb-3 small" style="font-style:italic">"{{ $t['quote'] }}"</p>
          <div class="fw-bold small">{{ $t['author'] }}</div>
          <div class="text-muted" style="font-size:0.72rem">{{ $t['country'] }}</div>
        </div>
      </div>
    @endforeach
  </div>

  {{-- Final CTA --}}
  <div class="clay-cta text-center py-5 mb-4">
    <h2 class="fw-bold mb-2" style="font-size:1.75rem;font-family:'Baloo 2',sans-serif">Start Your Child's Journey Today</h2>
    <p class="mb-4" style="opacity:0.88">7 free activities. No card required. Cancel anytime.</p>
    <div class="d-flex gap-3 justify-content-center flex-wrap">
      <a href="{{ route('register') }}" class="btn btn-warning rounded-pill px-5 py-2 fw-bold" style="border-width:var(--nn-border-w)">
        Get Started Free →
      </a>
      <a href="{{ route('pricing') }}" class="btn btn-outline-light rounded-pill px-4 py-2" style="border-width:var(--nn-border-w)">
        View Pricing
      </a>
    </div>
    {{-- WhatsApp Share --}}
    <div class="mt-4">
      <a href="https://wa.me/?text={{ urlencode('🌟 My kids are learning on NobleNest Academy – world-class early education in 8 languages! Try it free: ' . url('/')) }}"
         class="btn btn-success btn-sm rounded-pill px-3" target="_blank" rel="noopener">
        <i class="bi bi-whatsapp me-1"></i> Share on WhatsApp
      </a>
    </div>
  </div>

</div>
@endguest
@endsection
