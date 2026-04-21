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
                    </div>
                </div>
            </div>

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
                                        <td>{{ $item->product_name }}</td>
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
                        <div class="col-md-6"><strong>Discount:</strong> INR {{ number_format((float) $order->discount_total, 2) }}</div>
                        <div class="col-md-6"><strong>Shipping:</strong> INR {{ number_format((float) $order->shipping_total, 2) }}</div>
                        <div class="col-md-12"><h5 class="mb-0">Grand Total: INR {{ number_format((float) $order->grand_total, 2) }}</h5></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
