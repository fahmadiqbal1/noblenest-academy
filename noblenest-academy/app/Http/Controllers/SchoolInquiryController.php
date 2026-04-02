<?php

namespace App\Http\Controllers;

use App\Models\SchoolInquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class SchoolInquiryController extends Controller
{
    public function store(Request $request)
    {
        // Rate-limit to 3 inquiries per IP per hour
        $key = 'school_inquiry:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            return back()->withErrors(['message' => 'Too many submissions. Please try again later.']);
        }
        RateLimiter::hit($key, 3600);

        $data = $request->validate([
            'school_name'   => 'required|string|max:255',
            'contact_name'  => 'required|string|max:150',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'country'       => 'nullable|string|max:100',
            'student_count' => 'nullable|integer|min:1|max:10000',
            'message'       => 'nullable|string|max:2000',
        ]);

        SchoolInquiry::create($data);

        return back()->with('status', 'Thank you! We will be in touch within 24 hours.');
    }
}
