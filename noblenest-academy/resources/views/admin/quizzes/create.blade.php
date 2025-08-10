@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Add Quiz</h1>
    <form action="{{ route('admin.quizzes.store') }}" method="POST">
        @csrf
        @include('admin.quizzes._form', ['quiz' => null, 'modules' => $modules])
        <button type="submit" class="btn btn-success">Create</button>
        <a href="{{ route('admin.quizzes.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection

