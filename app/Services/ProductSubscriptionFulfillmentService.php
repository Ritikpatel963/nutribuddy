<?php

namespace App\Services;

use App\Mail\OrderPlacedCustomerMail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductSubscription;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ProductSubscriptionFulfillmentService
{
    public function __construct(
        protected StockService $stockService
    ) {}

    /**
     * Fulfill a cycle of a product subscription by creating an Order and OrderItems.
     */
    public function fulfillCycle(
        ProductSubscription $subscription,
        int $cycleNumber,
        float $chargedAmount,
        ?string $transactionId = null
    ): ?Order {
        // Idempotency: Check if an order already exists for this subscription and cycle
        $existingOrder = Order::where('product_subscription_id', $subscription->id)
            ->where('admin_note', 'like', "%Cycle #{$cycleNumber}%")
            ->first();

        if ($existingOrder) {
            Log::info("Subscription #{$subscription->id} Cycle #{$cycleNumber} already fulfilled as Order #{$existingOrder->id}");
            return $existingOrder;
        }

        return DB::transaction(function () use ($subscription, $cycleNumber, $chargedAmount, $transactionId) {
            $user = $subscription->user;
            $variant = $subscription->variant ?: ProductVariant::with('product')->find($subscription->variant_id);
            $product = $subscription->product ?: $variant->product;
            $plan = $subscription->plan;
            $address = $subscription->shipping_address ?: [];

            $orderNumber = 'ORD-SUB-' . strtoupper(Str::random(8));
            $invoiceNumber = 'NB/' . date('Y-y') . '/' . str_pad((string) (Order::max('id') + 1), 4, '0', STR_PAD_LEFT);

            $order = Order::create([
                'order_number' => $orderNumber,
                'invoice_number' => $invoiceNumber,
                'checkout_token' => 'SUB_' . Str::random(24),
                'user_id' => $user->id,
                'product_subscription_id' => $subscription->id,
                'status' => 'processing',
                'fulfillment_status' => 'unfulfilled',
                'payment_status' => 'paid',
                'payment_method' => 'cashfree_subscription',
                'currency' => 'INR',
                'customer_name' => $address['name'] ?? $user->name,
                'customer_email' => $user->email,
                'customer_phone' => $address['phone'] ?? $user->phone,
                'shipping_name' => $address['name'] ?? $user->name,
                'shipping_phone' => $address['phone'] ?? $user->phone,
                'shipping_address_line_1' => $address['address_line_1'] ?? 'Address line 1',
                'shipping_address_line_2' => $address['address_line_2'] ?? null,
                'shipping_city' => $address['city'] ?? 'City',
                'shipping_state' => $address['state'] ?? 'State',
                'shipping_postal_code' => $address['postal_code'] ?? '000000',
                'shipping_country' => $address['country'] ?? 'India',
                'subtotal' => $chargedAmount,
                'tax_total' => 0.00,
                'gst_total' => 0.00,
                'cgst_total' => 0.00,
                'sgst_total' => 0.00,
                'igst_total' => 0.00,
                'discount_total' => 0.00,
                'shipping_total' => 0.00,
                'grand_total' => $chargedAmount,
                'placed_at' => now(),
                'admin_note' => "Auto-generated from Subscription #{$subscription->id} (Cycle #{$cycleNumber} of {$subscription->max_cycles}). Transaction: " . ($transactionId ?: 'N/A'),
            ]);

            // 1. Primary Line Item
            $item = OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_variant_id' => $variant->id,
                'product_name' => $product->name,
                'variant_name' => $variant->name,
                'sku' => $variant->sku ?: ($product->sku ?: 'SKU-' . $variant->id),
                'unit_price' => $chargedAmount,
                'quantity' => 1,
                'subtotal' => $chargedAmount,
                'tax_total' => 0.00,
                'total' => $chargedAmount,
            ]);

            // Deduct stock (respects virtual bundle if combo)
            $this->stockService->deductStock($item);

            // 2. Free Gift Line Item (Cycle 1 perk if configured)
            if ($cycleNumber === 1 && $plan && $plan->free_gift_variant_id) {
                $giftVariant = ProductVariant::with('product')->find($plan->free_gift_variant_id);
                if ($giftVariant) {
                    $giftItem = OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $giftVariant->product_id,
                        'product_variant_id' => $giftVariant->id,
                        'product_name' => $giftVariant->product->name . ' (Free Subscription Gift)',
                        'variant_name' => $giftVariant->name,
                        'sku' => $giftVariant->sku ?: 'GIFT-' . $giftVariant->id,
                        'unit_price' => 0.00,
                        'quantity' => 1,
                        'subtotal' => 0.00,
                        'tax_total' => 0.00,
                        'total' => 0.00,
                    ]);

                    $this->stockService->deductStock($giftItem);
                }
            }

            // Update Subscription Cycle Status
            $newCycleCount = max($subscription->cycles_completed, $cycleNumber);
            $isComplete = $newCycleCount >= $subscription->max_cycles;

            $subscription->update([
                'status' => $isComplete ? 'COMPLETED' : 'ACTIVE',
                'cycles_completed' => $newCycleCount,
                'last_billed_at' => now(),
                'next_billing_at' => $isComplete ? null : now()->addMonth(),
            ]);

            // Send Confirmation Email
            try {
                if ($user->email) {
                    Mail::to($user->email)->queue(new OrderPlacedCustomerMail($order));
                }
            } catch (\Throwable $mailErr) {
                Log::warning("Subscription Order #{$order->id} email dispatch warning: " . $mailErr->getMessage());
            }

            Log::info("Subscription #{$subscription->id} successfully fulfilled Cycle #{$cycleNumber} as Order #{$order->id}");

            return $order;
        });
    }
}
