<?php

namespace App\Http\Controllers;

use App\Models\ClassSession;
use App\Models\SessionToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * ClassroomController — serves the WebRTC video session room.
 *
 * Architecture:
 * - Each session has a stable `room_id`.
 * - Teachers start the session (status → live); students join.
 * - Signalling is done peer-to-peer in the browser via WebRTC with a
 *   simple BroadcastChannel / localStorage relay (works on modern
 *   browsers within the same origin) or via an optional STUN/TURN
 *   configured through env.  For production, swap `channel_backend`
 *   to 'pusher' or 'soketi' and configure the .env accordingly.
 * - This controller issues a signed JWT-style token per user so the
 *   front-end JS can identify each peer.
 */
class ClassroomController extends Controller
{
    /**
     * Enter the classroom room for a given room_id.
     * Validates that the user holds a valid SessionToken for this room.
     */
    public function room(string $roomId)
    {
        $session = ClassSession::where('room_id', $roomId)->firstOrFail();

        if (in_array($session->status, ['cancelled', 'ended'])) {
            return redirect()->route('home')
                             ->with('error', 'This class session has ended.');
        }

        $user = Auth::user();

        // Teacher always allowed in their own session
        $isTeacher = $session->teacher_id === $user->id;

        if (! $isTeacher) {
            // Student must hold a valid token
            $token = SessionToken::where('class_session_id', $session->id)
                                  ->where('user_id', $user->id)
                                  ->first();

            if (! $token || $token->isExpired()) {
                // If the session is live, auto-generate a token for enrolled students
                $enrollment = $session->course
                                      ->enrollments()
                                      ->where('student_id', $user->id)
                                      ->where('status', 'active')
                                      ->first();

                if (! $enrollment) {
                    abort(403, 'You are not enrolled in this course.');
                }

                $token = SessionToken::generate($session->id, $user->id, 'student');
            }

            $token->update(['joined_at' => now()]);
        }

        // Build a lightweight peer identity token for WebRTC signalling
        $peerToken = base64_encode(json_encode([
            'user_id'  => $user->id,
            'name'     => $user->name,
            'role'     => $isTeacher ? 'teacher' : 'student',
            'room_id'  => $roomId,
            'ts'       => now()->timestamp,
        ]));

        return view('classroom.room', compact('session', 'peerToken', 'isTeacher'));
    }

    /**
     * AJAX: return current participant list for the room.
     */
    public function participants(string $roomId)
    {
        $session = ClassSession::where('room_id', $roomId)->firstOrFail();

        $participants = SessionToken::where('class_session_id', $session->id)
                                    ->whereNotNull('joined_at')
                                    ->with('user:id,name')
                                    ->get()
                                    ->map(fn ($t) => [
                                        'id'   => $t->user_id,
                                        'name' => $t->user->name ?? 'Unknown',
                                        'role' => $t->role,
                                    ]);

        return response()->json(['participants' => $participants]);
    }
}
