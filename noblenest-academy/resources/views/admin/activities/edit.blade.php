@extends('layouts.app')
@section('content')
<div class="container py-4" style="max-width:820px">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.activities.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h1 class="mb-0 text-primary">✏️ Edit Activity</h1>
    </div>
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('admin.activities.update', $activity) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.activities.partials.form', ['activity' => $activity])
        <div class="d-flex gap-2 mt-2">
            <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-circle"></i> Save Changes</button>
            <a href="{{ route('admin.activities.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

