<?php

namespace Tests\Feature;

use App\Models\DietSubscription;
use App\Models\Nutrition\Child;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\Nutrition\DietPaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DietPaymentControllerRegressionTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_start_or_verify_subscription_payment(): void
    {
        $this->postJson(route('diet.payment.create'))->assertUnauthorized();
        $this->postJson(route('diet.payment.verify'))->assertUnauthorized();
    }

    public function test_parent_cannot_start_payment_for_another_users_child(): void
    {
        $parent = User::factory()->create();
        $otherParent = User::factory()->create();
        $child = Child::create(['user_id' => $otherParent->id, 'name' => 'Private child', 'sex' => 'girl']);
        SubscriptionPlan::create([
            'name' => 'Monthly', 'slug' => 'monthly', 'price' => 100,
            'monthly_price' => 100, 'is_active' => true,
        ]);

        $this->actingAs($parent)->postJson(route('diet.payment.create'), [
            'plan_slug' => 'monthly', 'gateway' => 'razorpay', 'cycle' => 'monthly', 'child_id' => $child->id,
        ])->assertForbidden();

        $this->assertDatabaseCount('diet_subscriptions', 0);
    }

    public function test_gateway_failure_does_not_activate_the_subscription_as_free(): void
    {
        config(['services.razorpay.key_id' => 'test-key', 'services.razorpay.key_secret' => 'test-secret']);
        Http::fake(['https://api.razorpay.com/v1/orders' => Http::response(['error' => 'unavailable'], 503)]);
        $parent = User::factory()->create();
        $child = Child::create(['user_id' => $parent->id, 'name' => 'Mira', 'sex' => 'girl']);
        SubscriptionPlan::create([
            'name' => 'Monthly', 'slug' => 'monthly', 'price' => 100,
            'monthly_price' => 100, 'is_active' => true,
        ]);

        $this->actingAs($parent)->postJson(route('diet.payment.create'), [
            'plan_slug' => 'monthly', 'gateway' => 'razorpay', 'cycle' => 'monthly', 'child_id' => $child->id,
        ])->assertStatus(502)->assertJson([
            'success' => false,
            'message' => 'Unable to process payment at this time.',
        ]);

        $this->assertDatabaseHas('diet_subscriptions', [
            'user_id' => $parent->id,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);
    }

    public function test_missing_cycle_price_cannot_create_a_free_subscription(): void
    {
        $parent = User::factory()->create();
        $child = Child::create(['user_id' => $parent->id, 'name' => 'Mira', 'sex' => 'girl']);
        SubscriptionPlan::create([
            'name' => 'Monthly', 'slug' => 'monthly', 'price' => 100,
            'monthly_price' => null, 'is_active' => true,
        ]);

        $this->actingAs($parent)->postJson(route('diet.payment.create'), [
            'plan_slug' => 'monthly', 'gateway' => 'razorpay', 'cycle' => 'monthly', 'child_id' => $child->id,
        ])->assertUnprocessable()->assertJsonPath('success', false);

        $this->assertDatabaseCount('diet_subscriptions', 0);
    }

    public function test_inactive_subscription_plan_cannot_be_purchased_directly(): void
    {
        $parent = User::factory()->create();
        $child = Child::create(['user_id' => $parent->id, 'name' => 'Mira', 'sex' => 'girl']);
        SubscriptionPlan::create([
            'name' => 'Monthly', 'slug' => 'monthly', 'price' => 100,
            'monthly_price' => 100, 'is_active' => false,
        ]);

        $this->actingAs($parent)->postJson(route('diet.payment.create'), [
            'plan_slug' => 'monthly', 'gateway' => 'razorpay', 'cycle' => 'monthly', 'child_id' => $child->id,
        ])->assertNotFound();

        $this->assertDatabaseCount('diet_subscriptions', 0);
    }

    public function test_razorpay_subscription_requires_a_matching_captured_payment_and_starts_term_on_payment(): void
    {
        config([
            'services.razorpay.key_id' => 'test-key',
            'services.razorpay.key_secret' => 'test-secret',
        ]);
        Http::fake(['https://api.razorpay.com/v1/payments/pay_diet_123' => Http::response([
            'order_id' => 'order_diet_123',
            'amount' => 10000,
            'currency' => 'INR',
            'status' => 'captured',
        ])]);
        $parent = User::factory()->create();
        $child = Child::create(['user_id' => $parent->id, 'name' => 'Mira', 'sex' => 'girl']);
        $subscription = DietSubscription::create([
            'user_id' => $parent->id,
            'child_id' => $child->id,
            'model' => 'Monthly (Monthly)',
            'status' => 'pending',
            'payment_status' => 'pending',
            'amount_paid' => 100,
            'razorpay_order_id' => 'order_diet_123',
            'starts_at' => now()->subDays(35),
            'expires_at' => now()->subDays(5),
        ]);
        $signature = hash_hmac('sha256', 'order_diet_123|pay_diet_123', 'test-secret');
        $payload = [
            'subscription_id' => $subscription->id,
            'razorpay_order_id' => 'order_diet_123',
            'razorpay_payment_id' => 'pay_diet_123',
            'razorpay_signature' => $signature,
        ];

        $this->actingAs($parent)->postJson(route('diet.payment.verify'), $payload)
            ->assertOk()->assertJsonPath('success', true);

        $subscription->refresh();
        $this->assertSame('active', $subscription->status);
        $this->assertSame('paid', $subscription->payment_status);
        $this->assertTrue($subscription->starts_at->greaterThan(now()->subMinute()));
        $this->assertEquals(30, $subscription->starts_at->diffInDays($subscription->expires_at));
        $initialExpiry = $subscription->expires_at->toDateTimeString();

        $this->actingAs($parent)->postJson(route('diet.payment.verify'), $payload)->assertOk();
        $this->assertSame($initialExpiry, $subscription->fresh()->expires_at->toDateTimeString());
    }

    public function test_authorized_but_uncaptured_razorpay_payment_does_not_activate_subscription(): void
    {
        config([
            'services.razorpay.key_id' => 'test-key',
            'services.razorpay.key_secret' => 'test-secret',
        ]);
        Http::fake(['https://api.razorpay.com/v1/payments/pay_diet_456' => Http::response([
            'order_id' => 'order_diet_456',
            'amount' => 10000,
            'currency' => 'INR',
            'status' => 'authorized',
        ])]);
        $parent = User::factory()->create();
        $child = Child::create(['user_id' => $parent->id, 'name' => 'Mira', 'sex' => 'girl']);
        $subscription = DietSubscription::create([
            'user_id' => $parent->id,
            'child_id' => $child->id,
            'model' => 'Monthly (Monthly)',
            'status' => 'pending',
            'payment_status' => 'pending',
            'amount_paid' => 100,
            'razorpay_order_id' => 'order_diet_456',
        ]);

        $this->actingAs($parent)->postJson(route('diet.payment.verify'), [
            'subscription_id' => $subscription->id,
            'razorpay_order_id' => 'order_diet_456',
            'razorpay_payment_id' => 'pay_diet_456',
            'razorpay_signature' => hash_hmac('sha256', 'order_diet_456|pay_diet_456', 'test-secret'),
        ])->assertBadRequest();

        $this->assertSame('pending', $subscription->fresh()->status);
        $this->assertSame('pending', $subscription->fresh()->payment_status);
    }

    public function test_cashfree_return_rejects_paid_order_with_mismatched_amount(): void
    {
        config([
            'services.cashfree.app_id' => 'test-app',
            'services.cashfree.secret_key' => 'test-secret',
        ]);
        Http::fake(['https://sandbox.cashfree.com/pg/orders/cashfree_diet_123' => Http::response([
            'order_id' => 'cashfree_diet_123',
            'order_amount' => 1,
            'order_currency' => 'INR',
            'order_status' => 'PAID',
        ])]);
        $parent = User::factory()->create();
        $child = Child::create(['user_id' => $parent->id, 'name' => 'Mira', 'sex' => 'girl']);
        $subscription = DietSubscription::create([
            'user_id' => $parent->id,
            'child_id' => $child->id,
            'model' => 'Monthly (Monthly)',
            'status' => 'pending',
            'payment_status' => 'pending',
            'amount_paid' => 100,
            'razorpay_order_id' => 'cashfree_diet_123',
        ]);

        $result = app(DietPaymentService::class)->confirmCashfreeReturn('cashfree_diet_123', (string) $subscription->id);

        $this->assertFalse($result['success']);
        $this->assertSame('pending', $subscription->fresh()->status);
        $this->assertSame('pending', $subscription->fresh()->payment_status);
    }
}
