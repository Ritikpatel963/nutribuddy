<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create basic permissions based on the sidebar sections
        $permissions = [
            'view analytics',
            'manage diet chart',
            'manage products',
            'manage orders',
            'manage customers',
            'manage promotions',
            'manage loyalty',
            'manage moderation',
            'manage administration',
            'manage content',
            'manage assessment',
            'manage roles', // Added for the new Role & Access section
            'manage users', // Added for the new Users section
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'admin']);
        }

        // Create roles and assign created permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'admin']);
        $superAdminRole->syncPermissions(Permission::where('guard_name', 'admin')->get());
        
        $editorRole = Role::firstOrCreate(['name' => 'Editor', 'guard_name' => 'admin']);
        $editorRole->syncPermissions(['manage products', 'manage content', 'manage orders']);

        // Assign 'Super Admin' role to user ID 1
        $user = User::find(1);
        if ($user) {
            $user->assignRole($superAdminRole);
        } else {
            // Optional: Create a default admin user if one doesn't exist
            // This is just a fallback to ensure we don't lock ourselves out
            $admin = User::firstOrCreate([
                'email' => 'admin@nutribuddy.com',
            ], [
                'name' => 'Admin',
                'password' => bcrypt('password'),
                'is_active' => true,
            ]);
            $admin->assignRole($superAdminRole);
        }
    }
}
