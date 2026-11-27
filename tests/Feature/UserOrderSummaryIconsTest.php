<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserOrderSummaryIconsTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_summary_uses_accessible_svg_icons_for_each_stat(): void
    {
        $response = $this->actingAs(User::factory()->create())->get(route('order'));

        $response->assertOk();
        $response->assertSee('Total Orders');
        $response->assertSee('Pending');
        $response->assertSee('Delivered');
        $response->assertSee('Cancelled');
        $this->assertSame(4, substr_count($response->getContent(), 'class="order-stat-icon"'));
    }
}
