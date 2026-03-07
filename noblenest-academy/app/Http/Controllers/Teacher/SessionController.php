<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassSession;
use App\Models\SessionToken;
use App\Models\TeacherCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    // ------------------------------------------------------------------
    // Create a new class session
    // ------------------------------------------------------------------

    public function store(Request $request, TeacherCourse $course)
    {
        $this->authoriseCourse($course);

        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'starts_at'        => 'required|date|after:now',
            'duration_minutes' => 'required|integer|min:15|max:480',
            'meeting_url'      => 'nullable|url',
        ]);

        $session = ClassSession::create([
            'teacher_course_id' => $course->id,
            'teacher_id'        => Auth::id(),
            'title'             => $data['title'],
            'description'       => $data['description'] ?? null,
            'starts_at'         => $data['starts_at'],
            'duration_minutes'  => $data['duration_minutes'],
            'meeting_url'       => $data['meeting_url'] ?? null,
            'room_id'           => ClassSession::generateRoomId(),
            'status'            => 'scheduled',
        ]);

        // Generate teacher's own session token
        SessionToken::generate($session->id, Auth::id(), 'teacher');

        return back()->with('status', 'Session "' . $session->title . '" scheduled.');
    }

    // ------------------------------------------------------------------
    // Cancel a session
    // ------------------------------------------------------------------

    public function cancel(ClassSession $session)
    {
        $this->authoriseSession($session);
        $session->update(['status' => 'cancelled']);

        return back()->with('status', 'Session cancelled.');
    }

    // ------------------------------------------------------------------
    // Start (go live) / End a session
    // ------------------------------------------------------------------

    public function start(ClassSession $session)
    {
        $this->authoriseSession($session);
        $session->update(['status' => 'live']);

        // Issue tokens for enrolled students that don't have one yet
        $enrolledStudentIds = $session->course->activeEnrollments()->pluck('student_id');
        foreach ($enrolledStudentIds as $studentId) {
            SessionToken::generate($session->id, $studentId, 'student');
        }

        return redirect()->route('classroom.room', $session->room_id);
    }

    public function end(ClassSession $session)
    {
        $this->authoriseSession($session);
        $session->update(['status' => 'ended']);

        return back()->with('status', 'Session ended.');
    }

    // ------------------------------------------------------------------
    // Private helpers
    // ------------------------------------------------------------------

    private function authoriseCourse(TeacherCourse $course): void
    {
        if ($course->teacher_id !== Auth::id()) {
            abort(403);
        }
    }

    private function authoriseSession(ClassSession $session): void
    {
        if ($session->teacher_id !== Auth::id()) {
            abort(403);
        }
    }
}
