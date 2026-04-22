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
        <div class="orders-card fade-in d1" style="padding:20px">
            <h2 style="margin-bottom:10px">Order {{ $order->order_number }}</h2>
            <p>Status: <strong>{{ strtoupper($order->status) }}</strong> | Fulfillment: <strong>{{ strtoupper($order->fulfillment_status) }}</strong></p>
            <p>Placed at: {{ optional($order->placed_at)->format('d M Y h:i A') ?? '-' }}</p>
            <p>Total: <strong>₹{{ number_format($order->grand_total, 2) }}</strong></p>
        </div>

        <div class="orders-card fade-in d2" style="padding:20px; margin-top:16px">
            <h3 style="margin-bottom:10px">Items</h3>
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

        <div class="orders-card fade-in d3" style="padding:20px; margin-top:16px">
            <h3 style="margin-bottom:10px">Order Timeline</h3>
            @forelse($order->statusHistories as $history)
                <p>
                    {{ optional($history->created_at)->format('d M Y h:i A') }} -
                    {{ strtoupper($history->from_status ?? 'NEW') }} to {{ strtoupper($history->to_status) }}
                    @if($history->note)
                        ({{ $history->note }})
                    @endif
                </p>
            @empty
                <p>No status timeline available.</p>
            @endforelse
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
