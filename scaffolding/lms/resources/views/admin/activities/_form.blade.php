<div class="mb-3">
    <label for="title" class="form-label">Title</label>
    <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $activity->title ?? '') }}" required>
</div>
<div class="mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea name="description" id="description" class="form-control">{{ old('description', $activity->description ?? '') }}</textarea>
</div>
<div class="row mb-3">
    <div class="col">
        <label for="age_min" class="form-label">Age Min</label>
        <input type="number" name="age_min" id="age_min" class="form-control" min="0" max="12" value="{{ old('age_min', $activity->age_min ?? '') }}">
    </div>
    <div class="col">
        <label for="age_max" class="form-label">Age Max</label>
        <input type="number" name="age_max" id="age_max" class="form-control" min="0" max="12" value="{{ old('age_max', $activity->age_max ?? '') }}">
    </div>
</div>
<div class="mb-3">
    <label for="skill" class="form-label">Skill</label>
    <input type="text" name="skill" id="skill" class="form-control" value="{{ old('skill', $activity->skill ?? '') }}">
</div>
<div class="mb-3">
    <label for="activity_type" class="form-label">Activity Type</label>
    <input type="text" name="activity_type" id="activity_type" class="form-control" value="{{ old('activity_type', $activity->activity_type ?? '') }}">
</div>
<div class="mb-3">
    <label for="duration" class="form-label">Duration (minutes)</label>
    <input type="number" name="duration" id="duration" class="form-control" min="1" max="60" value="{{ old('duration', $activity->duration ?? '') }}">
</div>
<div class="mb-3">
    <label for="language" class="form-label">Language</label>
    <select name="language" id="language" class="form-select" required>
        @foreach(['en'=>'English','fr'=>'French','ru'=>'Russian','zh'=>'Mandarin','es'=>'Spanish','ko'=>'Korean','ur'=>'Urdu','ar'=>'Arabic'] as $code=>$lang)
            <option value="{{ $code }}" @if(old('language', $activity->language ?? '')==$code) selected @endif>{{ $lang }}</option>
        @endforeach
    </select>
</div>
<div class="mb-3">
    <label for="media_url" class="form-label">Media URL</label>
    <input type="text" name="media_url" id="media_url" class="form-control" value="{{ old('media_url', $activity->media_url ?? '') }}">
</div>
<div class="form-check mb-3">
    <input class="form-check-input" type="checkbox" name="is_rtl" id="is_rtl" value="1" @if(old('is_rtl', $activity->is_rtl ?? false)) checked @endif>
    <label class="form-check-label" for="is_rtl">Right-to-Left (Urdu/Arabic)</label>
</div>
