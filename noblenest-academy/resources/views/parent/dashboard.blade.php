@extends('layouts.app')

@section('title', 'Parent Dashboard — Noble Nest Academy')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Welcome back, {{ Auth::user()->name }} 👋</h1>
            <p class="text-muted mb-0">Here's what your children have been up to</p>
        </div>
        @unless($hasSubscription)
        <a href="{{ route('pricing') }}" class="btn btn-warning fw-semibold">
            ✨ Upgrade to Premium
        </a>
        @endunless
    </div>

    {{-- Children Cards --}}
    <div class="row g-4 mb-5">
        @forelse($children as $child)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="avatar avatar-lg bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:52px;height:52px;font-size:1.4rem">
                            {{ mb_substr($child->name, 0, 1) }}
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $child->name }}</h5>
                            <small class="text-muted text-capitalize">{{ $child->age_tier ?? 'learner' }}</small>
                        </div>
                    </div>

                    <div class="row text-center g-2 mb-3">
                        <div class="col-4">
                            <div class="fw-bold text-primary">{{ $child->activity_progress_count }}</div>
                            <div class="small text-muted">Activities</div>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold text-warning">{{ $child->streak_days ?? 0 }}</div>
                            <div class="small text-muted">Day streak</div>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold text-success">{{ $child->age_months ? floor($child->age_months / 12) : '?' }}</div>
                            <div class="small text-muted">Age (yrs)</div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('child.activities', $child) }}" class="btn btn-primary btn-sm flex-fill">
                            🎮 Activities
                        </a>
                        <a href="{{ route('parent.child', $child) }}" class="btn btn-outline-secondary btn-sm flex-fill">
                            Progress
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card border-dashed text-center py-5">
                <div class="card-body">
                    <p class="display-6 mb-2">👶</p>
                    <h5>Add your first child</h5>
                    <p class="text-muted">Create a child profile to start their learning journey</p>
                    <a href="{{ route('children.create') }}" class="btn btn-primary">Add Child</a>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    {{-- Recent Activity --}}
    @if($recentActivity->isNotEmpty())
    <h4 class="mb-3">Recent Activity</h4>
    <div class="list-group shadow-sm">
        @foreach($recentActivity as $item)
        <div class="list-group-item d-flex align-items-center gap-3">
            <span class="fs-4">{{ $item->activity->emoji ?? '📚' }}</span>
            <div class="flex-fill">
                <div class="fw-semibold">{{ $item->activity->title ?? 'Activity' }}</div>
                <small class="text-muted">{{ $item->childProfile->name }} · {{ $item->completed_at?->diffForHumans() }}</small>
            </div>
            @if($item->childProfile->share_card_url)
            <a href="{{ $item->childProfile->share_card_url }}" target="_blank" class="btn btn-sm btn-outline-primary">Share 🎉</a>
            @endif
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
