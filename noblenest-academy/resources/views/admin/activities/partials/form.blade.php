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
$materialsText = is_array($activity->materials_needed ?? null)
    ? implode("\n", $activity->materials_needed)
    : old('materials_needed', $activity->materials_needed ?? '');
$objectivesText = is_array($activity->learning_objectives ?? null)
    ? implode("\n", $activity->learning_objectives)
    : old('learning_objectives', $activity->learning_objectives ?? '');
@endphp

{{-- Section: Basic Info --}}
<x-ui.section :title="__('Basic Info')" class="pt-0">

    {{-- Title + Emoji --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
        <div class="sm:col-span-2">
            <x-ui.field :label="__('Activity Title')" name="title" required :error="$errors->first('title')">
                <x-ui.input
                    type="text"
                    name="title"
                    :placeholder="__('e.g. Tracing Numbers 1–5')"
                    :value="$val('title')"
                    :invalid="$errors->has('title')"
                    required
                />
            </x-ui.field>
        </div>
        <div>
            <x-ui.field :label="__('Emoji Badge')" name="emoji">
                <div class="flex items-center gap-2">
                    <x-ui.input
                        type="text"
                        name="emoji"
                        id="emojiInput"
                        maxlength="8"
                        placeholder="🎯"
                        :value="$val('emoji')"
                        class="flex-1"
                        oninput="document.getElementById('emojiPreview').textContent=this.value||'…'"
                    />
                    <span
                        id="emojiPreview"
                        class="text-3xl leading-none min-w-[2.5rem] text-center select-none"
                        aria-hidden="true"
                    >{{ $val('emoji') ?: '…' }}</span>
                </div>
            </x-ui.field>
        </div>
    </div>

    {{-- Description --}}
    <x-ui.field :label="__('Description')" name="description" required :error="$errors->first('description')">
        <x-ui.textarea
            name="description"
            :placeholder="__('Brief description of what the child will do…')"
            :value="$val('description')"
            :invalid="$errors->has('description')"
            :rows="3"
        />
    </x-ui.field>

</x-ui.section>

{{-- Section: Classification --}}
<x-ui.section :title="__('Classification')">

    {{-- Subject + Activity Type --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
        <x-ui.field :label="__('Subject')" name="subject" required :error="$errors->first('subject')">
            <div class="relative">
                <select
                    name="subject"
                    id="subjectSelect"
                    class="block w-full rounded-[var(--radius-sm)] border-[2px] border-[var(--color-border)] bg-[var(--color-surface-strong)] text-[var(--color-text)] text-base py-2.5 ps-4 pe-10 focus:outline-none focus:border-[var(--color-brand-500)] appearance-none cursor-pointer min-h-[2.5rem] border-s-4 transition-colors {{ $errors->has('subject') ? 'border-[var(--color-coral-500)]' : '' }}"
                    aria-required="true"
                    onchange="updateSubjectColor(this.value)"
                >
                    <option value="">— {{ __('Choose subject') }} —</option>
                    @foreach($subjects as $key => $label)
                        <option
                            value="{{ $key }}"
                            data-color="{{ $subjectColors[$key] }}"
                            @selected(old('subject', $activity->subject ?? '') === $key)
                        >{{ $label }}</option>
                    @endforeach
                </select>
                <span class="pointer-events-none absolute inset-y-0 end-0 flex items-center pe-3 text-[var(--color-text-muted)]">
                    <x-ui.icon name="chevron-down" class="w-4 h-4" />
                </span>
            </div>
            @error('subject')
                <p class="text-sm text-[var(--color-coral-500)] flex items-center gap-1 mt-1" role="alert">
                    <x-ui.icon name="alert-circle" class="w-3.5 h-3.5 shrink-0" />
                    {{ $message }}
                </p>
            @enderror
        </x-ui.field>

        <x-ui.field :label="__('Activity Type')" name="activity_type" required :error="$errors->first('activity_type')">
            <div class="relative">
                <select
                    name="activity_type"
                    class="block w-full rounded-[var(--radius-sm)] border-[2px] border-[var(--color-border)] bg-[var(--color-surface-strong)] text-[var(--color-text)] text-base py-2.5 ps-4 pe-10 focus:outline-none focus:border-[var(--color-brand-500)] appearance-none cursor-pointer min-h-[2.5rem] {{ $errors->has('activity_type') ? 'border-[var(--color-coral-500)]' : '' }}"
                    required
                >
                    @foreach($actTypes as $key => $label)
                        <option value="{{ $key }}" @selected(old('activity_type', $activity->activity_type ?? '') === $key)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                <span class="pointer-events-none absolute inset-y-0 end-0 flex items-center pe-3 text-[var(--color-text-muted)]">
                    <x-ui.icon name="chevron-down" class="w-4 h-4" />
                </span>
            </div>
            @error('activity_type')
                <p class="text-sm text-[var(--color-coral-500)] flex items-center gap-1 mt-1" role="alert">
                    <x-ui.icon name="alert-circle" class="w-3.5 h-3.5 shrink-0" />
                    {{ $message }}
                </p>
            @enderror
        </x-ui.field>
    </div>

    {{-- Age group + Age min/max + Duration --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-4">
        <div class="col-span-2 sm:col-span-1">
            <x-ui.field :label="__('Age Group')" name="age_group">
                <div class="relative">
                    <select
                        name="age_group"
                        class="block w-full rounded-[var(--radius-sm)] border-[2px] border-[var(--color-border)] bg-[var(--color-surface-strong)] text-[var(--color-text)] text-sm py-2.5 ps-3 pe-8 focus:outline-none focus:border-[var(--color-brand-500)] appearance-none cursor-pointer min-h-[2.5rem]"
                    >
                        <option value="">— {{ __('Any') }} —</option>
                        @foreach($ageGroups as $key => $label)
                            <option value="{{ $key }}" @selected(old('age_group', $activity->age_group ?? '') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <span class="pointer-events-none absolute inset-y-0 end-0 flex items-center pe-2 text-[var(--color-text-muted)]">
                        <x-ui.icon name="chevron-down" class="w-4 h-4" />
                    </span>
                </div>
            </x-ui.field>
        </div>
        <x-ui.field :label="__('Age Min')" name="age_min">
            <x-ui.input type="number" name="age_min" min="0" max="18" placeholder="0" :value="$val('age_min')" />
        </x-ui.field>
        <x-ui.field :label="__('Age Max')" name="age_max">
            <x-ui.input type="number" name="age_max" min="0" max="18" placeholder="10" :value="$val('age_max')" />
        </x-ui.field>
        <div>
            <label class="block text-sm font-semibold text-[var(--color-text)] mb-1">
                {{ __('Duration') }}
                <span id="durationLabel" class="text-[var(--color-primary)] font-bold ms-1">{{ $val('duration_minutes', 15) }}min</span>
            </label>
            <input
                type="range"
                name="duration_minutes"
                min="1"
                max="60"
                step="1"
                value="{{ $val('duration_minutes', 15) }}"
                class="w-full accent-[var(--color-primary)] cursor-pointer"
                oninput="document.getElementById('durationLabel').textContent=this.value+'min'"
            />
        </div>
    </div>

    {{-- Difficulty + Language --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <fieldset>
            <legend class="block text-sm font-semibold text-[var(--color-text)] mb-2">{{ __('Difficulty') }}</legend>
            <div class="flex gap-2 flex-wrap">
                @foreach(['easy' => ['text-emerald-700','bg-emerald-50','border-emerald-400'], 'medium' => ['text-amber-700','bg-amber-50','border-amber-400'], 'hard' => ['text-[var(--color-coral-700)]','bg-[var(--color-coral-50)]','border-[var(--color-coral-400)]']] as $d => [$tc, $bg, $bc])
                    <label class="inline-flex items-center cursor-pointer">
                        <input
                            type="radio"
                            name="difficulty"
                            value="{{ $d }}"
                            class="sr-only peer"
                            @checked(old('difficulty', $activity->difficulty ?? 'easy') === $d)
                        />
                        <span class="px-4 py-1.5 rounded-full text-sm font-bold border-2 transition-all peer-checked:border-current peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-[var(--color-brand-600)] {{ $tc }} {{ $bg }} {{ $bc }} peer-not-checked:border-transparent">
                            {{ ucfirst($d) }}
                        </span>
                    </label>
                @endforeach
            </div>
        </fieldset>

        <x-ui.field :label="__('Language')" name="language">
            <div class="relative">
                <select
                    name="language"
                    class="block w-full rounded-[var(--radius-sm)] border-[2px] border-[var(--color-border)] bg-[var(--color-surface-strong)] text-[var(--color-text)] text-sm py-2.5 ps-4 pe-10 focus:outline-none focus:border-[var(--color-brand-500)] appearance-none cursor-pointer min-h-[2.5rem]"
                >
                    @foreach($langs as $code => $label)
                        <option value="{{ $code }}" @selected(old('language', $activity->language ?? 'en') === $code)>{{ $label }}</option>
                    @endforeach
                </select>
                <span class="pointer-events-none absolute inset-y-0 end-0 flex items-center pe-3 text-[var(--color-text-muted)]">
                    <x-ui.icon name="chevron-down" class="w-4 h-4" />
                </span>
            </div>
        </x-ui.field>
    </div>

</x-ui.section>

{{-- Section: Media --}}
<x-ui.section :title="__('Media')">

    <div class="space-y-4">
        <x-ui.field :label="__('Media URL')" name="media_url" :help="__('Paste a video, audio, or image URL')">
            <x-ui.input
                type="text"
                name="media_url"
                :placeholder="__('https://…')"
                :value="$val('media_url')"
            />
        </x-ui.field>

        <x-ui.field :label="__('Upload Media File')" name="media_file">
            <x-ui.input
                type="file"
                name="media_file"
                accept="video/*,audio/*,image/*,.pdf"
            />
        </x-ui.field>

        @if(isset($activity) && $activity->media_url)
            <x-ui.button variant="secondary" size="sm" icon="play" href="{{ $activity->media_url }}" target="_blank" rel="noopener noreferrer">
                {{ __('View Current Media') }}
            </x-ui.button>
        @endif

        <x-ui.field :label="__('Thumbnail URL')" name="thumbnail_url" :help="__('Image shown on activity cards')">
            <x-ui.input
                type="text"
                name="thumbnail_url"
                :placeholder="__('https://… thumbnail image URL')"
                :value="$val('thumbnail_url')"
            />
        </x-ui.field>
    </div>

</x-ui.section>

{{-- Section: Content --}}
<x-ui.section :title="__('Content')">

    <x-ui.field :label="__('Instructions')" name="instructions" :help="__('Step-by-step guide for the parent / child')" class="mb-4">
        <x-ui.textarea
            name="instructions"
            :placeholder="__('Step-by-step guide for the parent / child…')"
            :value="$val('instructions')"
            :rows="3"
        />
    </x-ui.field>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <x-ui.field :label="__('Materials Needed')" name="materials_needed" :help="__('One per line')">
            <x-ui.textarea
                name="materials_needed"
                :placeholder="__('Paper') . chr(10) . __('Crayons') . chr(10) . __('Scissors')"
                :value="old('materials_needed', $materialsText)"
                :rows="3"
            />
        </x-ui.field>

        <x-ui.field :label="__('Learning Objectives')" name="learning_objectives" :help="__('One per line')">
            <x-ui.textarea
                name="learning_objectives"
                :placeholder="__('Recognize numbers 1-5') . chr(10) . __('Improve fine motor control')"
                :value="old('learning_objectives', $objectivesText)"
                :rows="3"
            />
        </x-ui.field>
    </div>

</x-ui.section>

{{-- Section: Publishing --}}
<x-ui.section :title="__('Publishing')">

    <div class="flex flex-wrap gap-6">
        <label class="flex items-center gap-3 cursor-pointer select-none">
            <div class="relative">
                <input
                    type="checkbox"
                    name="is_free"
                    value="1"
                    class="sr-only peer"
                    @checked($val('is_free', false))
                />
                <div class="w-11 h-6 rounded-full bg-gray-200 peer-checked:bg-[var(--color-primary)] transition-colors"></div>
                <div class="absolute top-0.5 start-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
            </div>
            <span class="text-sm font-semibold text-[var(--color-text)]">{{ __('Free Activity') }}</span>
        </label>

        <label class="flex items-center gap-3 cursor-pointer select-none">
            <div class="relative">
                <input
                    type="checkbox"
                    name="is_rtl"
                    value="1"
                    class="sr-only peer"
                    @checked($val('is_rtl', false))
                />
                <div class="w-11 h-6 rounded-full bg-gray-200 peer-checked:bg-[var(--color-primary)] transition-colors"></div>
                <div class="absolute top-0.5 start-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
            </div>
            <span class="text-sm font-semibold text-[var(--color-text)]">{{ __('RTL Layout') }}</span>
        </label>

        <label class="flex items-center gap-3 cursor-pointer select-none">
            <div class="relative">
                <input
                    type="checkbox"
                    name="is_muslim_only"
                    value="1"
                    class="sr-only peer"
                    @checked($val('is_muslim_only', false))
                />
                <div class="w-11 h-6 rounded-full bg-gray-200 peer-checked:bg-[var(--color-primary)] transition-colors"></div>
                <div class="absolute top-0.5 start-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
            </div>
            <span class="text-sm font-semibold text-[var(--color-text)]">{{ __('Muslim-Specific') }}</span>
        </label>
    </div>

</x-ui.section>

<script>
function updateSubjectColor(val) {
    const colors = @json($subjectColors);
    const sel = document.getElementById('subjectSelect');
    if (sel && colors[val]) {
        sel.style.borderLeftColor = colors[val];
    } else if (sel) {
        sel.style.borderLeftColor = '';
    }
}
document.addEventListener('DOMContentLoaded', function () {
    const sel = document.getElementById('subjectSelect');
    if (sel && sel.value) updateSubjectColor(sel.value);
});
</script>
