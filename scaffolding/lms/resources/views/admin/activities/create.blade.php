@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Add Activity</h1>
    <form action="{{ route('admin.activities.store') }}" method="POST">
        @csrf
        @include('admin.activities._form', ['activity' => null])
        <button type="submit" class="btn btn-success">Create</button>
        <a href="{{ route('admin.activities.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection

