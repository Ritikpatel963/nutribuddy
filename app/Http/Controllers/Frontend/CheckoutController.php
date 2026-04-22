<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\CheckoutPlaceOrderRequest;
use App\Http\Requests\Frontend\CheckoutSummaryRequest;
use App\Mail\OrderPlacedAdminMail;
use App\Mail\OrderPlacedCustomerMail;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\CustomerAddress;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use App\Services\PricingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function summary(CheckoutSummaryRequest $request, PricingService $pricingService): JsonResponse
    {
        $validated = $request->validated();

        $coupon = null;
        if (! empty($validated['coupon_code'])) {
            $coupon = Coupon::whereRaw('UPPER(code) = ?', [strtoupper(trim($validated['coupon_code']))])->first();
            if (! $coupon || ! $coupon->isCurrentlyValid()) {
                return response()->json(['message' => 'Invalid or expired coupon code.'], 422);
            }
        }

        $cart = $this->resolveUserCart($request)->load(['items.product.taxRate', 'items.productVariant']);
        if ($cart->items->isEmpty()) {
            return response()->json(['message' => 'Cart is empty.'], 422);
        }

        $pricing = $pricingService->calculate($cart->items, $coupon);

        if ($coupon && $coupon->min_order_amount !== null && $pricing['subtotal'] < (float) $coupon->min_order_amount) {
            return response()->json(['message' => 'Coupon minimum order amount not met.'], 422);
        }

        return response()->json([
            'coupon' => $coupon,
            'pricing' => $pricing,
            'checkout_token' => (string) Str::uuid(),
        ]);
    }

    public function placeOrder(CheckoutPlaceOrderRequest $request, PricingService $pricingService): JsonResponse
    {
        $validated = $request->validated();

        $user = $request->user();
        $address = CustomerAddress::where('user_id', $user->id)->findOrFail($validated['address_id']);
        $coupon = $this->resolveCoupon($validated['coupon_code'] ?? null, $user->id);
        $checkoutToken = trim((string) ($validated['checkout_token'] ?? ''));
        if ($checkoutToken === '') {
            $checkoutToken = (string) Str::uuid();
        }

        $existingOrder = Order::where('user_id', $user->id)
            ->where('checkout_token', $checkoutToken)
            ->first();

        if ($existingOrder) {
            return response()->json([
                'message' => 'Order already placed for this checkout request.',
                'order' => $existingOrder->load(['items', 'payments', 'statusHistories']),
            ]);
        }

        $order = DB::transaction(function () use ($request, $user, $address, $coupon, $pricingService, $validated, $checkoutToken) {
            $cart = $this->resolveUserCart($request)->load(['items.product.taxRate', 'items.productVariant']);
            if ($cart->items->isEmpty()) {
                abort(422, 'Cart is empty.');
            }

            $pricing = $pricingService->calculate($cart->items, $coupon);
            $this->validateCouponRules($coupon, $pricing['subtotal'], $user->id);
            $this->validateAndReserveInventory($cart);

            $order = Order::create([
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
                'shipping_total' => $pricing['shipping_total'],
                'grand_total' => $pricing['grand_total'],
                'customer_note' => $validated['customer_note'] ?? null,
                'placed_at' => now(),
                'pricing_snapshot' => [
                    'line_items' => $pricing['line_items'],
                    'coupon' => $coupon?->only(['id', 'code', 'discount_type', 'discount_value']),
                ],
            ]);

            foreach ($pricing['line_items'] as $lineItem) {
                $cartItem = $lineItem['cart_item'];
                $product = $cartItem->product;
                $variant = $cartItem->productVariant;

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_variant_id' => $variant?->id,
                    'product_name' => $product->name,
                    'sku' => $variant?->sku ?? $product->sku,
                    'quantity' => $lineItem['quantity'],
                    'unit_price' => $lineItem['unit_price'],
                    'tax_percent' => $lineItem['tax_percent'],
                    'tax_code' => $lineItem['tax_code'],
                    'tax_amount' => $lineItem['tax_amount'],
                    'gst_amount' => $lineItem['tax_amount'],
                    'discount_amount' => 0,
                    'line_total' => ($lineItem['unit_price'] * $lineItem['quantity']) + $lineItem['tax_amount'],
                    'item_snapshot' => [
                        'product_slug' => $product->slug,
                        'variant_name' => $variant?->name,
                    ],
                ]);
            }

            Payment::create([
                'order_id' => $order->id,
                'provider' => 'cod',
                'transaction_type' => 'capture',
                'status' => 'pending',
                'currency' => 'INR',
                'amount' => $order->grand_total,
                'notes' => 'Cash on Delivery',
            ]);

            if ($coupon) {
                $coupon->increment('used_count');
                CouponUsage::create([
                    'coupon_id' => $coupon->id,
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                ]);
            }

            $order->statusHistories()->create([
                'from_status' => null,
                'to_status' => 'pending',
                'from_fulfillment_status' => null,
                'to_fulfillment_status' => 'unfulfilled',
                'updated_by' => $user->id,
                'note' => 'Order placed by customer.',
            ]);

            $cart->items()->delete();

            return $order->load(['items', 'payments', 'statusHistories']);
        });

        $this->sendOrderMails($order);
        $this->notifyAdmins($order);

        return response()->json([
            'message' => 'Order placed successfully.',
            'order' => $order,
        ]);
    }

    private function resolveCoupon(?string $couponCode, int $userId): ?Coupon
    {
        if (! $couponCode) {
            return null;
        }

        $coupon = Coupon::whereRaw('UPPER(code) = ?', [strtoupper(trim($couponCode))])->first();
        if (! $coupon || ! $coupon->isCurrentlyValid()) {
            abort(422, 'Invalid or expired coupon code.');
        }

        $userUsageCount = CouponUsage::where('coupon_id', $coupon->id)->where('user_id', $userId)->count();
        if ($coupon->usage_limit_per_user !== null && $userUsageCount >= $coupon->usage_limit_per_user) {
            abort(422, 'Coupon usage limit reached for this user.');
        }

        return $coupon;
    }

    private function validateCouponRules(?Coupon $coupon, float $subtotal, int $userId): void
    {
        if (! $coupon) {
            return;
        }

        if ($coupon->min_order_amount !== null && $subtotal < (float) $coupon->min_order_amount) {
            abort(422, 'Coupon minimum order amount not met.');
        }

        if ($coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit) {
            abort(422, 'Coupon usage limit reached.');
        }

        $userUsageCount = CouponUsage::where('coupon_id', $coupon->id)->where('user_id', $userId)->count();
        if ($coupon->usage_limit_per_user !== null && $userUsageCount >= $coupon->usage_limit_per_user) {
            abort(422, 'Coupon usage limit reached for this user.');
        }
    }

    private function validateAndReserveInventory(Cart $cart): void
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
            if ($available < $item->quantity) {
                abort(422, 'Some cart items are out of stock.');
            }

            $inventory->update([
                'stock_qty' => $inventory->stock_qty - $item->quantity,
                'is_in_stock' => ($inventory->stock_qty - $item->quantity) > 0,
            ]);
        }
    }

    private function resolveUserCart(Request $request): Cart
    {
        return Cart::firstOrCreate(
            ['user_id' => $request->user()->id],
            ['currency' => 'INR']
        );
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
