@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Batch Preview — {{ ucfirst($job->payload['subject'] ?? 'N/A') }} ({{ ucfirst($job->payload['age_tier'] ?? '') }})</h2>
    <a href="{{ route('admin.orchestrator.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <div class="row text-center">
            <div class="col-md-3"><strong>Status:</strong> <span class="badge bg-{{ $job->status === 'completed' ? 'success' : ($job->status === 'failed' ? 'danger' : 'warning') }}">{{ ucfirst($job->status) }}</span></div>
            <div class="col-md-3"><strong>Requested:</strong> {{ $job->payload['count'] ?? '?' }} activities</div>
            <div class="col-md-3"><strong>Generated:</strong> {{ $activities->count() }}</div>
            <div class="col-md-3"><strong>Language:</strong> {{ strtoupper($job->payload['language'] ?? 'en') }}</div>
        </div>
    </div>
</div>

@if($activities->isEmpty())
    <div class="alert alert-info">No activities generated yet. The batch may still be processing.</div>
@else
    <form method="POST" action="{{ route('admin.content-batch.publish', $job) }}">
        @csrf
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="selectAll" onchange="document.querySelectorAll('.activity-check').forEach(c => c.checked = this.checked)">
                <label class="form-check-label fw-bold" for="selectAll">Select All</label>
            </div>
            <button type="submit" class="btn btn-success"><i class="bi bi-check2-all"></i> Publish Selected</button>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th style="width:40px"></th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Subject</th>
                        <th>Age</th>
                        <th>Difficulty</th>
                        <th>Duration</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activities as $activity)
                    <tr>
                        <td><input type="checkbox" name="ids[]" value="{{ $activity->id }}" class="form-check-input activity-check"></td>
                        <td>
                            <strong>{{ $activity->emoji }} {{ $activity->title }}</strong>
                            <br><small class="text-muted">{{ Str::limit($activity->description, 80) }}</small>
                        </td>
                        <td><span class="badge bg-secondary">{{ $activity->activity_type }}</span></td>
                        <td>{{ $activity->subject }}</td>
                        <td>{{ $activity->age_min }}–{{ $activity->age_max }}</td>
                        <td>{{ ucfirst($activity->difficulty ?? 'N/A') }}</td>
                        <td>{{ $activity->duration_minutes ? $activity->duration_minutes . 'min' : '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </form>
@endif
@endsection
