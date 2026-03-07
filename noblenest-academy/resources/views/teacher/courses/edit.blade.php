@php
// Edit view: just re-use the create form which handles both create & update
@endphp
@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <a href="{{ route('teacher.courses.show', $course) }}" class="btn btn-sm btn-outline-secondary mb-3">
            <i class="bi bi-arrow-left"></i> Back to Course
        </a>
        <h1 class="fw-bold mb-0">Edit Course</h1>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('teacher.courses.update', $course) }}" enctype="multipart/form-data">
        @csrf @method('PUT')

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header fw-bold">Course Details</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Course Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="{{ old('title', $course->title) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="4">{{ old('description', $course->description) }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">What Students Will Learn</label>
                            <textarea name="what_you_learn" class="form-control" rows="4">{{ old('what_you_learn', $course->what_you_learn) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header fw-bold d-flex justify-content-between align-items-center">
                        <span>Curriculum Sections</span>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addSection()"><i class="bi bi-plus"></i> Add Section</button>
                    </div>
                    <div class="card-body" id="sections-container">
                        @foreach($course->sections as $i => $sec)
                        <div class="section-row border rounded p-3 mb-2 bg-light">
                            <input type="hidden" name="sections[{{ $i }}][id]" value="{{ $sec->id }}">
                            <div class="d-flex gap-2 align-items-start">
                                <div class="flex-grow-1">
                                    <input type="text" name="sections[{{ $i }}][title]" class="form-control form-control-sm mb-1" value="{{ $sec->title }}" required>
                                    <input type="text" name="sections[{{ $i }}][description]" class="form-control form-control-sm" value="{{ $sec->description }}">
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.section-row').remove()"><i class="bi bi-trash"></i></button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header fw-bold">Course Settings</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Subject</label>
                            <input type="text" name="subject" class="form-control" value="{{ old('subject', $course->subject) }}">
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col"><label class="form-label">Age Min</label><input type="number" name="age_min" class="form-control" value="{{ old('age_min', $course->age_min) }}" min="0" max="18"></div>
                            <div class="col"><label class="form-label">Age Max</label><input type="number" name="age_max" class="form-control" value="{{ old('age_max', $course->age_max) }}" min="0" max="18"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Level</label>
                            <select name="level" class="form-select">
                                @foreach(['beginner','intermediate','advanced'] as $lvl)
                                    <option value="{{ $lvl }}" {{ old('level', $course->level) === $lvl ? 'selected' : '' }}>{{ ucfirst($lvl) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Language</label>
                            <select name="language" class="form-select">
                                @foreach(['en'=>'English','fr'=>'French','ru'=>'Russian','zh'=>'Mandarin','es'=>'Spanish','ko'=>'Korean'] as $code => $name)
                                    <option value="{{ $code }}" {{ old('language', $course->language) === $code ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-8"><label class="form-label">Price</label><input type="number" name="price" step="0.01" min="0" class="form-control" value="{{ old('price', $course->price) }}"></div>
                            <div class="col-4"><label class="form-label">Currency</label>
                                <select name="currency" class="form-select">
                                    @foreach(['USD','EUR','GBP','AED'] as $c)
                                        <option value="{{ $c }}" {{ old('currency', $course->currency) === $c ? 'selected' : '' }}>{{ $c }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Max Students</label>
                            <input type="number" name="max_students" class="form-control" value="{{ old('max_students', $course->max_students) }}" min="1">
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header fw-bold">Media</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Thumbnail</label>
                            @if($course->thumbnail)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($course->thumbnail) }}" class="img-thumbnail mb-2 d-block" style="max-height:80px">
                            @endif
                            <input type="file" name="thumbnail" class="form-control" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Syllabus File</label>
                            @if($course->syllabus_file)
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($course->syllabus_file) }}" target="_blank" class="d-block mb-2 small"><i class="bi bi-file-earmark-pdf"></i> Current syllabus</a>
                            @endif
                            <input type="file" name="syllabus_file" class="form-control" accept=".pdf,.doc,.docx">
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-save"></i> Save Changes</button>
                    <a href="{{ route('teacher.courses.show', $course) }}" class="btn btn-outline-secondary">Cancel</a>
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
    row.innerHTML = `<div class="d-flex gap-2 align-items-start"><div class="flex-grow-1"><input type="text" name="sections[${sectionIndex}][title]" class="form-control form-control-sm mb-1" placeholder="Section title" required><input type="text" name="sections[${sectionIndex}][description]" class="form-control form-control-sm" placeholder="Brief description"></div><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.section-row').remove()"><i class="bi bi-trash"></i></button></div>`;
    container.appendChild(row);
    sectionIndex++;
}
</script>
@endsection

