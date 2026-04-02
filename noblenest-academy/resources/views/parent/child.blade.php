@extends('layouts.app')

@section('title', '{{ $child->name }}\'s Progress — Noble Nest Academy')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('parent.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">{{ $child->name }}</li>
        </ol>
    </nav>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center p-4">
                <div class="display-4 mb-2">{{ mb_substr($child->name, 0, 1) }}</div>
                <h4>{{ $child->name }}</h4>
                <span class="badge bg-primary text-capitalize">{{ $child->age_tier ?? 'learner' }}</span>
                <div class="mt-3 d-grid">
                    <a href="{{ route('child.activities', $child) }}" class="btn btn-primary">🎮 Start Learning</a>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5>Progress Overview</h5>
                    <div class="row text-center g-3">
                        <div class="col-4">
                            <div class="h2 text-primary">{{ $progress->total() }}</div>
                            <div class="text-muted small">Total completed</div>
                        </div>
                        <div class="col-4">
                            <div class="h2 text-warning">🔥 {{ $child->streak_days ?? 0 }}</div>
                            <div class="text-muted small">Day streak</div>
                        </div>
                        <div class="col-4">
                            <div class="h2 text-success">{{ $child->age_months ? floor($child->age_months/12) .'y '. ($child->age_months % 12).'m' : '—' }}</div>
                            <div class="text-muted small">Age</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h5 class="mb-3">Activity History</h5>
    <div class="list-group shadow-sm mb-4">
        @forelse($progress as $item)
        <div class="list-group-item d-flex align-items-center gap-3 py-3">
            <span class="fs-3">{{ $item->activity->emoji ?? '📚' }}</span>
            <div class="flex-fill">
                <div class="fw-semibold">{{ $item->activity->title ?? 'Activity' }}</div>
                <small class="text-muted">{{ $item->completed_at?->format('M d, Y \a\t g:i A') }}</small>
            </div>
            <span class="badge bg-success">✓ Done</span>
        </div>
        @empty
        <div class="list-group-item text-center py-4 text-muted">
            No activities completed yet. <a href="{{ route('child.activities', $child) }}">Start now!</a>
        </div>
        @endforelse
    </div>

    {{ $progress->links() }}
</div>
@endsection
