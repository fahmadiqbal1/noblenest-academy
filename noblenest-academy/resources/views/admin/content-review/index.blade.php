@extends('layouts.admin')
@section('meta_title', 'Content Review Queue – Admin')

@section('content')
<div class="w-full px-4 py-4">
    <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
        <h1 class="h4 font-bold mb-0">Content Review Queue</h1>
        <div class="flex gap-2">
            <form method="POST" action="{{ route('admin.content-review.approve-all') }}">
                @csrf
                <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-emerald-600 text-white hover:bg-emerald-700 px-3 py-1.5 text-sm rounded-full"
                        onclick="return confirm('Publish all pending activities?')">
                    <x-ui.icon name="check-check" class="me-1" /> Approve All
                </button>
            </form>
            <a href="{{ route('admin.content-batch.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-violet-600 text-white hover:bg-violet-700 px-3 py-1.5 text-sm rounded-full">
                <x-ui.icon name="plus" class="me-1" /> New Batch
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" class="flex flex-wrap gap-2 mb-4 items-end">
        <div class="w-auto">
            <select name="subject" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 px-2 py-1 text-sm rounded-full">
                <option value="">All Subjects</option>
                @foreach(['literacy','numeracy','creativity','stem','social','motor'] as $s)
                    <option value="{{ $s }}" @selected(request('subject') === $s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-auto">
            <select name="age_tier" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 px-2 py-1 text-sm rounded-full">
                <option value="">All Ages</option>
                @foreach(['baby','toddler','preschool','school'] as $t)
                    <option value="{{ $t }}" @selected(request('age_tier') === $t)>{{ ucfirst($t) }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-auto">
            <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-gray-300 text-gray-700 hover:bg-gray-100 px-3 py-1.5 text-sm rounded-full">Filter</button>
        </div>
    </form>

    @if($activities->isEmpty())
        <div class="flex items-start gap-3 p-4 rounded-lg border bg-sky-50 border-sky-200 text-sky-800">
            <x-ui.icon name="check-circle" class="me-2" /> No pending activities. Queue is clear!
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm border-collapse align-middle">
                <thead class="bg-gray-50">
                    <tr>
                        <th style="width:40px"></th>
                        <th>Title</th>
                        <th>Subject</th>
                        <th>Age Tier</th>
                        <th>Type</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activities as $activity)
                        <tr>
                            <td class="text-center text-xl">{{ $activity->emoji ?? '📚' }}</td>
                            <td class="font-semibold">{{ $activity->title }}</td>
                            <td>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                    {{ ucfirst($activity->subject ?? '—') }}
                                </span>
                            </td>
                            <td>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-sky-50 text-sky-600">
                                    {{ ucfirst($activity->age_tier ?? '—') }}
                                </span>
                            </td>
                            <td class="text-[var(--color-text-muted)] text-sm">{{ $activity->type ?? '—' }}</td>
                            <td class="text-[var(--color-text-muted)] text-sm">{{ $activity->created_at->diffForHumans() }}</td>
                            <td>
                                <div class="flex gap-1">
                                    <form method="POST" action="{{ route('admin.content-review.approve', $activity) }}">
                                        @csrf
                                        <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-emerald-600 text-white hover:bg-emerald-700 px-3 py-1.5 text-sm rounded-full px-2 py-0" title="Approve">
                                            <x-ui.icon name="check" />
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.content-review.reject', $activity) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-red-600 text-white hover:bg-red-700 px-3 py-1.5 text-sm rounded-full px-2 py-0"
                                                onclick="return confirm('Reject and delete this activity?')" title="Reject">
                                            <x-ui.icon name="x" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $activities->links() }}
    @endif
</div>
@endsection
