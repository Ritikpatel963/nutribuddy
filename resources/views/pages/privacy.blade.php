@extends('layouts.main')
@section('title', 'Privacy Policy — NutriBuddy Kids')

@section('content')
    <section class="product-listing-hero">
        <div class="product-listing-hero-inner">
            <div class="product-listing-breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span>/</span>
                <span>Privacy Policy</span>
            </div>
            <span class="product-listing-hero-badge">Legal · NutriBuddy Kids</span>
            <h1 class="product-listing-hero-title">Privacy Policy</h1>
            <p class="product-listing-hero-sub">Please read this policy carefully to understand how we collect, use, and protect your
                personal information.</p>
        </div>
    </section>
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
