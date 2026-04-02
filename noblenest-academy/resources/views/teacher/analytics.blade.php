@extends('layouts.app')
@section('meta_title', 'Teacher Analytics – NobleNest')

@section('content')
<div class="container-fluid py-4">
    <h1 class="h4 fw-bold mb-4">Your Analytics</h1>

    {{-- KPI Row --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body text-center py-4">
                    <div class="display-5 fw-black text-primary">{{ $courses->count() }}</div>
                    <div class="text-muted small fw-semibold mt-1">Courses</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body text-center py-4">
                    <div class="display-5 fw-black text-success">{{ $totalStudents }}</div>
                    <div class="text-muted small fw-semibold mt-1">Active Students</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body text-center py-4">
                    <div class="display-5 fw-black text-warning">{{ number_format($totalRevenue / 100, 2) }}</div>
                    <div class="text-muted small fw-semibold mt-1">Revenue (USD)</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body text-center py-4">
                    <div class="display-5 fw-black text-danger">
                        {{ $avgRating ? number_format($avgRating, 1) : '—' }}
                        <span style="font-size:1.2rem">⭐</span>
                    </div>
                    <div class="text-muted small fw-semibold mt-1">Avg Rating</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Courses Table --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                    <h6 class="fw-bold mb-0">Course Performance</h6>
                </div>
                <div class="card-body px-0 pb-0">
                    <div class="table-responsive">
                        <table class="table mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Course</th>
                                    <th class="text-center">Students</th>
                                    <th class="text-center">Sessions</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($courses as $course)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-semibold">{{ $course->title }}</div>
                                            <div class="text-muted" style="font-size:0.75rem">{{ Str::limit($course->description, 50) }}</div>
                                        </td>
                                        <td class="text-center">{{ $course->enrollments_count }}</td>
                                        <td class="text-center">{{ $course->sessions_count }}</td>
                                        <td class="text-center">
                                            @if($course->published)
                                                <span class="badge bg-success-subtle text-success rounded-pill">Live</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary rounded-pill">Draft</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted py-4">No courses yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Sessions + Ratings --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Recent Sessions</h6>
                    @forelse($recentSessions as $session)
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <div class="fw-semibold small">{{ $session->title }}</div>
                                <div class="text-muted" style="font-size:0.72rem">{{ $session->course->title ?? '—' }}</div>
                            </div>
                            <div class="text-end">
                                <div class="small">{{ $session->starts_at->format('M j') }}</div>
                                <span class="badge rounded-pill
                                    @if($session->status === 'live') bg-danger
                                    @elseif($session->status === 'completed') bg-success-subtle text-success
                                    @else bg-secondary-subtle text-secondary @endif">
                                    {{ ucfirst($session->status) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">No sessions yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Rating Breakdown</h6>
                    @foreach([5,4,3,2,1] as $star)
                        @php $count = $ratingSummary[$star] ?? 0; $total = $ratingSummary->sum(); @endphp
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="small">{{ $star }}★</span>
                            <div class="progress flex-grow-1" style="height:8px">
                                <div class="progress-bar bg-warning"
                                     style="width:{{ $total > 0 ? ($count/$total)*100 : 0 }}%"></div>
                            </div>
                            <span class="text-muted" style="font-size:0.72rem;width:20px">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
