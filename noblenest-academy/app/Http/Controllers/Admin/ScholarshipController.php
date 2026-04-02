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
            'credits'         => 'required|integer|min:1',
            'duration_months' => 'required|integer|min:1|max:24',
            'reason'          => 'required|string|max:500',
        ]);

        $recipient = User::where('email', $validated['recipient_email'])->firstOrFail();

        Scholarship::create([
            'granted_by'   => Auth::id(),
            'user_id'      => $recipient->id,
            'credits'      => $validated['credits'],
            'expires_at'   => now()->addMonths($validated['duration_months']),
            'reason'       => $validated['reason'],
            'is_claimed'   => false,
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
}
