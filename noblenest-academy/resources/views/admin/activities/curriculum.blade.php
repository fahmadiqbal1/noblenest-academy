@extends('layouts.app')
@section('content')
<div class="container py-4">
    <h1 class="mb-4 text-primary">Curriculum Mapping</h1>
    <p class="mb-3">Assign activities to subjects and age groups. Drag activities between subjects or use the dropdowns.</p>
    <form class="row g-3 mb-4">
        <div class="col-md-3">
            <label class="form-label">Filter by Age</label>
            <select class="form-select" name="age" onchange="this.form.submit()">
                <option value="">All</option>
                @for($i=0;$i<=10;$i++)
                    <option value="{{ $i }}" @if(request('age')==$i) selected @endif>{{ $i }}</option>
                @endfor
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Filter by Subject</label>
            <input type="text" class="form-control" name="subject" value="{{ request('subject') }}" placeholder="Subject name...">
        </div>
        <div class="col-md-3 align-self-end">
            <button class="btn btn-outline-primary">Filter</button>
        </div>
    </form>
    <div class="row g-4">
        @foreach($subjects as $subject => $activities)
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-info text-white fw-bold">{{ ucfirst($subject) }}</div>
                <div class="card-body">
                    <ul class="list-group mb-3 curriculum-draggable" id="subject-{{ Str::slug($subject) }}" data-subject="{{ $subject }}">
                        @foreach($activities as $activity)
                        <li class="list-group-item d-flex justify-content-between align-items-center" data-activity-id="{{ $activity->id }}">
                            <span>{{ $activity->title }}</span>
                            <form method="POST" action="{{ route('admin.curriculum.remove') }}" style="display:inline-block;">
                                @csrf
                                <input type="hidden" name="activity_id" value="{{ $activity->id }}">
                                <input type="hidden" name="subject" value="{{ $subject }}">
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-x"></i></button>
                            </form>
                        </li>
                        @endforeach
                    </ul>
                    <form method="POST" action="{{ route('admin.curriculum.add') }}" class="d-flex gap-2">
                        @csrf
                        <select name="activity_id" class="form-select form-select-sm">
                            <option value="">Add activity...</option>
                            @foreach($allActivities as $a)
                                @if($a->subject !== $subject)
                                    <option value="{{ $a->id }}">{{ $a->title }}</option>
                                @endif
                            @endforeach
                        </select>
                        <input type="hidden" name="subject" value="{{ $subject }}">
                        <button class="btn btn-sm btn-success">Add</button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.curriculum-draggable').forEach(function(list) {
        new Sortable(list, {
            group: 'activities',
            animation: 150,
            onAdd: function (evt) {
                const activityId = evt.item.getAttribute('data-activity-id');
                const subject = evt.to.getAttribute('data-subject');
                fetch('{{ route("admin.curriculum.dragAssign") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ activity_id: activityId, subject: subject })
                });
            },
            onRemove: function (evt) {
                const activityId = evt.item.getAttribute('data-activity-id');
                const subject = evt.from.getAttribute('data-subject');
                fetch('{{ route("admin.curriculum.dragRemove") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ activity_id: activityId, subject: subject })
                });
            }
        });
    });
});
</script>
@endpush
