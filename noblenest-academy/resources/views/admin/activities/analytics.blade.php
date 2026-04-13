@extends('layouts.app')
@section('content')
@php
$subjectColors = ['sensory'=>'#f59e0b','motor'=>'#10b981','language'=>'#3b82f6','literacy'=>'#6366f1',
    'numeracy'=>'#ec4899','science'=>'#06b6d4','art'=>'#f97316','music'=>'#8b5cf6',
    'social'=>'#14b8a6','character'=>'#22c55e','etiquette'=>'#a855f7','quran'=>'#059669',
    'islamic'=>'#065f46','arabic'=>'#0891b2','coding'=>'#1d4ed8','robotics'=>'#7c3aed',
    'stem'=>'#0369a1','cultural'=>'#b45309'];
$ageTiers = [
    'Baby (0–2)'       => fn($r) => ($r['age_min'] ?? 99) <= 2 && ($r['age_max'] ?? 0) >= 0,
    'Preschool (3–6)'  => fn($r) => ($r['age_min'] ?? 99) <= 6 && ($r['age_max'] ?? 0) >= 3,
    'School (7–10)'    => fn($r) => ($r['age_min'] ?? 99) <= 10 && ($r['age_max'] ?? 0) >= 7,
];
$minActivities = 2;
$topLiked = $topLiked ?? [];
@endphp
<div class="container-fluid py-3" style="max-width:1000px">
    <h1 class="mb-2 text-primary"><i class="bi bi-bar-chart-line"></i> Curriculum Analytics</h1>
    <div class="d-flex flex-wrap gap-2 mb-4 align-items-center">
        <span class="badge bg-info fs-6">{{ $totalSkills }} Subjects</span>
        <span class="badge bg-success fs-6">{{ $totalActivities }} Activities</span>
        <a href="?export=csv" class="btn btn-outline-secondary btn-sm ms-2"><i class="bi bi-download"></i> Export CSV</a>
        <form method="POST" action="{{ route('admin.analytics.reportEmail') }}" class="d-inline ms-1">
            @csrf
            <button class="btn btn-outline-primary btn-sm"><i class="bi bi-envelope"></i> Email Report</button>
        </form>
    </div>

    {{-- Coverage alerts --}}
    @foreach($coverage as $row)
        @if($row['count'] < $minActivities)
        <div class="alert alert-warning d-flex align-items-center gap-2 py-2 mb-2">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span>
                <strong>Low Coverage:</strong>
                Subject <b>{{ ucfirst($row['subject']) }}</b> has only {{ $row['count'] }} {{ Str::plural('activity', $row['count']) }}.
                <span data-bs-toggle="tooltip" title="This subject has fewer than {{ $minActivities }} activities. Add more to fill the gap." style="cursor:help">
                    <i class="bi bi-info-circle text-muted"></i>
                </span>
            </span>
            <a href="{{ route('admin.activities.index') }}?openAdd=1&subject={{ urlencode($row['subject']) }}"
               class="btn btn-sm btn-outline-primary ms-auto">
               <i class="bi bi-plus-circle"></i> Add Activity
            </a>
            <a href="{{ route('admin.curriculum') }}?subject={{ urlencode($row['subject']) }}"
               class="btn btn-sm btn-outline-info">Assign</a>
        </div>
        @endif
    @endforeach

    {{-- Age-tier grouped coverage --}}
    @foreach(['Baby (0–2)' => [0,2], 'Preschool (3–6)' => [3,6], 'School (7–10)' => [7,10]] as $tierLabel => $range)
    @php
        $tierRows = collect($coverage)->filter(fn($r) => ($r['age_min'] ?? 99) <= $range[1] && ($r['age_max'] ?? 0) >= $range[0])->values();
    @endphp
    <div class="mb-4">
        <h5 class="text-muted fw-bold mb-2">📊 {{ $tierLabel }}</h5>
        @if($tierRows->isEmpty())
            <p class="text-muted small">No subjects in this age range yet.</p>
        @else
        <div class="row g-2 mb-2">
            @foreach($tierRows as $row)
            @php $color = $subjectColors[$row['subject'] ?? ''] ?? '#9ca3af'; @endphp
            <div class="col-6 col-md-4 col-lg-3">
                <div class="p-2 rounded-3 border" style="border-left: 4px solid {{ $color }} !important; background: {{ $color }}11">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold small">{{ ucfirst($row['subject']) }}</span>
                        <span class="badge" style="background:{{ $color }};color:#fff">{{ $row['count'] }}</span>
                    </div>
                    <div class="text-muted" style="font-size:0.72rem">Ages {{ $row['age_min'] }}–{{ $row['age_max'] }}</div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    @endforeach

    {{-- Top Liked --}}
    <div class="my-4">
        <h4 class="text-success mb-3"><i class="bi bi-heart-fill"></i> Most Liked Activities</h4>
        <table class="table table-striped table-bordered bg-white">
            <thead class="table-light">
                <tr>
                    <th>Activity</th><th>Subject</th><th>Likes</th><th>Age Range</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topLiked as $activity)
                <tr>
                    <td>{{ $activity->title }}</td>
                    <td>{{ $activity->subject }}</td>
                    <td><span class="badge bg-danger fs-6">{{ $activity->likes_count }}</span></td>
                    <td>{{ $activity->age_min }}–{{ $activity->age_max }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted">No likes data yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Full Coverage Table --}}
    <table class="table table-bordered table-hover bg-white">
        <thead class="table-light">
            <tr><th>Subject</th><th>Activities</th><th>Age Range</th></tr>
        </thead>
        <tbody>
            @foreach($coverage as $row)
            <tr>
                <td>{{ ucfirst($row['subject']) }}</td>
                <td><span class="badge bg-primary">{{ $row['count'] }}</span></td>
                <td>{{ $row['age_min'] }}–{{ $row['age_max'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-4">
        <canvas id="coverageChart" height="120"></canvas>
    </div>
</div>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Bootstrap tooltips
document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));

const ctx = document.getElementById('coverageChart').getContext('2d');
const subjectColors = @json($subjectColors);
const labels = @json(array_column($coverage, 'subject'));
const counts  = @json(array_column($coverage, 'count'));
const bgColors = labels.map(s => (subjectColors[s] || '#9ca3af') + '88');
const bdrColors = labels.map(s => subjectColors[s] || '#9ca3af');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels.map(s => s.charAt(0).toUpperCase() + s.slice(1)),
        datasets: [{ label: 'Activities per Subject', data: counts, backgroundColor: bgColors, borderColor: bdrColors, borderWidth: 2 }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});
</script>
@endsection
@endsection

</script>
@endsection
@endsection
