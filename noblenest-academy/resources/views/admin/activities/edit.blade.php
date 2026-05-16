@extends('layouts.admin')
@section('content')
<div class="container py-4" style="max-width:820px">
    <div class="flex items-center gap-3 mb-4">
        <a href="{{ route('admin.activities.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-gray-300 text-gray-700 hover:bg-gray-100 px-3 py-1.5 text-sm">
            <x-ui.icon name="arrow-left" />
        </a>
        <h1 class="mb-0 text-[var(--color-primary)]">✏️ Edit Activity</h1>
    </div>
    @if($errors->any())
        <div class="flex items-start gap-3 p-4 rounded-lg border bg-red-50 border-red-200 text-red-800">
            <ul class="mb-0">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('admin.activities.update', $activity) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.activities.partials.form', ['activity' => $activity])
        <div class="flex gap-2 mt-2">
            <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-violet-600 text-white hover:bg-violet-700"><x-ui.icon name="check-circle" /> Save Changes</button>
            <a href="{{ route('admin.activities.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-gray-300 text-gray-700 hover:bg-gray-100">Cancel</a>
        </div>
    </form>
</div>
@endsection

