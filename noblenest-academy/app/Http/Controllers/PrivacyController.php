<?php

namespace App\Http\Controllers;

use App\Models\ChildProfile;
use App\Models\ChildActivityProgress;
use App\Models\QuizAttempt;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PrivacyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $children = ChildProfile::where('user_id', $user->id)->withCount('activityProgress')->get();
        $paymentCount = Payment::where('user_id', $user->id)->count();

        return view('privacy.dashboard', compact('user', 'children', 'paymentCount'));
    }

    public function exportData(Request $request)
    {
        $user = Auth::user();
        $children = ChildProfile::where('user_id', $user->id)
            ->with(['activityProgress.activity', 'quizAttempts'])
            ->get();

        $export = [
            'account' => [
                'name'       => $user->name,
                'email'      => $user->email,
                'created_at' => $user->created_at->toIso8601String(),
                'role'       => $user->role,
            ],
            'children' => $children->map(fn($c) => [
                'name'              => $c->name,
                'birth_date'        => $c->birth_date,
                'activity_count'    => $c->activityProgress->count(),
                'quiz_attempts'     => $c->quizAttempts->count(),
            ])->toArray(),
            'payments' => Payment::where('user_id', $user->id)
                ->select(['amount', 'currency', 'status', 'created_at'])
                ->get()
                ->toArray(),
            'exported_at' => now()->toIso8601String(),
        ];

        return response()->json($export)
            ->header('Content-Disposition', 'attachment; filename="noblenest-my-data.json"');
    }

    public function deleteData(Request $request)
    {
        $request->validate([
            'password'  => 'required|string',
            'confirm'   => 'required|in:DELETE',
        ]);

        $user = Auth::user();

        if (! Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Incorrect password.']);
        }

        // Anonymise child profiles (COPPA: no orphaned child PII)
        ChildProfile::where('user_id', $user->id)->each(function ($child) {
            ChildActivityProgress::where('child_profile_id', $child->id)->delete();
            QuizAttempt::where('child_profile_id', $child->id)->delete();
            $child->delete();
        });

        // Anonymise user — keep payment row for audit but strip PII
        Payment::where('user_id', $user->id)->update(['stripe_customer_id' => null]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $user->forceFill([
            'name'              => 'Deleted User',
            'email'             => 'deleted_' . $user->id . '@noblenest.invalid',
            'password'          => Hash::make(str()->random(32)),
            'email_verified_at' => null,
        ])->save();

        return redirect('/')->with('status', 'Your account and all associated data have been permanently deleted.');
    }
}
