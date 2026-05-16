@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-[var(--color-primary)] mb-0"><x-ui.icon name="bar-chart" /> Curriculum Analytics</h1>
        <form method="POST" action="{{ route('admin.analytics.reportEmail') }}">
            @csrf
            <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-violet-600 text-violet-600 hover:bg-violet-600 hover:text-white px-3 py-1.5 text-sm"><x-ui.icon name="mail" /> Email Monthly Report</button>
        </form>
    </div>

    @if(session('status'))
        <div class="flex items-start gap-3 p-4 rounded-lg border bg-emerald-50 border-emerald-200 text-emerald-800">{{ session('status') }}</div>
    @endif
    @if(session('error'))
        <div class="flex items-start gap-3 p-4 rounded-lg border bg-red-50 border-red-200 text-red-800">{{ session('error') }}</div>
    @endif

    <div class="flex flex-wrap gap-3 mb-4">
        <div class="md:w-3/12">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm text-white bg-[var(--color-primary)]">
                <div class="p-5 text-center">
                    <div class="text-4xl font-bold">{{ $totalSkills }}</div>
                    <div class="text-sm">Total Skills / Domains</div>
                </div>
            </div>
        </div>
        <div class="md:w-3/12">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm text-white bg-emerald-600">
                <div class="p-5 text-center">
                    <div class="text-4xl font-bold">{{ $totalActivities }}</div>
                    <div class="text-sm">Total Activities</div>
                </div>
            </div>
        </div>
        <div class="md:w-3/12">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm text-white bg-sky-600">
                <div class="p-5 text-center">
                    <div class="text-4xl font-bold">{{ $monthlyCompletions->sum('completions') }}</div>
                    <div class="text-sm">Total Completions</div>
                </div>
            </div>
        </div>
        <div class="md:w-3/12">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm text-white bg-amber-600">
                <div class="p-5 text-center">
                    <div class="text-4xl font-bold">{{ $coverage ? collect($coverage)->where('count', '<', 2)->count() : 0 }}</div>
                    <div class="text-sm">Low Coverage Skills</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Coverage Alerts --}}
    @foreach($coverage as $row)
        @if($row['count'] < 2)
            <div class="flex items-start gap-3 p-4 rounded-lg border bg-amber-50 border-amber-200 text-amber-800 py-2 mb-2">
                <strong>Low coverage:</strong> Skill <b>{{ $row['skill'] }}</b> has only {{ $row['count'] }} activities.
                <a href="{{ route('admin.activities.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-violet-600 text-violet-600 hover:bg-violet-600 hover:text-white ms-2">Add Activity</a>
            </div>
        @endif
    @endforeach

    <div class="flex flex-wrap gap-4 mt-2">
        {{-- Coverage Chart --}}
        <div class="lg:w-8/12">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-5 py-3 border-b border-gray-200 font-semibold font-bold"><x-ui.icon name="trending-up" /> Activities per Skill</div>
                <div class="p-5">
                    <canvas id="coverageChart" height="100"></canvas>
                </div>
            </div>
        </div>

        {{-- Monthly Completions --}}
        <div class="lg:w-4/12">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-5 py-3 border-b border-gray-200 font-semibold font-bold"><x-ui.icon name="clipboard-check" /> Monthly Completions</div>
                <div class="p-5">
                    <canvas id="completionsChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Coverage Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mt-4">
        <div class="px-5 py-3 border-b border-gray-200 font-semibold font-bold"><x-ui.icon name="table" /> Skill Coverage Detail</div>
        <div class="p-5 p-0">
            <table class="w-full text-sm border-collapse table-hover-tw mb-0">
                <thead class="bg-gray-50">
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
                        <td><span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-[var(--color-primary)]">{{ $row['count'] }}</span></td>
                        <td>{{ $row['age_min'] ?? '?' }}–{{ $row['age_max'] ?? '?' }}</td>
                        <td>
                            @if($row['count'] < 2)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-600 text-gray-900">Low</span>
                            @elseif($row['count'] < 5)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-sky-600 text-gray-900">Fair</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-600">Good</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-[var(--color-text-muted)] py-4">No data yet. Add activities to see coverage.</td></tr>
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
