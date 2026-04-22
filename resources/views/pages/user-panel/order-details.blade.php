@extends('layouts.user-panel')
@section('title', 'Order Details — NutriBuddy Kids')
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
        <span class="it-title">Order Details 📋</span>
        <div style="width:36px"></div>
    </div>

    <div class="page">
        <div class="orders-card fade-in d1 order-detail-card">
            <div class="order-detail-head">
                <div>
                    <h2>Order {{ $order->order_number }}</h2>
                    <p>Placed at: {{ optional($order->placed_at)->format('d M Y h:i A') ?? '-' }}</p>
                </div>
                <div class="order-detail-total">₹{{ number_format($order->grand_total, 2) }}</div>
            </div>
            <div class="order-detail-meta">
                <span class="status-badge {{ $order->status === 'delivered' ? 's-delivered' : ($order->status === 'cancelled' ? 's-cancelled' : 's-pending') }}">
                    {{ strtoupper($order->status) }}
                </span>
                <span class="status-badge {{ $order->fulfillment_status === 'fulfilled' ? 's-delivered' : 's-pending' }}">
                    {{ strtoupper($order->fulfillment_status) }}
                </span>
                <span class="order-pill">{{ strtoupper($order->payment_method ?? 'cod') }}</span>
            </div>
        </div>

        <div class="orders-card fade-in d2 order-detail-card">
            <h3 class="card-title">Items</h3>
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Tax</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($order->items as $item)
                        <tr>
                            <td>{{ $item->product_name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>₹{{ number_format($item->unit_price, 2) }}</td>
                            <td>₹{{ number_format($item->tax_amount, 2) }}</td>
                            <td>₹{{ number_format($item->line_total, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5">No items found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="orders-card fade-in d3 order-detail-card">
            <h3 class="card-title">Order Timeline</h3>
            <div class="order-timeline">
            @forelse($order->statusHistories as $history)
                <div class="timeline-item">
                    <div class="timeline-time">{{ optional($history->created_at)->format('d M Y h:i A') }}</div>
                    <div class="timeline-content">
                    <strong>{{ strtoupper($history->from_status ?? 'NEW') }} → {{ strtoupper($history->to_status) }}</strong>
                    @if($history->note)
                        <span>{{ $history->note }}</span>
                    @endif
                    </div>
                </div>
            @empty
                <p class="timeline-empty">No status timeline available.</p>
            @endforelse
            </div>
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
