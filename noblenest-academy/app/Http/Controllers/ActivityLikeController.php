<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ActivityLikeController extends Controller
{
    /**
     * Toggle like on an activity. Idempotent.
     */
    public function toggle(Request $request, Activity $activity)
    {
        $userId = Auth::id();

        $liked = DB::table('activity_likes')
            ->where('user_id', $userId)
            ->where('activity_id', $activity->id)
            ->exists();

        if ($liked) {
            DB::table('activity_likes')
                ->where('user_id', $userId)
                ->where('activity_id', $activity->id)
                ->delete();
            $activity->decrement('like_count');
            $isLiked = false;
        } else {
            DB::table('activity_likes')->insert([
                'user_id'     => $userId,
                'activity_id' => $activity->id,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
            $activity->increment('like_count');
            $isLiked = true;
        }

        if ($request->expectsJson()) {
            return response()->json([
                'liked'      => $isLiked,
                'like_count' => $activity->fresh()->like_count,
            ]);
        }

        return back();
    }
}
