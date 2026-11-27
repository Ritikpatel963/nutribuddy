@extends('emails.layout')

@section('title', 'Return Update — Return #' . $orderReturn->return_number)
@section('preheader', 'Your return request status is now ' . strtoupper($orderReturn->status))

@section('hero')
    @php
        $badgeColor = match($orderReturn->status) {
            'approved' => '#10b981',
            'rejected' => '#ef4444',
            'completed' => '#00bfff',
            default => '#7c3aed'
        };
    @endphp
    <p style="margin: 0 0 10px; color: #c0306f; font-size: 11px; font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase;">
        RETURN STATUS UPDATE 📦
    </p>
    <h1 class="hero-h1" style="margin: 0 auto 12px; color: #0d0020; font-family: 'Nunito', Arial, sans-serif; font-size: 28px; font-weight: 900; line-height: 1.25;">
        Return #{{ $orderReturn->return_number }}
    </h1>
    <div style="display: inline-block; padding: 6px 16px; border-radius: 20px; background-color: {{ $badgeColor }}; color: #ffffff; font-weight: 800; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-top: 4px;">
        {{ strtoupper($orderReturn->status) }}
    </div>
@endsection

@section('content')
    <p style="margin: 0 0 16px; font-size: 16px;">
        Hello <strong>{{ $orderReturn->order?->customer_name ?? 'Customer' }}</strong>,
    </p>
    <p style="margin: 0 0 20px; color: #555166; line-height: 1.7;">
        Your return request for order <strong>#{{ $orderReturn->order?->order_number }}</strong> has been updated to <strong>{{ strtoupper($orderReturn->status) }}</strong>.
    </p>

    <!-- STATUS CARD -->
    <div style="background-color: #faf5ff; border: 1px solid #f1e8f5; border-radius: 14px; padding: 20px; margin-bottom: 24px;">
        <table width="100%" cellspacing="0" cellpadding="0" border="0" style="font-size: 14px;">
            <tr>
                <td style="padding: 5px 0; color: #6b6b80;" width="40%">Return Number:</td>
                <td style="padding: 5px 0; color: #0d0020; font-weight: 700;">#{{ $orderReturn->return_number }}</td>
            </tr>
            <tr>
                <td style="padding: 5px 0; color: #6b6b80;">Status:</td>
                <td style="padding: 5px 0; font-weight: 800; color: {{ $badgeColor }};">{{ strtoupper($orderReturn->status) }}</td>
            </tr>
            @if($orderReturn->admin_note)
                <tr>
                    <td style="padding: 5px 0; color: #6b6b80; vertical-align: top;">Note from Team:</td>
                    <td style="padding: 5px 0; color: #0d0020;">{{ $orderReturn->admin_note }}</td>
                </tr>
            @endif
            @if($orderReturn->refund_amount > 0)
                <tr>
                    <td style="padding: 8px 0 5px; color: #6b6b80; border-top: 1px dashed #f4e7ed;">Refund Amount:</td>
                    <td style="padding: 8px 0 5px; color: #10b981; font-weight: 800; font-size: 16px; border-top: 1px dashed #f4e7ed;">
                        ₹{{ number_format($orderReturn->refund_amount, 2) }}
                    </td>
                </tr>
            @endif
        </table>
    </div>

    @if($orderReturn->refund_amount > 0)
        <div style="padding: 14px 18px; border-radius: 10px; background-color: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; font-size: 13px; line-height: 1.6; margin-bottom: 24px;">
            💰 <strong>Refund Notification:</strong> Your refund of <strong>₹{{ number_format($orderReturn->refund_amount, 2) }}</strong> has been initiated. Depending on your bank or payment method, it may take 3–5 business days to reflect.
        </div>
    @endif

    <div style="text-align: center; margin: 28px 0 16px;">
        <a href="{{ route('user.orders.returns.index') }}" class="cta-btn" target="_blank">
            View Returns in Account &rarr;
        </a>
    </div>
@endsection
