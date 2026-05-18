@extends('layouts.marketing')

@section('meta_title', 'NobleNest Global Academy | Family-First Learning Platform')
@section('meta_description', 'Explore NobleNest Global Academy: a family-first learning platform with adaptive courses, onboarding guidance, and AI support for parents, students, teachers, and admins.')
@section('meta_image', asset('og-home.png'))

@section('content')

{{-- ============================================================
     HERO
     ============================================================ --}}
<section aria-label="Hero" class="relative overflow-hidden rounded-[var(--radius-card)] border-[3px] border-[var(--color-border)] shadow-[var(--shadow-clay)] bg-gradient-to-br from-[var(--color-brand-50)] via-white to-[var(--color-accent-50)] p-8 lg:p-12 mb-12">

  {{-- Decorative background glow --}}
  <div aria-hidden="true" class="pointer-events-none absolute -end-16 -bottom-20 w-80 h-80 rounded-full bg-[radial-gradient(circle,rgba(124,58,237,0.10),transparent_70%)]"></div>

  <div class="grid lg:grid-cols-[1fr_auto] gap-10 items-center">

    {{-- Left: copy + CTAs --}}
    <div>
      <p class="text-xs font-extrabold uppercase tracking-widest text-[var(--color-primary)] font-[var(--font-display)] mb-3">{{ __('marketing.home_eyebrow') }}</p>

      <h1 class="text-4xl lg:text-5xl font-bold text-[var(--color-text)] font-[var(--font-display)] leading-tight mb-4">
        {{ I18n::get('home_title') }}
      </h1>

      <p class="text-xl font-bold tracking-widest mb-4" aria-hidden="true">🎨 📖 🔬 🎵 🧩 ✍️</p>

      <p class="text-lg text-[var(--color-text-muted)] mb-8 max-w-lg">{{ I18n::get('home_subtitle') }}</p>

      <div class="flex flex-wrap gap-3">
        <x-ui.button variant="primary" size="lg" as="button" type="button" onclick="openAIModal()">
          {{ I18n::get('get_weekly_plan') }}
        </x-ui.button>

        @auth
          @if(auth()->user()->role === 'Admin')
            <x-ui.button variant="secondary" size="lg" href="{{ route('admin.courses.index') }}">
              {{ I18n::get('manage_courses') }}
            </x-ui.button>
          @else
            <x-ui.button variant="secondary" size="lg" href="{{ route('onboarding') }}" icon="arrow-right">
              {{ __('marketing.home_get_started') }}
            </x-ui.button>
          @endif
        @else
          <x-ui.button variant="secondary" size="lg" href="{{ route('register') }}" icon="user">
            {{ __('marketing.home_register_free') }}
          </x-ui.button>
        @endauth
      </div>

      {{-- Metric pills --}}
      <div class="grid grid-cols-3 gap-4 mt-8">
        <div class="bg-white/80 border-[2px] border-[var(--color-border)] rounded-[var(--radius-card)] p-4 transition-transform duration-[var(--duration-base)] hover:-translate-y-1 focus-within:-translate-y-1 shadow-[var(--shadow-clay)]">
          <p class="text-xs font-extrabold uppercase tracking-widest text-[var(--color-primary)] font-[var(--font-display)] mb-1">{{ __('marketing.home_coverage') }}</p>
          <p class="text-3xl font-bold text-[var(--color-text)] font-[var(--font-display)] leading-none">1200+</p>
          <p class="text-xs text-[var(--color-text-muted)] mt-1">{{ __('marketing.home_coverage_desc') }}</p>
        </div>
        <div class="bg-white/80 border-[2px] border-[var(--color-border)] rounded-[var(--radius-card)] p-4 transition-transform duration-[var(--duration-base)] hover:-translate-y-1 shadow-[var(--shadow-clay)]">
          <p class="text-xs font-extrabold uppercase tracking-widest text-[var(--color-primary)] font-[var(--font-display)] mb-1">{{ __('marketing.home_languages') }}</p>
          <p class="text-3xl font-bold text-[var(--color-text)] font-[var(--font-display)] leading-none">{{ count(\App\Helpers\I18n::availableLanguages()) }}</p>
          <p class="text-xs text-[var(--color-text-muted)] mt-1">{{ __('marketing.home_languages_desc') }}</p>
        </div>
        <div class="bg-white/80 border-[2px] border-[var(--color-border)] rounded-[var(--radius-card)] p-4 transition-transform duration-[var(--duration-base)] hover:-translate-y-1 shadow-[var(--shadow-clay)]">
          <p class="text-xs font-extrabold uppercase tracking-widest text-[var(--color-primary)] font-[var(--font-display)] mb-1">{{ __('marketing.home_ai_loop') }}</p>
          <p class="text-3xl font-bold text-[var(--color-text)] font-[var(--font-display)] leading-none">{{ __('marketing.home_ai_loop_value') }}</p>
          <p class="text-xs text-[var(--color-text-muted)] mt-1">{{ __('marketing.home_ai_loop_desc') }}</p>
        </div>
      </div>
    </div>

    {{-- Right: orbital brand mark --}}
    <div class="hidden lg:flex items-center justify-center relative w-80 h-80 shrink-0" aria-hidden="true">
      {{-- Dashed ring --}}
      <div class="absolute inset-[7%] rounded-full border-[2px] border-dashed border-[var(--color-brand-200)]"></div>

      {{-- Core circle --}}
      <div class="relative z-10 w-64 h-64 rounded-full bg-white/94 border-[2px] border-[var(--color-border)] shadow-[var(--shadow-clay)] flex flex-col items-center justify-center text-center p-8">
        <img src="{{ asset('brand/noblenest-logo.svg') }}" alt="" class="w-24 h-24 rounded-[var(--radius-sm)] shadow-[var(--shadow-clay)] mb-4">
        <p class="text-xs font-extrabold uppercase tracking-widest text-[var(--color-primary)] font-[var(--font-display)] mb-1">{{ __('marketing.home_brand_name') }}</p>
        <p class="text-xs text-[var(--color-text-muted)]">{{ __('marketing.home_brand_tagline') }}</p>
      </div>

      {{-- Orbit dots --}}
      @foreach([
        ['top-4 start-8',   'translate'],
        ['top-12 end-0',    'robot'],
        ['bottom-8 start-2','star'],
        ['bottom-2 end-10', 'book-open'],
      ] as $dot)
      <div class="absolute {{ $dot[0] }} w-14 h-14 rounded-full bg-white border-[2px] border-[var(--color-border)] shadow-[var(--shadow-clay)] flex items-center justify-center text-[var(--color-primary)] transition-transform duration-[var(--duration-base)] hover:scale-110">
        <x-ui.icon name="{{ $dot[1] }}" class="w-6 h-6" />
      </div>
      @endforeach
    </div>

  </div>
