@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Edit Activity</h1>
    <form action="{{ route('admin.activities.update', $activity) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.activities._form', ['activity' => $activity])
        <button type="submit" class="btn btn-success">Update</button>
        <a href="{{ route('admin.activities.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection

