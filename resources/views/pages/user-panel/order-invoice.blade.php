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
        <div class="orders-card fade-in d1 invoice-hero">
            <div>
                <h2>{{ $invoiceNumber }}</h2>
                <p>Order: <strong>{{ $order->order_number }}</strong></p>
                <p>Customer: <strong>{{ $order->customer_name }}</strong></p>
                <p>Payment: <strong>{{ strtoupper($order->payment_method) }}</strong></p>
            </div>
            <a href="{{ route('user.orders.invoice-download', $order) }}" class="nav-cta">Download Invoice</a>
        </div>

        <div class="orders-card fade-in d2 invoice-breakdown">
            <h3 class="card-title">Amount Breakdown</h3>
            <div class="invoice-row"><span>Subtotal</span><strong>₹{{ number_format($order->subtotal, 2) }}</strong></div>
            <div class="invoice-row"><span>Discount</span><strong>₹{{ number_format($order->discount_total, 2) }}</strong></div>
            <div class="invoice-row"><span>Tax</span><strong>₹{{ number_format($order->tax_total, 2) }}</strong></div>
            <div class="invoice-row"><span>GST</span><strong>₹{{ number_format($order->gst_total, 2) }}</strong></div>
            <div class="invoice-row"><span>Shipping</span><strong>₹{{ number_format($order->shipping_total, 2) }}</strong></div>
            <div class="invoice-row total"><span>Grand Total</span><strong>₹{{ number_format($order->grand_total, 2) }}</strong></div>
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
