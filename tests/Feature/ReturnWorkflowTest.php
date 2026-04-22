<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReturnWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_return_for_delivered_order_and_duplicate_is_blocked(): void
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

        $this->actingAs($user);

        $this->postJson(route('user.orders.returns.store', $order), [
            'reason' => 'Product taste is not suitable for my child.',
        ])->assertCreated();

        $this->assertDatabaseCount('order_returns', 1);

        $this->postJson(route('user.orders.returns.store', $order), [
            'reason' => 'Trying duplicate return request.',
        ])->assertStatus(422);

        $this->assertDatabaseCount('order_returns', 1);
    }
}
