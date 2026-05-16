@php
// Edit view: just re-use the create form which handles both create & update
@endphp
@extends('layouts.teacher')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <a href="{{ route('teacher.courses.show', $course) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-gray-300 text-gray-700 hover:bg-gray-100 mb-3">
            <x-ui.icon name="arrow-left" /> Back to Course
        </a>
        <h1 class="font-bold mb-0">Edit Course</h1>
    </div>

    @if($errors->any())
        <div class="flex items-start gap-3 p-4 rounded-lg border bg-red-50 border-red-200 text-red-800">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('teacher.courses.update', $course) }}" enctype="multipart/form-data">
        @csrf @method('PUT')

        <div class="flex flex-wrap gap-4">
            <div class="lg:w-8/12">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-4">
                    <div class="px-5 py-3 border-b border-gray-200 font-semibold font-bold">Course Details</div>
                    <div class="p-5">
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Course Title <span class="text-red-600">*</span></label>
                            <input type="text" name="title" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" value="{{ old('title', $course->title) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Description</label>
                            <textarea name="description" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" rows="4">{{ old('description', $course->description) }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">What Students Will Learn</label>
                            <textarea name="what_you_learn" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" rows="4">{{ old('what_you_learn', $course->what_you_learn) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-4">
                    <div class="px-5 py-3 border-b border-gray-200 font-semibold font-bold flex justify-between items-center">
                        <span>Curriculum Sections</span>
                        <button type="button" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-violet-600 text-violet-600 hover:bg-violet-600 hover:text-white" onclick="addSection()"><x-ui.icon name="plus" /> Add Section</button>
                    </div>
                    <div class="p-5" id="sections-container">
                        @foreach($course->sections as $i => $sec)
                        <div class="section-row border rounded p-3 mb-2 bg-gray-50">
                            <input type="hidden" name="sections[{{ $i }}][id]" value="{{ $sec->id }}">
                            <div class="flex gap-2 items-start">
                                <div class="flex-grow-1">
                                    <input type="text" name="sections[{{ $i }}][title]" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 px-2 py-1 text-sm mb-1" value="{{ $sec->title }}" required>
                                    <input type="text" name="sections[{{ $i }}][description]" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 px-2 py-1 text-sm" value="{{ $sec->description }}">
                                </div>
                                <button type="button" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-red-600 text-red-600 hover:bg-red-600 hover:text-white" onclick="this.closest('.section-row').remove()"><x-ui.icon name="trash" /></button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="lg:w-4/12">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-4">
                    <div class="px-5 py-3 border-b border-gray-200 font-semibold font-bold">Course Settings</div>
                    <div class="p-5">
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                            <input type="text" name="subject" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" value="{{ old('subject', $course->subject) }}">
                        </div>
                        <div class="flex flex-wrap gap-2 mb-3">
                            <div class="flex-1"><label class="block text-sm font-medium text-gray-700 mb-1">Age Min</label><input type="number" name="age_min" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" value="{{ old('age_min', $course->age_min) }}" min="0" max="18"></div>
                            <div class="flex-1"><label class="block text-sm font-medium text-gray-700 mb-1">Age Max</label><input type="number" name="age_max" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" value="{{ old('age_max', $course->age_max) }}" min="0" max="18"></div>
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Level</label>
                            <select name="level" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500">
                                @foreach(['beginner','intermediate','advanced'] as $lvl)
                                    <option value="{{ $lvl }}" {{ old('level', $course->level) === $lvl ? 'selected' : '' }}>{{ ucfirst($lvl) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Language</label>
                            <select name="language" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500">
                                @foreach(['en'=>'English','fr'=>'French','ru'=>'Russian','zh'=>'Mandarin','es'=>'Spanish','ko'=>'Korean'] as $code => $name)
                                    <option value="{{ $code }}" {{ old('language', $course->language) === $code ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex flex-wrap gap-2 mb-3">
                            <div class="w-8/12"><label class="block text-sm font-medium text-gray-700 mb-1">Price</label><input type="number" name="price" step="0.01" min="0" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" value="{{ old('price', $course->price) }}"></div>
                            <div class="w-4/12"><label class="block text-sm font-medium text-gray-700 mb-1">Currency</label>
                                <select name="currency" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500">
                                    @foreach(['USD','EUR','GBP','AED'] as $c)
                                        <option value="{{ $c }}" {{ old('currency', $course->currency) === $c ? 'selected' : '' }}>{{ $c }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Max Students</label>
                            <input type="number" name="max_students" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" value="{{ old('max_students', $course->max_students) }}" min="1">
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-4">
                    <div class="px-5 py-3 border-b border-gray-200 font-semibold font-bold">Media</div>
                    <div class="p-5">
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Thumbnail</label>
                            @if($course->thumbnail)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($course->thumbnail) }}" class="img-thumbnail mb-2 block" style="max-height:80px">
                            @endif
                            <input type="file" name="thumbnail" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Syllabus File</label>
                            @if($course->syllabus_file)
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($course->syllabus_file) }}" target="_blank" class="block mb-2 text-sm"><x-ui.icon name="file-text" /> Current syllabus</a>
                            @endif
                            <input type="file" name="syllabus_file" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" accept=".pdf,.doc,.docx">
                        </div>
                    </div>
                </div>

                <div class="grid gap-2">
                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-violet-600 text-white hover:bg-violet-700 px-5 py-3 text-lg"><x-ui.icon name="save" /> Save Changes</button>
                    <a href="{{ route('teacher.courses.show', $course) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-gray-300 text-gray-700 hover:bg-gray-100">Cancel</a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
let sectionIndex = {{ $course->sections->count() }};
function addSection() {
    const container = document.getElementById('sections-container');
    const row = document.createElement('div');
    row.className = 'section-row border rounded p-3 mb-2 bg-light';
    row.innerHTML = `<div class="flex gap-2 items-start"><div class="flex-grow-1"><input type="text" name="sections[${sectionIndex}][title]" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 px-2 py-1 text-sm mb-1" placeholder="Section title" required><input type="text" name="sections[${sectionIndex}][description]" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 px-2 py-1 text-sm" placeholder="Brief description"></div><button type="button" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-red-600 text-red-600 hover:bg-red-600 hover:text-white" onclick="this.closest('.section-row').remove()"><x-ui.icon name="trash" /></button></div>`;
    container.appendChild(row);
    sectionIndex++;
}
</script>
@endsection

