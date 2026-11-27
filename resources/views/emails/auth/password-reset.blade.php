@extends('emails.layout')

@section('title', 'Reset Your NutriBuddy Password')
@section('preheader', 'Secure password reset request for your NutriBuddy account.')

@section('hero')
    <p style="margin: 0 0 10px; color: #7c3aed; font-size: 11px; font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase;">
        ACCOUNT SECURITY 🔐
    </p>
    <h1 class="hero-h1" style="margin: 0 auto 12px; color: #0d0020; font-family: 'Nunito', Arial, sans-serif; font-size: 28px; font-weight: 900; line-height: 1.25;">
        Password Reset Request
    </h1>
    <p style="margin: 0 auto; color: #555166; font-size: 15px; line-height: 1.6;">
        Follow the instructions below to reset your password.
    </p>
@endsection

@section('content')
    <p style="margin: 0 0 16px; font-size: 16px;">
        Hello <strong>{{ $user->name ?: 'Customer' }}</strong>,
    </p>
    <p style="margin: 0 0 18px; color: #555166; line-height: 1.75;">
        You are receiving this email because we received a password reset request for your NutriBuddy account.
    </p>

    <div style="background-color: #faf5ff; border: 1px solid #f1e8f5; border-radius: 12px; padding: 18px; margin: 20px 0; font-size: 14px; color: #555166; line-height: 1.6;">
        ⏱️ This password reset link is valid for <strong>{{ $expiresMinutes }} minutes</strong>. For your security, this link can only be used once.
    </div>

    <div style="text-align: center; margin: 30px 0 20px;">
        <a href="{{ $resetUrl }}" class="cta-btn" target="_blank">
            Reset Your Password &rarr;
        </a>
    </div>

    <p style="margin: 24px 0 10px; color: #6b6b80; font-size: 13px; line-height: 1.6;">
        If you did not request a password reset, no further action is required. Your account remains completely secure.
    </p>
    <p style="margin: 0; color: #8e8e9f; font-size: 12px; word-break: break-all;">
        If the button above does not work, copy and paste this URL into your browser:<br>
        <a href="{{ $resetUrl }}" style="color: #c0306f;">{{ $resetUrl }}</a>
    </p>
@endsection
