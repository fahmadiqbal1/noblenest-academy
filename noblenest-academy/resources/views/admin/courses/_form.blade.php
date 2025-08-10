@csrf
@if ($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif
<div class="mb-3">
  <label class="form-label">{{ I18n::get('title') }}</label>
  <input type="text" name="title" class="form-control" value="{{ old('title', $course->title ?? '') }}" required>
</div>
<div class="mb-3">
  <label class="form-label">{{ I18n::get('slug') }}</label>
  <input type="text" name="slug" class="form-control" value="{{ old('slug', $course->slug ?? '') }}" placeholder="{{ I18n::get('optional') }}">
  <div class="form-text">{{ I18n::get('slug_help') }}</div>
</div>
<div class="mb-3">
  <label class="form-label">{{ I18n::get('description') }}</label>
  <textarea name="description" class="form-control" rows="6">{{ old('description', $course->description ?? '') }}</textarea>
</div>
<div class="d-flex gap-2">
  <button type="submit" class="btn btn-primary">{{ I18n::get('save') }}</button>
  <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary">{{ I18n::get('cancel') }}</a>
</div>
