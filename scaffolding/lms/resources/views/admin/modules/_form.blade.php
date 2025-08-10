<div class="mb-3">
    <label for="course_id" class="form-label">Course</label>
    <select name="course_id" id="course_id" class="form-select" required>
        <option value="">Select Course</option>
        @foreach($courses as $course)
            <option value="{{ $course->id }}" @if(old('course_id', $module->course_id ?? '') == $course->id) selected @endif>{{ $course->title }}</option>
        @endforeach
    </select>
</div>
<div class="mb-3">
    <label for="title" class="form-label">Module Title</label>
    <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $module->title ?? '') }}" required>
</div>
<div class="mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea name="description" id="description" class="form-control">{{ old('description', $module->description ?? '') }}</textarea>
</div>
<div class="mb-3">
    <label for="order" class="form-label">Order</label>
    <input type="number" name="order" id="order" class="form-control" min="0" value="{{ old('order', $module->order ?? 0) }}">
</div>
<div class="mb-3">
    <label for="activities" class="form-label">Assign Activities</label>
    <select name="activities[]" id="activities" class="form-select" multiple size="8">
        @foreach($activities as $activity)
            <option value="{{ $activity->id }}" @if(in_array($activity->id, old('activities', $selected ?? []))) selected @endif>
                {{ $activity->title }} ({{ $activity->language }}, {{ $activity->skill }})
            </option>
        @endforeach
    </select>
    <small class="form-text text-muted">Hold Ctrl (Windows) or Cmd (Mac) to select multiple activities.</small>
</div>
