@extends('layouts.app')

@section('content')
<div class="p-5 mb-4 bg-white rounded-3 shadow-sm hero">
  <div class="container py-5">
    <h1 class="display-5 fw-bold">{{ I18n::get('home_title') }}</h1>
    <p class="col-lg-8 fs-5 mt-3">{{ I18n::get('home_subtitle') }}</p>
    <div class="d-flex gap-2 mt-3">
      <a class="btn btn-primary btn-lg" href="#assistantModal" data-bs-toggle="modal">{{ I18n::get('get_weekly_plan') }}</a>
      <a class="btn btn-outline-secondary btn-lg" href="/admin/courses">{{ I18n::get('manage_courses') }}</a>
    </div>
  </div>
</div>

@php
  $user = Auth::user();
  $role = $user->role ?? null;
  $theme = session('theme', $role === 'Parent' ? 'professional' : ($role === 'Student' ? 'playful' : 'professional'));
  $isPlayful = $theme === 'playful';
@endphp
@if($user)
  @if($role === 'Parent')
    <div class="row g-4 mb-4">
      <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
          <div class="card-body">
            <h4 class="card-title mb-3"><i class="bi bi-bar-chart-line text-primary"></i> Child Progress</h4>
            <ul class="list-group list-group-flush">
              @foreach($user->children ?? [] as $child)
                <li class="list-group-item d-flex align-items-center justify-content-between">
                  <span><i class="bi bi-person-circle me-2"></i>{{ $child->name }}</span>
                  <span class="badge bg-success">{{ $child->progress ?? '0%' }}</span>
                </li>
              @endforeach
            </ul>
            <a href="/children" class="btn btn-outline-primary mt-3">Manage Children</a>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
          <div class="card-body">
            <h4 class="card-title mb-3"><i class="bi bi-bell text-info"></i> Announcements</h4>
            <ul class="list-unstyled mb-0">
              <li><i class="bi bi-star-fill text-warning"></i> New STEM activities added!</li>
              <li><i class="bi bi-gift text-success"></i> Invite friends and earn rewards.</li>
            </ul>
            <a href="/profile" class="btn btn-outline-info mt-3">View Profile</a>
          </div>
        </div>
      </div>
    </div>
  @elseif($role === 'Student')
    <div class="row g-4 mb-4">
      <div class="col-12">
        <div class="card shadow-lg border-0 h-100 text-center playful-font" style="background:linear-gradient(120deg,#ffe6fa 0%,#e0f7fa 100%);">
          <div class="card-body">
            <h2 class="mb-3"><i class="bi bi-emoji-smile text-pink"></i> Welcome, {{ $user->name }}!</h2>
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
            <a href="/activities" class="btn btn-lg btn-pink mt-2"><i class="bi bi-controller"></i> Start Learning</a>
          </div>
        </div>
      </div>
    </div>
  @endif
@endif

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
    } catch (\Throwable $e) {
      // ignore DB errors in contexts where tables may not be migrated (e.g., tests)
    }
  @endphp
  <div class="alert {{ $subscription ? 'alert-success' : 'alert-warning' }} mt-4">
    @if($subscription)
      Subscription active until <strong>{{ optional($subscription->ends_at)->format('F j, Y') }}</strong>.
    @else
      No active subscription. <a href="/checkout" class="btn btn-sm btn-primary ms-2">Subscribe now</a>
    @endif
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
<script>
// Confetti on achievement (for kids)
@if($isPlayful)
  setTimeout(()=>{
    if(document.querySelector('.progress-bar.bg-pink') && parseInt(document.querySelector('.progress-bar.bg-pink').innerText) >= 100) {
      import('https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js').then(({default:confetti})=>{
        confetti({particleCount:150,spread:90,origin:{y:0.7}});
      });
    }
  }, 500);
@endif
</script>
@endsection
