@extends('layouts.main')
@section('title', 'Privacy Policy — NutriBuddy Kids')

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

        .privacy-block p {
            font-family: 'DM Sans', sans-serif;
            color: #666;
            font-size: 1rem;
            line-height: 1.8;
            margin-bottom: 18px;
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

        .privacy-block p:last-child {
            margin-bottom: 0;
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
    <div class="page-hero">
        <span class="hero-eyebrow">Legal · NutriBuddy Kids</span>
        <h1 class="hero-title">Privacy &amp; <span>Policy</span></h1>
        <p class="hero-subtitle">Please read this policy carefully to understand how we collect, use, and protect your
            personal information.</p>
        <div class="hero-meta">
            <div class="meta-pill">Last Updated: June 1, 2025</div>
            <div class="meta-pill">NutriBuddy Kids Pvt. Ltd.</div>
            <div class="meta-pill">Governed by Indian Law</div>
        </div>
    </div>

    <section class="privacy-text-wrap">
        <div class="privacy-block">
            <h2>Privacy Policy</h2>

            <p>We collect the email addresses of those who communicate with us via email, aggregate information on what
                pages users access or visit, and information volunteered by users (such as survey information and/or
                site registrations). The information we collect is used to improve the content on our website and the
                quality of our service. We do not share or sell your information to other organizations for commercial
                purposes, except to provide products or services you have requested, when we have your permission, or
                under legal requirements.</p>

            <p>We may transfer information about you if NutriBuddy is acquired by or merged with another company. In
                this event, NutriBuddy will notify you before information about you is transferred and becomes subject
                to a different privacy policy.</p>

            <h3>Information Gathering and Usage</h3>

            <p>When you register for NutriBuddy, we may ask for information such as your name, company name, email
                address, billing address, and payment details. Members who sign up for a free account are not required
                to enter payment details.</p>

            <p>NutriBuddy uses collected information for the following general purposes: products and services
                provision, billing, identification and authentication, service improvement, contact, and research.</p>
        </div>
    </section>
@endsection
