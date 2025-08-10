<div class="mb-3">
    <label class="form-label">Title</label>
    <input type="text" name="title" class="form-control" value="{{ old('title', $activity->title ?? '') }}" required>
</div>
<div class="mb-3">
    <label class="form-label">Description</label>
    <textarea name="description" class="form-control" rows="2" required>{{ old('description', $activity->description ?? '') }}</textarea>
</div>
<div class="mb-3 row g-2">
    <div class="col">
        <label class="form-label">Age Min</label>
        <input type="number" name="age_min" class="form-control" min="0" max="18" value="{{ old('age_min', $activity->age_min ?? '') }}">
    </div>
    <div class="col">
        <label class="form-label">Age Max</label>
        <input type="number" name="age_max" class="form-control" min="0" max="18" value="{{ old('age_max', $activity->age_max ?? '') }}">
    </div>
</div>
<div class="mb-3">
    <label class="form-label">Skill</label>
    <input type="text" name="skill" class="form-control" value="{{ old('skill', $activity->skill ?? '') }}">
</div>
<div class="mb-3">
    <label class="form-label">Type</label>
    <select name="type" class="form-select" required>
        <option value="video" @if(old('type', $activity->type ?? '')=='video') selected @endif>Video</option>
        <option value="tracing" @if(old('type', $activity->type ?? '')=='tracing') selected @endif>Tracing</option>
        <option value="drawing" @if(old('type', $activity->type ?? '')=='drawing') selected @endif>Drawing</option>
        <option value="puzzle" @if(old('type', $activity->type ?? '')=='puzzle') selected @endif>Puzzle</option>
        <option value="quiz" @if(old('type', $activity->type ?? '')=='quiz') selected @endif>Quiz</option>
    </select>
</div>
<div class="mb-3">
    <label class="form-label">Language</label>
    <select name="language" class="form-select">
        <option value="en" @if(old('language', $activity->language ?? '')=='en') selected @endif>English</option>
        <option value="fr" @if(old('language', $activity->language ?? '')=='fr') selected @endif>French</option>
        <option value="ru" @if(old('language', $activity->language ?? '')=='ru') selected @endif>Russian</option>
        <option value="zh" @if(old('language', $activity->language ?? '')=='zh') selected @endif>Mandarin</option>
        <option value="es" @if(old('language', $activity->language ?? '')=='es') selected @endif>Spanish</option>
        <option value="ko" @if(old('language', $activity->language ?? '')=='ko') selected @endif>Korean</option>
        <option value="ur" @if(old('language', $activity->language ?? '')=='ur') selected @endif>Urdu</option>
        <option value="ar" @if(old('language', $activity->language ?? '')=='ar') selected @endif>Arabic</option>
        <option value="multi" @if(old('language', $activity->language ?? '')=='multi') selected @endif>Multi</option>
    </select>
</div>
<div class="mb-3">
    <label class="form-label">Media (URL or Upload)</label>
    <input type="text" name="media_url" class="form-control mb-2" placeholder="Paste media URL or leave blank to upload" value="{{ old('media_url', $activity->media_url ?? '') }}">
    <input type="file" name="media_file" class="form-control">
    @if(isset($activity) && $activity->media_url)
        <div class="mt-2"><a href="{{ $activity->media_url }}" target="_blank">Current Media</a></div>
    @endif
</div>

