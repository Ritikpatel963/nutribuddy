<?php

namespace Tests\Unit;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\Setting;
use App\Models\TaxRate;
use App\Models\User;
use App\Services\PricingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PricingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_calculates_totals_with_percentage_coupon_and_tax(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $taxRate = TaxRate::factory()->create(['rate' => 18, 'code' => 'GST18']);
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'tax_rate_id' => $taxRate->id,
            'base_price' => 100,
            'shipping_price' => 10,
        ]);
        $cart = Cart::create(['user_id' => $user->id, 'currency' => 'INR']);
        $cartItem = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
        $cartItem->load(['product.taxRate', 'productVariant']);

        $coupon = Coupon::create([
            'code' => 'SAVE10',
            'discount_type' => 'percentage',
            'discount_value' => 10,
            'is_active' => true,
        ]);

        $result = app(PricingService::class)->calculate(collect([$cartItem]), $coupon);

        $this->assertSame(200.0, $result['subtotal']);
        $this->assertSame(20.0, $result['discount_total']);
        $this->assertSame(36.0, $result['tax_total']);
        $this->assertSame(20.0, $result['shipping_total']);
        $this->assertSame(236.0, $result['grand_total']);
    }

    public function test_it_limits_coin_redemption_to_admin_coin_cap(): void
    {
        Setting::set('loyalty_enabled', 1);
        Setting::set('loyalty_conversion_rate', 10);
        Setting::set('loyalty_max_redemption_percent', 100);
        Setting::set('loyalty_max_redeemable_coins', 50);

        $user = User::factory()->create();
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'tax_rate_id' => null,
            'base_price' => 100,
            'shipping_price' => 0,
        ]);
        $cart = Cart::create(['user_id' => $user->id, 'currency' => 'INR']);
        $cartItem = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);
        $cartItem->load(['product.taxRate', 'productVariant']);

        try {
            $result = app(PricingService::class)->calculate(collect([$cartItem]), null, 90);

            $this->assertSame(50, $result['coins_redeemed']);
            $this->assertSame(5.0, $result['coin_discount']);
            $this->assertSame(95.0, $result['grand_total']);
        } finally {
            Setting::set('loyalty_enabled', 1);
            Setting::set('loyalty_conversion_rate', 10);
            Setting::set('loyalty_max_redemption_percent', 30);
            Setting::set('loyalty_max_redeemable_coins', 0);
        }
    }
}
