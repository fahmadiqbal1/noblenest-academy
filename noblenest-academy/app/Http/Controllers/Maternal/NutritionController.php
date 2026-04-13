<?php

namespace App\Http\Controllers\Maternal;

use App\Http\Controllers\Controller;
use App\Models\MaternalMealPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NutritionController extends Controller
{
    public function index(Request $request)
    {
        $profile = Auth::user()->maternalProfile;

        $query = MaternalMealPlan::where('stage', $profile->stage);

        if ($request->filled('culture')) {
            $query->where('cultural_origin', $request->input('culture'));
        }

        $mealPlans = $query->orderBy('sort_order')->paginate(12);

        return view('maternal.nutrition.index', compact('mealPlans', 'profile'));
    }

    public function show(MaternalMealPlan $maternalMealPlan)
    {
        $profile = Auth::user()->maternalProfile;

        return view('maternal.nutrition.show', compact('maternalMealPlan', 'profile'));
    }
}
