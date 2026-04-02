<?php

namespace App\Http\Controllers;

use App\Models\ChildProfile;
use App\Services\LearningPathService;
use App\Services\MilestoneService;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    public function __construct(
        private readonly LearningPathService $learningPathService,
        private readonly MilestoneService $milestoneService
    ) {}

    /**
     * Show personalised learning assessment & path for a child.
     */
    public function index(Request $request, ChildProfile $child)
    {
        $this->authorize('view', $child);

        $summary = $this->learningPathService->progressSummary($child);
        $daily   = $this->learningPathService->buildDailyPath($child, 6);
        $next    = $this->milestoneService->nextTargets($child);

        return view('assessment.index', compact('child', 'summary', 'daily', 'next'));
    }
}
