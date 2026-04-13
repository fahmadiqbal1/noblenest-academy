<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\ChildProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChildController extends Controller
{
    public function create()
    {
        return view('parent.add_child');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'date_of_birth'      => 'required|date|before:today',
            'gender'             => 'required|in:male,female,other,prefer_not_to_say',
            'preferred_language' => 'nullable|in:en,fr,ru,zh,es,ko,ur,ar',
            'is_muslim'          => 'nullable|boolean',
        ]);

        ChildProfile::create([
            'parent_id'          => Auth::id(),
            'name'               => $validated['name'],
            'date_of_birth'      => $validated['date_of_birth'],
            'gender'             => $validated['gender'],
            'preferred_language' => $validated['preferred_language'] ?? 'en',
            'is_muslim'          => $validated['is_muslim'] ?? false,
        ]);

        return redirect()->route('children.index')->with('status', 'Child profile added successfully.');
    }

    public function index()
    {
        $children = ChildProfile::where('parent_id', Auth::id())->get();
        return view('parent.children', compact('children'));
    }

    public function edit(ChildProfile $child)
    {
        $this->authorizeChild($child);
        return view('parent.edit_child', compact('child'));
    }

    public function update(Request $request, ChildProfile $child)
    {
        $this->authorizeChild($child);

        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'date_of_birth'      => 'required|date|before:today',
            'gender'             => 'required|in:male,female,other,prefer_not_to_say',
            'preferred_language' => 'nullable|in:en,fr,ru,zh,es,ko,ur,ar',
            'is_muslim'          => 'nullable|boolean',
        ]);

        $child->update($validated);

        return redirect()->route('children.index')->with('status', 'Child profile updated successfully.');
    }

    public function destroy(ChildProfile $child)
    {
        $this->authorizeChild($child);
        $child->delete();
        return redirect()->route('children.index')->with('status', 'Child profile deleted successfully.');
    }

    protected function authorizeChild(ChildProfile $child): void
    {
        if ($child->parent_id !== Auth::id()) {
            abort(403);
        }
    }
}
