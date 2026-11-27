@extends('emails.layout')

@section('title', 'Return Request Received — Return #' . $orderReturn->return_number)
@section('preheader', 'Your return request for order #' . ($orderReturn->order?->order_number ?? '') . ' has been received.')

@section('hero')
    <p style="margin: 0 0 10px; color: #c0306f; font-size: 11px; font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase;">
        RETURN REQUEST RECEIVED 🔄
    </p>
    <h1 class="hero-h1" style="margin: 0 auto 12px; color: #0d0020; font-family: 'Nunito', Arial, sans-serif; font-size: 28px; font-weight: 900; line-height: 1.25;">
        We’ve Received Your Return Request
    </h1>
    <p style="margin: 0 auto; color: #555166; font-size: 15px; line-height: 1.6;">
        Return ID <strong>#{{ $orderReturn->return_number }}</strong>
    </p>
@endsection

@section('content')
    <p style="margin: 0 0 16px; font-size: 16px;">
        Hello <strong>{{ $orderReturn->order?->customer_name ?? 'Customer' }}</strong>,
    </p>
    <p style="margin: 0 0 20px; color: #555166; line-height: 1.7;">
        We have received your return request for order <strong>#{{ $orderReturn->order?->order_number }}</strong>. Our support team is reviewing the details and will schedule inspection/pickup shortly.
    </p>

    <!-- RETURN DETAILS CARD -->
    <div style="background-color: #faf5ff; border: 1px solid #f1e8f5; border-radius: 14px; padding: 20px; margin-bottom: 24px;">
        <table width="100%" cellspacing="0" cellpadding="0" border="0" style="font-size: 14px;">
            <tr>
                <td style="padding: 5px 0; color: #6b6b80;" width="40%">Return Number:</td>
                <td style="padding: 5px 0; color: #0d0020; font-weight: 700;">#{{ $orderReturn->return_number }}</td>
            </tr>
            <tr>
                <td style="padding: 5px 0; color: #6b6b80;">Original Order:</td>
                <td style="padding: 5px 0; color: #7c3aed; font-weight: 700;">#{{ $orderReturn->order?->order_number }}</td>
            </tr>
            <tr>
                <td style="padding: 5px 0; color: #6b6b80;">Status:</td>
                <td style="padding: 5px 0; color: #d97706; font-weight: 800;">{{ strtoupper($orderReturn->status) }}</td>
            </tr>
            @if($orderReturn->reason)
                <tr>
                    <td style="padding: 5px 0; color: #6b6b80;">Reason:</td>
                    <td style="padding: 5px 0; color: #0d0020;">{{ ucfirst(str_replace('_', ' ', $orderReturn->reason)) }}</td>
                </tr>
            @endif
            @if($orderReturn->customer_comment)
                <tr>
                    <td style="padding: 5px 0; color: #6b6b80;">Notes:</td>
                    <td style="padding: 5px 0; color: #555166; font-style: italic;">“{{ $orderReturn->customer_comment }}”</td>
                </tr>
            @endif
        </table>
    </div>

    <!-- WHAT HAPPENS NEXT -->
    <div style="margin: 24px 0; padding: 18px 20px; border: 1px solid #f4e7ed; border-radius: 12px; background-color: #fffbf5;">
        <div style="font-size: 13px; font-weight: 800; color: #5b21b6; text-transform: uppercase; margin-bottom: 10px;">
            What Happens Next?
        </div>
        <ol style="margin: 0; padding-left: 20px; color: #555166; font-size: 14px; line-height: 1.8;">
            <li>Our operations team verifies your return eligibility within 24–48 hours.</li>
            <li>Once approved, a pickup partner will be assigned to collect the items.</li>
            <li>Upon warehouse verification, your refund will be processed to the original payment method or bank account.</li>
        </ol>
    </div>

    <div style="text-align: center; margin: 28px 0 16px;">
        <a href="{{ route('user.orders.returns.index') }}" class="cta-btn" target="_blank">
            View Returns Dashboard &rarr;
        </a>
    </div>
@endsection
