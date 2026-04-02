@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="d-flex align-items-center gap-3 mb-4">
                <h1 class="h3 fw-bold mb-0">Edit Teacher Profile</h1>
                <a href="{{ route('teacher.profile.show') }}" class="btn btn-outline-secondary ms-auto">← Back</a>
            </div>

            @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
            @endif

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('teacher.profile.update') }}" enctype="multipart/form-data">
                        @csrf @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Display Name</label>
                            <input type="text" name="display_name" class="form-control" value="{{ old('display_name', $teacherProfile->display_name ?? auth()->user()->name) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Bio</label>
                            <textarea name="bio" class="form-control" rows="4" placeholder="Tell students about yourself, your qualifications, and teaching style...">{{ old('bio', $teacherProfile->bio ?? '') }}</textarea>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Country</label>
                                <input type="text" name="country" class="form-control" value="{{ old('country', $teacherProfile->country ?? '') }}" placeholder="e.g. United Kingdom">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Languages Taught</label>
                                <input type="text" name="languages" class="form-control" value="{{ old('languages', $teacherProfile->languages ?? '') }}" placeholder="e.g. English, French">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Subjects / Specialties</label>
                            <input type="text" name="subjects" class="form-control" value="{{ old('subjects', $teacherProfile->subjects ?? '') }}" placeholder="e.g. Early Literacy, STEM, Music">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Qualifications / Certifications</label>
                            <textarea name="qualifications" class="form-control" rows="3" placeholder="PGCE, TEFL, MA Education...">{{ old('qualifications', $teacherProfile->qualifications ?? '') }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Profile Photo</label>
                            <input type="file" name="photo" class="form-control" accept="image/*">
                            <div class="form-text">JPEG or PNG, max 2MB.</div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold">Save Profile</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
