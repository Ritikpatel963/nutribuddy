@extends('layouts.user-panel')
@section('title', 'My Coupons — NutriBuddy Kids')
@section('panel-page-class', 'panel-coupons')
@section('panel-content')

<style>
    .coupon-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        padding: 24px;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        border-left: 6px solid var(--or);
        margin-bottom: 20px;
    }
    .coupon-card.disabled {
        border-left-color: #cbd5e1;
        opacity: 0.7;
    }
    .coupon-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }
    .coupon-code {
        font-family: monospace;
        background: #f1f5f9;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--dk);
        letter-spacing: 1px;
        border: 1px dashed #cbd5e1;
    }
    .coupon-value {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--or);
    }
    .coupon-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--dk);
        margin-bottom: 8px;
    }
    .coupon-desc {
        color: #64748b;
        font-size: 0.95rem;
        line-height: 1.5;
        margin-bottom: 15px;
        flex-grow: 1;
    }
    .coupon-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.85rem;
        color: #94a3b8;
        border-top: 1px solid #f1f5f9;
        padding-top: 12px;
        margin-top: auto;
    }
    .coupon-badge {
        background: #fff7ed;
        color: #c2410c;
        padding: 4px 8px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.8rem;
    }
</style>

<div class="inner-topbar">
    <button class="sidebar-toggle" onclick="toggleSidebar()">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <line x1="3" y1="6" x2="21" y2="6" />
            <line x1="3" y1="12" x2="21" y2="12" />
            <line x1="3" y1="18" x2="21" y2="18" />
        </svg>
    </button>
    <span class="it-title">My Coupons 🎟️</span>
    <div style="width:36px"></div>
</div>

<div class="page">
    <p style="color: #64748b; margin-bottom: 24px; font-size: 1.05rem;">Here are the discount coupons available for your account. Copy a code and apply it during checkout!</p>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px;">
        @forelse($coupons as $coupon)
            @php
                $isValid = $coupon->isCurrentlyValid();
            @endphp
            <div class="coupon-card fade-in {{ !$isValid ? 'disabled' : '' }}">
                <div class="coupon-header">
                    <div class="coupon-code">{{ $coupon->code }}</div>
                    <div class="coupon-value">
                        {{ $coupon->discount_type == 'percentage' ? $coupon->discount_value . '%' : '₹' . number_format($coupon->discount_value, 2) }} OFF
                    </div>
                </div>
                
                <div class="coupon-title">{{ $coupon->name ?? 'Discount Coupon' }}</div>
                <div class="coupon-desc">
                    @if($coupon->min_order_amount)
                        Valid on minimum order of ₹{{ number_format($coupon->min_order_amount, 2) }}.
                    @else
                        No minimum order required.
                    @endif
                    @if($coupon->max_discount_amount)
                        Maximum discount up to ₹{{ number_format($coupon->max_discount_amount, 2) }}.
                    @endif
                </div>

                <div class="coupon-footer">
                    <div>
                        @if($coupon->ends_at)
                            Expires: {{ $coupon->ends_at->format('d M, Y') }}
                        @else
                            No Expiry
                        @endif
                    </div>
                    @if($coupon->user_id)
                        <span class="coupon-badge" style="background:#eff6ff; color:#1d4ed8;">Exclusively for You</span>
                    @elseif(!$isValid)
                        <span class="coupon-badge" style="background:#fee2e2; color:#b91c1c;">Expired/Used Up</span>
                    @endif
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px; background: white; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                <div style="font-size: 3rem; margin-bottom: 10px;">🎫</div>
                <h3 style="color: var(--dk); margin-bottom: 8px;">No Coupons Yet</h3>
                <p style="color: #64748b;">You don't have any active coupons right now. Keep checking back for new offers!</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
