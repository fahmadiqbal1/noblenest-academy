<div class="mb-3">
    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
    <input type="text" name="title" id="title" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" value="{{ old('title', $quiz->title ?? '') }}" required>
</div>
<div class="mb-3">
    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
    <textarea name="description" id="description" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500">{{ old('description', $quiz->description ?? '') }}</textarea>
</div>
<div class="mb-3">
    <label for="module_id" class="block text-sm font-medium text-gray-700 mb-1">Module</label>
    <select name="module_id" id="module_id" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500">
        <option value="">-- None --</option>
        @foreach($modules as $module)
            <option value="{{ $module->id }}" @if(old('module_id', $quiz->module_id ?? '') == $module->id) selected @endif>{{ $module->title }}</option>
        @endforeach
    </select>
</div>

