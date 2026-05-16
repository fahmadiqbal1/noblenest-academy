@extends('layouts.marketing')

@section('title', 'About — Noble Nest Global Academy')
@section('meta_description', 'Family-first online learning academy for ages 0–10. Built on Japanese, Chinese, and Scandinavian early-childhood pedagogy, multilingual, AI-supported, COPPA-compliant.')

@section('content')
<div class="max-w-3xl mx-auto py-12 space-y-10">
    <header class="text-center space-y-3">
        <p class="text-xs font-semibold tracking-widest uppercase text-[var(--color-primary)]">About</p>
        <h1 class="text-4xl font-bold text-[var(--color-text)] font-[var(--font-display)]">Family-first learning, built thoughtfully.</h1>
        <p class="text-[var(--color-text-muted)] max-w-xl mx-auto">
            Noble Nest Academy gives parents a single, calm place to grow their child's
            curiosity, confidence, and character — from their first words to their first
            lines of code.
        </p>
    </header>

    <section class="space-y-3">
        <h2 class="text-2xl font-bold">What guides us</h2>
        <ul class="space-y-2 text-[var(--color-text)]">
            <li>• <strong>Play-based.</strong> Every activity has a step-by-step interactive player. The empty "you're ready to begin" placeholder is gone.</li>
            <li>• <strong>Multilingual + RTL.</strong> 8 supported locales: English, French, Russian, Mandarin, Spanish, Korean, Urdu, Arabic.</li>
            <li>• <strong>Privacy-first.</strong> COPPA + GDPR-K compliant. Parental consent is recorded, audited, and revocable.</li>
            <li>• <strong>Accessibility-first.</strong> Tailwind v4 + Lucide icons, keyboard nav, screen-reader announcers, <code>prefers-reduced-motion</code> honoured.</li>
            <li>• <strong>Cross-cultural pedagogy.</strong> Japanese empathy + group, Chinese structured arts, Scandinavian outdoor play, Islamic Quran + Arabic — woven into the curriculum.</li>
        </ul>
    </section>

    <section class="space-y-3">
        <h2 class="text-2xl font-bold">Who we serve</h2>
        <p class="text-[var(--color-text)]">
            Parents of children aged 0–6 looking for evidence-based daily activities,
            and families of 7–10-year-olds ready to explore STEM through Blockly, Python,
            and AI literacy. Schools and childcare centres can license per seat.
        </p>
    </section>

    <section class="space-y-3">
        <h2 class="text-2xl font-bold">Get in touch</h2>
        <p class="text-[var(--color-text)]">
            Questions, partnership ideas, accreditation enquiries —
            <a href="{{ url('/contact') }}" class="text-violet-600 hover:underline">say hello</a>.
        </p>
    </section>
</div>
@endsection
