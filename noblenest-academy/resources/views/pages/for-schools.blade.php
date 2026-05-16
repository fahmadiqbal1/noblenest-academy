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
        </div>
    </div>
</section>

{{-- Inquiry — coming soon notice (Phase 7 will rebuild this) --}}
<section id="inquiry" class="py-5 bg-gray-50">
    <div class="container">
        <div class="flex flex-wrap justify-center">
            <div class="md:w-7/12">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-0 p-4 text-center">
                    <h3 class="font-bold mb-2">Institutional Licensing — Coming Soon</h3>
                    <p class="text-[var(--color-text-muted)] mb-4">Our school and NGO partnership programme is being rebuilt. In the meantime, please reach out by email and our team will get back to you within 48 hours.</p>
                    <a href="mailto:schools@noblenest.academy?subject=Noble%20Nest%20Academy%20for%20Schools" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition bg-violet-600 text-white hover:bg-violet-700">Email schools@noblenest.academy</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
