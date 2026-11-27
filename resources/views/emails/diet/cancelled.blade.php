@extends('emails.layout')

@section('title', 'Your NutriBuddy Subscription Was Cancelled')
@section('preheader', 'Confirmation of cancellation for your diet subscription.')

@section('hero')
    <p style="margin: 0 0 10px; color: #6b6b80; font-size: 11px; font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase;">
        SUBSCRIPTION CANCELLED
    </p>
    <h1 class="hero-h1" style="margin: 0 auto 12px; color: #0d0020; font-family: 'Nunito', Arial, sans-serif; font-size: 28px; font-weight: 900; line-height: 1.25;">
        Subscription Cancelled
    </h1>
    <p style="margin: 0 auto; color: #555166; font-size: 15px; line-height: 1.6;">
        Your recurring billing for {{ $plan?->name ?? 'Diet Plan' }} has been stopped.
    </p>
@endsection

@section('content')
    <p style="margin: 0 0 16px; font-size: 16px;">
        Hi <strong>{{ $user?->name ?: 'Parent' }}</strong>,
    </p>
    <p style="margin: 0 0 20px; color: #555166; line-height: 1.75;">
        As requested, your <strong>{{ $plan?->name ?? 'Diet Plan' }}</strong> subscription{{ $child ? ' for ' . $child->name : '' }} has been cancelled. You will not be charged again for future cycles.
    </p>

    @if($subscription->expires_at && $subscription->expires_at->isFuture())
        <div style="background-color: #faf5ff; border: 1px solid #f1e8f5; border-radius: 12px; padding: 18px 20px; margin-bottom: 24px; font-size: 14px; color: #555166; line-height: 1.7;">
            ℹ️ <strong>Remaining Access:</strong> You can continue accessing your active diet charts and previous recommendations until the end of your prepaid period on <strong>{{ $subscription->expires_at->format('d M Y') }}</strong>.
        </div>
    @endif

    <p style="margin: 0 0 24px; color: #555166; line-height: 1.75;">
        We’re always here if you want to rejoin in the future. We wish {{ $child?->name ?? 'your child' }} radiant health and growth!
    </p>

    <div style="text-align: center; margin: 28px 0 16px;">
        <a href="{{ route('home') }}" class="cta-btn" target="_blank">
            Visit NutriBuddy &rarr;
        </a>
    </div>
@endsection
