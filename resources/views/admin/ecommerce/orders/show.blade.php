@extends('layout.layout')
@php
    $title = 'Order Details';
    $subTitle = 'Ecommerce / Order #' . $order->order_number;
@endphp

@section('content')
    @include('admin.ecommerce._messages')

    <div class="row g-4">
        <div class="col-xl-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Update Order Status</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.ecommerce.orders.update-status', $order) }}">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label class="form-label">Order Status</label>
                            <select name="status" class="form-select" required>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status }}" {{ $order->status === $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Payment Status</label>
                            <select name="payment_status" class="form-select" required>
                                @foreach ($paymentStatuses as $paymentStatus)
                                    <option value="{{ $paymentStatus }}" {{ $order->payment_status === $paymentStatus ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $paymentStatus)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fulfillment Status</label>
                            <select name="fulfillment_status" class="form-select" required>
                                @foreach ($fulfillmentStatuses as $fulfillmentStatus)
                                    <option value="{{ $fulfillmentStatus }}" {{ $order->fulfillment_status === $fulfillmentStatus ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $fulfillmentStatus)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Admin Note</label>
                            <textarea name="admin_note" rows="4" class="form-control">{{ $order->admin_note }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Status</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card mb-24">
                <div class="card-header">
                    <h5 class="card-title mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6"><strong>Order #:</strong> {{ $order->order_number }}</div>
                        <div class="col-md-6"><strong>Placed At:</strong> {{ optional($order->placed_at)->format('d M Y H:i') ?? optional($order->created_at)->format('d M Y H:i') ?? 'N/A' }}</div>
                        <div class="col-md-6"><strong>Customer:</strong> {{ $order->customer_name }} ({{ $order->customer_phone }})</div>
                        <div class="col-md-6"><strong>Email:</strong> {{ $order->customer_email ?: 'N/A' }}</div>
                        <div class="col-md-6"><strong>Payment:</strong> {{ strtoupper($order->payment_method) }} / {{ ucfirst($order->payment_status) }}</div>
                        <div class="col-md-6"><strong>Status:</strong> {{ ucfirst($order->status) }}</div>
                        <div class="col-md-6"><strong>Fulfillment:</strong> {{ ucfirst(str_replace('_', ' ', $order->fulfillment_status)) }}</div>
                        <div class="col-md-6"><strong>Coupon:</strong> {{ $order->coupon_code ?: 'N/A' }}</div>
                        <div class="col-12">
                            <strong>Shipping Address:</strong>
                            <div>
                                {{ $order->shipping_name }}, {{ $order->shipping_phone }}<br>
                                {{ $order->shipping_address_line_1 }} {{ $order->shipping_address_line_2 }}<br>
                                {{ $order->shipping_city }}, {{ $order->shipping_state }} - {{ $order->shipping_postal_code }}, {{ $order->shipping_country }}
                            </div>
                        </div>
                        @if ($order->customer_note)
                            <div class="col-12"><strong>Customer Note:</strong> {{ $order->customer_note }}</div>
                        @endif
                        <div class="col-12 mt-3 pt-3 border-top d-flex gap-2">
                            <a href="{{ route('admin.ecommerce.invoices.show', $order) }}" class="btn btn-sm btn-info text-white d-inline-flex align-items-center gap-1">
                                <iconify-icon icon="lucide:file-text"></iconify-icon> View Official Invoice
                            </a>
                            <a href="{{ route('user.orders.invoice-download', $order) }}?print=1" target="_blank" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1">
                                <iconify-icon icon="lucide:printer"></iconify-icon> Print for Packaging
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            @if($order->returns()->exists())
                <div class="card mb-24 border border-warning shadow-none">
                    <div class="card-header bg-warning-focus d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 text-warning-main">Return Information ↩️</h5>
                        <a href="{{ route('admin.ecommerce.order-returns.show', $order->returns()->latest()->first()) }}" class="btn btn-sm btn-warning-main">View Full Return Details</a>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4"><strong>Return #:</strong> #{{ $order->returns()->latest()->first()->return_number }}</div>
                            <div class="col-md-4"><strong>Status:</strong> <span class="badge bg-warning-focus text-warning-main">{{ strtoupper($order->returns()->latest()->first()->status) }}</span></div>
                            <div class="col-md-4"><strong>Requested At:</strong> {{ $order->returns()->latest()->first()->created_at->format('d M Y') }}</div>
                            <div class="col-12"><strong>Reason:</strong> {{ $order->returns()->latest()->first()->reason }}</div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="card mb-24">
                <div class="card-header">
                    <h5 class="card-title mb-0">Order Items</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table bordered-table mb-0">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Qty</th>
                                    <th>Unit Price</th>
                                    <th>Tax</th>
                                    <th>Discount</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->items as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="text-md fw-medium">{{ $item->product_name }}</span>
                                                @php
                                                    $vName = $item->item_snapshot['variant_name'] ?? ($item->productVariant?->name ?? null);
                                                    $variant = $item->productVariant;
                                                    $product = $item->product;
                                                @endphp
                                                @if($vName)
                                                    <small class="text-secondary-light d-block">{{ $vName }}</small>
                                                @endif
                                                <div class="d-flex flex-wrap gap-2 mt-1">
                                                    @php
                                                        $specs = collect();
                                                        if ($product) {
                                                            if ($product->flavor || $product->flavour) $specs->put('flavor', ['k' => 'Flavor', 'v' => $product->flavor ?? $product->flavour, 'c' => 'success']);
                                                            if ($product->pack_size) $specs->put('pack', ['k' => 'Pack Size', 'v' => $product->pack_size, 'c' => 'info']);
                                                            if ($product->age_group) $specs->put('age', ['k' => 'Age Group', 'v' => $product->age_group, 'c' => 'primary']);
                                                        }
                                                        if ($variant && $variant->attributes) {
                                                            foreach($variant->attributes as $k => $v) {
                                                                $key = strtolower(str_replace(['_', '-'], ' ', $k));
                                                                if (str_contains($key, 'flav') || str_contains($key, 'pack') || str_contains($key, 'age')) continue;
                                                                $specs->put($key, ['k' => ucfirst($k), 'v' => $v, 'c' => 'info']);
                                                            }
                                                        }
                                                    @endphp
                                                    @foreach($specs as $spec)
                                                        <span class="badge bg-{{ $spec['c'] }}-100 text-{{ $spec['c'] }}-600 text-xs">{{ $spec['k'] }}: {{ $spec['v'] }}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $item->sku }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>INR {{ number_format((float) $item->unit_price, 2) }}</td>
                                        <td>INR {{ number_format((float) $item->tax_amount, 2) }}</td>
                                        <td>INR {{ number_format((float) $item->discount_amount, 2) }}</td>
                                        <td>INR {{ number_format((float) $item->line_total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Totals</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6"><strong>Subtotal:</strong> INR {{ number_format((float) $order->subtotal, 2) }}</div>
                        <div class="col-md-6"><strong>Tax:</strong> INR {{ number_format((float) $order->tax_total, 2) }}</div>
                        <div class="col-md-6"><strong>GST:</strong> INR {{ number_format((float) $order->gst_total, 2) }}</div>
                        <div class="col-md-6"><strong>CGST / SGST / IGST:</strong> INR {{ number_format((float) $order->cgst_total, 2) }} / INR {{ number_format((float) $order->sgst_total, 2) }} / INR {{ number_format((float) $order->igst_total, 2) }}</div>
                        <div class="col-md-6"><strong>Discount:</strong> INR {{ number_format((float) $order->discount_total, 2) }}</div>
                        <div class="col-md-6"><strong>Shipping:</strong> INR {{ number_format((float) $order->shipping_total, 2) }}</div>
                        <div class="col-md-12"><h5 class="mb-0">Grand Total: INR {{ number_format((float) $order->grand_total, 2) }}</h5></div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Status Timeline</h5>
                </div>
                <div class="card-body">
                    @forelse ($order->statusHistories as $history)
                        <div class="mb-3 pb-3 border-bottom">
                            <div><strong>{{ ucfirst($history->to_status) }}</strong> at {{ optional($history->created_at)->format('d M Y H:i') }}</div>
                            <div class="text-muted">From: {{ ucfirst($history->from_status ?? 'new') }} | Fulfillment: {{ ucfirst(str_replace('_', ' ', $history->to_fulfillment_status ?? 'n/a')) }}</div>
                            @if ($history->note)
                                <div>{{ $history->note }}</div>
                            @endif
                        </div>
                    @empty
                        <p class="mb-0">No status history available.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
