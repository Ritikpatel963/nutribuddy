<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderReturn;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserReturnsIconsTest extends TestCase
{
    use RefreshDatabase;

    public function test_empty_returns_page_uses_svg_icons(): void
    {
        $response = $this->actingAs(User::factory()->create())->get(route('user.orders.returns.index'));

        $response->assertOk()
            ->assertSee('No return requests yet')
            ->assertSee('class="return-page-icon"', false);
        $this->assertSame(2, substr_count($response->getContent(), 'class="return-page-icon"'));
    }

    public function test_return_progress_uses_svg_icons_for_statuses_and_sections(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create();
        OrderReturn::create([
            'order_id' => $order->id,
            'return_number' => 'RET-TEST-001',
            'reason' => 'Damaged item',
            'status' => 'pending',
            'refund_amount' => 250,
        ]);

        $response = $this->actingAs($user)->get(route('user.orders.returns.index'));

        $response->assertOk()
            ->assertSee('Return Progress')
            ->assertSee('Pickup Scheduled')
            ->assertSee('class="return-page-icon"', false);
        $this->assertSame(13, substr_count($response->getContent(), 'class="return-page-icon"'));
    }
}
