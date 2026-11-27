@extends('emails.layout')

@section('title', 'Welcome to the NutriBuddy Family!')
@section('preheader', 'A happier, healthier journey starts here. We’re glad you’re with us.')

@section('hero')
    <p style="margin: 0 0 10px; color: #c0306f; font-size: 11px; font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase;">
        WELCOME TO THE NUTRIBUDDY FAMILY
    </p>
    <h1 class="hero-h1" style="margin: 0 auto 12px; color: #0d0020; font-family: 'Nunito', Arial, sans-serif; font-size: 28px; font-weight: 900; line-height: 1.25;">
        A happier, healthier journey starts here.
    </h1>
    <p style="margin: 0 auto; color: #555166; font-size: 15px; line-height: 1.6;">
        Thoughtful nutrition and little everyday wins can make a big difference. We’re glad you’re here.
    </p>
@endsection

@section('content')
    <p style="margin: 0 0 16px; font-size: 16px;">
        Hi <strong>{{ $name }}</strong>,
    </p>
    <p style="margin: 0 0 18px; color: #555166; line-height: 1.75;">
        At NutriBuddy, we believe healthy habits should feel simple, positive, and possible for every family. Whether you are addressing fussy eating, supporting healthy weight and growth, or discovering clean-label daily nutrition, we are here with you at every step.
    </p>
    
    <p style="margin: 0 0 12px; font-weight: 700; color: #0d0020;">
        Here are three easy ways to get started:
    </p>

    <!-- 3 STEP GUIDE -->
    <table width="100%" cellspacing="0" cellpadding="0" border="0" style="margin: 16px 0 24px;">
        <tr>
            <td style="padding: 12px 16px; background-color: #faf5ff; border: 1px solid #f1e8f5; border-radius: 10px; margin-bottom: 8px;">
                <table width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td width="36" valign="top">
                            <div style="width: 28px; height: 28px; border-radius: 50%; background-color: #ede9fe; color: #5b21b6; font-weight: 800; line-height: 28px; text-align: center; font-size: 13px;">
                                1
                            </div>
                        </td>
                        <td style="color: #0d0020; font-size: 14px; line-height: 1.6;">
                            <strong>Take the Free Pediatric Assessment:</strong> Understand your child’s micronutrient gaps and developmental milestones in under 3 minutes.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr><td height="8" style="font-size:0; line-height:0;">&nbsp;</td></tr>
        <tr>
            <td style="padding: 12px 16px; background-color: #faf5ff; border: 1px solid #f1e8f5; border-radius: 10px;">
                <table width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td width="36" valign="top">
                            <div style="width: 28px; height: 28px; border-radius: 50%; background-color: #ede9fe; color: #5b21b6; font-weight: 800; line-height: 28px; text-align: center; font-size: 13px;">
                                2
                            </div>
                        </td>
                        <td style="color: #0d0020; font-size: 14px; line-height: 1.6;">
                            <strong>Explore Feel-Good Nutrition:</strong> Certified clean-label formulas made with natural prebiotic fiber, vitamins, and minerals.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr><td height="8" style="font-size:0; line-height:0;">&nbsp;</td></tr>
        <tr>
            <td style="padding: 12px 16px; background-color: #faf5ff; border: 1px solid #f1e8f5; border-radius: 10px;">
                <table width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td width="36" valign="top">
                            <div style="width: 28px; height: 28px; border-radius: 50%; background-color: #ede9fe; color: #5b21b6; font-weight: 800; line-height: 28px; text-align: center; font-size: 13px;">
                                3
                            </div>
                        </td>
                        <td style="color: #0d0020; font-size: 14px; line-height: 1.6;">
                            <strong>Personalized Diet Chart:</strong> Work with certified pediatric dietitians to get custom weekly meal plans tailored to regional taste.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div style="text-align: center; margin: 28px 0 16px;">
        <a href="{{ url('/') }}" class="cta-btn" target="_blank">
            Explore NutriBuddy &rarr;
        </a>
    </div>
@endsection
