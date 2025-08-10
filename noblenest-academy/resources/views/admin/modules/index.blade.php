@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Modules</h1>
    <a href="{{ route('admin.modules.create') }}" class="btn btn-primary mb-3">Add Module</a>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Title</th>
                <th>Course</th>
                <th>Order</th>
                <th>Activities</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($modules as $module)
            <tr>
                <td>{{ $module->title }}</td>
                <td>{{ $module->course->title ?? '-' }}</td>
                <td>{{ $module->order }}</td>
                <td>{{ $module->activities->count() }}</td>
                <td>
                    <a href="{{ route('admin.modules.edit', $module) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('admin.modules.destroy', $module) }}" method="POST" style="display:inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this module?')">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $modules->links() }}
</div>
@endsection

