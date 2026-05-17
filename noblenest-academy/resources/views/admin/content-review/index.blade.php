@extends('layouts.admin')
@section('meta_title', __('admin.content_review.meta_title'))

@section('content')
<div class="w-full px-4 py-4">
    <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
        <h1 class="h4 font-bold mb-0">{{ __('admin.content_review.title') }}</h1>
        <div class="flex gap-2">
            <form method="POST" action="{{ route('admin.content-review.approve-all') }}">
                @csrf
                <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-emerald-600 text-white hover:bg-emerald-700 px-3 py-1.5 text-sm rounded-full"
                        onclick="return confirm('{{ __('admin.content_review.approve_all_confirm') }}')">
                    <x-ui.icon name="check-check" class="me-1" /> {{ __('admin.content_review.approve_all') }}
                </button>
            </form>
            <a href="{{ route('admin.content-batch.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-violet-600 text-white hover:bg-violet-700 px-3 py-1.5 text-sm rounded-full">
                <x-ui.icon name="plus" class="me-1" /> {{ __('admin.content_review.new_batch') }}
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" class="flex flex-wrap gap-2 mb-4 items-end">
        <div class="w-auto">
            <select name="subject" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 px-2 py-1 text-sm rounded-full">
                <option value="">{{ __('admin.content_review.all_subjects') }}</option>
                @foreach(['literacy','numeracy','creativity','stem','social','motor'] as $s)
                    <option value="{{ $s }}" @selected(request('subject') === $s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-auto">
            <select name="age_tier" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 px-2 py-1 text-sm rounded-full">
                <option value="">{{ __('admin.content_review.all_ages') }}</option>
                @foreach(['baby','toddler','preschool','school'] as $t)
                    <option value="{{ $t }}" @selected(request('age_tier') === $t)>{{ ucfirst($t) }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-auto">
            <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-gray-300 text-gray-700 hover:bg-gray-100 px-3 py-1.5 text-sm rounded-full">{{ __('admin.content_review.filter') }}</button>
        </div>
    </form>

    @if($activities->isEmpty())
        <div class="flex items-start gap-3 p-4 rounded-lg border bg-sky-50 border-sky-200 text-sky-800">
            <x-ui.icon name="check-circle" class="me-2" /> {{ __('admin.content_review.empty') }}
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm border-collapse align-middle">
                <thead class="bg-gray-50">
                    <tr>
                        <th style="width:40px"></th>
                        <th>{{ __('admin.content_review.col_title') }}</th>
                        <th>{{ __('admin.content_review.col_subject') }}</th>
                        <th>{{ __('admin.content_review.col_age_tier') }}</th>
                        <th>{{ __('admin.content_review.col_type') }}</th>
                        <th>{{ __('admin.content_review.col_created') }}</th>
                        <th>{{ __('admin.content_review.col_actions') }}</th>
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
                                        <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-emerald-600 text-white hover:bg-emerald-700 px-3 py-1.5 text-sm rounded-full px-2 py-0" title="{{ __('admin.content_review.approve') }}">
                                            <x-ui.icon name="check" />
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.content-review.reject', $activity) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-red-600 text-white hover:bg-red-700 px-3 py-1.5 text-sm rounded-full px-2 py-0"
                                                onclick="return confirm('{{ __('admin.content_review.reject_confirm') }}')" title="{{ __('admin.content_review.reject') }}">
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
