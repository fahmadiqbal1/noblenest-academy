@extends('layouts.app')
@section('content')
<div class="container py-4">
    <h1 class="mb-4 text-primary">Manage Activities</h1>
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <form class="d-flex gap-2" method="GET">
            <input type="text" name="q" class="form-control" placeholder="Search title or skill..." value="{{ request('q') }}">
            <select name="type" class="form-select">
                <option value="">All Types</option>
                <option value="video">Video</option>
                <option value="tracing">Tracing</option>
                <option value="drawing">Drawing</option>
                <option value="puzzle">Puzzle</option>
                <option value="quiz">Quiz</option>
            </select>
            <button class="btn btn-outline-primary">Filter</button>
        </form>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addActivityModal"><i class="bi bi-plus-circle"></i> Add Activity</button>
    </div>
    <table class="table table-hover table-bordered align-middle bg-white">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Type</th>
                <th>Skill</th>
                <th>Age</th>
                <th>Language</th>
                <th>Media</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($activities as $activity)
            <tr>
                <td>{{ $activity->id }}</td>
                <td>{{ $activity->title }}</td>
                <td>{{ ucfirst($activity->type) }}</td>
                <td>{{ $activity->skill }}</td>
                <td>{{ $activity->age_min }}–{{ $activity->age_max }}</td>
                <td>{{ $activity->language }}</td>
                <td>
                    @if($activity->media_url)
                        <a href="{{ $activity->media_url }}" target="_blank">View</a>
                    @endif
                </td>
                <td>
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editActivityModal{{ $activity->id }}"><i class="bi bi-pencil"></i></button>
                    <form method="POST" action="/admin/activities/{{ $activity->id }}" style="display:inline-block;" onsubmit="return confirm('Delete this activity?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
            <!-- Edit Modal -->
            <div class="modal fade" id="editActivityModal{{ $activity->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST" action="/admin/activities/{{ $activity->id }}" enctype="multipart/form-data">
                            @csrf @method('PUT')
                            <div class="modal-header"><h5 class="modal-title">Edit Activity</h5></div>
                            <div class="modal-body">
                                @include('admin.activities.partials.form', ['activity' => $activity])
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </tbody>
    </table>
    {{ $activities->links() }}
    <!-- Add Modal -->
    <div class="modal fade" id="addActivityModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="/admin/activities" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header"><h5 class="modal-title">Add Activity</h5></div>
                    <div class="modal-body">
                        @include('admin.activities.partials.form', ['activity' => null])
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="mt-4">
        <form method="POST" action="/admin/activities/bulk-upload" enctype="multipart/form-data" class="d-inline-block">
            @csrf
            <label class="form-label">Bulk Upload (CSV/Excel):</label>
            <input type="file" name="file" class="form-control d-inline-block w-auto" required>
            <button class="btn btn-outline-info">Upload</button>
        </form>
    </div>
</div>
@endsection

