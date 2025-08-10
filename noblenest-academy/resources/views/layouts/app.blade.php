@php
    $lang = session('lang', auth()->user()->preferred_language ?? 'en');
    $rtlLangs = ['ar', 'ur'];
    $dir = in_array($lang, $rtlLangs) ? 'rtl' : 'ltr';
    $user = auth()->user();
    $role = $user->role ?? null;
    $theme = session('theme', $role === 'Parent' ? 'professional' : ($role === 'Student' ? 'playful' : 'professional'));
    $isPlayful = $theme === 'playful';
    if (!class_exists('I18n')) { class_alias(\App\Helpers\I18n::class, 'I18n'); }
@endphp
<!doctype html>
<html lang="{{ $lang }}" dir="{{ $dir }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Noble Nest Academy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    @if($dir === 'rtl')
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-rtl@5.3.3/dist/css/bootstrap-rtl.min.css" rel="stylesheet" crossorigin="anonymous">
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: {{ $isPlayful ? 'linear-gradient(135deg, #ffe6fa 0%, #e0f7fa 100%)' : 'linear-gradient(180deg, #f8f9ff 0%, #ffffff 100%)' }}; transition: background 0.5s; }
        .brand-grad { background: linear-gradient(90deg, #6f42c1, #0d6efd); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .hero { background: {{ $isPlayful ? 'radial-gradient(1200px circle at 0% 0%, #ffb6f9 0%, transparent 40%), radial-gradient(1200px circle at 100% 0%, #b2ebf2 0%, transparent 40%)' : 'radial-gradient(1200px circle at 0% 0%, rgba(13,110,253,0.08), transparent 40%), radial-gradient(1200px circle at 100% 0%, rgba(111,66,193,0.08), transparent 40%)' }}; }
        .theme-toggle { cursor:pointer; }
        .navbar, .footer { box-shadow: 0 2px 8px rgba(0,0,0,0.03); }
        .avatar { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 2px solid #fff; box-shadow: 0 1px 4px rgba(0,0,0,0.08); }
        .bottom-nav { position:fixed; bottom:0; left:0; right:0; background:#fff; border-top:1px solid #eee; z-index:1030; display:flex; justify-content:space-around; align-items:center; padding:0.5rem 0; box-shadow:0 -2px 8px rgba(0,0,0,0.04); }
        .bottom-nav a { color:#888; font-size:1.4rem; text-align:center; flex:1; transition:color 0.2s; }
        .bottom-nav a.active, .bottom-nav a:hover { color:{{ $isPlayful ? '#ff69b4' : '#0d6efd' }}; }
        .notification-badge { position:absolute; top:0; right:0; background:#ff1744; color:#fff; border-radius:50%; font-size:0.7rem; padding:2px 6px; }
        .playful-font { font-family: 'Comic Sans MS', 'Comic Sans', cursive, sans-serif; }
        .professional-font { font-family: 'Inter', 'Segoe UI', Arial, sans-serif; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top {{ $isPlayful ? 'playful-font' : 'professional-font' }}">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="/noble">
            <span class="brand-grad">Noble Nest</span>
            @if($isPlayful)
                <span class="d-none d-md-inline"><i class="bi bi-emoji-smile text-warning"></i></span>
            @endif
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="/noble">{{ I18n::get('welcome') }}</a></li>
                <li class="nav-item"><a class="nav-link" href="/admin/courses">{{ I18n::get('admin_courses') }}</a></li>
                @auth
                    <li class="nav-item"><a class="nav-link" href="/profile"><i class="bi bi-person-circle"></i> {{ I18n::get('profile') }}</a></li>
                    @if(auth()->user()->role === 'Admin')
                        <li class="nav-item"><a class="nav-link" href="/admin/users">{{ I18n::get('admin_users') }}</a></li>
                        <li class="nav-item"><a class="nav-link" href="/admin/courses/create">{{ I18n::get('add_course') }}</a></li>
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
                <a class="btn btn-primary" href="#assistantModal" data-bs-toggle="modal"><i class="bi bi-chat-dots"></i> {{ I18n::get('ai_assistant') }}</a>
            </div>
        </div>
    </div>
</nav>

<main class="py-4">
    <div class="container">
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @yield('content')
    </div>
</main>

<footer class="bg-white border-top mt-5 py-4 {{ $isPlayful ? 'playful-font' : 'professional-font' }}">
    <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center">
        <div class="mb-2 mb-md-0">
            <span class="text-muted">&copy; {{ date('Y') }} Noble Nest Academy</span>
        </div>
        <div>
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
    <div id="ai-assistant-bubble" style="position:fixed;bottom:90px;right:24px;z-index:1050;">
        <div class="d-flex align-items-end gap-2">
            <div id="ai-bubble-msg" class="d-none bg-white border rounded shadow p-2 mb-2" style="max-width:220px;animation:bounceIn 0.5s;">
                <img src='https://api.dicebear.com/7.x/bottts/svg?seed=ai' style='width:32px;height:32px;border-radius:50%;vertical-align:middle;margin-right:6px;'>
                <span id="ai-bubble-text">{{ I18n::get('ai_bubble_hello') ?? 'Hi! Need help?' }}</span>
            </div>
            <button class="btn btn-lg btn-pink shadow rounded-circle position-relative" style="width:56px;height:56px;" onclick="openAIModal()">
                <img src='https://api.dicebear.com/7.x/bottts/svg?seed=ai' style='width:36px;height:36px;'>
                <span id="ai-typing-indicator" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-info d-none" style="font-size:0.7rem;">...</span>
            </button>
        </div>
    </div>
@endif
<style>
@keyframes bounceIn { 0%{transform:scale(0.7);} 60%{transform:scale(1.1);} 100%{transform:scale(1);} }
@keyframes aiTyping { 0%{opacity:0.2;} 50%{opacity:1;} 100%{opacity:0.2;} }
#ai-typing-indicator { animation: aiTyping 1.2s infinite; }
</style>
<script>
function openAIModal() {
    const modal = document.getElementById('assistantModal');
    if (!modal) { window.location.href = '/#assistantModal'; return; }
    modal.classList.add('show');
    modal.style.display = 'block';
    document.body.classList.add('modal-open');
    const bubble = document.getElementById('ai-bubble-msg');
    if (bubble) setTimeout(()=>bubble.classList.add('d-none'), 200);
}
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
</body>
</html>
