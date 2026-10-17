<?php

namespace Tests\Feature;

use App\Models\Nutrition\Child;
use App\Models\SubscriptionPlan;
use App\Models\User;
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
}
