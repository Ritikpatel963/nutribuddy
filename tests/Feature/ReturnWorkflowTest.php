<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReturnWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_quantity_based_return_and_over_return_is_blocked(): void
    {
        $this->withoutMiddleware(ValidateCsrfToken::class);

        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'delivered',
            'fulfillment_status' => 'fulfilled',
            'payment_method' => 'cod',
            'payment_status' => 'paid',
        ]);
        $item = OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_name' => 'Nutri Gummies',
            'sku' => 'NG-001',
            'quantity' => 3,
            'unit_price' => 100,
            'line_total' => 300,
        ]);

        $this->actingAs($user);

        $this->postJson(route('user.orders.returns.store', $order), [
            'reason' => 'Product taste is not suitable for my child.',
            'items' => [
                ['order_item_id' => $item->id, 'quantity' => 2],
            ],
        ])->assertCreated();

        $this->assertDatabaseCount('order_returns', 1);
        $this->assertDatabaseHas('order_return_items', [
            'order_item_id' => $item->id,
            'quantity' => 2,
        ]);

        $this->postJson(route('user.orders.returns.store', $order), [
            'reason' => 'Trying to return more than remaining quantity.',
            'items' => [
                ['order_item_id' => $item->id, 'quantity' => 2],
            ],
        ])->assertStatus(422);

        $this->assertDatabaseCount('order_returns', 1);
    }

    public function test_user_can_return_remaining_quantity_in_second_request(): void
    {
        $this->withoutMiddleware(ValidateCsrfToken::class);

        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'delivered',
            'fulfillment_status' => 'fulfilled',
            'payment_method' => 'cod',
            'payment_status' => 'paid',
        ]);
        $item = OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_name' => 'Nutri Gummies',
            'sku' => 'NG-001',
            'quantity' => 3,
            'unit_price' => 100,
            'line_total' => 300,
        ]);

        $this->actingAs($user);

        $this->postJson(route('user.orders.returns.store', $order), [
            'reason' => 'First partial return request.',
            'items' => [
                ['order_item_id' => $item->id, 'quantity' => 2],
            ],
        ])->assertCreated();

        $this->postJson(route('user.orders.returns.store', $order), [
            'reason' => 'Returning remaining quantity now.',
            'items' => [
                ['order_item_id' => $item->id, 'quantity' => 1],
            ],
        ])->assertCreated();

        $this->assertDatabaseCount('order_returns', 2);
        $this->assertDatabaseHas('order_return_items', [
            'order_item_id' => $item->id,
            'quantity' => 1,
        ]);
    }
}
