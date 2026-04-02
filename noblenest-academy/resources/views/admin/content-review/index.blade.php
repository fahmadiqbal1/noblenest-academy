@extends('layouts.app')
@section('meta_title', 'Content Review Queue – Admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <h1 class="h4 fw-bold mb-0">Content Review Queue</h1>
        <div class="d-flex gap-2">
            <form method="POST" action="{{ route('admin.content-review.approve-all') }}">
                @csrf
                <button class="btn btn-success btn-sm rounded-pill px-3 fw-semibold"
                        onclick="return confirm('Publish all pending activities?')">
                    <i class="bi bi-check-all me-1"></i> Approve All
                </button>
            </form>
            <a href="{{ route('admin.content-batch.create') }}" class="btn btn-primary btn-sm rounded-pill px-3 fw-semibold">
                <i class="bi bi-plus-lg me-1"></i> New Batch
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" class="row g-2 mb-4 align-items-end">
        <div class="col-auto">
            <select name="subject" class="form-select form-select-sm rounded-pill">
                <option value="">All Subjects</option>
                @foreach(['literacy','numeracy','creativity','stem','social','motor'] as $s)
                    <option value="{{ $s }}" @selected(request('subject') === $s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <select name="age_tier" class="form-select form-select-sm rounded-pill">
                <option value="">All Ages</option>
                @foreach(['baby','toddler','preschool','school'] as $t)
                    <option value="{{ $t }}" @selected(request('age_tier') === $t)>{{ ucfirst($t) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <button class="btn btn-outline-secondary btn-sm rounded-pill">Filter</button>
        </div>
    </form>

    @if($activities->isEmpty())
        <div class="alert alert-info rounded-3">
            <i class="bi bi-check2-circle me-2"></i> No pending activities. Queue is clear!
        </div>
    @else
        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width:40px"></th>
                        <th>Title</th>
                        <th>Subject</th>
                        <th>Age Tier</th>
                        <th>Type</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activities as $activity)
                        <tr>
                            <td class="text-center fs-5">{{ $activity->emoji ?? '📚' }}</td>
                            <td class="fw-semibold">{{ $activity->title }}</td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill">
                                    {{ ucfirst($activity->subject ?? '—') }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-info-subtle text-info rounded-pill">
                                    {{ ucfirst($activity->age_tier ?? '—') }}
                                </span>
                            </td>
                            <td class="text-muted small">{{ $activity->type ?? '—' }}</td>
                            <td class="text-muted small">{{ $activity->created_at->diffForHumans() }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <form method="POST" action="{{ route('admin.content-review.approve', $activity) }}">
                                        @csrf
                                        <button class="btn btn-success btn-sm rounded-pill px-2 py-0" title="Approve">
                                            <i class="bi bi-check2"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.content-review.reject', $activity) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm rounded-pill px-2 py-0"
                                                onclick="return confirm('Reject and delete this activity?')" title="Reject">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $activities->links() }}
    @endif
</div>
@endsection
