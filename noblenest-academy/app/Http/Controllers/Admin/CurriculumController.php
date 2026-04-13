<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Activity;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CurriculumController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::query();
        if ($request->filled('age')) {
            $query->where('age_min', '<=', $request->age)->where('age_max', '>=', $request->age);
        }
        if ($request->filled('subject')) {
            $query->where('subject', 'like', '%'.$request->subject.'%');
        }
        $allActivities = Activity::orderBy('age_min')->get();
        $activities = $query->orderBy('subject')->get();
        $subjects = $activities->groupBy('subject');
        return view('admin.activities.curriculum', [
            'subjects' => $subjects,
            'allActivities' => $allActivities,
        ]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'activity_id' => 'required|exists:activities,id',
            'subject' => 'required|string',
        ]);
        $activity = Activity::findOrFail($request->activity_id);
        $activity->subject = $request->subject;
        $activity->save();
        return redirect()->back()->with('status', 'Activity assigned to subject!');
    }

    public function remove(Request $request)
    {
        $request->validate([
            'activity_id' => 'required|exists:activities,id',
            'subject' => 'required|string',
        ]);
        $activity = Activity::findOrFail($request->activity_id);
        if ($activity->subject === $request->subject) {
            $activity->subject = null;
            $activity->save();
        }
        return redirect()->back()->with('status', 'Activity removed from subject!');
    }

    // DRAG-AND-DROP SUPPORT: Accept AJAX drag-and-drop assignment/removal
    public function dragAssign(Request $request)
    {
        $request->validate([
            'activity_id' => 'required|exists:activities,id',
            'subject' => 'required|string',
        ]);
        $activity = Activity::findOrFail($request->activity_id);
        $activity->subject = $request->subject;
        $activity->save();
        return response()->json(['success' => true]);
    }

    public function dragRemove(Request $request)
    {
        $request->validate([
            'activity_id' => 'required|exists:activities,id',
            'subject' => 'required|string',
        ]);
        $activity = Activity::findOrFail($request->activity_id);
        if ($activity->subject === $request->subject) {
            $activity->subject = null;
            $activity->save();
        }
        return response()->json(['success' => true]);
    }

    // ANALYTICS: Show curriculum coverage (skills, activities per skill, age coverage)
    public function analytics(Request $request)
    {
        $skills = Activity::select('skill')->distinct()->pluck('skill')->filter();
        $coverage = [];
        foreach ($skills as $skill) {
            $activities = Activity::where('skill', $skill)->get();
            $ages = $activities->map(function($a){ return [$a->age_min, $a->age_max]; })->flatten();
            $coverage[] = [
                'skill' => $skill,
                'count' => $activities->count(),
                'age_min' => $ages->min(),
                'age_max' => $ages->max(),
            ];
        }
        $totalSkills = $skills->count();
        $totalActivities = Activity::count();
        return view('admin.activities.analytics', compact('coverage', 'totalSkills', 'totalActivities'));
    }

    // SCHEDULED MONTHLY REPORTS: Send analytics to admin via email
    public function sendMonthlyReport()
    {
        $skills = Activity::select('skill')->distinct()->pluck('skill')->filter();
        $coverage = [];
        foreach ($skills as $skill) {
            $activities = Activity::where('skill', $skill)->get();
            $ages = $activities->map(function($a){ return [$a->age_min, $a->age_max]; })->flatten();
            $coverage[] = [
                'skill' => $skill,
                'count' => $activities->count(),
                'age_min' => $ages->min(),
                'age_max' => $ages->max(),
            ];
        }
        $topLiked = Activity::withCount('likes')->orderByDesc('likes_count')->take(10)->get();
        $totalSkills = $skills->count();
        $totalActivities = Activity::count();
        // Send email to admin(s)
        Mail::send('admin.activities.report_email', compact('coverage', 'topLiked', 'totalSkills', 'totalActivities'), function($m) {
            $m->to(config('mail.admin_address', 'admin@noblenest.com'))
              ->subject('Monthly Curriculum Analytics Report');
        });
    }
}
