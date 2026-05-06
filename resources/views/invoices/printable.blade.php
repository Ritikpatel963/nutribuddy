<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $order->order_number }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            color: #1a1a1a;
            background: #fff;
            padding: 40px;
            line-height: 1.5;
            font-size: 14px;
        }

        .invoice-box {
            max-width: 820px;
            margin: auto;
        }

        /* ── HEADER ── */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 3px solid #FF4D8F;
            padding-bottom: 28px;
            margin-bottom: 36px;
        }

        .company-block { max-width: 340px; }
        .company-block img { max-height: 55px; margin-bottom: 10px; display: block; }
        .company-block .company-name { font-size: 15px; font-weight: 800; color: #111; margin-bottom: 4px; }
        .company-block p { font-size: 12px; color: #555; margin: 2px 0; line-height: 1.5; }

        .company-legal { margin-top: 10px; display: flex; flex-wrap: wrap; gap: 6px; }
        .legal-tag {
            background: #f3e8ff;
            color: #6d28d9;
            font-size: 10px;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 5px;
            letter-spacing: 0.4px;
        }

        .invoice-info { text-align: right; }
        .invoice-info h1 {
            font-size: 36px;
            font-weight: 800;
            color: #FF4D8F;
            letter-spacing: -1px;
            line-height: 1;
        }
        .invoice-info .inv-num { font-size: 14px; color: #555; margin: 6px 0; }
        .invoice-info .inv-date { font-size: 12px; color: #888; }
        .status-badge {
            display: inline-block;
            margin-top: 10px;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }
        .status-paid   { background: #d1fae5; color: #065f46; }
        .status-unpaid { background: #fef3c7; color: #92400e; }

        /* ── DETAILS GRID ── */
        .details-grid {
            display: flex;
            justify-content: space-between;
            gap: 30px;
            margin-bottom: 36px;
            background: #fafafa;
            border-radius: 12px;
            padding: 24px;
            border: 1px solid #f0f0f0;
        }
        .details-col { flex: 1; }
        .details-col h4 {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #FF4D8F;
            font-weight: 800;
            margin-bottom: 12px;
        }
        .details-col p { font-size: 13px; color: #333; margin: 3px 0; }
        .details-col .muted { color: #666; font-size: 12px; }

        /* ── ITEMS TABLE ── */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 36px;
        }
        th {
            background: #fafafa;
            padding: 12px 14px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #888;
            border-bottom: 2px solid #eee;
            font-weight: 700;
        }
        td {
            padding: 16px 14px;
            border-bottom: 1px solid #f5f5f5;
            font-size: 13px;
            vertical-align: top;
        }
        tr:last-child td { border-bottom: none; }

        .item-name { font-weight: 700; color: #111; font-size: 14px; }
        .item-meta { font-size: 11px; color: #888; margin-top: 3px; }
        .item-specs { margin-top: 6px; display: flex; flex-wrap: wrap; gap: 5px; }
        .item-spec {
            background: #ede9fe;
            color: #5b21b6;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 4px;
        }

        .text-center { text-align: center; }
        .text-right  { text-align: right; }
        .fw-bold { font-weight: 700; }

        /* ── SUMMARY ── */
        .summary-wrap {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 50px;
        }
        .summary-table { width: 280px; }
        .s-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            font-size: 13px;
            border-bottom: 1px solid #f5f5f5;
        }
        .s-row:last-child { border-bottom: none; }
        .s-row.discount { color: #ef4444; }
        .s-row.grand {
            margin-top: 8px;
            padding: 14px 0;
            border-top: 2px solid #111;
            font-size: 18px;
            font-weight: 800;
            color: #7C3AED;
        }

        /* ── FOOTER ── */
        .footer {
            margin-top: 40px;
            text-align: center;
            border-top: 1px solid #eee;
            padding-top: 20px;
            font-size: 11px;
            color: #aaa;
            line-height: 1.8;
        }
        .footer strong { color: #555; }

        /* ── TAX SUMMARY TABLE ── */
        .tax-summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            margin-top: 10px;
        }
        .tax-summary-table th {
            background: #fdf2f8; /* Soft pink tint */
            color: #be185d;
            font-size: 10px;
            padding: 8px 10px;
            border: 1px solid #fbcfe8;
            text-align: right;
            text-transform: uppercase;
        }
        .tax-summary-table th:first-child { text-align: left; }
        .tax-summary-table td {
            padding: 8px 10px;
            border: 1px solid #f3f4f6;
            font-size: 11px;
            color: #4b5563;
            text-align: right;
        }
        .tax-summary-table td:first-child { text-align: left; font-weight: 600; color: #111; }
        .tax-summary-table tr.total-row td {
            background: #f9fafb;
            font-weight: 700;
            color: #111;
            border-top: 2px solid #eee;
        }


        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="invoice-box" id="printableInvoice">

        {{-- ── HEADER ── --}}
        <div class="header">
            <div class="company-block">
                <img src="{{ asset('assets/images/logo.png') }}" alt="{{ config('company.name') }}">
                <div class="company-name">{{ config('company.name') }}</div>
                <p>{{ config('company.address') }}</p>
                <p>{{ config('company.city') }}</p>
                <p>📞 {{ config('company.phone') }}</p>
                <p>✉️ {{ config('company.email') }}</p>
                <p>🌐 {{ config('company.website') }}</p>
                <div class="company-legal">
                    <span class="legal-tag">GSTIN: {{ config('company.gst') }}</span>
                    <span class="legal-tag">PAN: {{ config('company.pan') }}</span>
                    <span class="legal-tag">CIN: {{ config('company.cin') }}</span>
                </div>
            </div>

            <div class="invoice-info">
                <h1>INVOICE</h1>
                <p class="inv-num">#INV-{{ $order->order_number }}</p>
                <p class="inv-date">Issued: {{ optional($order->placed_at)->format('d M Y') ?? $order->created_at->format('d M Y') }}</p>
                <span class="status-badge {{ $order->payment_status === 'paid' ? 'status-paid' : 'status-unpaid' }}">
                    {{ strtoupper($order->payment_status) }}
                </span>
            </div>
        </div>

        {{-- ── BILLING / SHIPPING / PAYMENT DETAILS ── --}}
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
                @if($order->shipping_address_line_2)
                    <p class="muted">{{ $order->shipping_address_line_2 }}</p>
                @endif
                <p class="muted">{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}</p>
            </div>
            <div class="details-col">
                <h4>Payment</h4>
                <p>Method: <strong>{{ strtoupper($order->payment_method) }}</strong></p>
                <p>Status: <strong>{{ strtoupper($order->payment_status) }}</strong></p>
                <p>Currency: <strong>{{ $order->currency ?? 'INR' }}</strong></p>
                <p>Order #: <strong>{{ $order->order_number }}</strong></p>
            </div>
        </div>

        {{-- ── ITEMS TABLE ── --}}
        <table>
            <thead>
                <tr>
                    <th>Product Description</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">GST</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    @php
                        $vName   = $item->item_snapshot['variant_name'] ?? ($item->productVariant?->name ?? null);
                        $variant = $item->productVariant;
                        $product = $item->product;

                        // Use stored tax values
                        $taxRate = $item->tax_percent;
                        $gstAmount = $item->tax_amount;
                        $finalTotal = $item->line_total;

                        // Specs
                        $specs = collect();
                        if ($product) {
                            if ($product->flavor || $product->flavour)
                                $specs->put('flavor', 'Flavor: ' . ($product->flavor ?? $product->flavour));
                            if ($product->pack_size)
                                $specs->put('pack', 'Pack Size: ' . $product->pack_size);
                            if ($product->age_group)
                                $specs->put('age', 'Age Group: ' . $product->age_group);
                        }
                        if ($variant && $variant->attributes) {
                            foreach ($variant->attributes as $k => $v) {
                                $key = strtolower(str_replace(['_', '-'], ' ', $k));
                                if (str_contains($key, 'flav') || str_contains($key, 'pack') || str_contains($key, 'age')) continue;
                                $specs->put($key, ucfirst($k) . ': ' . $v);
                            }
                        }
                        $specs = $specs->values();
                    @endphp
                    <tr>
                        <td>
                            <div class="item-name">{{ $item->product_name }}</div>
                            @if($vName)
                                <div class="item-meta">Variant: {{ $vName }}</div>
                            @endif
                            @if($specs->isNotEmpty())
                                <div class="item-specs">
                                    @foreach($specs as $spec)
                                        <span class="item-spec">{{ $spec }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td class="text-center fw-bold">{{ $item->quantity }}</td>
                        <td class="text-right">₹{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-right">
                            {{ number_format($taxRate, 2) }}%<br>
                            <small>₹{{ number_format($gstAmount, 2) }}</small>
                        </td>
                        <td class="text-right fw-bold">₹{{ number_format($finalTotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @php
            $sellerState = 'Karnataka';
            $customerState = $order->shipping_state;
            $isIntraState = strtolower(trim($sellerState)) === strtolower(trim($customerState));

            $taxSummary = [];
            foreach($order->items as $item) {
                $rate = (float) $item->tax_percent;
                $taxableValue = $item->unit_price * $item->quantity;
                $taxAmount = $item->tax_amount;

                if (!isset($taxSummary[$rate])) {
                    $taxSummary[$rate] = [
                        'rate' => $rate,
                        'taxable_value' => 0,
                        'tax_amount' => 0,
                    ];
                }
                $taxSummary[$rate]['taxable_value'] += $taxableValue;
                $taxSummary[$rate]['tax_amount'] += $taxAmount;
            }
            ksort($taxSummary);
        @endphp

        {{-- ── TAX SUMMARY ── --}}
        <table class="tax-summary-table">
            <thead>
                <tr>
                    <th>Tax Rate</th>
                    <th>Taxable Value</th>
                    @if($isIntraState)
                        <th>CGST Rate</th>
                        <th>CGST Amt</th>
                        <th>SGST Rate</th>
                        <th>SGST Amt</th>
                    @else
                        <th>IGST Rate</th>
                        <th>IGST Amt</th>
                    @endif
                    <th>Total Tax</th>
                </tr>
            </thead>
            <tbody>
                @foreach($taxSummary as $summary)
                    <tr>
                        <td>GST {{ number_format($summary['rate'], 0) }}%</td>
                        <td>₹{{ number_format($summary['taxable_value'], 2) }}</td>
                        @if($isIntraState)
                            <td>{{ number_format($summary['rate'] / 2, 2) }}%</td>
                            <td>₹{{ number_format($summary['tax_amount'] / 2, 2) }}</td>
                            <td>{{ number_format($summary['rate'] / 2, 2) }}%</td>
                            <td>₹{{ number_format($summary['tax_amount'] / 2, 2) }}</td>
                        @else
                            <td>{{ number_format($summary['rate'], 2) }}%</td>
                            <td>₹{{ number_format($summary['tax_amount'], 2) }}</td>
                        @endif
                        <td>₹{{ number_format($summary['tax_amount'], 2) }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td>Total</td>
                    <td>₹{{ number_format(collect($taxSummary)->sum('taxable_value'), 2) }}</td>
                    @if($isIntraState)
                        <td></td>
                        <td>₹{{ number_format(collect($taxSummary)->sum('tax_amount') / 2, 2) }}</td>
                        <td></td>
                        <td>₹{{ number_format(collect($taxSummary)->sum('tax_amount') / 2, 2) }}</td>
                    @else
                        <td></td>
                        <td>₹{{ number_format(collect($taxSummary)->sum('tax_amount'), 2) }}</td>
                    @endif
                    <td>₹{{ number_format(collect($taxSummary)->sum('tax_amount'), 2) }}</td>
                </tr>
            </tbody>
        </table>

        {{-- ── SUMMARY ── --}}
        <div class="summary-wrap">
            <div class="summary-table">
                @php
                    // 1. Calculate the REAL original GST for the whole order
                    $totalOriginalGst = $order->items->sum(function($i) {
                        $rate = $i->tax_percent ?? 0;
                        return ($i->unit_price * $i->quantity * $rate) / 100;
                    });

                    // 2. The MRP Subtotal is the Base Subtotal + Original GST
                    $displaySubtotal = $order->subtotal + $totalOriginalGst;

                    // 3. To maintain Grand Total consistency, the discount shown must be the difference
                    $totalSavings = ($order->subtotal + $totalOriginalGst + $order->shipping_total) - $order->grand_total;

                    // 4. Distribute savings
                    $dbCoupon = $order->discount_total;
                    $dbCoins = $order->coin_discount;
                    $totalDbDiscount = $dbCoupon + $dbCoins;

                    if ($totalDbDiscount > 0) {
                        $displayCouponDiscount = ($dbCoupon / $totalDbDiscount) * $totalSavings;
                        $displayCoinDiscount = ($dbCoins / $totalDbDiscount) * $totalSavings;
                    } else {
                        $displayCouponDiscount = 0;
                        $displayCoinDiscount = 0;
                    }
                @endphp
                <div class="s-row">
                    <span>Subtotal (MRP)</span>
                    <strong>₹{{ number_format($displaySubtotal, 2) }}</strong>
                </div>
                @if($displayCouponDiscount > 0.01)
                    <div class="s-row discount">
                        <span>Coupon Discount</span>
                        <strong>- ₹{{ number_format($displayCouponDiscount, 2) }}</strong>
                    </div>
                @endif
                @if($displayCoinDiscount > 0.01)
                    <div class="s-row discount" style="color: #f97316;">
                        <span>NB Coins Discount</span>
                        <strong>- ₹{{ number_format($displayCoinDiscount, 2) }}</strong>
                    </div>
                @endif
                <div class="s-row">
                    <span>GST / Tax</span>
                    <strong>₹{{ number_format($totalOriginalGst, 2) }}</strong>
                </div>
                <div class="s-row">
                    <span>Shipping</span>
                    <strong>₹{{ number_format($order->shipping_total, 2) }}</strong>
                </div>
                <div class="s-row grand">
                    <span>Total</span>
                    <span>₹{{ number_format($order->grand_total, 2) }}</span>
                </div>
            </div>
        </div>

        {{-- ── FOOTER ── --}}
        <div class="footer">
            <p><strong>{{ config('company.name') }}</strong></p>
            <p>GSTIN: {{ config('company.gst') }} &nbsp;|&nbsp; PAN: {{ config('company.pan') }} &nbsp;|&nbsp; CIN: {{ config('company.cin') }}</p>
            <p>{{ config('company.email') }} &nbsp;|&nbsp; {{ config('company.phone') }} &nbsp;|&nbsp; {{ config('company.website') }}</p>
            <p style="margin-top:10px;">This is a computer-generated invoice and does not require a physical signature.</p>
        </div>
    </div>

    <script>
        if (window.location.search.indexOf('print=1') > -1) {
            window.print();
        }
    </script>
</body>
</html>
