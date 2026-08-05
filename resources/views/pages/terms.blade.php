@extends('layouts.main')
@section('title', 'Terms of Service — NutriBuddy Kids')

@section('content')
    <section class="product-listing-hero">
        <div class="product-listing-hero-inner">
            <div class="product-listing-breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span>/</span>
                <span>Terms of Service</span>
            </div>
            <span class="product-listing-hero-badge">Legal · NutriBuddy Kids</span>
            <h1 class="product-listing-hero-title">Terms of Service</h1>
            <p class="product-listing-hero-sub">Please read these terms carefully before using our website or purchasing our products. They
                govern your relationship with NutriBuddy.</p>
        </div>
    </section>

    <section class="terms-text-wrap">
        <div class="terms-block">
            <h2>Terms of Service</h2>

            <p>By using NutriBuddy's website, products, or services, you agree to comply with and be bound by these
                Terms of Service. Please review them carefully before placing an order or using any of our
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
