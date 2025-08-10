<div class="mb-3">
    <label for="title" class="form-label">Title</label>
    <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $quiz->title ?? '') }}" required>
</div>
<div class="mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea name="description" id="description" class="form-control">{{ old('description', $quiz->description ?? '') }}</textarea>
</div>
<div class="mb-3">
    <label for="module_id" class="form-label">Module</label>
    <select name="module_id" id="module_id" class="form-select">
        <option value="">-- None --</option>
        @foreach($modules as $module)
            <option value="{{ $module->id }}" @if(old('module_id', $quiz->module_id ?? '') == $module->id) selected @endif>{{ $module->title }}</option>
        @endforeach
    </select>
</div>

