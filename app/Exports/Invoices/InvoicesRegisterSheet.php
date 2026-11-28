<?php

namespace App\Exports\Invoices;

use App\Models\Order;
use App\Models\Setting;
use App\Services\PricingService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InvoicesRegisterSheet implements FromArray, ShouldAutoSize, WithStyles, WithTitle
{
    public function __construct(private readonly Collection $orders) {}

    public function array(): array
    {
        $headers = [
            'Invoice number',
            'Invoice date',
            'Invoice value',
            'HSN number',
            'Goods description',
            'UOM',
            'Quantity',
            'Total value',
            'GST rate',
            'Taxable value',
            'Integrated tax amount',
            'Central tax amount',
            'State tax amount',
            'Customer name',
            'Customer address',
            'Customer GST',
            'Place of supply',
        ];

        $rows = [$headers];
        $sellerState = Setting::get('store_state', 'Gujarat');

        foreach ($this->orders as $order) {
            /** @var Order $order */
            $invoiceNumber = $order->invoice_number ?: ('INV-'.$order->order_number);
            $invoiceDate = optional($order->placed_at ?? $order->created_at)->format('Y-m-d');
            $invoiceValue = number_format((float) $order->grand_total, 2, '.', '');
            $customerName = $order->customer_name ?: ($order->shipping_name ?: 'N/A');

            $addressParts = array_filter([
                $order->shipping_address_line_1,
                $order->shipping_address_line_2,
                $order->shipping_landmark,
                $order->shipping_city,
                $order->shipping_state ? ($order->shipping_state.($order->shipping_postal_code ? ' - '.$order->shipping_postal_code : '')) : null,
                $order->shipping_country ?: 'India',
            ]);
            $customerAddress = ! empty($addressParts) ? implode(', ', $addressParts) : 'N/A';
            $customerGst = $order->customer_gstin ?: ($order->user?->gstin ?: 'URP');
            $placeOfSupply = $order->shipping_state ?: ($order->billing_state ?: 'India');

            // Determine Interstate vs Intrastate tax split
            if ((float) $order->igst_total > 0) {
                $isInterState = true;
            } elseif ((float) $order->cgst_total > 0 || (float) $order->sgst_total > 0) {
                $isInterState = false;
            } else {
                $isInterState = ! PricingService::isSameState($placeOfSupply, $sellerState);
            }

            if ($order->items->isEmpty()) {
                $taxAmt = (float) $order->tax_total;
                $igstAmt = $isInterState ? $taxAmt : 0.0;
                $cgstAmt = $isInterState ? 0.0 : round($taxAmt / 2, 2);
                $sgstAmt = $isInterState ? 0.0 : round($taxAmt / 2, 2);
                $taxableAmt = max(0.0, (float) $order->grand_total - $taxAmt);

                $rows[] = [
                    $invoiceNumber,
                    $invoiceDate,
                    $invoiceValue,
                    '210690',
                    'Order #'.$order->order_number,
                    'NOS',
                    1,
                    $invoiceValue,
                    '18%',
                    number_format($taxableAmt, 2, '.', ''),
                    number_format($igstAmt, 2, '.', ''),
                    number_format($cgstAmt, 2, '.', ''),
                    number_format($sgstAmt, 2, '.', ''),
                    $customerName,
                    $customerAddress,
                    $customerGst,
                    $placeOfSupply,
                ];

                continue;
            }

            foreach ($order->items as $item) {
                $hsn = $item->item_snapshot['hsn_code'] ?? ($item->product?->hsn_code ?? '210690');
                $prodName = $item->product?->name ?: ($item->item_snapshot['name'] ?? ($item->product_name ?: 'Product'));
                $variantName = $item->productVariant?->name ?: ($item->item_snapshot['variant_name'] ?? '');
                $goodsDescription = ($variantName && $variantName !== 'Default') ? "{$prodName} ({$variantName})" : $prodName;
                $uom = $item->item_snapshot['uom'] ?? 'NOS';
                $quantity = (int) $item->quantity;

                $unitPrice = (float) $item->unit_price;
                $lineTotal = (float) $item->line_total;
                $itemTax = (float) $item->tax_amount;

                if ($unitPrice > 0 && $quantity > 0) {
                    $taxableValue = $unitPrice * $quantity;
                } else {
                    $taxableValue = max(0.0, $lineTotal - $itemTax);
                }

                $totalValue = $lineTotal > 0 ? $lineTotal : ($taxableValue + $itemTax);

                $rate = (float) $item->tax_percent;
                if ($rate <= 0 && isset($item->item_snapshot['tax_rate'])) {
                    $rate = (float) $item->item_snapshot['tax_rate'];
                }
                if ($rate <= 0 && $taxableValue > 0 && $itemTax > 0) {
                    $rate = round(($itemTax / $taxableValue) * 100, 2);
                }
                if ($rate <= 0) {
                    $rate = 18.0;
                }
                $gstRate = ((float) $rate).'%';

                if ($isInterState) {
                    $igst = $itemTax;
                    $cgst = 0.0;
                    $sgst = 0.0;
                } else {
                    $igst = 0.0;
                    $cgst = round($itemTax / 2, 2);
                    $sgst = round($itemTax / 2, 2);
                }

                $rows[] = [
                    $invoiceNumber,
                    $invoiceDate,
                    $invoiceValue,
                    (string) $hsn,
                    $goodsDescription,
                    $uom,
                    $quantity,
                    number_format($totalValue, 2, '.', ''),
                    $gstRate,
                    number_format($taxableValue, 2, '.', ''),
                    number_format($igst, 2, '.', ''),
                    number_format($cgst, 2, '.', ''),
                    number_format($sgst, 2, '.', ''),
                    $customerName,
                    $customerAddress,
                    $customerGst,
                    $placeOfSupply,
                ];
            }
        }

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Invoice Register';
    }
}
