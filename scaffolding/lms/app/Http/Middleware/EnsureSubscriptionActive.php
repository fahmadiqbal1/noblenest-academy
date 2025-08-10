<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\Subscription;
use Carbon\Carbon;

class EnsureSubscriptionActive
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $subscription = Subscription::where('user_id', $user->id)
            ->where('active', true)
            ->where('ends_at', '>', Carbon::now())
            ->first();
        if (!$subscription) {
            return redirect('/')->with('error', 'You need an active subscription to access this content.');
        }
        return $next($request);
    }
}

