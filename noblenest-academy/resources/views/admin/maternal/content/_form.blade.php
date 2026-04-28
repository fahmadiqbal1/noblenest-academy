@php $c = $content ?? null; @endphp

<div class="row g-3 mb-4">
    <div class="col-md-8">
        <label class="form-label fw-semibold">Title</label>
        <input type="text" name="title" class="form-control rounded-3" value="{{ old('title', $c?->title) }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Slug</label>
        <input type="text" name="slug" class="form-control rounded-3" value="{{ old('slug', $c?->slug) }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Content Type</label>
        <select name="content_type" class="form-select rounded-3" required>
            @foreach(['article', 'video', 'exercise', 'recipe', 'technique', 'herb_guide'] as $type)
                <option value="{{ $type }}" {{ old('content_type', $c?->content_type) === $type ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Stage</label>
        <select name="stage" class="form-select rounded-3" required>
            @foreach(['trimester_1', 'trimester_2', 'trimester_3', 'labor_prep', 'postnatal_0_3m', 'postnatal_3_6m', 'postnatal_6_12m'] as $stage)
                <option value="{{ $stage }}" {{ old('stage', $c?->stage) === $stage ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $stage)) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Cultural Origin</label>
        <select name="cultural_origin" class="form-select rounded-3">
            <option value="">General</option>
            @foreach(['chinese', 'japanese', 'ayurvedic'] as $culture)
                <option value="{{ $culture }}" {{ old('cultural_origin', $c?->cultural_origin) === $culture ? 'selected' : '' }}>{{ ucfirst($culture) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">Category</label>
        <input type="text" name="category" class="form-control rounded-3" value="{{ old('category', $c?->category) }}" required placeholder="e.g. exercise, herbs, nutrition, breastfeeding, newborn_care">
    </div>
    <div class="col-12">
        <label class="form-label fw-semibold">Description / Body</label>
        <textarea name="description" rows="6" class="form-control rounded-3" required>{{ old('description', $c?->description) }}</textarea>
    </div>
    <div class="col-12">
        <label class="form-label fw-semibold">Benefit Explanation <small class="text-danger">(required — tells users WHY this matters)</small></label>
        <textarea name="benefit_explanation" rows="3" class="form-control rounded-3" required maxlength="1000">{{ old('benefit_explanation', $c?->benefit_explanation) }}</textarea>
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Video URL</label>
        <input type="url" name="video_url" class="form-control rounded-3" value="{{ old('video_url', $c?->video_url) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Audio URL</label>
        <input type="url" name="audio_url" class="form-control rounded-3" value="{{ old('audio_url', $c?->audio_url) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Thumbnail URL</label>
        <input type="url" name="thumbnail_url" class="form-control rounded-3" value="{{ old('thumbnail_url', $c?->thumbnail_url) }}">
    </div>
    <div class="col-12">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_published" value="1" id="is_published" {{ old('is_published', $c?->is_published) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_published">Published</label>
        </div>
    </div>
</div>
