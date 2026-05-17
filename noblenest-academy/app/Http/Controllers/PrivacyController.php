<?php

namespace App\Http\Controllers;

use App\Jobs\ExportParentDataJob;
use App\Jobs\HardDeleteParentDataJob;
use App\Models\AuditLogEntry;
use App\Models\ChildProfile;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PrivacyController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $children = ChildProfile::where('parent_id', $user->id)->withCount('activityProgress')->get();
        $paymentCount = Payment::where('user_id', $user->id)->count();

        return view('privacy.dashboard', compact('user', 'children', 'paymentCount'));
    }

    public function showParentalConsent(Request $request, ChildProfile $child)
    {
        if ($child->parent_id !== Auth::id()) {
            abort(403);
        }
        return view('privacy.parental-consent', compact('child'));
    }

    public function recordParentalConsent(Request $request, ChildProfile $child)
    {
        if ($child->parent_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'agree_terms'   => 'accepted',
            'agree_privacy' => 'accepted',
            'agree_coppa'   => 'accepted',
        ]);

        $child->forceFill([
            'parental_consent_at'         => now(),
            'parental_consent_ip'         => substr((string) $request->ip(), 0, 45),
            'parental_consent_user_agent' => substr((string) $request->userAgent(), 0, 255),
        ])->save();

        return redirect()->route('privacy.dashboard')
            ->with('status', "Consent recorded for {$child->name}.");
    }

    /**
     * Phase 5 — dispatches a queued export job; user receives a signed link by email.
     */
    public function exportData(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        ExportParentDataJob::dispatch($user->id);

        AuditLogEntry::record(
            actorUserId: $user->id,
            action: 'privacy.export.requested',
            targetType: \App\Models\User::class,
            targetId: $user->id,
            ip: $request->ip(),
            userAgent: $request->userAgent(),
        );

        return redirect()->route('privacy.dashboard')
            ->with('status', __('We are preparing your data export. You will receive an email with a download link shortly.'));
    }

    /**
     * Signed download endpoint — returns the export file if it exists.
     */
    public function downloadExport(Request $request, int $user, string $ts)
    {
        $disk = Storage::disk('local');
        $zip  = "private/exports/{$user}/{$ts}.zip";
        $json = "private/exports/{$user}/{$ts}.json";

        if ($disk->exists($zip)) {
            return $disk->download($zip, "noblenest-export-{$ts}.zip");
        }
        if ($disk->exists($json)) {
            return $disk->download($json, "noblenest-export-{$ts}.json");
        }

        abort(404);
    }

    /**
     * Phase 5 — GDPR erase. Soft-delete now, hard-delete in 30 days.
     */
    public function deleteData(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
            'confirm'  => 'required|in:DELETE',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (! Hash::check($request->input('password'), $user->password)) {
            return back()->withErrors(['password' => __('Incorrect password.')]);
        }

        AuditLogEntry::record(
            actorUserId: $user->id,
            action: 'privacy.erase.requested',
            targetType: \App\Models\User::class,
            targetId: $user->id,
            ip: $request->ip(),
            userAgent: $request->userAgent(),
        );

        // Cascade soft-delete to children + their progress.
        $children = ChildProfile::where('parent_id', $user->id)->get();
        foreach ($children as $child) {
            $child->activityProgress()->delete();
            $child->delete();
        }
        $userId = $user->id;
        $user->delete();

        // Schedule hard-delete in 30 days.
        HardDeleteParentDataJob::dispatch($userId)->delay(now()->addDays(30));

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', __('Your account has been deleted. Final removal will complete in 30 days.'));
    }
}
