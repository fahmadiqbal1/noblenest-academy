<div class="mb-3">
    <label for="course_id" class="block text-sm font-medium text-gray-700 mb-1">Course</label>
    <select name="course_id" id="course_id" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" required>
        <option value="">Select Course</option>
        @foreach($courses as $course)
            <option value="{{ $course->id }}" @if(old('course_id', $module->course_id ?? '') == $course->id) selected @endif>{{ $course->title }}</option>
        @endforeach
    </select>
</div>
<div class="mb-3">
    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Module Title</label>
    <input type="text" name="title" id="title" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" value="{{ old('title', $module->title ?? '') }}" required>
</div>
<div class="mb-3">
    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
    <textarea name="description" id="description" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500">{{ old('description', $module->description ?? '') }}</textarea>
</div>
<div class="mb-3">
    <label for="order" class="block text-sm font-medium text-gray-700 mb-1">Order</label>
    <input type="number" name="order" id="order" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" min="0" value="{{ old('order', $module->order ?? 0) }}">
</div>
<div class="mb-3">
    <label for="activities" class="block text-sm font-medium text-gray-700 mb-1">Assign Activities</label>
    <select name="activities[]" id="activities" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" multiple size="8">
        @foreach($activities as $activity)
            <option value="{{ $activity->id }}" @if(in_array($activity->id, old('activities', $selected ?? []))) selected @endif>
                {{ $activity->title }} ({{ $activity->language }}, {{ $activity->skill }})
            </option>
        @endforeach
    </select>
    <small class="mt-1 text-sm text-[var(--color-text-muted)]">Hold Ctrl (Windows) or Cmd (Mac) to select multiple activities.</small>
</div>
