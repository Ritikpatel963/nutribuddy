<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AdminOrderCustomerLinkTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
        $this->app[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function test_order_page_links_the_customer_name_for_admins_who_can_view_customers(): void
    {
        $this->withoutVite();
        $customer = User::factory()->create(['role' => 'customer']);
        $order = Order::factory()->create(['user_id' => $customer->id, 'customer_name' => 'Anita Verma']);
        $link = route('admin.ecommerce.customers.show', $customer->id);

        $this->actingAs($this->adminWith(['view orders', 'view customers']), 'admin')
            ->get(route('admin.ecommerce.orders.show', $order))
            ->assertOk()
            ->assertSee('Anita Verma')
            ->assertSee('href="'.$link.'"', false);
    }

    public function test_order_page_shows_plain_text_without_customer_permission_or_for_guest_orders(): void
    {
        $this->withoutVite();
        $customer = User::factory()->create(['role' => 'customer']);
        $linked = Order::factory()->create(['user_id' => $customer->id, 'customer_name' => 'Anita Verma']);
        $guest = Order::factory()->create(['user_id' => null, 'customer_name' => 'Guest Buyer']);
        $link = route('admin.ecommerce.customers.show', $customer->id);

        $viewer = $this->adminWith(['view orders']);
        $this->actingAs($viewer, 'admin')
            ->get(route('admin.ecommerce.orders.show', $linked))
            ->assertOk()
            ->assertSee('Anita Verma')
            ->assertDontSee('href="'.$link.'"', false);

        $this->actingAs($this->adminWith(['view orders', 'view customers']), 'admin')
            ->get(route('admin.ecommerce.orders.show', $guest))
            ->assertOk()
            ->assertSee('Guest Buyer');
    }

    public function test_order_details_shows_payment_transaction_id(): void
    {
        $this->withoutVite();
        $order = Order::factory()->create([
            'order_number' => 'NB202608134XDBKZ',
            'payment_method' => 'cashfree',
            'payment_status' => 'paid',
            'customer_name' => 'Ritik Haldkar',
        ]);

        Payment::create([
            'order_id' => $order->id,
            'provider' => 'cashfree',
            'status' => 'paid',
            'amount' => 624.00,
            'currency' => 'INR',
            'gateway_payment_id' => 'CF_PAY_208607201879552',
            'gateway_order_id' => 'NB202608134XDBKZ',
            'paid_at' => now(),
        ]);

        $this->actingAs($this->adminWith(['view orders']), 'admin')
            ->get(route('admin.ecommerce.orders.show', $order))
            ->assertOk()
            ->assertSee('Payment Txn ID:')
            ->assertSee('CF_PAY_208607201879552')
            ->assertSee('Payment Transactions');
    }

    public function test_admin_can_filter_orders_by_date_and_export_excel(): void
    {
        $this->withoutVite();

        $oldOrder = Order::factory()->create([
            'order_number' => 'NB-OLD-001',
            'created_at' => '2026-08-01 10:00:00',
        ]);

        $recentOrder = Order::factory()->create([
            'order_number' => 'NB-RECENT-002',
            'created_at' => '2026-11-20 12:00:00',
        ]);

        $admin = $this->adminWith(['view orders']);

        // 1. Filter by date on index page
        $filterResponse = $this->actingAs($admin, 'admin')->get(route('admin.ecommerce.orders.index', [
            'from_date' => '2026-11-01',
            'to_date' => '2026-11-28',
        ]));
        $filterResponse->assertOk();
        $filterResponse->assertSee('NB-RECENT-002');
        $filterResponse->assertDontSee('NB-OLD-001');

        // 2. Export orders to Excel
        $exportResponse = $this->actingAs($admin, 'admin')->get(route('admin.ecommerce.orders.export', [
            'from_date' => '2026-11-01',
            'to_date' => '2026-11-28',
        ]));
        $exportResponse->assertOk();
        $this->assertStringContainsString('.xlsx', $exportResponse->headers->get('content-disposition') ?? '');
    }

    /** @param list<string> $permissions */
    private function adminWith(array $permissions): User
    {
        foreach (['view orders', 'manage orders', 'view customers', 'manage customers'] as $name) {
            Permission::findOrCreate($name, 'admin');
        }

        $role = Role::create(['name' => 'Role '.uniqid(), 'guard_name' => 'admin']);
        $role->givePermissionTo($permissions);
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $admin->assignRole($role);

        return $admin;
    }
}
