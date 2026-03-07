<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassSession;
use App\Models\TeacherCourse;
use App\Models\TeacherEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $teacher  = Auth::user();
        $courses  = TeacherCourse::where('teacher_id', $teacher->id)
                                 ->withCount(['enrollments', 'activeEnrollments', 'classSessions'])
                                 ->orderByDesc('created_at')
                                 ->get();

        $totalStudents = TeacherEnrollment::whereIn(
            'teacher_course_id',
            $courses->pluck('id')
        )->distinct('student_id')->count('student_id');

        $upcomingSessions = ClassSession::whereIn('teacher_course_id', $courses->pluck('id'))
                                        ->where('status', 'scheduled')
                                        ->where('starts_at', '>=', now())
                                        ->orderBy('starts_at')
                                        ->with('course')
                                        ->take(5)
                                        ->get();

        $totalEarnings = TeacherEnrollment::whereIn(
            'teacher_course_id',
            $courses->pluck('id')
        )->where('payment_status', 'paid')->sum('amount_paid');

        return view('teacher.dashboard', compact(
            'teacher', 'courses', 'totalStudents', 'upcomingSessions', 'totalEarnings'
        ));
    }
}
