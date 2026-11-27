@extends('emails.layout')

@section('title', 'Your NutriBuddy Diet Subscription is Expiring Soon')
@section('preheader', 'Your subscription will expire in ' . $daysRemaining . ' days. Renew now to maintain continuous pediatric care.')

@section('hero')
    <p style="margin: 0 0 10px; color: #d97706; font-size: 11px; font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase;">
        SUBSCRIPTION EXPIRING SOON ⏳
    </p>
    <h1 class="hero-h1" style="margin: 0 auto 12px; color: #0d0020; font-family: 'Nunito', Arial, sans-serif; font-size: 28px; font-weight: 900; line-height: 1.25;">
        {{ $daysRemaining }} Days Left in Your Plan
    </h1>
    <p style="margin: 0 auto; color: #555166; font-size: 15px; line-height: 1.6;">
        Keep {{ $child?->name ?? 'your child' }}’s nutrition journey going without interruption.
    </p>
@endsection

@section('content')
    <p style="margin: 0 0 16px; font-size: 16px;">
        Hi <strong>{{ $user?->name ?: 'Parent' }}</strong>,
    </p>
    <p style="margin: 0 0 20px; color: #555166; line-height: 1.75;">
        This is a friendly reminder that your <strong>{{ $plan?->name ?? 'Diet Plan' }}</strong> subscription{{ $child ? ' for ' . $child->name : '' }} is set to expire on <strong>{{ optional($subscription->expires_at)->format('d M Y') }}</strong> (in {{ $daysRemaining }} days).
    </p>

    <!-- HIGHLIGHTS -->
    <div style="background-color: #faf5ff; border: 1px solid #f1e8f5; border-radius: 14px; padding: 20px; margin-bottom: 24px;">
        <div style="font-size: 13px; font-weight: 800; color: #5b21b6; text-transform: uppercase; margin-bottom: 10px;">
            By Renewing, You Maintain:
        </div>
        <ul style="margin: 0; padding-left: 20px; color: #555166; font-size: 14px; line-height: 1.8;">
            <li>Continuous access to your certified pediatric nutritionist.</li>
            <li>New seasonal weekly diet charts adapted to your child's evolving growth metrics.</li>
            <li>Quarterly milestone assessments &amp; micronutrient deficiency tracking.</li>
        </ul>
    </div>

    <div style="text-align: center; margin: 28px 0 16px;">
        <a href="{{ route('userdashboard') }}" class="cta-btn" target="_blank">
            Renew Subscription Now &rarr;
        </a>
    </div>
@endsection
