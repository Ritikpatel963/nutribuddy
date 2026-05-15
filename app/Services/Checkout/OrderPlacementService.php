<?php

namespace App\Services\Checkout;

use App\Mail\OrderPlacedAdminMail;
use App\Mail\OrderPlacedCustomerMail;
use App\Models\Cart;
use App\Models\CoinTransaction;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\CustomerAddress;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use App\Services\PricingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OrderPlacementService
{
    public function __construct(
        private readonly CheckoutSummaryService $checkoutSummaryService,
        private readonly PricingService $pricingService
    ) {
    }

    public function place(User $user, array $validated): array
    {
        $address = CustomerAddress::where('user_id', $user->id)->findOrFail($validated['address_id']);
        $coupon = $this->checkoutSummaryService->resolveCoupon($validated['coupon_code'] ?? null, $user->id);
        $checkoutToken = trim((string) ($validated['checkout_token'] ?? '')) ?: (string) Str::uuid();

        $existingOrder = Order::where('user_id', $user->id)
            ->where('checkout_token', $checkoutToken)
            ->first();

        if ($existingOrder) {
            return [
                'message' => 'Order already placed for this checkout request.',
                'order' => $existingOrder->load(['items', 'payments', 'statusHistories']),
            ];
        }

        $order = DB::transaction(function () use ($user, $address, $coupon, $validated, $checkoutToken) {
            $cart = $this->resolveUserCart($user)
                ->load(['items.product.taxRate', 'items.product.primaryImage', 'items.product.images', 'items.productVariant']);

            abort_if($cart->items->isEmpty(), 422, 'Cart is empty.');

            $coinsToRedeem = (int) ($validated['coins_to_redeem'] ?? 0);
            abort_if($coinsToRedeem > (int) $user->coins_balance, 422, 'Insufficient coin balance.');

            $pricing = $this->pricingService->calculate($cart->items, $coupon, $coinsToRedeem);
            $this->validateCouponRules($coupon, (float) $pricing['subtotal'], $user->id);
            $this->validateInventory($cart);

            $order = $this->createOrder($user, $address, $coupon, $pricing, $validated, $checkoutToken);
            $this->recordCoinTransactions($order, $user, $pricing);
            $this->createOrderItems($order, $pricing['line_items']);
            $this->createPayment($order);
            $this->recordCouponUsage($order, $coupon, $user);
            $this->recordStatusHistory($order, $user);

            $cart->items()->delete();

            return $order->load(['items', 'payments', 'statusHistories']);
        });

        $this->sendOrderMails($order);
        $this->notifyAdmins($order);

        return [
            'message' => 'Order placed successfully.',
            'order' => $order,
        ];
    }

    private function resolveUserCart(User $user): Cart
    {
        return Cart::firstOrCreate(
            ['user_id' => $user->id],
            ['currency' => 'INR']
        );
    }

    private function validateCouponRules(?Coupon $coupon, float $subtotal, int $userId): void
    {
        if (! $coupon) {
            return;
        }

        $this->checkoutSummaryService->assertCouponMinimum($coupon, $subtotal);

        if ($coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit) {
            abort(422, 'Coupon usage limit reached.');
        }

        $userUsageCount = CouponUsage::where('coupon_id', $coupon->id)->where('user_id', $userId)->count();
        if ($coupon->usage_limit_per_user !== null && $userUsageCount >= $coupon->usage_limit_per_user) {
            abort(422, 'Coupon usage limit reached for this user.');
        }
    }

    private function validateInventory(Cart $cart): void
    {
        foreach ($cart->items as $item) {
            $inventoryQuery = Inventory::query()
                ->lockForUpdate()
                ->where('product_id', $item->product_id);

            if ($item->product_variant_id) {
                $inventoryQuery->where('product_variant_id', $item->product_variant_id);
            } else {
                $inventoryQuery->whereNull('product_variant_id');
            }

            $inventory = $inventoryQuery->first();
            if (! $inventory || ! $inventory->track_stock) {
                continue;
            }

            $available = max(0, (int) $inventory->stock_qty - (int) $inventory->reserved_qty);
            abort_if($available < $item->quantity, 422, 'Some cart items are out of stock.');
        }
    }

    private function createOrder(User $user, CustomerAddress $address, ?Coupon $coupon, array $pricing, array $validated, string $checkoutToken): Order
    {
        return Order::create([
            'order_number' => $this->generateOrderNumber(),
            'checkout_token' => $checkoutToken,
            'user_id' => $user->id,
            'coupon_id' => $coupon?->id,
            'coupon_code' => $coupon?->code,
            'status' => 'pending',
            'fulfillment_status' => 'unfulfilled',
            'payment_status' => 'pending',
            'payment_method' => 'cod',
            'currency' => 'INR',
            'customer_name' => $address->full_name,
            'customer_email' => $address->email ?: $user->email,
            'customer_phone' => $address->phone,
            'shipping_name' => $address->full_name,
            'shipping_phone' => $address->phone,
            'shipping_address_line_1' => $address->address_line_1,
            'shipping_address_line_2' => $address->address_line_2,
            'shipping_landmark' => $address->landmark,
            'shipping_city' => $address->city,
            'shipping_state' => $address->state,
            'shipping_postal_code' => $address->postal_code,
            'shipping_country' => $address->country,
            'subtotal' => $pricing['subtotal'],
            'tax_total' => $pricing['tax_total'],
            'gst_total' => $pricing['gst_total'],
            'cgst_total' => $pricing['cgst_total'],
            'sgst_total' => $pricing['sgst_total'],
            'igst_total' => $pricing['igst_total'],
            'discount_total' => $pricing['discount_total'],
            'coins_redeemed' => $pricing['coins_redeemed'],
            'coin_discount' => $pricing['coin_discount'],
            'shipping_total' => $pricing['shipping_total'],
            'grand_total' => $pricing['grand_total'],
            'customer_note' => $validated['customer_note'] ?? null,
            'placed_at' => now(),
            'pricing_snapshot' => [
                'line_items' => $pricing['line_items'],
                'coupon' => $coupon?->only(['id', 'code', 'discount_type', 'discount_value']),
                'coins_earned' => $pricing['total_coins_earned'],
            ],
        ]);
    }

    private function recordCoinTransactions(Order $order, User $user, array $pricing): void
    {
        if ($order->coins_redeemed > 0) {
            $user->decrement('coins_balance', $order->coins_redeemed);
            CoinTransaction::create([
                'user_id' => $user->id,
                'order_id' => $order->id,
                'type' => 'spent',
                'amount' => $order->coins_redeemed,
                'description' => "Coins redeemed for discount on order #{$order->order_number}",
            ]);
        }

        $coinsToEarn = (int) $pricing['total_coins_earned'];
        if ($coinsToEarn > 0) {
            $user->increment('coins_balance', $coinsToEarn);
            CoinTransaction::create([
                'user_id' => $user->id,
                'order_id' => $order->id,
                'type' => 'earned',
                'amount' => $coinsToEarn,
                'description' => "Coins earned from purchase on order #{$order->order_number}",
            ]);
        }
    }

    private function createOrderItems(Order $order, array $lineItems): void
    {
        foreach ($lineItems as $lineItem) {
            $cartItem = $lineItem['cart_item'];
            $product = $cartItem->product;
            $variant = $cartItem->productVariant;

            $order->items()->create([
                'product_id' => $product->id,
                'product_variant_id' => $variant?->id,
                'product_name' => $variant ? "{$product->name} - {$variant->name}" : $product->name,
                'sku' => $variant?->sku ?? $product->sku,
                'quantity' => $lineItem['quantity'],
                'unit_price' => $lineItem['unit_price'],
                'tax_percent' => $lineItem['tax_percent'],
                'tax_code' => $lineItem['tax_code'],
                'tax_amount' => $lineItem['tax_amount'],
                'gst_amount' => $lineItem['tax_amount'],
                'discount_amount' => 0,
                'line_total' => $lineItem['unit_price'] * $lineItem['quantity'],
                'item_snapshot' => [
                    'product_slug' => $product->slug,
                    'variant_name' => $variant?->name,
                ],
            ]);
        }
    }

    private function createPayment(Order $order): void
    {
        Payment::create([
            'order_id' => $order->id,
            'provider' => 'cod',
            'transaction_type' => 'capture',
            'status' => 'pending',
            'currency' => 'INR',
            'amount' => $order->grand_total,
            'notes' => 'Cash on Delivery',
        ]);
    }

    private function recordCouponUsage(Order $order, ?Coupon $coupon, User $user): void
    {
        if (! $coupon) {
            return;
        }

        $coupon->increment('used_count');
        CouponUsage::create([
            'coupon_id' => $coupon->id,
            'user_id' => $user->id,
            'order_id' => $order->id,
        ]);
    }

    private function recordStatusHistory(Order $order, User $user): void
    {
        $order->statusHistories()->create([
            'from_status' => null,
            'to_status' => 'pending',
            'from_fulfillment_status' => null,
            'to_fulfillment_status' => 'unfulfilled',
            'updated_by' => $user->id,
            'note' => 'Order placed by customer.',
        ]);
    }

    private function generateOrderNumber(): string
    {
        return 'NB' . now()->format('Ymd') . strtoupper(Str::random(6));
    }

    private function sendOrderMails(Order $order): void
    {
        if ($order->customer_email) {
            Mail::to($order->customer_email)->queue(new OrderPlacedCustomerMail($order));
        }

        $adminEmails = User::where('role', 'admin')
            ->whereNotNull('email')
            ->pluck('email')
            ->all();

        foreach ($adminEmails as $adminEmail) {
            Mail::to($adminEmail)->queue(new OrderPlacedAdminMail($order));
        }
    }

    private function notifyAdmins(Order $order): void
    {
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new NewOrderNotification($order));
        }
    }
}
