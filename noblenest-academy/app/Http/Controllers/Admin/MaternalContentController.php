<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaternalContent;
use App\Models\MaternalContentStep;
use Illuminate\Http\Request;

class MaternalContentController extends Controller
{
    public function index(Request $request)
    {
        $query = MaternalContent::query();

        if ($request->filled('status')) {
            $query->where('moderation_status', $request->input('status'));
        }
        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }
        if ($request->filled('q')) {
            $query->search($request->input('q'));
        }

        $contents = $query->latest()->paginate(20);

        return view('admin.maternal.content.index', compact('contents'));
    }

    public function create()
    {
        return view('admin.maternal.content.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'              => 'required|string|max:255',
            'slug'               => 'required|string|max:255|unique:maternal_contents,slug',
            'content_type'       => 'required|in:article,video,exercise,recipe,technique,herb_guide',
            'stage'              => 'required|string|max:50',
            'category'           => 'required|string|max:100',
            'cultural_origin'    => 'nullable|in:chinese,japanese,ayurvedic,general',
            'description'        => 'required|string',
            'benefit_explanation' => 'required|string|max:1000',
            'skills_improved'    => 'nullable|array',
            'video_url'          => 'nullable|url|max:500',
            'audio_url'          => 'nullable|url|max:500',
            'thumbnail_url'      => 'nullable|url|max:500',
            'is_published'       => 'boolean',
        ]);

        $validated['moderation_status'] = 'pending';

        $content = MaternalContent::create($validated);

        return redirect()->route('admin.maternal.content.edit', $content)
            ->with('success', 'Content created. It requires approval before publishing.');
    }

    public function edit(MaternalContent $maternalContent)
    {
        $maternalContent->load('steps');

        return view('admin.maternal.content.edit', compact('maternalContent'));
    }

    public function update(Request $request, MaternalContent $maternalContent)
    {
        $validated = $request->validate([
            'title'              => 'required|string|max:255',
            'slug'               => 'required|string|max:255|unique:maternal_contents,slug,' . $maternalContent->id,
            'content_type'       => 'required|in:article,video,exercise,recipe,technique,herb_guide',
            'stage'              => 'required|string|max:50',
            'category'           => 'required|string|max:100',
            'cultural_origin'    => 'nullable|in:chinese,japanese,ayurvedic,general',
            'description'        => 'required|string',
            'benefit_explanation' => 'required|string|max:1000',
            'skills_improved'    => 'nullable|array',
            'video_url'          => 'nullable|url|max:500',
            'audio_url'          => 'nullable|url|max:500',
            'thumbnail_url'      => 'nullable|url|max:500',
            'is_published'       => 'boolean',
        ]);

        // Reset moderation on significant changes
        if ($maternalContent->moderation_status === 'approved') {
            $significantFields = ['title', 'description', 'benefit_explanation', 'stage'];
            foreach ($significantFields as $field) {
                if (($validated[$field] ?? null) !== $maternalContent->$field) {
                    $validated['moderation_status'] = 'pending';
                    break;
                }
            }
        }

        $maternalContent->update($validated);

        return redirect()->route('admin.maternal.content.edit', $maternalContent)
            ->with('success', 'Content updated.');
    }

    public function destroy(MaternalContent $maternalContent)
    {
        $maternalContent->delete();

        return redirect()->route('admin.maternal.content.index')
            ->with('success', 'Content deleted.');
    }

    public function approve(Request $request, MaternalContent $maternalContent)
    {
        $request->validate([
            'medical_reviewer_name' => 'required|string|max:255',
        ]);

        $maternalContent->update([
            'moderation_status'      => 'approved',
            'medical_reviewer_name'  => $request->input('medical_reviewer_name'),
            'medical_reviewed_at'    => now(),
        ]);

        return redirect()->route('admin.maternal.content.index')
            ->with('success', 'Content approved.');
    }

    public function reject(MaternalContent $maternalContent)
    {
        $maternalContent->update([
            'moderation_status' => 'rejected',
        ]);

        return redirect()->route('admin.maternal.content.index')
            ->with('info', 'Content rejected.');
    }

    public function generateAnimations(MaternalContent $maternalContent)
    {
        if ($maternalContent->steps()->count() === 0) {
            return redirect()->route('admin.maternal.content.index')
                ->with('error', 'No steps found for this content. Add steps before generating animations.');
        }

        \App\Jobs\GenerateContentAnimationsJob::dispatch('maternal', $maternalContent->id);

        return redirect()->route('admin.maternal.content.index')
            ->with('success', 'Animation generation queued for "' . $maternalContent->title . '". Check job status in Horizon.');
    }
}
