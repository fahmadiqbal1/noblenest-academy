@extends('layouts.app')

@section('content')
<div class="p-5 mb-4 bg-white rounded-3 shadow-sm hero">
  <div class="container py-5">
    <h1 class="display-5 fw-bold">{{ I18n::get('home_title') }}</h1>
    <p class="col-lg-8 fs-5 mt-3">{{ I18n::get('home_subtitle') }}</p>
    <div class="d-flex gap-2 mt-3 flex-wrap">
      <a class="btn btn-primary btn-lg" href="#assistantModal" data-bs-toggle="modal">{{ I18n::get('get_weekly_plan') }}</a>
      @auth
        @if(auth()->user()->role === 'Admin')
          <a class="btn btn-outline-secondary btn-lg" href="{{ route('admin.courses.index') }}">{{ I18n::get('manage_courses') }}</a>
        @else
          <a class="btn btn-outline-secondary btn-lg" href="{{ route('onboarding.show') }}"><i class="bi bi-rocket-takeoff"></i> Get Started</a>
        @endif
      @else
        <a class="btn btn-outline-secondary btn-lg" href="{{ route('register') }}"><i class="bi bi-person-plus"></i> Register Free</a>
      @endauth
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
        <div class="card shadow-sm border-0 text-center h-100">
          <div class="card-body">
            <div class="fs-1 text-primary fw-bold">{{ $courseCount }}</div>
            <div class="text-muted small">Courses</div>
          </div>
        </div>
      </a>
    </div>
    <div class="col-6 col-md-3">
      <a href="/admin/activities" class="text-decoration-none">
        <div class="card shadow-sm border-0 text-center h-100">
          <div class="card-body">
            <div class="fs-1 text-success fw-bold">{{ $activityCount }}</div>
            <div class="text-muted small">Activities</div>
          </div>
        </div>
      </a>
    </div>
    <div class="col-6 col-md-3">
      <a href="{{ route('admin.analytics.index') }}" class="text-decoration-none">
        <div class="card shadow-sm border-0 text-center h-100">
          <div class="card-body">
            <div class="fs-1 text-info fw-bold">{{ $userCount }}</div>
            <div class="text-muted small">Users</div>
          </div>
        </div>
      </a>
    </div>
    <div class="col-6 col-md-3">
      <a href="{{ route('admin.orchestrator.index') }}" class="text-decoration-none">
        <div class="card shadow-sm border-0 text-center h-100">
          <div class="card-body">
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
      $children = \App\Models\User::where('parent_id', $user->id)->where('role', 'Child')->get();
    @endphp
    <div class="col-md-6">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-body">
          <h4 class="card-title mb-3"><i class="bi bi-people text-primary"></i> My Children</h4>
          @forelse($children as $child)
            <div class="d-flex align-items-center justify-content-between mb-2 p-2 bg-light rounded">
              <div>
                <img src="https://api.dicebear.com/7.x/bottts/svg?seed={{ $child->id }}" style="width:36px;height:36px;border-radius:50%;" class="me-2">
                <span class="fw-semibold">{{ $child->name }}</span>
                <span class="text-muted small ms-1">· Age {{ $child->age ?? '?' }} · {{ strtoupper($child->preferred_language ?? 'en') }}</span>
              </div>
              <a href="{{ route('children.edit', $child) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
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
      <div class="card shadow-sm border-0 h-100">
        <div class="card-body">
          <h4 class="card-title mb-3"><i class="bi bi-bell text-info"></i> What's New</h4>
          <ul class="list-unstyled mb-0">
            <li class="mb-2"><i class="bi bi-star-fill text-warning me-2"></i> New STEM robotics activities added!</li>
            <li class="mb-2"><i class="bi bi-translate text-primary me-2"></i> Korean language pack now available.</li>
            <li class="mb-2"><i class="bi bi-gift text-success me-2"></i> Invite friends and earn rewards.</li>
            <li class="mb-2"><i class="bi bi-robot text-info me-2"></i> AI-generated activity plans ready.</li>
          </ul>
          <a href="{{ route('onboarding.show') }}" class="btn btn-outline-primary btn-sm mt-3"><i class="bi bi-sliders"></i> Update Preferences</a>
        </div>
      </div>
    </div>
  </div>
