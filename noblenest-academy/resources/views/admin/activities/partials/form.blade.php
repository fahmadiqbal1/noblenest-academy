@php
$subjectColors = [
    'sensory'=>'#f59e0b','motor'=>'#10b981','language'=>'#3b82f6','literacy'=>'#6366f1',
    'numeracy'=>'#ec4899','science'=>'#06b6d4','art'=>'#f97316','music'=>'#8b5cf6',
    'social'=>'#14b8a6','character'=>'#22c55e','etiquette'=>'#a855f7','quran'=>'#059669',
    'islamic'=>'#065f46','arabic'=>'#0891b2','coding'=>'#1d4ed8','robotics'=>'#7c3aed',
    'stem'=>'#0369a1','cultural'=>'#b45309',
];
$subjects = [
    'sensory'=>'🌈 Sensory','motor'=>'🏃 Motor Skills','language'=>'💬 Language',
    'literacy'=>'📖 Literacy','numeracy'=>'🔢 Numeracy','science'=>'🔬 Science',
    'art'=>'🎨 Art','music'=>'🎵 Music','social'=>'🤝 Social','character'=>'💛 Character',
    'etiquette'=>'🎩 Etiquette','quran'=>'📿 Quran','islamic'=>'☪️ Islamic Studies',
    'arabic'=>'ع Arabic','coding'=>'💻 Coding','robotics'=>'🤖 Robotics',
    'stem'=>'🧪 STEM','cultural'=>'🌍 Cultural',
];
$actTypes = [
    'video'=>'📹 Video','tracing'=>'✏️ Tracing','drawing'=>'🎨 Drawing',
    'puzzle'=>'🧩 Puzzle','quiz'=>'🧠 Quiz','story'=>'📖 Story',
    'music'=>'🎵 Music','outdoor'=>'🌿 Outdoor','experiment'=>'🔬 Experiment','coding'=>'💻 Coding',
];
$ageGroups = ['baby'=>'👶 Baby (0–2)','toddler'=>'🐣 Toddler (2–3)','preschool'=>'🎒 Preschool (3–6)',
    'early-school'=>'📚 Early School (6–8)','school'=>'🏫 School (8–10)'];
$langs = ['en'=>'🇬🇧 English','fr'=>'🇫🇷 French','ru'=>'🇷🇺 Russian','zh'=>'🇨🇳 Mandarin',
    'es'=>'🇪🇸 Spanish','ko'=>'🇰🇷 Korean','ur'=>'🇵🇰 Urdu','ar'=>'🇸🇦 Arabic','multi'=>'🌐 Multi'];
$val = fn($field, $default='') => old($field, $activity->$field ?? $default);
// Format array-cast fields back to newline text for editing
$materialsText = is_array($activity->materials_needed ?? null)
    ? implode("\n", $activity->materials_needed)
    : old('materials_needed', $activity->materials_needed ?? '');
$objectivesText = is_array($activity->learning_objectives ?? null)
    ? implode("\n", $activity->learning_objectives)
    : old('learning_objectives', $activity->learning_objectives ?? '');
