<?php

namespace App\Http\Controllers\Maternal;

use App\Http\Controllers\Controller;
use App\Models\MaternalJournal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JournalController extends Controller
{
    public function index()
    {
        $profile = Auth::user()->maternalProfile;

        $entries = MaternalJournal::where('maternal_profile_id', $profile->id)
            ->latest('entry_date')
            ->paginate(15);

        return view('maternal.journal.index', compact('entries', 'profile'));
    }

    public function create()
    {
        $profile = Auth::user()->maternalProfile;

        return view('maternal.journal.create', compact('profile'));
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $profile = $user->maternalProfile;

        $validated = $request->validate([
            'entry_date'      => 'required|date|before_or_equal:today',
            'mood'            => 'required|in:great,good,okay,low,bad',
            'energy_level'    => 'required|integer|min:1|max:5',
            'symptoms'        => 'nullable|array',
            'symptoms.*'      => 'string|max:100',
            'baby_kicks'      => 'nullable|integer|min:0',
            'notes'           => 'nullable|string|max:2000',
        ]);

        // Compute pregnancy week from registration data
        $weekNumber = $profile->pregnancy_week_at_registration
            + (int) $profile->created_at->diffInWeeks(now());

        $journal = new MaternalJournal();
        $journal->maternal_profile_id = $profile->id;
        $journal->entry_date          = $validated['entry_date'];
        $journal->week_number         = min($weekNumber, 42);
        $journal->mood                = $validated['mood'];
        $journal->energy_level        = $validated['energy_level'];
        $journal->baby_kicks_count    = $validated['baby_kicks'] ?? null;
        $journal->notes               = $validated['notes'] ?? null;
        $journal->symptoms            = $validated['symptoms'] ?? null;
        $journal->save();

        // Check for alert symptoms
        if ($journal->hasAlertSymptoms()) {
            return redirect()->route('maternal.emergency-signs')
                ->with('warning', 'Some of your symptoms may require attention. Please review the emergency signs below.');
        }

        return redirect()->route('maternal.journal.index')
            ->with('success', 'Journal entry saved.');
    }

    public function show(MaternalJournal $maternalJournal)
    {
        $this->authorize('view', $maternalJournal);

        return view('maternal.journal.show', compact('maternalJournal'));
    }
}
