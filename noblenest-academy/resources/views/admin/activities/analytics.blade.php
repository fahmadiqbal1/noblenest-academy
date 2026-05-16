@extends('layouts.admin')
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
<div class="w-full px-4 py-3" style="max-width:1000px">
    <h1 class="mb-2 text-[var(--color-primary)]"><x-ui.icon name="bar-chart" /> Curriculum Analytics</h1>
    <div class="flex flex-wrap gap-2 mb-4 items-center">
        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-sky-600 text-base">{{ $totalSkills }} Subjects</span>
        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-600 text-base">{{ $totalActivities }} Activities</span>
        <a href="?export=csv" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-gray-300 text-gray-700 hover:bg-gray-100 px-3 py-1.5 text-sm ms-2"><x-ui.icon name="download" /> Export CSV</a>
        <form method="POST" action="{{ route('admin.analytics.reportEmail') }}" class="inline ms-1">
            @csrf
            <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-violet-600 text-violet-600 hover:bg-violet-600 hover:text-white px-3 py-1.5 text-sm"><x-ui.icon name="mail" /> Email Report</button>
        </form>
    </div>

    {{-- Coverage alerts --}}
    @foreach($coverage as $row)
        @if($row['count'] < $minActivities)
        <div class="flex items-start gap-3 p-4 rounded-lg border bg-amber-50 border-amber-200 text-amber-800 items-center gap-2 py-2 mb-2">
            <x-ui.icon name="alert-triangle" />
            <span>
                <strong>Low Coverage:</strong>
                Subject <b>{{ ucfirst($row['subject']) }}</b> has only {{ $row['count'] }} {{ Str::plural('activity', $row['count']) }}.
                <span title="This subject has fewer than {{ $minActivities }} activities. Add more to fill the gap." style="cursor:help">
                    <x-ui.icon name="info" class="text-[var(--color-text-muted)]" />
                </span>
            </span>
            <a href="{{ route('admin.activities.index') }}?openAdd=1&subject={{ urlencode($row['subject']) }}"
               class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-violet-600 text-violet-600 hover:bg-violet-600 hover:text-white ms-auto">
               <x-ui.icon name="circle-plus" /> Add Activity
            </a>
            <a href="{{ route('admin.curriculum') }}?subject={{ urlencode($row['subject']) }}"
               class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-sky-600 text-sky-600 hover:bg-sky-600 hover:text-white">Assign</a>
        </div>
        @endif
    @endforeach

    {{-- Age-tier grouped coverage --}}
    @foreach(['Baby (0–2)' => [0,2], 'Preschool (3–6)' => [3,6], 'School (7–10)' => [7,10]] as $tierLabel => $range)
    @php
        $tierRows = collect($coverage)->filter(fn($r) => ($r['age_min'] ?? 99) <= $range[1] && ($r['age_max'] ?? 0) >= $range[0])->values();
    @endphp
    <div class="mb-4">
        <h5 class="text-[var(--color-text-muted)] font-bold mb-2">📊 {{ $tierLabel }}</h5>
        @if($tierRows->isEmpty())
            <p class="text-[var(--color-text-muted)] text-sm">No subjects in this age range yet.</p>
        @else
        <div class="flex flex-wrap gap-2 mb-2">
            @foreach($tierRows as $row)
            @php $color = $subjectColors[$row['subject'] ?? ''] ?? '#9ca3af'; @endphp
            <div class="w-6/12 md:w-4/12 lg:w-3/12">
                <div class="p-2 rounded-lg border" style="border-left: 4px solid {{ $color }} !important; background: {{ $color }}11">
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-sm">{{ ucfirst($row['subject']) }}</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" style="background:{{ $color }};color:#fff">{{ $row['count'] }}</span>
                    </div>
                    <div class="text-[var(--color-text-muted)]" style="font-size:0.72rem">Ages {{ $row['age_min'] }}–{{ $row['age_max'] }}</div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    @endforeach

    {{-- Top Liked --}}
    <div class="my-4">
        <h4 class="text-emerald-600 mb-3"><x-ui.icon name="heart" /> Most Liked Activities</h4>
        <table class="w-full text-sm border-collapse table-striped-tw border border-gray-200 bg-white">
            <thead class="bg-gray-50">
                <tr>
                    <th>Activity</th><th>Subject</th><th>Likes</th><th>Age Range</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topLiked as $activity)
                <tr>
                    <td>{{ $activity->title }}</td>
                    <td>{{ $activity->subject }}</td>
                    <td><span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-600 text-base">{{ $activity->likes_count }}</span></td>
                    <td>{{ $activity->age_min }}–{{ $activity->age_max }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-[var(--color-text-muted)]">No likes data yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Full Coverage Table --}}
    <table class="w-full text-sm border-collapse border border-gray-200 table-hover-tw bg-white">
        <thead class="bg-gray-50">
            <tr><th>Subject</th><th>Activities</th><th>Age Range</th></tr>
        </thead>
        <tbody>
            @foreach($coverage as $row)
            <tr>
                <td>{{ ucfirst($row['subject']) }}</td>
                <td><span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-[var(--color-primary)]">{{ $row['count'] }}</span></td>
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
