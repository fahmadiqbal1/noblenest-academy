<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChildController extends Controller
{
    // Show add child form
    public function create()
    {
        return view('parent.add_child');
    }

    // Store new child profile
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'required|integer|min:0|max:12',
            'preferred_language' => 'nullable|in:en,fr,ru,zh,es,ko',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'nullable|string|min:6',
        ]);

        $child = User::create([
            'name' => $validated['name'],
            'age' => $validated['age'],
            'preferred_language' => $validated['preferred_language'] ?? null,
            'email' => $validated['email'] ?? null,
            'password' => $validated['password'] ? Hash::make($validated['password']) : Hash::make(uniqid()),
            'role' => 'Child',
            'parent_id' => Auth::id(),
        ]);

        // Optionally, store age in a profile table for extensibility

        return redirect()->back()->with('success', 'Child profile added successfully.');
    }

    // List all children for the authenticated parent
    public function index()
    {
        $children = User::where('parent_id', Auth::id())->where('role', 'Child')->get();
        return view('parent.children', compact('children'));
    }

    // Show edit child form
    public function edit(User $child)
    {
        $this->authorizeChild($child);
        return view('parent.edit_child', compact('child'));
    }

    // Update child profile
    public function update(Request $request, User $child)
    {
        $this->authorizeChild($child);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'required|integer|min:0|max:12',
            'preferred_language' => 'nullable|in:en,fr,ru,zh,es,ko,ur,ar',
            'email' => 'nullable|email|unique:users,email,' . $child->id,
        ]);
        $child->update($validated);
        return redirect()->route('children.index')->with('status', 'Child profile updated successfully.');
    }

    // Delete child profile
    public function destroy(User $child)
    {
        $this->authorizeChild($child);
        $child->delete();
        return redirect()->route('children.index')->with('status', 'Child profile deleted successfully.');
    }

    // Helper to ensure parent can only manage their own children
    protected function authorizeChild(User $child)
    {
        if ($child->parent_id !== Auth::id() || $child->role !== 'Child') {
            abort(403);
        }
    }
}
