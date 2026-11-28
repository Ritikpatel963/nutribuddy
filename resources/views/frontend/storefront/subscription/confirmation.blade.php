@extends('layouts.main')
@section('title', 'Subscription Activated - NutriBuddy')

@section('content')
<div style="background:#faf8f5;min-height:80vh;padding:60px 16px;display:flex;align-items:center;justify-content:center;">
    <div style="background:#fff;max-width:580px;width:100%;border-radius:20px;padding:40px;box-shadow:0 10px 30px rgba(0,0,0,0.06);border:1px solid #f1f5f9;text-align:center;">
        <div style="width:72px;height:72px;background:#ecfdf5;color:#059669;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:2rem;">
            ✓
        </div>
        <h1 style="font-family:'DM Sans',sans-serif;font-size:1.85rem;color:#0f172a;font-weight:800;margin:0 0 8px;">
            Subscription Activated!
        </h1>
        <p style="color:#64748b;font-size:0.95rem;margin:0 0 24px;line-height:1.5;">
            Thank you for subscribing to <strong>{{ $subscription->product->name }}</strong>. Your first cycle is confirmed and dispatched for monthly delivery.
        </p>

        <div style="background:#f8fafc;border-radius:12px;padding:20px;text-align:left;margin-bottom:28px;border:1px solid #e2e8f0;">
            <div style="display:flex;justify-content:space-between;margin-bottom:10px;font-size:0.9rem;">
                <span style="color:#64748b;">Subscription Ref</span>
                <strong style="color:#1e293b;">{{ $subscription->cf_sub_reference }}</strong>
            </div>
            <div style="display:flex;justify-content:space-between;margin-bottom:10px;font-size:0.9rem;">
                <span style="color:#64748b;">Plan Tier</span>
                <strong style="color:#6b21a8;">{{ $subscription->max_cycles }} Months Plan</strong>
            </div>
            <div style="display:flex;justify-content:space-between;margin-bottom:10px;font-size:0.9rem;">
                <span style="color:#64748b;">Next Auto-Renewal</span>
                <strong style="color:#059669;">
                    {{ $subscription->next_billing_at ? $subscription->next_billing_at->format('M d, Y') : 'N/A' }} (₹{{ number_format($subscription->recurring_price, 0) }})
                </strong>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:0.9rem;">
                <span style="color:#64748b;">Status</span>
                <strong style="color:#059669;">{{ strtoupper($subscription->status) }}</strong>
            </div>
        </div>

        <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
            <a href="{{ route('account.orders') }}" style="padding:12px 24px;background:#6b21a8;color:#fff;border-radius:10px;text-decoration:none;font-weight:700;font-size:0.92rem;">
                View Your Orders
            </a>
            <a href="{{ url('/') }}" style="padding:12px 24px;background:#f1f5f9;color:#334155;border-radius:10px;text-decoration:none;font-weight:700;font-size:0.92rem;">
                Continue Shopping
            </a>
        </div>
    </div>
</div>
@endsection
