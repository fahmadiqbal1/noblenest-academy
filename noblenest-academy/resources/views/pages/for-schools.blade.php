@extends('layouts.app')

@section('title', 'Noble Nest Academy for Schools')

@section('content')
{{-- Hero --}}
<section class="bg-primary text-white py-5">
    <div class="container py-3 text-center">
        <h1 class="display-5 fw-bold mb-3">Learning Without Borders</h1>
        <p class="lead mb-4">Bring Noble Nest Academy to your school, NGO, or community center. Bulk pricing, multilingual support, and dedicated onboarding.</p>
        <a href="#inquiry" class="btn btn-warning btn-lg fw-semibold px-5">Request a Demo</a>
    </div>
</section>

{{-- Stats --}}
<section class="py-5 bg-light">
    <div class="container">
        <div class="row text-center g-4">
            <div class="col-md-3">
                <div class="h2 fw-bold text-primary">150+</div>
                <div class="text-muted">Countries reached</div>
            </div>
            <div class="col-md-3">
                <div class="h2 fw-bold text-primary">8</div>
                <div class="text-muted">Languages supported</div>
            </div>
            <div class="col-md-3">
                <div class="h2 fw-bold text-primary">500+</div>
                <div class="text-muted">Activities for ages 0–12</div>
            </div>
            <div class="col-md-3">
                <div class="h2 fw-bold text-primary">97%</div>
                <div class="text-muted">Parent satisfaction rate</div>
            </div>
        </div>
    </div>
</section>

{{-- Features --}}
<section class="py-5">
    <div class="container">
        <h2 class="text-center fw-bold mb-5">Built for Educators</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 p-4">
                    <div class="fs-2 mb-3">🎓</div>
                    <h5>Curriculum-Aligned</h5>
                    <p class="text-muted">Activities mapped to developmental milestones and school curricula across 20+ countries.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 p-4">
                    <div class="fs-2 mb-3">📊</div>
                    <h5>Classroom Analytics</h5>
                    <p class="text-muted">Track engagement, completion rates, and developmental progress for every student.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 p-4">
                    <div class="fs-2 mb-3">💰</div>
                    <h5>Scholarship Program</h5>
                    <p class="text-muted">We offer free access to qualifying low-income schools and NGOs. Apply today.</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Inquiry Form --}}
<section id="inquiry" class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card border-0 shadow-sm p-4">
                    <h3 class="fw-bold mb-1">Get in Touch</h3>
                    <p class="text-muted mb-4">Fill in the form and our team will respond within 24 hours.</p>

                    @if(session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                    @endif

                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('school-inquiry.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">School / Organization Name</label>
                            <input type="text" name="school_name" class="form-control @error('school_name') is-invalid @enderror" value="{{ old('school_name') }}" required>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Contact Name</label>
                                <input type="text" name="contact_name" class="form-control @error('contact_name') is-invalid @enderror" value="{{ old('contact_name') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Contact Email</label>
                                <input type="email" name="contact_email" class="form-control @error('contact_email') is-invalid @enderror" value="{{ old('contact_email') }}" required>
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Country</label>
                                <input type="text" name="country" class="form-control @error('country') is-invalid @enderror" value="{{ old('country') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Number of Students</label>
                                <select name="student_count" class="form-select @error('student_count') is-invalid @enderror">
                                    <option value="">Select range</option>
                                    <option value="1-50">1–50</option>
                                    <option value="50-200">50–200</option>
                                    <option value="200-1000">200–1000</option>
                                    <option value="1000+">1000+</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Message (optional)</label>
                            <textarea name="message" class="form-control" rows="3" placeholder="Tell us about your needs, goals, or questions">{{ old('message') }}</textarea>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="scholarship_interest" id="scholarship_interest" value="1">
                            <label class="form-check-label" for="scholarship_interest">
                                We are interested in the scholarship program
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-semibold py-2">Send Inquiry</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
