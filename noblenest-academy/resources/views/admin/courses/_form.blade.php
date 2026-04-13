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
<div class="row g-3">
  <div class="col-12">
    <label class="form-label">{{ I18n::get('title') }}</label>
    <input type="text" name="title" class="form-control" value="{{ old('title', $course->title ?? '') }}" required>
  </div>
  <div class="col-12">
    <label class="form-label">{{ I18n::get('slug') }}</label>
    <input type="text" name="slug" class="form-control" value="{{ old('slug', $course->slug ?? '') }}" placeholder="{{ I18n::get('optional') }}">
    <div class="form-text">{{ I18n::get('slug_help') }}</div>
  </div>
  <div class="col-md-3">
    <label class="form-label">Emoji</label>
    <input type="text" name="emoji" class="form-control" value="{{ old('emoji', $course->emoji ?? '') }}" placeholder="👶" maxlength="10" id="courseEmoji">
  </div>
  <div class="col-md-3">
    <label class="form-label">Color</label>
    <div class="input-group">
      <input type="color" name="color" class="form-control form-control-color" value="{{ old('color', $course->color ?? '#64B5F6') }}" style="max-width:3rem" id="courseColor">
      <input type="text" class="form-control" value="{{ old('color', $course->color ?? '#64B5F6') }}" id="courseColorText"
             oninput="document.getElementById('courseColor').value=this.value">
    </div>
  </div>
  <div class="col-md-3">
    <label class="form-label">Min Age (years)</label>
    <input type="number" name="age_min" class="form-control" value="{{ old('age_min', $course->age_min ?? '') }}" min="0" max="18" placeholder="0">
  </div>
  <div class="col-md-3">
    <label class="form-label">Max Age (years)</label>
    <input type="number" name="age_max" class="form-control" value="{{ old('age_max', $course->age_max ?? '') }}" min="0" max="18" placeholder="1">
  </div>
  <div class="col-12">
    <label class="form-label">{{ I18n::get('description') }}</label>
    <textarea name="description" class="form-control" rows="4">{{ old('description', $course->description ?? '') }}</textarea>
  </div>
</div>
<div class="d-flex gap-2 mt-4">
  <button type="submit" class="btn btn-primary">{{ I18n::get('save') }}</button>
  <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary">{{ I18n::get('cancel') }}</a>
</div>

<script>
document.getElementById('courseColor')?.addEventListener('input', function() {
    document.getElementById('courseColorText').value = this.value;
});
</script>
