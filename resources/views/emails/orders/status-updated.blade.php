@extends('emails.layout')

@section('title', 'Order Update: #' . $order->order_number . ' is ' . ucfirst($statusLabel))
@section('preheader', $headline)

@section('hero')
    @php
        $badgeBg = match($order->status) {
            'delivered' => '#00d68f',
            'shipped' => '#00bfff',
            'packed', 'processing' => '#7c3aed',
            'cancelled' => '#ef4444',
            default => '#ff4d8f'
        };
    @endphp
    <p style="margin: 0 0 10px; color: #c0306f; font-size: 11px; font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase;">
        ORDER STATUS UPDATE 📦
    </p>
    <h1 class="hero-h1" style="margin: 0 auto 12px; color: #0d0020; font-family: 'Nunito', Arial, sans-serif; font-size: 28px; font-weight: 900; line-height: 1.25;">
        {{ $headline }}
    </h1>
    <div style="display: inline-block; padding: 6px 16px; border-radius: 20px; background-color: {{ $badgeBg }}; color: #ffffff; font-weight: 800; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-top: 4px;">
        {{ strtoupper($statusLabel) }}
    </div>
@endsection

@section('content')
    <p style="margin: 0 0 16px; font-size: 16px;">
        Hi <strong>{{ $order->customer_name }}</strong>,
    </p>
    <p style="margin: 0 0 20px; color: #555166; font-size: 15px; line-height: 1.7;">
        {{ $messageLine }}
    </p>

    <!-- STATUS CARD -->
    <div style="background-color: #faf5ff; border: 1px solid #f1e8f5; border-radius: 14px; padding: 20px; margin-bottom: 24px;">
        <table width="100%" cellspacing="0" cellpadding="0" border="0" style="font-size: 14px;">
            <tr>
                <td style="padding: 4px 0; color: #6b6b80;" width="40%">Order Number:</td>
                <td style="padding: 4px 0; color: #0d0020; font-weight: 700;">#{{ $order->order_number }}</td>
            </tr>
            <tr>
                <td style="padding: 4px 0; color: #6b6b80;">Order Status:</td>
                <td style="padding: 4px 0; color: #7c3aed; font-weight: 800;">{{ strtoupper($statusLabel) }}</td>
            </tr>
            <tr>
                <td style="padding: 4px 0; color: #6b6b80;">Fulfillment:</td>
                <td style="padding: 4px 0; color: #0d0020; font-weight: 600;">{{ strtoupper($order->fulfillment_status) }}</td>
            </tr>
            @if($order->payment_method === 'cod')
                <tr>
                    <td style="padding: 4px 0; color: #6b6b80;">Payment Method:</td>
                    <td style="padding: 4px 0; color: #d97706; font-weight: 700;">Cash on Delivery (₹{{ number_format($order->grand_total, 2) }})</td>
                </tr>
            @endif
            @if(filled($order->awb_code))
                <tr>
                    <td style="padding: 4px 0; color: #6b6b80;">Carrier / AWB:</td>
                    <td style="padding: 4px 0; color: #00bfff; font-weight: 800;">{{ $order->awb_code }}</td>
                </tr>
            @endif
        </table>
    </div>

    <!-- TRACKING CALLOUT IF SHIPPED -->
    @if($order->status === 'shipped' && filled($order->awb_code))
        <div style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 16px; margin-bottom: 24px; text-align: center;">
            <div style="font-size: 14px; font-weight: 800; color: #166534; margin-bottom: 4px;">
                🚚 Tracking Details Ready
            </div>
            <div style="font-size: 13px; color: #15803d;">
                Your parcel tracking number is <strong>{{ $order->awb_code }}</strong>. It may take a few hours for the courier partner to update initial scans.
            </div>
        </div>
    @endif

    <!-- CTA -->
    <div style="text-align: center; margin: 26px 0 16px;">
        <a href="{{ route('user.orders.detail-page', $order) }}" class="cta-btn" target="_blank">
            View Order & Live Status &rarr;
        </a>
    </div>
@endsection
