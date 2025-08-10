@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 m-0">{{ I18n::get('courses') }}</h1>
  <a class="btn btn-primary" href="{{ route('admin.courses.create') }}">{{ I18n::get('new_course') }}</a>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>{{ I18n::get('title') }}</th>
          <th>{{ I18n::get('slug') }}</th>
          <th>{{ I18n::get('created') }}</th>
          <th class="text-end">{{ I18n::get('actions') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($courses as $course)
        <tr>
          <td class="fw-semibold">{{ $course->title }}</td>
          <td><span class="text-muted">{{ $course->slug }}</span></td>
          <td>{{ $course->created_at ? $course->created_at->format('Y-m-d') : '' }}</td>
          <td class="text-end">
            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.courses.edit', $course) }}">{{ I18n::get('edit') }}</a>
            <form action="{{ route('admin.courses.destroy', $course) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ I18n::get('delete_course_confirm') }}');">
              @csrf
              @method('DELETE')
              <button class="btn btn-sm btn-outline-danger" type="submit">{{ I18n::get('delete') }}</button>
            </form>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="4" class="text-center text-muted py-4">{{ I18n::get('no_courses') }}</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="mt-3">
  {{ $courses->links() }}
</div>
@endsection
