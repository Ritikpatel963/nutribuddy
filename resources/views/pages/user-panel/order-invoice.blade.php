@extends('layouts.user-panel')
@section('title', 'Invoice Details — NutriBuddy Kids')
@section('panel-page-class', 'panel-order')

@push('styles')
    <style>
        .invoice-container {
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.04);
            padding: 40px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.05);
        }
        .invoice-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 8px;
            background: linear-gradient(90deg, var(--pk), var(--pu));
        }
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 50px;
        }
        .invoice-logo img {
            max-height: 50px;
        }
        .invoice-title {
            text-align: right;
        }
        .invoice-title h1 {
            font-family: 'Fredoka One', cursive;
            font-size: 2.2rem;
            color: var(--dk);
            margin: 0;
            line-height: 1;
        }
        .invoice-title p {
            color: var(--muted);
            font-size: 0.9rem;
            margin: 5px 0 0;
        }
        .invoice-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 40px;
            margin-bottom: 50px;
            padding: 30px;
            background: var(--cr);
            border-radius: 20px;
        }
        .grid-col h4 {
            font-family: 'Fredoka One', cursive;
            font-size: 0.85rem;
            color: var(--pk);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 15px;
        }
        .grid-col p {
            font-size: 0.95rem;
            line-height: 1.6;
            color: var(--dk);
            margin: 0;
        }
        .grid-col .muted {
            color: var(--muted);
            font-size: 0.85rem;
        }
        .items-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 12px;
            margin-bottom: 40px;
        }
        .items-table th {
            padding: 15px 20px;
            text-align: left;
            font-family: 'Fredoka One', cursive;
            font-size: 0.8rem;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 2px solid var(--border);
        }
        .items-table td {
            padding: 20px;
            background: #fff;
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
        }
        .items-table td:first-child { border-left: 1px solid var(--border); border-radius: 12px 0 0 12px; }
        .items-table td:last-child { border-right: 1px solid var(--border); border-radius: 0 12px 12px 0; }
        
        .item-name { font-weight: 800; color: var(--dk); font-size: 1.05rem; }
        .item-meta { font-size: 0.75rem; color: var(--muted); margin-top: 4px; }
        .item-spec { display: inline-block; background: var(--pul); color: var(--pud); padding: 2px 8px; border-radius: 6px; font-size: 0.7rem; margin-top: 6px; font-weight: 700; margin-right: 5px;}

        .summary-box {
            display: flex;
            justify-content: flex-end;
        }
        .summary-table {
            width: 320px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            font-size: 0.95rem;
        }
        .summary-row.grand-total {
            margin-top: 15px;
            padding: 20px 0;
            border-top: 2px dashed var(--border);
            font-family: 'Fredoka One', cursive;
            font-size: 1.5rem;
            color: var(--pu);
        }
        .invoice-footer {
            margin-top: 60px;
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid var(--border);
            color: var(--muted);
            font-size: 0.85rem;
        }
        .btn-download-float {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: linear-gradient(135deg, var(--pk), var(--pu));
            color: #fff;
            padding: 16px 32px;
            border-radius: 50px;
            font-family: 'Fredoka One', cursive;
            text-decoration: none;
            box-shadow: 0 10px 30px rgba(124, 58, 237, 0.3);
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 100;
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .btn-download-float:hover { transform: translateY(-5px); color: #fff; }

        @media (max-width: 900px) {
            .invoice-grid { grid-template-columns: 1fr; gap: 30px; }
            .invoice-header { flex-direction: column; gap: 20px; text-align: center; }
            .invoice-title { text-align: center; width: 100%; }
            .invoice-container { padding: 25px; }
        }
    </style>
@endpush

@section('panel-content')
    <div class="inner-topbar">
        <button class="sidebar-toggle" onclick="toggleSidebar()">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <line x1="3" y1="6" x2="21" y2="6" />
                <line x1="3" y1="12" x2="21" y2="12" />
                <line x1="3" y1="18" x2="21" y2="18" />
            </svg>
        </button>
        <span class="it-title">Invoice Details 🧾</span>
        <div style="width:36px"></div>
    </div>

    <div class="page">
        <div class="invoice-container fade-in">
            <div class="invoice-header">
                <div class="invoice-logo">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="NutriBuddy">
                </div>
                <div class="invoice-title">
                    <h1>INVOICE</h1>
                    <p>#INV-{{ $order->order_number }}</p>
                </div>
            </div>

            <div class="invoice-grid">
                <div class="grid-col">
                    <h4>Billed To</h4>
                    <p><strong>{{ $order->customer_name }}</strong></p>
                    <p class="muted">{{ $order->customer_email }}</p>
                    <p class="muted">{{ $order->customer_phone }}</p>
                </div>
                <div class="grid-col">
                    <h4>Shipped To</h4>
                    <p><strong>{{ $order->shipping_name }}</strong></p>
                    <p class="muted">{{ $order->shipping_address_line_1 }}</p>
                    @if($order->shipping_address_line_2)<p class="muted">{{ $order->shipping_address_line_2 }}</p>@endif
                    <p class="muted">{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}</p>
                </div>
                <div class="grid-col">
                    <h4>Summary</h4>
                    <p class="muted">Date: <strong>{{ optional($order->placed_at)->format('d M Y') ?? $order->created_at->format('d M Y') }}</strong></p>
                    <p class="muted">Method: <strong>{{ strtoupper($order->payment_method) }}</strong></p>
                    <p class="muted">Status: <span style="color: {{ $order->payment_status === 'paid' ? '#10b981' : '#f59e0b' }}; font-weight: 800;">{{ strtoupper($order->payment_status) }}</span></p>
                </div>
            </div>

            <table class="items-table">
                <thead>
                    <tr>
                        <th>Product Description</th>
                        <th style="text-align: center;">Qty</th>
                        <th style="text-align: right;">Unit Price</th>
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
                            <td style="text-align: center; font-weight: 700;">{{ $item->quantity }}</td>
                            <td style="text-align: right;">₹{{ number_format($item->unit_price, 2) }}</td>
                            <td style="text-align: right; font-weight: 800;">₹{{ number_format($item->line_total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="summary-box">
                <div class="summary-table">
                    <div class="summary-row"><span>Subtotal</span><strong>₹{{ number_format($order->subtotal, 2) }}</strong></div>
                    @if($order->discount_total > 0)
                        <div class="summary-row" style="color: #ef4444;"><span>Discount</span><strong>- ₹{{ number_format($order->discount_total, 2) }}</strong></div>
                    @endif
                    @if($order->tax_total > 0)
                        <div class="summary-row"><span>Tax (GST)</span><strong>₹{{ number_format($order->tax_total, 2) }}</strong></div>
                    @endif
                    <div class="summary-row"><span>Shipping</span><strong>₹{{ number_format($order->shipping_total, 2) }}</strong></div>
                    <div class="summary-row grand-total">
                        <span>Total Amount</span>
                        <strong>₹{{ number_format($order->grand_total, 2) }}</strong>
                    </div>
                </div>
            </div>

            <div class="invoice-footer">
                <p>Thank you for shopping with <strong>NutriBuddy Kids</strong>! 🌈</p>
                <p>For any billing inquiries, please contact support@nutribuddy.com</p>
            </div>
        </div>
    </div>

    <a href="{{ route('user.orders.invoice-download', $order) }}" class="btn-download-float">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
            <polyline points="7 10 12 15 17 10"></polyline>
            <line x1="12" y1="15" x2="12" y2="3"></line>
        </svg>
        Download PDF
    </a>

    @push('scripts')
        <script>
            function toggleSidebar() {
                document.getElementById('sidebar').classList.toggle('open');
                document.getElementById('overlay').classList.toggle('show');
            }
            function closeSidebar() {
                document.getElementById('sidebar').classList.remove('open');
                document.getElementById('overlay').classList.remove('show');
            }
        </script>
    @endpush
@endsection
