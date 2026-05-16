@extends('layouts.admin')
@section('content')
<div class="container">
    <h1>Edit Module</h1>
    <form action="{{ route('admin.modules.update', $module) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.modules._form', ['module' => $module, 'courses' => $courses, 'activities' => $activities, 'selected' => $selected])
        <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-emerald-600 text-white hover:bg-emerald-700">Update</button>
        <a href="{{ route('admin.modules.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-gray-500 text-white hover:bg-gray-600">Cancel</a>
    </form>
</div>
@endsection

