@extends('layouts.teacher')

@section('title', 'Edit Profile')

@section('content')
<div class="container py-5">
    <div class="flex flex-wrap justify-center">
        <div class="lg:w-7/12">
            <div class="flex items-center gap-3 mb-4">
                <h1 class="h3 font-bold mb-0">Edit Teacher Profile</h1>
                <a href="{{ route('teacher.profile.show') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed border-2 border-gray-300 text-gray-700 hover:bg-gray-100 ms-auto">← Back</a>
            </div>

            @if($errors->any())
            <div class="flex items-start gap-3 p-4 rounded-lg border bg-red-50 border-red-200 text-red-800">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
            @endif

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0">
                <div class="p-5 p-4">
                    <form method="POST" action="{{ route('teacher.profile.update') }}" enctype="multipart/form-data">
                        @csrf @method('PUT')

                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Display Name</label>
                            <input type="text" name="display_name" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" value="{{ old('display_name', $teacherProfile->display_name ?? auth()->user()->name) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Bio</label>
                            <textarea name="bio" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" rows="4" placeholder="Tell students about yourself, your qualifications, and teaching style...">{{ old('bio', $teacherProfile->bio ?? '') }}</textarea>
                        </div>

                        <div class="flex flex-wrap gap-3 mb-3">
                            <div class="md:w-6/12">
                                <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Country</label>
                                <input type="text" name="country" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" value="{{ old('country', $teacherProfile->country ?? '') }}" placeholder="e.g. United Kingdom">
                            </div>
                            <div class="md:w-6/12">
                                <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Languages Taught</label>
                                <input type="text" name="languages" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" value="{{ old('languages', $teacherProfile->languages ?? '') }}" placeholder="e.g. English, French">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Subjects / Specialties</label>
                            <input type="text" name="subjects" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" value="{{ old('subjects', $teacherProfile->subjects ?? '') }}" placeholder="e.g. Early Literacy, STEM, Music">
                        </div>

                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Qualifications / Certifications</label>
                            <textarea name="qualifications" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" rows="3" placeholder="PGCE, TEFL, MA Education...">{{ old('qualifications', $teacherProfile->qualifications ?? '') }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Profile Photo</label>
                            <input type="file" name="photo" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" accept="image/*">
                            <div class="mt-1 text-sm text-[var(--color-text-muted)]">JPEG or PNG, max 2MB.</div>
                        </div>

                        <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-violet-600 text-white hover:bg-violet-700 w-full font-bold">Save Profile</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
