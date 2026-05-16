<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = Cache::remember('admin.users.list.' . Cache::get('admin.users.version', 0) . '.page.' . (request('page', 1)), 300, function () {
            return User::with('roles')->latest()->paginate(10);
        });

        return view('dashboard.admin.users', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role'     => ['required', 'in:admin,instructor,student'],
        ]);

        try {
            DB::transaction(function () use ($request) {
                $user = User::create([
                    'name'     => $request->name,
                    'email'    => $request->email,
                    'password' => Hash::make($request->password),
                    'role'     => $request->role,
                ]);

                $user->syncRoles([$request->role]);
            });
        } catch (\Throwable $e) {
            return redirect()->route('admin.users')->with('error', 'Failed to create user. Please try again.');
        }

        return redirect()->route('admin.users')->with('success', 'User created successfully.');
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => ['required', 'in:admin,instructor,student'],
        ]);

        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot change your own role.');
        }

        try {
            DB::transaction(function () use ($request, $user) {
                $user->update(['role' => $request->role]);
                $user->syncRoles([$request->role]);
            });
        } catch (\Throwable $e) {
            return redirect()->route('admin.users')->with('error', 'Failed to update role. Please try again.');
        }

        return redirect()->route('admin.users')->with('success', "{$user->name}'s role updated to {$request->role}.");
    }

    public function updatePassword(Request $request, User $user)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        try {
            $user->update(['password' => Hash::make($request->password)]);
        } catch (\Throwable $e) {
            return redirect()->route('admin.users')->with('error', 'Failed to update password. Please try again.');
        }

        return redirect()->route('admin.users')->with('success', "{$user->name}'s password updated.");
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete yourself.');
        }

        try {
            DB::transaction(function () use ($user) {
                $user->delete();
            });
        } catch (\Throwable $e) {
            return redirect()->route('admin.users')->with('error', 'Failed to delete user. Please try again.');
        }

        return redirect()->route('admin.users')->with('success', 'User deleted successfully.');
    }
}
