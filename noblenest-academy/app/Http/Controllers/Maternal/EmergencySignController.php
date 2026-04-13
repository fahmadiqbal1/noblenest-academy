<?php

namespace App\Http\Controllers\Maternal;

use App\Http\Controllers\Controller;
use App\Models\MaternalEmergencySign;
use Illuminate\Support\Facades\Auth;

class EmergencySignController extends Controller
{
    public function index()
    {
        $profile = Auth::user()->maternalProfile;

        $emergencySigns = MaternalEmergencySign::where('stage', $profile->stage)
            ->orderByRaw("FIELD(severity, 'emergency', 'warning', 'info')")
            ->get()
            ->groupBy('severity');

        return view('maternal.emergency-signs', compact('emergencySigns', 'profile'));
    }
}
