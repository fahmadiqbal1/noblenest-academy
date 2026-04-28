<?php

namespace App\Http\Controllers\Practitioner;

use App\Http\Controllers\Controller;
use App\Models\ContentReview;
use App\Models\MaternalContent;
use Illuminate\Http\Request;

class ContentReviewController extends Controller
{
    public function index(Request $request)
    {
        $profile = $request->user()->practitionerProfile;

        $pending = MaternalContent::where('moderation_status', 'pending')
            ->whereDoesntHave('reviews', function ($q) use ($profile) {
                $q->where('practitioner_profile_id', $profile->id);
            })
            ->latest()
            ->paginate(15);

        $myReviews = $profile->reviews()->with('content')->latest()->paginate(10, ['*'], 'reviewed_page');

        return view('practitioner.reviews.index', compact('pending', 'myReviews', 'profile'));
    }

    public function show(Request $request, MaternalContent $maternalContent)
    {
        $profile = $request->user()->practitionerProfile;
        $maternalContent->load('steps');

        $existingReview = ContentReview::where('practitioner_profile_id', $profile->id)
            ->where('maternal_content_id', $maternalContent->id)
            ->first();

        return view('practitioner.reviews.show', compact('maternalContent', 'profile', 'existingReview'));
    }

    public function store(Request $request, MaternalContent $maternalContent)
    {
        $profile = $request->user()->practitionerProfile;

        $existing = ContentReview::where('practitioner_profile_id', $profile->id)
            ->where('maternal_content_id', $maternalContent->id)
            ->first();

        if ($existing) {
            return redirect()->route('practitioner.reviews.show', $maternalContent)
                ->with('error', 'You have already reviewed this content.');
        }

        $validated = $request->validate([
            'decision'       => 'required|in:approved,rejected,flagged',
            'side_notes'     => 'nullable|string|max:5000',
            'internal_notes' => 'nullable|string|max:5000',
        ]);

        ContentReview::create([
            'practitioner_profile_id' => $profile->id,
            'maternal_content_id'     => $maternalContent->id,
            'decision'                => $validated['decision'],
            'side_notes'              => $validated['side_notes'] ?? null,
            'internal_notes'          => $validated['internal_notes'] ?? null,
            'credential_used'         => $profile->license_type,
            'credential_number'       => $profile->license_number,
            'reviewed_at'             => now(),
        ]);

        $profile->increment('verified_content_count');

        if ($validated['decision'] === 'approved') {
            $maternalContent->update([
                'moderation_status'       => 'approved',
                'medical_reviewer_name'   => $request->user()->name,
                'reviewed_by_credential'  => $profile->formattedLicenseType(),
            ]);
        }

        return redirect()->route('practitioner.reviews.index')
            ->with('success', 'Review submitted successfully.');
    }
}
