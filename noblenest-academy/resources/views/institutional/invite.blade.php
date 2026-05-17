@extends('layouts.marketing')

@section('title', 'School Admin Invitation')

@section('content')
<div class="max-w-md mx-auto py-12">
    <h1 class="text-2xl font-bold mb-2">You're invited: {{ $invite->school_name }}</h1>
    <p class="text-sm text-gray-600 mb-6">Complete signup to accept your {{ $invite->seats }}-seat license as the school administrator.</p>

    <form method="POST" action="{{ route('institutional.invite.accept', ['token' => $invite->invite_token]) }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium">Email</label>
            <input type="email" value="{{ $invite->email }}" disabled class="mt-1 block w-full border rounded p-2 bg-gray-50">
        </div>
        <div>
            <label class="block text-sm font-medium" for="name">Your name</label>
            <input id="name" name="name" type="text" required class="mt-1 block w-full border rounded p-2">
        </div>
        <div>
            <label class="block text-sm font-medium" for="password">Password</label>
            <input id="password" name="password" type="password" required minlength="8" class="mt-1 block w-full border rounded p-2">
        </div>
        <div>
            <label class="block text-sm font-medium" for="password_confirmation">Confirm password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8" class="mt-1 block w-full border rounded p-2">
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Accept invitation</button>
    </form>
</div>
@endsection
