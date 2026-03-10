<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\InviteLink;
use App\Models\SessionToken;
use App\Models\TeacherCourse;
use App\Models\TeacherEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EnrollmentController extends Controller
{
    /**
     * Show the enrolment / checkout page for a teacher course.
     */
    public function checkout(TeacherCourse $course)
    {
        if (! $course->isPublished()) {
            abort(404);
        }

        $enrollment = TeacherEnrollment::where('student_id', Auth::id())
                                       ->where('teacher_course_id', $course->id)
                                       ->first();

        if ($enrollment && $enrollment->isActive()) {
            return redirect()->route('student.my-courses')
                             ->with('status', 'You are already enrolled in "' . $course->title . '".');
        }

        if (! $course->hasCapacity()) {
            return back()->with('error', 'This course is currently full.');
        }

        return view('student.enroll_checkout', compact('course', 'enrollment'));
    }

    /**
     * Process enrolment (free course or after payment simulation).
     */
    public function enroll(Request $request, TeacherCourse $course)
    {
        if (! $course->isPublished()) {
            abort(404);
        }

        if (! $course->hasCapacity()) {
            return back()->with('error', 'This course is currently full.');
        }

        $paymentProvider = $request->input('provider', 'free');
        $paymentRef      = null;
        $amountPaid      = 0;

        if ($course->price > 0) {
            // In production this would integrate with Stripe/PayPal.
            // For now we simulate a successful payment via a form flag.
            $data = $request->validate([
                'payment_ref' => 'required|string|max:255',
            ]);
            $paymentRef  = $data['payment_ref'];
            $amountPaid  = $course->price;
        }

        $enrollment = TeacherEnrollment::updateOrCreate(
            ['student_id' => Auth::id(), 'teacher_course_id' => $course->id],
            [
                'status'           => 'active',
                'payment_status'   => $course->price > 0 ? 'paid' : 'paid',
                'payment_provider' => $paymentProvider,
                'payment_ref'      => $paymentRef,
                'amount_paid'      => $amountPaid,
                'currency'         => $course->currency,
                'enrolled_at'      => now(),
            ]
        );

        // Immediately issue session tokens for any live/upcoming sessions
        $sessions = $course->classSessions()->whereIn('status', ['scheduled', 'live'])->get();
        foreach ($sessions as $session) {
            SessionToken::generate($session->id, Auth::id(), 'student');
        }

        return redirect()->route('student.my-courses')
                         ->with('status', 'You are now enrolled in "' . $course->title . '"! 🎉');
    }

    /**
     * Join a course via teacher-generated invite link.
     */
    public function joinViaInvite(string $token)
    {
        $link = InviteLink::where('token', $token)->firstOrFail();

        if (! $link->isValid()) {
            return redirect()->route('marketplace.index')
                             ->with('error', 'This invite link has expired or reached its use limit.');
        }

        $course = $link->course;

        if (! $course->isPublished()) {
            abort(404);
        }

        if (! Auth::check()) {
            session(['url.intended' => route('invite.join', $token)]);

            return redirect()->route('register')
                             ->with('status', 'Please register or log in to join "' . $course->title . '".');
        }

        if (Auth::user()->role !== 'Student') {
            return redirect()->route('marketplace.show', $course)
                             ->with('error', 'Invite links can only be claimed with a Student account.');
        }

        // Already enrolled
        $existing = TeacherEnrollment::where('student_id', Auth::id())
                                     ->where('teacher_course_id', $course->id)
                                     ->first();
        if ($existing && $existing->isActive()) {
            return redirect()->route('student.course.show', $course)
                             ->with('status', 'You are already enrolled!');
        }

        // Enrol for free (teacher-generated link bypasses payment for now)
        TeacherEnrollment::updateOrCreate(
            ['student_id' => Auth::id(), 'teacher_course_id' => $course->id],
            [
                'status'         => 'active',
                'payment_status' => 'paid',
                'enrolled_at'    => now(),
            ]
        );

        $link->increment('uses');

        return redirect()->route('student.course.show', $course)
                         ->with('status', 'Welcome! You have successfully joined "' . $course->title . '".');
    }

    /**
     * List the student's enrolled courses.
     */
    public function myCourses()
    {
        $enrollments = TeacherEnrollment::where('student_id', Auth::id())
                                        ->where('status', 'active')
                                        ->with(['course.teacher', 'course.classSessions' => fn ($q) => $q->where('status', 'scheduled')->orderBy('starts_at')])
                                        ->orderByDesc('enrolled_at')
                                        ->get();

        return view('student.my_courses', compact('enrollments'));
    }
}
