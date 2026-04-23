@extends('layouts.main')
@section('title', 'Refunds & Return Policy — NutriBuddy Kids')

@push('styles')
    <style>
        .page-hero {
            background: linear-gradient(135deg, var(--dk) 0%, #260050 50%, #0d0030 100%);
            padding: 130px 5% 56px;
            position: relative;
            overflow: hidden;
            text-align: center;
        }

        .page-hero::before {
            content: '';
            position: absolute;
            top: -80px;
            right: -80px;
            width: 360px;
            height: 360px;
            background: radial-gradient(circle, rgba(255, 77, 143, .12), transparent 65%);
            border-radius: 50%;
            pointer-events: none;
        }

        .page-hero::after {
            content: '';
            position: absolute;
            bottom: -60px;
            left: -60px;
            width: 280px;
            height: 280px;
            background: radial-gradient(circle, rgba(124, 58, 237, .1), transparent 65%);
            border-radius: 50%;
            pointer-events: none;
        }

        .hero-eyebrow {
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: .72rem;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            color: var(--pk);
            margin-bottom: 12px;
            display: block;
            position: relative;
            z-index: 2;
        }

        .hero-title {
            font-family: 'Fredoka One', cursive;
            font-size: clamp(2rem, 4vw, 3rem);
            color: #fff;
            margin-bottom: 14px;
            position: relative;
            z-index: 2;
            line-height: 1.1;
        }

        .hero-title span {
            color: var(--ye);
        }

        .hero-subtitle {
            font-size: .95rem;
            color: rgba(255, 255, 255, .55);
            max-width: 720px;
            margin: 0 auto 24px;
            line-height: 1.7;
            position: relative;
            z-index: 2;
        }

        .hero-meta {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
            position: relative;
            z-index: 2;
        }

        .meta-pill {
            background: rgba(255, 255, 255, .08);
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 50px;
            padding: 7px 16px;
            font-family: 'Nunito', sans-serif;
            font-weight: 800;
            font-size: .75rem;
            color: rgba(255, 255, 255, .75);
        }

        .return-text-wrap {
            max-width: 1100px;
            margin: 0 auto;
            padding: 48px 5% 80px;
        }

        .return-block {
            background: linear-gradient(145deg, #fff, #fff7fc);
            border: 2px solid var(--pkl);
            border-radius: 24px;
            box-shadow: 0 10px 28px rgba(26, 10, 62, .08);
            padding: 42px 44px;
        }

        .return-block h2 {
            font-family: 'Nunito', sans-serif;
            font-size: clamp(2rem, 3.5vw, 2.9rem);
            font-weight: 700;
            color: var(--dk);
            line-height: 1.15;
            margin-bottom: 20px;
        }

        .return-block h3 {
            font-family: 'Fredoka One', cursive;
            font-size: clamp(1.2rem, 2.2vw, 1.6rem);
            color: var(--pu);
            font-weight: 400;
            margin: 34px 0 14px;
        }

        .return-block p {
            font-family: 'DM Sans', sans-serif;
            color: #666;
            font-size: 1rem;
            line-height: 1.8;
            margin-bottom: 18px;
        }

        .return-block a {
            color: var(--pk);
            font-weight: 700;
            text-decoration: none;
        }

        .return-block a:hover {
            color: var(--pkd);
            text-decoration: underline;
        }

        .return-block p:last-child {
            margin-bottom: 0;
        }

        @media (max-width: 640px) {
            .return-block {
                padding: 28px 22px;
            }

            .return-text-wrap {
                padding: 34px 5% 56px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="page-hero">
        <span class="hero-eyebrow">Legal · NutriBuddy Kids</span>
        <h1 class="hero-title">Refunds &amp; <span>Return Policy</span></h1>
        <p class="hero-subtitle">Please read our refund and return policy carefully to understand eligibility,
            timelines, and how to request support.</p>
        <div class="hero-meta">
            <div class="meta-pill">Last Updated: June 1, 2025</div>
            <div class="meta-pill">NutriBuddy Kids Pvt. Ltd.</div>
            <div class="meta-pill">Governed by Indian Law</div>
        </div>
    </div>

    <section class="return-text-wrap">
        <div class="return-block">
            <h2>Refunds &amp; Return Policy</h2>

            <p>At NutriBuddy, your satisfaction is important to us. If you are not satisfied with a product, you may
                request a return or refund as per the terms below. This policy applies to purchases made through our
                official website and authorized channels.</p>

            <p>To start a return request, please contact our support team with your order number and reason for return.
                We may request basic details or photos to process your request quickly.</p>

            <h3>Eligibility</h3>

            <p>Returns are generally accepted for damaged, incorrect, or defective products reported within the allowed
                timeframe. Opened or used consumable products may not be eligible unless there is a quality issue.</p>

            <h3>Refund Timeline</h3>

            <p>Once your return is approved and verified, refunds are processed to the original payment method.
                Depending on your bank or payment provider, the amount may reflect within 5 to 10 business days.</p>

            <p>If you need help at any stage, contact us at <a href="mailto:hello@nutribuddy.in">hello@nutribuddy.in</a>
                or call <a href="tel:18001234567">1800-123-4567</a>.</p>
        </div>
    </section>
@endsection
