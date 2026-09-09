<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AccessControlController extends Controller
{
    public function index()
    {
        $roles = Role::where('guard_name', 'admin')->with('permissions')->get();
        $users = User::has('roles')->with('roles')->get();
        
        $modules = [
            'dashboard',
            'analytics',
            'diet chart',
            'products',
            'orders',
            'customers',
            'promotions',
            'loyalty',
            'moderation',
            'administration',
            'content',
            'assessment',
            'users',
            'roles'
        ];

        return view('admin.access.index', compact('roles', 'users', 'modules'));
    }

    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'array',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'admin'
        ]);

        $this->syncRolePermissions($role, $request->permissions);

        return response()->json(['success' => true, 'message' => 'Role created successfully.']);
    }

    public function updateRole(Request $request, Role $role)
    {
        if ($role->name === 'Super Admin') {
            return response()->json(['success' => false, 'message' => 'Cannot edit the Super Admin role.'], 403);
        }

        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
            'permissions' => 'array',
        ]);

        $role->update([
            'name' => $request->name,
        ]);

        $this->syncRolePermissions($role, $request->permissions);

        return response()->json(['success' => true, 'message' => 'Role updated successfully.']);
    }

    public function destroyRole(Role $role)
    {
        if ($role->name === 'Super Admin') {
            return response()->json(['success' => false, 'message' => 'Cannot delete the Super Admin role.'], 403);
        }

        $role->delete();

        return response()->json(['success' => true, 'message' => 'Role deleted successfully.']);
    }

    private function syncRolePermissions(Role $role, $permissionsArray)
    {
        $spatiePermissions = [];
        if (is_array($permissionsArray)) {
            foreach ($permissionsArray as $module => $levels) {
                if (isset($levels['view']) && $levels['view'] == '1') {
                    $spatiePermissions[] = 'view ' . $module;
                }
                if (isset($levels['manage']) && $levels['manage'] == '1') {
                    // Manage usually implies view as well, but we'll assign both just in case
                    $spatiePermissions[] = 'manage ' . $module;
                    if (!in_array('view ' . $module, $spatiePermissions)) {
                        $spatiePermissions[] = 'view ' . $module;
                    }
                }
            }
        }
        
        // Make sure all these permissions actually exist before syncing
        foreach ($spatiePermissions as $permName) {
            Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'admin']);
        }
        
        $role->syncPermissions($spatiePermissions);
    }

    // --- Users ---
    
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string',
            'status' => 'required|in:1,0',
        ]);

        $admin = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password, // Model casts to 'hashed', so we don't Hash::make here
            'role' => 'admin', // Required for AuthenticationController login check
            'is_active' => $request->status,
        ]);

        $admin->assignRole($request->role);

        return response()->json(['success' => true, 'message' => 'User created successfully.']);
    }

    public function updateUser(Request $request, User $admin)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $admin->id,
            'password' => 'nullable|string|min:8',
            'role' => 'required|string',
            'status' => 'required|in:1,0',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => 'admin', // Ensure role is forced to admin on update as well
            'is_active' => $request->status,
        ];

        if ($request->filled('password')) {
            $data['password'] = $request->password; // Model casts to 'hashed'
        }

        $admin->update($data);

        $admin->syncRoles([$request->role]);

        return response()->json(['success' => true, 'message' => 'User updated successfully.']);
    }

    public function destroyUser(User $admin)
    {
        if ($admin->id === auth('admin')->id()) {
            return response()->json(['success' => false, 'message' => 'Cannot delete yourself.'], 403);
        }

        $admin->delete();

        return response()->json(['success' => true, 'message' => 'User deleted successfully.']);
    }
}
