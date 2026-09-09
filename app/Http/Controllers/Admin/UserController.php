<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all users who have roles (admins/staff) OR just all users if you want to be able to make any user an admin
        // For an admin panel, it's often better to just show all users so you can assign roles to anyone
        $users = User::with('roles')->paginate(15);
        $roles = Role::where('guard_name', 'admin')->get();
        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Store a newly created resource in storage. (Optional if you want to create admins directly)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'roles' => 'array',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Hash::make($request->password),
            'is_active' => true,
        ]);

        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // Prevent modifying the super admin (User ID 1) if not yourself
        if ($user->id === 1 && auth()->id() !== 1) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot modify the primary Super Admin.');
        }

        $request->validate([
            'roles' => 'array',
        ]);

        $user->syncRoles($request->input('roles', []));

        return redirect()->route('admin.users.index')->with('success', 'User roles updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ($user->id === 1) {
            return redirect()->route('admin.users.index')->with('error', 'Cannot delete the primary Super Admin.');
        }
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'Cannot delete yourself.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
