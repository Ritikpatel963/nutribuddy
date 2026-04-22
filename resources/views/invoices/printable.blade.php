<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $invoiceNumber }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; color: #222; }
        .header, .row, .totals { display: flex; justify-content: space-between; gap: 24px; }
        .card { border: 1px solid #ddd; border-radius: 8px; padding: 20px; margin-bottom: 20px; }
        .muted { color: #666; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 14px; }
        th { background: #f7f7f7; }
        .text-end { text-align: right; }
        .grand { font-size: 18px; font-weight: bold; }
        @media print { .no-print { display: none; } body { margin: 0; } }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom:16px;">
        <button onclick="window.print()">Print Invoice</button>
    </div>

    <div class="card">
        <div class="header">
            <div>
                <h2 style="margin:0 0 8px;">{{ $invoiceNumber }}</h2>
                <div class="muted">Order: {{ $order->order_number }}</div>
                <div class="muted">Date: {{ optional($order->placed_at)->format('d M Y') ?? optional($order->created_at)->format('d M Y') }}</div>
            </div>
            <div style="text-align:right;">
                <h3 style="margin:0 0 8px;">NutriBuddy</h3>
                <div class="muted">Payment: {{ strtoupper($order->payment_method) }}</div>
                <div class="muted">Status: {{ strtoupper($order->payment_status) }}</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="card" style="flex:1;">
            <h4 style="margin-top:0;">Bill To</h4>
            <div>{{ $order->customer_name }}</div>
            <div class="muted">{{ $order->customer_email }}</div>
            <div class="muted">{{ $order->customer_phone }}</div>
        </div>
        <div class="card" style="flex:1;">
            <h4 style="margin-top:0;">Ship To</h4>
            <div>{{ $order->shipping_name }}</div>
            <div class="muted">{{ $order->shipping_phone }}</div>
            <div class="muted">{{ $order->shipping_address_line_1 }} {{ $order->shipping_address_line_2 }}</div>
            <div class="muted">{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}</div>
            <div class="muted">{{ $order->shipping_country }}</div>
        </div>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Qty</th>
                    <th class="text-end">Unit</th>
                    <th class="text-end">Tax</th>
                    <th class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->sku }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td class="text-end">INR {{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-end">INR {{ number_format($item->tax_amount, 2) }}</td>
                        <td class="text-end">INR {{ number_format($item->line_total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card totals">
        <div></div>
        <div style="min-width:320px;">
            <div class="row"><span>Subtotal</span><span>INR {{ number_format($order->subtotal, 2) }}</span></div>
            <div class="row"><span>Discount</span><span>INR {{ number_format($order->discount_total, 2) }}</span></div>
            <div class="row"><span>Tax</span><span>INR {{ number_format($order->tax_total, 2) }}</span></div>
            <div class="row"><span>GST</span><span>INR {{ number_format($order->gst_total, 2) }}</span></div>
            <div class="row"><span>Shipping</span><span>INR {{ number_format($order->shipping_total, 2) }}</span></div>
            <hr>
            <div class="row grand"><span>Grand Total</span><span>INR {{ number_format($order->grand_total, 2) }}</span></div>
        </div>
    </div>
</body>
</html>
