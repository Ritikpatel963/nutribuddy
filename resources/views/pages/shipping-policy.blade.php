@extends('layouts.main')
@section('title', 'Shipping Policy — NutriBuddy Kids')

@push('styles')
    <style>
        .shipping-text-wrap {
            max-width: 1100px;
            margin: 0 auto;
            padding: 48px 5% 80px;
        }

        .shipping-block {
            background: linear-gradient(145deg, #fff, #fff7fc);
            border: 2px solid var(--pkl);
            border-radius: 24px;
            box-shadow: 0 10px 28px rgba(26, 10, 62, .08);
            padding: 42px 44px;
        }

        .shipping-block h2 {
            font-family: 'Nunito', sans-serif;
            font-size: clamp(2rem, 3.5vw, 2.9rem);
            font-weight: 700;
            color: var(--dk);
            line-height: 1.15;
            margin-bottom: 20px;
        }

        .shipping-block h3 {
            font-family: 'Fredoka One', cursive;
            font-size: clamp(1.2rem, 2.2vw, 1.6rem);
            color: var(--pu);
            font-weight: 400;
            margin: 34px 0 14px;
        }

        .shipping-block p,
        .shipping-block li {
            font-family: 'DM Sans', sans-serif;
            color: #666;
            font-size: 1rem;
            line-height: 1.8;
        }

        .shipping-block p {
            margin-bottom: 18px;
        }

        .shipping-block ul {
            margin: 0 0 18px 20px;
        }

        @media (max-width: 640px) {
            .shipping-block {
                padding: 28px 22px;
            }

            .shipping-text-wrap {
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
                <span>Shipping Policy</span>
            </div>
            <span class="product-listing-hero-badge">Delivery · NutriBuddy Kids</span>
            <h1 class="product-listing-hero-title">Shipping Policy</h1>
            <p class="product-listing-hero-sub">Learn how NutriBuddy ships orders, estimated delivery timelines, and what to do if a package is delayed.</p>
        </div>
    </section>

    <section class="shipping-text-wrap">
        <div class="shipping-block">
            <h2>Shipping Policy</h2>

            <p>NutriBuddy ships orders across India through trusted delivery partners. Once your order is confirmed, we process it as quickly as possible and share tracking details when available.</p>

            <h3>Order Processing</h3>
            <p>Orders are usually processed within 1-2 business days after successful payment confirmation. Orders placed on weekends or public holidays may be processed on the next business day.</p>

            <h3>Delivery Timelines</h3>
            <ul>
                <li>Metro cities: usually 3-5 business days.</li>
                <li>Other cities and towns: usually 5-8 business days.</li>
                <li>Remote locations may take longer depending on courier service availability.</li>
            </ul>

            <h3>Shipping Charges</h3>
            <p>Shipping charges, if applicable, are shown during checkout before you place the order. Some offers may include free shipping based on order value or promotion rules.</p>

            <h3>Delays or Issues</h3>
            <p>If your order is delayed, damaged in transit, or tracking is not updating, please contact our support team with your order details so we can help resolve it.</p>
        </div>
    </section>
@endsection
