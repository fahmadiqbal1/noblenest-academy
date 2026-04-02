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
    <meta name="theme-color" content="#0d5c63">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    @if($dir === 'rtl')
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-rtl@5.3.3/dist/css/bootstrap-rtl.min.css" rel="stylesheet" crossorigin="anonymous">
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --nn-bg: #f4efe7;
            --nn-surface: rgba(255, 255, 255, 0.76);
            --nn-surface-strong: #ffffff;
            --nn-text: #18222f;
            --nn-text-muted: #5f6c7b;
            --nn-border: rgba(24, 34, 47, 0.08);
            --nn-primary: #0d5c63;
            --nn-primary-soft: rgba(13, 92, 99, 0.14);
            --nn-accent: #f2a541;
            --nn-danger: #c44536;
            --nn-success: #16866b;
            --nn-shadow: 0 24px 60px rgba(24, 34, 47, 0.10);
        }
        body {
            min-height: 100vh;
            color: var(--nn-text);
            background:
                radial-gradient(circle at top left, rgba(242, 165, 65, 0.20), transparent 28%),
                radial-gradient(circle at top right, rgba(13, 92, 99, 0.20), transparent 32%),
                linear-gradient(180deg, #f6f1ea 0%, #eef3f6 52%, #f7f9fb 100%);
            font-family: 'Manrope', sans-serif;
            transition: background 0.35s ease;
        }
        h1, h2, h3, h4, h5, h6, .navbar-brand, .brand-grad {
            font-family: 'Space Grotesk', sans-serif;
            letter-spacing: -0.03em;
        }
        .app-shell::before {
            content: '';
            position: fixed;
            inset: 0;
            pointer-events: none;
            background-image:
                linear-gradient(rgba(255,255,255,0.10) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.10) 1px, transparent 1px);
            background-size: 48px 48px;
            mask-image: linear-gradient(180deg, rgba(0,0,0,0.18), transparent 85%);
        }
        .brand-grad {
            background: linear-gradient(120deg, #0d5c63 0%, #1f7a8c 45%, #f2a541 100%);
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
            border-radius: 1rem;
            box-shadow: 0 14px 30px rgba(24, 34, 47, 0.10);
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
        .brand-lockup__title-image {
            width: 204px;
            height: auto;
            filter: drop-shadow(0 12px 24px rgba(24, 34, 47, 0.10));
        }
        .brand-lockup__subtitle {
            font-family: 'Manrope', sans-serif;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--nn-text-muted);
        }
        .hero {
            position: relative;
            overflow: hidden;
        }
        .theme-toggle { cursor: pointer; }
        .glass-panel {
            background: var(--nn-surface);
            backdrop-filter: blur(20px);
            border: 1px solid var(--nn-border);
            box-shadow: var(--nn-shadow);
        }
        .navbar,
        .footer-panel {
            background: rgba(255, 255, 255, 0.72);
            backdrop-filter: blur(18px);
            border-color: var(--nn-border) !important;
            box-shadow: 0 18px 40px rgba(24, 34, 47, 0.06);
        }
        .nav-link {
            color: var(--nn-text-muted) !important;
            font-weight: 600;
        }
        .nav-link:hover,
        .nav-link:focus {
            color: var(--nn-primary) !important;
        }
        .btn-primary {
            background: linear-gradient(135deg, #0d5c63, #1f7a8c);
            border-color: transparent;
            box-shadow: 0 12px 24px rgba(13, 92, 99, 0.22);
        }
        .btn-primary:hover,
        .btn-primary:focus {
            background: linear-gradient(135deg, #0b5258, #176977);
            border-color: transparent;
        }
        .btn-outline-secondary,
        .btn-outline-info,
        .btn-outline-success,
        .btn-outline-primary,
        .btn-outline-danger {
            border-width: 1.5px;
        }
        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(255,255,255,0.85);
            box-shadow: 0 10px 24px rgba(24, 34, 47, 0.10);
        }
        .app-status {
            border: 1px solid rgba(13, 92, 99, 0.10);
            background: rgba(13, 92, 99, 0.07);
            color: var(--nn-primary);
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 700;
            padding: 0.55rem 0.9rem;
        }
        .app-main {
            position: relative;
            z-index: 1;
        }
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.86);
            backdrop-filter: blur(18px);
            border-top: 1px solid var(--nn-border);
            z-index: 1030;
            display: flex;
            justify-content: space-around;
            align-items: center;
            padding: 0.55rem 0;
            box-shadow: 0 -18px 36px rgba(24, 34, 47, 0.08);
        }
        .bottom-nav a {
            color: var(--nn-text-muted);
            font-size: 1.35rem;
            text-align: center;
            flex: 1;
            transition: color 0.2s ease, transform 0.2s ease;
        }
        .bottom-nav a.active,
        .bottom-nav a:hover {
            color: var(--nn-primary);
            transform: translateY(-2px);
        }
        .playful-font,
        .professional-font {
            font-family: 'Manrope', sans-serif;
        }
        .assistant-shell {
            background: linear-gradient(180deg, rgba(255,255,255,0.96), rgba(241, 247, 248, 0.96));
            backdrop-filter: blur(22px);
        }
        .assistant-shell__glow {
            position: absolute;
            inset: -20% 35% auto -15%;
            height: 180px;
            background: radial-gradient(circle, rgba(242, 165, 65, 0.24) 0%, transparent 68%);
            pointer-events: none;
        }
        .assistant-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            margin-bottom: 0.35rem;
            color: var(--nn-primary);
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }
        .assistant-chat {
            min-height: 260px;
            max-height: 420px;
            overflow-y: auto;
            padding: 1rem;
            border-radius: 1.25rem;
            background: linear-gradient(180deg, rgba(255,255,255,0.88), rgba(232, 240, 241, 0.88));
            border: 1px solid rgba(24, 34, 47, 0.08);
        }
        .assistant-form {
            display: flex;
            gap: 0.75rem;
            align-items: center;
        }
        .assistant-form__field {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex: 1;
            border-radius: 999px;
            padding: 0.75rem 1rem;
            background: rgba(255,255,255,0.92);
            border: 1px solid rgba(24, 34, 47, 0.08);
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.8);
        }
        .assistant-form__field i {
            color: var(--nn-primary);
        }
        .assistant-send {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .assistant-message {
            display: flex;
            margin-bottom: 0.85rem;
        }
        .assistant-message--user {
            justify-content: flex-end;
        }
        .assistant-message__bubble {
            max-width: min(84%, 560px);
            padding: 0.9rem 1rem;
            border-radius: 1.1rem;
            box-shadow: 0 14px 28px rgba(24, 34, 47, 0.08);
        }
        .assistant-message--user .assistant-message__bubble {
            background: linear-gradient(135deg, #0d5c63, #1f7a8c);
            color: #fff;
            border-bottom-right-radius: 0.35rem;
        }
        .assistant-message--ai .assistant-message__bubble {
            background: rgba(255,255,255,0.96);
            border: 1px solid rgba(24, 34, 47, 0.08);
            border-bottom-left-radius: 0.35rem;
        }
        .assistant-message__meta {
            margin-bottom: 0.35rem;
            color: var(--nn-text-muted);
            font-size: 0.74rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        .assistant-suggestions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .assistant-chip {
            border-radius: 999px;
            background: rgba(255,255,255,0.78);
        }
        #ai-assistant-bubble {
            position: fixed;
            bottom: 96px;
            right: 22px;
            z-index: 1050;
        }
        .ai-bubble-launch {
            width: 64px;
            height: 64px;
            border: 0;
            border-radius: 1.35rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0d5c63, #1f7a8c);
            box-shadow: 0 20px 40px rgba(13, 92, 99, 0.28);
        }
        .ai-bubble-launch img {
            width: 38px;
            height: 38px;
        }
        .footer-brand {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
        }
        .footer-brand img {
            width: 40px;
            height: 40px;
            border-radius: 0.85rem;
        }
        .footer-brand__stack {
            display: flex;
            flex-direction: column;
            line-height: 1.15;
            gap: 0.1rem;
        }
        .footer-brand__name {
            font-weight: 800;
            color: var(--nn-text);
        }
        .footer-brand__tagline {
            font-size: 0.82rem;
            color: var(--nn-text-muted);
        }
        #ai-bubble-msg {
            max-width: 260px;
            border: 1px solid rgba(24, 34, 47, 0.08);
            border-radius: 1rem;
            box-shadow: 0 18px 38px rgba(24, 34, 47, 0.12);
        }
        .footer-links a {
            color: var(--nn-text-muted);
        }
        .footer-links a:hover {
            color: var(--nn-primary);
        }
        @media (max-width: 767.98px) {
            .app-status {
                display: none;
            }
            #ai-assistant-bubble {
                right: 16px;
                bottom: 84px;
            }
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
                <li class="nav-item"><a class="nav-link" href="{{ route('noble.home') }}">{{ I18n::get('welcome') }}</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('marketplace.index') }}"><i class="bi bi-shop"></i> Marketplace</a></li>
                <li class="nav-item"><a class="nav-link" href="/admin/courses">{{ I18n::get('admin_courses') }}</a></li>
                @auth
                    <li class="nav-item"><a class="nav-link" href="/profile"><i class="bi bi-person-circle"></i> {{ I18n::get('profile') }}</a></li>
                    @if(auth()->user()->role === 'Admin')
                        <li class="nav-item"><a class="nav-link" href="/admin/users">{{ I18n::get('admin_users') }}</a></li>
                        <li class="nav-item"><a class="nav-link" href="/admin/courses/create">{{ I18n::get('add_course') }}</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.analytics.index') }}"><i class="bi bi-bar-chart-line"></i> Analytics</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.orchestrator.index') }}"><i class="bi bi-robot"></i> Orchestrator</a></li>
                    @endif
                    @if(auth()->user()->role === 'Teacher')
                        <li class="nav-item"><a class="nav-link" href="{{ route('teacher.dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('teacher.courses.index') }}"><i class="bi bi-book"></i> My Courses</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('teacher.courses.create') }}"><i class="bi bi-plus-circle"></i> New Course</a></li>
                    @endif
                    @if(auth()->user()->role === 'Student')
                        <li class="nav-item"><a class="nav-link" href="{{ route('marketplace.index') }}"><i class="bi bi-shop"></i> Browse Courses</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('student.my-courses') }}"><i class="bi bi-book-open"></i> My Learning</a></li>
                    @endif
                    {{-- Teacher/Student course sections are not implemented yet; hiding to avoid 404s --}}
                    {{--
                    @if(auth()->user()->role === 'Teacher' || auth()->user()->role === 'Admin')
                        <li class="nav-item"><a class="nav-link" href="/teacher/courses">{{ I18n::get('teacher_courses') }}</a></li>
                        <li class="nav-item"><a class="nav-link" href="/teacher/courses/create">{{ I18n::get('add_teacher_course') }}</a></li>
                    @endif
                    @if(auth()->user()->role === 'Student')
                        <li class="nav-item"><a class="nav-link" href="/student/courses">{{ I18n::get('student_courses') }}</a></li>
                    @endif
                    --}}
                    @if(auth()->user()->role === 'Parent' || auth()->user()->role === 'Admin')
                        <li class="nav-item"><a class="nav-link" href="/children">{{ I18n::get('children') }}</a></li>
                    @endif
                    @if(auth()->user()->role === 'Parent')
                        <li class="nav-item"><a class="nav-link" href="/children/create">{{ I18n::get('add_child') }}</a></li>
                    @endif
                    <li class="nav-item"><a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">{{ I18n::get('logout') }}</a></li>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                @else
                    <li class="nav-item"><a class="nav-link" href="/login">{{ I18n::get('login') }}</a></li>
                    <li class="nav-item"><a class="nav-link" href="/register">{{ I18n::get('register') }}</a></li>
                @endauth
            </ul>
            <div class="d-flex gap-2 align-items-center">
                @auth
                    <img src="https://api.dicebear.com/7.x/bottts/svg?seed={{ auth()->user()->id }}" class="avatar me-2" alt="avatar">
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

<footer class="footer-panel border-top mt-5 py-4 {{ $isPlayful ? 'playful-font' : 'professional-font' }}">
    <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center">
        <div class="mb-2 mb-md-0">
            <span class="footer-brand">
                <img src="{{ asset('brand/noblenest-logo.svg') }}" alt="NobleNest Global Academy logo">
                <span class="footer-brand__stack">
                    <span class="footer-brand__name">&copy; {{ date('Y') }} NobleNest Global Academy</span>
                    <span class="footer-brand__tagline">Family-first learning, beautifully delivered.</span>
                </span>
            </span>
        </div>
        <div class="footer-links">
            <a href="/privacy" class="text-decoration-none me-3">Privacy Policy</a>
            <a href="/terms" class="text-decoration-none me-3">Terms of Service</a>
            <a href="mailto:support@noblenest.com" class="text-decoration-none">Support</a>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
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
