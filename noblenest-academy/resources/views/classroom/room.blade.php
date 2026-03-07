<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $session->title }} — Noble Nest Classroom</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
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
        <div class="d-flex align-items-center gap-3">
            <a href="/" class="text-decoration-none">
                <span style="background:linear-gradient(90deg,#a78bfa,#60a5fa);-webkit-background-clip:text;-webkit-text-fill-color:transparent;font-weight:700;font-size:1.1rem">Noble Nest</span>
            </a>
            <div>
                <span class="fw-semibold">{{ $session->title }}</span>
                <span class="text-muted ms-2 small">{{ $session->course->title ?? '' }}</span>
            </div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span id="sessionStatus" class="badge bg-{{ $session->status === 'live' ? 'danger' : 'secondary' }}">
                {{ strtoupper($session->status) }}
            </span>
            <span class="text-muted small" id="sessionTimer">00:00</span>
            <span class="badge bg-info" id="participantCount">0 in room</span>
            @if($isTeacher)
                <form method="POST" action="{{ route('teacher.sessions.end', $session) }}" class="d-inline">
                    @csrf
                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('End the session for everyone?')">
                        <i class="bi bi-stop-circle"></i> End Session
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
                        <i class="bi bi-person-fill me-1"></i> You {{ $isTeacher ? '(Teacher)' : '' }}
                    </div>
                </div>
            </div>
            {{-- Controls --}}
            <div id="controls">
                <button class="ctrl-btn active" id="btnMic" title="Mute/Unmute" onclick="toggleMic()">
                    <i class="bi bi-mic-fill" id="micIcon"></i>
                </button>
                <button class="ctrl-btn active" id="btnCam" title="Camera on/off" onclick="toggleCam()">
                    <i class="bi bi-camera-video-fill" id="camIcon"></i>
                </button>
                <button class="ctrl-btn inactive" id="btnScreen" title="Share screen" onclick="toggleScreen()">
                    <i class="bi bi-display" id="screenIcon"></i>
                </button>
                <button class="ctrl-btn inactive" id="btnChat" title="Chat" onclick="toggleSidebar()">
                    <i class="bi bi-chat-dots"></i>
                </button>
                <button class="ctrl-btn" style="background:#e74c3c;color:white" title="Leave" onclick="leaveRoom()">
                    <i class="bi bi-telephone-x-fill"></i>
                </button>
            </div>
        </div>

        {{-- Sidebar: participants + chat --}}
        <div id="sidebar">
            <ul class="nav nav-tabs nav-fill" style="background:#16213e">
                <li class="nav-item">
                    <a class="nav-link active text-light small" data-bs-toggle="tab" href="#tabChat">Chat</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-light small" data-bs-toggle="tab" href="#tabPeople">People</a>
                </li>
            </ul>
            <div class="tab-content flex-grow-1 d-flex flex-column overflow-hidden">
                <div class="tab-pane fade show active d-flex flex-column" id="tabChat" style="flex:1;overflow:hidden">
                    <div id="chatBox"></div>
                    <form id="chatForm" class="p-2 border-top border-secondary" onsubmit="sendChat(event)">
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control bg-dark text-light border-secondary" id="chatInput" placeholder="Type a message…" autocomplete="off">
                            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-send"></i></button>
                        </div>
                    </form>
                </div>
                <div class="tab-pane fade" id="tabPeople" style="flex:1;overflow-y:auto">
                    <div id="participantsList"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
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
                <i class="bi bi-camera-video-off fs-1 d-block mb-2"></i>
                <p class="small">${e.name === 'NotAllowedError' ? 'Camera/mic access denied' : 'No camera/mic found'}</p>
            </div>
            <div class="peer-label"><i class="bi bi-person-fill me-1"></i> You</div>`;
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
    document.getElementById('micIcon').className  = `bi bi-mic${micEnabled ? '-fill' : '-mute-fill'}`;
    document.getElementById('camIcon').className  = `bi bi-camera-video${camEnabled ? '-fill' : '-off'}`;
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
            tile.innerHTML = `<video autoplay playsinline></video><div class="peer-label"><i class="bi bi-person-fill me-1"></i>${escHtml(peerName)}</div>`;
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
            `<div class="d-flex align-items-center gap-2 p-2 border-bottom border-secondary">
                <img src="https://api.dicebear.com/7.x/bottts/svg?seed=${p.id}" style="width:28px;height:28px;border-radius:50%">
                <span>${escHtml(p.name)}</span>
                <span class="badge bg-${p.role==='teacher'?'warning text-dark':'info'} status-badge ms-auto">${p.role}</span>
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
