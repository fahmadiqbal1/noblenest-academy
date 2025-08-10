@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Edit Module</h1>
    <form action="{{ route('admin.modules.update', $module) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.modules._form', ['module' => $module, 'courses' => $courses, 'activities' => $activities, 'selected' => $selected])
        <button type="submit" class="btn btn-success">Update</button>
        <a href="{{ route('admin.modules.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection

