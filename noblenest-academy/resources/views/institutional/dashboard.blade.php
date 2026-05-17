@extends('layouts.marketing')

@section('title', 'School Admin Dashboard')

@section('content')
<div class="max-w-3xl mx-auto py-12">
    <h1 class="text-3xl font-bold mb-6">School Administrator</h1>

    @if($invite)
        <div class="rounded border p-4 mb-6">
            <p class="text-sm text-gray-500">School</p>
            <p class="text-xl font-semibold">{{ $invite->school_name }}</p>
            <p class="mt-2 text-sm">Licensed seats: <strong>{{ $invite->seats }}</strong></p>
            <p class="text-sm">Assigned: <strong>{{ $assignedSeats }}</strong></p>
        </div>
    @endif

    <h2 class="text-xl font-semibold mb-2">Assign a seat</h2>
    <form method="POST" action="{{ route('school.seats.assign') }}" class="flex gap-2">
        @csrf
        <input name="email" type="email" required placeholder="student@example.com" class="flex-1 border rounded p-2">
        <button class="bg-blue-600 text-white px-4 py-2 rounded">Assign</button>
    </form>
</div>
@endsection
