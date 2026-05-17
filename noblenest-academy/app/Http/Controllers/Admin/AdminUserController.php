<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $allowed = ['Admin', 'Parent'];
        $roleFilter = in_array($request->input('role'), $allowed, true) ? $request->input('role') : null;

        $query = User::query();

        if ($roleFilter) {
            $query->where('role', $roleFilter);
        }
        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->q.'%')
                    ->orWhere('email', 'like', '%'.$request->q.'%');
            });
        }

        $users = $query->latest()->paginate(30);
        $roleCounts = User::selectRaw('role, count(*) as total')->groupBy('role')->pluck('total', 'role');

        return view('admin.users.index', compact('users', 'roleFilter', 'roleCounts'));
    }

    public function updateRole(Request $request, User $user)
    {
        $data = $request->validate([
            'role' => ['required', Rule::in(['Admin', 'Parent'])],
        ]);

        // Prevent a user from removing their own admin access
        if ($user->id === Auth::id() && $data['role'] !== 'Admin') {
            return back()->with('error', 'You cannot change your own role.');
        }

        $user->update(['role' => $data['role']]);

        return back()->with('status', "Role for {$user->name} updated to {$data['role']}.");
    }
}
