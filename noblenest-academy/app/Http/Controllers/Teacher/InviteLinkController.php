<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\InviteLink;
use App\Models\TeacherCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InviteLinkController extends Controller
{
    public function store(Request $request, TeacherCourse $course)
    {
        if ($course->teacher_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->validate([
            'label'      => 'nullable|string|max:100',
            'max_uses'   => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $link = InviteLink::create([
            'teacher_course_id' => $course->id,
            'token'             => InviteLink::generateToken(),
            'label'             => $data['label'] ?? null,
            'max_uses'          => $data['max_uses'] ?? null,
            'expires_at'        => $data['expires_at'] ?? null,
        ]);

        return back()->with('status', 'Invite link created: ' . route('invite.join', $link->token));
    }

    public function destroy(InviteLink $link)
    {
        if ($link->course->teacher_id !== Auth::id()) {
            abort(403);
        }

        $link->delete();

        return back()->with('status', 'Invite link deleted.');
    }
}
