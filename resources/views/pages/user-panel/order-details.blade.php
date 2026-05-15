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
        <div class="orders-card fade-in d1 order-detail-card" style="margin-bottom: 24px;">
            <div class="order-detail-head" style="margin-bottom: 20px;">
                <div>
                    <h2>Order {{ $order->order_number }}</h2>
                    <p>Placed at: {{ optional($order->placed_at)->format('d M Y h:i A') ?? '-' }}</p>
                </div>
                <div class="order-detail-total">₹{{ number_format($order->grand_total, 2) }}</div>
            </div>

            <!-- ORDER TRACKING SYSTEM -->
            @php
                $step1 = in_array($order->status, ['pending', 'confirmed', 'processing', 'packed', 'shipped', 'delivered', 'returned']);
                $step2 = in_array($order->status, ['processing', 'packed', 'shipped', 'delivered', 'returned']);
                $step3 = in_array($order->status, ['shipped', 'delivered', 'returned']);
                $step4 = in_array($order->status, ['delivered', 'returned']);
                $isCancelled = $order->status === 'cancelled';
            @endphp

            @if(!$isCancelled)
            <div class="stepper-progress" style="margin: 30px 0; max-width: 600px; margin-left: auto; margin-right: auto; flex-wrap: nowrap;">
                <!-- Step 1 -->
                <div class="sp-step {{ $step1 ? 'done' : '' }}" style="flex: 1; text-align: center;">
                    <div class="sp-ball" style="margin: 0 auto; display:flex; align-items:center; justify-content:center;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M12 11h4"/><path d="M12 16h4"/><path d="M8 11h.01"/><path d="M8 16h.01"/></svg>
                    </div>
                    <div class="sp-label" style="margin-top: 6px;">Order Placed</div>
                </div>
                <div class="sp-line" style="flex: 1;"><div class="sp-line-fill {{ $step2 ? 'done' : '' }}"></div></div>

                <!-- Step 2 -->
                <div class="sp-step {{ $step2 ? 'done' : '' }}" style="flex: 1; text-align: center;">
                    <div class="sp-ball" style="margin: 0 auto; display:flex; align-items:center; justify-content:center;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                    </div>
                    <div class="sp-label" style="margin-top: 6px;">Processing</div>
                </div>
                <div class="sp-line" style="flex: 1;"><div class="sp-line-fill {{ $step3 ? 'done' : '' }}"></div></div>

                <!-- Step 3 -->
                <div class="sp-step {{ $step3 ? 'done' : '' }}" style="flex: 1; text-align: center;">
                    <div class="sp-ball" style="margin: 0 auto; display:flex; align-items:center; justify-content:center;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10 17h4V5H2v12h3"/><path d="M20 17h2v-3.34a4 4 0 0 0-1.17-2.83L19 9h-5"/><path d="M14 17h1"/><circle cx="7.5" cy="17.5" r="2.5"/><circle cx="17.5" cy="17.5" r="2.5"/></svg>
                    </div>
                    <div class="sp-label" style="margin-top: 6px;">Shipped</div>
                </div>
                <div class="sp-line" style="flex: 1;"><div class="sp-line-fill {{ $step4 ? 'done' : '' }}"></div></div>

                <!-- Step 4 -->
                <div class="sp-step {{ $step4 ? 'done' : '' }}" style="flex: 1; text-align: center;">
                    <div class="sp-ball" style="margin: 0 auto; display:flex; align-items:center; justify-content:center;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
                    </div>
                    <div class="sp-label" style="margin-top: 6px;">Delivered</div>
                </div>
            </div>
            @else
            <div style="background: #ffebee; color: #c62828; padding: 15px; border-radius: 10px; text-align: center; margin: 20px 0;">
                <h4 style="margin: 0;">Order Cancelled</h4>
                <p style="margin: 5px 0 0; font-size: 0.9rem;">This order has been cancelled and will not be delivered.</p>
            </div>
            @endif

            <div class="order-detail-meta" style="margin-top: 20px; border-top: 1px solid #eee; padding-top: 20px; display: flex; align-items: center; flex-wrap: wrap; gap: 12px;">
                <span class="status-badge {{ $order->status === 'delivered' ? 's-delivered' : ($order->status === 'cancelled' ? 's-cancelled' : 's-pending') }}">
                    {{ strtoupper($order->status) }}
                </span>
                <span class="order-pill">{{ strtoupper($order->payment_method ?? 'cod') }}</span>
                
                @if($order->status === 'delivered' && !$order->returns()->whereIn('status', ['pending', 'approved', 'completed'])->exists())
                    <button style="background: var(--or); color: white; padding: 8px 16px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 6px; transition: background 0.2s;" onmouseover="this.style.background='#ea580c'" onmouseout="this.style.background='var(--or)'" onclick="openReturnModal()">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 14 4 9l5-5"/><path d="M4 9h10.5a5.5 5.5 0 0 1 5.5 5.5a5.5 5.5 0 0 1-5.5 5.5H11"/></svg>
                        Return Order
                    </button>
                @elseif($order->returns()->exists())
                    <span style="background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; padding: 6px 12px; border-radius: 6px; font-weight: 600; font-size: 0.85rem; display: inline-flex; align-items: center;">
                        RETURN STATUS: {{ strtoupper($order->returns()->latest()->first()->status) }}
                    </span>
                @endif
            </div>
        </div>

        <!-- FULL DETAILS (ADDRESS & PAYMENT) -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px; margin-bottom: 24px;">
            <!-- Shipping Details -->
            <div class="orders-card fade-in d2 order-detail-card" style="margin-bottom: 0;">
                <h3 class="card-title" style="margin-bottom: 15px; font-size: 1.2rem;">Shipping Address</h3>
                <div style="font-size: 0.95rem; line-height: 1.6; color: #444;">
                    <strong style="color: var(--dk); display: block; margin-bottom: 5px; font-size: 1.05rem;">{{ $order->shipping_name }}</strong>
                    {{ $order->shipping_address_line_1 }}<br>
                    @if($order->shipping_address_line_2){{ $order->shipping_address_line_2 }}<br>@endif
                    @if($order->shipping_landmark)Landmark: {{ $order->shipping_landmark }}<br>@endif
                    {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}<br>
                    {{ $order->shipping_country }}<br>
                    <strong style="margin-top: 8px; display: inline-block;">Phone:</strong> {{ $order->shipping_phone }}
                </div>
            </div>

            <!-- Payment Details -->
            <div class="orders-card fade-in d2 order-detail-card" style="margin-bottom: 0;">
                <h3 class="card-title" style="margin-bottom: 15px; font-size: 1.2rem;">Payment Summary</h3>
                <div style="font-size: 0.95rem; line-height: 1.8; color: #444;">
                    @php
                        $couponDiscount = (float) $order->discount_total;
                        $coinDiscount = (float) $order->coin_discount;
                    @endphp

                    <div style="display: flex; justify-content: space-between;">
                        <span>Subtotal:</span>
                        <span>₹{{ number_format((float) $order->subtotal, 2) }}</span>
                    </div>
                    
                    @if($couponDiscount > 0)
                    <div style="display: flex; justify-content: space-between; color: var(--mn);">
                        <span>Coupon Discount:</span>
                        <span>-₹{{ number_format($couponDiscount, 2) }}</span>
                    </div>
                    @endif

                    @if($coinDiscount > 0)
                    <div style="display: flex; justify-content: space-between; color: var(--or);">
                        <span>NB Coins Discount{{ (int) $order->coins_redeemed > 0 ? ' (' . number_format((int) $order->coins_redeemed) . ' coins)' : '' }}:</span>
                        <span>-₹{{ number_format($coinDiscount, 2) }}</span>
                    </div>
                    @endif

                    <div style="display: flex; justify-content: space-between;">
                        <span>Tax (GST):</span>
                        <span>₹{{ number_format($order->tax_total, 2) }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span>Shipping:</span>
                        <span>₹{{ number_format($order->shipping_total, 2) }}</span>
                    </div>
                    <hr style="border: 0; border-top: 1px dashed #ddd; margin: 10px 0;">
                    <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 1.1rem; color: var(--dk);">
                        <span>Grand Total:</span>
                        <span>₹{{ number_format($order->grand_total, 2) }}</span>
                    </div>
                    <div style="margin-top: 15px; background: #f9f9f9; padding: 10px; border-radius: 8px;">
                        <strong>Method:</strong> {{ strtoupper($order->payment_method) }}<br>
                        <strong>Status:</strong> <span style="color: {{ $order->payment_status === 'paid' ? '#065f46' : '#92400e' }}">{{ strtoupper($order->payment_status) }}</span>
                    </div>
                </div>
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
                                        $vAttributes = $variant?->attributes ?? $item->item_snapshot['variant_attributes'] ?? null;
                                        if ($vAttributes) {
                                            foreach($vAttributes as $k => $v) {
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
                            <td>₹{{ number_format((float) $item->line_total + (float) $item->tax_amount, 2) }}</td>
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
            @php
                if (!function_exists('getStatusNarration')) {
                    function getStatusNarration($status) {
                        return match(strtolower($status)) {
                            'pending' => 'We have received your order and are waiting to process it.',
                            'confirmed' => 'Your order has been confirmed and is in our system.',
                            'processing' => 'We are currently processing and preparing your items.',
                            'packed' => 'Your items have been securely packed and are awaiting courier pickup.',
                            'shipped' => 'Your order has been handed over to our delivery partner and is on its way.',
                            'delivered' => 'Your order has been successfully delivered. We hope you enjoy it!',
                            'cancelled' => 'Your order has been cancelled.',
                            'returned' => 'A return has been processed for your order.',
                            default => 'Your order status was updated to ' . ucfirst($status) . '.'
                        };
                    }
                }
            @endphp

            @forelse($order->statusHistories as $history)
                <div class="timeline-item" style="padding-bottom: 20px; border-bottom: 1px solid #f0f0f0; margin-bottom: 20px;">
                    <div class="timeline-time" style="font-size: 0.85rem; color: #888; margin-bottom: 5px;">
                        {{ optional($history->created_at)->format('d M Y, h:i A') }}
                    </div>
                    <div class="timeline-content">
                        <strong style="color: var(--dk); font-size: 1.05rem; display: block; margin-bottom: 4px;">
                            {{ ucfirst($history->to_status) }}
                        </strong>
                        <p style="margin: 0; color: #555; font-size: 0.95rem; line-height: 1.5;">
                            {{ getStatusNarration($history->to_status) }}
                        </p>
                        @if($history->note)
                            <div style="margin-top: 10px; padding: 10px 15px; background: #f9f9f9; border-left: 3px solid var(--mn); border-radius: 4px; font-size: 0.85rem; color: #666;">
                                <strong>Note:</strong> {{ $history->note }}
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <p class="timeline-empty" style="text-align: center; color: #888; padding: 20px;">No status timeline available.</p>
            @endforelse
            </div>
        </div>
    </div>

    <div id="returnModal" class="modal" style="display:none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5);">
        <div class="modal-content" style="background: white; margin: 10% auto; padding: 30px; border-radius: 20px; width: 90%; max-width: 500px;">
            <h3 style="margin-top:0;">Request Return ↩️</h3>
            <form action="{{ route('user.orders.returns.store', $order) }}" method="POST" enctype="multipart/form-data">
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
                <style>
                    .return-file-input {
                        width: 100%;
                        padding: 12px;
                        border-radius: 12px;
                        border: 2px dashed #cbd5e1;
                        background: #f8fafc;
                        cursor: pointer;
                        font-size: 0.9rem;
                        color: #64748b;
                        transition: all 0.2s;
                    }
                    .return-file-input:hover {
                        border-color: var(--mn);
                        background: #f0f9ff;
                    }
                    .return-file-input::file-selector-button {
                        background: white;
                        color: var(--dk);
                        padding: 8px 16px;
                        border: 1px solid #cbd5e1;
                        border-radius: 8px;
                        margin-right: 15px;
                        cursor: pointer;
                        font-weight: 600;
                        transition: all 0.2s;
                        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
                    }
                    .return-file-input::file-selector-button:hover {
                        background: #f1f5f9;
                        border-color: #94a3b8;
                    }
                </style>
                <div style="margin-bottom:20px;">
                    <label style="display:block; margin-bottom:8px; font-weight:600; color:var(--dk);">Upload Images/Videos (Optional, Max 10MB)</label>
                    <input type="file" name="attachments[]" accept="image/*,video/*" multiple class="return-file-input">
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
