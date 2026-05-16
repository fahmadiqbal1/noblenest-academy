@extends('layouts.admin')

@section('content')
<div class="flex justify-between items-center mb-4">
    <h2>Batch Preview — {{ ucfirst($job->payload['subject'] ?? 'N/A') }} ({{ ucfirst($job->payload['age_tier'] ?? '') }})</h2>
    <a href="{{ route('admin.orchestrator.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-gray-300 text-gray-700 hover:bg-gray-100"><x-ui.icon name="arrow-left" /> Back</a>
</div>

<div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-3">
    <div class="p-5">
        <div class="flex flex-wrap text-center">
            <div class="md:w-3/12"><strong>Status:</strong> <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $job->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : ($job->status === 'failed' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">{{ ucfirst($job->status) }}</span></div>
            <div class="md:w-3/12"><strong>Requested:</strong> {{ $job->payload['count'] ?? '?' }} activities</div>
            <div class="md:w-3/12"><strong>Generated:</strong> {{ $activities->count() }}</div>
            <div class="md:w-3/12"><strong>Language:</strong> {{ strtoupper($job->payload['language'] ?? 'en') }}</div>
        </div>
    </div>
</div>

@if($activities->isEmpty())
    <div class="flex items-start gap-3 p-4 rounded-lg border bg-sky-50 border-sky-200 text-sky-800">No activities generated yet. The batch may still be processing.</div>
@else
    <form method="POST" action="{{ route('admin.content-batch.publish', $job) }}">
        @csrf
        <div class="flex justify-between items-center mb-3">
            <div class="flex items-center gap-2">
                <input class="w-4 h-4 rounded border-gray-300 text-violet-600 focus:ring-violet-500" type="checkbox" id="selectAll" onchange="document.querySelectorAll('.activity-check').forEach(c => c.checked = this.checked)">
                <label class="text-sm font-bold" for="selectAll">Select All</label>
            </div>
            <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-emerald-600 text-white hover:bg-emerald-700"><x-ui.icon name="check-check" /> Publish Selected</button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm border-collapse table-hover-tw align-middle">
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
                        <td><input type="checkbox" name="ids[]" value="{{ $activity->id }}" class="w-4 h-4 rounded border-gray-300 text-violet-600 focus:ring-violet-500 activity-check"></td>
                        <td>
                            <strong>{{ $activity->emoji }} {{ $activity->title }}</strong>
                            <br><small class="text-[var(--color-text-muted)]">{{ Str::limit($activity->description, 80) }}</small>
                        </td>
                        <td><span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-500">{{ $activity->activity_type }}</span></td>
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
