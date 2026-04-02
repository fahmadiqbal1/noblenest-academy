<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassSession;
use App\Models\CourseReview;
use App\Models\TeacherCourse;
use App\Models\TeacherEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $teacher = $request->user();

        // Course stats
        $courses = TeacherCourse::where('teacher_id', $teacher->id)
            ->withCount(['enrollments', 'sessions'])
            ->get();

        $totalStudents = TeacherEnrollment::whereHas('course', fn ($q) => $q->where('teacher_id', $teacher->id))
            ->where('status', 'active')
            ->count();

        $totalRevenue = TeacherEnrollment::whereHas('course', fn ($q) => $q->where('teacher_id', $teacher->id))
            ->where('status', 'active')
            ->sum('amount_paid');

        // Monthly enrollments (last 6 months)
        $monthlyEnrollments = TeacherEnrollment::whereHas('course', fn ($q) => $q->where('teacher_id', $teacher->id))
            ->where('created_at', '>=', now()->subMonths(6))
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count")
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month');

        // Rating summary
        $ratingSummary = CourseReview::whereHas('course', fn ($q) => $q->where('teacher_id', $teacher->id))
            ->visible()
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating');

        $avgRating = CourseReview::whereHas('course', fn ($q) => $q->where('teacher_id', $teacher->id))
            ->visible()
            ->avg('rating');

        // Recent sessions
        $recentSessions = ClassSession::where('teacher_id', $teacher->id)
            ->with('course:id,title')
            ->latest('starts_at')
            ->limit(5)
            ->get();

        return view('teacher.analytics', compact(
            'courses',
            'totalStudents',
            'totalRevenue',
            'monthlyEnrollments',
            'ratingSummary',
            'avgRating',
            'recentSessions'
        ));
    }
}
