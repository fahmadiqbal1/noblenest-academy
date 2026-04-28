<?php

namespace App\Http\Controllers\Maternal;

use App\Http\Controllers\Controller;
use App\Models\MaternalExercisePlan;
use App\Services\MaternalContentFilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExerciseController extends Controller
{
    public function index(Request $request)
    {
        $profile = Auth::user()->maternalProfile;

        $query = MaternalExercisePlan::where('stage', $profile->stage);

        if ($request->filled('culture')) {
            $query->where('cultural_origin', $request->input('culture'));
        }

        $exercises = $query->orderBy('sort_order')->paginate(12);

        return view('maternal.exercises.index', compact('exercises', 'profile'));
    }

    public function show(MaternalExercisePlan $maternalExercisePlan)
    {
        $profile = Auth::user()->maternalProfile;

        // Check contraindications
        $conditions = $profile->health_conditions ?? [];
        $contraindications = $maternalExercisePlan->contraindications ?? [];

        $blocked = array_intersect($conditions, $contraindications);

        return view('maternal.exercises.show', compact('maternalExercisePlan', 'profile', 'blocked'));
    }
}
