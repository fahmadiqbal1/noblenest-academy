@extends('layouts.teacher')

@section('content')
<div class="container py-4">
    <div class="flex justify-between items-center mb-4">
        <h1 class="mb-0 font-bold"><x-ui.icon name="book" class="text-[var(--color-primary)]" /> My Courses</h1>
        <a href="{{ route('teacher.courses.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-violet-600 text-white hover:bg-violet-700">
            <x-ui.icon name="circle-plus" /> New Course
        </a>
    </div>

    @if(session('status'))
        <div class="flex items-start gap-3 p-4 rounded-lg border bg-emerald-50 border-emerald-200 text-emerald-800">{{ session('status') }}<button type="button" class=""></button></div>
    @endif

    @forelse($courses as $course)
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-3">
        <div class="p-5 flex justify-between items-center flex-wrap gap-2">
            <div>
                <h5 class="mb-1 font-semibold">{{ $course->title }}</h5>
                <div class="text-[var(--color-text-muted)] text-sm">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $course->status === 'published' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-700' }}">{{ $course->status }}</span>
                    @if($course->subject) <span class="ms-1">· {{ $course->subject }}</span> @endif
                    <span class="ms-2"><x-ui.icon name="users" /> {{ $course->enrollments_count }} enrolled</span>
                    <span class="ms-2"><x-ui.icon name="video" /> {{ $course->class_sessions_count }} sessions</span>
                    @if($course->price > 0)
                        <span class="ms-2 text-emerald-600 font-semibold">${{ $course->price }}</span>
                    @else
                        <span class="ms-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-sky-600 text-gray-900">Free</span>
                    @endif
                </div>
            </div>
            <div class="flex gap-2 flex-wrap">
                <form method="POST" action="{{ route('teacher.courses.publish', $course) }}">
                    @csrf
                    <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm {{ $course->status === 'published' ? 'bg-amber-500 text-gray-900 hover:bg-amber-600' : 'bg-emerald-600 text-white hover:bg-emerald-700' }}">
                        {{ $course->status === 'published' ? 'Unpublish' : 'Publish' }}
                    </button>
                </form>
                <a href="{{ route('teacher.courses.show', $course) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-violet-600 text-violet-600 hover:bg-violet-600 hover:text-white"><x-ui.icon name="eye" /> Manage</a>
                <a href="{{ route('teacher.courses.edit', $course) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-gray-300 text-gray-700 hover:bg-gray-100"><x-ui.icon name="pencil" /> Edit</a>
                <form method="POST" action="{{ route('teacher.courses.destroy', $course) }}" onsubmit="return confirm('Delete this course?')">
                    @csrf @method('DELETE')
                    <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-red-600 text-red-600 hover:bg-red-600 hover:text-white"><x-ui.icon name="trash" /></button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-5">
        <x-ui.icon name="book-open" class="text-5xl font-bold text-[var(--color-text-muted)] block mb-3" />
        <p class="text-[var(--color-text-muted)]">You haven't created any courses yet.</p>
        <a href="{{ route('teacher.courses.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-violet-600 text-white hover:bg-violet-700">Create Your First Course</a>
    </div>
    @endforelse

    @if($courses->hasPages())
        <div class="mt-3">{{ $courses->links() }}</div>
    @endif
</div>
@endsection
