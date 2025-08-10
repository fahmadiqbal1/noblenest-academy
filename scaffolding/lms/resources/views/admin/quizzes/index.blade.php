@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Quizzes</h1>
    <a href="{{ route('admin.quizzes.create') }}" class="btn btn-primary mb-3">Add Quiz</a>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Title</th>
                <th>Module</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($quizzes as $quiz)
            <tr>
                <td>{{ $quiz->title }}</td>
                <td>{{ $quiz->module->title ?? '-' }}</td>
                <td>{{ $quiz->description }}</td>
                <td>
                    <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('admin.quizzes.destroy', $quiz) }}" method="POST" style="display:inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this quiz?')">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $quizzes->links() }}
</div>
@endsection

