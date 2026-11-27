@extends('emails.layout')

@section('title', 'Your NutriBuddy Password Has Been Updated')
@section('preheader', 'Your account password was successfully updated.')

@section('hero')
    <p style="margin: 0 0 10px; color: #10b981; font-size: 11px; font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase;">
        SECURITY CONFIRMATION ✅
    </p>
    <h1 class="hero-h1" style="margin: 0 auto 12px; color: #0d0020; font-family: 'Nunito', Arial, sans-serif; font-size: 28px; font-weight: 900; line-height: 1.25;">
        Password Updated Successfully
    </h1>
    <p style="margin: 0 auto; color: #555166; font-size: 15px; line-height: 1.6;">
        Your NutriBuddy account password was changed.
    </p>
@endsection

@section('content')
    <p style="margin: 0 0 16px; font-size: 16px;">
        Hello <strong>{{ $user->name ?: 'Customer' }}</strong>,
    </p>
    <p style="margin: 0 0 18px; color: #555166; line-height: 1.75;">
        This is a quick security confirmation that your NutriBuddy account password was changed on <strong>{{ now()->format('d M Y, h:i A') }}</strong>.
    </p>

    <div style="background-color: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 12px; padding: 18px; margin: 20px 0; color: #065f46; font-size: 14px; line-height: 1.6;">
        🛡️ <strong>If you made this change</strong>, you can safely ignore this notification.
    </div>

    <div style="background-color: #fff1f2; border: 1px solid #fecdd3; border-radius: 12px; padding: 18px; margin: 20px 0; color: #991b1b; font-size: 14px; line-height: 1.6;">
        ⚠️ <strong>If you did not change your password</strong>, your account may have been compromised. Please immediately reset your password and reach out to our team at <a href="mailto:support@nutribuddy.com" style="color: #991b1b; font-weight: 700;">support@nutribuddy.com</a>.
    </div>

    <div style="text-align: center; margin: 28px 0 16px;">
        <a href="{{ route('userdashboard') }}" class="cta-btn" target="_blank">
            Open Account Dashboard &rarr;
        </a>
    </div>
@endsection
