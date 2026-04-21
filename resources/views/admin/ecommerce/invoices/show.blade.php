@extends('layout.layout')
@php
    $title = 'Invoice Details';
    $subTitle = 'Ecommerce / Invoices / INV-'.$order->order_number;
    $script = '<script>
                    function printInvoice() {
                        var printContents = document.getElementById("invoice").innerHTML;
                        var originalContents = document.body.innerHTML;

                        document.body.innerHTML = printContents;

                        window.print();

                        document.body.innerHTML = originalContents;
                        window.location.reload();
                    }
                </script>';
@endphp

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <a href="{{ route('admin.ecommerce.orders.index') }}" class="btn btn-sm btn-secondary-light radius-8 d-inline-flex align-items-center gap-1">
                    <iconify-icon icon="lucide:arrow-left" class="text-xl"></iconify-icon>
                    Back to Orders
                </a>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-danger radius-8 d-inline-flex align-items-center gap-1" onclick="printInvoice()">
                        <iconify-icon icon="basil:printer-outline" class="text-xl"></iconify-icon>
                        Print Invoice
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body py-40">
            <div class="row justify-content-center" id="invoice">
                <div class="col-lg-10">
                    <div class="shadow-4 border radius-8">
                        <div class="p-20 d-flex flex-wrap justify-content-between gap-3 border-bottom">
                            <div>
                                <h3 class="text-xl mb-8">Invoice #INV-{{ $order->order_number }}</h3>
                                <p class="mb-1 text-sm">Date Issued: {{ optional($order->placed_at)->format('d/m/Y') ?? optional($order->created_at)->format('d/m/Y') ?? 'N/A' }}</p>
                                <p class="mb-0 text-sm">Payment Status: <span class="text-{{ strtolower($order->payment_status) == 'paid' ? 'success' : 'warning' }}-main fw-bold">{{ strtoupper($order->payment_status) }}</span></p>
                            </div>
                            <div class="text-sm-end">
                                <img src="{{ asset('assets/images/logo.png') }}" alt="logo" class="mb-8" style="max-height: 40px;">
                                <p class="mb-1 text-sm">NutriBuddy E-commerce Solutions</p>
                                <p class="mb-0 text-sm">support@nutribuddy.com, +91 9876543210</p>
                            </div>
                        </div>
                        <div class="py-28 px-20">
                            <div class="row g-4 justify-content-between align-items-start mb-32">
                                <div class="col-sm-6">
                                    <h6 class="text-md mb-12">Bill To:</h6>
                                    <table class="text-sm text-secondary-light">
                                        <tbody>
                                            <tr>
                                                <td>Name</td>
                                                <td class="ps-8">: <strong>{{ $order->customer_name }}</strong></td>
                                            </tr>
                                            <tr>
                                                <td>Email</td>
                                                <td class="ps-8">: {{ $order->customer_email }}</td>
                                            </tr>
                                            <tr>
                                                <td>Phone</td>
                                                <td class="ps-8">: {{ $order->customer_phone }}</td>
                                            </tr>
                                            <tr>
                                                <td>Address</td>
                                                <td class="ps-8">: {{ $order->shipping_address_line_1 }}, {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-sm-4">
                                    <h6 class="text-md mb-12">Order Details:</h6>
                                    <table class="text-sm text-secondary-light w-100">
                                        <tbody>
                                            <tr>
                                                <td>Order ID</td>
                                                <td class="ps-8">: #{{ $order->order_number }}</td>
                                            </tr>
                                            <tr>
                                                <td>Order Date</td>
                                                <td class="ps-8">: {{ optional($order->placed_at)->format('d M Y') ?? optional($order->created_at)->format('d M Y') ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Method</td>
                                                <td class="ps-8">: {{ strtoupper($order->payment_method) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="mt-24">
                                <div class="table-responsive scroll-sm">
                                    <table class="table bordered-table text-sm">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="text-sm">Item Description</th>
                                                <th scope="col" class="text-sm">SKU</th>
                                                <th scope="col" class="text-center text-sm">Qty</th>
                                                <th scope="col" class="text-end text-sm">Unit Price</th>
                                                <th scope="col" class="text-end text-sm">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($order->items as $item)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <img src="{{ !empty($item->product->images->first()) ? asset('storage/'.$item->product->images->first()->image_path) : asset('assets/images/logo-icon.png') }}" class="w-32-px h-32-px radius-4">
                                                            <span>{{ $item->product_name }}</span>
                                                        </div>
                                                    </td>
                                                    <td>{{ $item->sku }}</td>
                                                    <td class="text-center">{{ $item->quantity }}</td>
                                                    <td class="text-end">INR {{ number_format($item->unit_price, 2) }}</td>
                                                    <td class="text-end fw-medium text-dark">INR {{ number_format($item->line_total, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="row justify-content-end mt-24">
                                    <div class="col-sm-5">
                                        <table class="table table-sm table-borderless text-sm">
                                            <tbody>
                                                <tr>
                                                    <td class="text-secondary-light">Subtotal:</td>
                                                    <td class="text-end fw-semibold text-dark">INR {{ number_format($order->subtotal, 2) }}</td>
                                                </tr>
                                                @if($order->tax_total > 0)
                                                    <tr>
                                                        <td class="text-secondary-light">Tax Total:</td>
                                                        <td class="text-end fw-semibold text-dark">INR {{ number_format($order->tax_total, 2) }}</td>
                                                    </tr>
                                                @endif
                                                @if($order->discount_total > 0)
                                                    <tr>
                                                        <td class="text-secondary-light">Discount ({{ $order->coupon?->code ?? 'Coupon' }}):</td>
                                                        <td class="text-end fw-semibold text-danger-main">- INR {{ number_format($order->discount_total, 2) }}</td>
                                                    </tr>
                                                @endif
                                                <tr>
                                                    <td class="text-secondary-light border-bottom pb-8">Shipping:</td>
                                                    <td class="text-end fw-semibold text-dark border-bottom pb-8">INR {{ number_format($order->shipping_total, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="pt-8"><span class="text-md fw-bold">Grand Total:</span></td>
                                                    <td class="text-end pt-8"><span class="text-md fw-bold text-primary-600">INR {{ number_format($order->grand_total, 2) }}</span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-64 border-top pt-24 text-center">
                                <p class="text-secondary-light text-sm fw-semibold mb-0">Thank you for your business with NutriBuddy!</p>
                                <small class="text-xs text-secondary-light">Computer generated invoice. No signature required.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