@endif

{{-- Student/Child Dashboard --}}
@if($user && ($role === 'Student' || $role === 'Child'))
  <div class="row g-4 mb-4">
    <div class="col-12">
      <div class="card shadow-lg border-0 h-100 text-center playful-font" style="background:linear-gradient(120deg,#ffe6fa 0%,#e0f7fa 100%);">
        <div class="card-body">
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
  <div class="alert {{ $subscription ? 'alert-success' : 'alert-warning' }} mt-4">
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
  <div class="col-12"><h4 class="fw-bold text-secondary">Platform Highlights</h4></div>
  <div class="col-md-4">
    <div class="card h-100 shadow-sm border-0">
      <div class="card-body text-center">
        <div class="fs-1 mb-2">👶</div>
        <h5 class="card-title">Parent Academy</h5>
        <p class="text-muted small">{{ I18n::get('parent_academy_desc') }}</p>
        <span class="badge bg-light text-dark border">6 courses · 57 modules</span>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card h-100 shadow-sm border-0">
      <div class="card-body text-center">
        <div class="fs-1 mb-2">🌱</div>
        <h5 class="card-title">Early Years (0–6)</h5>
        <p class="text-muted small">{{ I18n::get('early_years_desc') }}</p>
        <span class="badge bg-light text-dark border">1200+ activities · 72 monthly units</span>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card h-100 shadow-sm border-0">
      <div class="card-body text-center">
        <div class="fs-1 mb-2">🤖</div>
        <h5 class="card-title">STEM (7–10)</h5>
        <p class="text-muted small">{{ I18n::get('stem_desc') }}</p>
        <span class="badge bg-light text-dark border">Robotics · Coding · Web</span>
      </div>
    </div>
  </div>
</div>
@endif

<!-- AI Assistant Modal -->
<div class="modal fade" id="assistantModal" tabindex="-1" aria-labelledby="assistantLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="assistantLabel">{{ I18n::get('ai_onboarding_assistant') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="assistant-chat" class="mb-3" style="max-height:300px;overflow-y:auto;background:#f8f9fa;padding:1rem;border-radius:8px;min-height:120px;"></div>
        <form id="assistant-form" class="d-flex gap-2">
          <input type="text" id="assistant-input" class="form-control" placeholder="{{ I18n::get('ask_ai_placeholder') }}" autocomplete="off" required>
          <button type="submit" class="btn btn-primary">{{ I18n::get('send') }}</button>
        </form>
        <div id="assistant-suggestions" class="mt-3"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ I18n::get('close') }}</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
(function() {
  const chat = document.getElementById('assistant-chat');
  const form = document.getElementById('assistant-form');
  const input = document.getElementById('assistant-input');
  const suggestions = document.getElementById('assistant-suggestions');
  function appendMessage(msg, sender) {
    const div = document.createElement('div');
    div.className = sender === 'user' ? 'text-end mb-2' : 'text-start mb-2';
    div.innerHTML = `<span class="badge bg-${sender==='user'?'primary':'info'}">${sender==='user'?'You':'AI'}</span> <span>${msg}</span>`;
    chat.appendChild(div);
    chat.scrollTop = chat.scrollHeight;
  }
  form.onsubmit = function(e) {
    e.preventDefault();
    const msg = input.value.trim();
    if (!msg) return;
    appendMessage(msg, 'user');
    input.value = '';
    fetch('/ai/assistant/message', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: JSON.stringify({ message: msg })
    })
    .then(r => r.json())
    .then(data => {
      appendMessage(data.reply, 'ai');
      if (data.suggestions) {
        suggestions.innerHTML = data.suggestions.map(s => `<button type='button' class='btn btn-sm btn-outline-secondary m-1'>${s}</button>`).join('');
        Array.from(suggestions.querySelectorAll('button')).forEach(btn => {
          btn.onclick = () => {
            input.value = btn.textContent;
            form.dispatchEvent(new Event('submit'));
          };
        });
      }
    });
  };
})();
</script>
@endsection
