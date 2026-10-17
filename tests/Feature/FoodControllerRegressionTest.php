<?php

namespace Tests\Feature;

use App\Models\Food;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class FoodControllerRegressionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app[PermissionRegistrar::class]->forgetCachedPermissions();
        Permission::create(['name' => 'view diet chart', 'guard_name' => 'admin']);
        $role = Role::create(['name' => 'Food Viewer', 'guard_name' => 'admin']);
        $role->givePermissionTo('view diet chart');
        $this->actingAs(User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ])->assignRole($role), 'admin');
    }

    public function test_food_data_endpoint_preserves_datatables_response_shape(): void
    {
        Food::create([
            'dish_id' => 'TEST-001',
            'dish_name' => 'Oatmeal',
            'food_group' => 'Cereal',
            'slot' => 'Breakfast',
            'diet_type' => 'Vegetarian',
            'is_veg' => 'Yes',
            'region' => 'North',
            'energy_kcal' => 150,
        ]);

        $this->getJson(route('admin.nutrition.foods.data', [
            'draw' => 3,
            'start' => 0,
            'length' => 10,
            'order' => [['column' => 1, 'dir' => 'asc']],
        ]))
            ->assertOk()
            ->assertJsonPath('draw', 3)
            ->assertJsonPath('recordsTotal', 1)
            ->assertJsonPath('recordsFiltered', 1)
            ->assertJsonPath('data.0.dish_name', 'Oatmeal')
            ->assertJsonPath('data.0.is_veg', '<span class="badge bg-success">Yes</span>');
    }

    public function test_food_data_rejects_unbounded_order_columns(): void
    {
        $this->getJson(route('admin.nutrition.foods.data', [
            'order' => [['column' => 80, 'dir' => 'asc']],
        ]))->assertUnprocessable()->assertJsonValidationErrors('order.0.column');
    }
}
