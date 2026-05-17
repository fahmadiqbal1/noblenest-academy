<?php

namespace App\Http\Controllers;

use App\Models\SchoolAdminInvite;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Phase 7 — Institutional licensing.
 *
 * Three personas:
 *   - Admin creates an invite via /admin/institutional/invite (admin-only).
 *   - Invitee opens the signed URL, completes signup, becomes school_admin.
 *   - school_admin views/manages seats at /school/dashboard.
 */
class InstitutionalController extends Controller
{
    public const INVITE_TTL_DAYS = 14;

    /**
     * Admin: issue a new invite. Returns JSON with the signed URL.
     */
    public function adminCreateInvite(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|max:255',
            'school_name' => 'required|string|max:255',
            'seats' => 'required|integer|min:1|max:10000',
        ]);

        $invite = SchoolAdminInvite::create([
            'email' => $data['email'],
            'school_name' => $data['school_name'],
            'seats' => $data['seats'],
            'invite_token' => Str::random(48),
            'expires_at' => now()->addDays(self::INVITE_TTL_DAYS),
        ]);

        return response()->json([
            'invite_id' => $invite->id,
            'url' => route('institutional.invite.show', ['token' => $invite->invite_token]),
            'expires_at' => $invite->expires_at?->toIso8601String(),
        ], 201);
    }

    /**
     * GET — render signup form for an invite token.
     */
    public function showInvite(Request $request, string $token)
    {
        $invite = SchoolAdminInvite::where('invite_token', $token)->first();
        if (! $invite || $invite->isExpired()) {
            abort(410, 'This invite link has expired or is invalid.');
        }
        if ($invite->isAccepted()) {
            abort(409, 'This invite has already been accepted.');
        }

        return view('institutional.invite', compact('invite'));
    }

    /**
     * POST — accept the invite and create the school_admin User.
     */
    public function acceptInvite(Request $request, string $token)
    {
        $invite = SchoolAdminInvite::where('invite_token', $token)->first();
        if (! $invite || $invite->isExpired()) {
            abort(410, 'This invite link has expired or is invalid.');
        }
        if ($invite->isAccepted()) {
            abort(409, 'This invite has already been accepted.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $invite->email,
            'password' => Hash::make($data['password']),
            'role' => 'school_admin',
        ]);

        $invite->forceFill([
            'accepted_at' => now(),
            'accepted_by_user_id' => $user->id,
        ])->save();

        Auth::login($user);

        return redirect()->route('school.dashboard');
    }

    /**
     * school_admin dashboard — list seats / invite info.
     */
    public function dashboard(Request $request)
    {
        $user = $request->user();
        $invite = SchoolAdminInvite::where('accepted_by_user_id', $user->id)->first();
        $assignedSeats = Subscription::where('provider', 'institutional')
            ->where('plan', 'institutional')
            ->count();

        return view('institutional.dashboard', [
            'invite' => $invite,
            'assignedSeats' => $assignedSeats,
        ]);
    }

    /**
     * Manual seat assignment placeholder (MVP — no real linking yet).
     */
    public function assignSeats(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|max:255',
        ]);

        // Placeholder — actual seat → child/student mapping ships Phase 8.
        return response()->json([
            'status' => 'queued',
            'message' => "Seat assignment for {$data['email']} queued (Phase 8 will deliver real linking).",
        ], 202);
    }
}
