<?php

namespace App\Http\Controllers;

use App\Services\PricingService;
use Illuminate\Http\Request;

class PricingController extends Controller
{
    public function __construct(private readonly PricingService $pricing) {}

    public function index(Request $request)
    {
        $tier = $this->pricing->resolve($request);

        // Phase 7: surface PPP-adjusted spec plans alongside the legacy region tier.
        $country = $this->pricing->resolveCountryFromRequest($request);
        $planKeys = ['freemium', 'individual', 'family', 'annual', 'institutional'];
        $plans = [];
        foreach ($planKeys as $key) {
            try {
                $plans[$key] = $this->pricing->resolveTier($key, $country);
            } catch (\RuntimeException $e) {
                $plans[$key] = null;
            }
        }

        return view('pricing', compact('tier', 'plans', 'country'));
    }
}
