<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $order->order_number }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700;800&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            color: #1a1a1a;
            margin: 0;
            padding: 40px;
            line-height: 1.5;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 30px;
            margin-bottom: 40px;
        }
        .logo img {
            max-height: 60px;
        }
        .invoice-info {
            text-align: right;
        }
        .invoice-info h1 {
            margin: 0;
            font-size: 32px;
            font-weight: 800;
            color: #FF4D8F;
        }
        .invoice-info p {
            margin: 5px 0 0;
            color: #666;
            font-size: 14px;
        }
        .details-grid {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            gap: 40px;
        }
        .details-col {
            flex: 1;
        }
        .details-col h4 {
            margin: 0 0 12px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #FF4D8F;
        }
        .details-col p {
            margin: 0;
            font-size: 14px;
            color: #333;
        }
        .details-col .muted {
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        th {
            background: #f9f9f9;
            text-align: left;
            padding: 12px 15px;
            font-size: 12px;
            text-transform: uppercase;
            color: #666;
            border-bottom: 2px solid #eee;
        }
        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
            vertical-align: top;
        }
        .item-name { font-weight: 700; color: #000; }
        .item-meta { font-size: 12px; color: #666; margin-top: 3px; }
        .item-spec { font-size: 11px; color: #7C3AED; font-weight: 700; margin-top: 4px; display: block; }
        
        .summary {
            display: flex;
            justify-content: flex-end;
        }
        .summary-table {
            width: 250px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 14px;
        }
        .summary-row.total {
            border-top: 2px solid #000;
            margin-top: 10px;
            padding-top: 15px;
            font-size: 18px;
            font-weight: 800;
            color: #7C3AED;
        }
        .footer {
            margin-top: 80px;
            text-align: center;
            font-size: 12px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="invoice-box" id="printableInvoice">
        <div class="header">
            <div class="logo">
                <img src="{{ asset('assets/images/logo.png') }}" alt="NutriBuddy">
            </div>
            <div class="invoice-info">
                <h1>INVOICE</h1>
                <p>#INV-{{ $order->order_number }}</p>
                <p>Issued: {{ optional($order->placed_at)->format('d/m/Y') ?? $order->created_at->format('d/m/Y') }}</p>
            </div>
        </div>

        <div class="details-grid">
            <div class="details-col">
                <h4>Bill To</h4>
                <p><strong>{{ $order->customer_name }}</strong></p>
                <p class="muted">{{ $order->customer_email }}</p>
                <p class="muted">{{ $order->customer_phone }}</p>
            </div>
            <div class="details-col">
                <h4>Ship To</h4>
                <p><strong>{{ $order->shipping_name }}</strong></p>
                <p class="muted">{{ $order->shipping_address_line_1 }}</p>
                @if($order->shipping_address_line_2)<p class="muted">{{ $order->shipping_address_line_2 }}</p>@endif
                <p class="muted">{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}</p>
            </div>
            <div class="details-col">
                <h4>Payment</h4>
                <p>Method: <strong>{{ strtoupper($order->payment_method) }}</strong></p>
                <p>Status: <strong>{{ strtoupper($order->payment_status) }}</strong></p>
                <p>Currency: <strong>{{ $order->currency ?? 'INR' }}</strong></p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align: center;">Qty</th>
                    <th style="text-align: right;">Price</th>
                    <th style="text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    @php
                        $vName = $item->item_snapshot['variant_name'] ?? ($item->productVariant?->name ?? null);
                        $variant = $item->productVariant;
                        $product = $item->product;
                        $specs = collect();
                        
                        if ($product) {
                            if ($product->flavor || $product->flavour) $specs->put('flavor', "Flavor: " . ($product->flavor ?? $product->flavour));
                            if ($product->pack_size) $specs->put('pack', "Pack Size: " . $product->pack_size);
                            if ($product->age_group) $specs->put('age', "Age Group: " . $product->age_group);
                        }

                        if ($variant && $variant->attributes) {
                            foreach($variant->attributes as $k => $v) {
                                $key = strtolower(str_replace(['_', '-'], ' ', $k));
                                if (str_contains($key, 'flav') || str_contains($key, 'pack') || str_contains($key, 'age')) {
                                    continue;
                                }
                                $specs->put($key, ucfirst($k) . ": " . $v);
                            }
                        }
                        $specs = $specs->values();
                    @endphp
                    <tr>
                        <td>
                            <div class="item-name">{{ $item->product_name }}</div>
                            @if($vName)
                                <div class="item-meta">{{ $vName }}</div>
                            @endif
                            @foreach($specs as $spec)
                                <span class="item-spec">{{ $spec }}</span>
                            @endforeach
                        </td>
                        <td style="text-align: center;">{{ $item->quantity }}</td>
                        <td style="text-align: right;">₹{{ number_format($item->unit_price, 2) }}</td>
                        <td style="text-align: right;">₹{{ number_format($item->line_total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary">
            <div class="summary-table">
                <div class="summary-row"><span>Subtotal</span><span>₹{{ number_format($order->subtotal, 2) }}</span></div>
                @if($order->discount_total > 0)
                    <div class="summary-row"><span>Discount</span><span>- ₹{{ number_format($order->discount_total, 2) }}</span></div>
                @endif
                @if($order->tax_total > 0)
                    <div class="summary-row"><span>Tax</span><span>₹{{ number_format($order->tax_total, 2) }}</span></div>
                @endif
                <div class="summary-row"><span>Shipping</span><span>₹{{ number_format($order->shipping_total, 2) }}</span></div>
                <div class="summary-row total">
                    <span>Total</span>
                    <span>₹{{ number_format($order->grand_total, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>NutriBuddy E-commerce Solutions · support@nutribuddy.com</p>
            <p>This is a computer-generated document and does not require a signature.</p>
        </div>
    </div>

    <script>
        // Auto-print if the URL has ?print=1
        if (window.location.search.indexOf('print=1') > -1) {
            window.print();
        }
    </script>
</body>
</html>
