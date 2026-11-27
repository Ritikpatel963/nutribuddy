@extends('emails.layout')

@section('title', 'Your NutriBuddy Subscription Was Successfully Renewed')
@section('preheader', 'Your recurring subscription has been renewed successfully.')

@section('hero')
    <p style="margin: 0 0 10px; color: #10b981; font-size: 11px; font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase;">
        AUTO-RENEWAL SUCCESSFUL 🔄
    </p>
    <h1 class="hero-h1" style="margin: 0 auto 12px; color: #0d0020; font-family: 'Nunito', Arial, sans-serif; font-size: 28px; font-weight: 900; line-height: 1.25;">
        Subscription Renewed!
    </h1>
    <p style="margin: 0 auto; color: #555166; font-size: 15px; line-height: 1.6;">
        Your {{ $plan?->name ?? 'Diet Plan' }} subscription has been renewed for the next billing cycle.
    </p>
@endsection

@section('content')
    <p style="margin: 0 0 16px; font-size: 16px;">
        Hi <strong>{{ $user?->name ?: 'Parent' }}</strong>,
    </p>
    <p style="margin: 0 0 20px; color: #555166; line-height: 1.75;">
        This is a confirmation that your recurring subscription for <strong>{{ $child?->name ?? 'your child' }}</strong> was renewed successfully. Your uninterrupted access to customized meal charts and certified pediatric guidance continues.
    </p>

    <!-- RENEWAL SUMMARY CARD -->
    <div style="background-color: #faf5ff; border: 1px solid #f1e8f5; border-radius: 14px; padding: 20px; margin-bottom: 24px;">
        <table width="100%" cellspacing="0" cellpadding="0" border="0" style="font-size: 14px;">
            <tr>
                <td style="padding: 5px 0; color: #6b6b80;" width="40%">Plan:</td>
                <td style="padding: 5px 0; color: #0d0020; font-weight: 700;">{{ $plan?->name ?? 'Diet Plan' }}</td>
            </tr>
            @if($child)
                <tr>
                    <td style="padding: 5px 0; color: #6b6b80;">Child Profile:</td>
                    <td style="padding: 5px 0; color: #7c3aed; font-weight: 600;">{{ $child->name }}</td>
                </tr>
            @endif
            <tr>
                <td style="padding: 5px 0; color: #6b6b80;">Amount Debited:</td>
                <td style="padding: 5px 0; color: #10b981; font-weight: 800; font-size: 16px;">₹{{ number_format($amountPaid, 2) }}</td>
            </tr>
            <tr>
                <td style="padding: 5px 0; color: #6b6b80;">Payment Method:</td>
                <td style="padding: 5px 0; color: #0d0020;">{{ $paymentMethod }}</td>
            </tr>
            <tr>
                <td style="padding: 5px 0; color: #6b6b80;">Next Billing Date:</td>
                <td style="padding: 5px 0; color: #0d0020; font-weight: 600;">{{ optional($subscription->expires_at)->format('d M Y') ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <div style="text-align: center; margin: 28px 0 16px;">
        <a href="{{ route('userdashboard') }}" class="cta-btn" target="_blank">
            Manage Subscription in Dashboard &rarr;
        </a>
    </div>
@endsection
