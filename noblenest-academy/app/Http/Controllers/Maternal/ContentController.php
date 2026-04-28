<?php

namespace App\Http\Controllers\Maternal;

use App\Http\Controllers\Controller;
use App\Models\MaternalContent;
use App\Models\MaternalProgress;
use App\Services\MaternalContentFilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContentController extends Controller
{
    public function __construct(private MaternalContentFilterService $filter) {}

    public function index(Request $request)
    {
        $profile = Auth::user()->maternalProfile;

        $query = $this->filter->safeContentQuery($profile);

        if ($request->filled('category')) {
            $query->forCategory($request->input('category'));
        }
        if ($request->filled('culture')) {
            $query->forCulture($request->input('culture'));
        }
        if ($request->filled('q')) {
            $query->search($request->input('q'));
        }

        $contents = $query->orderBy('sort_order')->paginate(12);

        $categories = MaternalContent::published()
            ->select('category')
            ->distinct()
            ->pluck('category');

        return view('maternal.content.index', compact('contents', 'categories', 'profile'));
    }

    public function show(MaternalContent $maternalContent)
    {
        $this->authorize('view', $maternalContent);

        $profile = Auth::user()->maternalProfile;

        abort_unless($this->filter->isSafe($maternalContent, $profile), 403, 'This content is not recommended for your health profile.');

        $maternalContent->load('steps', 'sideNotes');

        $progress = MaternalProgress::where('maternal_profile_id', $profile->id)
            ->where('maternal_content_id', $maternalContent->id)
            ->first();

        $related = MaternalContent::published()
            ->where('category', $maternalContent->category)
            ->where('id', '!=', $maternalContent->id)
            ->limit(4)
            ->get();

        return view('maternal.content.show', compact('maternalContent', 'progress', 'related', 'profile'));
    }

    public function start(MaternalContent $maternalContent)
    {
        $profile = Auth::user()->maternalProfile;
        abort_unless($this->filter->isSafe($maternalContent, $profile), 403);

        $progress = MaternalProgress::firstOrNew([
            'maternal_profile_id' => $profile->id,
            'maternal_content_id' => $maternalContent->id,
        ]);

        $progress->markStarted();

        return redirect()->route('maternal.content.show', $maternalContent)
            ->with('success', 'Progress started!');
    }

    public function complete(Request $request, MaternalContent $maternalContent)
    {
        $profile = Auth::user()->maternalProfile;
        abort_unless($this->filter->isSafe($maternalContent, $profile), 403);

        $request->validate(['rating' => 'nullable|integer|min:1|max:5']);

        $progress = MaternalProgress::where('maternal_profile_id', $profile->id)
            ->where('maternal_content_id', $maternalContent->id)
            ->firstOrFail();

        $progress->markCompleted($request->input('rating'));

        return redirect()->route('maternal.content.show', $maternalContent)
            ->with('success', 'Content completed! Great job on your wellness journey.');
    }
}
