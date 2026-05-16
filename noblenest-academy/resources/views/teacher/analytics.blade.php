@extends('layouts.teacher')
@section('meta_title', 'Teacher Analytics – NobleNest')

@section('content')
<div class="w-full px-4 py-4">
    <h1 class="h4 font-bold mb-4">Your Analytics</h1>

    {{-- KPI Row --}}
    <div class="flex flex-wrap gap-3 mb-4">
        <div class="w-6/12 md:w-3/12">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0 h-full">
                <div class="p-5 text-center py-4">
                    <div class="text-3xl font-bold fw-black text-[var(--color-primary)]">{{ $courses->count() }}</div>
                    <div class="text-[var(--color-text-muted)] text-sm font-semibold mt-1">Courses</div>
                </div>
            </div>
        </div>
        <div class="w-6/12 md:w-3/12">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0 h-full">
                <div class="p-5 text-center py-4">
                    <div class="text-3xl font-bold fw-black text-emerald-600">{{ $totalStudents }}</div>
                    <div class="text-[var(--color-text-muted)] text-sm font-semibold mt-1">Active Students</div>
                </div>
            </div>
        </div>
        <div class="w-6/12 md:w-3/12">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0 h-full">
                <div class="p-5 text-center py-4">
                    <div class="text-3xl font-bold fw-black text-amber-600">{{ number_format($totalRevenue / 100, 2) }}</div>
                    <div class="text-[var(--color-text-muted)] text-sm font-semibold mt-1">Revenue (USD)</div>
                </div>
            </div>
        </div>
        <div class="w-6/12 md:w-3/12">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0 h-full">
                <div class="p-5 text-center py-4">
                    <div class="text-3xl font-bold fw-black text-red-600">
                        {{ $avgRating ? number_format($avgRating, 1) : '—' }}
                        <span style="font-size:1.2rem">⭐</span>
                    </div>
                    <div class="text-[var(--color-text-muted)] text-sm font-semibold mt-1">Avg Rating</div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-wrap gap-4">
        {{-- Courses Table --}}
        <div class="lg:w-7/12">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0">
                <div class="px-5 py-3 border-b border-gray-200 font-semibold bg-transparent border-0 pt-4 pb-0 px-4">
                    <h6 class="font-bold mb-0">Course Performance</h6>
                </div>
                <div class="p-5 px-0 pb-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm border-collapse mb-0 align-middle">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="ps-4">Course</th>
                                    <th class="text-center">Students</th>
                                    <th class="text-center">Sessions</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($courses as $course)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="font-semibold">{{ $course->title }}</div>
                                            <div class="text-[var(--color-text-muted)]" style="font-size:0.75rem">{{ Str::limit($course->description, 50) }}</div>
                                        </td>
                                        <td class="text-center">{{ $course->enrollments_count }}</td>
                                        <td class="text-center">{{ $course->sessions_count }}</td>
                                        <td class="text-center">
                                            @if($course->published)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-600">Live</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Draft</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-[var(--color-text-muted)] py-4">No courses yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Sessions + Ratings --}}
        <div class="lg:w-5/12">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0 mb-4">
                <div class="p-5 p-4">
                    <h6 class="font-bold mb-3">Recent Sessions</h6>
                    @forelse($recentSessions as $session)
                        <div class="flex justify-between items-center py-2 border-b">
                            <div>
                                <div class="font-semibold text-sm">{{ $session->title }}</div>
                                <div class="text-[var(--color-text-muted)]" style="font-size:0.72rem">{{ $session->course->title ?? '—' }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm">{{ $session->starts_at->format('M j') }}</div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium @if($session->status === 'live') bg-red-600 @elseif($session->status 'completed') bg-emerald-50 text-emerald-600 @else bg-gray-100 text-gray-500 @endif">
                                    {{ ucfirst($session->status) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-[var(--color-text-muted)] text-sm mb-0">No sessions yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0">
                <div class="p-5 p-4">
                    <h6 class="font-bold mb-3">Rating Breakdown</h6>
                    @foreach([5,4,3,2,1] as $star)
                        @php $count = $ratingSummary[$star] ?? 0; $total = $ratingSummary->sum(); @endphp
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-sm">{{ $star }}★</span>
                            <div class="h-2 w-full bg-gray-200 rounded-full overflow-hidden flex-grow-1" style="height:8px">
                                <div class="h-full bg-violet-600 transition-all bg-amber-600"
                                     style="width:{{ $total > 0 ? ($count/$total)*100 : 0 }}%"></div>
                            </div>
                            <span class="text-[var(--color-text-muted)]" style="font-size:0.72rem;width:20px">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
