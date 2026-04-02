<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Http\Request;

class TeacherVettingController extends Controller
{
    public function index(Request $request)
    {
        $status    = $request->get('status', 'pending');
        $teachers  = TeacherProfile::with('user')
            ->where('status', $status)
            ->latest()
            ->paginate(25);

        return view('admin.teacher-vetting.index', compact('teachers', 'status'));
    }

    public function show(TeacherProfile $teacherProfile)
    {
        $teacherProfile->load('user');
        return view('admin.teacher-vetting.show', compact('teacherProfile'));
    }

    public function approve(TeacherProfile $teacherProfile)
    {
        $teacherProfile->update(['status' => 'approved', 'approved_at' => now()]);

        // Notify teacher
        $teacherProfile->user->notify(new \App\Notifications\TeacherApproved($teacherProfile));

        return redirect()->route('admin.teacher-vetting')
            ->with('status', "Teacher {$teacherProfile->user->name} approved.");
    }

    public function reject(Request $request, TeacherProfile $teacherProfile)
    {
        $request->validate(['reason' => 'required|string|max:500']);

        $teacherProfile->update([
            'status'          => 'rejected',
            'rejection_reason' => $request->reason,
        ]);

        return redirect()->route('admin.teacher-vetting')
            ->with('status', "Teacher rejected.");
    }
}
