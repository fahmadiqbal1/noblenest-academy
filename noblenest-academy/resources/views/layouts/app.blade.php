@php
    $lang = session('lang', auth()->user()->preferred_language ?? 'en');
    $rtlLangs = ['ar', 'ur'];
    $dir = in_array($lang, $rtlLangs) ? 'rtl' : 'ltr';
    $user = auth()->user();
    $role = $user->role ?? null;
    $theme = session('theme', $role === 'Parent' ? 'professional' : ($role === 'Student' ? 'playful' : 'professional'));
    $isPlayful = $theme === 'playful';
    if (!class_exists('I18n')) { class_alias(\App\Helpers\I18n::class, 'I18n'); }
    $metaTitle = trim($__env->yieldContent('meta_title')) ?: 'NobleNest Global Academy';
    $metaDescription = trim($__env->yieldContent('meta_description')) ?: 'Family-first learning with courses, onboarding, and AI guidance in one beautifully designed academy experience.';
    $metaImage = trim($__env->yieldContent('meta_image')) ?: asset('og-image.png');
    $metaUrl = url()->current();
@endphp
<!doctype html>
<html lang="{{ $lang }}" dir="{{ $dir }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $metaTitle }}</title>
    <meta name="application-name" content="NobleNest Global Academy">
    <meta name="description" content="{{ $metaDescription }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="NobleNest Global Academy">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ $metaUrl }}">
    <meta property="og:image" content="{{ $metaImage }}">
    <meta property="og:image:alt" content="NobleNest Global Academy brand preview">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    <meta name="twitter:image" content="{{ $metaImage }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="msapplication-config" content="{{ asset('browserconfig.xml') }}">
    <meta name="msapplication-TileColor" content="#0d5c63">
    <meta name="theme-color" content="#7C3AED">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="{{ asset('css/playful.css') }}" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    @stack('styles')
    <style>
        :root {
            --nn-bg: #FFFBF0;
            --nn-surface: rgba(255, 255, 255, 0.88);
            --nn-surface-strong: #ffffff;
            --nn-text: #1E1B4B;
            --nn-text-muted: #6B7280;
            --nn-border: rgba(124, 58, 237, 0.12);
            --nn-primary: #7C3AED;
            --nn-primary-soft: rgba(124, 58, 237, 0.12);
            --nn-accent: #F59E0B;
            --nn-danger: #F43F5E;
            --nn-success: #10B981;
            --nn-shadow: 6px 6px 14px rgba(124, 58, 237, 0.08), -2px -2px 8px rgba(255, 255, 255, 0.9), inset -2px -2px 6px rgba(255, 255, 255, 0.7);
            --nn-radius: 22px;
            --nn-radius-sm: 14px;
            --nn-border-w: 3px;
            --nn-bounce: cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        body {
            min-height: 100vh;
            color: var(--nn-text);
            background: linear-gradient(180deg, #F5F0FF 0%, #FFFBF0 50%, #FFF7ED 100%);
            font-family: 'Nunito', 'Comic Neue', sans-serif;
            font-weight: 400;
        }
        h1, h2, h3, h4, h5, h6, .navbar-brand, .brand-grad {
            font-family: 'Baloo 2', 'Fredoka', sans-serif;
            letter-spacing: -0.01em;
        }
        .brand-grad {
            background: linear-gradient(120deg, #7C3AED 0%, #A78BFA 45%, #F59E0B 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .brand-lockup {
            display: inline-flex;
            align-items: center;
            gap: 0.9rem;
        }
        .brand-lockup__logo {
            width: 52px;
            height: 52px;
            border-radius: var(--nn-radius-sm);
            box-shadow: var(--nn-shadow);
        }
        .brand-lockup__text {
            display: flex;
            flex-direction: column;
            line-height: 1;
            gap: 0.18rem;
        }
        .brand-lockup__title {
            font-size: 1.25rem;
        }
        .brand-lockup__subtitle {
            font-family: 'Comic Neue', sans-serif;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--nn-text-muted);
        }
        .glass-panel {
            background: var(--nn-surface);
            border: var(--nn-border-w) solid var(--nn-border);
            border-radius: var(--nn-radius);
            box-shadow: var(--nn-shadow);
        }
        .navbar {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            border-bottom: var(--nn-border-w) solid var(--nn-border) !important;
            box-shadow: 0 4px 16px rgba(30, 41, 59, 0.06);
            border-radius: 0;
        }
        .nav-link {
            color: var(--nn-text-muted) !important;
            font-weight: 700;
            font-family: 'Baloo 2', sans-serif;
            font-size: 0.92rem;
            border-radius: var(--nn-radius-sm);
            padding: 0.4rem 0.75rem !important;
            transition: all 0.2s ease;
        }
        .nav-link:hover, .nav-link:focus {
            color: var(--nn-primary) !important;
            background: var(--nn-primary-soft);
        }
        .nav-link.active {
            color: var(--nn-primary) !important;
            background: var(--nn-primary-soft);
        }
        .dropdown-menu {
            border: 2px solid var(--nn-border);
            border-radius: var(--nn-radius-sm);
            box-shadow: 0 8px 24px rgba(30,41,59,0.10);
            padding: 0.4rem;
        }
        .dropdown-item {
            border-radius: 10px;
            padding: 0.5rem 0.8rem;
            font-family: 'Baloo 2', sans-serif;
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--nn-text-muted);
            transition: background 0.15s, color 0.15s;
        }
        .dropdown-item:hover, .dropdown-item:focus {
            background: var(--nn-primary-soft);
            color: var(--nn-primary);
        }
        .dropdown-item.active {
            background: var(--nn-primary-soft);
            color: var(--nn-primary);
        }
        .dropdown-divider { opacity: 0.08; margin: 0.2rem 0; }
        .btn-primary {
            background: linear-gradient(135deg, #7C3AED, #A78BFA);
            border: var(--nn-border-w) solid #7C3AED;
            border-radius: var(--nn-radius-sm);
            box-shadow: var(--nn-shadow);
            font-family: 'Baloo 2', sans-serif;
            font-weight: 700;
            transition: all 0.25s var(--nn-bounce);
        }
        .btn-primary:hover, .btn-primary:focus {
            background: linear-gradient(135deg, #6D28D9, #7C3AED);
            border-color: #6D28D9;
            transform: translateY(-2px);
        }
        .btn-primary:active { transform: scale(0.96); }
        .btn-outline-secondary, .btn-outline-info, .btn-outline-success, .btn-outline-primary, .btn-outline-danger {
            border-width: var(--nn-border-w);
            border-radius: var(--nn-radius-sm);
            font-family: 'Baloo 2', sans-serif;
            font-weight: 700;
        }
        .avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            object-fit: cover;
            border: var(--nn-border-w) solid rgba(255,255,255,0.9);
            box-shadow: var(--nn-shadow);
        }
        .app-main { position: relative; z-index: 1; }
        .bottom-nav {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(16px);
            border-top: var(--nn-border-w) solid var(--nn-border);
            z-index: 1030;
            display: flex;
            justify-content: space-around;
            align-items: center;
            padding: 0.55rem 0;
            box-shadow: 0 -4px 16px rgba(30, 41, 59, 0.06);
        }
        .bottom-nav a {
            color: var(--nn-text-muted);
            font-size: 1.35rem;
            text-align: center;
            flex: 1;
            transition: color 0.2s ease, transform 0.2s var(--nn-bounce);
        }
        .bottom-nav a.active, .bottom-nav a:hover {
            color: var(--nn-primary);
            transform: translateY(-3px);
        }
        .footer-panel {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border-color: var(--nn-border) !important;
        }
        .footer-brand { display: inline-flex; align-items: center; gap: 0.75rem; }
        .footer-brand img { width: 40px; height: 40px; border-radius: var(--nn-radius-sm); }
        .footer-brand__stack { display: flex; flex-direction: column; line-height: 1.15; gap: 0.1rem; }
        .footer-brand__name { font-weight: 800; color: var(--nn-text); }
        .footer-brand__tagline { font-size: 0.82rem; color: var(--nn-text-muted); }
        .footer-links a { color: var(--nn-text-muted); }
        .footer-links a:hover { color: var(--nn-primary); }

        /* AI assistant */
        .assistant-shell {
            background: linear-gradient(180deg, rgba(255,255,255,0.96), rgba(238,242,255,0.96));
            backdrop-filter: blur(20px);
        }
        .assistant-eyebrow {
            display: inline-flex; align-items: center; gap: 0.4rem;
            margin-bottom: 0.35rem; color: var(--nn-primary);
            font-size: 0.75rem; font-weight: 800; letter-spacing: 0.1em; text-transform: uppercase;
        }
        .assistant-chat {
            min-height: 260px; max-height: 420px; overflow-y: auto;
            padding: 1rem; border-radius: var(--nn-radius);
            background: linear-gradient(180deg, rgba(255,255,255,0.92), rgba(238,242,255,0.88));
            border: var(--nn-border-w) solid var(--nn-border);
        }
        .assistant-form { display: flex; gap: 0.75rem; align-items: center; }
        .assistant-form__field {
            display: flex; align-items: center; gap: 0.75rem; flex: 1;
            border-radius: var(--nn-radius-sm); padding: 0.75rem 1rem;
            background: rgba(255,255,255,0.92); border: var(--nn-border-w) solid var(--nn-border);
        }
        .assistant-form__field i { color: var(--nn-primary); }
        .assistant-send {
            width: 52px; height: 52px; border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
        }
        .assistant-message { display: flex; margin-bottom: 0.85rem; }
        .assistant-message--user { justify-content: flex-end; }
        .assistant-message__bubble {
            max-width: min(84%, 560px); padding: 0.9rem 1rem;
            border-radius: var(--nn-radius); box-shadow: var(--nn-shadow);
        }
        .assistant-message--user .assistant-message__bubble {
            background: linear-gradient(135deg, #7C3AED, #A78BFA);
            color: #fff; border-bottom-right-radius: 6px;
        }
        .assistant-message--ai .assistant-message__bubble {
            background: rgba(255,255,255,0.96);
            border: var(--nn-border-w) solid var(--nn-border); border-bottom-left-radius: 6px;
        }
        .assistant-message__meta {
            margin-bottom: 0.35rem; color: var(--nn-text-muted);
            font-size: 0.74rem; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase;
        }
        .assistant-suggestions { display: flex; flex-wrap: wrap; gap: 0.5rem; }
        .assistant-chip { border-radius: var(--nn-radius-sm); background: rgba(255,255,255,0.78); }
        #ai-assistant-bubble { position: fixed; bottom: 96px; right: 22px; z-index: 1050; }
        .ai-bubble-launch {
            width: 64px; height: 64px; border: 0;
            border-radius: var(--nn-radius);
            display: inline-flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, #7C3AED, #A78BFA);
            box-shadow: var(--nn-shadow);
            transition: transform 0.25s var(--nn-bounce);
        }
        .ai-bubble-launch:hover { transform: scale(1.08); }
        .ai-bubble-launch img { width: 38px; height: 38px; }
        #ai-bubble-msg {
            max-width: 260px;
            border: var(--nn-border-w) solid var(--nn-border);
            border-radius: var(--nn-radius);
            box-shadow: var(--nn-shadow);
        }
        .app-status {
            border: var(--nn-border-w) solid var(--nn-primary-soft);
            background: var(--nn-primary-soft);
            color: var(--nn-primary);
            border-radius: var(--nn-radius-sm);
            font-size: 0.8rem;
            font-weight: 700;
            padding: 0.55rem 0.9rem;
        }
        @media (max-width: 767.98px) {
            .app-status { display: none; }
            #ai-assistant-bubble { right: 16px; bottom: 84px; }
        }
        @media (prefers-reduced-motion: reduce) {
            .btn-primary, .ai-bubble-launch, .bottom-nav a { transition: none !important; }
            .btn-primary:hover, .ai-bubble-launch:hover { transform: none !important; }
        }
    </style>
</head>
<body class="app-shell">
<nav class="navbar navbar-expand-lg border-bottom sticky-top {{ $isPlayful ? 'playful-font' : 'professional-font' }}">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{ route('noble.home') }}">
            <span class="brand-lockup">
                <img src="{{ asset('brand/noblenest-logo.svg') }}" alt="NobleNest Global Academy logo" class="brand-lockup__logo">
                <span class="brand-lockup__text">
                    <span class="brand-grad brand-lockup__title">NobleNest</span>
                    <span class="brand-lockup__subtitle">Global Academy</span>
                </span>
            </span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('noble.home') ? 'active' : '' }}" href="{{ route('noble.home') }}">
                        <i class="bi bi-house-door-fill me-1"></i>{{ I18n::get('welcome') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('marketplace.*') ? 'active' : '' }}" href="{{ route('marketplace.index') }}">
                        <i class="bi bi-shop me-1"></i>Marketplace
                    </a>
                </li>
                @auth
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('profile') ? 'active' : '' }}" href="/profile">
                            <i class="bi bi-person-circle me-1"></i>{{ I18n::get('profile') }}
                        </a>
                    </li>

                    @if(auth()->user()->role === 'Admin')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}" href="{{ route('admin.courses.index') }}">
                                <i class="bi bi-journal-richtext me-1"></i>{{ I18n::get('admin_courses') }}
                            </a>
                        </li>
                        {{-- Admin tools dropdown --}}
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.users.*','admin.children.*','admin.analytics.*','admin.orchestrator.*','admin.maternal.*','admin.practitioners.*') ? 'active' : '' }}"
                               href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-shield-lock me-1"></i>Admin
                            </a>
                            <ul class="dropdown-menu shadow border-0 rounded-3">
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                                       href="{{ route('admin.users.index') }}">
                                        <i class="bi bi-people me-2"></i>{{ I18n::get('admin_users') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('admin.children.*') ? 'active' : '' }}"
                                       href="{{ route('admin.children.index') }}">
                                        <i class="bi bi-person-hearts me-2"></i>Children
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}"
                                       href="{{ route('admin.analytics.index') }}">
                                        <i class="bi bi-bar-chart-line me-2"></i>Analytics
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('admin.orchestrator.*') ? 'active' : '' }}"
                                       href="{{ route('admin.orchestrator.index') }}">
                                        <i class="bi bi-robot me-2"></i>Orchestrator
                                    </a>
                                </li>
                                @if(config('features.maternal_module'))
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('admin.maternal.*') ? 'active' : '' }}"
                                       href="{{ route('admin.maternal.content.index') }}">
                                        <i class="bi bi-heart-pulse me-2"></i>Maternal Content
                                    </a>
                                </li>
                                @endif
                                @if(config('features.practitioner_portal'))
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('admin.practitioners.*') ? 'active' : '' }}"
                                       href="{{ route('admin.practitioners.index') }}">
                                        <i class="bi bi-shield-check me-2"></i>Practitioners
                                    </a>
                                </li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.courses.create') }}">
                                        <i class="bi bi-plus-circle me-2"></i>{{ I18n::get('add_course') }}
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif

                    @if(auth()->user()->role === 'Parent' || auth()->user()->role === 'Admin')
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('parent.dashboard','children.*','maternal.*') ? 'active' : '' }}"
                               href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-people-fill me-1"></i>Family
                            </a>
                            <ul class="dropdown-menu shadow border-0 rounded-3">
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('parent.dashboard') ? 'active' : '' }}"
                                       href="{{ route('parent.dashboard') }}">
                                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->is('children') ? 'active' : '' }}"
                                       href="/children">
                                        <i class="bi bi-person-hearts me-2"></i>{{ I18n::get('children') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="/children/create">
                                        <i class="bi bi-person-plus me-2"></i>{{ I18n::get('add_child') }}
                                    </a>
                                </li>
                                @if(config('features.maternal_module'))
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('maternal.*') ? 'active' : '' }}"
                                       href="{{ route('maternal.dashboard') }}">
                                        <i class="bi bi-heart-pulse me-2"></i>Maternal Wellness
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    @if(auth()->user()->role === 'Teacher')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}"
                               href="{{ route('teacher.dashboard') }}">
                                <i class="bi bi-speedometer2 me-1"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('teacher.courses.*') ? 'active' : '' }}"
                               href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-book me-1"></i>Courses
                            </a>
                            <ul class="dropdown-menu shadow border-0 rounded-3">
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('teacher.courses.index') ? 'active' : '' }}"
                                       href="{{ route('teacher.courses.index') }}">
                                        <i class="bi bi-collection me-2"></i>My Courses
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('teacher.courses.create') ? 'active' : '' }}"
                                       href="{{ route('teacher.courses.create') }}">
                                        <i class="bi bi-plus-circle me-2"></i>New Course
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif

                    @if(auth()->user()->role === 'Student')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('student.my-courses') ? 'active' : '' }}"
                               href="{{ route('student.my-courses') }}">
                                <i class="bi bi-book-open me-1"></i>My Learning
                            </a>
                        </li>
                    @endif

                    @if(auth()->user()->role === 'Practitioner' && config('features.practitioner_portal'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('practitioner.dashboard') ? 'active' : '' }}"
                               href="{{ route('practitioner.dashboard') }}">
                                <i class="bi bi-speedometer2 me-1"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('practitioner.reviews.*') ? 'active' : '' }}"
                               href="{{ route('practitioner.reviews.index') }}">
                                <i class="bi bi-clipboard-check me-1"></i>Reviews
                            </a>
                        </li>
                    @endif

                    <li class="nav-item">
                        <a class="nav-link text-danger" href="#"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="bi bi-box-arrow-right me-1"></i>{{ I18n::get('logout') }}
                        </a>
                    </li>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                @else
                    <li class="nav-item"><a class="nav-link btn btn-outline-primary btn-sm rounded-pill px-3 me-1" href="/login">{{ I18n::get('login') }}</a></li>
                    <li class="nav-item"><a class="nav-link btn btn-primary btn-sm rounded-pill px-3 text-white" href="/register">{{ I18n::get('register') }}</a></li>
                @endauth
            </ul>
            <div class="d-flex gap-2 align-items-center">
                @auth
                    <img src="https://api.dicebear.com/7.x/bottts/svg?seed={{ auth()->user()->id }}" class="avatar me-2" alt="avatar">
                    <span class="badge rounded-pill px-2 py-1 me-1" style="font-size:0.68rem; background:var(--nn-primary-soft); color:var(--nn-primary); border:1px solid var(--nn-primary);">{{ auth()->user()->role }}</span>
                @endauth
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ strtoupper(session('lang', 'en')) }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        @foreach(App\Helpers\I18n::availableLanguages() as $lang)
                            <li><a class="dropdown-item" href="/lang/{{ $lang }}">{{ strtoupper($lang) }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <span class="theme-toggle btn btn-light border ms-2" onclick="toggleTheme()" title="Switch theme">
                    <i class="bi {{ $isPlayful ? 'bi-palette-fill text-pink' : 'bi-briefcase-fill text-primary' }}"></i>
                </span>
                <button class="btn btn-primary" type="button" onclick="openAIModal()"><i class="bi bi-stars"></i> {{ I18n::get('ai_assistant') }}</button>
            </div>
        </div>
    </div>
</nav>

<main class="app-main py-4 py-lg-5">
    <div class="container">
        @if (session('status'))
            <div class="alert alert-success border-0 shadow-sm">{{ session('status') }}</div>
        @endif
        @if (session('success'))
            <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger border-0 shadow-sm">{{ session('error') }}</div>
        @endif
        @yield('content')
    </div>
</main>

{{-- Wave divider --}}
<div class="wave-divider mt-5" aria-hidden="true">
    <svg viewBox="0 0 1440 80" preserveAspectRatio="none" style="display:block;width:100%;height:60px;">
        <path d="M0,40 C360,80 720,0 1080,40 C1260,60 1380,50 1440,40 L1440,80 L0,80Z" fill="var(--nn-primary, #7C3AED)" opacity="0.07"/>
        <path d="M0,50 C320,10 640,70 960,30 C1200,50 1360,45 1440,50 L1440,80 L0,80Z" fill="var(--nn-primary, #7C3AED)" opacity="0.04"/>
    </svg>
</div>
<footer class="footer-panel py-5 {{ $isPlayful ? 'playful-font' : 'professional-font' }}" style="background:linear-gradient(180deg, rgba(245,240,255,0.5), rgba(255,251,240,0.6));">
    <div class="container">
        <div class="row g-4 align-items-start">
            {{-- Brand column --}}
            <div class="col-md-4 mb-3 mb-md-0">
                <span class="footer-brand">
                    <img src="{{ asset('brand/noblenest-logo.svg') }}" alt="NobleNest Global Academy logo">
                    <span class="footer-brand__stack">
                        <span class="footer-brand__name">NobleNest Global Academy</span>
                        <span class="footer-brand__tagline">Family-first learning, beautifully delivered.</span>
                    </span>
                </span>
                <p class="text-muted small mt-3 mb-0">Adaptive, multilingual early education for families worldwide. 🌍</p>
            </div>
            {{-- Links column --}}
            <div class="col-6 col-md-4">
                <h6 class="fw-bold text-uppercase small mb-3" style="letter-spacing:0.1em;color:var(--nn-primary);">Quick Links</h6>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><a href="{{ route('pricing') }}" class="text-decoration-none text-muted"><i class="bi bi-tag me-1"></i> Pricing</a></li>
                    <li class="mb-2"><a href="/privacy" class="text-decoration-none text-muted"><i class="bi bi-shield-check me-1"></i> Privacy Policy</a></li>
                    <li class="mb-2"><a href="/terms" class="text-decoration-none text-muted"><i class="bi bi-file-earmark-text me-1"></i> Terms of Service</a></li>
                </ul>
            </div>
            {{-- Info column --}}
            <div class="col-6 col-md-4">
                <h6 class="fw-bold text-uppercase small mb-3" style="letter-spacing:0.1em;color:var(--nn-primary);">Get in Touch</h6>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><a href="mailto:support@noblenest.com" class="text-decoration-none text-muted"><i class="bi bi-envelope me-1"></i> Support</a></li>
                    <li class="mb-2"><span class="text-muted"><i class="bi bi-translate me-1"></i> 8 Languages</span></li>
                    <li class="mb-2"><span class="text-muted"><i class="bi bi-heart me-1"></i> COPPA Compliant</span></li>
                </ul>
            </div>
        </div>
        <hr class="my-4" style="opacity:0.08;">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center">
            <p class="text-muted small mb-0">&copy; {{ date('Y') }} NobleNest Global Academy. All rights reserved.</p>
            <div class="d-flex gap-3 mt-2 mt-sm-0">
                <a href="https://wa.me/?text={{ urlencode('🌟 Check out NobleNest Academy – early learning in 8 languages! ' . url('/')) }}" class="text-muted" target="_blank" rel="noopener" title="Share on WhatsApp"><i class="bi bi-whatsapp fs-5"></i></a>
            </div>
        </div>
    </div>
</footer>
@if($isPlayful)
    <nav class="bottom-nav d-lg-none">
        <a href="/activities" class="@if(request()->is('activities*')) active @endif"><i class="bi bi-controller"></i><div style="font-size:0.8rem">Play</div></a>
        <a href="/quizzes" class="@if(request()->is('quizzes*')) active @endif"><i class="bi bi-puzzle"></i><div style="font-size:0.8rem">Quiz</div></a>
        <a href="/profile" class="@if(request()->is('profile')) active @endif"><i class="bi bi-person-circle"></i><div style="font-size:0.8rem">Me</div></a>
    </nav>
@endif
@if(!request()->is('profile'))
    <div id="ai-assistant-bubble">
        <div class="d-flex align-items-end gap-2">
            <div id="ai-bubble-msg" class="d-none bg-white p-2 mb-2" style="animation:bounceIn 0.5s;">
                <img src='{{ asset('brand/noblenest-logo.svg') }}' alt='NobleNest logo' style='width:32px;height:32px;border-radius:0.65rem;vertical-align:middle;margin-right:6px;'>
                <span id="ai-bubble-text">{{ I18n::get('ai_bubble_hello') ?? 'Hi! Need help?' }}</span>
            </div>
            <button class="ai-bubble-launch position-relative" type="button" onclick="openAIModal()">
                <img src='{{ asset('brand/noblenest-logo.svg') }}' alt='NobleNest assistant'>
                <span id="ai-typing-indicator" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-info d-none" style="font-size:0.7rem;">...</span>
            </button>
        </div>
    </div>
@endif
@include('partials.assistant')
<style>
@keyframes bounceIn { 0%{transform:scale(0.7);} 60%{transform:scale(1.1);} 100%{transform:scale(1);} }
@keyframes aiTyping { 0%{opacity:0.2;} 50%{opacity:1;} 100%{opacity:0.2;} }
#ai-typing-indicator { animation: aiTyping 1.2s infinite; }
</style>
<script>
window.addEventListener('DOMContentLoaded',()=>{
    const bubble = document.getElementById('ai-bubble-msg');
    if(bubble) {
        setTimeout(()=>bubble.classList.remove('d-none'), 1200);
        setTimeout(()=>bubble.classList.add('d-none'), 5200);
    }
});
</script>
@yield('scripts')
<script>
function toggleTheme() {
    fetch('/theme-toggle', {method:'POST', headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content}})
        .then(()=>window.location.reload());
}
</script>
@include('partials.pwa-install-prompt')
@include('partials.cookie-consent')
</body>
</html>
