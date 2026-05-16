@extends('layouts.admin')

@section('content')
<h1 class="h3 mb-3">{{ I18n::get('edit_course') }}</h1>
<div class="bg-white rounded-xl border border-gray-200 shadow-sm">
  <div class="p-5">
    <form action="{{ route('admin.courses.update', $course) }}" method="POST">
      @csrf
      @method('PUT')
      @include('admin.courses._form')
    </form>
  </div>
</div>
@endsection