</section>

{{-- ============================================================
     ROLE-SPECIFIC DASHBOARDS (authenticated)
     ============================================================ --}}
@php
  $user = Auth::user();
  $role = $user->role ?? null;
@endphp

@if($user && $role === 'Admin')
<section aria-label="Admin Dashboard" class="mb-10">
  @php
    $courseCount   = \App\Models\Course::count();
    $activityCount = \App\Models\Activity::count();
    $userCount     = \App\Models\User::where('role', '!=', 'Admin')->count();
    $jobCount      = \App\Models\AIJob::where('status', 'queued')->count();
    $pendingJobs   = \App\Models\AIJob::where('moderation_status', 'pending')->where('status', 'completed')->count();
  @endphp

  <h2 class="text-xl font-bold text-[var(--color-text)] mb-4 flex items-center gap-2">
    <x-ui.icon name="settings" class="w-5 h-5 text-[var(--color-primary)]" />
    {{ __('marketing.home_admin_dashboard') }}
  </h2>

  <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <a href="{{ route('admin.courses.index') }}" class="block focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2 rounded-[var(--radius-card)]">
      <x-ui.card variant="clay" padding="md" class="text-center">
        <p class="text-3xl font-bold text-[var(--color-primary)]">{{ $courseCount }}</p>
        <p class="text-sm text-[var(--color-text-muted)] mt-1">{{ __('marketing.home_courses') }}</p>
      </x-ui.card>
    </a>
    <a href="/admin/activities" class="block focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2 rounded-[var(--radius-card)]">
      <x-ui.card variant="clay" padding="md" class="text-center">
        <p class="text-3xl font-bold text-emerald-700">{{ $activityCount }}</p>
        <p class="text-sm text-[var(--color-text-muted)] mt-1">{{ __('marketing.home_activities') }}</p>
      </x-ui.card>
    </a>
    <a href="{{ route('admin.analytics.index') }}" class="block focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2 rounded-[var(--radius-card)]">
      <x-ui.card variant="clay" padding="md" class="text-center">
        <p class="text-3xl font-bold text-blue-600">{{ $userCount }}</p>
        <p class="text-sm text-[var(--color-text-muted)] mt-1">{{ __('marketing.home_users') }}</p>
      </x-ui.card>
    </a>
    <a href="{{ route('admin.orchestrator.index') }}" class="block focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2 rounded-[var(--radius-card)]">
      <x-ui.card variant="clay" padding="md" class="text-center">
        <p class="text-3xl font-bold {{ $pendingJobs > 0 ? 'text-amber-600' : 'text-[var(--color-text-muted)]' }}">{{ $jobCount + $pendingJobs }}</p>
        <p class="text-sm text-[var(--color-text-muted)] mt-1">{{ __('marketing.home_ai_jobs') }}{{ $pendingJobs > 0 ? ' ('.__('marketing.home_ai_jobs_need_review', ['count' => $pendingJobs]).')' : '' }}</p>
      </x-ui.card>
    </a>
  </div>

  <div class="flex flex-wrap gap-2">
    <x-ui.button variant="primary" href="{{ route('admin.orchestrator.index') }}" icon="star">{{ __('marketing.home_ai_orchestrator') }}</x-ui.button>
    <x-ui.button variant="secondary" href="{{ route('admin.analytics.index') }}">{{ __('marketing.home_analytics') }}</x-ui.button>
    <x-ui.button variant="secondary" href="{{ route('admin.courses.create') }}" icon="plus">{{ __('marketing.home_new_course') }}</x-ui.button>
    <x-ui.button variant="ghost" href="{{ route('admin.curriculum') }}">{{ __('marketing.home_curriculum_map') }}</x-ui.button>
  </div>
