<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserOrderInvoiceLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_content_stays_inside_its_themed_user_panel_container(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['status' => 'processing']);

        $response = $this->actingAs($user)->get(route('user.orders.invoice-page', $order));

        $response->assertOk();
        $this->assertMatchesRegularExpression(
            '/<div class="ud-panel-content panel-order panel-order-invoice">\s*<section class="user-panel-page-hero">.*?<div class="page">\s*<div class="inv-wrap/s',
            $response->getContent(),
        );
    }

    public function test_invoice_shows_configured_seller_details_and_the_real_fallback_logo(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create([
            'order_number' => 123456,
            'status' => 'processing',
        ]);

        Setting::set('company_name', 'NutriBuddy Demo Company');
        Setting::set('gst_number', '24AAAAA0000A1Z5');
        Setting::set('pan_number', 'AAAAA0000A');
        Setting::set('cin_number', 'U74999GJ2020PTC123456');

        $response = $this->actingAs($user)->get(route('user.orders.invoice-page', $order));

        $response->assertOk()
            ->assertSee('src="' . asset('img/logo.png') . '"', false)
            ->assertSee('NutriBuddy Demo Company')
            ->assertSee('INV-123456')
            ->assertSee('GSTIN: 24AAAAA0000A1Z5')
            ->assertSee('PAN: AAAAA0000A')
            ->assertSee('CIN: U74999GJ2020PTC123456');
    }
}
