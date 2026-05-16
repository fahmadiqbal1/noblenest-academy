@extends('layouts.admin')

@section('title', 'Teacher Vetting')

@section('content')
<div class="w-full px-4 py-4">
    <div class="flex items-center mb-4">
        <h1 class="h3 font-bold mb-0">Teacher Vetting</h1>
        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-600 text-gray-900 ms-3 text-base">{{ $pending->total() }} Pending</span>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0 mb-4">
        <div class="px-5 py-3 border-b border-gray-200 font-semibold bg-white font-bold">Pending Applications</div>
        <div class="p-5 p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse table-hover-tw mb-0">
                    <thead class="bg-gray-50">
                        <tr>
                            <th>Teacher</th>
                            <th>Email</th>
                            <th>Subjects</th>
                            <th>Country</th>
                            <th>Applied</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pending as $profile)
                        <tr>
                            <td class="font-semibold">{{ $profile->user->name ?? '–' }}</td>
                            <td>{{ $profile->user->email ?? '–' }}</td>
                            <td>{{ $profile->subjects ?? '–' }}</td>
                            <td>{{ $profile->country ?? '–' }}</td>
                            <td>{{ $profile->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('admin.teacher-vetting.show', $profile) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-violet-600 text-violet-600 hover:bg-violet-600 hover:text-white">Review</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-[var(--color-text-muted)] py-4">No pending applications. 🎉</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div>{{ $pending->links() }}</div>
</div>
@endsection
