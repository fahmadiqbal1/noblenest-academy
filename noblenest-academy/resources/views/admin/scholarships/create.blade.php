@extends('layouts.admin')

@section('title', 'Grant Scholarship')

@section('content')
<div class="container py-5">
    <div class="flex flex-wrap justify-center">
        <div class="lg:w-6/12">
            <div class="flex items-center mb-4">
                <a href="{{ route('admin.scholarships.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-gray-300 text-gray-700 hover:bg-gray-100 me-3">← Back</a>
                <h1 class="h3 font-bold mb-0">Grant Scholarship</h1>
            </div>

            @if($errors->any())
            <div class="flex items-start gap-3 p-4 rounded-lg border bg-red-50 border-red-200 text-red-800">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
            @endif

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0">
                <div class="p-5 p-4">
                    <form method="POST" action="{{ route('admin.scholarships.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Recipient (User ID or Email)</label>
                            <input type="text" name="user_search" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" value="{{ old('user_search') }}" placeholder="Search by email..." required>
                        </div>

                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Scholarship Type</label>
                            <select name="type" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" required>
                                <option value="full" @selected(old('type')==='full')>Full (100% free)</option>
                                <option value="partial" @selected(old('type')==='partial')>Partial (custom %)</option>
                                <option value="trial" @selected(old('type')==='trial')>Extended Trial</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Discount %</label>
                            <input type="number" name="discount_percent" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" value="{{ old('discount_percent', 100) }}" min="1" max="100" required>
                        </div>

                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Duration (months)</label>
                            <input type="number" name="duration_months" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" value="{{ old('duration_months', 12) }}" min="1" max="120">
                            <div class="mt-1 text-sm text-[var(--color-text-muted)]">Leave blank for indefinite.</div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Notes</label>
                            <textarea name="notes" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" rows="3" placeholder="Reason for grant, source (NGO, competition, etc.)...">{{ old('notes') }}</textarea>
                        </div>

                        <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-violet-600 text-white hover:bg-violet-700 w-full font-bold">Grant Scholarship</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
