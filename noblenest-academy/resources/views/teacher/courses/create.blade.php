@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <a href="{{ route('teacher.courses.index') }}" class="btn btn-sm btn-outline-secondary mb-3">
            <i class="bi bi-arrow-left"></i> Back to Courses
        </a>
        <h1 class="fw-bold mb-0">{{ isset($course) ? 'Edit Course' : 'Create New Course' }}</h1>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST"
          action="{{ isset($course) ? route('teacher.courses.update', $course) : route('teacher.courses.store') }}"
          enctype="multipart/form-data">
        @csrf
        @if(isset($course)) @method('PUT') @endif

        <div class="row g-4">
            <div class="col-lg-8">
                {{-- Basic Info --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header fw-bold">Course Details</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Course Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="{{ old('title', $course->title ?? '') }}" required placeholder="e.g. Beginner Math for Ages 5-7">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Tell students what this course is about...">{{ old('description', $course->description ?? '') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">What Students Will Learn</label>
                            <textarea name="what_you_learn" class="form-control" rows="4" placeholder="List key learning outcomes (one per line)...">{{ old('what_you_learn', $course->what_you_learn ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Curriculum Sections --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header fw-bold d-flex justify-content-between align-items-center">
                        <span>Curriculum Sections</span>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addSection()">
                            <i class="bi bi-plus"></i> Add Section
                        </button>
                    </div>
                    <div class="card-body" id="sections-container">
                        @php $sections = $course->sections ?? collect(); @endphp
                        @forelse($sections as $i => $sec)
                        <div class="section-row border rounded p-3 mb-2 bg-light">
                            <input type="hidden" name="sections[{{ $i }}][id]" value="{{ $sec->id }}">
                            <div class="d-flex gap-2 align-items-start">
                                <div class="flex-grow-1">
                                    <input type="text" name="sections[{{ $i }}][title]" class="form-control form-control-sm mb-1"
                                        placeholder="Section title" value="{{ $sec->title }}" required>
                                    <input type="text" name="sections[{{ $i }}][description]" class="form-control form-control-sm"
                                        placeholder="Brief description (optional)" value="{{ $sec->description }}">
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.section-row').remove()">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        @empty
                        <p class="text-muted small" id="no-sections-msg">No sections yet. Add sections to outline your curriculum.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                {{-- Settings --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header fw-bold">Course Settings</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Subject</label>
                            <input type="text" name="subject" class="form-control" value="{{ old('subject', $course->subject ?? '') }}" placeholder="e.g. Math, English, Science">
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col">
                                <label class="form-label fw-semibold">Age Min</label>
                                <input type="number" name="age_min" class="form-control" value="{{ old('age_min', $course->age_min ?? '') }}" min="0" max="18">
                            </div>
                            <div class="col">
                                <label class="form-label fw-semibold">Age Max</label>
                                <input type="number" name="age_max" class="form-control" value="{{ old('age_max', $course->age_max ?? '') }}" min="0" max="18">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Level</label>
                            <select name="level" class="form-select">
                                @foreach(['beginner','intermediate','advanced'] as $lvl)
                                    <option value="{{ $lvl }}" {{ old('level', $course->level ?? 'beginner') === $lvl ? 'selected' : '' }}>{{ ucfirst($lvl) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Language</label>
                            <select name="language" class="form-select">
                                @foreach(['en' => 'English','fr' => 'French','ru' => 'Russian','zh' => 'Mandarin','es' => 'Spanish','ko' => 'Korean'] as $code => $name)
                                    <option value="{{ $code }}" {{ old('language', $course->language ?? 'en') === $code ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-8">
                                <label class="form-label fw-semibold">Price</label>
                                <input type="number" name="price" step="0.01" min="0" class="form-control"
                                    value="{{ old('price', $course->price ?? 0) }}" placeholder="0 = free">
                            </div>
                            <div class="col-4">
                                <label class="form-label fw-semibold">Currency</label>
                                <select name="currency" class="form-select">
                                    @foreach(['USD','EUR','GBP','AED'] as $c)
                                        <option value="{{ $c }}" {{ old('currency', $course->currency ?? 'USD') === $c ? 'selected' : '' }}>{{ $c }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Max Students</label>
                            <input type="number" name="max_students" class="form-control" value="{{ old('max_students', $course->max_students ?? '') }}" placeholder="Leave blank for unlimited" min="1">
                        </div>
                    </div>
                </div>

                {{-- Media --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header fw-bold">Media & Files</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Course Thumbnail</label>
                            @if(isset($course) && $course->thumbnail)
                                <img src="{{ Storage::url($course->thumbnail) }}" class="img-thumbnail mb-2 d-block" style="max-height:100px">
                            @endif
                            <input type="file" name="thumbnail" class="form-control" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Syllabus / Curriculum File</label>
                            @if(isset($course) && $course->syllabus_file)
                                <a href="{{ Storage::url($course->syllabus_file) }}" target="_blank" class="d-block mb-2 small">
                                    <i class="bi bi-file-earmark-pdf"></i> View current syllabus
                                </a>
                            @endif
                            <input type="file" name="syllabus_file" class="form-control" accept=".pdf,.doc,.docx">
                            <div class="form-text">PDF or Word document (max 10MB)</div>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-save"></i> {{ isset($course) ? 'Save Changes' : 'Create Course' }}
                    </button>
                    @if(isset($course))
                        <a href="{{ route('teacher.courses.show', $course) }}" class="btn btn-outline-secondary">Cancel</a>
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
        <div class="d-flex gap-2 align-items-start">
            <div class="flex-grow-1">
                <input type="text" name="sections[${sectionIndex}][title]" class="form-control form-control-sm mb-1"
                    placeholder="Section title" required>
                <input type="text" name="sections[${sectionIndex}][description]" class="form-control form-control-sm"
                    placeholder="Brief description (optional)">
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.section-row').remove()">
                <i class="bi bi-trash"></i>
            </button>
        </div>`;
    container.appendChild(row);
    sectionIndex++;
}
</script>
@endsection
