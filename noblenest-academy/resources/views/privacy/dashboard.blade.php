@extends('layouts.parent')

@section('title', 'My Privacy Dashboard')

@section('content')
<div class="container py-5" style="max-width:760px">
  <h1 class="font-bold mb-1" style="font-size:1.6rem">Privacy &amp; Data Dashboard</h1>
  <p class="text-[var(--color-text-muted)] mb-4">You control your data. Review, export, or permanently delete everything NobleNest holds about you.</p>

  {{-- What We Hold --}}
  <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0 mb-4">
    <div class="p-5 p-4">
      <h2 class="font-bold mb-3" style="font-size:1.1rem">Data We Hold</h2>
      <ul class="list-unstyled mb-0">
        <li class="flex items-center gap-2 py-2 border-b">
          <x-ui.icon name="user" class="text-[var(--color-primary)]" />
          <div>
            <div class="font-semibold">Account</div>
            <div class="text-[var(--color-text-muted)] text-sm">Name · Email · Role · Sign-up Date</div>
          </div>
        </li>
        <li class="flex items-center gap-2 py-2 border-b">
          <x-ui.icon name="users" class="text-emerald-600" />
          <div>
            <div class="font-semibold">{{ $children->count() }} Child Profile(s)</div>
            <div class="text-[var(--color-text-muted)] text-sm">Name · Date of Birth · Learning Progress · Quiz Scores</div>
          </div>
        </li>
        <li class="flex items-center gap-2 py-2">
          <x-ui.icon name="credit-card" class="text-amber-600" />
          <div>
            <div class="font-semibold">{{ $paymentCount }} Payment Record(s)</div>
            <div class="text-[var(--color-text-muted)] text-sm">Amount · Currency · Status — no card numbers stored</div>
          </div>
        </li>
      </ul>
    </div>
  </div>

  {{-- Child Profiles Summary --}}
  @if($children->isNotEmpty())
  <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0 mb-4">
    <div class="p-5 p-4">
      <h2 class="font-bold mb-3" style="font-size:1.1rem">Child Profiles</h2>
      @foreach($children as $child)
        <div class="flex justify-between items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
          <div>
            <div class="font-semibold">{{ $child->name }}</div>
            <div class="text-[var(--color-text-muted)] text-sm">{{ $child->activity_progress_count }} activities completed</div>
          </div>
          <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-50 text-gray-900 border">{{ $child->age_tier ?? 'Seedling' }}</span>
        </div>
      @endforeach
    </div>
  </div>
  @endif

  {{-- Export --}}
  <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0 mb-4">
    <div class="p-5 p-4 flex justify-between items-center">
      <div>
        <div class="font-bold">Download Your Data</div>
        <div class="text-[var(--color-text-muted)] text-sm">Export everything as a JSON file (GDPR Article 20).</div>
      </div>
      <a href="{{ route('privacy.export') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-violet-600 text-violet-600 hover:bg-violet-600 hover:text-white rounded-full">
        <x-ui.icon name="download" class="me-1" /> Export JSON
      </a>
    </div>
  </div>

  {{-- Legal Links --}}
  <div class="flex gap-3 mb-5">
    <a href="/privacy-policy" class="text-[var(--color-text-muted)] text-sm no-underline">Privacy Policy</a>
    <a href="/terms" class="text-[var(--color-text-muted)] text-sm no-underline">Terms of Use</a>
    <a href="mailto:privacy@noblenest.academy" class="text-[var(--color-text-muted)] text-sm no-underline">Contact DPO</a>
  </div>

  {{-- Delete Account --}}
  <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-danger border-opacity-25 mb-5">
    <div class="p-5 p-4">
      <h2 class="font-bold text-red-600 mb-2" style="font-size:1.1rem"><x-ui.icon name="alert-triangle" class="me-1" /> Delete My Account</h2>
      <p class="text-[var(--color-text-muted)] text-sm mb-3">
        This permanently removes your account, all child profiles, activity history, and quiz scores.
        Payment records are anonymised for legal compliance. <strong>This cannot be undone.</strong>
      </p>
      <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-red-600 text-red-600 hover:bg-red-600 hover:text-white rounded-full px-3 py-1.5 text-sm">
        I understand — show delete form
      </button>
      <div id="deleteForm" class="mt-3">
        <form method="POST" action="{{ route('privacy.delete') }}" onsubmit="return confirm('Permanently delete all your data?')">
          @csrf
          @method('DELETE')
          <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Your password</label>
            <input type="password" name="password" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 @error('password') is-invalid @enderror" required autocomplete="current-password">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Type <code>DELETE</code> to confirm</label>
            <input type="text" name="confirm" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" placeholder="DELETE" required pattern="DELETE">
          </div>
          <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-red-600 text-white hover:bg-red-700 rounded-full">Delete Everything</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
