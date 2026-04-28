<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsurePractitionerActive
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (! $user || $user->role !== 'Practitioner') {
            abort(403, 'Practitioner access required.');
        }

        $profile = $user->practitionerProfile;

        if (! $profile) {
            return redirect()->route('practitioner.profile.setup');
        }

        if ($profile->isSuspended()) {
            abort(403, 'Your practitioner account has been suspended. Reason: ' . ($profile->suspended_reason ?? 'Contact admin.'));
        }

        return $next($request);
    }
}
