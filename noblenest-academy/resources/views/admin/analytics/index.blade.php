@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-primary mb-0"><i class="bi bi-bar-chart-line"></i> Curriculum Analytics</h1>
        <form method="POST" action="{{ route('admin.analytics.reportEmail') }}">
            @csrf
            <button class="btn btn-outline-primary btn-sm"><i class="bi bi-envelope"></i> Email Monthly Report</button>
        </form>
    </div>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary shadow-sm">
                <div class="card-body text-center">
                    <div class="fs-2 fw-bold">{{ $totalSkills }}</div>
                    <div class="small">Total Skills / Domains</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success shadow-sm">
                <div class="card-body text-center">
                    <div class="fs-2 fw-bold">{{ $totalActivities }}</div>
                    <div class="small">Total Activities</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info shadow-sm">
                <div class="card-body text-center">
                    <div class="fs-2 fw-bold">{{ $monthlyCompletions->sum('completions') }}</div>
                    <div class="small">Total Completions</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning shadow-sm">
                <div class="card-body text-center">
                    <div class="fs-2 fw-bold">{{ $coverage ? collect($coverage)->where('count', '<', 2)->count() : 0 }}</div>
                    <div class="small">Low Coverage Skills</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Coverage Alerts --}}
    @foreach($coverage as $row)
        @if($row['count'] < 2)
            <div class="alert alert-warning py-2 mb-2">
                <strong>Low coverage:</strong> Skill <b>{{ $row['skill'] }}</b> has only {{ $row['count'] }} activities.
                <a href="{{ route('admin.activities.create') }}" class="btn btn-sm btn-outline-primary ms-2">Add Activity</a>
            </div>
        @endif
    @endforeach

    <div class="row g-4 mt-2">
        {{-- Coverage Chart --}}
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header fw-bold"><i class="bi bi-graph-up"></i> Activities per Skill</div>
                <div class="card-body">
                    <canvas id="coverageChart" height="100"></canvas>
                </div>
            </div>
        </div>

        {{-- Monthly Completions --}}
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header fw-bold"><i class="bi bi-calendar-check"></i> Monthly Completions</div>
                <div class="card-body">
                    <canvas id="completionsChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Coverage Table --}}
    <div class="card shadow-sm mt-4">
        <div class="card-header fw-bold"><i class="bi bi-table"></i> Skill Coverage Detail</div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Skill / Domain</th>
                        <th>Activities</th>
                        <th>Age Range</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($coverage as $row)
                    <tr>
                        <td>{{ $row['skill'] }}</td>
                        <td><span class="badge bg-primary">{{ $row['count'] }}</span></td>
                        <td>{{ $row['age_min'] ?? '?' }}–{{ $row['age_max'] ?? '?' }}</td>
                        <td>
                            @if($row['count'] < 2)
                                <span class="badge bg-warning text-dark">Low</span>
                            @elseif($row['count'] < 5)
                                <span class="badge bg-info text-dark">Fair</span>
                            @else
                                <span class="badge bg-success">Good</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">No data yet. Add activities to see coverage.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    const coverageData = {
        labels: {!! json_encode(array_column($coverage, 'skill')) !!},
        datasets: [{
            label: 'Activities',
            data: {!! json_encode(array_column($coverage, 'count')) !!},
            backgroundColor: 'rgba(13,110,253,0.55)',
            borderColor: 'rgba(13,110,253,1)',
            borderWidth: 1,
            borderRadius: 4,
        }]
    };
    new Chart(document.getElementById('coverageChart'), {
        type: 'bar',
        data: coverageData,
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });

    const completions = {!! $monthlyCompletions->toJson() !!};
    new Chart(document.getElementById('completionsChart'), {
        type: 'line',
        data: {
            labels: completions.map(r => r.month),
            datasets: [{
                label: 'Completions',
                data: completions.map(r => r.completions),
                fill: true,
                backgroundColor: 'rgba(25,135,84,0.2)',
                borderColor: 'rgba(25,135,84,1)',
                tension: 0.4,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });
})();
</script>
@endsection
