<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\PayoutRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayoutController extends Controller
{
    public function index()
    {
        $payouts = PayoutRequest::where('teacher_id', Auth::id())
            ->latest()
            ->paginate(20);

        // Earnings summary (placeholder — real calculation from course enrollments)
        $pendingAmount = PayoutRequest::where('teacher_id', Auth::id())
            ->where('status', 'pending')
            ->sum('amount');

        return view('teacher.payouts', compact('payouts', 'pendingAmount'));
    }

    public function request(Request $request)
    {
        $validated = $request->validate([
            'amount'   => 'required|numeric|min:10|max:10000',
            'currency' => 'required|string|size:3',
            'notes'    => 'nullable|string|max:500',
        ]);

        // Prevent duplicate pending requests
        $existing = PayoutRequest::where('teacher_id', Auth::id())
            ->where('status', 'pending')
            ->exists();

        if ($existing) {
            return back()->withErrors(['amount' => 'You already have a pending payout request.']);
        }

        PayoutRequest::create([
            'teacher_id' => Auth::id(),
            'amount'     => $validated['amount'],
            'currency'   => $validated['currency'],
            'notes'      => $validated['notes'] ?? null,
            'status'     => 'pending',
        ]);

        return redirect()->route('teacher.payouts')
            ->with('status', 'Payout request submitted. Processing within 7 business days.');
    }
}