</section>
@endif

@if($user && $role === 'Parent')
@php $children = \App\Models\ChildProfile::where('parent_id', $user->id)->get(); @endphp
<section aria-label="Parent Dashboard" class="mb-10">
  <div class="grid md:grid-cols-2 gap-6">
    <x-ui.card variant="clay" padding="md">
      <h2 class="text-lg font-bold text-[var(--color-text)] mb-4 flex items-center gap-2">
        <x-ui.icon name="users" class="w-5 h-5 text-[var(--color-primary)]" />
        {{ __('marketing.home_my_children') }}
      </h2>
      @forelse($children as $child)
        <div class="flex items-center justify-between py-2 px-3 rounded-[var(--radius-sm)] bg-[var(--color-surface-strong)] mb-2">
          <div class="flex items-center gap-2">
            <x-ui.avatar :name="$child->name" size="sm" />
            <div>
              <p class="font-semibold text-sm text-[var(--color-text)]">{{ $child->name }}</p>
              <p class="text-xs text-[var(--color-text-muted)]">{{ $child->age_display ?? __('marketing.home_age_unknown') }} · {{ strtoupper($child->preferred_language ?? 'en') }}</p>
            </div>
          </div>
          <x-ui.button variant="ghost" size="sm" href="{{ route('children.edit', $child) }}" icon="edit">
            <span class="sr-only">{{ __('marketing.home_edit_child', ['name' => $child->name]) }}</span>
          </x-ui.button>
        </div>
      @empty
        <p class="text-sm text-[var(--color-text-muted)]">{{ __('marketing.home_no_children') }}</p>
      @endforelse
      <div class="flex gap-2 mt-4">
        <x-ui.button variant="secondary" size="sm" href="{{ route('children.create') }}" icon="plus">{{ __('marketing.home_add_child') }}</x-ui.button>
        <x-ui.button variant="ghost" size="sm" href="{{ route('children.index') }}">{{ __('marketing.home_manage_all') }}</x-ui.button>
      </div>
    </x-ui.card>

    <x-ui.card variant="clay" padding="md">
      <h2 class="text-lg font-bold text-[var(--color-text)] mb-4 flex items-center gap-2">
        <x-ui.icon name="bell" class="w-5 h-5 text-blue-500" />
        {{ __('marketing.home_whats_new') }}
      </h2>
      <ul class="space-y-2 text-sm text-[var(--color-text)]">
        <li class="flex items-start gap-2"><x-ui.icon name="star" class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" /> {{ __('marketing.home_new_stem') }}</li>
        <li class="flex items-start gap-2"><x-ui.icon name="book-open" class="w-4 h-4 text-[var(--color-primary)] shrink-0 mt-0.5" /> {{ __('marketing.home_new_korean') }}</li>
        <li class="flex items-start gap-2"><x-ui.icon name="heart" class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" /> {{ __('marketing.home_new_invite') }}</li>
        <li class="flex items-start gap-2"><x-ui.icon name="star" class="w-4 h-4 text-blue-500 shrink-0 mt-0.5" /> {{ __('marketing.home_new_ai_plans') }}</li>
      </ul>
      <x-ui.button variant="secondary" size="sm" href="{{ route('onboarding') }}" class="mt-4" icon="settings">{{ __('marketing.home_update_preferences') }}</x-ui.button>
    </x-ui.card>
  </div>
