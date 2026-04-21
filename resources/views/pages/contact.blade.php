@extends('layouts.main')
@section('title', 'Contact Us – NutriBuddy')

@push('styles')
    <style>
        /* Contact Page Specific Styles */
        .contact-hero {
            padding: 180px 5% 80px;
            text-align: center;
            position: relative;
            overflow: hidden;
            background-color: var(--lbg);
        }

        .contact-hero .blob {
            position: absolute;
            z-index: 0;
            opacity: 0.6;
            filter: blur(100px);
        }

        .contact-hero .blob-1 {
            width: 400px;
            height: 400px;
            background: var(--pk);
            top: -100px;
            left: -100px;
        }

        .contact-hero .blob-2 {
            width: 300px;
            height: 300px;
            background: #0000ff;
            bottom: -100px;
            right: -100px;
        }

        .contact-content-wrapper {
            position: relative;
            z-index: 2;
        }

        .contact-title {
            font-family: 'Fredoka One', cursive;
            font-size: clamp(2.5rem, 5vw, 4rem);
            color: var(--dk);
            margin-bottom: 20px;
            line-height: 1.1;
        }

        .contact-subtitle {
            font-size: clamp(1rem, 2vw, 1.2rem);
            color: #555;
            max-width: 600px;
            margin: 0 auto;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 40px;
            max-width: 1200px;
            margin: -40px auto 80px;
            padding: 0 5%;
            position: relative;
            z-index: 10;
        }

        @media (max-width: 800px) {
            .contact-grid {
                grid-template-columns: 1fr;
                margin-top: 20px;
            }
        }

        /* Contact Info Cards */
        .contact-info-col {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .info-card {
            background: white;
            padding: 30px;
            border-radius: 24px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
            border: 2px solid transparent;
            transition: 0.3s ease;
            display: flex;
            align-items: flex-start;
            gap: 20px;
        }

        .info-card:hover {
            transform: translateY(-5px);
        }

        .info-card.card-email:hover {
            border-color: var(--pk);
            box-shadow: 0 10px 40px rgba(255, 107, 138, 0.15);
        }

        .info-card.card-phone:hover {
            border-color: var(--ye);
            box-shadow: 0 10px 40px rgba(255, 214, 0, 0.15);
        }

        .info-card.card-location:hover {
            border-color: var(--sk);
            box-shadow: 0 10px 40px rgba(0, 191, 255, 0.15);
        }

        .info-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            flex-shrink: 0;
        }

        .card-email .info-icon {
            background: rgba(255, 107, 138, 0.1);
            color: var(--pk);
        }

        .card-phone .info-icon {
            background: rgba(255, 214, 0, 0.15);
        }

        .card-location .info-icon {
            background: rgba(0, 191, 255, 0.1);
            color: var(--sk);
        }

        .info-details h3 {
            font-family: 'Fredoka One', cursive;
            font-size: 1.3rem;
            color: var(--dk);
            margin-bottom: 8px;
        }

        .info-details p,
        .info-details a {
            color: #666;
            font-size: 1.05rem;
            line-height: 1.5;
            text-decoration: none;
            transition: color 0.3s;
        }

        .info-details a:hover {
            color: var(--pk);
        }

        /* Contact Form */
        .contact-form-col {
            background: white;
            padding: 40px;
            border-radius: 30px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
            position: relative;
        }

        .contact-form-col h3 {
            font-family: 'Fredoka One', cursive;
            font-size: 1.8rem;
            color: var(--dk);
            margin-bottom: 10px;
        }

        .contact-form-col p {
            color: #666;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        @media (max-width: 600px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        .form-label {
            display: block;
            font-family: 'Nunito', sans-serif;
            font-weight: 700;
            color: var(--dk);
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .form-control {
            width: 100%;
            padding: 16px 20px;
            border-radius: 16px;
            border: 2px solid #eee;
            background: #fafafa;
            font-family: 'DM Sans', sans-serif;
            font-size: 1rem;
            color: var(--dk);
            transition: all 0.3s ease;
            outline: none;
        }

        .form-control:focus {
            border-color: var(--pk);
            background: white;
            box-shadow: 0 0 0 4px rgba(255, 107, 138, 0.1);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 150px;
        }

        .btn-submit {
            width: 100%;
            background: var(--btn);
            color: white;
            border: none;
            padding: 18px;
            border-radius: 50px;
            font-family: 'Fredoka One', cursive;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 10px 20px rgba(255, 107, 138, 0.3);
        }

        .btn-submit:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(255, 107, 138, 0.4);
            background: #ff4d64;
            /* Slightly darker pink */
        }
    </style>
@endpush

@section('content')
    <!-- CONTACT HERO -->
    <section class="contact-hero">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="contact-content-wrapper">
            <div class="slide-badge badge-pk" style="margin: 0 auto 20px; display: inline-block;">👋 Hello There</div>
            <h1 class="contact-title">Let's Get In <span class="acc" style="color:var(--pk);">Touch</span></h1>
            <p class="contact-subtitle">Have a question about our gummies, need help with your child's personalized plan, or
                just want to say hi? We'd love to hear from you!</p>
        </div>
    </section>
    <!-- CONTACT GRID -->
    <div class="contact-grid">

        <!-- Info Cards -->
        <div class="contact-info-col">
            <div class="info-card card-email">
                <div class="info-icon">💌</div>
                <div class="info-details">
                    <h3>Email Us</h3>
                    <p>We're here to help.</p>
                    <a href="mailto:hello@nutribuddy.in">hello@nutribuddy.in</a>
                </div>
            </div>

            <div class="info-card card-phone">
                <div class="info-icon">📞</div>
                <div class="info-details">
                    <h3>Call Us</h3>
                    <p>Mon - Fri, 9am - 6pm (IST)</p>
                    <a href="tel:18001234567">1800-123-4567</a>
                </div>
            </div>

            <div class="info-card card-location">
                <div class="info-icon">🗺️</div>
                <div class="info-details">
                    <h3>Visit Us</h3>
                    <p>42, Wellness Tower, Bengaluru – 560001, Karnataka, India</p>
                </div>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="contact-form-col">
            <h3>Send us a Message 🚀</h3>
            <p>Fill out the form below and our wellness team will get back to you within 24 hours.</p>

            <form action="#" method="POST"
                onsubmit="event.preventDefault(); alert('Thank you for contacting us! We will get back to you soon.');">

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="firstName">First Name</label>
                        <input type="text" id="firstName" class="form-control" placeholder="e.g. Priya" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="lastName">Last Name</label>
                        <input type="text" id="lastName" class="form-control" placeholder="e.g. Sharma">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="email">Email Address</label>
                        <input type="email" id="email" class="form-control" placeholder="your@email.com" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="phone">Phone Number</label>
                        <input type="tel" id="phone" class="form-control" placeholder="+91 98765 43210">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="subject">Subject</label>
                    <select id="subject" class="form-control" required>
                        <option value="" disabled selected>Select a topic...</option>
                        <option value="order">Where is my order?</option>
                        <option value="product">Question about a product</option>
                        <option value="diet">Help with Diet Chart</option>
                        <option value="wholesale">Wholesale / Partnership</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="message">Your Message</label>
                    <textarea id="message" class="form-control" placeholder="How can we help you and your little one today?" required></textarea>
                </div>

                <button type="submit" class="btn-submit">Send Message ✨</button>
            </form>
        </div>

    </div> <!-- /contact-grid -->


@endsection
