@extends('layouts.app')
@section('meta_title', 'Batch Content Generation – Admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.orchestrator.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <h1 class="h4 fw-bold mb-0">Batch Content Generator</h1>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.content-batch.store') }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Subject</label>
                                <select name="subject" class="form-select rounded-3 @error('subject') is-invalid @enderror">
                                    @foreach(['literacy','numeracy','creativity','stem','social','motor'] as $s)
                                        <option value="{{ $s }}" @selected(old('subject') === $s)>{{ ucfirst($s) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Age Tier</label>
                                <select name="age_tier" class="form-select rounded-3 @error('age_tier') is-invalid @enderror">
                                    <option value="baby">Baby (0-23m)</option>
                                    <option value="toddler">Toddler (24-47m)</option>
                                    <option value="preschool">Preschool (48-71m)</option>
                                    <option value="school" @selected(old('age_tier')==='school')>School (72m+)</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Count</label>
                                <input type="number" name="count" min="1" max="50" class="form-control rounded-3"
                                       value="{{ old('count', 10) }}" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Language</label>
                                <select name="language" class="form-select rounded-3">
                                    <option value="en">English</option>
                                    <option value="fr">French</option>
                                    <option value="ar">Arabic</option>
                                    <option value="ur">Urdu</option>
                                    <option value="es">Spanish</option>
                                    <option value="zh">Chinese</option>
                                    <option value="ko">Korean</option>
                                    <option value="ru">Russian</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Free Tier?</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="is_free" id="is_free" value="1"
                                           @checked(old('is_free'))>
                                    <label class="form-check-label" for="is_free">Mark as free activities</label>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Activity Types</label>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach(['tracing','puzzle','drawing','quiz','matching','story'] as $type)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                   name="activity_types[]" value="{{ $type }}"
                                                   id="type_{{ $type }}"
                                                   @checked(in_array($type, old('activity_types', ['tracing','quiz'])))>
                                            <label class="form-check-label" for="type_{{ $type }}">{{ ucfirst($type) }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('activity_types')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <hr class="my-4">
                        <button type="submit" class="btn btn-dark rounded-pill px-4 fw-semibold">
                            <i class="bi bi-lightning-charge me-1"></i> Queue Batch Job
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 bg-light">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">How It Works</h5>
                    <ol class="ps-3 mb-0" style="font-size:0.875rem;line-height:2">
                        <li>Choose subject, age tier, language, and types</li>
                        <li>The AI provider generates content via your configured model</li>
                        <li>Activities land in the <strong>Content Review</strong> queue</li>
                        <li>Admin reviews and approves before publishing</li>
                        <li>Parents & children see the new content immediately</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
