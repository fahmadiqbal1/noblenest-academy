@extends('layouts.admin')

@section('title', 'Scholarships')

@section('content')
<div class="w-full px-4 py-4">
    <div class="flex items-center mb-4">
        <h1 class="h3 font-bold mb-0">Scholarships</h1>
        <a href="{{ route('admin.scholarships.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-violet-600 text-white hover:bg-violet-700 ms-auto font-bold">+ New Scholarship</a>
    </div>

    @if(session('success'))
    <div class="flex items-start gap-3 p-4 rounded-lg border bg-emerald-50 border-emerald-200 text-emerald-800">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0">
        <div class="px-5 py-3 border-b border-gray-200 font-semibold bg-white font-bold">Active Scholarships</div>
        <div class="p-5 p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse table-hover-tw mb-0">
                    <thead class="bg-gray-50">
                        <tr>
                            <th>Recipient</th>
                            <th>Type</th>
                            <th>Discount</th>
                            <th>Valid Until</th>
                            <th>Granted</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($scholarships as $scholarship)
                        <tr>
                            <td>{{ $scholarship->user->name ?? '–' }}</td>
                            <td>{{ ucfirst($scholarship->type ?? 'full') }}</td>
                            <td>{{ $scholarship->discount_percent ?? 100 }}%</td>
                            <td>{{ $scholarship->expires_at ? $scholarship->expires_at->format('d M Y') : 'Never' }}</td>
                            <td>{{ $scholarship->created_at->format('d M Y') }}</td>
                            <td>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $scholarship->active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $scholarship->active ? 'Active' : 'Revoked' }}
                                </span>
                            </td>
                            <td>
                                @if($scholarship->active)
                                <form method="POST" action="{{ route('admin.scholarships.revoke', $scholarship) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-red-600 text-red-600 hover:bg-red-600 hover:text-white" onclick="return confirm('Revoke this scholarship?')">Revoke</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-[var(--color-text-muted)] py-4">No scholarships granted yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="mt-3">{{ $scholarships->links() }}</div>
</div>
@endsection
