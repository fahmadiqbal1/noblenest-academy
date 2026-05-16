@extends('layouts.teacher')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <a href="{{ route('teacher.courses.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-gray-300 text-gray-700 hover:bg-gray-100 mb-3">
            <x-ui.icon name="arrow-left" /> Back to Courses
        </a>
        <h1 class="font-bold mb-0">{{ isset($course) ? 'Edit Course' : 'Create New Course' }}</h1>
    </div>

    @if($errors->any())
        <div class="flex items-start gap-3 p-4 rounded-lg border bg-red-50 border-red-200 text-red-800">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST"
          action="{{ isset($course) ? route('teacher.courses.update', $course) : route('teacher.courses.store') }}"
          enctype="multipart/form-data">
        @csrf
        @if(isset($course)) @method('PUT') @endif

        <div class="flex flex-wrap gap-4">
            <div class="lg:w-8/12">
                {{-- Basic Info --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-4">
                    <div class="px-5 py-3 border-b border-gray-200 font-semibold font-bold">Course Details</div>
                    <div class="p-5">
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Course Title <span class="text-red-600">*</span></label>
                            <input type="text" name="title" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" value="{{ old('title', $course->title ?? '') }}" required placeholder="e.g. Beginner Math for Ages 5-7">
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Description</label>
                            <textarea name="description" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" rows="4" placeholder="Tell students what this course is about...">{{ old('description', $course->description ?? '') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">What Students Will Learn</label>
                            <textarea name="what_you_learn" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" rows="4" placeholder="List key learning outcomes (one per line)...">{{ old('what_you_learn', $course->what_you_learn ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Curriculum Sections --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-4">
                    <div class="px-5 py-3 border-b border-gray-200 font-semibold font-bold flex justify-between items-center">
                        <span>Curriculum Sections</span>
                        <button type="button" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-violet-600 text-violet-600 hover:bg-violet-600 hover:text-white" onclick="addSection()">
                            <x-ui.icon name="plus" /> Add Section
                        </button>
                    </div>
                    <div class="p-5" id="sections-container">
                        @php $sections = $course->sections ?? collect(); @endphp
                        @forelse($sections as $i => $sec)
                        <div class="section-row border rounded p-3 mb-2 bg-gray-50">
                            <input type="hidden" name="sections[{{ $i }}][id]" value="{{ $sec->id }}">
                            <div class="flex gap-2 items-start">
                                <div class="flex-grow-1">
                                    <input type="text" name="sections[{{ $i }}][title]" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 px-2 py-1 text-sm mb-1"
                                        placeholder="Section title" value="{{ $sec->title }}" required>
                                    <input type="text" name="sections[{{ $i }}][description]" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 px-2 py-1 text-sm"
                                        placeholder="Brief description (optional)" value="{{ $sec->description }}">
                                </div>
                                <button type="button" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-red-600 text-red-600 hover:bg-red-600 hover:text-white" onclick="this.closest('.section-row').remove()">
                                    <x-ui.icon name="trash" />
                                </button>
                            </div>
                        </div>
                        @empty
                        <p class="text-[var(--color-text-muted)] text-sm" id="no-sections-msg">No sections yet. Add sections to outline your curriculum.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="lg:w-4/12">
                {{-- Settings --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-4">
                    <div class="px-5 py-3 border-b border-gray-200 font-semibold font-bold">Course Settings</div>
                    <div class="p-5">
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Subject</label>
                            <input type="text" name="subject" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" value="{{ old('subject', $course->subject ?? '') }}" placeholder="e.g. Math, English, Science">
                        </div>
                        <div class="flex flex-wrap gap-2 mb-3">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Age Min</label>
                                <input type="number" name="age_min" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" value="{{ old('age_min', $course->age_min ?? '') }}" min="0" max="18">
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Age Max</label>
                                <input type="number" name="age_max" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" value="{{ old('age_max', $course->age_max ?? '') }}" min="0" max="18">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Level</label>
                            <select name="level" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500">
                                @foreach(['beginner','intermediate','advanced'] as $lvl)
                                    <option value="{{ $lvl }}" {{ old('level', $course->level ?? 'beginner') === $lvl ? 'selected' : '' }}>{{ ucfirst($lvl) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Language</label>
                            <select name="language" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500">
                                @foreach(['en' => 'English','fr' => 'French','ru' => 'Russian','zh' => 'Mandarin','es' => 'Spanish','ko' => 'Korean'] as $code => $name)
                                    <option value="{{ $code }}" {{ old('language', $course->language ?? 'en') === $code ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex flex-wrap gap-2 mb-3">
                            <div class="w-8/12">
                                <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Price</label>
                                <input type="number" name="price" step="0.01" min="0" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500"
                                    value="{{ old('price', $course->price ?? 0) }}" placeholder="0 = free">
                            </div>
                            <div class="w-4/12">
                                <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Currency</label>
                                <select name="currency" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500">
                                    @foreach(['USD','EUR','GBP','AED'] as $c)
                                        <option value="{{ $c }}" {{ old('currency', $course->currency ?? 'USD') === $c ? 'selected' : '' }}>{{ $c }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Max Students</label>
                            <input type="number" name="max_students" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" value="{{ old('max_students', $course->max_students ?? '') }}" placeholder="Leave blank for unlimited" min="1">
                        </div>
                    </div>
                </div>

                {{-- Media --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-4">
                    <div class="px-5 py-3 border-b border-gray-200 font-semibold font-bold">Media & Files</div>
                    <div class="p-5">
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Course Thumbnail</label>
                            @if(isset($course) && $course->thumbnail)
                                <img src="{{ Storage::url($course->thumbnail) }}" class="img-thumbnail mb-2 block" style="max-height:100px">
                            @endif
                            <input type="file" name="thumbnail" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Syllabus / Curriculum File</label>
                            @if(isset($course) && $course->syllabus_file)
                                <a href="{{ Storage::url($course->syllabus_file) }}" target="_blank" class="block mb-2 text-sm">
                                    <x-ui.icon name="file-text" /> View current syllabus
                                </a>
                            @endif
                            <input type="file" name="syllabus_file" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" accept=".pdf,.doc,.docx">
                            <div class="mt-1 text-sm text-[var(--color-text-muted)]">PDF or Word document (max 10MB)</div>
                        </div>
                    </div>
                </div>

                <div class="grid gap-2">
                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-violet-600 text-white hover:bg-violet-700 px-5 py-3 text-lg">
                        <x-ui.icon name="save" /> {{ isset($course) ? 'Save Changes' : 'Create Course' }}
                    </button>
                    @if(isset($course))
                        <a href="{{ route('teacher.courses.show', $course) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-gray-300 text-gray-700 hover:bg-gray-100">Cancel</a>
                    @endif
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
let sectionIndex = {{ isset($course) ? $course->sections->count() : 0 }};

function addSection() {
    const container = document.getElementById('sections-container');
    const msg = document.getElementById('no-sections-msg');
    if (msg) msg.remove();
    const row = document.createElement('div');
    row.className = 'section-row border rounded p-3 mb-2 bg-light';
    row.innerHTML = `
        <div class="flex gap-2 items-start">
            <div class="flex-grow-1">
                <input type="text" name="sections[${sectionIndex}][title]" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 px-2 py-1 text-sm mb-1"
                    placeholder="Section title" required>
                <input type="text" name="sections[${sectionIndex}][description]" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 px-2 py-1 text-sm"
                    placeholder="Brief description (optional)">
            </div>
            <button type="button" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-red-600 text-red-600 hover:bg-red-600 hover:text-white" onclick="this.closest('.section-row').remove()">
                <x-ui.icon name="trash" />
            </button>
        </div>`;
    container.appendChild(row);
    sectionIndex++;
}
</script>
@endsection
