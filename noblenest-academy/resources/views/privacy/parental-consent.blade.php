@extends('layouts.parent')

@section('title', 'Parental Consent')

@section('content')
<div class="max-w-2xl mx-auto py-8 space-y-6">
    <header class="space-y-2 text-center">
        <p class="text-xs font-semibold tracking-widest uppercase text-[var(--color-primary)]">COPPA + GDPR-K</p>
        <h1 class="text-3xl font-bold text-[var(--color-text)] font-[var(--font-display)]">Parental Consent for {{ $child->name }}</h1>
        <p class="text-[var(--color-text-muted)]">
            Children under 13 need verified parental consent before using Noble Nest Academy.
            Please review what we collect, why, and confirm consent below.
        </p>
    </header>

    <x-ui.card padding="lg" class="space-y-4">
        <h2 class="text-xl font-bold">What we collect</h2>
        <ul class="list-disc list-inside space-y-1 text-sm text-[var(--color-text)]">
            <li>{{ $child->name }}'s first name and age tier (NOT their full date of birth).</li>
            <li>Activity completions, time on task, badges earned.</li>
            <li>Drawing / tracing artwork your child saves (stored locally first; only synced if you choose).</li>
            <li>Quiz answers and assessment responses for the child.</li>
        </ul>

        <h2 class="text-xl font-bold pt-2">What we do NOT collect</h2>
        <ul class="list-disc list-inside space-y-1 text-sm text-[var(--color-text)]">
            <li>Photos, voice recordings, or video unless you explicitly upload them.</li>
            <li>Location data.</li>
            <li>Targeted advertising profiles.</li>
        </ul>

        <h2 class="text-xl font-bold pt-2">Your rights</h2>
        <p class="text-sm text-[var(--color-text-muted)]">
            You can export or delete every piece of data we hold for {{ $child->name }} at any time from your
            <a href="{{ route('privacy.dashboard') }}" class="text-violet-600 hover:underline">Privacy dashboard</a>.
        </p>
    </x-ui.card>

    <form method="POST" action="{{ route('privacy.parental-consent.store', ['child' => $child->id]) }}"
          class="space-y-4">
        @csrf
        <div class="space-y-3">
            <label class="flex items-start gap-3">
                <input type="checkbox" name="agree_terms" value="1" required
                       class="mt-1 w-5 h-5 rounded border-gray-300 text-violet-600 focus:ring-violet-500">
                <span class="text-sm">
                    I have read and agree to the
                    <a href="{{ url('/terms') }}" target="_blank" class="text-violet-600 hover:underline">Terms of Service</a>.
                </span>
            </label>
            <label class="flex items-start gap-3">
                <input type="checkbox" name="agree_privacy" value="1" required
                       class="mt-1 w-5 h-5 rounded border-gray-300 text-violet-600 focus:ring-violet-500">
                <span class="text-sm">
                    I have read and agree to the
                    <a href="{{ url('/privacy') }}" target="_blank" class="text-violet-600 hover:underline">Privacy Policy</a>.
                </span>
            </label>
            <label class="flex items-start gap-3">
                <input type="checkbox" name="agree_coppa" value="1" required
                       class="mt-1 w-5 h-5 rounded border-gray-300 text-violet-600 focus:ring-violet-500">
                <span class="text-sm">
                    I am {{ $child->name }}'s parent or legal guardian and I consent to the collection
                    of {{ $child->name }}'s data as described above.
                </span>
            </label>
        </div>

        <div class="flex flex-col sm:flex-row gap-2 justify-between items-center pt-2">
            <a href="{{ route('privacy.dashboard') }}"
               class="text-sm text-[var(--color-text-muted)] hover:underline">
                ← Back to privacy dashboard
            </a>
            <x-ui.button type="submit" variant="primary" size="lg" icon="badge-check">
                Record Consent
            </x-ui.button>
        </div>
    </form>

    <p class="text-xs text-center text-[var(--color-text-muted)]">
        Your IP and browser will be logged with this consent timestamp for COPPA record-keeping.
    </p>
</div>
@endsection
