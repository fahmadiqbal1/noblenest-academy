@extends('layouts.teacher')

@section('content')
<div class="container py-4">
    <div class="flex justify-between items-center mb-3 flex-wrap gap-2">
        <div>
            <a href="{{ route('teacher.courses.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-gray-300 text-gray-700 hover:bg-gray-100 mb-2">
                <x-ui.icon name="arrow-left" /> My Courses
            </a>
            <h1 class="font-bold mb-0">{{ $course->title }}</h1>
            <p class="text-[var(--color-text-muted)] mb-0">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $course->status === 'published' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-700' }}">{{ $course->status }}</span>
                @if($course->subject) <span class="ms-2">{{ $course->subject }}</span> @endif
                @if($course->price > 0) <span class="ms-2 text-emerald-600 font-semibold">${{ $course->price }} {{ $course->currency }}</span>
                @else <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-sky-600 text-gray-900 ms-2">Free</span> @endif
            </p>
        </div>
        <div class="flex gap-2 flex-wrap">
            <form method="POST" action="{{ route('teacher.courses.publish', $course) }}">
                @csrf
                <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed {{ $course->status === 'published' ? 'bg-amber-500 text-gray-900 hover:bg-amber-600' : 'bg-emerald-600 text-white hover:bg-emerald-700' }}">
                    <x-ui.icon name="{{ $course->status === 'published' ? 'eye-off' : 'globe' }}" />
                    {{ $course->status === 'published' ? 'Unpublish' : 'Publish' }}
                </button>
            </form>
            <a href="{{ route('teacher.courses.edit', $course) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-gray-300 text-gray-700 hover:bg-gray-100">
                <x-ui.icon name="pencil" /> Edit
            </a>
            @if($course->isPublished())
                <a href="{{ route('marketplace.show', $course->slug) }}" target="_blank" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-sky-600 text-sky-600 hover:bg-sky-600 hover:text-white">
                    <x-ui.icon name="external-link" /> Public View
                </a>
            @endif
        </div>
    </div>

    @if(session('status'))
        <div class="flex items-start gap-3 p-4 rounded-lg border bg-emerald-50 border-emerald-200 text-emerald-800">{{ session('status') }}<button type="button" class=""></button></div>
    @endif
    @if(session('error'))
        <div class="flex items-start gap-3 p-4 rounded-lg border bg-red-50 border-red-200 text-red-800">{{ session('error') }}<button type="button" class=""></button></div>
    @endif

    <div class="flex flex-wrap gap-4">
        {{-- Left: Sections + Students --}}
        <div class="lg:w-8/12">
            {{-- Curriculum Sections --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-4">
                <div class="px-5 py-3 border-b border-gray-200 font-semibold font-bold flex justify-between items-center">
                    <span><x-ui.icon name="list-ordered" /> Curriculum</span>
                    <a href="{{ route('teacher.courses.edit', $course) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-violet-600 text-violet-600 hover:bg-violet-600 hover:text-white">Edit Sections</a>
                </div>
                <div class="p-5 p-0">
                    @forelse($course->sections as $sec)
                    <div class="px-3 py-3 border-b flex justify-between items-center">
                        <div>
                            <div class="font-semibold">{{ $loop->iteration }}. {{ $sec->title }}</div>
                            @if($sec->description)<div class="text-[var(--color-text-muted)] text-sm">{{ $sec->description }}</div>@endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-[var(--color-text-muted)] py-3 text-sm">No sections yet. <a href="{{ route('teacher.courses.edit', $course) }}">Add sections</a> to outline your curriculum.</div>
                    @endforelse
                </div>
            </div>

            {{-- Enrolled Students --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-4">
                <div class="px-5 py-3 border-b border-gray-200 font-semibold font-bold"><x-ui.icon name="users" /> Enrolled Students ({{ $course->enrollments->count() }})</div>
                <div class="p-5 p-0">
                    @forelse($course->enrollments->take(10) as $enrollment)
                    <div class="flex items-center gap-3 px-3 py-2 border-b">
                        <img src="https://api.dicebear.com/7.x/bottts/svg?seed={{ $enrollment->student_id }}" style="width:36px;height:36px;border-radius:50%">
                        <div class="flex-grow-1">
                            <div class="font-semibold">{{ $enrollment->student->name ?? 'Student #'.$enrollment->student_id }}</div>
                            <div class="text-[var(--color-text-muted)] text-sm">Enrolled {{ $enrollment->enrolled_at?->diffForHumans() ?? 'recently' }}</div>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $enrollment->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-800' }}">
                            {{ $enrollment->payment_status }}
                        </span>
                    </div>
                    @empty
                    <div class="text-center text-[var(--color-text-muted)] py-3 text-sm">No students enrolled yet.</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Right: Sessions + Invite Links --}}
        <div class="lg:w-4/12">
            {{-- Schedule New Session --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-4">
                <div class="px-5 py-3 border-b border-gray-200 font-semibold font-bold"><x-ui.icon name="video" /> Schedule a Session</div>
                <div class="p-5">
                    <form method="POST" action="{{ route('teacher.sessions.store', $course) }}">
                        @csrf
                        <div class="mb-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Session Title <span class="text-red-600">*</span></label>
                            <input type="text" name="title" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 px-2 py-1 text-sm" required placeholder="e.g. Week 1 Introduction">
                        </div>
                        <div class="mb-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date &amp; Time <span class="text-red-600">*</span></label>
                            <input type="datetime-local" name="starts_at" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 px-2 py-1 text-sm" required>
                        </div>
                        <div class="mb-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Duration (minutes)</label>
                            <input type="number" name="duration_minutes" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 px-2 py-1 text-sm" value="60" min="15" max="480">
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">External Meeting URL (optional)</label>
                            <input type="url" name="meeting_url" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 px-2 py-1 text-sm" placeholder="https://meet.google.com/...">
                            <div class="mt-1 text-sm text-[var(--color-text-muted)]">Leave blank to use the built-in classroom.</div>
                        </div>
                        <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-violet-600 text-white hover:bg-violet-700 w-full"><x-ui.icon name="calendar" /> Schedule</button>
                    </form>
                </div>
            </div>

            {{-- Upcoming Sessions --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-4">
                <div class="px-5 py-3 border-b border-gray-200 font-semibold font-bold"><x-ui.icon name="calendar" /> Sessions</div>
                <div class="p-5 p-0" style="max-height:300px;overflow-y:auto;">
                    @forelse($course->classSessions as $session)
                    <div class="px-3 py-2 border-b">
                        <div class="flex justify-between items-start gap-1">
                            <div>
                                <div class="font-semibold text-sm">{{ $session->title }}</div>
                                <div class="text-[var(--color-text-muted)]" style="font-size:0.75rem">{{ $session->starts_at->format('D M j, g:i A') }} · {{ $session->duration_minutes }}min</div>
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $session->status === 'live' ? 'bg-red-100 text-red-700' : ($session->status === 'scheduled' ? 'bg-violet-100 text-violet-700' : 'bg-gray-100 text-gray-700') }}">
                                {{ $session->status }}
                            </span>
                        </div>
                        <div class="flex gap-1 mt-1">
                            @if($session->status === 'scheduled')
                                <form method="POST" action="{{ route('teacher.sessions.start', $session) }}">@csrf<button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-2 py-1 text-xs bg-emerald-600 text-white hover:bg-emerald-700 px-3 py-1.5 text-sm py-0"><x-ui.icon name="play" /> Start</button></form>
                                <form method="POST" action="{{ route('teacher.sessions.cancel', $session) }}">@csrf<button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-2 py-1 text-xs border-2 border-red-600 text-red-600 hover:bg-red-600 hover:text-white px-3 py-1.5 text-sm py-0"><x-ui.icon name="x" /></button></form>
                            @elseif($session->status === 'live')
                                <a href="{{ route('classroom.room', $session->room_id) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm bg-red-600 text-white hover:bg-red-700 py-0 px-2"><x-ui.icon name="video" /> Enter</a>
                                <form method="POST" action="{{ route('teacher.sessions.end', $session) }}">@csrf<button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-gray-300 text-gray-700 hover:bg-gray-100 py-0 px-2">End</button></form>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-[var(--color-text-muted)] py-3 text-sm">No sessions scheduled yet.</div>
                    @endforelse
                </div>
            </div>

            {{-- Invite Links --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-5 py-3 border-b border-gray-200 font-semibold font-bold"><x-ui.icon name="link-2" /> Invite Links</div>
                <div class="p-5">
                    <form method="POST" action="{{ route('teacher.invite-links.store', $course) }}" class="mb-3">
                        @csrf
                        <div class="mb-2">
                            <input type="text" name="label" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 px-2 py-1 text-sm" placeholder="Label (optional)">
                        </div>
                        <div class="flex flex-wrap gap-2 mb-2">
                            <div class="flex-1">
                                <input type="number" name="max_uses" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 px-2 py-1 text-sm" placeholder="Max uses (optional)" min="1">
                            </div>
                            <div class="flex-1">
                                <input type="datetime-local" name="expires_at" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 px-2 py-1 text-sm" placeholder="Expires">
                            </div>
                        </div>
                        <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-violet-600 text-violet-600 hover:bg-violet-600 hover:text-white w-full px-3 py-1.5 text-sm"><x-ui.icon name="plus" /> Generate Link</button>
                    </form>

                    @forelse($course->inviteLinks as $link)
                    <div class="border rounded p-2 mb-2 bg-gray-50 text-sm">
                        <div class="flex justify-between items-start">
                            <div>
                                @if($link->label)<div class="font-semibold">{{ $link->label }}</div>@endif
                                <div class="break-words" style="font-size:0.75rem">
                                    <code>{{ route('invite.join', $link->token) }}</code>
                                </div>
                                <div class="text-[var(--color-text-muted)] mt-1" style="font-size:0.72rem">
                                    Uses: {{ $link->uses }}{{ $link->max_uses ? '/'.$link->max_uses : '' }}
                                    @if($link->expires_at) · Expires {{ $link->expires_at->format('M j') }}@endif
                                    @if($link->isExpired()) <span class="text-red-600">Expired</span>
                                    @elseif($link->isExhausted()) <span class="text-amber-600">Exhausted</span>
                                    @else <span class="text-emerald-600">Active</span> @endif
                                </div>
                            </div>
                            <div class="flex gap-1">
                                <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-2 py-1 text-xs border-2 border-gray-300 text-gray-700 hover:bg-gray-100 px-3 py-1.5 text-sm py-0 px-1"
                                    onclick="navigator.clipboard.writeText('{{ route('invite.join', $link->token) }}').then(()=>alert('Copied!'))"
                                    title="Copy"><x-ui.icon name="clipboard" /></button>
                                <form method="POST" action="{{ route('teacher.invite-links.destroy', $link) }}" onsubmit="return confirm('Delete?')">
                                    @csrf @method('DELETE')
                                    <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-2 py-1 text-xs border-2 border-red-600 text-red-600 hover:bg-red-600 hover:text-white px-3 py-1.5 text-sm py-0 px-1"><x-ui.icon name="trash" /></button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-[var(--color-text-muted)] text-sm text-center">No invite links yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
