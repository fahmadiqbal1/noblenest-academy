@extends('layouts.app')

@section('content')
<h1 class="h3 mb-3">New Course</h1>
<div class="card">
  <div class="card-body">
    <form action="{{ route('admin.courses.store') }}" method="POST">
      @include('admin.courses._form')
    </form>
  </div>
</div>
@endsection
