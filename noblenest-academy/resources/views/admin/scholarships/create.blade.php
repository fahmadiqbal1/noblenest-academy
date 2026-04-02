@extends('layouts.app')

@section('title', 'Grant Scholarship')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('admin.scholarships.index') }}" class="btn btn-outline-secondary me-3">← Back</a>
                <h1 class="h3 fw-bold mb-0">Grant Scholarship</h1>
            </div>

            @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
            @endif

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.scholarships.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Recipient (User ID or Email)</label>
                            <input type="text" name="user_search" class="form-control" value="{{ old('user_search') }}" placeholder="Search by email..." required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Scholarship Type</label>
                            <select name="type" class="form-select" required>
                                <option value="full" @selected(old('type')==='full')>Full (100% free)</option>
                                <option value="partial" @selected(old('type')==='partial')>Partial (custom %)</option>
                                <option value="trial" @selected(old('type')==='trial')>Extended Trial</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Discount %</label>
                            <input type="number" name="discount_percent" class="form-control" value="{{ old('discount_percent', 100) }}" min="1" max="100" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Duration (months)</label>
                            <input type="number" name="duration_months" class="form-control" value="{{ old('duration_months', 12) }}" min="1" max="120">
                            <div class="form-text">Leave blank for indefinite.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Reason for grant, source (NGO, competition, etc.)...">{{ old('notes') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold">Grant Scholarship</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
