@extends('layouts.teacher')

@section('title', 'My Profile')

@section('content')
<div class="container py-5">
    <div class="flex flex-wrap justify-center">
        <div class="lg:w-8/12">
            <div class="flex items-center gap-3 mb-4">
                <div class="rounded-full bg-[var(--color-primary)] flex items-center justify-center text-white font-bold" style="width:64px;height:64px;font-size:1.5rem;">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div>
                    <h1 class="h3 font-bold mb-0">{{ auth()->user()->name }}</h1>
                    <p class="text-[var(--color-text-muted)] mb-0">{{ auth()->user()->email }}</p>
                </div>
                <a href="{{ route('teacher.profile.edit') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-violet-600 text-violet-600 hover:bg-violet-600 hover:text-white ms-auto">Edit Profile</a>
            </div>

            @if(session('success'))
            <div class="flex items-start gap-3 p-4 rounded-lg border bg-emerald-50 border-emerald-200 text-emerald-800">{{ session('success') }}</div>
            @endif

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0 mb-4">
                <div class="px-5 py-3 border-b border-gray-200 font-semibold bg-white font-bold">Vetting Status</div>
                <div class="p-5">
                    @php $profile = $teacherProfile ?? null; @endphp
                    @if($profile)
                    <div class="flex items-center gap-3">
                        @if($profile->vetting_status === 'approved')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-600 text-base">✓ Approved</span>
                        <span class="text-[var(--color-text-muted)] text-sm">You can accept students and create paid courses.</span>
                        @elseif($profile->vetting_status === 'pending')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-600 text-gray-900 text-base">⏳ Under Review</span>
                        <span class="text-[var(--color-text-muted)] text-sm">We'll notify you when your application is reviewed.</span>
                        @elseif($profile->vetting_status === 'rejected')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-600 text-base">✗ Rejected</span>
                        <form method="POST" action="{{ route('teacher.profile.update') }}" class="mt-2">
                            @csrf @method('PUT')
                            <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-amber-500 text-amber-600 hover:bg-amber-500 hover:text-gray-900">Re-submit Application</button>
                        </form>
                        @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-500 text-base">Not Submitted</span>
                        <form method="POST" action="{{ route('teacher.profile.update') }}" class="mt-2">
                            @csrf @method('PUT')
                            <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm bg-violet-600 text-white hover:bg-violet-700">Submit for Vetting</button>
                        </form>
                        @endif
                    </div>
                    @else
                    <p class="text-[var(--color-text-muted)]">No teacher profile found. <a href="{{ route('teacher.profile.edit') }}">Set up your profile</a> to get started.</p>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0">
                <div class="px-5 py-3 border-b border-gray-200 font-semibold bg-white font-bold">My Courses</div>
                <div class="p-5 p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm border-collapse table-hover-tw mb-0">
                            <thead class="bg-gray-50">
                                <tr><th>Course</th><th>Students</th><th>Status</th><th></th></tr>
                            </thead>
                            <tbody>
                                @forelse($courses ?? [] as $course)
                                <tr>
                                    <td>{{ $course->title }}</td>
                                    <td>{{ $course->enrollments_count ?? 0 }}</td>
                                    <td><span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $course->published ? 'bg-success' : 'bg-secondary' }}">{{ $course->published ? 'Published' : 'Draft' }}</span></td>
                                    <td><a href="{{ route('teacher.courses.show', $course) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-violet-600 text-violet-600 hover:bg-violet-600 hover:text-white">Manage</a></td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center text-[var(--color-text-muted)] py-3">No courses yet. <a href="{{ route('teacher.courses.create') }}">Create one →</a></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
