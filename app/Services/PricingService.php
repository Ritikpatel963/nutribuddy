<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Product;
use App\Models\TaxRate;
use Illuminate\Support\Collection;

class PricingService
{
    public function calculate(Collection $cartItems, ?Coupon $coupon = null, int $coinsToRedeem = 0): array
    {
        $subtotal = 0.0;
        $taxTotal = 0.0;
        $shippingTotal = 0.0;
        $lineItems = [];
        $totalCoinsEarned = 0;

        // Variables for what the user sees in the checkout breakdown
        $displaySubtotal = 0.0;
        $hiddenTaxOnSubtotal = 0.0;
        $shownTaxOnSubtotal = 0.0;

        foreach ($cartItems as $cartItem) {
            /** @var Product $product */
            $product = $cartItem->product;
            if (! $product) {
                continue;
            }
            $variant = $cartItem->productVariant;
            $quantity = (int) $cartItem->quantity;
            $unitPrice = (float) ($variant?->price ?? $product->base_price);
            $lineSubTotal = $unitPrice * $quantity;

            /** @var TaxRate|null $taxRate */
            $taxRate = $product->taxRate;
            $taxPercent = (float) ($taxRate?->rate ?? 0);
            $showGstInCheckout = (bool) ($taxRate?->show_in_checkout ?? true);
            
            // Storefront prices are tax-exclusive, so apply GST ON TOP of the price.
            $lineTax = $taxPercent > 0
                ? ($lineSubTotal * $taxPercent) / 100
                : 0.0;
            $lineShipping = ((float) ($product->shipping_price ?? 0)) * $quantity;

            // Calculate Coins Earned for this line item
            $lineCoinsReward = (int) (!empty($product->coins_reward) ? $product->coins_reward : round($unitPrice * 0.05));
            $totalCoinsEarned += ($lineCoinsReward * $quantity);

            // Update actual totals
            $subtotal += $lineSubTotal;
            $taxTotal += $lineTax;
            $shippingTotal += $lineShipping;

            // Update display components
            if ($showGstInCheckout) {
                $displaySubtotal += $lineSubTotal;
                $shownTaxOnSubtotal += $lineTax;
            } else {
                // If hidden, add the tax directly into the subtotal (MRP) shown to the user
                $displaySubtotal += ($lineSubTotal + $lineTax);
                $hiddenTaxOnSubtotal += $lineTax;
            }

            // For itemized display
            $displayUnitPrice = $showGstInCheckout ? $unitPrice : ($unitPrice + ($unitPrice * $taxPercent) / 100);

            $lineItems[] = [
                'cart_item' => $cartItem,
                'quantity' => $quantity,
                'unit_price' => round($unitPrice, 2),
                'display_unit_price' => round($displayUnitPrice, 2),
                'display_line_total' => round($displayUnitPrice * $quantity, 2),
                'tax_percent' => round($taxPercent, 2),
                'tax_code' => $taxRate?->code,
                'tax_amount' => round($lineTax, 2),
                'show_gst_in_checkout' => $showGstInCheckout,
                'shipping_amount' => round($lineShipping, 2),
                'coins_earned' => $lineCoinsReward * $quantity,
            ];
        }

        // ══ COUPON LOGIC ══
        $discountTotal = 0.0;
        if ($coupon) {
            if ($coupon->discount_type === 'percentage') {
                // Apply percentage on the base subtotal
                $discountTotal = ($subtotal * (float) $coupon->discount_value) / 100;
            } else {
                // Fixed amount: user wants the full value to be deducted
                $discountTotal = (float) $coupon->discount_value;
            }

            if ($coupon->max_discount_amount !== null) {
                $discountTotal = min($discountTotal, (float) $coupon->max_discount_amount);
            }
            
            // Limit discount to Subtotal + Tax (don't go negative)
            $discountTotal = min($discountTotal, ($subtotal + $taxTotal));
        }

        // ══ COIN REDEMPTION LOGIC ══
        $coinDiscount = 0.0;
        $maxCoinDiscountPercent = (int) \App\Models\Setting::get('loyalty_max_redemption_percent', 30);
        $coinToCashRate = (int) \App\Models\Setting::get('loyalty_conversion_rate', 10);
        $isLoyaltyEnabled = (bool) \App\Models\Setting::get('loyalty_enabled', 1);
        
        if ($isLoyaltyEnabled && $coinsToRedeem > 0) {
            $requestedCashValue = $coinsToRedeem / $coinToCashRate;
            
            // User wants coins to be a flat deduction on the final price
            $potentialCoinDiscount = $requestedCashValue;

            // Limit based on max percentage of the total MRP
            $maxAllowedCoinDiscount = (($subtotal + $taxTotal) * $maxCoinDiscountPercent) / 100;
            
            // Apply after coupon
            $remainingBalance = ($subtotal + $taxTotal) - $discountTotal;
            $coinDiscount = min($potentialCoinDiscount, $maxAllowedCoinDiscount, $remainingBalance);
        }

        // ══ TOTALS CALCULATION ══
        // Display Subtotal and Tax are already accumulated correctly based on the `show_in_checkout` flag
        $displayTaxTotal = $shownTaxOnSubtotal;

        // Discount components for display
        $displayCouponDiscount = $discountTotal;
        $displayCoinDiscount = $coinDiscount;
        $displayDiscountTotal = $discountTotal + $coinDiscount;

        // Calculations for DB / Logic
        $grandTotal = ($subtotal + $taxTotal + $shippingTotal) - $discountTotal - $coinDiscount;
        $grandTotal = max(0, $grandTotal);

        return [
            'line_items' => $lineItems,
            'subtotal' => round($subtotal, 2),
            'tax_total' => round($taxTotal, 2),
            'discount_total' => round($discountTotal, 2),
            'coin_discount' => round($coinDiscount, 2),
            'coins_redeemed' => $coinsToRedeem,
            'total_coins_earned' => $totalCoinsEarned,
            'display_subtotal' => round($displaySubtotal, 2),
            'display_tax_total' => round($displayTaxTotal, 2),
            'display_discount_total' => round($displayDiscountTotal, 2),
            'display_coupon_discount' => round($displayCouponDiscount, 2),
            'display_coin_discount' => round($displayCoinDiscount, 2),
            'gst_total' => round($taxTotal, 2),
            'cgst_total' => round($taxTotal / 2, 2),
            'sgst_total' => round($taxTotal / 2, 2),
            'igst_total' => 0.0,
            'shipping_total' => round($shippingTotal, 2),
            'grand_total' => round($grandTotal, 2),
        ];
    }
}
