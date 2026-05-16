@extends('layouts.admin')

@section('title', 'Review Teacher Application')

@section('content')
<div class="container py-5">
    <div class="flex items-center mb-4">
        <a href="{{ route('admin.teacher-vetting') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-gray-300 text-gray-700 hover:bg-gray-100 me-3">← Back</a>
        <h1 class="h3 font-bold mb-0">Review: {{ $teacherProfile->user->name ?? 'Teacher' }}</h1>
    </div>

    @if(session('success'))
    <div class="flex items-start gap-3 p-4 rounded-lg border bg-emerald-50 border-emerald-200 text-emerald-800">{{ session('success') }}</div>
    @endif

    <div class="flex flex-wrap gap-4">
        <div class="lg:w-7/12">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0 mb-3">
                <div class="px-5 py-3 border-b border-gray-200 font-semibold bg-white font-bold">Profile Details</div>
                <div class="p-5">
                    <dl class="flex flex-wrap mb-0">
                        <dt class="sm:w-4/12">Name</dt>
                        <dd class="sm:w-8/12">{{ $teacherProfile->user->name ?? '–' }}</dd>
                        <dt class="sm:w-4/12">Email</dt>
                        <dd class="sm:w-8/12">{{ $teacherProfile->user->email ?? '–' }}</dd>
                        <dt class="sm:w-4/12">Country</dt>
                        <dd class="sm:w-8/12">{{ $teacherProfile->country ?? '–' }}</dd>
                        <dt class="sm:w-4/12">Subjects</dt>
                        <dd class="sm:w-8/12">{{ $teacherProfile->subjects ?? '–' }}</dd>
                        <dt class="sm:w-4/12">Languages</dt>
                        <dd class="sm:w-8/12">{{ $teacherProfile->languages ?? '–' }}</dd>
                        <dt class="sm:w-4/12">Qualifications</dt>
                        <dd class="sm:w-8/12">{{ $teacherProfile->qualifications ?? '–' }}</dd>
                        <dt class="sm:w-4/12">Bio</dt>
                        <dd class="sm:w-8/12">{{ $teacherProfile->bio ?? '–' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="lg:w-5/12">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0">
                <div class="px-5 py-3 border-b border-gray-200 font-semibold bg-white font-bold">Decision</div>
                <div class="p-5">
                    <p class="text-[var(--color-text-muted)] text-sm">Current status: <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $teacherProfile->vetting_status === 'approved' ? 'bg-success' : ($teacherProfile->vetting_status 'rejected' 'bg-danger' 'bg-warning text-dark') }}">{{ ucfirst($teacherProfile->vetting_status ?? 'pending') }}</span></p>

                    <form method="POST" action="{{ route('admin.teacher-vetting.approve', $teacherProfile) }}" class="mb-2">
                        @csrf @method('PATCH')
                        <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-emerald-600 text-white hover:bg-emerald-700 w-full font-bold">✓ Approve</button>
                    </form>
                    <form method="POST" action="{{ route('admin.teacher-vetting.reject', $teacherProfile) }}">
                        @csrf @method('PATCH')
                        <div class="mb-2">
                            <textarea name="rejection_reason" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" rows="2" placeholder="Reason for rejection (optional)"></textarea>
                        </div>
                        <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-red-600 text-white hover:bg-red-700 w-full">✗ Reject</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
