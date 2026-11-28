<?php

namespace App\Exports\Orders;

use App\Models\Order;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrdersDataSheet implements FromArray, ShouldAutoSize, WithStyles, WithTitle
{
    public function __construct(private readonly Collection $orders) {}

    public function array(): array
    {
        $headers = [
            'Order #',
            'Invoice #',
            'Date & Time',
            'Customer Name',
            'Customer Phone',
            'Customer Email',
            'Order Status',
            'Payment Status',
            'Payment Method',
            'Payment Txn ID',
            'Total Items',
            'Items Breakdown',
            'Subtotal (INR)',
            'Tax / GST (INR)',
            'Discount (INR)',
            'Coins Redeemed',
            'Coin Discount (INR)',
            'Shipping (INR)',
            'Grand Total (INR)',
            'Shipping Address',
            'City',
            'State',
            'Pincode',
            'Country',
            'Customer Note',
        ];

        $rows = [$headers];

        foreach ($this->orders as $order) {
            /** @var Order $order */
            $itemsSummary = $order->items->map(function ($item) {
                $name = $item->product?->name ?: ($item->item_snapshot['name'] ?? 'Product');
                $variant = $item->productVariant?->name ?: ($item->item_snapshot['variant_name'] ?? '');
                $title = $variant && $variant !== 'Default' ? "{$name} ({$variant})" : $name;

                return "{$title} x {$item->quantity}";
            })->implode('; ');

            $addressParts = array_filter([
                $order->shipping_address_line_1,
                $order->shipping_address_line_2,
                $order->shipping_landmark,
            ]);

            $rows[] = [
                $order->order_number,
                $order->display_invoice_number,
                optional($order->placed_at ?? $order->created_at)->format('Y-m-d H:i:s'),
                $order->customer_name ?: ($order->shipping_name ?: 'N/A'),
                $order->customer_phone ?: ($order->shipping_phone ?: 'N/A'),
                $order->customer_email ?: 'N/A',
                ucfirst($order->status),
                ucfirst($order->payment_status),
                strtoupper($order->payment_method),
                $order->transaction_id ?: 'N/A',
                $order->items_count ?? $order->items->count(),
                $itemsSummary ?: 'N/A',
                number_format((float) $order->subtotal, 2, '.', ''),
                number_format((float) $order->tax_total, 2, '.', ''),
                number_format((float) $order->discount_total, 2, '.', ''),
                (int) $order->coins_redeemed,
                number_format((float) $order->coin_discount, 2, '.', ''),
                number_format((float) $order->shipping_total, 2, '.', ''),
                number_format((float) $order->grand_total, 2, '.', ''),
                implode(', ', $addressParts),
                $order->shipping_city ?: 'N/A',
                $order->shipping_state ?: 'N/A',
                $order->shipping_postal_code ?: 'N/A',
                $order->shipping_country ?: 'India',
                $order->customer_note ?: '',
            ];
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
        return 'All Orders Data';
    }
}
