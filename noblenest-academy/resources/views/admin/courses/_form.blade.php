@csrf
@if ($errors->any())
  <div class="flex items-start gap-3 p-4 rounded-lg border bg-red-50 border-red-200 text-red-800">
    <ul class="mb-0">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif
<div class="flex flex-wrap gap-3">
  <div class="w-full">
    <label class="block text-sm font-medium text-gray-700 mb-1">{{ I18n::get('title') }}</label>
    <input type="text" name="title" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" value="{{ old('title', $course->title ?? '') }}" required>
  </div>
  <div class="w-full">
    <label class="block text-sm font-medium text-gray-700 mb-1">{{ I18n::get('slug') }}</label>
    <input type="text" name="slug" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" value="{{ old('slug', $course->slug ?? '') }}" placeholder="{{ I18n::get('optional') }}">
    <div class="mt-1 text-sm text-[var(--color-text-muted)]">{{ I18n::get('slug_help') }}</div>
  </div>
  <div class="md:w-3/12">
    <label class="block text-sm font-medium text-gray-700 mb-1">Emoji</label>
    <input type="text" name="emoji" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" value="{{ old('emoji', $course->emoji ?? '') }}" placeholder="👶" maxlength="10" id="courseEmoji">
  </div>
  <div class="md:w-3/12">
    <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
    <div class="flex w-full items-stretch">
      <input type="color" name="color" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 h-10 w-16 cursor-pointer p-0" value="{{ old('color', $course->color ?? '#64B5F6') }}" style="max-width:3rem" id="courseColor">
      <input type="text" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" value="{{ old('color', $course->color ?? '#64B5F6') }}" id="courseColorText"
             oninput="document.getElementById('courseColor').value=this.value">
    </div>
  </div>
  <div class="md:w-3/12">
    <label class="block text-sm font-medium text-gray-700 mb-1">Min Age (years)</label>
    <input type="number" name="age_min" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" value="{{ old('age_min', $course->age_min ?? '') }}" min="0" max="18" placeholder="0">
  </div>
  <div class="md:w-3/12">
    <label class="block text-sm font-medium text-gray-700 mb-1">Max Age (years)</label>
    <input type="number" name="age_max" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" value="{{ old('age_max', $course->age_max ?? '') }}" min="0" max="18" placeholder="1">
  </div>
  <div class="w-full">
    <label class="block text-sm font-medium text-gray-700 mb-1">{{ I18n::get('description') }}</label>
    <textarea name="description" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" rows="4">{{ old('description', $course->description ?? '') }}</textarea>
  </div>
</div>
<div class="flex gap-2 mt-4">
  <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-violet-600 text-white hover:bg-violet-700">{{ I18n::get('save') }}</button>
  <a href="{{ route('admin.courses.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-gray-300 text-gray-700 hover:bg-gray-100">{{ I18n::get('cancel') }}</a>
</div>

<script>
document.getElementById('courseColor')?.addEventListener('input', function() {
    document.getElementById('courseColorText').value = this.value;
});
</script>
