<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\AIJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ContentBatchController extends Controller
{
    /**
     * Show the batch content generation form.
     */
    public function create()
    {
        return view('admin.content-batch.create');
    }

    /**
     * Dispatch a batch AI content generation job.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject'        => ['required', Rule::in(['literacy', 'numeracy', 'creativity', 'stem', 'social', 'motor'])],
            'age_tier'       => ['required', Rule::in(['baby', 'toddler', 'preschool', 'school'])],
            'count'          => ['required', 'integer', 'min:1', 'max:50'],
            'language'       => ['required', Rule::in(['en', 'fr', 'ar', 'ur', 'es', 'zh', 'ko', 'ru'])],
            'activity_types' => ['required', 'array'],
            'activity_types.*' => [Rule::in(['tracing', 'puzzle', 'drawing', 'quiz', 'matching', 'story'])],
            'is_free'        => ['nullable', 'boolean'],
        ]);

        $job = AIJob::create([
            'type'       => 'content_batch',
            'status'     => 'pending',
            'payload'    => $validated,
            'created_by' => Auth::id(),
        ]);

        // Dispatch to queue
        \App\Jobs\ProcessContentBatchJob::dispatch($job);

        return redirect()->route('admin.orchestrator.index')
            ->with('status', "Batch queued: {$validated['count']} {$validated['subject']} activities for {$validated['age_tier']} tier.");
    }

    /**
     * Preview generated content before publish.
     */
    public function preview(AIJob $job)
    {
        abort_if($job->type !== 'content_batch', 404);

        $activities = Activity::where('source_job_id', $job->id)->get();

        return view('admin.content-batch.preview', compact('job', 'activities'));
    }

    /**
     * Bulk-publish approved activities from a batch job.
     */
    public function publish(Request $request, AIJob $job)
    {
        abort_if($job->type !== 'content_batch', 404);

        $ids = $request->validate(['ids' => ['required', 'array'], 'ids.*' => ['integer']])['ids'];

        Activity::whereIn('id', $ids)
            ->where('source_job_id', $job->id)
            ->update(['published' => true]);

        return back()->with('status', count($ids) . ' activities published.');
    }
}
