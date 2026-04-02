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

        return view('pricing', compact('tier'));
    }
}
