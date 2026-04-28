<?php

namespace App\Http\Controllers\Maternal;

use App\Http\Controllers\Controller;
use App\Models\MaternalContent;
use App\Models\MaternalExercisePlan;
use App\Services\MaternalContentFilterService;
use Illuminate\Support\Facades\Auth;

class TechniqueController extends Controller
{
    public function __construct(private MaternalContentFilterService $filter) {}

    public function index(string $culture)
    {
        $profile = Auth::user()->maternalProfile;

        $content = $this->filter->safeContentQuery($profile)
            ->forCulture($culture)
            ->orderBy('sort_order')
            ->paginate(12);

        $exercises = MaternalExercisePlan::where('stage', $profile->stage)
            ->where('cultural_origin', $culture)
            ->orderBy('sort_order')
            ->get();

        $cultureLabels = [
            'chinese'   => 'Ancient Chinese',
            'japanese'  => 'Japanese',
            'ayurvedic' => 'Ayurvedic',
            'general'   => 'General Wellness',
        ];

        $cultureName = $cultureLabels[$culture] ?? ucfirst($culture);

        return view('maternal.techniques.index', compact('content', 'exercises', 'profile', 'culture', 'cultureName'));
    }
}
