@extends('layouts.admin')

@section('title', __('admin.school_inquiries.fallback_title'))

@section('content')
<div class="container py-5">
    <div class="flex items-center mb-4">
        <a href="{{ route('admin.school-inquiries.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-gray-300 text-gray-700 hover:bg-gray-100 me-3">{{ __('admin.school_inquiries.back') }}</a>
        <h1 class="h3 font-bold mb-0">{{ $schoolInquiry->school_name ?? __('admin.school_inquiries.fallback_title') }}</h1>
        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $schoolInquiry->status === 'closed' ? 'bg-success' : 'bg-primary' }} ms-3">{{ ucfirst($schoolInquiry->status ?? 'open') }}</span>
    </div>

    @if(session('success'))
    <div class="flex items-start gap-3 p-4 rounded-lg border bg-emerald-50 border-emerald-200 text-emerald-800">{{ session('success') }}</div>
    @endif

    <div class="flex flex-wrap gap-4">
        <div class="lg:w-7/12">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0">
                <div class="px-5 py-3 border-b border-gray-200 font-semibold bg-white font-bold">{{ __('admin.school_inquiries.inquiry_details') }}</div>
                <div class="p-5">
                    <dl class="flex flex-wrap mb-0">
                        <dt class="sm:w-4/12">{{ __('admin.school_inquiries.school') }}</dt>
                        <dd class="sm:w-8/12">{{ $schoolInquiry->school_name ?? '–' }}</dd>
                        <dt class="sm:w-4/12">{{ __('admin.school_inquiries.contact_name') }}</dt>
                        <dd class="sm:w-8/12">{{ $schoolInquiry->contact_name ?? '–' }}</dd>
                        <dt class="sm:w-4/12">{{ __('admin.school_inquiries.contact_email') }}</dt>
                        <dd class="sm:w-8/12">{{ $schoolInquiry->contact_email ?? '–' }}</dd>
                        <dt class="sm:w-4/12">{{ __('admin.school_inquiries.country') }}</dt>
                        <dd class="sm:w-8/12">{{ $schoolInquiry->country ?? '–' }}</dd>
                        <dt class="sm:w-4/12">{{ __('admin.school_inquiries.students') }}</dt>
                        <dd class="sm:w-8/12">{{ $schoolInquiry->student_count ?? '–' }}</dd>
                        <dt class="sm:w-4/12">{{ __('admin.school_inquiries.message') }}</dt>
                        <dd class="sm:w-8/12">{{ $schoolInquiry->message ?? '–' }}</dd>
                        <dt class="sm:w-4/12">{{ __('admin.school_inquiries.assigned_to') }}</dt>
                        <dd class="sm:w-8/12">{{ $schoolInquiry->assignedAdmin->name ?? __('admin.school_inquiries.unassigned') }}</dd>
                        <dt class="sm:w-4/12">{{ __('admin.school_inquiries.received') }}</dt>
                        <dd class="sm:w-8/12">{{ $schoolInquiry->created_at->format('d M Y H:i') }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="lg:w-5/12">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0 mb-3">
                <div class="px-5 py-3 border-b border-gray-200 font-semibold bg-white font-bold">{{ __('admin.school_inquiries.assign') }}</div>
                <div class="p-5">
                    <form method="POST" action="{{ route('admin.school-inquiries.assign', $schoolInquiry) }}">
                        @csrf @method('PATCH')
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin.school_inquiries.assign_to_admin') }}</label>
                            <select name="admin_id" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500">
                                @foreach($admins ?? [] as $admin)
                                <option value="{{ $admin->id }}" @selected($schoolInquiry->assigned_to == $admin->id)>{{ $admin->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-violet-600 text-white hover:bg-violet-700 w-full">{{ __('admin.school_inquiries.assign') }}</button>
                    </form>
                </div>
            </div>

            @if($schoolInquiry->status !== 'closed')
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0">
                <div class="px-5 py-3 border-b border-gray-200 font-semibold bg-white font-bold">{{ __('admin.school_inquiries.close_inquiry') }}</div>
                <div class="p-5">
                    <form method="POST" action="{{ route('admin.school-inquiries.close', $schoolInquiry) }}">
                        @csrf @method('PATCH')
                        <div class="mb-3">
                            <textarea name="resolution_notes" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" rows="3" placeholder="{{ __('admin.school_inquiries.resolution_placeholder') }}"></textarea>
                        </div>
                        <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-emerald-600 text-white hover:bg-emerald-700 w-full">{{ __('admin.school_inquiries.mark_closed') }}</button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
