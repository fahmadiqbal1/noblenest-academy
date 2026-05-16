@extends('layouts.marketing')

@section('title', 'Contact — Noble Nest Global Academy')
@section('meta_description', 'Get in touch with Noble Nest Academy — support, partnerships, school enquiries, press, accreditation.')

@section('content')
<div class="max-w-2xl mx-auto py-12 space-y-8">
    <header class="text-center space-y-2">
        <p class="text-xs font-semibold tracking-widest uppercase text-[var(--color-primary)]">Contact</p>
        <h1 class="text-4xl font-bold text-[var(--color-text)] font-[var(--font-display)]">Say hello.</h1>
        <p class="text-[var(--color-text-muted)] max-w-md mx-auto">
            We read every message. Pick the channel that fits.
        </p>
    </header>

    <div class="grid sm:grid-cols-2 gap-4">
        <x-ui.card padding="md" class="space-y-2">
            <h2 class="font-bold flex items-center gap-2"><x-ui.icon name="mail" /> Email</h2>
            <p class="text-sm"><a class="text-violet-600 hover:underline" href="mailto:hello@noblenest.example">hello@noblenest.example</a></p>
            <p class="text-xs text-[var(--color-text-muted)]">Support · partnerships · press</p>
        </x-ui.card>
        <x-ui.card padding="md" class="space-y-2">
            <h2 class="font-bold flex items-center gap-2"><x-ui.icon name="shield-check" /> Privacy + COPPA</h2>
            <p class="text-sm"><a class="text-violet-600 hover:underline" href="mailto:privacy@noblenest.example">privacy@noblenest.example</a></p>
            <p class="text-xs text-[var(--color-text-muted)]">Data-export, deletion, parental consent issues</p>
        </x-ui.card>
        <x-ui.card padding="md" class="space-y-2">
            <h2 class="font-bold flex items-center gap-2"><x-ui.icon name="award" /> Schools + districts</h2>
            <p class="text-sm"><a class="text-violet-600 hover:underline" href="mailto:schools@noblenest.example">schools@noblenest.example</a></p>
            <p class="text-xs text-[var(--color-text-muted)]">Volume licensing, SSO, analytics</p>
        </x-ui.card>
        <x-ui.card padding="md" class="space-y-2">
            <h2 class="font-bold flex items-center gap-2"><x-ui.icon name="file-text" /> Accreditation</h2>
            <p class="text-sm"><a class="text-violet-600 hover:underline" href="mailto:accreditation@noblenest.example">accreditation@noblenest.example</a></p>
            <p class="text-xs text-[var(--color-text-muted)]">Cognia · BAC · regional ministries</p>
        </x-ui.card>
    </div>

    <p class="text-center text-xs text-[var(--color-text-muted)]">
        We respond within two business days. For child-safety concerns, please email <strong>privacy@noblenest.example</strong> with the subject line "URGENT".
    </p>
</div>
@endsection
