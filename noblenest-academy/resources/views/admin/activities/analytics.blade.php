@extends('layouts.app')
@section('content')
<div class="container py-4">
    <h1 class="mb-4 text-primary">Curriculum Analytics</h1>
    <div class="mb-3">
        <span class="badge bg-info fs-5">Total Skills: {{ $totalSkills }}</span>
        <span class="badge bg-success fs-5">Total Activities: {{ $totalActivities }}</span>
        <a href="?export=csv" class="btn btn-outline-secondary btn-sm ms-3"><i class="bi bi-download"></i> Export CSV</a>
    </div>
    @php
        $minActivities = 2;
        $minAge = 0;
        $maxAge = 10;
        $topLiked = $topLiked ?? [];
    @endphp
    @foreach($coverage as $row)
        @if($row['count'] < $minActivities)
            <div class="alert alert-warning py-2 mb-2">
                <strong>Low coverage:</strong> Skill <b>{{ $row['skill'] }}</b> has only {{ $row['count'] }} activities.
                <a href="/admin/activities?skill={{ urlencode($row['skill']) }}" class="btn btn-sm btn-outline-primary ms-2">Add Activity</a>
                <a href="/admin/activities/curriculum?skill={{ urlencode($row['skill']) }}" class="btn btn-sm btn-outline-info ms-1">Assign Activity</a>
            </div>
        @endif
        @if($row['age_min'] > $minAge || $row['age_max'] < $maxAge)
            <div class="alert alert-warning py-2 mb-2">
                <strong>Age gap:</strong> Skill <b>{{ $row['skill'] }}</b> covers ages {{ $row['age_min'] }}–{{ $row['age_max'] }}.
            </div>
        @endif
    @endforeach
    <div class="my-4">
        <h4 class="text-success mb-3"><i class="bi bi-heart-fill"></i> Most Liked Activities</h4>
        <table class="table table-striped table-bordered bg-white">
            <thead class="table-light">
                <tr>
                    <th>Activity</th>
                    <th>Skill</th>
                    <th>Likes</th>
                    <th>Age Range</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topLiked as $activity)
                <tr>
                    <td>{{ $activity->title }}</td>
                    <td>{{ $activity->skill }}</td>
                    <td><span class="badge bg-danger fs-6">{{ $activity->likes_count }}</span></td>
                    <td>{{ $activity->age_min }}–{{ $activity->age_max }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted">No likes data yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="text-muted small">These activities are most liked by users. Consider expanding similar content for growth.</div>
    </div>
    <table class="table table-bordered table-hover bg-white">
        <thead class="table-light">
            <tr>
                <th>Skill</th>
                <th>Activities</th>
                <th>Age Range</th>
            </tr>
        </thead>
        <tbody>
            @foreach($coverage as $row)
            <tr>
                <td>{{ $row['skill'] }}</td>
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
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const ctx = document.getElementById('coverageChart').getContext('2d');
const data = {
    labels: {!! json_encode(array_column($coverage, 'skill')) !!},
    datasets: [{
        label: 'Activities per Skill',
        data: {!! json_encode(array_column($coverage, 'count')) !!},
        backgroundColor: 'rgba(54, 162, 235, 0.5)',
        borderColor: 'rgba(54, 162, 235, 1)',
        borderWidth: 1
    }]
};
new Chart(ctx, {
    type: 'bar',
    data: data,
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});
</script>
@endpush
@endsection
