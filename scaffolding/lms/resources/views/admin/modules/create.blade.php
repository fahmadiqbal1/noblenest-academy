@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Add Module</h1>
    <form action="{{ route('admin.modules.store') }}" method="POST">
        @csrf
        @include('admin.modules._form', ['module' => null, 'courses' => $courses, 'activities' => $activities, 'selected' => []])
        <button type="submit" class="btn btn-success">Create</button>
        <a href="{{ route('admin.modules.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection

