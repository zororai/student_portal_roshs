<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Admin']);
    }

    public function index()
    {
        // Exclude users who have Teacher, Student, or Parent roles
        $excludedRoles = ['Teacher', 'Student', 'Parent'];
        
        $users = User::with('roles')
            ->get()
            ->filter(function ($user) use ($excludedRoles) {
                // Keep user if they have at least one role AND don't have any of the excluded roles
                return $user->roles->isNotEmpty() && !$user->hasAnyRole($excludedRoles);
            });
        
        // Manual pagination
        $page = request()->get('page', 1);
        $perPage = 20;
        $total = $users->count();
        $users = new \Illuminate\Pagination\LengthAwarePaginator(
            $users->forPage($page, $perPage),
            $total,
            $perPage,
            $page,
            ['path' => request()->url()]
        );
        
        return view('backend.admin.users.index', compact('users'));
    }

    public function create()
    {
        // Get roles excluding Teacher, Student, Parent
        $roles = Role::whereNotIn('name', ['Teacher', 'Student', 'Parent'])->get();
        
        return view('backend.admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|exists:roles,name',
            'phone' => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
        ]);

        $user->assignRole($request->role);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function show($id)
    {
        $user = User::with('roles', 'permissions')->findOrFail($id);
        
        return view('backend.admin.users.show', compact('user'));
    }

    public function edit($id)
    {
        $user = User::with('roles')->findOrFail($id);
        $roles = Role::whereNotIn('name', ['Teacher', 'Student', 'Parent'])->get();
        
        return view('backend.admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6|confirmed',
            'role' => 'required|exists:roles,name',
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        // Sync role (remove old, add new)
        $user->syncRoles([$request->role]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete yourself.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
