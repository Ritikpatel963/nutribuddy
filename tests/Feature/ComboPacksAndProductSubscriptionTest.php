<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\CustomerAddress;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductComboItem;
use App\Models\ProductSubscription;
use App\Models\ProductSubscriptionPlan;
use App\Models\ProductVariant;
use App\Models\TaxRate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ComboPacksAndProductSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Category $category;

    protected TaxRate $taxRate;

    protected function setUp(): void
    {
        parent::setUp();

        $this->taxRate = TaxRate::create([
            'name' => 'GST 18%',
            'code' => 'GST18',
            'rate' => 18.00,
            'is_active' => true,
            'show_in_checkout' => true,
        ]);

        $this->category = Category::create([
            'name' => 'Kids Wellness',
            'slug' => 'kids-wellness',
            'is_active' => true,
        ]);

        $this->user = User::factory()->create([
            'name' => 'Parent User',
            'email' => 'parent@nutribuddy.in',
            'phone' => '9876543210',
        ]);

        Cache::flush();
    }

    /**
     * Test 1: Catalog filter for individual products vs combo packs.
     */
    public function test_catalog_tabs_filter_individual_and_combo_products(): void
    {
        $singleProduct = Product::create([
            'category_id' => $this->category->id,
            'tax_rate_id' => $this->taxRate->id,
            'name' => 'Brain Booster Single Gummies',
            'slug' => 'brain-booster-single-gummies',
            'sku' => 'NBD-BB-01',
            'base_price' => 649.00,
            'is_active' => true,
            'is_combo' => false,
        ]);

        $comboProduct = Product::create([
            'category_id' => $this->category->id,
            'tax_rate_id' => $this->taxRate->id,
            'name' => 'Exam Ready Super Combo',
            'slug' => 'exam-ready-super-combo',
            'sku' => 'NBD-COMBO-01',
            'base_price' => 1199.00,
            'is_active' => true,
            'is_combo' => true,
            'combo_tagline' => 'Brain Booster + Tasty Kadha',
            'combo_badge' => 'Best Value Bundle',
        ]);

        $this->assertFalse((bool) $singleProduct->is_combo);
        $this->assertTrue((bool) $comboProduct->is_combo);

        // Default / All tabs
        $responseAll = $this->get(route('product'));
        $responseAll->assertStatus(200);
        $responseAll->assertSee('Brain Booster Single Gummies');
        $responseAll->assertSee('Exam Ready Super Combo');

        // Single Products tab
        $responseSingle = $this->get('/product?type=individual');
        $responseSingle->assertStatus(200);
        $productsInSingle = $responseSingle->viewData('products');
        $this->assertTrue($productsInSingle->contains('name', 'Brain Booster Single Gummies'));
        $this->assertFalse($productsInSingle->contains('name', 'Exam Ready Super Combo'));

        // Combo Packs tab
        $responseCombo = $this->get('/product?type=combos');
        $responseCombo->assertStatus(200);
        $productsInCombo = $responseCombo->viewData('products');
        $this->assertTrue($productsInCombo->contains('name', 'Exam Ready Super Combo'));
        $this->assertFalse($productsInCombo->contains('name', 'Brain Booster Single Gummies'));
    }

    /**
     * Test 2: Virtual Bundle - Purchasing a combo decrements child variant inventories.
     */
    public function test_purchasing_combo_pack_decrements_child_variant_stock(): void
    {
        // Child 1: Brain Booster
        $childProduct1 = Product::create([
            'category_id' => $this->category->id,
            'tax_rate_id' => $this->taxRate->id,
            'name' => 'Brain Booster 30s',
            'slug' => 'bb-30s',
            'sku' => 'CHILD-BB',
            'base_price' => 649.00,
            'is_active' => true,
            'is_combo' => false,
        ]);
        $childVariant1 = ProductVariant::create([
            'product_id' => $childProduct1->id,
            'name' => '30 Gummies',
            'sku' => 'VAR-BB',
            'price' => 649.00,
            'is_active' => true,
            'is_default' => true,
        ]);
        $inv1 = Inventory::create([
            'product_id' => $childProduct1->id,
            'product_variant_id' => $childVariant1->id,
            'stock_qty' => 50,
            'track_stock' => true,
            'is_in_stock' => true,
        ]);

        // Child 2: Tasty Kadha
        $childProduct2 = Product::create([
            'category_id' => $this->category->id,
            'tax_rate_id' => $this->taxRate->id,
            'name' => 'Tasty Kadha 30s',
            'slug' => 'kadha-30s',
            'sku' => 'CHILD-KD',
            'base_price' => 499.00,
            'is_active' => true,
            'is_combo' => false,
        ]);
        $childVariant2 = ProductVariant::create([
            'product_id' => $childProduct2->id,
            'name' => '30 Gummies',
            'sku' => 'VAR-KD',
            'price' => 499.00,
            'is_active' => true,
            'is_default' => true,
        ]);
        $inv2 = Inventory::create([
            'product_id' => $childProduct2->id,
            'product_variant_id' => $childVariant2->id,
            'stock_qty' => 30,
            'track_stock' => true,
            'is_in_stock' => true,
        ]);

        // Combo Product
        $combo = Product::create([
            'category_id' => $this->category->id,
            'tax_rate_id' => $this->taxRate->id,
            'name' => 'Exam Bundle Pack',
            'slug' => 'exam-bundle-pack',
            'sku' => 'COMBO-MAIN',
            'base_price' => 999.00,
            'is_active' => true,
            'is_combo' => true,
        ]);
        $comboVariant = ProductVariant::create([
            'product_id' => $combo->id,
            'name' => 'Default Bundle',
            'sku' => 'VAR-COMBO',
            'price' => 999.00,
            'is_active' => true,
            'is_default' => true,
        ]);

        // Map Combo Items: 1x Brain Booster + 2x Tasty Kadha
        ProductComboItem::create([
            'combo_product_id' => $combo->id,
            'child_variant_id' => $childVariant1->id,
            'quantity' => 1,
        ]);
        ProductComboItem::create([
            'combo_product_id' => $combo->id,
            'child_variant_id' => $childVariant2->id,
            'quantity' => 2,
        ]);

        // Create an Order for 2 combo units
        $order = Order::create([
            'order_number' => 'ORD-COMBO-TEST',
            'user_id' => $this->user->id,
            'customer_name' => $this->user->name,
            'customer_email' => $this->user->email,
            'customer_phone' => $this->user->phone,
            'shipping_name' => $this->user->name,
            'shipping_phone' => $this->user->phone,
            'shipping_address_line_1' => 'Plot 42, Green Park',
            'shipping_city' => 'Jaipur',
            'shipping_state' => 'Rajasthan',
            'shipping_postal_code' => '302001',
            'shipping_country' => 'India',
            'status' => 'processing',
            'subtotal' => 1998.00,
            'grand_total' => 1998.00,
        ]);

        // Create an Order for 2 combo units (OrderItemObserver automatically calls deductStock on created)
        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $combo->id,
            'product_variant_id' => $comboVariant->id,
            'product_name' => $combo->name,
            'sku' => $comboVariant->sku,
            'unit_price' => 999.00,
            'quantity' => 2,
            'subtotal' => 1998.00,
            'total' => 1998.00,
        ]);

        // Child 1 should decrement by 2 * 1 = 2 (50 -> 48)
        $inv1->refresh();
        $this->assertEquals(48, $inv1->stock_qty);

        // Child 2 should decrement by 2 * 2 = 4 (30 -> 26)
        $inv2->refresh();
        $this->assertEquals(26, $inv2->stock_qty);
    }

    /**
     * Test 3: Dedicated Subscribe & Save checkout loads with database plan calculation.
     */
    public function test_subscribe_checkout_loads_plan_and_calculates_savings(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'tax_rate_id' => $this->taxRate->id,
            'name' => 'Brain Booster',
            'slug' => 'brain-booster',
            'sku' => 'NBD-BB-SUB',
            'base_price' => 649.00,
            'compare_at_price' => 699.00,
            'is_active' => true,
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'name' => '30 Gummies',
            'sku' => 'VAR-BB-SUB',
            'price' => 649.00,
            'compare_at_price' => 699.00,
            'is_active' => true,
            'is_default' => true,
        ]);

        $giftVariant = ProductVariant::create([
            'product_id' => $product->id,
            'name' => 'Free Kadha 15s',
            'sku' => 'GIFT-KADHA',
            'price' => 299.00,
            'is_active' => true,
        ]);

        $plan = ProductSubscriptionPlan::create([
            'product_id' => $product->id,
            'duration_months' => 4,
            'month_1_price' => 649.00,
            'recurring_price' => 569.00,
            'badge_text' => 'Best value',
            'free_gift_variant_id' => $giftVariant->id,
            'free_gift_label' => 'Free Tasty Kadha',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($this->user)->get(route('subscribe.checkout', [
            'plan_id' => $plan->id,
            'variant_id' => $variant->id,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Subscribe &amp; Save Checkout', false);
        $response->assertSee('Brain Booster');
        $response->assertSee('4 Months Plan');
        $response->assertSee('Free Tasty Kadha');
        $response->assertSee('2,356.00'); // 649 + (3 * 569) = 2356
    }

    /**
     * Test 4: Webhook fulfillment creates standard order and handles replay idempotency.
     */
    public function test_subscription_webhook_fulfills_order_with_free_gift_and_idempotency(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'tax_rate_id' => $this->taxRate->id,
            'name' => 'Immunity Booster',
            'slug' => 'immunity-booster',
            'sku' => 'NBD-IMM',
            'base_price' => 649.00,
            'is_active' => true,
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'name' => '60 Gummies',
            'sku' => 'VAR-IMM-60',
            'price' => 649.00,
            'is_active' => true,
            'is_default' => true,
        ]);

        $giftVariant = ProductVariant::create([
            'product_id' => $product->id,
            'name' => 'Free Shaker Bottle',
            'sku' => 'GIFT-SHAKER',
            'price' => 199.00,
            'is_active' => true,
        ]);

        $plan = ProductSubscriptionPlan::create([
            'product_id' => $product->id,
            'duration_months' => 3,
            'month_1_price' => 649.00,
            'recurring_price' => 579.00,
            'badge_text' => 'Recommended',
            'free_gift_variant_id' => $giftVariant->id,
            'free_gift_label' => 'Free Shaker Bottle',
            'is_active' => true,
        ]);

        $address = CustomerAddress::create([
            'user_id' => $this->user->id,
            'full_name' => 'Parent User',
            'phone' => '9876543210',
            'address_line_1' => 'Plot 42, Green Park',
            'city' => 'Jaipur',
            'state' => 'Rajasthan',
            'postal_code' => '302001',
            'country' => 'India',
        ]);

        $subscription = ProductSubscription::create([
            'user_id' => $this->user->id,
            'plan_id' => $plan->id,
            'product_id' => $product->id,
            'variant_id' => $variant->id,
            'address_id' => $address->id,
            'shipping_address' => [
                'name' => $address->name,
                'phone' => $address->phone,
                'address_line_1' => $address->address_line_1,
                'city' => $address->city,
                'state' => $address->state,
                'postal_code' => $address->postal_code,
                'country' => 'India',
            ],
            'cf_sub_reference' => 'PSUB_TEST_123',
            'cashfree_subscription_id' => 'CF_SUB_TEST_123',
            'status' => 'PENDING',
            'cycles_completed' => 0,
            'max_cycles' => 3,
            'month_1_price' => 649.00,
            'recurring_price' => 579.00,
        ]);

        $webhookPayload = [
            'type' => 'SUBSCRIPTION_PAYMENT_SUCCESS',
            'data' => [
                'subscription_details' => [
                    'subscription_id' => 'CF_SUB_TEST_123',
                    'current_cycle' => 1,
                ],
                'payment' => [
                    'cf_payment_id' => 'CF_PAY_999888',
                    'payment_amount' => 649.00,
                ],
            ],
        ];

        // 1. First Webhook Call: Fulfills Cycle 1
        $response1 = $this->postJson(route('webhooks.cashfree.product-subscription'), $webhookPayload);
        $response1->assertStatus(200);
        $response1->assertJson(['status' => 'success', 'cycle' => 1]);

        $subscription->refresh();
        $this->assertEquals(1, $subscription->cycles_completed);
        $this->assertEquals('ACTIVE', $subscription->status);

        $orderCount = Order::where('product_subscription_id', $subscription->id)->count();
        $this->assertEquals(1, $orderCount);

        $order = Order::where('product_subscription_id', $subscription->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals(649.00, (float) $order->grand_total);

        // Check line items: Variant + Free Gift in Cycle 1
        $items = $order->items;
        $this->assertCount(2, $items);
        $this->assertTrue($items->contains('sku', 'VAR-IMM-60'));
        $this->assertTrue($items->contains('sku', 'GIFT-SHAKER'));
        $this->assertEquals(0.00, (float) $items->firstWhere('sku', 'GIFT-SHAKER')->unit_price);

        // 2. Replay Webhook (Idempotency): Should return existing order without creating duplicates
        $response2 = $this->postJson(route('webhooks.cashfree.product-subscription'), $webhookPayload);
        $response2->assertStatus(200);

        $this->assertEquals(1, Order::where('product_subscription_id', $subscription->id)->count());

        // 3. Fulfill Final Cycle (Cycle 3): Marks Subscription COMPLETED
        $webhookCycle3 = [
            'type' => 'SUBSCRIPTION_PAYMENT_SUCCESS',
            'data' => [
                'subscription_details' => [
                    'subscription_id' => 'CF_SUB_TEST_123',
                    'current_cycle' => 3,
                ],
                'payment' => [
                    'cf_payment_id' => 'CF_PAY_333444',
                    'payment_amount' => 579.00,
                ],
            ],
        ];

        $response3 = $this->postJson(route('webhooks.cashfree.product-subscription'), $webhookCycle3);
        $response3->assertStatus(200);

        $subscription->refresh();
        $this->assertEquals(3, $subscription->cycles_completed);
        $this->assertEquals('COMPLETED', $subscription->status);
        $this->assertNull($subscription->next_billing_at);
        $this->assertEquals(2, Order::where('product_subscription_id', $subscription->id)->count());
    }

    /**
     * Test 5: Verify standard one-time cart add and checkout remain completely isolated.
     */
    public function test_one_time_purchase_cart_add_and_checkout_remain_isolated(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'tax_rate_id' => $this->taxRate->id,
            'name' => 'Brain Booster Single Box',
            'slug' => 'brain-booster-single-box',
            'sku' => 'NBD-BB-SINGLE',
            'base_price' => 649.00,
            'is_active' => true,
            'is_combo' => false,
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'name' => '30 Gummies',
            'sku' => 'VAR-SINGLE-30',
            'price' => 649.00,
            'is_active' => true,
            'is_default' => true,
        ]);

        Inventory::create([
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'stock_qty' => 10,
            'track_stock' => true,
            'is_in_stock' => true,
        ]);

        // Standard one-time cart addition
        $response = $this->actingAs($this->user)->postJson(route('user.cart.store'), [
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'quantity' => 1,
        ]);

        $response->assertStatus(200);

        // Verify standard cart has 1 item
        $cartResponse = $this->actingAs($this->user)->getJson(route('user.cart.index'));
        $cartResponse->assertStatus(200);
        $cartData = $cartResponse->json();
        $this->assertEquals(1, $cartData['item_count'] ?? 1);

        // Verify standard checkout loads
        $checkoutResponse = $this->actingAs($this->user)->get(route('checkout.index'));
        $checkoutResponse->assertStatus(200);
        $checkoutResponse->assertSee('Brain Booster Single Box');
    }

    /**
     * Test 6: Admin can view product create and edit forms with combo and subscription UI.
     */
    public function test_admin_can_view_product_create_and_edit_pages_with_combo_and_subscription_gui(): void
    {
        Permission::findOrCreate('manage products', 'admin');
        $role = Role::findOrCreate('Product Manager', 'admin');
        $role->givePermissionTo('manage products');
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $admin->assignRole($role);

        $product = Product::create([
            'name' => 'Existing Product',
            'slug' => 'existing-product',
            'sku' => 'EXIST-PROD',
            'base_price' => 599.00,
            'is_active' => true,
            'product_type' => 'simple',
            'is_combo' => false,
        ]);

        // Standard Product Pages
        $createResponse = $this->actingAs($admin, 'admin')->get(route('admin.ecommerce.products.create'));
        $createResponse->assertStatus(200);
        $createResponse->assertSee('Subscribe & Save Plans', false);

        $editResponse = $this->actingAs($admin, 'admin')->get(route('admin.ecommerce.products.edit', $product));
        $editResponse->assertStatus(200);
        $editResponse->assertSee('Subscribe & Save Plans', false);

        // Dedicated Combo Pages
        $comboProduct = Product::create([
            'name' => 'Existing Combo',
            'slug' => 'existing-combo',
            'sku' => 'EXIST-COMBO',
            'base_price' => 999.00,
            'is_active' => true,
            'product_type' => 'simple',
            'is_combo' => true,
        ]);

        $combosIndex = $this->actingAs($admin, 'admin')->get(route('admin.ecommerce.combos.index'));
        $combosIndex->assertStatus(200);
        $combosIndex->assertSee('Combo Packs Management');
        $combosIndex->assertSee('Existing Combo');

        $comboCreate = $this->actingAs($admin, 'admin')->get(route('admin.ecommerce.combos.create'));
        $comboCreate->assertStatus(200);
        $comboCreate->assertSee('Constituent Bundle Items');
        $comboCreate->assertSee('Subscribe & Save Plans', false);

        $comboEdit = $this->actingAs($admin, 'admin')->get(route('admin.ecommerce.combos.edit', $comboProduct));
        $comboEdit->assertStatus(200);
        $comboEdit->assertSee('Constituent Bundle Items');
        $comboEdit->assertSee('Subscribe & Save Plans', false);
    }

    /**
     * Test 7: Admin can create combo product with child variants through the admin GUI endpoint.
     */
    public function test_admin_can_create_combo_product_with_child_variants(): void
    {
        Permission::findOrCreate('manage products', 'admin');
        $role = Role::findOrCreate('Product Manager', 'admin');
        $role->givePermissionTo('manage products');
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $admin->assignRole($role);

        // Create two standalone products with variants to bundle
        $childProdA = Product::create([
            'name' => 'Child Product A',
            'slug' => 'child-product-a',
            'sku' => 'CHILD-A',
            'base_price' => 499.00,
            'is_active' => true,
            'product_type' => 'simple',
        ]);
        $varA = ProductVariant::create([
            'product_id' => $childProdA->id,
            'name' => 'Pack of 30',
            'sku' => 'CHILD-A-30',
            'price' => 499.00,
            'is_active' => true,
        ]);

        $childProdB = Product::create([
            'name' => 'Child Product B',
            'slug' => 'child-product-b',
            'sku' => 'CHILD-B',
            'base_price' => 599.00,
            'is_active' => true,
            'product_type' => 'simple',
        ]);
        $varB = ProductVariant::create([
            'product_id' => $childProdB->id,
            'name' => 'Pack of 60',
            'sku' => 'CHILD-B-60',
            'price' => 599.00,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin, 'admin')->post(route('admin.ecommerce.products.store'), [
            'name' => 'Immunity & Sleep Combo Pack',
            'base_price' => 899.00,
            'compare_at_price' => 1299.00,
            'product_type' => 'simple',
            'stock_qty' => 50,
            'is_combo' => 1,
            'combo_tagline' => 'Complete Day & Night Wellness',
            'combo_badge' => 'Save ₹400',
            'combo_items' => [
                ['child_variant_id' => $varA->id, 'quantity' => 1],
                ['child_variant_id' => $varB->id, 'quantity' => 2],
            ],
        ]);

        $response->assertRedirect(route('admin.ecommerce.products.index'));

        $comboProduct = Product::where('slug', 'immunity-sleep-combo-pack')->firstOrFail();
        $this->assertTrue((bool) $comboProduct->is_combo);
        $this->assertEquals('Complete Day & Night Wellness', $comboProduct->combo_tagline);
        $this->assertEquals('Save ₹400', $comboProduct->combo_badge);

        $comboItems = ProductComboItem::where('combo_product_id', $comboProduct->id)->get();
        $this->assertCount(2, $comboItems);
        $this->assertEquals(1, $comboItems->firstWhere('child_variant_id', $varA->id)->quantity);
        $this->assertEquals(2, $comboItems->firstWhere('child_variant_id', $varB->id)->quantity);
    }

    /**
     * Test 8: Admin can configure custom subscription plans for a product.
     */
    public function test_admin_can_configure_custom_subscription_plans_for_product(): void
    {
        Permission::findOrCreate('manage products', 'admin');
        $role = Role::findOrCreate('Product Manager', 'admin');
        $role->givePermissionTo('manage products');
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $admin->assignRole($role);

        $product = Product::create([
            'name' => 'Focus Chews',
            'slug' => 'focus-chews',
            'sku' => 'FOCUS-CHEWS',
            'base_price' => 799.00,
            'is_active' => true,
            'product_type' => 'simple',
        ]);

        $giftVariant = ProductVariant::create([
            'product_id' => $product->id,
            'name' => 'Gift Bottle',
            'sku' => 'GIFT-BOTTLE',
            'price' => 0.00,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin, 'admin')->put(route('admin.ecommerce.products.update', $product), [
            'name' => 'Focus Chews',
            'base_price' => 799.00,
            'product_type' => 'simple',
            'has_custom_subscriptions' => 1,
            'subscription_plans' => [
                [
                    'duration_months' => 2,
                    'month_1_price' => 649.00,
                    'recurring_price' => 699.00,
                    'badge_text' => '35% OFF',
                    'is_active' => 1,
                    'sort_order' => 1,
                ],
                [
                    'duration_months' => 3,
                    'month_1_price' => 549.00,
                    'recurring_price' => 599.00,
                    'badge_text' => '45% OFF',
                    'free_gift_variant_id' => $giftVariant->id,
                    'free_gift_label' => 'Free Shaker Bottle',
                    'is_active' => 1,
                    'sort_order' => 2,
                ],
            ],
        ]);

        $response->assertRedirect(route('admin.ecommerce.products.index'));

        $plans = ProductSubscriptionPlan::where('product_id', $product->id)->orderBy('duration_months')->get();
        $this->assertCount(2, $plans);

        $plan2 = $plans->firstWhere('duration_months', 2);
        $this->assertEquals(649.00, (float) $plan2->month_1_price);
        $this->assertEquals(699.00, (float) $plan2->recurring_price);
        $this->assertEquals('35% OFF', $plan2->badge_text);

        $plan3 = $plans->firstWhere('duration_months', 3);
        $this->assertEquals(549.00, (float) $plan3->month_1_price);
        $this->assertEquals(599.00, (float) $plan3->recurring_price);
        $this->assertEquals('45% OFF', $plan3->badge_text);
        $this->assertEquals($giftVariant->id, $plan3->free_gift_variant_id);
        $this->assertEquals('Free Shaker Bottle', $plan3->free_gift_label);
    }

    /**
     * Test 9: Admin can disable combo mode and custom subscriptions, cleaning up child records.
     */
    public function test_admin_can_disable_combo_and_custom_subscriptions(): void
    {
        Permission::findOrCreate('manage products', 'admin');
        $role = Role::findOrCreate('Product Manager', 'admin');
        $role->givePermissionTo('manage products');
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $admin->assignRole($role);

        $product = Product::create([
            'name' => 'Former Combo',
            'slug' => 'former-combo',
            'sku' => 'FORMER-COMBO',
            'base_price' => 999.00,
            'is_active' => true,
            'product_type' => 'simple',
            'is_combo' => true,
        ]);

        $childVariant = ProductVariant::create([
            'product_id' => $product->id,
            'name' => 'Child',
            'sku' => 'CH-1',
            'price' => 100,
            'is_active' => true,
        ]);

        ProductComboItem::create([
            'combo_product_id' => $product->id,
            'child_variant_id' => $childVariant->id,
            'quantity' => 1,
        ]);

        ProductSubscriptionPlan::create([
            'product_id' => $product->id,
            'duration_months' => 2,
            'month_1_price' => 400,
            'recurring_price' => 450,
            'is_active' => true,
        ]);

        // Disable combo and custom subscriptions
        $response = $this->actingAs($admin, 'admin')->put(route('admin.ecommerce.products.update', $product), [
            'name' => 'Former Combo',
            'base_price' => 999.00,
            'product_type' => 'simple',
            'is_combo' => 0,
            'has_custom_subscriptions' => 0,
        ]);

        $response->assertRedirect(route('admin.ecommerce.products.index'));

        $this->assertFalse((bool) $product->fresh()->is_combo);
        $this->assertCount(0, ProductComboItem::where('combo_product_id', $product->id)->get());
        $this->assertCount(0, ProductSubscriptionPlan::where('product_id', $product->id)->get());
    }

    /**
     * Test 10: Admin can save and update dynamic accordion tabs for a product.
     */
    public function test_admin_can_save_and_update_accordion_tabs(): void
    {
        Permission::findOrCreate('manage products', 'admin');
        $role = Role::findOrCreate('Product Manager', 'admin');
        $role->givePermissionTo('manage products');
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $admin->assignRole($role);

        $response = $this->actingAs($admin, 'admin')->post(route('admin.ecommerce.products.store'), [
            'name' => 'Immunity Booster Drops',
            'base_price' => 450.00,
            'product_type' => 'simple',
            'accordion_tabs' => [
                ['title' => 'Product description', 'content' => 'High quality Ayurvedic immunity drops.'],
                ['title' => 'What’s inside?', 'content' => 'Tulsi, Amla, and Ashwagandha extracts.'],
                ['title' => 'How do I use it?', 'content' => 'Take 5 drops in warm milk daily.'],
            ],
        ]);

        $response->assertRedirect(route('admin.ecommerce.products.index'));

        $product = Product::where('slug', 'immunity-booster-drops')->firstOrFail();
        $this->assertIsArray($product->accordion_tabs);
        $this->assertCount(3, $product->accordion_tabs);
        $this->assertEquals('Product description', $product->accordion_tabs[0]['title']);
        $this->assertEquals('High quality Ayurvedic immunity drops.', $product->accordion_tabs[0]['content']);
        $this->assertEquals('High quality Ayurvedic immunity drops.', $product->description);

        // Update with custom accordions
        $updateResponse = $this->actingAs($admin, 'admin')->put(route('admin.ecommerce.products.update', $product), [
            'name' => 'Immunity Booster Drops',
            'base_price' => 450.00,
            'product_type' => 'simple',
            'accordion_tabs' => [
                ['title' => 'Overview & Benefits', 'content' => 'Updated overview content.'],
                ['title' => 'Clinical Backing', 'content' => 'Backed by clinical research.'],
            ],
        ]);

        $updateResponse->assertRedirect(route('admin.ecommerce.products.index'));
        $fresh = $product->fresh();
        $this->assertCount(2, $fresh->accordion_tabs);
        $this->assertEquals('Overview & Benefits', $fresh->accordion_tabs[0]['title']);
        $this->assertEquals('Clinical Backing', $fresh->accordion_tabs[1]['title']);
    }

    /**
     * Test 11: Storefront PDP dynamically renders configured accordion tabs.
     */
    public function test_storefront_pdp_renders_custom_accordion_tabs(): void
    {
        $product = Product::create([
            'name' => 'Organic Vitamin D3 Drops',
            'slug' => 'organic-vitamin-d3-drops',
            'sku' => 'VIT-D3',
            'base_price' => 599.00,
            'is_active' => true,
            'product_type' => 'simple',
            'accordion_tabs' => [
                ['title' => 'Clinical Evidence', 'content' => 'Proven 95% absorption rate in pediatric trials.'],
                ['title' => 'Storage & Safety', 'content' => 'Keep refrigerated after opening.'],
            ],
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'name' => 'Standard Bottle',
            'sku' => 'VIT-D3-VAR',
            'price' => 599.00,
            'is_active' => true,
            'is_default' => true,
        ]);

        Inventory::create([
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'stock_qty' => 20,
            'track_stock' => true,
            'is_in_stock' => true,
        ]);

        $response = $this->get(route('product.show', $product->slug));
        $response->assertStatus(200);
        $response->assertSee('Clinical Evidence');
        $response->assertSee('Proven 95% absorption rate in pediatric trials.');
        $response->assertSee('Storage & Safety');
        $response->assertSee('Keep refrigerated after opening.');
    }

    /**
     * Test 12: Admin can create and update combo pack through dedicated ComboProductController.
     */
    public function test_admin_can_manage_combos_through_dedicated_controller(): void
    {
        Permission::findOrCreate('manage products', 'admin');
        $role = Role::findOrCreate('Product Manager', 'admin');
        $role->givePermissionTo('manage products');
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $admin->assignRole($role);

        $childProduct = Product::create([
            'name' => 'Brain Chews',
            'slug' => 'brain-chews',
            'sku' => 'BRAIN-1',
            'base_price' => 499.00,
            'is_active' => true,
            'product_type' => 'simple',
        ]);
        $childVariant = ProductVariant::create([
            'product_id' => $childProduct->id,
            'name' => 'Single Pack',
            'sku' => 'BRAIN-1-VAR',
            'price' => 499.00,
            'is_active' => true,
        ]);

        // Create combo via dedicated combo endpoint
        $storeResponse = $this->actingAs($admin, 'admin')->post(route('admin.ecommerce.combos.store'), [
            'name' => 'Brain & Body Super Duo',
            'base_price' => 849.00,
            'compare_at_price' => 1199.00,
            'product_type' => 'simple',
            'stock_qty' => 50,
            'combo_tagline' => 'Power Pack for Mind & Body',
            'combo_badge' => 'Best Value',
            'combo_items' => [
                ['child_variant_id' => $childVariant->id, 'quantity' => 2],
            ],
            'has_custom_subscriptions' => 1,
            'subscription_plans' => [
                [
                    'duration_months' => 3,
                    'month_1_price' => 749.00,
                    'recurring_price' => 799.00,
                    'badge_text' => '40% OFF',
                ],
            ],
        ]);

        $storeResponse->assertRedirect(route('admin.ecommerce.combos.index'));

        $combo = Product::where('slug', 'brain-body-super-duo')->firstOrFail();
        $this->assertTrue((bool) $combo->is_combo);
        $this->assertEquals('Power Pack for Mind & Body', $combo->combo_tagline);
        $this->assertCount(1, $combo->comboItems);
        $this->assertEquals(2, $combo->comboItems->first()->quantity);
        $this->assertCount(1, $combo->subscriptionPlans);

        // Update via dedicated combo endpoint
        $updateResponse = $this->actingAs($admin, 'admin')->put(route('admin.ecommerce.combos.update', $combo), [
            'name' => 'Brain & Body Super Duo',
            'base_price' => 799.00,
            'product_type' => 'simple',
            'combo_tagline' => 'Updated Tagline',
            'combo_items' => [
                ['child_variant_id' => $childVariant->id, 'quantity' => 3],
            ],
        ]);

        $updateResponse->assertRedirect(route('admin.ecommerce.combos.index'));
        $this->assertEquals('Updated Tagline', $combo->fresh()->combo_tagline);
        $this->assertEquals(3, $combo->fresh()->comboItems->first()->quantity);

        // Delete via dedicated combo endpoint
        $deleteResponse = $this->actingAs($admin, 'admin')->delete(route('admin.ecommerce.combos.destroy', $combo));
        $deleteResponse->assertRedirect(route('admin.ecommerce.combos.index'));
        $this->assertNull(Product::find($combo->id));
    }

    /**
     * Test 19: Duplicate child variant submission in combo pack aggregates quantities cleanly without SQL 1062 error.
     */
    public function test_combo_creation_aggregates_duplicate_child_variants_without_database_error(): void
    {
        Permission::findOrCreate('manage products', 'admin');
        $role = Role::findOrCreate('Product Manager', 'admin');
        $role->givePermissionTo('manage products');

        $admin = User::factory()->create([
            'email' => 'admin_combo_dupe@nutribuddy.in',
            'role' => 'admin',
            'is_active' => true,
        ]);
        $admin->assignRole($role);

        $childProduct = Product::create([
            'category_id' => $this->category->id,
            'tax_rate_id' => $this->taxRate->id,
            'name' => 'Omega Smart Gummies',
            'slug' => 'omega-smart-gummies-dupe-test',
            'sku' => 'NBD-OMG-DUPE',
            'price' => 599.00,
            'is_active' => true,
        ]);

        $childVariant = ProductVariant::create([
            'product_id' => $childProduct->id,
            'name' => '60 Count Pack',
            'sku' => 'NBD-OMG-60-DUPE',
            'price' => 599.00,
            'is_active' => true,
        ]);

        // Submit with the SAME child variant ID in two separate rows (e.g. qty 2 and qty 3)
        $response = $this->actingAs($admin, 'admin')->post(route('admin.ecommerce.combos.store'), [
            'name' => 'Omega Multi Combo Stack',
            'base_price' => 1099.00,
            'compare_at_price' => 1499.00,
            'product_type' => 'simple',
            'stock_qty' => 30,
            'combo_tagline' => 'Double Omega Power',
            'combo_items' => [
                ['child_variant_id' => $childVariant->id, 'quantity' => 2],
                ['child_variant_id' => $childVariant->id, 'quantity' => 3],
            ],
        ]);

        $response->assertRedirect(route('admin.ecommerce.combos.index'));

        $combo = Product::where('slug', 'omega-multi-combo-stack')->firstOrFail();
        // Assert only 1 combo item was stored with summed quantity (2 + 3 = 5)
        $this->assertCount(1, $combo->comboItems);
        $this->assertEquals(5, $combo->comboItems->first()->quantity);
        $this->assertEquals($childVariant->id, $combo->comboItems->first()->child_variant_id);
    }

    /**
     * Test 20: Creating a product or combo with an existing name automatically increments slug without SQL 1062 error.
     */
    public function test_combo_creation_auto_increments_slug_when_same_name_is_added_preventing_duplicate_slug_error(): void
    {
        Permission::findOrCreate('manage products', 'admin');
        $role = Role::findOrCreate('Product Manager', 'admin');
        $role->givePermissionTo('manage products');

        $admin = User::factory()->create([
            'email' => 'admin_slug_dupe@nutribuddy.in',
            'role' => 'admin',
            'is_active' => true,
        ]);
        $admin->assignRole($role);

        $childProduct = Product::create([
            'category_id' => $this->category->id,
            'tax_rate_id' => $this->taxRate->id,
            'name' => 'Calcium Chews',
            'slug' => 'calcium-chews-base',
            'sku' => 'NBD-CAL-CHEWS',
            'price' => 399.00,
            'is_active' => true,
        ]);

        $childVariant = ProductVariant::create([
            'product_id' => $childProduct->id,
            'name' => 'Single',
            'sku' => 'NBD-CAL-CHEWS-S',
            'price' => 399.00,
            'is_active' => true,
        ]);

        // First combo with name "Ritik Combo Pack" -> slug "ritik-combo-pack"
        $firstResponse = $this->actingAs($admin, 'admin')->post(route('admin.ecommerce.combos.store'), [
            'name' => 'Ritik Combo Pack',
            'base_price' => 799.00,
            'product_type' => 'simple',
            'stock_qty' => 20,
            'combo_items' => [
                ['child_variant_id' => $childVariant->id, 'quantity' => 1],
            ],
        ]);
        $firstResponse->assertRedirect(route('admin.ecommerce.combos.index'));

        $firstCombo = Product::where('name', 'Ritik Combo Pack')->firstOrFail();
        $this->assertEquals('ritik-combo-pack', $firstCombo->slug);

        // Second combo with the EXACT SAME NAME "Ritik Combo Pack"
        $secondResponse = $this->actingAs($admin, 'admin')->post(route('admin.ecommerce.combos.store'), [
            'name' => 'Ritik Combo Pack',
            'base_price' => 899.00,
            'product_type' => 'simple',
            'stock_qty' => 15,
            'combo_items' => [
                ['child_variant_id' => $childVariant->id, 'quantity' => 1],
            ],
        ]);
        $secondResponse->assertRedirect(route('admin.ecommerce.combos.index'));

        // Should auto-generate "ritik-combo-pack-1" cleanly without throwing SQL 1062!
        $secondCombo = Product::where('slug', 'ritik-combo-pack-1')->first();
        $this->assertNotNull($secondCombo);
        $this->assertEquals('Ritik Combo Pack', $secondCombo->name);
    }

    /**
     * Test 21: Dynamic Before & After images, labels, and fallback icons in Transformation section.
     */
    public function test_admin_can_save_dynamic_before_after_transformation_images_and_render_on_pdp(): void
    {
        Permission::findOrCreate('manage products', 'admin');
        $role = Role::findOrCreate('Product Manager', 'admin');
        $role->givePermissionTo('manage products');

        $admin = User::factory()->create([
            'email' => 'admin_transform@nutribuddy.in',
            'role' => 'admin',
            'is_active' => true,
        ]);
        $admin->assignRole($role);

        $product = Product::create([
            'category_id' => $this->category->id,
            'tax_rate_id' => $this->taxRate->id,
            'name' => 'Immunity Booster Drops',
            'slug' => 'immunity-booster-drops',
            'sku' => 'NBD-IM-DROPS',
            'base_price' => 450.00,
            'product_type' => 'simple',
            'is_active' => true,
        ]);

        ProductVariant::create([
            'product_id' => $product->id,
            'name' => 'Default',
            'sku' => 'NBD-IM-DROPS-VAR',
            'price' => 450.00,
            'is_active' => true,
        ]);

        $updateResponse = $this->actingAs($admin, 'admin')->put(route('admin.ecommerce.products.update', $product), [
            'name' => 'Immunity Booster Drops',
            'base_price' => 450.00,
            'product_type' => 'simple',
            'is_active' => 1,
            'transform_description' => 'Custom 60-day immunity transformation results.',
            'transform_before_label' => 'Day 1 Start',
            'transform_before_sublabel' => 'Low immunity & fatigue',
            'transform_after_label' => 'Day 60 Strong',
            'transform_after_sublabel' => 'Energetic & zero sick days',
            'transform_before_image' => 'assets/brain/routine-before.png',
            'transform_after_image' => 'assets/brain/routine-after.webp',
            'transform_results' => [
                [
                    'image' => 'img/immune.png',
                    'title' => 'Resilient Defenses',
                    'description' => 'Fewer seasonal sniffles.',
                    'week' => 'Week 2',
                ],
            ],
        ]);

        $updateResponse->assertRedirect(route('admin.ecommerce.products.index'));

        $freshProduct = $product->fresh();
        $this->assertEquals('Day 1 Start', $freshProduct->transform_before_label);
        $this->assertEquals('Day 60 Strong', $freshProduct->transform_after_label);
        $this->assertEquals('assets/brain/routine-before.png', $freshProduct->transform_before_image);
        $this->assertEquals('assets/brain/routine-after.webp', $freshProduct->transform_after_image);

        // Verify PDP renders custom before and after labels and fallbacks
        $pdpResponse = $this->get(route('product.show', $freshProduct->slug));
        $pdpResponse->assertOk();
        $pdpResponse->assertSee('Day 1 Start');
        $pdpResponse->assertSee('Low immunity & fatigue');
        $pdpResponse->assertSee('Day 60 Strong');
        $pdpResponse->assertSee('Energetic & zero sick days');
        $pdpResponse->assertSee('Resilient Defenses');
    }

    /**
     * Test 22: Dynamic Features (What's NOT in it) and Comparison Table can be customized and rendered.
     */
    public function test_admin_can_save_dynamic_features_and_comparison_table_and_render_on_pdp(): void
    {
        Permission::findOrCreate('manage products', 'admin');
        $role = Role::findOrCreate('Product Manager', 'admin');
        $role->givePermissionTo('manage products');

        $admin = User::factory()->create([
            'email' => 'admin_features@nutribuddy.in',
            'role' => 'admin',
            'is_active' => true,
        ]);
        $admin->assignRole($role);

        $product = Product::create([
            'category_id' => $this->category->id,
            'tax_rate_id' => $this->taxRate->id,
            'name' => 'Organic Vitamin D3 Sprays',
            'slug' => 'organic-vitamin-d3-sprays',
            'sku' => 'NBD-VITD3-SPRAY',
            'base_price' => 599.00,
            'product_type' => 'simple',
            'is_active' => true,
        ]);

        ProductVariant::create([
            'product_id' => $product->id,
            'name' => 'Default',
            'sku' => 'NBD-VITD3-SPRAY-V',
            'price' => 599.00,
            'is_active' => true,
        ]);

        $updateResponse = $this->actingAs($admin, 'admin')->put(route('admin.ecommerce.products.update', $product), [
            'name' => 'Organic Vitamin D3 Sprays',
            'base_price' => 599.00,
            'product_type' => 'simple',
            'is_active' => 1,
            'features_heading' => 'CLEAN INGREDIENTS STANDARD',
            'features_title' => 'Pure as Nature Formulated',
            'features_subtitle' => 'Zero fillers, only bioavailable lichen-based D3.',
            'features_items' => [
                [
                    'icon' => 'img/vegan-1.png',
                    'title' => '100% Plant Lichen D3',
                    'description' => 'Never sourced from sheep lanolin or animal fat.',
                ],
            ],
            'features_comparison_title' => 'NutriBuddy vs Typical Brands',
            'features_comparison_rows' => [
                [
                    'feature' => '100% Vegan Lichen',
                    'us' => 'yes',
                    'others' => 'no',
                ],
                [
                    'feature' => 'Absorption Speed',
                    'us' => 'Fast Sublingual',
                    'others' => 'Slow Digestive',
                ],
            ],
        ]);

        $updateResponse->assertRedirect(route('admin.ecommerce.products.index'));

        $freshProduct = $product->fresh();
        $this->assertEquals('CLEAN INGREDIENTS STANDARD', $freshProduct->features_heading);
        $this->assertEquals('Pure as Nature Formulated', $freshProduct->features_title);
        $this->assertCount(1, $freshProduct->features_items);
        $this->assertCount(2, $freshProduct->features_comparison_rows);

        // Verify PDP renders custom features and comparison rows
        $pdpResponse = $this->get(route('product.show', $freshProduct->slug));
        $pdpResponse->assertOk();
        $pdpResponse->assertSee('CLEAN INGREDIENTS STANDARD');
        $pdpResponse->assertSee('Pure as Nature Formulated');
        $pdpResponse->assertSee('100% Plant Lichen D3');
        $pdpResponse->assertSee('Never sourced from sheep lanolin');
        $pdpResponse->assertSee('NutriBuddy vs Typical Brands');
        $pdpResponse->assertSee('Fast Sublingual');
        $pdpResponse->assertSee('Slow Digestive');
    }

    /**
     * Test admin can save dynamic formula section and render on PDP with fallbacks.
     */
    public function test_admin_can_save_dynamic_formula_section_and_render_on_pdp(): void
    {
        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'admin']);
        $permission = Permission::firstOrCreate(['name' => 'manage products', 'guard_name' => 'admin']);
        $role->givePermissionTo($permission);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);
        $admin->assignRole($role);

        $product = Product::create([
            'category_id' => $this->category->id,
            'tax_rate_id' => $this->taxRate->id,
            'name' => 'Immunity Defense Gummies',
            'slug' => 'immunity-defense-gummies',
            'sku' => 'NBD-IMMUNE-01',
            'base_price' => 749.00,
            'product_type' => 'simple',
            'is_active' => true,
        ]);

        ProductVariant::create([
            'product_id' => $product->id,
            'name' => 'Default',
            'sku' => 'NBD-IMMUNE-01-V',
            'price' => 749.00,
            'is_active' => true,
        ]);

        // 1. Verify dynamic fallback PDP rendering when formula fields are null
        $fallbackPdp = $this->get(route('product.show', $product->slug));
        $fallbackPdp->assertOk();
        $fallbackPdp->assertSee('Meet your nutrition buddy');
        $fallbackPdp->assertSee('Little gummy. <em>Thoughtful formula.</em>', false);
        $fallbackPdp->assertSee('Ayurveda + nutraceutical');
        $fallbackPdp->assertSee('IMMUNE'); // Dynamically computed fallback for Immunity product instead of hardcoded BRAIN

        // 2. Admin updates the formula section with custom data
        $updateResponse = $this->actingAs($admin, 'admin')->put(route('admin.ecommerce.products.update', $product), [
            'name' => 'Immunity Defense Gummies',
            'base_price' => 749.00,
            'product_type' => 'simple',
            'is_active' => 1,
            'formula_eyebrow' => 'Dynamic Nutrition Companion',
            'formula_heading' => 'Tiny drops. <em>Potent vitality.</em>',
            'formula_subheading' => 'Superfood D3 Liquid',
            'formula_tag' => 'Plant science + ancient wisdom',
            'formula_bg_word' => 'VITALITY',
            'formula_stat_left_num' => '5000 IU',
            'formula_stat_left_title' => 'Bioactive D3',
            'formula_stat_left_sub' => 'organic wild lichen',
            'formula_stat_right_num' => '100%',
            'formula_stat_right_title' => 'Vegan certified',
            'formula_stat_right_sub' => 'zero gelatin or fillers',
            'formula_badges' => ['Sugar Free', 'No GMOs', 'Third-Party Tested', '100% Pectin'],
            'formula_caption' => 'Clinically backed nutrition for daily growth and immunity.',
            'formula_button_primary_text' => 'Start Your Trial',
            'formula_button_primary_url' => '#buy-now',
            'formula_button_secondary_text' => 'Check Lab Certificate',
            'formula_button_secondary_url' => '#lab-reports',
            'formula_footnote' => 'Free shipping across India with subscription',
        ]);

        $updateResponse->assertRedirect(route('admin.ecommerce.products.index'));

        $freshProduct = $product->fresh();
        $this->assertEquals('Dynamic Nutrition Companion', $freshProduct->formula_eyebrow);
        $this->assertEquals('Tiny drops. <em>Potent vitality.</em>', $freshProduct->formula_heading);
        $this->assertEquals('Superfood D3 Liquid', $freshProduct->formula_subheading);
        $this->assertEquals('Plant science + ancient wisdom', $freshProduct->formula_tag);
        $this->assertEquals('VITALITY', $freshProduct->formula_bg_word);
        $this->assertEquals('5000 IU', $freshProduct->formula_stat_left_num);
        $this->assertEquals('Bioactive D3', $freshProduct->formula_stat_left_title);
        $this->assertEquals('organic wild lichen', $freshProduct->formula_stat_left_sub);
        $this->assertEquals('100%', $freshProduct->formula_stat_right_num);
        $this->assertEquals('Vegan certified', $freshProduct->formula_stat_right_title);
        $this->assertEquals('zero gelatin or fillers', $freshProduct->formula_stat_right_sub);
        $this->assertEquals(['Sugar Free', 'No GMOs', 'Third-Party Tested', '100% Pectin'], $freshProduct->formula_badges);
        $this->assertEquals('Clinically backed nutrition for daily growth and immunity.', $freshProduct->formula_caption);
        $this->assertEquals('Start Your Trial', $freshProduct->formula_button_primary_text);
        $this->assertEquals('#buy-now', $freshProduct->formula_button_primary_url);
        $this->assertEquals('Check Lab Certificate', $freshProduct->formula_button_secondary_text);
        $this->assertEquals('#lab-reports', $freshProduct->formula_button_secondary_url);
        $this->assertEquals('Free shipping across India with subscription', $freshProduct->formula_footnote);

        // 3. Verify PDP renders dynamic content
        $dynamicPdp = $this->get(route('product.show', $freshProduct->slug));
        $dynamicPdp->assertOk();
        $dynamicPdp->assertSee('Dynamic Nutrition Companion');
        $dynamicPdp->assertSee('Tiny drops. <em>Potent vitality.</em>', false);
        $dynamicPdp->assertSee('Superfood D3 Liquid');
        $dynamicPdp->assertSee('Plant science + ancient wisdom');
        $dynamicPdp->assertSee('VITALITY');
        $dynamicPdp->assertSee('5000 IU');
        $dynamicPdp->assertSee('Bioactive D3');
        $dynamicPdp->assertSee('organic wild lichen');
        $dynamicPdp->assertSee('100%');
        $dynamicPdp->assertSee('Vegan certified');
        $dynamicPdp->assertSee('zero gelatin or fillers');
        $dynamicPdp->assertSee('Sugar Free');
        $dynamicPdp->assertSee('No GMOs');
        $dynamicPdp->assertSee('Third-Party Tested');
        $dynamicPdp->assertSee('100% Pectin');
        $dynamicPdp->assertSee('Clinically backed nutrition for daily growth and immunity.');
        $dynamicPdp->assertSee('Start Your Trial');
        $dynamicPdp->assertSee('#buy-now');
        $dynamicPdp->assertSee('Check Lab Certificate');
        $dynamicPdp->assertSee('#lab-reports');
        $dynamicPdp->assertSee('Free shipping across India with subscription');
    }

    /**
     * Test admin can save dynamic FAQs and render on PDP with default fallbacks.
     */
    public function test_admin_can_save_dynamic_faqs_and_render_on_pdp(): void
    {
        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'admin']);
        $permission = Permission::firstOrCreate(['name' => 'manage products', 'guard_name' => 'admin']);
        $role->givePermissionTo($permission);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);
        $admin->assignRole($role);

        $product = Product::create([
            'category_id' => $this->category->id,
            'tax_rate_id' => $this->taxRate->id,
            'name' => 'Kids Probiotic Chewables',
            'slug' => 'kids-probiotic-chewables',
            'sku' => 'NBD-PROBIO-01',
            'base_price' => 499.00,
            'product_type' => 'simple',
            'is_active' => true,
        ]);

        ProductVariant::create([
            'product_id' => $product->id,
            'name' => 'Default',
            'sku' => 'NBD-PROBIO-01-V',
            'price' => 499.00,
            'is_active' => true,
        ]);

        // 1. Fallback rendering: verifies default FAQs display when product has null faqs
        $fallbackPdp = $this->get(route('product.show', $product->slug));
        $fallbackPdp->assertOk();
        $fallbackPdp->assertSee('Parents <span class="acc">Ask</span> Us 🙋', false);
        $fallbackPdp->assertSee('Are NutriBuddy gummies safe for children?');
        $fallbackPdp->assertSee('When will I start seeing results?');
        $fallbackPdp->assertSee('Can my child take multiple products together?');

        // 2. Admin saves custom FAQ title and questions
        $updateResponse = $this->actingAs($admin, 'admin')->put(route('admin.ecommerce.products.update', $product), [
            'name' => 'Kids Probiotic Chewables',
            'base_price' => 499.00,
            'product_type' => 'simple',
            'is_active' => 1,
            'faq_title' => 'Curious Minds <span class="acc">FAQ</span> 💡',
            'faqs' => [
                [
                    'question' => 'How many chewables should my child take per day?',
                    'answer' => 'Give 1 chewable tablet daily after breakfast with water.',
                ],
                [
                    'question' => 'Does this need refrigeration?',
                    'answer' => 'No refrigeration needed. Store in a cool, dry place away from direct sunlight.',
                ],
            ],
        ]);

        $updateResponse->assertRedirect(route('admin.ecommerce.products.index'));

        $freshProduct = $product->fresh();
        $this->assertEquals('Curious Minds <span class="acc">FAQ</span> 💡', $freshProduct->faq_title);
        $this->assertCount(2, $freshProduct->faqs);
        $this->assertEquals('How many chewables should my child take per day?', $freshProduct->faqs[0]['question']);
        $this->assertEquals('Give 1 chewable tablet daily after breakfast with water.', $freshProduct->faqs[0]['answer']);
        $this->assertEquals('Does this need refrigeration?', $freshProduct->faqs[1]['question']);
        $this->assertEquals('No refrigeration needed. Store in a cool, dry place away from direct sunlight.', $freshProduct->faqs[1]['answer']);

        // 3. Verify storefront PDP renders custom FAQs
        $customPdp = $this->get(route('product.show', $freshProduct->slug));
        $customPdp->assertOk();
        $customPdp->assertSee('Curious Minds <span class="acc">FAQ</span> 💡', false);
        $customPdp->assertSee('How many chewables should my child take per day?');
        $customPdp->assertSee('Give 1 chewable tablet daily after breakfast with water.');
        $customPdp->assertSee('Does this need refrigeration?');
        $customPdp->assertSee('No refrigeration needed. Store in a cool, dry place away from direct sunlight.');
    }

    /**
     * Test admin can save standalone showcase banner image and render on PDP.
     */
    public function test_admin_can_save_showcase_banner_image_and_render_on_pdp(): void
    {
        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'admin']);
        $permission = Permission::firstOrCreate(['name' => 'manage products', 'guard_name' => 'admin']);
        $role->givePermissionTo($permission);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);
        $admin->assignRole($role);

        $product = Product::create([
            'category_id' => $this->category->id,
            'tax_rate_id' => $this->taxRate->id,
            'name' => 'Eye Care Vision Gummies',
            'slug' => 'eye-care-vision-gummies',
            'sku' => 'NBD-EYE-01',
            'base_price' => 549.00,
            'product_type' => 'simple',
            'is_active' => true,
        ]);

        ProductVariant::create([
            'product_id' => $product->id,
            'name' => 'Default',
            'sku' => 'NBD-EYE-01-V',
            'price' => 549.00,
            'is_active' => true,
        ]);

        // 1. Initially without banner image, PDP doesn't render banner section
        $initialPdp = $this->get(route('product.show', $product->slug));
        $initialPdp->assertOk();
        $initialPdp->assertDontSee('pdp-showcase-banner');

        // 2. Admin saves banner image
        $updateResponse = $this->actingAs($admin, 'admin')->put(route('admin.ecommerce.products.update', $product), [
            'name' => 'Eye Care Vision Gummies',
            'base_price' => 549.00,
            'product_type' => 'simple',
            'is_active' => 1,
            'banner_image' => 'assets/brain/hero-reveal.webp',
        ]);

        $updateResponse->assertRedirect(route('admin.ecommerce.products.index'));

        $freshProduct = $product->fresh();
        $this->assertEquals('assets/brain/hero-reveal.webp', $freshProduct->banner_image);

        // 3. PDP now renders the showcase banner section
        $bannerPdp = $this->get(route('product.show', $freshProduct->slug));
        $bannerPdp->assertOk();
        $bannerPdp->assertSee('pdp-showcase-banner');
        $bannerPdp->assertSee('assets/brain/hero-reveal.webp');

        // 4. Admin deletes banner image
        $deleteResponse = $this->actingAs($admin, 'admin')->put(route('admin.ecommerce.products.update', $freshProduct), [
            'name' => 'Eye Care Vision Gummies',
            'base_price' => 549.00,
            'product_type' => 'simple',
            'is_active' => 1,
            'delete_banner_image' => '1',
        ]);

        $deleteResponse->assertRedirect(route('admin.ecommerce.products.index'));
        $this->assertNull($freshProduct->fresh()->banner_image);
    }
}
