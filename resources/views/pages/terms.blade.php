@extends('layouts.main')
@section('title', 'Terms & Conditions — NutriBuddy Kids')

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

        .terms-text-wrap {
            max-width: 1100px;
            margin: 0 auto;
            padding: 48px 5% 80px;
        }

        .terms-block {
            background: linear-gradient(145deg, #fff, #fff7fc);
            border: 2px solid var(--pkl);
            border-radius: 24px;
            box-shadow: 0 10px 28px rgba(26, 10, 62, .08);
            padding: 42px 44px;
        }

        .terms-block h2 {
            font-family: 'Nunito', sans-serif;
            font-size: clamp(2rem, 3.5vw, 2.9rem);
            font-weight: 700;
            color: var(--dk);
            line-height: 1.15;
            margin-bottom: 20px;
        }

        .terms-block h3 {
            font-family: 'Fredoka One', cursive;
            font-size: clamp(1.2rem, 2.2vw, 1.6rem);
            color: var(--pu);
            font-weight: 400;
            margin: 34px 0 14px;
        }

        .terms-block p {
            font-family: 'DM Sans', sans-serif;
            color: #666;
            font-size: 1rem;
            line-height: 1.8;
            margin-bottom: 18px;
        }

        .terms-block a {
            color: var(--pk);
            font-weight: 700;
            text-decoration: none;
        }

        .terms-block a:hover {
            color: var(--pkd);
            text-decoration: underline;
        }

        .terms-block p:last-child {
            margin-bottom: 0;
        }

        @media (max-width: 640px) {
            .terms-block {
                padding: 28px 22px;
            }

            .terms-text-wrap {
                padding: 34px 5% 56px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="page-hero">
        <span class="hero-eyebrow">Legal · NutriBuddy Kids</span>
        <h1 class="hero-title">Terms &amp; <span>Conditions</span></h1>
        <p class="hero-subtitle">Please read these terms carefully before using our website or purchasing our products. They
            govern your relationship with NutriBuddy.</p>
        <div class="hero-meta">
            <div class="meta-pill">Last Updated: June 1, 2025</div>
            <div class="meta-pill">NutriBuddy Kids Pvt. Ltd.</div>
            <div class="meta-pill">Governed by Indian Law</div>
        </div>
    </div>

    <section class="terms-text-wrap">
        <div class="terms-block">
            <h2>Terms &amp; Conditions</h2>

            <p>By using NutriBuddy's website, products, or services, you agree to comply with and be bound by these
                Terms &amp; Conditions. Please review them carefully before placing an order or using any of our
                services.</p>

            <p>These terms apply to all visitors, users, and customers. If you do not agree with any part of these
                terms, please do not use our website or services.</p>

            <h3>Use of Service</h3>

            <p>You agree to use NutriBuddy only for lawful purposes and in a way that does not infringe the rights of,
                restrict, or inhibit anyone else's use of the service.</p>

            <p>We reserve the right to update, modify, or discontinue any part of our services at any time without
                prior notice. Continued use of the service after changes are posted means you accept the updated
                terms.</p>

            <h3>Orders and Payments</h3>

            <p>All orders are subject to availability and acceptance. Prices are shown in INR and may change without
                prior notice. Payment must be completed through approved payment methods before order processing.</p>
        </div>
    </section>
@endsection
