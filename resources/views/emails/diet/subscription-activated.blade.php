@extends('emails.layout')

@section('title', 'Welcome to Your NutriBuddy Diet Subscription!')
@section('preheader', 'Your child’s personalized nutrition journey begins now.')

@section('hero')
    <p style="margin: 0 0 10px; color: #10b981; font-size: 11px; font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase;">
        SUBSCRIPTION ACTIVE 🎉
    </p>
    <h1 class="hero-h1" style="margin: 0 auto 12px; color: #0d0020; font-family: 'Nunito', Arial, sans-serif; font-size: 28px; font-weight: 900; line-height: 1.25;">
        Your {{ $plan?->name ?? 'Diet Plan' }} is Confirmed!
    </h1>
    <p style="margin: 0 auto; color: #555166; font-size: 15px; line-height: 1.6;">
        Personalized pediatric nutrition designed specifically for {{ $child?->name ?? 'your child' }}.
    </p>
@endsection

@section('content')
    <p style="margin: 0 0 16px; font-size: 16px;">
        Hi <strong>{{ $user?->name ?: 'Parent' }}</strong>,
    </p>
    <p style="margin: 0 0 20px; color: #555166; line-height: 1.75;">
        Thank you for choosing NutriBuddy! We have activated your <strong>{{ $plan?->name ?? 'Personalized Diet' }}</strong> subscription. Our certified pediatric nutritionists are already reviewing your child’s health assessment to create their personalized meal schedule.
    </p>

    <!-- SUBSCRIPTION DETAILS CARD -->
    <div style="background-color: #faf5ff; border: 1px solid #f1e8f5; border-radius: 14px; padding: 20px; margin-bottom: 24px;">
        <table width="100%" cellspacing="0" cellpadding="0" border="0" style="font-size: 14px;">
            <tr>
                <td style="padding: 5px 0; color: #6b6b80;" width="40%">Plan Name:</td>
                <td style="padding: 5px 0; color: #0d0020; font-weight: 700; font-size: 15px;">{{ $plan?->name ?? 'Diet Plan' }}</td>
            </tr>
            @if($child)
                <tr>
                    <td style="padding: 5px 0; color: #6b6b80;">Child Profile:</td>
                    <td style="padding: 5px 0; color: #7c3aed; font-weight: 700;">{{ $child->name }} ({{ $child->age_years ?? '' }} yrs)</td>
                </tr>
            @endif
            <tr>
                <td style="padding: 5px 0; color: #6b6b80;">Payment Status:</td>
                <td style="padding: 5px 0; font-weight: 800; color: #10b981;">PAID &amp; ACTIVE</td>
            </tr>
            <tr>
                <td style="padding: 5px 0; color: #6b6b80;">Valid Until:</td>
                <td style="padding: 5px 0; color: #0d0020; font-weight: 600;">{{ optional($subscription->expires_at)->format('d M Y') ?? 'Ongoing' }}</td>
            </tr>
        </table>
    </div>

    <!-- WHAT TO EXPECT -->
    <div style="margin: 22px 0; padding: 18px 20px; border: 1px solid #f4e7ed; border-radius: 12px; background-color: #fffbf5;">
        <div style="font-size: 13px; font-weight: 800; color: #5b21b6; text-transform: uppercase; margin-bottom: 8px;">
            What Happens Next?
        </div>
        <p style="margin: 0 0 10px; color: #555166; font-size: 14px; line-height: 1.7;">
            1. <strong>Pediatric Review:</strong> Our clinical dietitians analyze the questionnaire to verify age compatibility, allergies, and micronutrient gaps.
        </p>
        <p style="margin: 0 0 10px; color: #555166; font-size: 14px; line-height: 1.7;">
            2. <strong>Chart Release:</strong> You’ll receive an email the moment the 7-day personalized meal plan and nutritional guide are approved.
        </p>
        <p style="margin: 0; color: #555166; font-size: 14px; line-height: 1.7;">
            3. <strong>Quarterly Guidance:</strong> Enjoy ongoing growth tracking, check-ins, and meal log feedback throughout your plan.
        </p>
    </div>

    <div style="text-align: center; margin: 28px 0 16px;">
        <a href="{{ route('userdashboard') }}" class="cta-btn" target="_blank">
            Open Nutrition Dashboard &rarr;
        </a>
    </div>
@endsection
