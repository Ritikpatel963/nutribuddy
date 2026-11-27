<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <title>@yield('title', 'NutriBuddy — Stronger. Smarter. Healthier.')</title>
    <style>
        body { margin: 0; padding: 0; background-color: #fffbf5; color: #0d0020; font-family: 'DM Sans', Arial, sans-serif; -webkit-font-smoothing: antialiased; }
        a { color: #c0306f; text-decoration: underline; }
        .cta-btn { display: inline-block; padding: 14px 28px; border-radius: 10px; background-color: #ff4d8f; color: #ffffff !important; font-size: 15px; font-weight: 800; text-decoration: none !important; text-align: center; }
        .cta-btn:hover { background-color: #e03978; }
        @media only screen and (max-width: 620px) {
            .email-container { width: 100% !important; border-radius: 0 !important; }
            .hero-padding { padding: 26px 20px !important; }
            .content-padding { padding: 24px 20px !important; }
            .hero-h1 { font-size: 26px !important; }
            .table-responsive { width: 100% !important; display: block !important; overflow-x: auto !important; }
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Nunito:wght@700;800;900&display=swap" rel="stylesheet">
</head>
<body style="margin: 0; padding: 0; background-color: #fffbf5; color: #0d0020; font-family: 'DM Sans', Arial, sans-serif;">
    <div style="display: none; max-height: 0; overflow: hidden; opacity: 0; font-size: 1px; line-height: 1px; color: #fffbf5;">
        @yield('preheader', 'A happier, healthier journey starts here with NutriBuddy.')&#8203;&nbsp;&#8203;&nbsp;
    </div>
    
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #fffbf5;">
        <tr>
            <td align="center" style="padding: 24px 12px;">
                <!-- MAIN CARD -->
                <table role="presentation" class="email-container" width="600" cellspacing="0" cellpadding="0" border="0" style="width: 600px; max-width: 600px; background-color: #ffffff; border: 1px solid #f4e7ed; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 16px rgba(13,0,32,0.04);">
                    
                    <!-- TOP ACCENT GRADIENT -->
                    <tr>
                        <td height="7" style="height: 7px; background-color: #ff4d8f; background-image: linear-gradient(90deg, #ff4d8f, #7c3aed, #00bfff, #00d68f); font-size: 0; line-height: 0;">&nbsp;</td>
                    </tr>
                    
                    <!-- HEADER / LOGO -->
                    <tr>
                        <td align="center" style="padding: 20px 24px 14px; background-color: #ffffff;">
                            <a href="{{ url('/') }}" style="text-decoration: none;" target="_blank">
                                <img src="{{ asset('img/logo.png') }}" width="190" alt="KD's NutriBuddy — Stronger. Smarter. Healthier." style="display: block; width: 190px; max-width: 100%; height: auto; border: 0; margin: 0 auto;">
                            </a>
                        </td>
                    </tr>

                    @hasSection('hero')
                        <!-- HERO BANNER -->
                        <tr>
                            <td class="hero-padding" style="padding: 32px 40px 28px; background-color: #fff5fa; text-align: center; border-bottom: 1px solid #fae6ef;">
                                @yield('hero')
                            </td>
                        </tr>
                    @endif

                    <!-- CONTENT BODY -->
                    <tr>
                        <td class="content-padding" style="padding: 32px 40px; color: #0d0020; font-size: 15px; line-height: 1.75; font-family: 'DM Sans', Arial, sans-serif;">
                            @yield('content')

                            <!-- SIGNOFF & PROMISE -->
                            @sectionMissing('no_signoff')
                                <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #f4e7ed;">
                                    <p style="margin: 0 0 16px; color: #555166; font-size: 14px; line-height: 1.7;">
                                        Here’s to raising <strong style="color: #0d0020;">stronger, smarter, healthier</strong> kids.<br>
                                        Warmly,<br>
                                        <strong style="color: #0d0020;">Your NutriBuddy Team</strong>
                                    </p>
                                    <div style="padding: 14px 18px; border-radius: 10px; background-color: #f4f0ff; color: #5b21b6; font-size: 13px; font-weight: 700; text-align: center;">
                                        ✨ Made with care for little ones and the people who love them.
                                    </div>
                                </div>
                            @endif
                        </td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td align="center" style="padding: 24px 24px; background-color: #fffbf5; color: #6b6b80; font-size: 12px; line-height: 1.7; border-top: 1px solid #f4e7ed;">
                            <p style="margin: 0 0 4px; color: #5b21b6; font-family: 'Nunito', Arial, sans-serif; font-size: 13px; font-weight: 800;">
                                KD's NutriBuddy
                            </p>
                            <p style="margin: 0 0 4px;">
                                Thoughtful pediatric nutrition & wellness for growing champions.
                            </p>
                            <p style="margin: 0 0 10px;">
                                Questions? Reply directly to this email or reach us at <a href="mailto:support@nutribuddy.com" style="color: #c0306f; text-decoration: underline;">support@nutribuddy.com</a>.
                            </p>
                            <p style="margin: 0; color: #8e8e9f; font-size: 11px;">
                                &copy; {{ date('Y') }} NutriBuddy. All rights reserved.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
