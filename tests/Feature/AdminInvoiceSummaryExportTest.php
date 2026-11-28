<?php

namespace Tests\Feature;

use App\Exports\Invoices\InvoicesRegisterSheet;
use App\Exports\Invoices\InvoicesSummaryExport;
use App\Exports\Invoices\InvoicesSummarySheet;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AdminInvoiceSummaryExportTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app[PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::findOrCreate('view orders', 'admin');
        Permission::findOrCreate('manage orders', 'admin');

        $role = Role::findOrCreate('Order Manager', 'admin');
        $role->givePermissionTo(['view orders', 'manage orders']);

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);
        $this->admin->assignRole($role);
    }

    public function test_invoices_index_renders_date_filters_and_export_button(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.ecommerce.invoices.index'));

        $response->assertOk();
        $response->assertSee('from_date');
        $response->assertSee('to_date');
        $response->assertSee('Download Invoice Summary');
        $response->assertSee(route('admin.ecommerce.invoices.export'));
    }

    public function test_invoices_index_filters_by_date_range(): void
    {
        $oldOrder = Order::factory()->create([
            'order_number' => 'NB-OLD-111',
            'placed_at' => now()->subDays(20),
            'status' => 'delivered',
        ]);

        $inRangeOrder = Order::factory()->create([
            'order_number' => 'NB-INRANGE-222',
            'placed_at' => now()->subDays(5),
            'status' => 'delivered',
        ]);

        $futureOrder = Order::factory()->create([
            'order_number' => 'NB-FUTURE-333',
            'placed_at' => now()->addDays(5),
            'status' => 'processing',
        ]);

        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.ecommerce.invoices.index', [
            'from_date' => now()->subDays(10)->format('Y-m-d'),
            'to_date' => now()->format('Y-m-d'),
        ]));

        $response->assertOk();
        $response->assertSee('NB-INRANGE-222');
        $response->assertDontSee('NB-OLD-111');
        $response->assertDontSee('NB-FUTURE-333');
    }

    public function test_invoices_export_endpoint_downloads_excel(): void
    {
        Order::factory()->create([
            'order_number' => 'NB-EXP-001',
            'placed_at' => now()->subDays(2),
            'status' => 'delivered',
        ]);

        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.ecommerce.invoices.export'));

        $response->assertOk();
        $response->assertDownload();
    }

    public function test_invoices_register_sheet_has_exact_17_required_columns(): void
    {
        $product = Product::factory()->create([
            'name' => 'Brain Booster Gummies',
            'hsn_code' => '21069099',
        ]);

        $order = Order::factory()->create([
            'order_number' => 'NB2026TEST01',
            'invoice_number' => 'INV-2026-001',
            'placed_at' => now(),
            'grand_total' => 1180.00,
            'subtotal' => 1000.00,
            'tax_total' => 180.00,
            'igst_total' => 180.00,
            'cgst_total' => 0.00,
            'sgst_total' => 0.00,
            'customer_name' => 'Rahul Sharma',
            'shipping_name' => 'Rahul Sharma',
            'shipping_address_line_1' => 'Flat 101, Galaxy Heights',
            'shipping_city' => 'Bengaluru',
            'shipping_state' => 'Karnataka',
            'shipping_postal_code' => '560001',
            'shipping_country' => 'India',
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'sku' => 'SKU-BB-001',
            'product_name' => 'Brain Booster Gummies',
            'quantity' => 2,
            'unit_price' => 500.00,
            'line_total' => 1180.00,
            'tax_percent' => 18.00,
            'tax_amount' => 180.00,
        ]);

        $order->load(['items.product', 'items.productVariant']);
        $sheet = new InvoicesRegisterSheet(collect([$order]));
        $data = $sheet->array();

        $expectedHeaders = [
            'Invoice number',
            'Invoice date',
            'Invoice value',
            'HSN number',
            'Goods description',
            'UOM',
            'Quantity',
            'Total value',
            'GST rate',
            'Taxable value',
            'Integrated tax amount',
            'Central tax amount',
            'State tax amount',
            'Customer name',
            'Customer address',
            'Customer GST',
            'Place of supply',
        ];

        $this->assertSame($expectedHeaders, $data[0]);

        // Check the line row values
        $row = $data[1];
        $this->assertSame('INV-2026-001', $row[0]);
        $this->assertSame(now()->format('Y-m-d'), $row[1]);
        $this->assertSame('1180.00', $row[2]);
        $this->assertSame('21069099', $row[3]);
        $this->assertSame('Brain Booster Gummies', $row[4]);
        $this->assertSame('NOS', $row[5]);
        $this->assertSame(2, $row[6]);
        $this->assertSame('1180.00', $row[7]);
        $this->assertSame('18%', $row[8]);
        $this->assertSame('1000.00', $row[9]);
        $this->assertSame('180.00', $row[10]); // IGST
        $this->assertSame('0.00', $row[11]);   // CGST
        $this->assertSame('0.00', $row[12]);   // SGST
        $this->assertSame('Rahul Sharma', $row[13]);
        $this->assertStringContainsString('Galaxy Heights', $row[14]);
        $this->assertSame('URP', $row[15]);
        $this->assertSame('Karnataka', $row[16]);
    }

    public function test_invoices_intrastate_splits_tax_into_cgst_and_sgst(): void
    {
        $order = Order::factory()->create([
            'order_number' => 'NB2026INTRA',
            'invoice_number' => 'INV-2026-002',
            'placed_at' => now(),
            'grand_total' => 590.00,
            'subtotal' => 500.00,
            'tax_total' => 90.00,
            'igst_total' => 0.00,
            'cgst_total' => 45.00,
            'sgst_total' => 45.00,
            'customer_name' => 'Priya Patel',
            'shipping_state' => 'Gujarat',
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'sku' => 'SKU-VITC-001',
            'product_name' => 'Vitamin C Gummies',
            'quantity' => 1,
            'unit_price' => 500.00,
            'line_total' => 590.00,
            'tax_percent' => 18.00,
            'tax_amount' => 90.00,
        ]);

        $order->load(['items.product', 'items.productVariant']);
        $sheet = new InvoicesRegisterSheet(collect([$order]));
        $data = $sheet->array();

        $row = $data[1];
        $this->assertSame('0.00', $row[10]);  // IGST
        $this->assertSame('45.00', $row[11]); // CGST
        $this->assertSame('45.00', $row[12]); // SGST
    }

    public function test_invoices_summary_export_has_multiple_sheets(): void
    {
        $export = new InvoicesSummaryExport(collect());
        $sheets = $export->sheets();

        $this->assertCount(2, $sheets);
        $this->assertInstanceOf(InvoicesSummarySheet::class, $sheets[0]);
        $this->assertInstanceOf(InvoicesRegisterSheet::class, $sheets[1]);
    }
}
