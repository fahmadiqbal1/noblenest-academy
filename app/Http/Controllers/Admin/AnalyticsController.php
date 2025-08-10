<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index()
    {
        // Most liked activities
        $mostLiked = Activity::withCount('likes')->orderByDesc('likes_count')->take(10)->get();
        // Most engaged users
        $mostEngaged = User::withCount('activityProgress')->orderByDesc('activity_progress_count')->take(10)->get();
        // Monthly activity completions
        $monthly = Activity::select(DB::raw('count(*) as count, MONTH(created_at) as month'))
            ->groupBy('month')->get();
        return view('admin.analytics.index', compact('mostLiked', 'mostEngaged', 'monthly'));
    }

    public function reportEmail()
    {
        // Generate and send monthly report (stub)
        $admin = User::where('role', 'Admin')->first();
        // You can generate a real report here (PDF, CSV, etc.)
        // For now, just send a notification
        Mail::raw('Monthly analytics report attached.', function ($message) use ($admin) {
            $message->to($admin->email)->subject('Monthly Analytics Report');
        });
        return back()->with('success', 'Report sent!');
    }

    // Enhancement: API endpoint for most liked activities (for dashboard widgets)
    public function mostLiked()
    {
        $mostLiked = Activity::withCount('likes')->orderByDesc('likes_count')->take(10)->get();
        return response()->json($mostLiked);
    }

    // Enhancement: API endpoint for monthly completions (for chart widgets)
    public function monthlyCompletions()
    {
        $monthly = Activity::select(DB::raw('count(*) as count, MONTH(created_at) as month'))
            ->groupBy('month')->get();
        return response()->json($monthly);
    }
}
