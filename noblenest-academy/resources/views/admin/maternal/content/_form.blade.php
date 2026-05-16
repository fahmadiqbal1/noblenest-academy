@php $c = $content ?? null; @endphp

<div class="flex flex-wrap gap-3 mb-4">
    <div class="md:w-8/12">
        <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Title</label>
        <input type="text" name="title" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 rounded-lg" value="{{ old('title', $c?->title) }}" required>
    </div>
    <div class="md:w-4/12">
        <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Slug</label>
        <input type="text" name="slug" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 rounded-lg" value="{{ old('slug', $c?->slug) }}" required>
    </div>
    <div class="md:w-4/12">
        <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Content Type</label>
        <select name="content_type" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 rounded-lg" required>
            @foreach(['article', 'video', 'exercise', 'recipe', 'technique', 'herb_guide'] as $type)
                <option value="{{ $type }}" {{ old('content_type', $c?->content_type) === $type ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
            @endforeach
        </select>
    </div>
    <div class="md:w-4/12">
        <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Stage</label>
        <select name="stage" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 rounded-lg" required>
            @foreach(['trimester_1', 'trimester_2', 'trimester_3', 'labor_prep', 'postnatal_0_3m', 'postnatal_3_6m', 'postnatal_6_12m'] as $stage)
                <option value="{{ $stage }}" {{ old('stage', $c?->stage) === $stage ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $stage)) }}</option>
            @endforeach
        </select>
    </div>
    <div class="md:w-4/12">
        <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Cultural Origin</label>
        <select name="cultural_origin" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 rounded-lg">
            <option value="">General</option>
            @foreach(['chinese', 'japanese', 'ayurvedic'] as $culture)
                <option value="{{ $culture }}" {{ old('cultural_origin', $c?->cultural_origin) === $culture ? 'selected' : '' }}>{{ ucfirst($culture) }}</option>
            @endforeach
        </select>
    </div>
    <div class="md:w-6/12">
        <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Category</label>
        <input type="text" name="category" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 rounded-lg" value="{{ old('category', $c?->category) }}" required placeholder="e.g. exercise, herbs, nutrition, breastfeeding, newborn_care">
    </div>
    <div class="w-full">
        <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Description / Body</label>
        <textarea name="description" rows="6" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 rounded-lg" required>{{ old('description', $c?->description) }}</textarea>
    </div>
    <div class="w-full">
        <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Benefit Explanation <small class="text-red-600">(required — tells users WHY this matters)</small></label>
        <textarea name="benefit_explanation" rows="3" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 rounded-lg" required maxlength="1000">{{ old('benefit_explanation', $c?->benefit_explanation) }}</textarea>
    </div>
    <div class="md:w-4/12">
        <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Video URL</label>
        <input type="url" name="video_url" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 rounded-lg" value="{{ old('video_url', $c?->video_url) }}">
    </div>
    <div class="md:w-4/12">
        <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Audio URL</label>
        <input type="url" name="audio_url" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 rounded-lg" value="{{ old('audio_url', $c?->audio_url) }}">
    </div>
    <div class="md:w-4/12">
        <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Thumbnail URL</label>
        <input type="url" name="thumbnail_url" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 rounded-lg" value="{{ old('thumbnail_url', $c?->thumbnail_url) }}">
    </div>
    <div class="w-full">
        <div class="flex items-center gap-2">
            <input class="w-4 h-4 rounded border-gray-300 text-violet-600 focus:ring-violet-500" type="checkbox" name="is_published" value="1" id="is_published" {{ old('is_published', $c?->is_published) ? 'checked' : '' }}>
            <label class="text-sm" for="is_published">Published</label>
        </div>
    </div>
</div>
