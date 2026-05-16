@extends('layouts.admin')

@section('title', 'Admin: Maternal Content')

@section('content')
<div class="container py-4">
    <div class="flex justify-between items-center mb-4">
        <h3 style="font-family:'Baloo 2',sans-serif;">Maternal Content Management</h3>
        <a href="{{ route('admin.maternal.content.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed rounded-full" style="background:var(--nn-primary); color:#fff;">
            <x-ui.icon name="plus" class="me-1" /> Add Content
        </a>
    </div>

    {{-- Filters --}}
    <form method="GET" class="flex gap-2 mb-4">
        <select name="status" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 px-2 py-1 text-sm rounded-lg" style="width:auto;">
            <option value="">All Status</option>
            @foreach(['pending', 'approved', 'rejected'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <input type="text" name="q" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 px-2 py-1 text-sm rounded-lg" value="{{ request('q') }}" placeholder="Search..." style="width:200px;">
        <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-violet-600 text-violet-600 hover:bg-violet-600 hover:text-white rounded-full">Filter</button>
    </form>

    <div class="overflow-x-auto">
        <table class="w-full text-sm border-collapse align-middle">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Stage</th>
                    <th>Culture</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contents as $content)
                <tr>
                    <td class="font-semibold">{{ $content->title }}</td>
                    <td><span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" style="background:var(--nn-primary-soft); color:var(--nn-primary);">{{ ucfirst($content->content_type) }}</span></td>
                    <td>{{ ucfirst(str_replace('_', ' ', $content->stage)) }}</td>
                    <td>{{ ucfirst($content->cultural_origin ?? '—') }}</td>
                    <td>
                        @if($content->moderation_status === 'approved')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-600">Approved</span>
                        @elseif($content->moderation_status === 'rejected')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-600">Rejected</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-600 text-gray-900">Pending</span>
                        @endif
                    </td>
                    <td>
                        <div class="flex gap-1">
                            <a href="{{ route('admin.maternal.content.edit', $content) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-violet-600 text-violet-600 hover:bg-violet-600 hover:text-white rounded-full">Edit</a>
                            @if($content->moderation_status === 'pending')
                                <form method="POST" action="{{ route('admin.maternal.content.approve', $content) }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="medical_reviewer_name" value="{{ auth()->user()->name }}">
                                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm bg-emerald-600 text-white hover:bg-emerald-700 rounded-full">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('admin.maternal.content.reject', $content) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-red-600 text-red-600 hover:bg-red-600 hover:text-white rounded-full">Reject</button>
                                </form>
                            @endif
                            @if($content->steps_count ?? $content->steps()->count())
                                <form method="POST" action="{{ route('admin.maternal.content.generateAnimations', $content) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-sky-600 text-sky-600 hover:bg-sky-600 hover:text-white rounded-full" title="Generate step illustrations & narration">
                                        <x-ui.icon name="film" /> Animate
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-[var(--color-text-muted)] py-4">No content yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $contents->withQueryString()->links() }}
</div>
@endsection
