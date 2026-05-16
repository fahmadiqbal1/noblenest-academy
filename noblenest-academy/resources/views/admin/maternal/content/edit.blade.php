@extends('layouts.admin')

@section('title', 'Admin: Edit — ' . $maternalContent->title)

@section('content')
<div class="container py-4">
    <h3 class="mb-4" style="font-family:'Baloo 2',sans-serif;">Edit: {{ $maternalContent->title }}</h3>

    @if(session('success'))
        <div class="flex items-start gap-3 p-4 rounded-lg border bg-emerald-50 border-emerald-200 text-emerald-800 border-0 mb-4">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0" style="background:rgba(255,255,255,0.88); border-radius:1.25rem;">
        <div class="p-5 p-4">
            @if($errors->any())
                <div class="flex items-start gap-3 p-4 rounded-lg border bg-red-50 border-red-200 text-red-800 border-0 mb-4">
                    <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.maternal.content.update', $maternalContent) }}">
                @csrf @method('PUT')
                @include('admin.maternal.content._form', ['content' => $maternalContent])
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed rounded-full" style="background:var(--nn-primary); color:#fff;">Save Changes</button>
                <a href="{{ route('admin.maternal.content.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-gray-300 text-gray-700 hover:bg-gray-100 rounded-full">Back</a>
            </form>
        </div>
    </div>
</div>
@endsection
