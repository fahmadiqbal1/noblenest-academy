<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $session->title }} — Noble Nest Classroom</title>
    {{-- Self-hosted Tailwind v4 + design tokens. No Bootstrap, no bi-* icons. --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    <style>
        body { background: #1a1a2e; color: #eee; font-family: 'Inter', 'Segoe UI', Arial, sans-serif; margin: 0; overflow: hidden; }
        #room { display: flex; height: 100vh; flex-direction: column; }
        #topbar { background: #16213e; padding: 0.5rem 1rem; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #0f3460; flex-shrink: 0; }
        #videoGrid { flex: 1; display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 0.5rem; padding: 0.5rem; overflow-y: auto; }
        .video-tile { position: relative; background: #0f3460; border-radius: 10px; overflow: hidden; aspect-ratio: 16/9; display: flex; align-items: center; justify-content: center; }
        .video-tile video { width: 100%; height: 100%; object-fit: cover; }
        .video-tile .peer-label { position: absolute; bottom: 8px; left: 8px; background: rgba(0,0,0,0.6); padding: 2px 8px; border-radius: 12px; font-size: 0.75rem; }
        .video-tile .no-video { color: #aaa; text-align: center; font-size: 0.9rem; }
        #sidebar { width: 280px; flex-shrink: 0; background: #16213e; border-left: 1px solid #0f3460; display: flex; flex-direction: column; }
        #main-content { flex: 1; display: flex; overflow: hidden; }
        #chatBox { flex: 1; overflow-y: auto; padding: 0.5rem; }
        .chat-msg { margin-bottom: 0.5rem; font-size: 0.85rem; }
        .chat-msg .sender { font-weight: 600; color: #7ecfff; }
        #controls { background: #16213e; padding: 0.5rem 1rem; display: flex; justify-content: center; gap: 0.75rem; border-top: 1px solid #0f3460; flex-shrink: 0; }
        .ctrl-btn { border-radius: 50%; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; cursor: pointer; transition: all 0.2s; }
        .ctrl-btn.active { background: #e74c3c; color: white; }
        .ctrl-btn.inactive { background: #2d4a7a; color: #ccc; }
        .ctrl-btn:hover { transform: scale(1.1); }
        #participantsList { max-height: 180px; overflow-y: auto; padding: 0.5rem; font-size: 0.85rem; }
        .status-badge { font-size: 0.7rem; padding: 2px 6px; border-radius: 10px; }
        @media (max-width: 768px) {
            #sidebar { display: none; }
            #videoGrid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div id="room">
    {{-- Top bar --}}
    <div id="topbar">
        <div class="flex items-center gap-3">
            <a href="/" class="no-underline">
                <span style="background:linear-gradient(90deg,#a78bfa,#60a5fa);-webkit-background-clip:text;-webkit-text-fill-color:transparent;font-weight:700;font-size:1.1rem">Noble Nest</span>
            </a>
            <div>
                <span class="font-semibold">{{ $session->title }}</span>
                <span class="text-[var(--color-text-muted)] ms-2 text-sm">{{ $session->course->title ?? '' }}</span>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <span id="sessionStatus" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $session->status === 'live' ? 'bg-red-100 text-red-700' : 'bg-gray-200 text-gray-700' }}">
                {{ strtoupper($session->status) }}
            </span>
            <span class="text-[var(--color-text-muted)] text-sm" id="sessionTimer">00:00</span>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-sky-600" id="participantCount">0 in room</span>
            @if($isTeacher)
                <form method="POST" action="{{ route('teacher.sessions.end', $session) }}" class="inline">
                    @csrf
                    <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-red-600 text-red-600 hover:bg-red-600 hover:text-white" onclick="return confirm('End the session for everyone?')">
                        <x-ui.icon name="circle-stop" /> End Session
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div id="main-content">
        {{-- Video grid --}}
        <div style="flex:1;display:flex;flex-direction:column;overflow:hidden;">
            <div id="videoGrid">
                {{-- Local video --}}
                <div class="video-tile" id="localTile">
                    <video id="localVideo" autoplay muted playsinline></video>
                    <div class="peer-label">
                        <x-ui.icon name="user" class="me-1" /> You {{ $isTeacher ? '(Teacher)' : '' }}
                    </div>
                </div>
            </div>
            {{-- Controls --}}
            <div id="controls">
                <button class="ctrl-btn active" id="btnMic" title="Mute/Unmute" onclick="toggleMic()">
                    <x-ui.icon name="mic" class="when-on" />
                    <x-ui.icon name="mic-off" class="when-off" />
                </button>
                <button class="ctrl-btn active" id="btnCam" title="Camera on/off" onclick="toggleCam()">
                    <x-ui.icon name="video" class="when-on" />
                    <x-ui.icon name="video-off" class="when-off" />
                </button>
                <button class="ctrl-btn inactive" id="btnScreen" title="Share screen" onclick="toggleScreen()">
                    <x-ui.icon name="monitor" id="screenIcon" />
                </button>
                <button class="ctrl-btn inactive" id="btnChat" title="Chat" onclick="toggleSidebar()">
                    <x-ui.icon name="message-circle" />
                </button>
                <button class="ctrl-btn" style="background:#e74c3c;color:white" title="Leave" onclick="leaveRoom()">
                    <x-ui.icon name="phone-off" />
                </button>
            </div>
        </div>

        {{-- Sidebar: participants + chat --}}
        <div id="sidebar" x-data="{ tab: 'chat' }">
            <ul class="flex border-b border-gray-200 flex-wrap" style="background:#16213e">
                <li>
                    <button type="button" @click="tab = 'chat'"
                            :class="tab === 'chat' ? 'text-white font-semibold' : 'text-gray-300'"
                            class="px-4 py-2 rounded-md hover:bg-white/10 text-sm font-medium">Chat</button>
                </li>
                <li>
                    <button type="button" @click="tab = 'people'"
                            :class="tab === 'people' ? 'text-white font-semibold' : 'text-gray-300'"
                            class="px-4 py-2 rounded-md hover:bg-white/10 text-sm font-medium">People</button>
                </li>
            </ul>
            <div class="flex-1 flex flex-col overflow-hidden">
                <div x-show="tab === 'chat'" class="flex flex-col" id="tabChat" style="flex:1;overflow:hidden">
                    <div id="chatBox"></div>
                    <form id="chatForm" class="p-2 border-t border-white/10" onsubmit="sendChat(event)">
                        <div class="flex w-full items-stretch text-sm">
                            <input type="text" class="block w-full px-3 py-2 rounded-l-md border border-gray-700 bg-gray-900 text-gray-200 focus:border-violet-500 focus:outline-none" id="chatInput" placeholder="Type a message…" autocomplete="off">
                            <button type="submit" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 rounded-r-md font-semibold transition bg-violet-600 text-white hover:bg-violet-700 text-sm"><x-ui.icon name="send" /></button>
                        </div>
                    </form>
                </div>
                <div x-show="tab === 'people'" id="tabPeople" style="flex:1;overflow-y:auto">
                    <div id="participantsList"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// ─── Peer identity ──────────────────────────────────────────────────────────
const PEER_INFO = JSON.parse(atob('{{ $peerToken }}'));
const ROOM_ID   = '{{ $session->room_id }}';
const IS_TEACHER = {{ $isTeacher ? 'true' : 'false' }};

// ─── State ──────────────────────────────────────────────────────────────────
let localStream  = null;
let peers        = {};          // peerId → RTCPeerConnection
let micEnabled   = true;
let camEnabled   = true;
let screenSharing = false;
const chatChannel = new BroadcastChannel('nn-classroom-' + ROOM_ID);

// ─── Start local media ───────────────────────────────────────────────────────
async function startMedia() {
    try {
        localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
        document.getElementById('localVideo').srcObject = localStream;
    } catch (e) {
        // Camera/mic denied — show placeholder
        document.getElementById('localTile').innerHTML = `
            <div class="no-video">
                <x-ui.icon name="video-off" class="text-5xl block mb-2" />
                <p class="text-sm">${e.name === 'NotAllowedError' ? 'Camera/mic access denied' : 'No camera/mic found'}</p>
            </div>
            <div class="peer-label"><x-ui.icon name="user" class="me-1" /> You</div>`;
        micEnabled = false; camEnabled = false;
        updateControls();
    }
}

// ─── Controls ────────────────────────────────────────────────────────────────
function toggleMic() {
    if (!localStream) return;
    micEnabled = !micEnabled;
    localStream.getAudioTracks().forEach(t => t.enabled = micEnabled);
    updateControls();
    broadcastSignal({ type: 'state', mic: micEnabled, cam: camEnabled });
}

function toggleCam() {
    if (!localStream) return;
    camEnabled = !camEnabled;
    localStream.getVideoTracks().forEach(t => t.enabled = camEnabled);
    updateControls();
    broadcastSignal({ type: 'state', mic: micEnabled, cam: camEnabled });
}

async function toggleScreen() {
    if (screenSharing) {
        // Stop screen share, revert to camera
        const videoTrack = localStream.getVideoTracks()[0];
        if (videoTrack) {
            videoTrack.stop();
        }
        try {
            const camStream = await navigator.mediaDevices.getUserMedia({ video: true });
            const newTrack = camStream.getVideoTracks()[0];
            localStream.removeTrack(videoTrack);
            localStream.addTrack(newTrack);
            Object.values(peers).forEach(pc => {
                const sender = pc.getSenders().find(s => s.track && s.track.kind === 'video');
                if (sender) sender.replaceTrack(newTrack);
            });
            document.getElementById('localVideo').srcObject = localStream;
        } catch (e) {}
        screenSharing = false;
    } else {
        try {
            const screenStream = await navigator.mediaDevices.getDisplayMedia({ video: true });
            const screenTrack = screenStream.getVideoTracks()[0];
            const existingVideo = localStream.getVideoTracks()[0];
            if (existingVideo) localStream.removeTrack(existingVideo);
            localStream.addTrack(screenTrack);
            Object.values(peers).forEach(pc => {
                const sender = pc.getSenders().find(s => s.track && s.track.kind === 'video');
                if (sender) sender.replaceTrack(screenTrack);
            });
            document.getElementById('localVideo').srcObject = localStream;
            screenTrack.onended = () => toggleScreen();
            screenSharing = true;
        } catch (e) {}
    }
    updateControls();
}

function updateControls() {
    document.getElementById('btnMic').className   = `ctrl-btn ${micEnabled ? 'active' : 'inactive'}`;
    document.getElementById('btnCam').className   = `ctrl-btn ${camEnabled ? 'active' : 'inactive'}`;
    document.getElementById('btnScreen').className = `ctrl-btn ${screenSharing ? 'active' : 'inactive'}`;
    // Icon state is driven by CSS rules keyed off the parent's `.active`/`.inactive`
    // class — see `.ctrl-btn .when-on / .when-off` in resources/css/app.css.
}

function toggleSidebar() {
    const sb = document.getElementById('sidebar');
    sb.style.display = sb.style.display === 'none' ? 'flex' : 'none';
    sb.style.flexDirection = 'column';
}

function leaveRoom() {
    if (confirm('Leave the classroom?')) {
        broadcastSignal({ type: 'leave', peerId: PEER_INFO.user_id });
        if (localStream) localStream.getTracks().forEach(t => t.stop());
        window.location.href = IS_TEACHER ? '/teacher/dashboard' : '/student/my-courses';
    }
}

// ─── Chat ─────────────────────────────────────────────────────────────────────
function sendChat(e) {
    e.preventDefault();
    const input = document.getElementById('chatInput');
    const msg   = input.value.trim();
    if (!msg) return;
    input.value = '';
    const payload = { type: 'chat', sender: PEER_INFO.name, role: PEER_INFO.role, text: msg, ts: Date.now() };
    broadcastSignal(payload);
    appendChat(payload);
}

function appendChat({ sender, role, text }) {
    const box  = document.getElementById('chatBox');
    const div  = document.createElement('div');
    div.className = 'chat-msg';
    div.innerHTML = `<span class="sender">${escHtml(sender)} ${role === 'teacher' ? '👩‍🏫' : ''}</span>: ${escHtml(text)}`;
    box.appendChild(div);
    box.scrollTop = box.scrollHeight;
}

function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ─── BroadcastChannel signalling (same-origin peers in different tabs/windows)
// In production: replace with Laravel Echo + Pusher/Soketi for cross-device support.
function broadcastSignal(msg) {
    chatChannel.postMessage({ ...msg, from: PEER_INFO.user_id, fromName: PEER_INFO.name });
}

chatChannel.onmessage = (event) => {
    const data = event.data;
    if (data.from === PEER_INFO.user_id) return; // ignore own messages
    if (data.type === 'chat') appendChat(data);
    if (data.type === 'join') addRemotePeer(data);
    if (data.type === 'leave') removeRemotePeer(data.from);
    if (data.type === 'offer') handleOffer(data);
    if (data.type === 'answer') handleAnswer(data);
    if (data.type === 'ice') handleIce(data);
};

// ─── WebRTC peer connection helpers ──────────────────────────────────────────
const ICE_SERVERS = [
    { urls: 'stun:stun.l.google.com:19302' },
    { urls: 'stun:stun1.l.google.com:19302' },
];

function createPeerConnection(peerId, peerName) {
    const pc = new RTCPeerConnection({ iceServers: ICE_SERVERS });

    if (localStream) {
        localStream.getTracks().forEach(t => pc.addTrack(t, localStream));
    }

    pc.onicecandidate = e => {
        if (e.candidate) {
            broadcastSignal({ type: 'ice', to: peerId, candidate: e.candidate });
        }
    };

    pc.ontrack = e => {
        let tile = document.getElementById('peer-' + peerId);
        if (!tile) {
            tile = document.createElement('div');
            tile.className = 'video-tile';
            tile.id = 'peer-' + peerId;
            tile.innerHTML = `<video autoplay playsinline></video><div class="peer-label"><x-ui.icon name="user" class="me-1" />${escHtml(peerName)}</div>`;
            document.getElementById('videoGrid').appendChild(tile);
        }
        tile.querySelector('video').srcObject = e.streams[0];
    };

    peers[peerId] = pc;
    return pc;
}

async function addRemotePeer({ from, fromName }) {
    if (peers[from]) return;
    const pc = createPeerConnection(from, fromName);
    const offer = await pc.createOffer();
    await pc.setLocalDescription(offer);
    broadcastSignal({ type: 'offer', to: from, sdp: offer, from: PEER_INFO.user_id, fromName: PEER_INFO.name });
}

async function handleOffer({ from, fromName, sdp, to }) {
    if (to && to !== PEER_INFO.user_id) return;
    if (peers[from]) return;
    const pc = createPeerConnection(from, fromName);
    await pc.setRemoteDescription(new RTCSessionDescription(sdp));
    const answer = await pc.createAnswer();
    await pc.setLocalDescription(answer);
    broadcastSignal({ type: 'answer', to: from, sdp: answer });
}

async function handleAnswer({ from, sdp, to }) {
    if (to && to !== PEER_INFO.user_id) return;
    const pc = peers[from];
    if (pc) await pc.setRemoteDescription(new RTCSessionDescription(sdp));
}

async function handleIce({ from, candidate, to }) {
    if (to && to !== PEER_INFO.user_id) return;
    const pc = peers[from];
    if (pc) await pc.addIceCandidate(new RTCIceCandidate(candidate)).catch(() => {});
}

function removeRemotePeer(peerId) {
    const tile = document.getElementById('peer-' + peerId);
    if (tile) tile.remove();
    if (peers[peerId]) {
        peers[peerId].close();
        delete peers[peerId];
    }
}

// ─── Session timer ────────────────────────────────────────────────────────────
const startTime = Date.now();
setInterval(() => {
    const secs = Math.floor((Date.now() - startTime) / 1000);
    const m    = String(Math.floor(secs / 60)).padStart(2, '0');
    const s    = String(secs % 60).padStart(2, '0');
    document.getElementById('sessionTimer').textContent = `${m}:${s}`;
}, 1000);

// ─── Participant polling ──────────────────────────────────────────────────────
async function refreshParticipants() {
    try {
        const r = await fetch(`/classroom/${ROOM_ID}/participants`, {
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        });
        const data = await r.json();
        const list = document.getElementById('participantsList');
        list.innerHTML = data.participants.map(p =>
            `<div class="flex items-center gap-2 p-2 border-b border-secondary">
                <img src="https://api.dicebear.com/7.x/bottts/svg?seed=${p.id}" style="width:28px;height:28px;border-radius:50%">
                <span>${escHtml(p.name)}</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-${p.role==='teacher'?'warning text-dark':'info'} status-badge ms-auto">${p.role}</span>
            </div>`
        ).join('');
        document.getElementById('participantCount').textContent = data.participants.length + ' in room';
    } catch (e) {}
}
setInterval(refreshParticipants, 8000);

// ─── Init ─────────────────────────────────────────────────────────────────────
(async () => {
    await startMedia();
    // Announce join
    broadcastSignal({ type: 'join', from: PEER_INFO.user_id, fromName: PEER_INFO.name });
    refreshParticipants();
    // Greet
    setTimeout(() => appendChat({
        sender: 'System', role: 'system',
        text: `Welcome to the classroom, ${PEER_INFO.name}! ${IS_TEACHER ? '👩‍🏫 You are the teacher.' : '👋 Say hello!'}`
    }), 500);
})();
</script>
</body>
</html>
