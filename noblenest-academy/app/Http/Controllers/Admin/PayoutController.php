<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PayoutRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayoutController extends Controller
{
    public function index(Request $request)
    {
        $status   = $request->get('status', 'pending');
        $payouts  = PayoutRequest::with('teacher')
            ->where('status', $status)
            ->latest()
            ->paginate(25);

        return view('admin.payouts.index', compact('payouts', 'status'));
    }

    public function approve(PayoutRequest $payoutRequest)
    {
        $payoutRequest->update([
            'status'       => 'approved',
            'processed_by' => Auth::id(),
            'processed_at' => now(),
        ]);

        return redirect()->route('admin.payouts')
            ->with('status', "Payout #{$payoutRequest->id} approved.");
    }

    public function reject(Request $request, PayoutRequest $payoutRequest)
    {
        $request->validate(['reason' => 'nullable|string|max:500']);

        $payoutRequest->update([
            'status'       => 'rejected',
            'notes'        => $request->reason,
            'processed_by' => Auth::id(),
            'processed_at' => now(),
        ]);

        return redirect()->route('admin.payouts')
            ->with('status', "Payout #{$payoutRequest->id} rejected.");
    }
}
