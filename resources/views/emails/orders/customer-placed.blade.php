@extends('emails.layout')

@section('title', 'Your NutriBuddy Order #' . $order->order_number . ' is Confirmed!')
@section('preheader', 'Thank you for your order! We are packing your child’s nutrition essentials.')

@section('hero')
    <p style="margin: 0 0 10px; color: #c0306f; font-size: 11px; font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase;">
        ORDER CONFIRMED 🎉
    </p>
    <h1 class="hero-h1" style="margin: 0 auto 12px; color: #0d0020; font-family: 'Nunito', Arial, sans-serif; font-size: 28px; font-weight: 900; line-height: 1.25;">
        Thank You for Your Order!
    </h1>
    <p style="margin: 0 auto; color: #555166; font-size: 15px; line-height: 1.6;">
        Order <strong>#{{ $order->order_number }}</strong> has been placed successfully.
    </p>
@endsection

@section('content')
    <p style="margin: 0 0 18px; font-size: 16px;">
        Hi <strong>{{ $order->customer_name }}</strong>,
    </p>
    <p style="margin: 0 0 20px; color: #555166; line-height: 1.7;">
        We’ve received your order and our team is already preparing it with the utmost care. Below is your order summary and delivery details.
    </p>

    <!-- ORDER SUMMARY TABLE -->
    <div style="background-color: #ffffff; border: 1px solid #f1e8f5; border-radius: 14px; overflow: hidden; margin-bottom: 24px;">
        <div style="background-color: #faf5ff; padding: 12px 18px; border-bottom: 1px solid #f1e8f5; font-weight: 800; color: #5b21b6; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">
            Order Items
        </div>
        <table width="100%" cellspacing="0" cellpadding="0" border="0" style="font-size: 14px; border-collapse: collapse;">
            <tbody>
                @foreach($order->items as $item)
                    @php
                        $vName = $item->item_snapshot['variant_name'] ?? ($item->productVariant?->name ?? null);
                    @endphp
                    <tr>
                        <td style="padding: 14px 18px; border-bottom: 1px solid #f8f2fa; vertical-align: middle;">
                            <strong style="color: #0d0020; font-size: 14px;">{{ $item->product_name }}</strong>
                            @if($vName)
                                <div style="color: #845ec2; font-size: 12px; margin-top: 2px;">Variant: {{ $vName }}</div>
                            @endif
                        </td>
                        <td align="center" style="padding: 14px 12px; border-bottom: 1px solid #f8f2fa; color: #555166; font-size: 13px; white-space: nowrap; vertical-align: middle;">
                            Qty: <strong>{{ $item->quantity }}</strong>
                        </td>
                        <td align="right" style="padding: 14px 18px; border-bottom: 1px solid #f8f2fa; font-weight: 700; color: #0d0020; white-space: nowrap; vertical-align: middle;">
                            ₹{{ number_format($item->line_total, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- TOTALS BREAKDOWN -->
        <table width="100%" cellspacing="0" cellpadding="0" border="0" style="font-size: 14px; background-color: #fffbf5;">
            <tr>
                <td style="padding: 10px 18px 4px; color: #6b6b80;">Subtotal</td>
                <td align="right" style="padding: 10px 18px 4px; color: #0d0020; font-weight: 600;">₹{{ number_format($order->subtotal, 2) }}</td>
            </tr>
            @if($order->discount_total > 0)
                <tr>
                    <td style="padding: 4px 18px; color: #10b981;">Coupon Discount</td>
                    <td align="right" style="padding: 4px 18px; color: #10b981; font-weight: 600;">-₹{{ number_format($order->discount_total, 2) }}</td>
                </tr>
            @endif
            @if($order->coin_discount > 0)
                <tr>
                    <td style="padding: 4px 18px; color: #f59e0b;">NutriCoins Redeemed ({{ $order->coins_redeemed }})</td>
                    <td align="right" style="padding: 4px 18px; color: #f59e0b; font-weight: 600;">-₹{{ number_format($order->coin_discount, 2) }}</td>
                </tr>
            @endif
            <tr>
                <td style="padding: 4px 18px; color: #6b6b80;">Shipping Charges</td>
                <td align="right" style="padding: 4px 18px; color: #0d0020; font-weight: 600;">
                    {{ $order->shipping_total > 0 ? '₹' . number_format($order->shipping_total, 2) : 'FREE' }}
                </td>
            </tr>
            <tr>
                <td style="padding: 12px 18px; border-top: 1.5px dashed #f4e7ed; font-size: 16px; font-weight: 800; color: #0d0020;">
                    Total Amount
                </td>
                <td align="right" style="padding: 12px 18px; border-top: 1.5px dashed #f4e7ed; font-size: 18px; font-weight: 900; color: #ff4d8f;">
                    ₹{{ number_format($order->grand_total, 2) }}
                </td>
            </tr>
        </table>
    </div>

    <!-- DELIVERY & PAYMENT DETAILS -->
    <table width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 24px;">
        <tr>
            <td width="48%" style="padding: 16px; background-color: #faf5ff; border: 1px solid #f1e8f5; border-radius: 12px; vertical-align: top;">
                <div style="font-size: 12px; font-weight: 800; color: #7c3aed; text-transform: uppercase; margin-bottom: 8px;">
                    📍 Shipping Address
                </div>
                <div style="font-weight: 700; color: #0d0020; font-size: 14px;">{{ $order->shipping_name }}</div>
                <div style="color: #555166; font-size: 13px; line-height: 1.5; margin-top: 4px;">
                    {{ $order->shipping_address_line_1 }}<br>
                    @if($order->shipping_address_line_2){{ $order->shipping_address_line_2 }}<br>@endif
                    {{ $order->shipping_city }}, {{ $order->shipping_state }} - {{ $order->shipping_postal_code }}<br>
                    Phone: {{ $order->shipping_phone }}
                </div>
            </td>
            <td width="4%">&nbsp;</td>
            <td width="48%" style="padding: 16px; background-color: #faf5ff; border: 1px solid #f1e8f5; border-radius: 12px; vertical-align: top;">
                <div style="font-size: 12px; font-weight: 800; color: #7c3aed; text-transform: uppercase; margin-bottom: 8px;">
                    💳 Payment Info
                </div>
                <div style="font-size: 14px; font-weight: 700; color: #0d0020;">
                    {{ strtoupper($order->payment_method) === 'COD' ? 'Cash on Delivery (COD)' : 'Prepaid Online Payment' }}
                </div>
                <div style="color: #555166; font-size: 13px; margin-top: 6px;">
                    Status: <strong style="color: {{ $order->payment_status === 'paid' ? '#10b981' : '#f59e0b' }};">{{ strtoupper($order->payment_status) }}</strong>
                </div>
                <div style="color: #555166; font-size: 13px; margin-top: 4px;">
                    Placed on: {{ optional($order->placed_at)->format('d M Y, h:i A') ?? $order->created_at->format('d M Y, h:i A') }}
                </div>
            </td>
        </tr>
    </table>

    <!-- CTA BUTTON -->
    <div style="text-align: center; margin: 28px 0 16px;">
        <a href="{{ route('user.orders.detail-page', $order) }}" class="cta-btn" target="_blank">
            Track & View Order Details &rarr;
        </a>
    </div>
@endsection
