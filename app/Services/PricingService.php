<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Product;
use App\Models\TaxRate;
use Illuminate\Support\Collection;

class PricingService
{
    public function calculate(Collection $cartItems, ?Coupon $coupon = null): array
    {
        $subtotal = 0.0;
        $taxTotal = 0.0;
        $shippingTotal = 0.0;
        $lineItems = [];

        foreach ($cartItems as $cartItem) {
            /** @var Product $product */
            $product = $cartItem->product;
            $variant = $cartItem->productVariant;
            $quantity = (int) $cartItem->quantity;
            $unitPrice = (float) ($variant?->price ?? $product->base_price);
            $lineSubTotal = $unitPrice * $quantity;

            /** @var TaxRate|null $taxRate */
            $taxRate = $product->taxRate;
            $taxPercent = (float) ($taxRate?->rate ?? 0);
            // Storefront prices are tax-inclusive, so extract GST from the price
            // instead of adding it on top again during checkout.
            $lineTax = $taxPercent > 0
                ? ($lineSubTotal * $taxPercent) / (100 + $taxPercent)
                : 0.0;
            $lineShipping = ((float) ($product->shipping_price ?? 0)) * $quantity;

            $subtotal += $lineSubTotal;
            $taxTotal += $lineTax;
            $shippingTotal += $lineShipping;

            $lineItems[] = [
                'cart_item' => $cartItem,
                'quantity' => $quantity,
                'unit_price' => round($unitPrice, 2),
                'tax_percent' => round($taxPercent, 2),
                'tax_code' => $taxRate?->code,
                'tax_amount' => round($lineTax, 2),
                'shipping_amount' => round($lineShipping, 2),
            ];
        }

        $discountTotal = 0.0;
        if ($coupon) {
            if ($coupon->discount_type === 'percentage') {
                $discountTotal = ($subtotal * (float) $coupon->discount_value) / 100;
            } else {
                $discountTotal = (float) $coupon->discount_value;
            }

            if ($coupon->max_discount_amount !== null) {
                $discountTotal = min($discountTotal, (float) $coupon->max_discount_amount);
            }

            $discountTotal = min($discountTotal, $subtotal);
        }

        $taxableAmount = max(0, $subtotal - $discountTotal);
        $effectiveTaxTotal = $subtotal > 0
            ? ($taxTotal * $taxableAmount) / $subtotal
            : 0.0;
        $gstTotal = $effectiveTaxTotal;
        $cgstTotal = round($gstTotal / 2, 2);
        $sgstTotal = round($gstTotal / 2, 2);
        $igstTotal = 0.0;
        $grandTotal = $taxableAmount + $shippingTotal;

        return [
            'line_items' => $lineItems,
            'subtotal' => round($subtotal, 2),
            'discount_total' => round($discountTotal, 2),
            'tax_total' => round($effectiveTaxTotal, 2),
            'gst_total' => round($gstTotal, 2),
            'cgst_total' => round($cgstTotal, 2),
            'sgst_total' => round($sgstTotal, 2),
            'igst_total' => round($igstTotal, 2),
            'shipping_total' => round($shippingTotal, 2),
            'grand_total' => round($grandTotal, 2),
        ];
    }
}
