@extends('layouts.admin')
@section('content')
<div class="container">
    <h1>Quizzes</h1>
    <a href="{{ route('admin.quizzes.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-violet-600 text-white hover:bg-violet-700 mb-3">Add Quiz</a>
    @if(session('success'))
        <div class="flex items-start gap-3 p-4 rounded-lg border bg-emerald-50 border-emerald-200 text-emerald-800">{{ session('success') }}</div>
    @endif
    <table class="w-full text-sm border-collapse border border-gray-200 table-striped-tw">
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
                    <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm bg-amber-500 text-gray-900 hover:bg-amber-600">Edit</a>
                    <form action="{{ route('admin.quizzes.destroy', $quiz) }}" method="POST" style="display:inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm bg-red-600 text-white hover:bg-red-700" onclick="return confirm('Delete this quiz?')">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $quizzes->links() }}
</div>
@endsection