@endphp
<style>
.nn-form-card { background: rgba(255,255,255,0.72); border: 1px solid rgba(24,34,47,0.08); border-radius: 1.2rem; padding: 1.2rem 1.4rem; margin-bottom: 1rem; }
.nn-field-label { font-size: 0.78rem; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; color: #5f6c7b; margin-bottom: 0.45rem; display: block; }
.nn-toggle { display: flex; align-items: center; gap: 0.6rem; cursor: pointer; user-select: none; }
.nn-toggle input[type=checkbox] { width: 44px; height: 24px; border-radius: 999px; appearance: none; background: #d1d5db; transition: background 0.2s; cursor: pointer; position: relative; flex-shrink: 0; }
.nn-toggle input[type=checkbox]:checked { background: var(--nn-primary, #0d5c63); }
.nn-toggle input[type=checkbox]::after { content:''; position: absolute; width: 18px; height: 18px; border-radius: 50%; background: #fff; top: 3px; left: 3px; transition: left 0.2s; box-shadow: 0 2px 6px rgba(0,0,0,0.18); }
.nn-toggle input[type=checkbox]:checked::after { left: 23px; }
.diff-radio { display: none; }
.diff-label { padding: 0.35rem 1rem; border-radius: 999px; font-size: 0.82rem; font-weight: 700; border: 2px solid transparent; cursor: pointer; transition: all 0.15s; }
.diff-radio:checked + .diff-label { border-color: currentColor; box-shadow: 0 0 0 3px rgba(0,0,0,0.08); }
.diff-easy  .diff-label { color: #16a34a; background: #dcfce7; }
.diff-medium .diff-label { color: #d97706; background: #fef3c7; }
.diff-hard  .diff-label { color: #dc2626; background: #fee2e2; }
.emoji-preview { font-size: 2rem; line-height: 1; display: inline-block; min-width: 2.5rem; text-align: center; }
.subject-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; flex-shrink: 0; }
</style>

{{-- Row 1: Title + Emoji --}}
<div class="nn-form-card">
    <div class="row g-3 align-items-start">
        <div class="col-12 col-md-9">
            <label class="nn-field-label">Activity Title *</label>
            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                placeholder="e.g. Tracing Numbers 1–5" value="{{ $val('title') }}" required>
            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 col-md-3">
            <label class="nn-field-label">Emoji Badge</label>
            <div class="input-group">
                <input type="text" name="emoji" id="emojiInput" class="form-control"
                    maxlength="8" placeholder="🎯" value="{{ $val('emoji') }}"
                    oninput="document.getElementById('emojiPreview').textContent=this.value||'…'">
                <span class="input-group-text fs-4" id="emojiPreview">{{ $val('emoji') ?: '…' }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Row 2: Description --}}
<div class="nn-form-card">
    <label class="nn-field-label">Description *</label>
    <textarea name="description" class="form-control @error('description') is-invalid @enderror"
        rows="3" placeholder="Brief description of what the child will do…" required>{{ $val('description') }}</textarea>
    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

{{-- Row 3: Subject + Activity Type --}}
<div class="nn-form-card">
    <div class="row g-3">
        <div class="col-12 col-sm-6">
            <label class="nn-field-label">Subject *</label>
            <select name="subject" id="subjectSelect" class="form-select @error('subject') is-invalid @enderror"
                onchange="updateSubjectColor(this.value)">
                <option value="">— Choose subject —</option>
                @foreach($subjects as $key => $label)
                    <option value="{{ $key }}"
                        data-color="{{ $subjectColors[$key] }}"
                        @selected(old('subject', $activity->subject ?? '') === $key)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 col-sm-6">
            <label class="nn-field-label">Activity Type *</label>
            <select name="activity_type" class="form-select @error('activity_type') is-invalid @enderror" required>
                @foreach($actTypes as $key => $label)
                    <option value="{{ $key }}" @selected(old('activity_type', $activity->activity_type ?? '') === $key)>{{ $label }}</option>
                @endforeach
            </select>
            @error('activity_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

{{-- Row 4: Age group + Age min/max + Duration --}}
<div class="nn-form-card">
    <div class="row g-3">
        <div class="col-12 col-sm-4">
            <label class="nn-field-label">Age Group</label>
            <select name="age_group" class="form-select">
                <option value="">— Any —</option>
                @foreach($ageGroups as $key => $label)
                    <option value="{{ $key }}" @selected(old('age_group', $activity->age_group ?? '') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-sm-2">
            <label class="nn-field-label">Age Min</label>
            <input type="number" name="age_min" class="form-control" min="0" max="18"
                placeholder="0" value="{{ $val('age_min') }}">
        </div>
        <div class="col-6 col-sm-2">
            <label class="nn-field-label">Age Max</label>
            <input type="number" name="age_max" class="form-control" min="0" max="18"
                placeholder="10" value="{{ $val('age_max') }}">
        </div>
        <div class="col-12 col-sm-4">
            <label class="nn-field-label">Duration <span id="durationLabel" class="text-primary fw-bold">{{ $val('duration_minutes', 15) }}min</span></label>
            <input type="range" name="duration_minutes" class="form-range" min="1" max="60" step="1"
                value="{{ $val('duration_minutes', 15) }}"
                oninput="document.getElementById('durationLabel').textContent=this.value+'min'">
        </div>
    </div>
</div>

{{-- Row 5: Difficulty + Language --}}
<div class="nn-form-card">
    <div class="row g-3 align-items-start">
        <div class="col-12 col-sm-7">
            <label class="nn-field-label">Difficulty</label>
            <div class="d-flex gap-2 flex-wrap mt-1">
                @foreach(['easy'=>['🟢','diff-easy'],'medium'=>['🟡','diff-medium'],'hard'=>['🔴','diff-hard']] as $d=>[$icon,$cls])
                    <div class="{{ $cls }}">
                        <input class="diff-radio" type="radio" name="difficulty" id="diff_{{ $d }}" value="{{ $d }}"
                            @checked(old('difficulty', $activity->difficulty ?? 'easy') === $d)>
                        <label class="diff-label" for="diff_{{ $d }}">{{ $icon }} {{ ucfirst($d) }}</label>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="col-12 col-sm-5">
            <label class="nn-field-label">Language</label>
            <select name="language" class="form-select">
                @foreach($langs as $code => $label)
                    <option value="{{ $code }}" @selected(old('language', $activity->language ?? 'en') === $code)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

{{-- Row 6: Media --}}
<div class="nn-form-card">
    <label class="nn-field-label">Media URL <span class="text-muted fw-normal">(or upload below)</span></label>
    <input type="text" name="media_url" class="form-control mb-2"
        placeholder="Paste video/audio/image URL…" value="{{ $val('media_url') }}">
    <label class="nn-field-label">Upload Media File</label>
    <input type="file" name="media_file" class="form-control mb-2" accept="video/*,audio/*,image/*,.pdf">
    @if(isset($activity) && $activity->media_url)
        <a href="{{ $activity->media_url }}" target="_blank" class="btn btn-sm btn-outline-secondary mt-1">
            <i class="bi bi-play-circle"></i> Current Media
        </a>
    @endif
    <div class="mt-2">
        <label class="nn-field-label">Thumbnail URL</label>
        <input type="text" name="thumbnail_url" class="form-control"
            placeholder="https://… thumbnail image for card view" value="{{ $val('thumbnail_url') }}">
    </div>
</div>

{{-- Row 7: Instructions + Materials + Objectives --}}
<div class="nn-form-card">
    <div class="mb-3">
        <label class="nn-field-label">Instructions</label>
        <textarea name="instructions" class="form-control" rows="3"
            placeholder="Step-by-step guide for the parent / child…">{{ $val('instructions') }}</textarea>
    </div>
    <div class="row g-3">
        <div class="col-12 col-sm-6">
            <label class="nn-field-label">Materials Needed <span class="text-muted fw-normal">(one per line)</span></label>
            <textarea name="materials_needed" class="form-control" rows="3"
                placeholder="Paper&#10;Crayons&#10;Scissors">{{ old('materials_needed', $materialsText) }}</textarea>
        </div>
        <div class="col-12 col-sm-6">
            <label class="nn-field-label">Learning Objectives <span class="text-muted fw-normal">(one per line)</span></label>
            <textarea name="learning_objectives" class="form-control" rows="3"
                placeholder="Recognize numbers 1-5&#10;Improve fine motor control">{{ old('learning_objectives', $objectivesText) }}</textarea>
        </div>
    </div>
</div>

{{-- Row 8: Toggles --}}
<div class="nn-form-card">
    <label class="nn-field-label mb-3">Settings</label>
    <div class="d-flex flex-wrap gap-4">
        <label class="nn-toggle">
            <input type="checkbox" name="is_free" value="1" @checked($val('is_free', false))>
            <span class="text-sm fw-semibold">🆓 Free Activity</span>
        </label>
        <label class="nn-toggle">
            <input type="checkbox" name="is_rtl" value="1" @checked($val('is_rtl', false))>
            <span class="text-sm fw-semibold">↩️ RTL Layout</span>
        </label>
        <label class="nn-toggle">
            <input type="checkbox" name="is_muslim_only" value="1" @checked($val('is_muslim_only', false))>
            <span class="text-sm fw-semibold">☪️ Muslim-Specific</span>
        </label>
    </div>
</div>

<script>
function updateSubjectColor(val) {
    const colors = @json($subjectColors);
    const sel = document.getElementById('subjectSelect');
    if (sel && colors[val]) {
        sel.style.borderLeftColor = colors[val];
        sel.style.borderLeftWidth = '4px';
    }
}
document.addEventListener('DOMContentLoaded', function () {
    const sel = document.getElementById('subjectSelect');
    if (sel && sel.value) updateSubjectColor(sel.value);
});
</script>

