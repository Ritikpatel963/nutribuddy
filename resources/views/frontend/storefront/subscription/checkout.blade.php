@extends('layouts.main')
@section('title', 'Subscribe & Save Checkout - NutriBuddy')

@section('content')
<div style="background:#faf8f5;min-height:85vh;padding:40px 16px;">
    <div style="max-width:960px;margin:0 auto;">
        <div style="margin-bottom:28px;">
            <a href="{{ route('product.show', $product->slug) }}" style="color:#64748b;font-size:0.88rem;text-decoration:none;font-weight:600;">← Back to {{ $product->name }}</a>
            <h1 style="font-family:'DM Sans',sans-serif;font-size:1.85rem;color:#1e293b;margin:8px 0 4px;font-weight:800;">
                Subscribe &amp; Save Checkout
            </h1>
            <p style="color:#64748b;font-size:0.95rem;margin:0;">
                Set up recurring auto-deliveries. Save from month two · Pause or cancel anytime.
            </p>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(320px, 1fr));gap:32px;">
            {{-- Left Column: Address & Details --}}
            <div style="background:#fff;border-radius:16px;padding:28px;box-shadow:0 4px 20px rgba(0,0,0,0.04);border:1px solid #f1f5f9;">
                <h2 style="font-size:1.2rem;color:#1e293b;font-weight:800;margin:0 0 20px;">1. Shipping Address</h2>

                <form id="subscribeForm" method="POST" action="{{ route('subscribe.initiate') }}">
                    @csrf
                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                    <input type="hidden" name="variant_id" value="{{ $variant->id }}">

                    @if ($addresses->isNotEmpty())
                        <div style="margin-bottom:20px;">
                            <label style="display:block;font-size:0.85rem;font-weight:700;color:#475569;margin-bottom:8px;">Choose Saved Address:</label>
                            <div style="display:flex;flex-direction:column;gap:10px;">
                                @foreach ($addresses as $addr)
                                    <label style="display:flex;align-items:flex-start;gap:12px;padding:12px;border:1.5px solid #e2e8f0;border-radius:10px;cursor:pointer;">
                                        <input type="radio" name="address_id" value="{{ $addr->id }}" @checked($loop->first) style="margin-top:3px;">
                                        <span style="font-size:0.88rem;color:#334155;line-height:1.4;">
                                            <strong>{{ $addr->name }}</strong> ({{ $addr->phone }})<br>
                                            {{ $addr->address_line_1 }} {{ $addr->address_line_2 }}, {{ $addr->city }}, {{ $addr->state }} - {{ $addr->postal_code }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
                            <div>
                                <label style="display:block;font-size:0.82rem;font-weight:700;color:#475569;margin-bottom:4px;">Full Name *</label>
                                <input type="text" name="shipping_name" value="{{ $user->name }}" required style="width:100%;padding:10px 12px;border:1.5px solid #cbd5e1;border-radius:8px;font-size:0.9rem;">
                            </div>
                            <div>
                                <label style="display:block;font-size:0.82rem;font-weight:700;color:#475569;margin-bottom:4px;">Phone Number *</label>
                                <input type="tel" name="shipping_phone" value="{{ $user->phone }}" required style="width:100%;padding:10px 12px;border:1.5px solid #cbd5e1;border-radius:8px;font-size:0.9rem;">
                            </div>
                        </div>
                        <div style="margin-bottom:14px;">
                            <label style="display:block;font-size:0.82rem;font-weight:700;color:#475569;margin-bottom:4px;">Street Address *</label>
                            <input type="text" name="shipping_address_line_1" placeholder="Flat / House no / Street" required style="width:100%;padding:10px 12px;border:1.5px solid #cbd5e1;border-radius:8px;font-size:0.9rem;">
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;margin-bottom:16px;">
                            <div>
                                <label style="display:block;font-size:0.82rem;font-weight:700;color:#475569;margin-bottom:4px;">City *</label>
                                <input type="text" name="shipping_city" required style="width:100%;padding:10px 12px;border:1.5px solid #cbd5e1;border-radius:8px;font-size:0.9rem;">
                            </div>
                            <div>
                                <label style="display:block;font-size:0.82rem;font-weight:700;color:#475569;margin-bottom:4px;">State *</label>
                                <input type="text" name="shipping_state" required style="width:100%;padding:10px 12px;border:1.5px solid #cbd5e1;border-radius:8px;font-size:0.9rem;">
                            </div>
                            <div>
                                <label style="display:block;font-size:0.82rem;font-weight:700;color:#475569;margin-bottom:4px;">Pincode *</label>
                                <input type="text" name="shipping_postal_code" pattern="[0-9]{6}" required style="width:100%;padding:10px 12px;border:1.5px solid #cbd5e1;border-radius:8px;font-size:0.9rem;">
                            </div>
                        </div>
                    @endif

                    <button type="submit" id="submitBtn" style="width:100%;padding:14px;background:#6b21a8;color:#fff;border:none;border-radius:12px;font-size:1rem;font-weight:800;cursor:pointer;margin-top:12px;transition:background 0.2s;">
                        Set Up Mandate &amp; Pay ₹{{ number_format($plan->month_1_price, 0) }} Today →
                    </button>
                    <p style="text-align:center;font-size:0.78rem;color:#64748b;margin:8px 0 0;">
                        Secure 256-bit automated mandate via Cashfree Payments
                    </p>
                </form>
            </div>

            {{-- Right Column: Plan & Pricing Summary --}}
            <div style="background:#fff;border-radius:16px;padding:28px;box-shadow:0 4px 20px rgba(0,0,0,0.04);border:1px solid #f1f5f9;height:fit-content;">
                <h2 style="font-size:1.2rem;color:#1e293b;font-weight:800;margin:0 0 16px;">2. Plan Summary</h2>

                <div style="display:flex;gap:16px;align-items:center;padding-bottom:16px;border-bottom:1px solid #e2e8f0;margin-bottom:16px;">
                    <div style="width:64px;height:64px;border-radius:10px;background:#f8fafc;display:flex;align-items:center;justify-content:center;overflow:hidden;border:1px solid #e2e8f0;">
                        @if ($product->images->isNotEmpty())
                            <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" alt="{{ $product->name }}" style="max-width:100%;max-height:100%;object-fit:contain;">
                        @else
                            <img src="{{ asset('img/product2.png') }}" alt="{{ $product->name }}" style="max-width:100%;max-height:100%;object-fit:contain;">
                        @endif
                    </div>
                    <div>
                        <strong style="display:block;font-size:1rem;color:#0f172a;">{{ $product->name }}</strong>
                        <span style="font-size:0.82rem;color:#64748b;">
                            {{ $variant->name ?: collect($variant->attributes)->implode(' · ') }}
                        </span>
                        <span style="display:inline-block;background:#ede9fe;color:#6b21a8;font-size:0.75rem;font-weight:700;padding:2px 8px;border-radius:12px;margin-top:4px;">
                            {{ $plan->duration_months }} Months Plan
                        </span>
                    </div>
                </div>

                @if ($plan->free_gift_label)
                    <div style="background:#ecfdf5;border:1px solid #a7f3d0;border-radius:10px;padding:10px 14px;display:flex;align-items:center;gap:10px;margin-bottom:18px;">
                        <span style="font-size:1.2rem;">🎁</span>
                        <div style="font-size:0.85rem;color:#065f46;font-weight:700;">
                            Bonus Perk Included: {{ $plan->free_gift_label }}
                        </div>
                    </div>
                @endif

                <div style="display:flex;flex-direction:column;gap:10px;font-size:0.9rem;color:#334155;margin-bottom:18px;">
                    <div style="display:flex;justify-content:space-between;">
                        <span>Month 1 (due today)</span>
                        <strong>₹{{ number_format($plan->month_1_price, 2) }}</strong>
                    </div>
                    <div style="display:flex;justify-content:space-between;color:#059669;">
                        <span>Months 2 to {{ $plan->duration_months }}</span>
                        <strong>₹{{ number_format($plan->recurring_price, 2) }} / month</strong>
                    </div>
                    <div style="display:flex;justify-content:space-between;padding-top:10px;border-top:1px dashed #cbd5e1;font-size:1rem;">
                        <strong>Total Plan Value</strong>
                        <strong>₹{{ number_format($calculation['total'], 2) }}</strong>
                    </div>
                    @if ($calculation['saving'] > 0)
                        <div style="display:flex;justify-content:space-between;color:#059669;font-weight:700;font-size:0.85rem;">
                            <span>Total Savings vs MRP</span>
                            <span>Save ₹{{ number_format($calculation['saving'], 2) }}</span>
                        </div>
                    @endif
                </div>

                <div style="background:#f8fafc;border-radius:10px;padding:12px;font-size:0.8rem;color:#64748b;line-height:1.5;">
                    ✓ Zero commitment beyond scheduled plan<br>
                    ✓ Pause, skip, or cancel future cycles anytime from your account<br>
                    ✓ Automatic monthly delivery to your doorstep
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('subscribeForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.textContent = 'Setting up Mandate...';

    const formData = new FormData(this);

    try {
        const res = await fetch(this.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: formData
        });

        const data = await res.json();
        if (data.redirect_url) {
            window.location.href = data.redirect_url;
        } else if (data.auth_url) {
            window.location.href = data.auth_url;
        } else {
            alert(data.message || data.error || 'Failed to initiate subscription mandate.');
            btn.disabled = false;
            btn.textContent = 'Set Up Mandate & Pay Today →';
        }
    } catch (err) {
        console.error(err);
        alert('An unexpected error occurred. Please try again.');
        btn.disabled = false;
        btn.textContent = 'Set Up Mandate & Pay Today →';
    }
});
</script>
@endsection
