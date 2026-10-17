<?php

namespace App\Http\Controllers\Admin\Access;

use App\Http\Controllers\Controller;
use App\Services\Access\AccessControlService;
use App\Http\Requests\Admin\Access\StoreAdminUserRequest;
use App\Http\Requests\Admin\Access\StoreRoleRequest;
use App\Http\Requests\Admin\Access\UpdateAdminUserRequest;
use App\Http\Requests\Admin\Access\UpdateRoleRequest;
use Spatie\Permission\Models\Role;
use App\Models\User;

class AccessControlController extends Controller
{
    public function __construct(private readonly AccessControlService $accessControl) {}

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

    public function storeRole(StoreRoleRequest $request)
    {
        $this->accessControl->createRole($request->name, $request->permissions);

        return response()->json(['success' => true, 'message' => 'Role created successfully.']);
    }

    public function updateRole(UpdateRoleRequest $request, Role $role)
    {
        if ($role->name === 'Super Admin') {
            return response()->json(['success' => false, 'message' => 'Cannot edit the Super Admin role.'], 403);
        }

        $this->accessControl->updateRole($role, $request->name, $request->permissions);

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

    // --- Users ---
    
    public function storeUser(StoreAdminUserRequest $request)
    {
        $this->accessControl->createAdmin($request->validated());

        return response()->json(['success' => true, 'message' => 'User created successfully.']);
    }

    public function updateUser(UpdateAdminUserRequest $request, User $admin)
    {
        $this->accessControl->updateAdmin($admin, $request->validated());

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
