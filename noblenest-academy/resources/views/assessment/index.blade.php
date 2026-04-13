@extends('layouts.app')
@section('meta_title', $child->name . ''s Learning Assessment')

@section('content')
<div class="container py-5" style="max-width:860px">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('parent.child', $child) }}" class="btn btn-outline-secondary btn-sm rounded-pill">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <div>
            <h1 class="h4 fw-bold mb-0">{{ $child->name }}'s Learning Assessment</h1>
            <p class="text-muted small mb-0">{{ $child->age_display }} · Personalised path based on activity history</p>
        </div>
    </div>

    {{-- Progress Overview --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body text-center py-4">
                    <div style="font-size:2.5rem;font-weight:800;color:#7C3AED">{{ $summary['pct'] }}%</div>
                    <div class="text-muted small fw-semibold">Curriculum Coverage</div>
                    <div class="progress mt-2" style="height:6px">
                        <div class="progress-bar" style="width:{{ $summary['pct'] }}%;background:#7C3AED"></div>
                    </div>
                    <div class="text-muted" style="font-size:0.72rem;margin-top:4px">
                        {{ $summary['completed'] }} / {{ $summary['total'] }} activities
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body text-center py-4">
                    <div style="font-size:2.5rem;font-weight:800;color:#f59e0b">🔥 {{ $summary['streak'] }}</div>
                    <div class="text-muted small fw-semibold">Day Streak</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body text-center py-4">
                    <div style="font-size:2.5rem;font-weight:800;color:#7c3aed">{{ count($summary['gaps']) }}</div>
                    <div class="text-muted small fw-semibold">Subject Gaps</div>
                    @if(!empty($summary['gaps']))
                        <div class="mt-2">
                            @foreach($summary['gaps'] as $gap)
                                <span class="badge bg-warning-subtle text-warning rounded-pill me-1">{{ ucfirst($gap) }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Subject Breakdown --}}
        <div class="col-md-5">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Subject Breakdown</h6>
                    @if(empty($summary['subject_breakdown']))
                        <p class="text-muted small">No completed activities yet.</p>
                    @else
                        @foreach($summary['subject_breakdown'] as $subject => $count)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small fw-semibold">{{ ucfirst($subject) }}</span>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress" style="width:120px;height:8px">
                                        <div class="progress-bar bg-info"
                                             style="width:{{ min(100, $count * 10) }}%"></div>
                                    </div>
                                    <span class="text-muted" style="font-size:0.72rem;width:20px">{{ $count }}</span>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        {{-- Today's Recommended Path --}}
        <div class="col-md-7">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Recommended for Today</h6>
                    @if($daily->isEmpty())
                        <p class="text-muted small">No activities to recommend — all done! 🎉</p>
                    @else
                        <div class="row g-2">
                            @foreach($daily as $activity)
                                <div class="col-6">
                                    <div class="d-flex align-items-center gap-2 bg-light rounded-3 p-2">
                                        <span style="font-size:1.4rem">{{ $activity->emoji ?? '📚' }}</span>
                                        <div>
                                            <div class="fw-semibold" style="font-size:0.8rem;line-height:1.2">{{ $activity->title }}</div>
                                            <div class="text-muted" style="font-size:0.65rem">{{ ucfirst($activity->subject ?? '') }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Next Milestones --}}
    @if($next->isNotEmpty())
        <div class="mt-4">
            <h6 class="fw-bold mb-3">Upcoming Milestones</h6>
            <div class="row g-3">
                @foreach($next as $milestone)
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-body p-3">
                                <div class="fw-semibold small">{{ $milestone->name }}</div>
                                <div class="text-muted" style="font-size:0.72rem;margin-top:2px">
                                    {{ ucfirst($milestone->domain ?? '') }} · ~{{ $milestone->typical_age_months }}m
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection
