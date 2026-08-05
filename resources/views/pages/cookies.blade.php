@extends('layouts.main')
@section('title', 'Cookie Policy - NutriBuddy Kids')

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