</section>
@endif

@if($user && ($role === 'Student' || $role === 'Child'))
<section aria-label="Student Welcome" class="mb-10">
  <x-ui.card variant="gradient" padding="lg" class="text-center">
    <h2 class="text-2xl font-bold text-[var(--color-text)] mb-3 font-[var(--font-display)]">{{ __('marketing.home_welcome_name', ['name' => $user->name]) }}</h2>
    <x-ui.badge tone="warning" class="mb-4">{{ __('marketing.home_keep_going') }}</x-ui.badge>
    <div class="mt-4">
      <x-ui.button variant="primary" size="lg" href="{{ route('activities.index') }}" icon="play">{{ __('marketing.home_start_learning') }}</x-ui.button>
    </div>
  </x-ui.card>
</section>
@endif

{{-- Subscription status --}}
@if(Auth::check())
  @php
    $subscription = null;
    try {
      if (class_exists(\App\Models\Subscription::class)) {
        $subscription = \App\Models\Subscription::where('user_id', Auth::id())
          ->where('active', true)
          ->where('ends_at', '>', now())
          ->first();
      }
    } catch (\Throwable $e) { }
  @endphp
  @if($subscription)
    <x-ui.alert tone="success" class="mb-8">
      {{ __('marketing.home_subscription_active') }} <strong>{{ optional($subscription->ends_at)->format('F j, Y') }}</strong>.
    </x-ui.alert>
  @else
    <x-ui.alert tone="warn" class="mb-8">
      {{ __('marketing.home_no_subscription') }}
      <x-ui.button variant="primary" size="sm" href="/checkout" class="ms-2">{{ __('marketing.home_subscribe_now') }}</x-ui.button>
    </x-ui.alert>
  @endif
@endif

{{-- Platform highlights (shown to guests and parents) --}}
@if(!$user || in_array($role, ['Parent', 'Admin', null]))
<x-ui.section title="Platform highlights" subtitle="Built to feel alive, guided, and role-aware" class="mb-10">
  <div class="grid md:grid-cols-3 gap-6">
    <x-ui.card variant="clay" padding="md" class="text-center">
      <div class="w-14 h-14 rounded-[var(--radius-sm)] bg-[var(--color-brand-50)] text-[var(--color-primary)] flex items-center justify-center mx-auto mb-4">
        <x-ui.icon name="users" class="w-7 h-7" />
      </div>
      <h3 class="font-bold text-[var(--color-text)] mb-2">Parent Academy</h3>
      <p class="text-sm text-[var(--color-text-muted)] mb-3">{{ I18n::get('parent_academy_desc') }}</p>
      <x-ui.badge tone="neutral">6 courses · 57 modules</x-ui.badge>
    </x-ui.card>

    <x-ui.card variant="clay" padding="md" class="text-center">
      <div class="w-14 h-14 rounded-[var(--radius-sm)] bg-[var(--color-brand-50)] text-[var(--color-primary)] flex items-center justify-center mx-auto mb-4">
        <x-ui.icon name="heart" class="w-7 h-7" />
      </div>
      <h3 class="font-bold text-[var(--color-text)] mb-2">Early Years (0–6)</h3>
      <p class="text-sm text-[var(--color-text-muted)] mb-3">{{ I18n::get('early_years_desc') }}</p>
      <x-ui.badge tone="neutral">1200+ activities · 72 monthly units</x-ui.badge>
    </x-ui.card>

    <x-ui.card variant="clay" padding="md" class="text-center">
      <div class="w-14 h-14 rounded-[var(--radius-sm)] bg-[var(--color-brand-50)] text-[var(--color-primary)] flex items-center justify-center mx-auto mb-4">
        <x-ui.icon name="star" class="w-7 h-7" />
      </div>
      <h3 class="font-bold text-[var(--color-text)] mb-2">STEM (7–10)</h3>
      <p class="text-sm text-[var(--color-text-muted)] mb-3">{{ I18n::get('stem_desc') }}</p>
      <x-ui.badge tone="neutral">Robotics · Coding · Web</x-ui.badge>
    </x-ui.card>
  </div>
</x-ui.section>
@endif

{{-- ============================================================
     MARKETING SECTIONS (guest only)
     ============================================================ --}}
@guest

