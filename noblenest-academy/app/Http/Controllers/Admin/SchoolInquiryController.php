<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolInquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SchoolInquiryController extends Controller
{
    public function index(Request $request)
    {
        $status    = $request->input('status', 'new');
        $inquiries = SchoolInquiry::where('status', $status)
            ->latest()
            ->paginate(25);

        return view('admin.school-inquiries.index', compact('inquiries', 'status'));
    }

    public function show(SchoolInquiry $schoolInquiry)
    {
        return view('admin.school-inquiries.show', compact('schoolInquiry'));
    }

    public function assign(Request $request, SchoolInquiry $schoolInquiry)
    {
        $schoolInquiry->update([
            'status'      => 'in_progress',
            'assigned_to' => Auth::id(),
        ]);

        return redirect()->route('admin.school-inquiries')
            ->with('status', "Inquiry #{$schoolInquiry->id} assigned to you.");
    }

    public function close(SchoolInquiry $schoolInquiry)
    {
        $schoolInquiry->update(['status' => 'closed']);

        return redirect()->route('admin.school-inquiries')
            ->with('status', "Inquiry #{$schoolInquiry->id} closed.");
    }
}
