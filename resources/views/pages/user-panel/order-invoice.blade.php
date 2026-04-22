@extends('layouts.user-panel')
@section('title', 'Invoice — NutriBuddy Kids')
@section('panel-page-class', 'panel-order')
@section('panel-content')
    <div class="inner-topbar">
        <button class="sidebar-toggle" onclick="toggleSidebar()">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <line x1="3" y1="6" x2="21" y2="6" />
                <line x1="3" y1="12" x2="21" y2="12" />
                <line x1="3" y1="18" x2="21" y2="18" />
            </svg>
        </button>
        <span class="it-title">Invoice 🧾</span>
        <div style="width:36px"></div>
    </div>

    <div class="page">
        <div class="orders-card fade-in d1" style="padding:20px">
            <h2 style="margin-bottom:10px">{{ $invoiceNumber }}</h2>
            <p>Order: <strong>{{ $order->order_number }}</strong></p>
            <p>Customer: <strong>{{ $order->customer_name }}</strong></p>
            <p>Payment: <strong>{{ strtoupper($order->payment_method) }}</strong></p>
            <p style="margin-top:12px;">
                <a href="{{ route('user.orders.invoice-download', $order) }}" class="act-btn act-review">Download Invoice</a>
            </p>
        </div>

        <div class="orders-card fade-in d2" style="padding:20px; margin-top:16px">
            <h3 style="margin-bottom:10px">Amount Breakdown</h3>
            <p>Subtotal: ₹{{ number_format($order->subtotal, 2) }}</p>
            <p>Discount: ₹{{ number_format($order->discount_total, 2) }}</p>
            <p>Tax: ₹{{ number_format($order->tax_total, 2) }}</p>
            <p>GST: ₹{{ number_format($order->gst_total, 2) }}</p>
            <p>Shipping: ₹{{ number_format($order->shipping_total, 2) }}</p>
            <p><strong>Grand Total: ₹{{ number_format($order->grand_total, 2) }}</strong></p>
        </div>
    </div>

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
