<?php

namespace Tests\Feature;

use App\Mail\DietChartApprovedCustomerMail;
use App\Mail\DietSubscriptionActivatedCustomerMail;
use App\Mail\DietSubscriptionCancelledCustomerMail;
use App\Mail\DietSubscriptionExpiringCustomerMail;
use App\Mail\DietSubscriptionPaymentFailedMail;
use App\Mail\DietSubscriptionRenewalSuccessMail;
use App\Mail\LowStockAlertMail;
use App\Mail\OrderPlacedAdminMail;
use App\Mail\OrderPlacedCustomerMail;
use App\Mail\OrderStatusUpdatedMail;
use App\Mail\PasswordChangedCustomerMail;
use App\Mail\PasswordResetCustomerMail;
use App\Mail\ReturnRequestCustomerMail;
use App\Mail\ReturnStatusUpdatedMail;
use App\Mail\WelcomeCustomerMail;
use App\Models\DietSubscription;
use App\Models\Nutrition\Child;
use App\Models\Nutrition\ChildAssessment;
use App\Models\Nutrition\DietPlan;
use App\Models\NutritionLead;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderReturn;
use App\Models\Product;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComprehensiveEmailLifecycleTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;
    private Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::factory()->create([
            'name' => 'Aditi Sharma',
            'email' => 'aditi@example.com',
            'phone' => '9876543210',
            'role' => 'customer',
        ]);

        $this->order = Order::create([
            'order_number' => 'NB-TEST-1001',
            'user_id' => $this->customer->id,
            'customer_name' => 'Aditi Sharma',
            'customer_email' => 'aditi@example.com',
            'customer_phone' => '9876543210',
            'shipping_name' => 'Aditi Sharma',
            'shipping_phone' => '9876543210',
            'shipping_address_line_1' => 'Flat 402, Sunshine Apartments',
            'shipping_city' => 'Mumbai',
            'shipping_state' => 'Maharashtra',
            'shipping_postal_code' => '400001',
            'shipping_country' => 'India',
            'subtotal' => 1998.00,
            'shipping_total' => 0.00,
            'discount_total' => 100.00,
            'coin_discount' => 50.00,
            'coins_redeemed' => 50,
            'tax_total' => 0.00,
            'grand_total' => 1848.00,
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'status' => 'confirmed',
            'fulfillment_status' => 'unfulfilled',
        ]);

        $product = Product::create([
            'name' => 'NutriBuddy Super Mix Gummies',
            'slug' => 'super-mix-gummies',
            'sku' => 'NBD-GUMMY-01',
            'price' => 999.00,
            'stock_quantity' => 20,
            'is_active' => true,
        ]);

        OrderItem::create([
            'order_id' => $this->order->id,
            'product_id' => $product->id,
            'sku' => 'NBD-GUMMY-01',
            'product_name' => 'NutriBuddy Super Mix Gummies',
            'quantity' => 2,
            'unit_price' => 999.00,
            'line_total' => 1998.00,
            'item_snapshot' => ['variant_name' => 'Strawberry (Pack of 2)'],
        ]);
    }

    public function test_product_order_placed_customer_mail_renders(): void
    {
        $this->order->load('items');
        $mailable = new OrderPlacedCustomerMail($this->order);
        $rendered = $mailable->render();

        $this->assertStringContainsString('NB-TEST-1001', $rendered);
        $this->assertStringContainsString('Aditi Sharma', $rendered);
        $this->assertStringContainsString('NutriBuddy Super Mix Gummies', $rendered);
        $this->assertStringContainsString('Strawberry (Pack of 2)', $rendered);
        $this->assertStringContainsString('1,848.00', $rendered);
        $this->assertStringContainsString('Cash on Delivery', $rendered);
    }

    public function test_product_order_placed_admin_mail_renders(): void
    {
        $this->order->load('items');
        $mailable = new OrderPlacedAdminMail($this->order);
        $rendered = $mailable->render();

        $this->assertStringContainsString('NB-TEST-1001', $rendered);
        $this->assertStringContainsString('Aditi Sharma', $rendered);
        $this->assertStringContainsString('Open Order in Admin Panel', $rendered);
    }

    public function test_product_order_status_updated_mail_renders_across_statuses(): void
    {
        $this->order->status = 'shipped';
        $this->order->awb_code = 'DELHIVERY987654';
        $this->order->save();

        $mailable = new OrderStatusUpdatedMail($this->order);
        $rendered = $mailable->render();

        $this->assertStringContainsString('SHIPPED', $rendered);
        $this->assertStringContainsString('DELHIVERY987654', $rendered);
        $this->assertStringContainsString('Tracking Details Ready', $rendered);
    }

    public function test_product_return_mails_render(): void
    {
        $orderReturn = OrderReturn::create([
            'return_number' => 'RET-2026-001',
            'order_id' => $this->order->id,
            'reason' => 'wrong_item',
            'status' => 'approved',
            'refund_amount' => 999.00,
            'admin_note' => 'Pickup scheduled for tomorrow.',
        ]);

        $orderReturn->setRelation('order', $this->order);

        $customerRequestMail = new ReturnRequestCustomerMail($orderReturn);
        $reqRendered = $customerRequestMail->render();
        $this->assertStringContainsString('RET-2026-001', $reqRendered);
        $this->assertStringContainsString('Wrong item', $reqRendered);

        $statusUpdatedMail = new ReturnStatusUpdatedMail($orderReturn);
        $statusRendered = $statusUpdatedMail->render();
        $this->assertStringContainsString('APPROVED', $statusRendered);
        $this->assertStringContainsString('999.00', $statusRendered);
        $this->assertStringContainsString('Pickup scheduled for tomorrow', $statusRendered);
    }

    public function test_low_stock_alert_mail_renders(): void
    {
        $product = Product::first();
        $mailable = new LowStockAlertMail($product, currentStock: 2, threshold: 5, variantName: '500g Pouch');
        $rendered = $mailable->render();

        $this->assertStringContainsString('Low Stock Warning', $rendered);
        $this->assertStringContainsString($product->name, $rendered);
        $this->assertStringContainsString('500g Pouch', $rendered);
        $this->assertStringContainsString('2 units', $rendered);
    }

    public function test_account_creation_welcome_mail_renders(): void
    {
        $mailable = new WelcomeCustomerMail($this->customer);
        $rendered = $mailable->render();

        $this->assertStringContainsString('Aditi Sharma', $rendered);
        $this->assertStringContainsString('WELCOME TO THE NUTRIBUDDY FAMILY', $rendered);
        $this->assertStringContainsString('Take the Free Pediatric Assessment', $rendered);
        $this->assertStringContainsString('Explore Feel-Good Nutrition', $rendered);
        $this->assertStringContainsString('Explore NutriBuddy', $rendered);
    }

    public function test_account_password_reset_and_changed_mails_render(): void
    {
        $resetMail = new PasswordResetCustomerMail($this->customer, 'https://nutribuddy.com/reset/token123', 60);
        $resetRendered = $resetMail->render();
        $this->assertStringContainsString('https://nutribuddy.com/reset/token123', $resetRendered);
        $this->assertStringContainsString('60 minutes', $resetRendered);

        $changedMail = new PasswordChangedCustomerMail($this->customer);
        $changedRendered = $changedMail->render();
        $this->assertStringContainsString('Password Updated Successfully', $changedRendered);
        $this->assertStringContainsString('Aditi Sharma', $changedRendered);
    }

    public function test_diet_subscription_activated_and_approved_mails_render(): void
    {
        $plan = SubscriptionPlan::create([
            'name' => 'NutriBuddy Pro Plus',
            'slug' => 'pro-plus',
            'price' => 1499.00,
            'is_active' => true,
        ]);

        $child = Child::create([
            'user_id' => $this->customer->id,
            'name' => 'Aarav',
            'sex' => 'boy',
            'dob' => now()->subYears(4),
        ]);

        $subscription = DietSubscription::create([
            'user_id' => $this->customer->id,
            'child_id' => $child->id,
            'subscription_plan_id' => $plan->id,
            'model' => 'pro-plus',
            'status' => 'active',
            'payment_status' => 'paid',
            'amount' => 1499.00,
            'expires_at' => now()->addDays(30),
        ]);

        $subscription->setRelation('user', $this->customer);
        $subscription->setRelation('subscriptionPlan', $plan);
        $subscription->setRelation('child', $child);

        // Test activated mail
        $actMail = new DietSubscriptionActivatedCustomerMail($subscription);
        $actRendered = $actMail->render();
        $this->assertStringContainsString('NutriBuddy Pro Plus', $actRendered);
        $this->assertStringContainsString('Aarav', $actRendered);

        // Test chart approved mail
        $assessment = ChildAssessment::create([
            'child_id' => $child->id,
            'model_level' => 'basic',
            'diet_type' => 'vegetarian',
            'meal_pattern' => 'default',
        ]);

        $dietPlan = DietPlan::create([
            'child_assessment_id' => $assessment->id,
            'version' => 1,
            'status' => 'active',
            'review_status' => 'approved',
            'review_notes' => 'Boost leafy greens and almonds for zinc and iron.',
        ]);

        $lead = NutritionLead::create([
            'user_id' => $this->customer->id,
            'child_id' => $child->id,
            'diet_subscription_id' => $subscription->id,
            'status' => 'completed',
        ]);

        $lead->setRelation('customer', $this->customer);
        $lead->setRelation('child', $child);

        $approvedMail = new DietChartApprovedCustomerMail($lead, $dietPlan);
        $appRendered = $approvedMail->render();
        $this->assertStringContainsString('Aarav', $appRendered);
        $this->assertStringContainsString('Boost leafy greens and almonds', $appRendered);
        $this->assertStringContainsString('View &amp; Download Diet Chart', $appRendered);

        // Test renewal success mail
        $renewalMail = new DietSubscriptionRenewalSuccessMail($subscription, 1499.00);
        $renRendered = $renewalMail->render();
        $this->assertStringContainsString('1,499.00', $renRendered);
        $this->assertStringContainsString('AUTO-RENEWAL SUCCESSFUL', $renRendered);

        // Test payment failed mail
        $failedMail = new DietSubscriptionPaymentFailedMail($subscription, 'Insufficient funds in customer VPA');
        $failRendered = $failedMail->render();
        $this->assertStringContainsString('Insufficient funds in customer VPA', $failRendered);
        $this->assertStringContainsString('Payment Could Not Be Processed', $failRendered);

        // Test expiring soon mail
        $expiringMail = new DietSubscriptionExpiringCustomerMail($subscription, 3);
        $expRendered = $expiringMail->render();
        $this->assertStringContainsString('3 Days Left', $expRendered);

        // Test cancelled mail
        $cancelledMail = new DietSubscriptionCancelledCustomerMail($subscription);
        $cancRendered = $cancelledMail->render();
        $this->assertStringContainsString('Subscription Cancelled', $cancRendered);
    }
}
