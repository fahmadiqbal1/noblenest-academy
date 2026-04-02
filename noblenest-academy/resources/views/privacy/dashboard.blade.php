@extends('layouts.app')

@section('title', 'My Privacy Dashboard')

@section('content')
<div class="container py-5" style="max-width:760px">
  <h1 class="fw-bold mb-1" style="font-size:1.6rem">Privacy &amp; Data Dashboard</h1>
  <p class="text-muted mb-4">You control your data. Review, export, or permanently delete everything NobleNest holds about you.</p>

  {{-- What We Hold --}}
  <div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
      <h2 class="fw-bold mb-3" style="font-size:1.1rem">Data We Hold</h2>
      <ul class="list-unstyled mb-0">
        <li class="d-flex align-items-center gap-2 py-2 border-bottom">
          <i class="bi bi-person-fill text-primary"></i>
          <div>
            <div class="fw-semibold">Account</div>
            <div class="text-muted small">Name · Email · Role · Sign-up Date</div>
          </div>
        </li>
        <li class="d-flex align-items-center gap-2 py-2 border-bottom">
          <i class="bi bi-people-fill text-success"></i>
          <div>
            <div class="fw-semibold">{{ $children->count() }} Child Profile(s)</div>
            <div class="text-muted small">Name · Date of Birth · Learning Progress · Quiz Scores</div>
          </div>
        </li>
        <li class="d-flex align-items-center gap-2 py-2">
          <i class="bi bi-credit-card-fill text-warning"></i>
          <div>
            <div class="fw-semibold">{{ $paymentCount }} Payment Record(s)</div>
            <div class="text-muted small">Amount · Currency · Status — no card numbers stored</div>
          </div>
        </li>
      </ul>
    </div>
  </div>

  {{-- Child Profiles Summary --}}
  @if($children->isNotEmpty())
  <div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
      <h2 class="fw-bold mb-3" style="font-size:1.1rem">Child Profiles</h2>
      @foreach($children as $child)
        <div class="d-flex justify-content-between align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
          <div>
            <div class="fw-semibold">{{ $child->name }}</div>
            <div class="text-muted small">{{ $child->activity_progress_count }} activities completed</div>
          </div>
          <span class="badge bg-light text-dark border rounded-pill">{{ $child->age_tier ?? 'Seedling' }}</span>
        </div>
      @endforeach
    </div>
  </div>
  @endif

  {{-- Export --}}
  <div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4 d-flex justify-content-between align-items-center">
      <div>
        <div class="fw-bold">Download Your Data</div>
        <div class="text-muted small">Export everything as a JSON file (GDPR Article 20).</div>
      </div>
      <a href="{{ route('privacy.export') }}" class="btn btn-outline-primary rounded-pill px-4">
        <i class="bi bi-download me-1"></i> Export JSON
      </a>
    </div>
  </div>

  {{-- Legal Links --}}
  <div class="d-flex gap-3 mb-5">
    <a href="/privacy-policy" class="text-muted small text-decoration-none">Privacy Policy</a>
    <a href="/terms" class="text-muted small text-decoration-none">Terms of Use</a>
    <a href="mailto:privacy@noblenest.academy" class="text-muted small text-decoration-none">Contact DPO</a>
  </div>

  {{-- Delete Account --}}
  <div class="card border-danger border-opacity-25 rounded-4 mb-5">
    <div class="card-body p-4">
      <h2 class="fw-bold text-danger mb-2" style="font-size:1.1rem"><i class="bi bi-exclamation-triangle-fill me-1"></i> Delete My Account</h2>
      <p class="text-muted small mb-3">
        This permanently removes your account, all child profiles, activity history, and quiz scores.
        Payment records are anonymised for legal compliance. <strong>This cannot be undone.</strong>
      </p>
      <button class="btn btn-outline-danger rounded-pill btn-sm" data-bs-toggle="collapse" data-bs-target="#deleteForm">
        I understand — show delete form
      </button>
      <div id="deleteForm" class="collapse mt-3">
        <form method="POST" action="{{ route('privacy.delete') }}" onsubmit="return confirm('Permanently delete all your data?')">
          @csrf
          @method('DELETE')
          <div class="mb-3">
            <label class="form-label small fw-semibold">Your password</label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="current-password">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Type <code>DELETE</code> to confirm</label>
            <input type="text" name="confirm" class="form-control" placeholder="DELETE" required pattern="DELETE">
          </div>
          <button type="submit" class="btn btn-danger rounded-pill px-4">Delete Everything</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
