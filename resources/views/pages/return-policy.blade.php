@extends('layouts.main')
@section('title', 'Returns & Refunds — NutriBuddy Kids')

@section('content')
    <section class="product-listing-hero">
        <div class="product-listing-hero-inner">
            <div class="product-listing-breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span>/</span>
                <span>Returns &amp; Refunds</span>
            </div>
            <span class="product-listing-hero-badge">Legal · NutriBuddy Kids</span>
            <h1 class="product-listing-hero-title">Returns &amp; Refunds</h1>
            <p class="product-listing-hero-sub">Please read our refund and return policy carefully to understand eligibility,
                timelines, and how to request support.</p>
        </div>
    </section>

    <section class="return-text-wrap">
        <div class="return-block">
            <h2>Returns &amp; Refunds</h2>

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
