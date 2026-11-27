@extends('emails.layout')

@section('title', 'Action Required: Subscription Payment Failed')
@section('preheader', 'We were unable to process your recurring subscription payment.')

@section('hero')
    <p style="margin: 0 0 10px; color: #dc2626; font-size: 11px; font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase;">
        PAYMENT FAILED ⚠️
    </p>
    <h1 class="hero-h1" style="margin: 0 auto 12px; color: #0d0020; font-family: 'Nunito', Arial, sans-serif; font-size: 28px; font-weight: 900; line-height: 1.25;">
        Payment Could Not Be Processed
    </h1>
    <p style="margin: 0 auto; color: #555166; font-size: 15px; line-height: 1.6;">
        Please update your payment method to avoid service interruption.
    </p>
@endsection

@section('content')
    <p style="margin: 0 0 16px; font-size: 16px;">
        Hi <strong>{{ $user?->name ?: 'Parent' }}</strong>,
    </p>
    <p style="margin: 0 0 18px; color: #555166; line-height: 1.75;">
        We attempted to process the scheduled renewal payment for your <strong>{{ $plan?->name ?? 'Diet Plan' }}</strong> subscription{{ $child ? ' for ' . $child->name : '' }}, but the transaction was declined by your bank or payment provider.
    </p>

    <!-- ERROR DETAILS CARD -->
    <div style="background-color: #fff1f2; border: 1px solid #fecdd3; border-radius: 14px; padding: 20px; margin-bottom: 24px;">
        <table width="100%" cellspacing="0" cellpadding="0" border="0" style="font-size: 14px;">
            <tr>
                <td style="padding: 5px 0; color: #6b6b80;" width="40%">Subscription:</td>
                <td style="padding: 5px 0; color: #0d0020; font-weight: 700;">{{ $plan?->name ?? 'Diet Plan' }}</td>
            </tr>
            @if($errorMessage)
                <tr>
                    <td style="padding: 5px 0; color: #6b6b80;">Decline Reason:</td>
                    <td style="padding: 5px 0; color: #dc2626; font-weight: 600;">{{ $errorMessage }}</td>
                </tr>
            @endif
            <tr>
                <td style="padding: 5px 0; color: #6b6b80;">Next Auto-Retry:</td>
                <td style="padding: 5px 0; color: #0d0020; font-weight: 700;">{{ $retryDate ?? 'Within 48 hours' }}</td>
            </tr>
        </table>
    </div>

    <div style="background-color: #fffbf5; border: 1px solid #f4e7ed; border-radius: 12px; padding: 16px 20px; margin-bottom: 24px; font-size: 14px; color: #555166; line-height: 1.7;">
        💡 <strong>Common reasons for failure:</strong> Insufficient balance, expired mandate/card, or bank security blocks on recurring auto-debit. Please ensure your account has sufficient balance or update your mandate.
    </div>

    <div style="text-align: center; margin: 28px 0 16px;">
        <a href="{{ route('userdashboard') }}" class="cta-btn" style="background-color: #dc2626;" target="_blank">
            Update Payment Method &rarr;
        </a>
    </div>
@endsection
