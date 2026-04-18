@extends('layouts.app')

@section('meta_title', 'Find a Teacher | NobleNest Global Academy')
@section('meta_description', 'Browse live online courses from expert teachers on NobleNest Global Academy. Compare subjects, age fit, pricing, and enrolment options in one marketplace.')
@section('meta_image', asset('og-marketplace.png'))

@section('content')
<style>
    .market-hero,
    .market-filter,
    .market-card,
    .market-cta {
        background: var(--nn-surface);
        border: var(--nn-border-w) solid var(--nn-border);
        box-shadow: var(--nn-shadow);
    }
    .market-hero,
    .market-filter,
    .market-card,
    .market-cta { border-radius: var(--nn-radius); }
    .market-hero {
        position: relative;
        overflow: hidden;
        padding: 1.8rem;
        margin-bottom: 1.5rem;
        background:
            radial-gradient(circle at 14% 20%, var(--nn-primary-soft), transparent 20%),
            radial-gradient(circle at 88% 16%, var(--nn-primary-soft), transparent 24%),
            var(--nn-surface);
    }
    .market-eyebrow {
        text-transform: uppercase;
        letter-spacing: 0.14em;
        font-size: 0.78rem;
        font-weight: 800;
        color: var(--nn-primary);
    }
    .market-filter { padding: 1rem; }
    .market-filter .form-control,
    .market-filter .form-select { min-height: 48px; border-radius: var(--nn-radius-sm); }
    .market-card { overflow: hidden; height: 100%; }
    .market-card__media {
        height: 190px;
        background: linear-gradient(135deg, var(--nn-primary), #A78BFA 58%, var(--nn-accent));
    }
    .market-card__meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.45rem;
        margin-bottom: 0.75rem;
    }
    .market-card__teacher {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.8rem 0.9rem;
        border-radius: var(--nn-radius-sm);
        background: var(--nn-surface);
        border: var(--nn-border-w) solid var(--nn-border);
    }
    .market-cta {
        background: linear-gradient(135deg, var(--nn-primary), #A78BFA 58%, var(--nn-accent));
    }
</style>

<div class="container py-4">
    <section class="market-hero">
        <div class="row g-4 align-items-center">
            <div class="col-lg-7">
                <div class="market-eyebrow mb-2">Live learning marketplace</div>
                <h1 class="fw-bold mb-3"><i class="bi bi-shop text-primary"></i> Find a Teacher</h1>
                <p class="text-muted fs-5 mb-0">Browse expert-led courses, compare age fit and price instantly, and move from discovery to enrolment without friction.</p>
            </div>
            <div class="col-lg-5">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="glass-panel p-3 h-100">
                            <div class="market-eyebrow mb-2">Published</div>
                            <div class="display-6 fw-bold" style="color:var(--nn-text);">{{ $courses->total() }}</div>
                            <div class="small" style="color:var(--nn-text-muted);">available course results in this search</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="glass-panel p-3 h-100">
                            <div class="market-eyebrow mb-2">Subjects</div>
                            <div class="display-6 fw-bold" style="color:var(--nn-text);">{{ $subjects->count() }}</div>
                            <div class="small" style="color:var(--nn-text-muted);">topics currently represented by teachers</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('status') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    {{-- Filters --}}
    <form method="GET" class="market-filter mb-4">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <input type="text" name="q" class="form-control" placeholder="Search courses, subjects..." value="{{ request('q') }}">
            </div>
            <div class="col-md-2">
                <select name="subject" class="form-select">
                    <option value="">All Subjects</option>
                    @foreach($subjects as $subj)
                        <option value="{{ $subj }}" {{ request('subject') === $subj ? 'selected' : '' }}>{{ $subj }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="level" class="form-select">
                    <option value="">All Levels</option>
                    @foreach(['beginner','intermediate','advanced'] as $lvl)
                        <option value="{{ $lvl }}" {{ request('level') === $lvl ? 'selected' : '' }}>{{ ucfirst($lvl) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" name="age" class="form-control" placeholder="Child age" value="{{ request('age') }}" min="0" max="18">
            </div>
            <div class="col-md-1">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="free" value="1" id="freeOnly" {{ request('free') ? 'checked' : '' }}>
                    <label class="form-check-label" for="freeOnly">Free</label>
                </div>
            </div>
            <div class="col-md-1">
                <button class="btn btn-primary w-100"><i class="bi bi-search"></i></button>
            </div>
        </div>
    </form>

    {{-- Results --}}
    @if($courses->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-search display-3 text-muted d-block mb-3"></i>
            <p class="text-muted">No courses found. Try a different search.</p>
        </div>
    @else
    <div class="row g-4">
        @foreach($courses as $course)
        <div class="col-md-6 col-lg-4">
            <div class="market-card">
                @if($course->thumbnail)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($course->thumbnail) }}" class="w-100 market-card__media" style="object-fit:cover;" alt="{{ $course->title }}">
                @else
                    <div class="market-card__media d-flex align-items-center justify-content-center">
                        <i class="bi bi-book-half text-white" style="font-size:3rem"></i>
                    </div>
                @endif
                <div class="p-4 d-flex flex-column h-100">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        @if($course->subject)
                            <span class="badge" style="background:var(--nn-primary-soft);color:var(--nn-primary);border:1px solid var(--nn-border);">{{ $course->subject }}</span>
                        @endif
                        @if($course->price > 0)
                            <span class="fw-bold" style="color:var(--nn-success);">${{ $course->price }}</span>
                        @else
                            <span class="badge" style="background:var(--nn-success);">Free</span>
                        @endif
                    </div>
                    <h5 class="card-title fw-semibold mt-2" style="color:var(--nn-text);">{{ $course->title }}</h5>
                    <p class="card-text small" style="color:var(--nn-text-muted);">{{ Str::limit($course->description, 110) }}</p>
                    <div class="market-card__meta small">
                        @if($course->age_min || $course->age_max)
                            <span class="badge" style="background:var(--nn-primary-soft);color:var(--nn-primary);border:1px solid var(--nn-border);">Ages {{ $course->age_min ?? '?' }}–{{ $course->age_max ?? '?' }}</span>
                        @endif
                        <span class="badge" style="background:var(--nn-primary-soft);color:var(--nn-primary);border:1px solid var(--nn-border);">{{ ucfirst($course->level) }}</span>
                        <span class="badge" style="background:var(--nn-primary-soft);color:var(--nn-primary);border:1px solid var(--nn-border);">{{ $course->active_enrollments_count ?? 0 }} enrolled</span>
                    </div>
                    <div class="market-card__teacher mb-3 mt-auto">
                        <img src="https://api.dicebear.com/7.x/bottts/svg?seed={{ $course->teacher_id }}" style="width:42px;height:42px;border-radius:50%" alt="teacher avatar">
                        <div>
                            <div class="fw-semibold small">{{ $course->teacher->name ?? 'Teacher' }}</div>
                            <div class="text-muted small">Instructor</div>
                        </div>
                    </div>
                    <a href="{{ route('marketplace.show', $course->slug) }}" class="btn btn-primary stretched-link">
                        View Course <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-4">{{ $courses->links() }}</div>
    @endif

    {{-- CTA for teachers --}}
    <div class="market-cta mt-5 border-0 text-white">
        <div class="card-body text-center py-5">
            <h3 class="fw-bold">Are you a teacher?</h3>
            <p>Create your own courses and reach students worldwide. Set your own schedule and prices.</p>
            <a href="{{ route('register') }}?role=Teacher" class="btn btn-light btn-lg">
                <i class="bi bi-person-video3"></i> Register as Teacher
            </a>
        </div>
    </div>
</div>
@endsection
