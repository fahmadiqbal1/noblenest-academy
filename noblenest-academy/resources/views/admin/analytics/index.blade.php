@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    {{-- Header --}}
    <div class="flex flex-wrap justify-between items-center gap-4">
        <h1 class="text-2xl font-bold text-[var(--color-primary)] flex items-center gap-2">
            <x-ui.icon name="bar-chart" /> Curriculum Analytics
        </h1>
        <form method="POST" action="{{ route('admin.analytics.reportEmail') }}">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold border-2 border-violet-600 text-violet-600 hover:bg-violet-600 hover:text-white transition">
                <x-ui.icon name="mail" /> Email Monthly Report
            </button>
        </form>
    </div>

    @if(session('status'))
        <div class="flex items-start gap-3 p-4 rounded-lg border bg-emerald-50 border-emerald-200 text-emerald-800">{{ session('status') }}</div>
    @endif
    @if(session('error'))
        <div class="flex items-start gap-3 p-4 rounded-lg border bg-red-50 border-red-200 text-red-800">{{ session('error') }}</div>
    @endif

    {{-- Stat cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-xl p-5 text-center text-white bg-[var(--color-primary)] shadow-sm">
            <div class="text-4xl font-bold">{{ $totalSkills }}</div>
            <div class="text-sm mt-1 opacity-90">Total Skills / Domains</div>
        </div>
        <div class="rounded-xl p-5 text-center text-white bg-emerald-600 shadow-sm">
            <div class="text-4xl font-bold">{{ $totalActivities }}</div>
            <div class="text-sm mt-1 opacity-90">Total Activities</div>
        </div>
        <div class="rounded-xl p-5 text-center text-white bg-sky-600 shadow-sm">
            <div class="text-4xl font-bold">{{ $monthlyCompletions->sum('completions') }}</div>
            <div class="text-sm mt-1 opacity-90">Total Completions</div>
        </div>
        <div class="rounded-xl p-5 text-center text-white bg-amber-500 shadow-sm">
            <div class="text-4xl font-bold">{{ $coverage ? collect($coverage)->where('count', '<', 2)->count() : 0 }}</div>
            <div class="text-sm mt-1 opacity-90">Low Coverage Skills</div>
        </div>
    </div>

    {{-- Coverage Alerts --}}
    @foreach($coverage as $row)
        @if($row['count'] < 2)
            <div class="flex flex-wrap items-center gap-3 p-4 rounded-lg border bg-amber-50 border-amber-200 text-amber-800 text-sm">
                <strong>Low coverage:</strong> Skill <b>{{ $row['skill'] }}</b> has only {{ $row['count'] }} activities.
                <a href="{{ route('admin.activities.create') }}" class="ml-auto inline-flex items-center gap-1.5 px-3 py-1.5 rounded text-sm font-semibold border-2 border-violet-600 text-violet-600 hover:bg-violet-600 hover:text-white transition">Add Activity</a>
            </div>
        @endif
    @endforeach

    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-5 py-3 border-b border-gray-200 font-semibold flex items-center gap-2">
                <x-ui.icon name="trending-up" /> Activities per Skill
            </div>
            <div class="p-5">
                <canvas id="coverageChart" height="120"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-5 py-3 border-b border-gray-200 font-semibold flex items-center gap-2">
                <x-ui.icon name="clipboard-check" /> Monthly Completions
            </div>
            <div class="p-5">
                <canvas id="completionsChart" height="220"></canvas>
            </div>
        </div>
    </div>

    {{-- Coverage Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-200 font-semibold flex items-center gap-2">
            <x-ui.icon name="table" /> Skill Coverage Detail
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="px-5 py-3 font-semibold text-gray-600">Skill / Domain</th>
                        <th class="px-5 py-3 font-semibold text-gray-600">Activities</th>
                        <th class="px-5 py-3 font-semibold text-gray-600">Age Range</th>
                        <th class="px-5 py-3 font-semibold text-gray-600">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($coverage as $row)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-medium">{{ $row['skill'] }}</td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-[var(--color-primary)] text-white">{{ $row['count'] }}</span>
                        </td>
                        <td class="px-5 py-3 text-gray-500">{{ $row['age_min'] ?? '?' }}–{{ $row['age_max'] ?? '?' }}</td>
                        <td class="px-5 py-3">
                            @if($row['count'] < 2)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Low</span>
                            @elseif($row['count'] < 5)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-sky-100 text-sky-800">Fair</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Good</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-5 py-8 text-center text-[var(--color-text-muted)]">No data yet. Add activities to see coverage.</td></tr>
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
