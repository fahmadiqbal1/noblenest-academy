<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Scholarship;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScholarshipController extends Controller
{
    public function index()
    {
        $scholarships = Scholarship::with(['grantor', 'recipient'])
            ->latest()
            ->paginate(25);

        return view('admin.scholarships.index', compact('scholarships'));
    }

    public function create()
    {
        return view('admin.scholarships.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'recipient_email' => 'required|email|exists:users,email',
            'duration_months' => 'required|integer|min:1|max:24',
            'reason'          => 'required|string|max:500',
        ]);

        $recipient = User::where('email', $validated['recipient_email'])->firstOrFail();

        Scholarship::create([
            'code'            => strtoupper(\Illuminate\Support\Str::random(10)),
            'granted_by'      => Auth::id(),
            'granted_to'      => $recipient->id,
            'recipient_email'  => $validated['recipient_email'],
            'duration_months' => $validated['duration_months'],
            'expires_at'      => now()->addMonths($validated['duration_months']),
            'reason'          => $validated['reason'],
            'is_claimed'      => false,
        ]);

        return redirect()->route('admin.scholarships')
            ->with('status', "Scholarship granted to {$recipient->name}.");
    }

    public function revoke(Scholarship $scholarship)
    {
        $scholarship->delete();

        return redirect()->route('admin.scholarships')
            ->with('status', "Scholarship revoked.");
    }

    /**
     * Handle a public scholarship application (no auth required).
     */
    public function publicApply(Request $request)
    {
        $validated = $request->validate([
            'parent_name'      => 'required|string|max:255',
            'email'            => 'required|email|max:255',
            'child_name'       => 'required|string|max:255',
            'child_age_months' => 'required|integer|min:0|max:180',
            'country'          => 'required|string|max:100',
            'need_statement'   => 'nullable|string|max:2000',
            'merit_statement'  => 'nullable|string|max:2000',
            'agree_terms'      => 'accepted',
        ]);

        Scholarship::create([
            'code'            => strtoupper(\Illuminate\Support\Str::random(10)),
            'granted_by'      => User::where('role', 'admin')->value('id') ?? 1,
            'recipient_email' => $validated['email'],
            'duration_months' => 3,
            'expires_at'      => now()->addMonths(3),
            'reason'          => "Application from {$validated['parent_name']} for {$validated['child_name']} "
                               . "(age {$validated['child_age_months']}mo, {$validated['country']}). "
                               . ($validated['need_statement'] ? "Need: {$validated['need_statement']} " : '')
                               . ($validated['merit_statement'] ? "Merit: {$validated['merit_statement']}" : ''),
            'is_claimed'      => false,
        ]);

        return back()->with('success', 'Your scholarship application has been submitted. We will review it and get back to you via email.');
    }
}
