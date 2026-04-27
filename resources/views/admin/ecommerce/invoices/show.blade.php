@extends('layout.layout')
@php
    $title = 'Official Invoice';
    $subTitle = 'Ecommerce / Invoices / INV-'.$order->order_number;
    $script = '<script>
                    function printInvoice() {
                        var printUrl = "'.route('user.orders.invoice-download', ['order' => $order->id]).'?print=1";
                        window.open(printUrl, "_blank");
                    }
                </script>';
@endphp

@push('styles')
    <style>
        .admin-invoice-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            padding: 40px;
            border: 1px solid #eee;
            position: relative;
        }
        .admin-invoice-card::after {
            content: "ADMIN COPY";
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 0.6rem;
            color: #ddd;
            letter-spacing: 2px;
            font-weight: 800;
        }
        .invoice-head-flex {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            border-bottom: 1px solid #f5f5f5;
            padding-bottom: 25px;
        }
        .invoice-branding img { max-height: 45px; margin-bottom: 10px; }
        .invoice-meta-top h1 { font-family: "Fredoka One", cursive; font-size: 1.8rem; color: var(--dk); margin: 0; }
        
        .details-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            margin-bottom: 40px;
        }
        .detail-group h6 { font-size: 0.75rem; color: var(--pk); text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 12px; font-weight: 800; }
        .detail-group p { font-size: 0.9rem; color: #333; margin: 0 0 5px; line-height: 1.4; }
        
        .items-list { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .items-list th { text-align: left; padding: 12px; border-bottom: 2px solid #f0f0f0; font-size: 0.75rem; text-transform: uppercase; color: #888; letter-spacing: 1px; }
        .items-list td { padding: 15px 12px; border-bottom: 1px solid #f9f9f9; font-size: 0.9rem; }
        
        .item-title { font-weight: 700; color: #111; }
        .item-specs { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 5px; }
        .spec-pill { background: #f0f4ff; color: #5a67d8; font-size: 0.65rem; padding: 2px 8px; border-radius: 4px; font-weight: 700; }
        
        .totals-flex { display: flex; justify-content: flex-end; }
        .totals-table { width: 300px; }
        .total-row { display: flex; justify-content: space-between; padding: 8px 0; font-size: 0.9rem; color: #555; }
        .total-row.grand { border-top: 2px solid #eee; margin-top: 10px; padding-top: 15px; font-weight: 800; font-size: 1.3rem; color: var(--pu); }
    </style>
@endpush

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-24 no-print">
        <a href="{{ route('admin.ecommerce.orders.index') }}" class="btn btn-sm btn-secondary-light radius-8 d-inline-flex align-items-center gap-1">
            <iconify-icon icon="lucide:arrow-left"></iconify-icon>
            Back to Orders
        </a>
        <div class="d-flex gap-2">
            <button onclick="printInvoice()" class="btn btn-sm btn-primary radius-8 d-inline-flex align-items-center gap-1">
                <iconify-icon icon="lucide:printer"></iconify-icon>
                Print / Download
            </button>
        </div>
    </div>

    <div class="admin-invoice-card" id="invoiceArea">
        <div class="invoice-head-flex">
            <div class="invoice-branding">
                <img src="{{ asset('assets/images/logo.png') }}" alt="NutriBuddy">
                <p class="text-xs text-secondary-light mb-0">Official E-commerce Billing</p>
            </div>
            <div class="invoice-meta-top text-end">
                <h1>INVOICE</h1>
                <p class="text-sm text-secondary-light mb-0">#INV-{{ $order->order_number }}</p>
                <p class="text-xs text-secondary-light">Date: {{ optional($order->placed_at)->format('d M Y') }}</p>
            </div>
        </div>

        <div class="details-row">
            <div class="detail-group">
                <h6>Billed To:</h6>
                <p><strong>{{ $order->customer_name }}</strong></p>
                <p>{{ $order->customer_email }}</p>
                <p>{{ $order->customer_phone }}</p>
            </div>
            <div class="detail-group">
                <h6>Shipping To:</h6>
                <p><strong>{{ $order->shipping_name }}</strong></p>
                <p>{{ $order->shipping_address_line_1 }}</p>
                @if($order->shipping_address_line_2)<p>{{ $order->shipping_address_line_2 }}</p>@endif
                <p>{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}</p>
            </div>
            <div class="detail-group">
                <h6>Payment Info:</h6>
                <p>Method: <strong>{{ strtoupper($order->payment_method) }}</strong></p>
                <p>Status: <span class="badge {{ $order->payment_status === 'paid' ? 'bg-success-100 text-success-600' : 'bg-warning-100 text-warning-600' }}">{{ strtoupper($order->payment_status) }}</span></p>
                <p>Order Reference: #{{ $order->order_number }}</p>
            </div>
        </div>

        <table class="items-list">
            <thead>
                <tr>
                    <th>Product</th>
                    <th class="text-center">Qty</th>
                    <th class="text-end">Unit Price</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    @php
                        $vName = $item->item_snapshot['variant_name'] ?? ($item->productVariant?->name ?? null);
                        $variant = $item->productVariant;
                        $product = $item->product;
                        $specs = [];
                        if ($variant && $variant->attributes) {
                            foreach($variant->attributes as $k => $v) $specs[] = "$k: $v";
                        }
                        if ($product) {
                            if ($product->flavor || $product->flavour) $specs[] = "Flavour: " . ($product->flavor ?? $product->flavour);
                            if ($product->pack_size) $specs[] = "Pack: " . $product->pack_size;
                            if ($product->age_group) $specs[] = "Age: " . $product->age_group;
                        }
                    @endphp
                    <tr>
                        <td>
                            <div class="item-title">{{ $item->product_name }}</div>
                            @if($vName)<div class="text-xs text-secondary-light mt-1">{{ $vName }}</div>@endif
                            <div class="item-specs">
                                @foreach($specs as $spec)
                                    <span class="spec-pill">{{ $spec }}</span>
                                @endforeach
                            </div>
                        </td>
                        <td class="text-center fw-bold">{{ $item->quantity }}</td>
                        <td class="text-end">₹{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-end fw-bold">₹{{ number_format($item->line_total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals-flex">
            <div class="totals-table">
                <div class="total-row"><span>Subtotal</span><strong>₹{{ number_format($order->subtotal, 2) }}</strong></div>
                @if($order->discount_total > 0)
                    <div class="total-row text-danger"><span>Discount</span><strong>- ₹{{ number_format($order->discount_total, 2) }}</strong></div>
                @endif
                @if($order->tax_total > 0)
                    <div class="total-row"><span>Tax Total</span><strong>₹{{ number_format($order->tax_total, 2) }}</strong></div>
                @endif
                <div class="total-row"><span>Shipping</span><strong>₹{{ number_format($order->shipping_total, 2) }}</strong></div>
                <div class="total-row grand">
                    <span>Total</span>
                    <span>₹{{ number_format($order->grand_total, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="mt-40 text-center border-top pt-20">
            <p class="text-xs text-secondary-light">This invoice is a legal record of purchase from NutriBuddy Kids. For any issues, contact support@nutribuddy.com</p>
        </div>
    </div>
@endsection
