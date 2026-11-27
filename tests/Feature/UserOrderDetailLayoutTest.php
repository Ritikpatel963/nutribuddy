<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserOrderDetailLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_detail_content_stays_inside_the_user_panel_theme_container(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['status' => 'processing']);

        $response = $this->actingAs($user)->get(route('user.orders.detail-page', $order));

        $response->assertOk();
        $this->assertMatchesRegularExpression(
            '/<div class="ud-panel-content panel-order">\s*<section class="user-panel-page-hero">.*?<div class="page">/s',
            $response->getContent(),
        );
    }
}