{{-- Social proof --}}
<x-ui.section aria-label="Social proof" class="mb-10">
  <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
    @foreach([
      ['value' => '50K+',  'label' => 'Children Learning',  'color' => 'text-[var(--color-primary)]'],
      ['value' => '120+',  'label' => 'Countries',           'color' => 'text-violet-600'],
      ['value' => '4.9★', 'label' => 'Parent Rating',       'color' => 'text-[var(--color-accent-700)]'],
      ['value' => '8',     'label' => 'Languages',           'color' => 'text-emerald-700'],
    ] as $stat)
    <x-ui.card variant="clay" padding="md">
      <p class="text-3xl font-bold {{ $stat['color'] }} font-[var(--font-display)]">{{ $stat['value'] }}</p>
      <p class="text-sm text-[var(--color-text-muted)] mt-1">{{ $stat['label'] }}</p>
    </x-ui.card>
    @endforeach
  </div>
</x-ui.section>

{{-- How It Works --}}
<x-ui.section title="Start in 3 Simple Steps" class="mb-10">
  <div class="grid md:grid-cols-3 gap-4">
    @foreach([
      ['icon' => 'edit',        'step' => '1', 'title' => 'Create Account',  'desc' => 'Sign up in 30 seconds. No credit card to start.'],
      ['icon' => 'users',       'step' => '2', 'title' => 'Add Your Child',   'desc' => 'Date of birth → instant age-adapted curriculum.'],
      ['icon' => 'arrow-right', 'step' => '3', 'title' => 'Start Learning',   'desc' => '7 free activities daily. Upgrade anytime.'],
    ] as $step)
    <x-ui.card variant="clay" padding="md" class="flex gap-4 items-start">
      <div class="w-14 h-14 rounded-[var(--radius-sm)] bg-[var(--color-brand-50)] text-[var(--color-primary)] flex items-center justify-center shrink-0">
        <x-ui.icon name="{{ $step['icon'] }}" class="w-7 h-7" />
      </div>
      <div>
        <p class="font-bold text-[var(--color-text)]">{{ $step['title'] }}</p>
        <p class="text-sm text-[var(--color-text-muted)] mt-1">{{ $step['desc'] }}</p>
      </div>
    </x-ui.card>
    @endforeach
  </div>
</x-ui.section>

{{-- Testimonials --}}
<x-ui.section title="What Families Say" class="mb-10">
  <div class="grid md:grid-cols-3 gap-4">
    @foreach([
      ['quote' => 'My daughter went from refusing to read to asking for more activities every day. NobleNest changed everything.', 'author' => 'Amara O.', 'country' => 'Nigeria', 'stars' => 5],
      ['quote' => 'Finally a platform that works in Arabic and adapts to my 3-year-old\'s pace. Incredible!', 'author' => 'Fatima A.', 'country' => 'Pakistan', 'stars' => 5],
      ['quote' => 'The teacher marketplace is fantastic. My son\'s tutor is from Indonesia and he loves the sessions.', 'author' => 'Li Wei', 'country' => 'China', 'stars' => 5],
    ] as $t)
    <x-ui.card variant="clay" padding="md">
      <div class="flex gap-0.5 mb-3" role="img" aria-label="{{ $t['stars'] }} stars">
        @for($i = 0; $i < $t['stars']; $i++)
          <x-ui.icon name="star" class="w-4 h-4 text-amber-400 fill-amber-400" />
        @endfor
      </div>
      <blockquote class="text-sm text-[var(--color-text)] italic mb-4">"{{ $t['quote'] }}"</blockquote>
      <footer>
        <p class="font-bold text-sm text-[var(--color-text)]">{{ $t['author'] }}</p>
        <p class="text-xs text-[var(--color-text-muted)]">{{ $t['country'] }}</p>
      </footer>
    </x-ui.card>
    @endforeach
  </div>
</x-ui.section>

{{-- Final CTA --}}
<section aria-label="Call to action" class="rounded-[var(--radius-card)] bg-gradient-to-br from-[var(--color-brand-600)] to-[var(--color-brand-400)] shadow-[var(--shadow-clay)] text-white text-center py-16 px-8 mb-8">
  <h2 class="text-3xl font-bold font-[var(--font-display)] mb-3">Start Your Child's Journey Today</h2>
  <p class="text-white/85 mb-8 text-lg">7 free activities. No card required. Cancel anytime.</p>
  <div class="flex flex-wrap gap-4 justify-center">
    <x-ui.button variant="secondary" size="lg" href="{{ route('register') }}" iconRight="arrow-right">
      Get Started Free
    </x-ui.button>
    <x-ui.button
      size="lg"
      href="{{ route('pricing') }}"
      class="bg-white/15 border-white/30 text-white hover:bg-white/25"
    >
      View Pricing
    </x-ui.button>
  </div>
</section>

@endguest

@endsection
