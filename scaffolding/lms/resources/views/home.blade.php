@extends('layouts.app')

@section('content')
<div class="p-5 mb-4 bg-white rounded-3 shadow-sm hero">
  <div class="container py-5">
    <h1 class="display-5 fw-bold">Welcome to <span class="brand-grad">Noble Nest Academy</span></h1>
    <p class="col-lg-8 fs-5 mt-3">An interactive, multilingual LMS for parents and kids (0–10). Explore 1200+ early years activities, language packs, and STEM paths. Get started with our AI assistant for a personalized plan.</p>
    <div class="d-flex gap-2 mt-3">
      <a class="btn btn-primary btn-lg" href="#assistantModal" data-bs-toggle="modal">Get a Weekly Plan</a>
      <a class="btn btn-outline-secondary btn-lg" href="/admin/courses">Manage Courses</a>
    </div>
  </div>
</div>

<div class="row g-3">
  <div class="col-md-4">
    <div class="card h-100">
      <div class="card-body">
        <h5 class="card-title">Parent Academy</h5>
        <p class="card-text">Child psychology, etiquette, mannerism, chivalry, cultural parenting styles.</p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card h-100">
      <div class="card-body">
        <h5 class="card-title">Early Years (0–6)</h5>
        <p class="card-text">1200+ activities: tracing, language & literacy, numeracy, arts, motor skills, cultural studies.</p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card h-100">
      <div class="card-body">
        <h5 class="card-title">STEM (7–10)</h5>
        <p class="card-text">Robotics simulation, block-based coding, game design, and web basics (Bootstrap).</p>
      </div>
    </div>
  </div>
</div>

<!-- AI Assistant Modal -->
<div class="modal fade" id="assistantModal" tabindex="-1" aria-labelledby="assistantLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="assistantLabel">AI Onboarding Assistant</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="assistant-log" class="mb-3" style="max-height: 40vh; overflow:auto;">
          <div class="alert alert-info">Hi! Tell me your child's age and preferred language (EN/FR/RU/ZH/ES/KO). I’ll suggest a weekly plan.</div>
        </div>
        <div class="input-group">
          <input id="assistant-input" type="text" class="form-control" placeholder="Type your message...">
          <button id="assistant-send" class="btn btn-primary" type="button">Send</button>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
(function(){
  const input = document.getElementById('assistant-input');
  const sendBtn = document.getElementById('assistant-send');
  const log = document.getElementById('assistant-log');
  const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  function appendBubble(text, type) {
    const div = document.createElement('div');
    div.className = 'alert ' + (type === 'user' ? 'alert-secondary' : 'alert-primary');
    div.textContent = text;
    log.appendChild(div);
    log.scrollTop = log.scrollHeight;
  }

  async function sendMessage() {
    const msg = input.value.trim();
    if (!msg) return;
    appendBubble(msg, 'user');
    input.value = '';

    try {
      const resp = await fetch('/ai/assistant/message', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf
        },
        body: JSON.stringify({ message: msg })
      });
      const data = await resp.json();
      appendBubble(data.reply || 'Hello! How can I help?', 'bot');
    } catch (e) {
      appendBubble('Sorry, something went wrong. Please try again.', 'bot');
    }
  }

  sendBtn?.addEventListener('click', sendMessage);
  input?.addEventListener('keydown', function(e){ if (e.key === 'Enter') sendMessage(); });
})();
</script>
@endsection
