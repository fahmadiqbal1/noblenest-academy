@extends('layouts.admin')

@section('title', 'Style guide — x-ui.* component library')

@section('content')
<div class="max-w-5xl mx-auto py-6 space-y-12">

    <header class="space-y-2 border-b border-gray-200 pb-6">
        <p class="text-xs font-semibold tracking-widest uppercase text-[var(--color-primary)]">Noble Nest Academy</p>
        <h1 class="text-3xl font-bold text-[var(--color-text)] font-[var(--font-display)]">x-ui.* style guide</h1>
        <p class="text-[var(--color-text-muted)] max-w-2xl">
            Visual regression baseline for the Tailwind v4 + <code>x-ui.*</code> component library that replaced
            <code>playful.css</code>, the four <code>tier-*.css</code> overlays, and every Bootstrap utility
            class in Phase 1. Every component variant is rendered below so we can eyeball-diff after future changes.
        </p>
        <p class="text-xs text-[var(--color-text-muted)]">Admin-only route — see <code>routes/web.php</code> <code>/_styleguide</code>.</p>
    </header>

    <section id="tokens" class="space-y-4">
        <h2 class="text-xl font-bold">Design tokens</h2>
        <p class="text-sm text-[var(--color-text-muted)]">Live samples bound to CSS variables defined in <code>resources/css/app.css</code> <code>@theme</code> block. If a swatch goes wrong, the token went wrong.</p>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            @foreach([
                'brand-50','brand-100','brand-300','brand-500','brand-600','brand-700',
            ] as $shade)
                <div class="rounded-lg border border-gray-200 overflow-hidden">
                    <div class="h-16" style="background: var(--color-{{ $shade }})"></div>
                    <div class="px-2 py-1 text-xs font-mono">{{ $shade }}</div>
                </div>
            @endforeach
        </div>
        <div class="grid grid-cols-3 gap-3 pt-2">
            <div class="rounded-lg border border-gray-200 p-3 text-sm">
                <p class="font-display font-bold text-lg">Baloo 2</p>
                <p class="text-xs text-[var(--color-text-muted)]">Display / kids surfaces — <code>var(--font-display)</code></p>
            </div>
            <div class="rounded-lg border border-gray-200 p-3 text-sm" style="font-family: var(--font-body)">
                <p class="font-bold text-lg">Nunito</p>
                <p class="text-xs text-[var(--color-text-muted)]">Body / parent — <code>var(--font-body)</code></p>
            </div>
            <div class="rounded-lg border border-gray-200 p-3 text-sm" style="font-family: var(--font-sans)">
                <p class="font-bold text-lg">Inter</p>
                <p class="text-xs text-[var(--color-text-muted)]">Admin / teacher — <code>var(--font-sans)</code></p>
            </div>
        </div>
    </section>

    <section id="buttons" class="space-y-4">
        <h2 class="text-xl font-bold">Buttons — <code>&lt;x-ui.button&gt;</code></h2>
        <div class="flex flex-wrap items-center gap-3">
            <x-ui.button variant="primary">Primary</x-ui.button>
            <x-ui.button variant="secondary">Secondary</x-ui.button>
            <x-ui.button variant="ghost">Ghost</x-ui.button>
            <x-ui.button variant="danger">Danger</x-ui.button>
            <x-ui.button variant="success">Success</x-ui.button>
            <x-ui.button variant="outline">Outline</x-ui.button>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <x-ui.button variant="primary" size="sm">Small</x-ui.button>
            <x-ui.button variant="primary">Default</x-ui.button>
            <x-ui.button variant="primary" size="lg">Large</x-ui.button>
            <x-ui.button variant="primary" icon="plus">With icon</x-ui.button>
            <x-ui.button variant="primary" disabled>Disabled</x-ui.button>
        </div>
    </section>

    <section id="cards" class="space-y-4">
        <h2 class="text-xl font-bold">Cards — <code>&lt;x-ui.card&gt;</code></h2>
        <div class="grid sm:grid-cols-3 gap-4">
            <x-ui.card padding="md">
                <h3 class="font-bold mb-1">Default</h3>
                <p class="text-sm text-[var(--color-text-muted)]">Standard surface card with default padding.</p>
            </x-ui.card>
            @isset($component)
            @endisset
            <x-ui.card variant="clay" padding="md">
                <h3 class="font-bold mb-1">Clay variant</h3>
                <p class="text-sm text-[var(--color-text-muted)]">Used on kids surfaces.</p>
            </x-ui.card>
            <x-ui.card padding="lg">
                <h3 class="font-bold mb-1">Large padding</h3>
                <p class="text-sm text-[var(--color-text-muted)]">For hero / landing card.</p>
            </x-ui.card>
        </div>
    </section>

    <section id="badges-alerts" class="space-y-4">
        <h2 class="text-xl font-bold">Badges + alerts</h2>
        <div class="flex flex-wrap items-center gap-2">
            <x-ui.badge>Default</x-ui.badge>
            <x-ui.badge variant="primary">Primary</x-ui.badge>
            <x-ui.badge variant="success">Success</x-ui.badge>
            <x-ui.badge variant="warning">Warning</x-ui.badge>
            <x-ui.badge variant="danger">Danger</x-ui.badge>
            <x-ui.badge variant="info">Info</x-ui.badge>
        </div>
        <div class="space-y-3">
            <x-ui.alert variant="info">Informational message — neutral context.</x-ui.alert>
            <x-ui.alert variant="success">Action completed successfully.</x-ui.alert>
            <x-ui.alert variant="warning">Heads-up — review before saving.</x-ui.alert>
            <x-ui.alert variant="danger">Something went wrong; please retry.</x-ui.alert>
        </div>
    </section>

    <section id="form" class="space-y-4">
        <h2 class="text-xl font-bold">Form controls</h2>
        <div class="grid sm:grid-cols-2 gap-4">
            <x-ui.field label="Full name" name="full_name" placeholder="Ada Lovelace" />
            <x-ui.field label="Email" name="email" type="email" placeholder="you@example.com" />
            <x-ui.field label="Disabled" name="disabled" disabled value="Locked value" />
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="bio">Bio</label>
                <x-ui.textarea name="bio" id="bio" rows="3" placeholder="Tell us about yourself..." />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="role">Role</label>
                <x-ui.select name="role" id="role" :options="['Parent' => 'Parent', 'Teacher' => 'Teacher', 'Student' => 'Student']" />
            </div>
            <div class="space-y-2">
                <x-ui.checkbox name="news" label="Subscribe to newsletter" />
                <x-ui.checkbox name="terms" label="I agree to the terms" checked />
                <x-ui.radio name="plan" value="monthly" label="Monthly" />
                <x-ui.radio name="plan" value="yearly" label="Yearly" />
                <x-ui.switch name="notify" label="Email notifications" />
            </div>
        </div>
    </section>

    <section id="icons" class="space-y-4">
        <h2 class="text-xl font-bold">Icons — <code>&lt;x-ui.icon&gt;</code></h2>
        <p class="text-sm text-[var(--color-text-muted)]">Registry of 127 Lucide icons (see <code>app/View/Components/Ui/Icon.php</code>). Sample below shows the full set; legacy <code>bi bi-*</code> usages are mapped per <code>docs/phase1-launch/icon-migration.md</code>.</p>
        <div class="grid grid-cols-6 sm:grid-cols-10 gap-3">
            @foreach(\App\View\Components\Ui\Icon::$registry as $name => $_)
                <div class="flex flex-col items-center gap-1 text-center" title="{{ $name }}">
                    <x-ui.icon :name="$name" class="w-6 h-6 text-[var(--color-primary)]" />
                    <span class="text-[10px] text-[var(--color-text-muted)] font-mono break-all">{{ $name }}</span>
                </div>
            @endforeach
        </div>
    </section>

    <section id="data" class="space-y-4">
        <h2 class="text-xl font-bold">Data displays</h2>
        <div class="grid sm:grid-cols-3 gap-4">
            <x-ui.stat label="Active children" value="248" hint="+12 this week" />
            <x-ui.stat label="Activities played" value="1,402" hint="across 24 hours" />
            <x-ui.stat label="Streak" value="7d" hint="best: 14d" />
        </div>
        <x-ui.progress :value="62" />
        <x-ui.skeleton class="h-8 w-full" />
    </section>

    <section id="navigation" class="space-y-4">
        <h2 class="text-xl font-bold">Navigation</h2>
        <x-ui.tabs :tabs="['Overview' => 'overview', 'Activity' => 'activity', 'Settings' => 'settings']" active="overview" />
        <p class="text-sm text-[var(--color-text-muted)]">See the active tab style above.</p>
    </section>

    <section id="empty" class="space-y-4">
        <h2 class="text-xl font-bold">Empty state</h2>
        <x-ui.empty-state
            title="No activities yet"
            description="Pick an age tier from the sidebar to start the day."
            icon="sparkles" />
    </section>

    <section id="stroke-tracer" class="space-y-4">
        <h2 class="text-xl font-bold">Stroke engine (Phase 2 deterministic tracer)</h2>
        <p class="text-sm text-[var(--color-text-muted)]">
            Renders the target glyph as a faint guide, captures pointer input on a canvas
            overlay, and validates each user stroke against the expected path
            (start-endpoint check + average distance score). Stroke-by-stroke advancement
            with RTL mirror for Arabic / Urdu. Seed glyph set lives in
            <code>resources/views/components/stroke-tracer.blade.php</code> —
            extend the <code>GLYPHS</code> object with more letters / digits / scripts.
        </p>
        <div class="flex flex-wrap items-start gap-4 justify-center">
            <div class="space-y-1 text-center">
                <x-stroke-tracer glyph="A" />
                <p class="text-xs text-[var(--color-text-muted)] font-mono">Latin A · 2 strokes</p>
            </div>
            <div class="space-y-1 text-center">
                <x-stroke-tracer glyph="C" />
                <p class="text-xs text-[var(--color-text-muted)] font-mono">Latin C · 1 stroke</p>
            </div>
            <div class="space-y-1 text-center">
                <x-stroke-tracer glyph="0" />
                <p class="text-xs text-[var(--color-text-muted)] font-mono">Digit 0 · 1 stroke</p>
            </div>
            <div class="space-y-1 text-center">
                <x-stroke-tracer glyph="ar:ا" />
                <p class="text-xs text-[var(--color-text-muted)] font-mono">Arabic ا · RTL</p>
            </div>
            <div class="space-y-1 text-center">
                <x-stroke-tracer glyph="ar:ب" />
                <p class="text-xs text-[var(--color-text-muted)] font-mono">Arabic ب · RTL · 2 strokes</p>
            </div>
        </div>
    </section>

</div>
@endsection
