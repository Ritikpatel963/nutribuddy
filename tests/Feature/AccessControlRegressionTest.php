<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AccessControlRegressionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function test_admin_user_role_changes_are_persisted_with_the_user(): void
    {
        $admin = $this->adminWithUserManagementPermission();
        Role::create(['name' => 'Content Reviewer', 'guard_name' => 'admin']);
        Role::create(['name' => 'Operations Manager', 'guard_name' => 'admin']);

        $this->actingAs($admin, 'admin')
            ->postJson(route('admin.access-control.users.store'), [
                'name' => 'Taylor Admin',
                'email' => 'taylor@example.test',
                'password' => 'secure-password',
                'role' => 'Content Reviewer',
                'status' => '1',
            ])
            ->assertOk()
            ->assertExactJson(['success' => true, 'message' => 'User created successfully.']);

        $created = User::where('email', 'taylor@example.test')->firstOrFail();
        $this->assertSame('admin', $created->role);
        $this->assertTrue($created->is_active);
        $this->assertTrue($created->hasRole('Content Reviewer', 'admin'));

        $this->putJson(route('admin.access-control.users.update', $created), [
            'name' => 'Taylor Operations',
            'email' => 'taylor@example.test',
            'role' => 'Operations Manager',
            'status' => '0',
        ])
            ->assertOk()
            ->assertExactJson(['success' => true, 'message' => 'User updated successfully.']);

        $created->refresh();
        $this->assertSame('Taylor Operations', $created->name);
        $this->assertFalse($created->is_active);
        $this->assertTrue($created->hasRole('Operations Manager', 'admin'));
        $this->assertFalse($created->hasRole('Content Reviewer', 'admin'));
    }

    public function test_admin_account_cannot_be_assigned_a_role_from_another_guard(): void
    {
        $admin = $this->adminWithUserManagementPermission();
        Role::create(['name' => 'Customer', 'guard_name' => 'web']);

        $this->actingAs($admin, 'admin')
            ->postJson(route('admin.access-control.users.store'), [
                'name' => 'Invalid Role User',
                'email' => 'invalid-role@example.test',
                'password' => 'secure-password',
                'role' => 'Customer',
                'status' => '1',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('role');

        $this->assertDatabaseMissing('users', ['email' => 'invalid-role@example.test']);
    }

    private function adminWithUserManagementPermission(): User
    {
        Permission::create(['name' => 'manage users', 'guard_name' => 'admin']);
        $role = Role::create(['name' => 'User Manager', 'guard_name' => 'admin']);
        $role->givePermissionTo('manage users');
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $admin->assignRole($role);

        return $admin;
    }
}
