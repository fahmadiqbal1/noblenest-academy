@extends('layouts.marketing')

@section('title', 'Noble Nest Academy for Schools')

@section('content')
{{-- Hero --}}
<section class="bg-[var(--color-primary)] text-white py-5">
    <div class="container py-3 text-center">
        <h1 class="text-3xl font-bold mb-3">Learning Without Borders</h1>
        <p class="text-lg leading-relaxed mb-4">Bring Noble Nest Academy to your school, NGO, or community center. Bulk pricing, multilingual support, and dedicated onboarding.</p>
        <a href="#inquiry" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-amber-500 text-gray-900 hover:bg-amber-600 px-5 py-3 text-lg">Request a Demo</a>
    </div>
</section>

{{-- Stats --}}
<section class="py-5 bg-gray-50">
    <div class="container">
        <div class="flex flex-wrap text-center gap-4">
            <div class="md:w-3/12">
                <div class="h2 font-bold text-[var(--color-primary)]">150+</div>
                <div class="text-[var(--color-text-muted)]">Countries reached</div>
            </div>
            <div class="md:w-3/12">
                <div class="h2 font-bold text-[var(--color-primary)]">8</div>
                <div class="text-[var(--color-text-muted)]">Languages supported</div>
            </div>
            <div class="md:w-3/12">
                <div class="h2 font-bold text-[var(--color-primary)]">500+</div>
                <div class="text-[var(--color-text-muted)]">Activities for ages 0–12</div>
            </div>
            <div class="md:w-3/12">
                <div class="h2 font-bold text-[var(--color-primary)]">97%</div>
                <div class="text-[var(--color-text-muted)]">Parent satisfaction rate</div>
            </div>
        </div>
    </div>
</section>

{{-- Features --}}
<section class="py-5">
    <div class="container">
        <h2 class="text-center font-bold mb-5">Built for Educators</h2>
        <div class="flex flex-wrap gap-4">
            <div class="md:w-4/12">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0 h-full p-4">
                    <div class="text-4xl mb-3">🎓</div>
                    <h5>Curriculum-Aligned</h5>
                    <p class="text-[var(--color-text-muted)]">Activities mapped to developmental milestones and school curricula across 20+ countries.</p>
                </div>
            </div>
            <div class="md:w-4/12">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0 h-full p-4">
                    <div class="text-4xl mb-3">📊</div>
                    <h5>Classroom Analytics</h5>
                    <p class="text-[var(--color-text-muted)]">Track engagement, completion rates, and developmental progress for every student.</p>
                </div>
            </div>
            <div class="md:w-4/12">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0 h-full p-4">
                    <div class="text-4xl mb-3">💰</div>
                    <h5>Scholarship Program</h5>
                    <p class="text-[var(--color-text-muted)]">We offer free access to qualifying low-income schools and NGOs. Apply today.</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Inquiry Form --}}
<section id="inquiry" class="py-5 bg-gray-50">
    <div class="container">
        <div class="flex flex-wrap justify-center">
            <div class="md:w-7/12">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0 p-4">
                    <h3 class="font-bold mb-1">Get in Touch</h3>
                    <p class="text-[var(--color-text-muted)] mb-4">Fill in the form and our team will respond within 24 hours.</p>

                    @if(session('status'))
                    <div class="flex items-start gap-3 p-4 rounded-lg border bg-emerald-50 border-emerald-200 text-emerald-800">{{ session('status') }}</div>
                    @endif

                    @if($errors->any())
                    <div class="flex items-start gap-3 p-4 rounded-lg border bg-red-50 border-red-200 text-red-800">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('school-inquiry.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">School / Organization Name</label>
                            <input type="text" name="school_name" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 @error('school_name') is-invalid @enderror" value="{{ old('school_name') }}" required>
                        </div>
                        <div class="flex flex-wrap gap-3 mb-3">
                            <div class="md:w-6/12">
                                <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Contact Name</label>
                                <input type="text" name="contact_name" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 @error('contact_name') is-invalid @enderror" value="{{ old('contact_name') }}" required>
                            </div>
                            <div class="md:w-6/12">
                                <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Contact Email</label>
                                <input type="email" name="contact_email" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 @error('contact_email') is-invalid @enderror" value="{{ old('contact_email') }}" required>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-3 mb-3">
                            <div class="md:w-6/12">
                                <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Country</label>
                                <input type="text" name="country" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 @error('country') is-invalid @enderror" value="{{ old('country') }}" required>
                            </div>
                            <div class="md:w-6/12">
                                <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Number of Students</label>
                                <select name="student_count" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 @error('student_count') is-invalid @enderror">
                                    <option value="">Select range</option>
                                    <option value="1-50">1–50</option>
                                    <option value="50-200">50–200</option>
                                    <option value="200-1000">200–1000</option>
                                    <option value="1000+">1000+</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1 font-semibold">Message (optional)</label>
                            <textarea name="message" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500" rows="3" placeholder="Tell us about your needs, goals, or questions">{{ old('message') }}</textarea>
                        </div>
                        <div class="flex items-center gap-2 mb-3">
                            <input class="w-4 h-4 rounded border-gray-300 text-violet-600 focus:ring-violet-500" type="checkbox" name="scholarship_interest" id="scholarship_interest" value="1">
                            <label class="text-sm" for="scholarship_interest">
                                We are interested in the scholarship program
                            </label>
                        </div>
                        <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-violet-600 text-white hover:bg-violet-700 w-full">Send Inquiry</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
