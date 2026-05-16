@extends('layouts.admin')

@section('title', 'School Inquiries')

@section('content')
<div class="w-full px-4 py-4">
    <h1 class="h3 font-bold mb-4">School Inquiries</h1>

    @if(session('success'))
    <div class="flex items-start gap-3 p-4 rounded-lg border bg-emerald-50 border-emerald-200 text-emerald-800">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0">
        <div class="px-5 py-3 border-b border-gray-200 font-semibold bg-white font-bold">All Inquiries</div>
        <div class="p-5 p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse table-hover-tw mb-0">
                    <thead class="bg-gray-50">
                        <tr>
                            <th>School</th>
                            <th>Contact</th>
                            <th>Country</th>
                            <th>Students</th>
                            <th>Status</th>
                            <th>Received</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inquiries as $inquiry)
                        <tr>
                            <td class="font-semibold">{{ $inquiry->school_name ?? '–' }}</td>
                            <td>{{ $inquiry->contact_email ?? '–' }}</td>
                            <td>{{ $inquiry->country ?? '–' }}</td>
                            <td>{{ $inquiry->student_count ?? '–' }}</td>
                            <td>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $inquiry->status === 'closed' ? 'bg-success' : ($inquiry->status 'open' 'bg-primary' 'bg-warning text-dark') }}">
                                    {{ ucfirst($inquiry->status ?? 'open') }}
                                </span>
                            </td>
                            <td>{{ $inquiry->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('admin.school-inquiries.show', $inquiry) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-violet-600 text-violet-600 hover:bg-violet-600 hover:text-white">View</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-[var(--color-text-muted)] py-4">No school inquiries yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="mt-3">{{ $inquiries->links() }}</div>
</div>
@endsection
