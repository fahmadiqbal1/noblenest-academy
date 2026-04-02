@extends('layouts.app')

@section('title', 'Refer Friends — Noble Nest Academy')

@section('content')
<div class="container py-4" style="max-width: 700px;">
    <h2 class="fw-bold mb-1">Invite Friends 🎁</h2>
    <p class="text-muted mb-4">When a friend signs up with your link, you both get 1 month free!</p>

    {{-- Referral link box --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <label class="form-label fw-semibold">Your referral link</label>
            <div class="input-group">
                <input type="text" class="form-control" id="refLink" value="{{ url('/ref/' . Auth::user()->referral_code) }}" readonly>
                <button class="btn btn-primary" onclick="copyRefLink()" id="copyBtn">Copy</button>
            </div>
            <p class="text-muted small mt-2">Share via WhatsApp, email, or social media</p>
            <div class="d-flex gap-2 mt-3">
                <a href="https://wa.me/?text={{ urlencode('Join me on Noble Nest Academy — learning made magical for kids! Use my link: ' . url('/ref/' . Auth::user()->referral_code)) }}"
                   class="btn btn-success btn-sm" target="_blank" rel="noopener noreferrer">
                    📱 WhatsApp
                </a>
                <a href="mailto:?subject={{ urlencode('Join Noble Nest Academy') }}&body={{ urlencode('I\'ve been using Noble Nest Academy with my kids and thought you\'d love it! Sign up here: ' . url('/ref/' . Auth::user()->referral_code)) }}"
                   class="btn btn-outline-secondary btn-sm">
                    📧 Email
                </a>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-4 text-center">
            <div class="h3 fw-bold text-primary">{{ $referrals->total() }}</div>
            <div class="text-muted small">Total invites</div>
        </div>
        <div class="col-4 text-center">
            <div class="h3 fw-bold text-success">{{ $referrals->where('converted_at', '!=', null)->count() }}</div>
            <div class="text-muted small">Converted</div>
        </div>
        <div class="col-4 text-center">
            <div class="h3 fw-bold text-warning">{{ $referrals->where('reward_granted', true)->count() }}</div>
            <div class="text-muted small">Rewards earned</div>
        </div>
    </div>

    {{-- Referral list --}}
    @if($referrals->isNotEmpty())
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold">Referral history</div>
        <ul class="list-group list-group-flush">
            @foreach($referrals as $ref)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <div class="fw-semibold">{{ $ref->referred->name ?? 'Pending...' }}</div>
                    <small class="text-muted">{{ $ref->created_at->format('M d, Y') }}</small>
                </div>
                <span class="badge {{ $ref->converted_at ? 'bg-success' : 'bg-secondary' }}">
                    {{ $ref->converted_at ? 'Converted ✓' : 'Pending' }}
                </span>
            </li>
            @endforeach
        </ul>
    </div>
    {{ $referrals->links('pagination::bootstrap-5') }}
    @endif
</div>

@push('scripts')
<script>
function copyRefLink() {
    const input = document.getElementById('refLink');
    input.select();
    document.execCommand('copy');
    const btn = document.getElementById('copyBtn');
    btn.textContent = 'Copied!';
    setTimeout(() => btn.textContent = 'Copy', 2000);
}
</script>
@endpush
@endsection
