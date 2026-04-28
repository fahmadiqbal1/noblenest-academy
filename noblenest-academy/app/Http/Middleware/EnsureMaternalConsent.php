<?php

namespace App\Http\Middleware;

use App\Models\MaternalProfile;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMaternalConsent
{
    public function handle(Request $request, Closure $next): Response
    {
        $profile = MaternalProfile::where('user_id', $request->user()->id)->first();

        if (! $profile) {
            return redirect()->route('maternal.onboarding');
        }

        if (! $profile->consent_accepted_at) {
            return redirect()->route('maternal.onboarding');
        }

        return $next($request);
    }
}
