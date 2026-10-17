<?php

namespace App\Services\Access;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSyncService
{
    public function sync(Role $role, mixed $permissionsArray): void
    {
        $permissions = [];
        if (is_array($permissionsArray)) {
            foreach ($permissionsArray as $module => $levels) {
                if (isset($levels['view']) && $levels['view'] == '1') {
                    $permissions[] = 'view ' . $module;
                }
                if (isset($levels['manage']) && $levels['manage'] == '1') {
                    $permissions[] = 'manage ' . $module;
                    if (! in_array('view ' . $module, $permissions, true)) {
                        $permissions[] = 'view ' . $module;
                    }
                }
            }
        }

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'admin']);
        }

        $role->syncPermissions($permissions);
    }
}
