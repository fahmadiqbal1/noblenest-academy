<?php

namespace App\Http\Controllers;

use App\Models\Referral;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReferralController extends Controller
{
    /**
     * Track a referral click — store the ref code in session, then redirect to register.
     */
    public function track(Request $request, string $code)
    {
        $code = strtoupper(preg_replace('/[^A-Z0-9]/', '', $code));

        if (strlen($code) >= 6 && strlen($code) <= 20) {
            session(['referral_code' => $code]);
        }

        return redirect()->route('register', ['ref' => $code]);
    }

    /**
     * Show the referral dashboard for the authenticated user.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $referrals = Referral::where('referrer_id', $user->id)
            ->with('referred:id,name,created_at')
            ->latest()
            ->paginate(20);

        $totalEarned = Referral::where('referrer_id', $user->id)
            ->where('reward_issued', true)
            ->sum('reward_amount');

        return view('referrals.index', compact('referrals', 'totalEarned'));
    }
}
