@extends('layouts.main')
@section('title', 'Cookie Policy - NutriBuddy Kids')

@push('styles')
    <style>
        .privacy-text-wrap {
            max-width: 1100px;
            margin: 0 auto;
            padding: 48px 5% 80px;
        }

        .privacy-block {
            background: linear-gradient(145deg, #fff, #fff7fc);
            border: 2px solid var(--pkl);
            border-radius: 24px;
            box-shadow: 0 10px 28px rgba(26, 10, 62, .08);
            padding: 42px 44px;
        }

        .privacy-block h2 {
            font-family: 'Nunito', sans-serif;
            font-size: clamp(2rem, 3.5vw, 2.9rem);
            font-weight: 700;
            color: var(--dk);
            line-height: 1.15;
            margin-bottom: 20px;
        }

        .privacy-block h3 {
            font-family: 'Fredoka One', cursive;
            font-size: clamp(1.2rem, 2.2vw, 1.6rem);
            color: var(--pu);
            font-weight: 400;
            margin: 34px 0 14px;
        }

        .privacy-block p,
        .privacy-block li {
            font-family: 'DM Sans', sans-serif;
            color: #666;
            font-size: 1rem;
            line-height: 1.8;
        }

        .privacy-block p {
            margin-bottom: 18px;
        }

        .privacy-block ul {
            margin: 0 0 18px 20px;
        }

        .privacy-block a {
            color: var(--pk);
            font-weight: 700;
            text-decoration: none;
        }

        .privacy-block a:hover {
            color: var(--pkd);
            text-decoration: underline;
        }

        @media (max-width: 640px) {
            .privacy-block {
                padding: 28px 22px;
            }

            .privacy-text-wrap {
                padding: 34px 5% 56px;
            }
        }
    </style>
@endpush

@section('content')
    <section class="product-listing-hero">
        <div class="product-listing-hero-inner">
            <div class="product-listing-breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span>/</span>
                <span>Cookie Policy</span>
            </div>
            <span class="product-listing-hero-badge">Legal · NutriBuddy Kids</span>
            <h1 class="product-listing-hero-title">Cookie Policy</h1>
            <p class="product-listing-hero-sub">Learn how NutriBuddy uses cookies and similar technologies to keep the website useful,
                secure, and easy to shop.</p>
        </div>
    </section>

    <section class="privacy-text-wrap">
        <div class="privacy-block">
            <h2>Cookie Policy</h2>
            <p>NutriBuddy uses cookies and similar technologies to remember your preferences, maintain your cart and login session,
                understand how visitors use our website, and improve the shopping experience.</p>

            <h3>What Cookies We Use</h3>
            <ul>
                <li><strong>Essential cookies:</strong> required for security, checkout, account login, and cart functionality.</li>
                <li><strong>Preference cookies:</strong> help remember choices such as form details or browsing preferences.</li>
                <li><strong>Analytics cookies:</strong> help us understand page performance and improve our content and product pages.</li>
                <li><strong>Marketing cookies:</strong> may help us show relevant offers or measure campaign performance.</li>
            </ul>

            <h3>Managing Cookies</h3>
            <p>You can control or delete cookies through your browser settings. Blocking some cookies may affect features such as
                cart, checkout, login, or personalized recommendations.</p>

            <h3>Updates</h3>
            <p>We may update this Cookie Policy when our website features or technology partners change. For more details about how we
                protect personal information, please read our <a href="{{ route('privacy') }}">Privacy Policy</a>.</p>
        </div>
    </section>
@endsection
