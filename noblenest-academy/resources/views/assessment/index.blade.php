@extends('layouts.student')
@section('meta_title', $child->name . ''s Learning Assessment')

@section('content')
<div class="container py-5" style="max-width:860px">

    <div class="flex items-center gap-3 mb-4">
        <a href="{{ route('parent.child', $child) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-gray-300 text-gray-700 hover:bg-gray-100 px-3 py-1.5 text-sm rounded-full">
            <x-ui.icon name="arrow-left" /> Back
        </a>
        <div>
            <h1 class="h4 font-bold mb-0">{{ $child->name }}'s Learning Assessment</h1>
            <p class="text-[var(--color-text-muted)] text-sm mb-0">{{ $child->age_display }} · Personalised path based on activity history</p>
        </div>
    </div>

    {{-- Progress Overview --}}
    <div class="flex flex-wrap gap-3 mb-4">
        <div class="md:w-4/12">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0 h-full">
                <div class="p-5 text-center py-4">
                    <div style="font-size:2.5rem;font-weight:800;color:#7C3AED">{{ $summary['pct'] }}%</div>
                    <div class="text-[var(--color-text-muted)] text-sm font-semibold">{{ __('activities.curriculum_coverage') }}</div>
                    <div class="h-2 w-full bg-gray-200 rounded-full overflow-hidden mt-2" style="height:6px">
                        <div class="h-full bg-violet-600 transition-all" style="width:{{ $summary['pct'] }}%;background:#7C3AED"></div>
                    </div>
                    <div class="text-[var(--color-text-muted)]" style="font-size:0.72rem;margin-top:4px">
                        {{ $summary['completed'] }} / {{ $summary['total'] }} activities
                    </div>
                </div>
            </div>
        </div>
        <div class="md:w-4/12">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0 h-full">
                <div class="p-5 text-center py-4">
                    <div style="font-size:2.5rem;font-weight:800;color:#f59e0b">🔥 {{ $summary['streak'] }}</div>
                    <div class="text-[var(--color-text-muted)] text-sm font-semibold">{{ __('activities.day_streak') }}</div>
                </div>
            </div>
        </div>
        <div class="md:w-4/12">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0 h-full">
                <div class="p-5 text-center py-4">
                    <div style="font-size:2.5rem;font-weight:800;color:#7c3aed">{{ count($summary['gaps']) }}</div>
                    <div class="text-[var(--color-text-muted)] text-sm font-semibold">{{ __('activities.subject_gaps') }}</div>
                    @if(!empty($summary['gaps']))
                        <div class="mt-2">
                            @foreach($summary['gaps'] as $gap)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-600 me-1">{{ ucfirst($gap) }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-wrap gap-4">
        {{-- Subject Breakdown --}}
        <div class="md:w-5/12">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0 h-full">
                <div class="p-5 p-4">
                    <h6 class="font-bold mb-3">{{ __('activities.subject_breakdown') }}</h6>
                    @if(empty($summary['subject_breakdown']))
                        <p class="text-[var(--color-text-muted)] text-sm">{{ __('activities.no_completed') }}</p>
                    @else
                        @foreach($summary['subject_breakdown'] as $subject => $count)
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-semibold">{{ ucfirst($subject) }}</span>
                                <div class="flex items-center gap-2">
                                    <div class="h-2 w-full bg-gray-200 rounded-full overflow-hidden" style="width:120px;height:8px">
                                        <div class="h-full bg-violet-600 transition-all bg-sky-600"
                                             style="width:{{ min(100, $count * 10) }}%"></div>
                                    </div>
                                    <span class="text-[var(--color-text-muted)]" style="font-size:0.72rem;width:20px">{{ $count }}</span>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        {{-- Today's Recommended Path --}}
        <div class="md:w-7/12">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0">
                <div class="p-5 p-4">
                    <h6 class="font-bold mb-3">{{ __('activities.recommended_today') }}</h6>
                    @if($daily->isEmpty())
                        <p class="text-[var(--color-text-muted)] text-sm">{{ __('activities.all_done') }}</p>
                    @else
                        <div class="flex flex-wrap gap-2">
                            @foreach($daily as $activity)
                                <div class="w-6/12">
                                    <div class="flex items-center gap-2 bg-gray-50 rounded-lg p-2">
                                        <span style="font-size:1.4rem">{{ $activity->emoji ?? '📚' }}</span>
                                        <div>
                                            <div class="font-semibold" style="font-size:0.8rem;line-height:1.2">{{ $activity->title }}</div>
                                            <div class="text-[var(--color-text-muted)]" style="font-size:0.65rem">{{ ucfirst($activity->subject ?? '') }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Next Milestones --}}
    @if($next->isNotEmpty())
        <div class="mt-4">
            <h6 class="font-bold mb-3">{{ __('activities.upcoming_milestones') }}</h6>
            <div class="flex flex-wrap gap-3">
                @foreach($next as $milestone)
                    <div class="md:w-4/12">
                        <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0">
                            <div class="p-5 p-3">
                                <div class="font-semibold text-sm">{{ $milestone->name }}</div>
                                <div class="text-[var(--color-text-muted)]" style="font-size:0.72rem;margin-top:2px">
                                    {{ ucfirst($milestone->domain ?? '') }} · ~{{ $milestone->typical_age_months }}m
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection
