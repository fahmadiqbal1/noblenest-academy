@extends('layouts.app')

@section('content')
<h1 class="h3 mb-3">Edit Course</h1>
<div class="card">
  <div class="card-body">
    <form action="{{ route('admin.courses.update', $course) }}" method="POST">
      @csrf
      @method('PUT')
      @include('admin.courses._form')
    </form>
  </div>
</div>
@endsection
