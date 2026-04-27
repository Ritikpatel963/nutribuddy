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
                
                @if($order->status === 'delivered' && !$order->returns()->whereIn('status', ['pending', 'approved', 'completed'])->exists())
                    <button class="status-badge s-pending" style="border:none; cursor:pointer;" onclick="openReturnModal()">
                        RETURN ORDER ↩️
                    </button>
                @elseif($order->returns()->exists())
                    <span class="status-badge s-delivered">RETURN {{ strtoupper($order->returns()->latest()->first()->status) }}</span>
                @endif
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
                            <td>
                                <div style="display: flex; flex-direction: column; gap: 2px;">
                                    <div style="font-weight: 500;">{{ $item->product_name }}</div>
                                    @php
                                        $vName = $item->item_snapshot['variant_name'] ?? ($item->productVariant?->name ?? null);
                                        $variant = $item->productVariant;
                                        $product = $item->product;
                                    @endphp
                                    @if($vName)
                                        <div style="font-size: 0.75rem; color: #777;">{{ $vName }}</div>
                                    @endif
                                    <div style="font-size: 0.7rem; color: #888; display: flex; flex-wrap: wrap; gap: 8px; margin-top: 2px;">
                                    @php
                                        $specs = collect();
                                        // 1. First set the product-level ones (They take priority for labels)
                                        if ($product) {
                                            if ($product->flavor || $product->flavour) {
                                                $specs->put('flavor', ['k' => 'Flavor', 'v' => $product->flavor ?? $product->flavour]);
                                            }
                                            if ($product->pack_size) {
                                                $specs->put('pack', ['k' => 'Pack Size', 'v' => $product->pack_size]);
                                            }
                                            if ($product->age_group) {
                                                $specs->put('age', ['k' => 'Age Group', 'v' => $product->age_group]);
                                            }
                                        }

                                        // 2. Add other variant attributes ONLY if they are not the above
                                        if ($variant && $variant->attributes) {
                                            foreach($variant->attributes as $k => $v) {
                                                $key = strtolower(str_replace(['_', '-'], ' ', $k));
                                                // If it's a common key we already handled, skip it to avoid duplicates
                                                if (str_contains($key, 'flav') || str_contains($key, 'pack') || str_contains($key, 'age')) {
                                                    continue;
                                                }
                                                $specs->put($key, ["k" => ucfirst($k), "v" => $v]);
                                            }
                                        }
                                    @endphp
                                    @foreach($specs as $spec)
                                        <span>{{ $spec['k'] }}: {{ $spec['v'] }}</span>
                                    @endforeach
                                    </div>
                                </div>
                            </td>
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

    <div id="returnModal" class="modal" style="display:none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5);">
        <div class="modal-content" style="background: white; margin: 10% auto; padding: 30px; border-radius: 20px; width: 90%; max-width: 500px;">
            <h3 style="margin-top:0;">Request Return ↩️</h3>
            <form action="{{ route('user.orders.returns.store', $order) }}" method="POST">
                @csrf
                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:5px; font-weight:bold;">Reason for Return</label>
                    <select name="reason" required style="width:100%; padding:10px; border-radius:10px; border:1px solid #ddd;">
                        <option value="">Select a reason</option>
                        @foreach(\App\Support\OrderFlow::RETURN_REASONS as $reason)
                            <option value="{{ $reason }}">{{ $reason }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:5px; font-weight:bold;">Additional Comments</label>
                    <textarea name="comments" rows="3" style="width:100%; padding:10px; border-radius:10px; border:1px solid #ddd;" placeholder="Tell us more about the issue..."></textarea>
                </div>
                <div style="display:flex; gap:10px;">
                    <button type="submit" class="status-badge s-delivered" style="border:none; cursor:pointer; flex:1; padding:12px;">Submit Request</button>
                    <button type="button" class="status-badge s-cancelled" style="border:none; cursor:pointer; flex:1; padding:12px;" onclick="closeReturnModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function openReturnModal() {
                document.getElementById('returnModal').style.display = 'block';
            }
            function closeReturnModal() {
                document.getElementById('returnModal').style.display = 'none';
            }
        </script>
    @endpush
@endsection
