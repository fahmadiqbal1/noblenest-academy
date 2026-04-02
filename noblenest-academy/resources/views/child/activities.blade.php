@extends('layouts.child')

@section('title', $child->name . '\'s Activities')

@section('content')
<div class="container py-4">
    {{-- Child header --}}
    <div class="d-flex align-items-center gap-3 mb-4">
        <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width:52px;height:52px;font-size:1.3rem">
            {{ mb_substr($child->name, 0, 1) }}
        </div>
        <div>
            <h4 class="mb-0">{{ $child->name }}'s Activities</h4>
            @if($child->streak_days)
            <span class="text-warning fw-semibold">🔥 {{ $child->streak_days }}-day streak!</span>
            @endif
        </div>
        <div class="ms-auto d-flex gap-2">
            @if($child->share_card_url)
            <a href="{{ $child->share_card_url }}" target="_blank" class="btn btn-outline-primary btn-sm">Share Progress 🎉</a>
            @endif
        </div>
    </div>

    @unless($hasSubscription)
    <div class="alert alert-info d-flex align-items-center gap-3 mb-4">
        <span class="fs-4">✨</span>
        <div>
            <strong>Free tier:</strong> First 30 activities free. Unlock unlimited learning with Premium.
            <a href="{{ route('pricing') }}" class="alert-link ms-2">View plans →</a>
        </div>
    </div>
    @endunless

    {{-- Activity grid --}}
    <div class="row g-3">
        @forelse($activities as $activity)
        <div class="col-sm-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm {{ ($activity->locked ?? false) ? 'opacity-75' : '' }}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="fs-2">{{ $activity->emoji ?? '📚' }}</span>
                        @if($activity->is_free)
                        <span class="badge bg-success">Free</span>
                        @elseif($activity->locked ?? false)
                        <span class="badge bg-secondary">🔒 Premium</span>
                        @endif
                    </div>
                    <h6 class="card-title">{{ $activity->title }}</h6>
                    @if($activity->description)
                    <p class="card-text small text-muted">{{ Str::limit($activity->description, 80) }}</p>
                    @endif
                    <div class="mt-auto pt-2">
                        @if($activity->locked ?? false)
                        <a href="{{ route('pricing') }}" class="btn btn-sm btn-outline-primary w-100">Unlock with Premium</a>
                        @else
                        <form action="{{ route('child.activity.complete', [$child, $activity]) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-primary w-100">
                                🎮 Start Activity
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <p class="fs-4 text-muted">No activities found for this age group yet.</p>
            <p>Check back soon — we're adding more every week!</p>
        </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $activities->links() }}
    </div>
</div>
@endsection
