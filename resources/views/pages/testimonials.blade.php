@extends('layouts.main')

@section('title', 'Testimonials - NutriBuddy')

@push('styles')
    <style>
        .testimonials-hero {
            background:
                radial-gradient(circle at 12% 22%, rgba(255, 214, 0, .16), transparent 28%),
                radial-gradient(circle at 88% 16%, rgba(255, 77, 143, .2), transparent 34%),
                linear-gradient(135deg, #170032 0%, #321064 52%, #0d0028 100%);
            overflow: hidden;
            padding: 150px 5% 76px;
            position: relative;
        }

        .testimonials-hero::before,
        .testimonials-hero::after {
            border-radius: 999px;
            content: '';
            pointer-events: none;
            position: absolute;
        }

        .testimonials-hero::before {
            animation: blobMorph 11s ease-in-out infinite;
            background: radial-gradient(circle, rgba(255, 77, 143, .14), transparent 70%);
            height: 520px;
            right: -150px;
            top: -180px;
            width: 520px;
        }

        .testimonials-hero::after {
            animation: blobMorph 14s ease-in-out infinite reverse;
            background: radial-gradient(circle, rgba(0, 214, 143, .1), transparent 70%);
            bottom: -150px;
            height: 420px;
            left: -120px;
            width: 420px;
        }

        .testimonials-hero-inner {
            align-items: center;
            display: grid;
            gap: 44px;
            grid-template-columns: minmax(0, 1fr) minmax(320px, .78fr);
            margin: 0 auto;
            max-width: 1180px;
            position: relative;
            z-index: 2;
        }

        .testimonials-crumb {
            align-items: center;
            background: rgba(255, 255, 255, .08);
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 999px;
            color: rgba(255, 255, 255, .86);
            display: inline-flex;
            flex-wrap: wrap;
            font-family: 'Nunito', sans-serif;
            font-size: .9rem;
            font-weight: 800;
            gap: 12px;
            margin-bottom: 22px;
            padding: 8px 14px;
        }

        .testimonials-crumb a {
            color: #fff;
            text-decoration: none;
        }

        .testimonials-badge {
            background: var(--pkl);
            border-radius: 999px;
            box-shadow: 0 12px 30px rgba(255, 77, 143, .2);
            color: var(--pk);
            display: inline-flex;
            font-family: 'Nunito', sans-serif;
            font-size: .75rem;
            font-weight: 900;
            margin-bottom: 16px;
            padding: 8px 16px;
            text-transform: uppercase;
        }

        .testimonials-title {
            color: #fff;
            font-family: 'Fredoka One', cursive;
            font-size: clamp(2.15rem, 5vw, 4.1rem);
            letter-spacing: 0;
            line-height: 1.08;
            margin-bottom: 18px;
            max-width: 720px;
        }

        .testimonials-title span {
            color: var(--ye);
        }

        .testimonials-sub {
            color: rgba(255, 255, 255, .78);
            font-size: 1.04rem;
            line-height: 1.75;
            margin: 0;
            max-width: 650px;
        }

        .testimonials-actions {
            align-items: center;
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 28px;
        }

        .testimonials-btn,
        .testimonials-link {
            align-items: center;
            border-radius: 999px;
            display: inline-flex;
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            min-height: 48px;
            padding: 0 22px;
            text-decoration: none;
        }

        .testimonials-btn {
            background: linear-gradient(135deg, var(--pk), var(--pkd));
            box-shadow: 0 16px 34px rgba(255, 77, 143, .32);
            color: #fff;
        }

        .testimonials-link {
            border: 1px solid rgba(255, 255, 255, .18);
            color: #fff;
        }

        .testimonials-score-card {
            background: rgba(255, 255, 255, .96);
            border: 1px solid rgba(255, 255, 255, .5);
            border-radius: 30px;
            box-shadow: 0 30px 70px rgba(0, 0, 0, .24);
            overflow: hidden;
            padding: 28px;
            position: relative;
        }

        .testimonials-score-card::before {
            background: linear-gradient(135deg, rgba(255, 77, 143, .16), rgba(124, 58, 237, .12));
            border-radius: 999px;
            content: '';
            height: 150px;
            position: absolute;
            right: -54px;
            top: -54px;
            width: 150px;
        }

        .score-top {
            align-items: center;
            display: flex;
            gap: 18px;
            margin-bottom: 22px;
            position: relative;
        }

        .score-number {
            align-items: center;
            background: var(--pkl);
            border-radius: 22px;
            color: var(--pk);
            display: inline-flex;
            font-family: 'Fredoka One', cursive;
            font-size: 2rem;
            height: 76px;
            justify-content: center;
            width: 76px;
        }

        .score-copy strong {
            color: var(--dk);
            display: block;
            font-family: 'Fredoka One', cursive;
            font-size: 1.25rem;
        }

        .score-copy span {
            color: #888;
            display: block;
            font-family: 'Nunito', sans-serif;
            font-size: .82rem;
            font-weight: 800;
            margin-top: 4px;
        }

        .score-stars {
            color: var(--ye);
            font-size: 1.05rem;
            letter-spacing: 2px;
            margin-top: 6px;
        }

        .score-bars {
            display: grid;
            gap: 10px;
            position: relative;
        }

        .score-row {
            align-items: center;
            color: #777;
            display: grid;
            font-family: 'Nunito', sans-serif;
            font-size: .78rem;
            font-weight: 900;
            gap: 10px;
            grid-template-columns: 36px 1fr 40px;
        }

        .score-track {
            background: #f2e8f0;
            border-radius: 999px;
            height: 9px;
            overflow: hidden;
        }

        .score-fill {
            background: linear-gradient(90deg, var(--pk), var(--ye));
            border-radius: inherit;
            height: 100%;
        }

        .testimonials-page {
            background:
                radial-gradient(circle at 0% 8%, rgba(255, 77, 143, .08), transparent 26%),
                radial-gradient(circle at 100% 42%, rgba(0, 214, 143, .08), transparent 28%),
                #fff;
            padding: 76px 5% 90px;
        }

        .testimonials-wrap {
            margin: 0 auto;
            max-width: 1180px;
        }

        .testimonials-section-head {
            align-items: end;
            display: flex;
            flex-wrap: wrap;
            gap: 22px;
            justify-content: space-between;
            margin-bottom: 28px;
        }

        .section-kicker {
            background: var(--mnl);
            border-radius: 999px;
            color: #009463;
            display: inline-flex;
            font-family: 'Nunito', sans-serif;
            font-size: .75rem;
            font-weight: 900;
            margin-bottom: 12px;
            padding: 7px 15px;
            text-transform: uppercase;
        }

        .section-title {
            color: var(--dk);
            font-family: 'Fredoka One', cursive;
            font-size: clamp(1.9rem, 4vw, 3rem);
            line-height: 1.12;
            margin: 0;
        }

        .section-sub {
            color: #777;
            line-height: 1.7;
            margin: 10px 0 0;
            max-width: 620px;
        }

        .trust-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .trust-pill {
            background: #fff;
            border: 1px solid #f3e5ef;
            border-radius: 999px;
            box-shadow: 0 10px 26px rgba(13, 0, 40, .05);
            color: var(--dk);
            display: inline-flex;
            font-family: 'Nunito', sans-serif;
            font-size: .82rem;
            font-weight: 900;
            padding: 10px 15px;
        }

        .featured-story {
            align-items: stretch;
            background: #fff;
            border: 1px solid #f3e5ef;
            border-radius: 30px;
            box-shadow: 0 24px 64px rgba(13, 0, 40, .08);
            display: grid;
            gap: 0;
            grid-template-columns: minmax(0, .82fr) minmax(0, 1.18fr);
            margin-bottom: 30px;
            overflow: hidden;
        }

        .featured-media {
            background:
                radial-gradient(circle at 30% 18%, rgba(255, 255, 255, .5), transparent 28%),
                linear-gradient(135deg, var(--pk), var(--pu));
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 360px;
            padding: 28px;
        }

        .featured-avatar {
            align-items: center;
            background: rgba(255, 255, 255, .92);
            border-radius: 26px;
            color: var(--pk);
            display: inline-flex;
            font-size: 3rem;
            height: 112px;
            justify-content: center;
            width: 112px;
        }

        .featured-product {
            background: rgba(255, 255, 255, .92);
            border-radius: 20px;
            color: var(--dk);
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            padding: 16px;
            width: fit-content;
        }

        .featured-content {
            padding: clamp(28px, 4vw, 44px);
        }

        .featured-stars,
        .review-stars,
        .video-stars {
            color: var(--ye);
            letter-spacing: 2px;
        }

        .featured-quote {
            color: var(--dk);
            font-family: 'Fredoka One', cursive;
            font-size: clamp(1.55rem, 3vw, 2.3rem);
            line-height: 1.25;
            margin: 18px 0;
        }

        .featured-text {
            color: #777;
            font-size: 1rem;
            line-height: 1.75;
            margin-bottom: 24px;
        }

        .featured-author {
            align-items: center;
            display: flex;
            gap: 14px;
        }

        .author-mark {
            align-items: center;
            background: var(--pkl);
            border-radius: 16px;
            color: var(--pk);
            display: inline-flex;
            font-family: 'Fredoka One', cursive;
            height: 52px;
            justify-content: center;
            width: 52px;
        }

        .author-name {
            color: var(--dk);
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
        }

        .author-meta,
        .review-meta {
            color: #999;
            font-size: .82rem;
            margin-top: 2px;
        }

        .reviews-grid {
            display: grid;
            gap: 22px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .review-card {
            background: #fff;
            border: 1px solid #f3e5ef;
            border-radius: 24px;
            box-shadow: 0 18px 44px rgba(13, 0, 40, .06);
            display: flex;
            flex-direction: column;
            min-height: 100%;
            padding: 24px;
            transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
        }

        .review-card:hover {
            border-color: rgba(255, 77, 143, .28);
            box-shadow: 0 24px 58px rgba(255, 77, 143, .11);
            transform: translateY(-6px);
        }

        .review-tag {
            background: var(--pkl);
            border-radius: 999px;
            color: var(--pk);
            display: inline-flex;
            font-family: 'Nunito', sans-serif;
            font-size: .72rem;
            font-weight: 900;
            margin: 14px 0;
            padding: 6px 12px;
            width: fit-content;
        }

        .review-text {
            color: #68636d;
            flex: 1;
            line-height: 1.7;
            margin-bottom: 20px;
        }

        .review-author {
            align-items: center;
            border-top: 1px solid #f4e8f0;
            display: flex;
            gap: 12px;
            padding-top: 18px;
        }

        .review-avatar {
            align-items: center;
            border-radius: 16px;
            display: inline-flex;
            flex: 0 0 46px;
            font-family: 'Fredoka One', cursive;
            height: 46px;
            justify-content: center;
            width: 46px;
        }

        .review-name {
            color: var(--dk);
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
        }

        .video-review-section {
            margin-top: 72px;
        }

        .video-strip {
            display: grid;
            gap: 18px;
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .video-card {
            aspect-ratio: 9 / 14;
            border-radius: 28px;
            box-shadow: 0 18px 44px rgba(13, 0, 40, .1);
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
            padding: 18px;
            position: relative;
        }

        .video-card::before {
            background: linear-gradient(180deg, rgba(13, 0, 40, .12), rgba(13, 0, 40, .72));
            content: '';
            inset: 0;
            position: absolute;
        }

        .video-play {
            align-items: center;
            align-self: flex-end;
            background: rgba(255, 255, 255, .22);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, .28);
            border-radius: 50%;
            display: inline-flex;
            height: 44px;
            justify-content: center;
            position: relative;
            width: 44px;
            z-index: 1;
        }

        .video-info {
            position: relative;
            z-index: 1;
        }

        .video-name {
            font-family: 'Fredoka One', cursive;
            font-size: 1.05rem;
            margin-top: 8px;
        }

        .video-copy {
            color: rgba(255, 255, 255, .78);
            font-size: .84rem;
            line-height: 1.55;
            margin-top: 7px;
        }

        .testimonial-cta {
            align-items: center;
            background: linear-gradient(135deg, var(--dk), #321064);
            border-radius: 30px;
            color: #fff;
            display: flex;
            gap: 24px;
            justify-content: space-between;
            margin-top: 72px;
            overflow: hidden;
            padding: clamp(28px, 4vw, 44px);
            position: relative;
        }

        .testimonial-cta::after {
            background: radial-gradient(circle, rgba(255, 77, 143, .22), transparent 70%);
            content: '';
            height: 240px;
            position: absolute;
            right: -70px;
            top: -80px;
            width: 240px;
        }

        .testimonial-cta h2 {
            color: #fff;
            font-family: 'Fredoka One', cursive;
            font-size: clamp(1.7rem, 4vw, 2.5rem);
            line-height: 1.15;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }

        .testimonial-cta p {
            color: rgba(255, 255, 255, .74);
            line-height: 1.7;
            margin: 0;
            max-width: 640px;
            position: relative;
            z-index: 1;
        }

        .testimonial-cta .testimonials-btn {
            flex: 0 0 auto;
            position: relative;
            z-index: 1;
        }

        @media (max-width: 1040px) {
            .testimonials-hero-inner,
            .featured-story {
                grid-template-columns: 1fr;
            }

            .reviews-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .video-strip {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 720px) {
            .testimonials-hero {
                padding: 94px 5% 54px;
            }

            .testimonials-page {
                padding: 52px 16px 72px;
            }

            .testimonials-score-card,
            .featured-story,
            .testimonial-cta {
                border-radius: 24px;
            }

            .reviews-grid,
            .video-strip {
                grid-template-columns: 1fr;
            }

            .video-card {
                aspect-ratio: 4 / 5;
            }

            .testimonial-cta {
                align-items: flex-start;
                flex-direction: column;
            }

            .testimonial-cta .testimonials-btn,
            .testimonials-btn,
            .testimonials-link {
                justify-content: center;
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $reviews = [
            [
                'name' => 'Priya Sharma',
                'meta' => 'Mum of 2 · Delhi',
                'tag' => 'Immunity support',
                'avatar' => 'PS',
                'color' => '#FFE8F5',
                'text' => 'My 7-year-old was constantly falling sick. After adding GrowStrong to her routine, she has been more energetic and school mornings feel so much smoother.',
            ],
            [
                'name' => 'Rahul Mehta',
                'meta' => 'Dad of 1 · Mumbai',
                'tag' => 'Focus and learning',
                'avatar' => 'RM',
                'color' => '#E8F5FF',
                'text' => 'Brain Booster Gummies became our exam-season support. My son is more settled during study time, and he actually reminds me to give it to him.',
            ],
            [
                'name' => 'Dr. Anita Nair',
                'meta' => 'Pediatrician · Bengaluru',
                'tag' => 'Expert confidence',
                'avatar' => 'AN',
                'color' => '#EDE9FE',
                'text' => 'I like the transparency of the formulations. Parents need products that are easy to use, age-aware, and made with a clear nutritional purpose.',
            ],
            [
                'name' => 'Fatima Khan',
                'meta' => 'Mum of 1 · Hyderabad',
                'tag' => 'Better bedtime',
                'avatar' => 'FK',
                'color' => '#FFF4D6',
                'text' => 'Our bedtime routine used to be a battle. The calm routine with NutriBuddy made evenings feel softer and much more predictable for our family.',
            ],
            [
                'name' => 'Vikram Patel',
                'meta' => 'Dad of 2 · Ahmedabad',
                'tag' => 'Daily routine',
                'avatar' => 'VP',
                'color' => '#E7FFF5',
                'text' => 'Both my kids have different needs, but NutriBuddy made it simple to build a routine. The taste helps because there is no convincing needed.',
            ],
            [
                'name' => 'Sneha Joshi',
                'meta' => 'Mum of toddler · Pune',
                'tag' => 'Picky eater win',
                'avatar' => 'SJ',
                'color' => '#FFEAF0',
                'text' => 'My toddler is picky with almost everything, but these gummies are the one wellness habit he accepts happily every morning.',
            ],
        ];

        $videos = [
            ['name' => 'Priya Sharma', 'copy' => 'School mornings feel easier now.', 'bg' => 'linear-gradient(160deg,#FF8FAB,#FF4D8F)'],
            ['name' => 'Rahul Mehta', 'copy' => 'A better study routine for exam weeks.', 'bg' => 'linear-gradient(160deg,#7BC8FF,#0099DD)'],
            ['name' => 'Dr. Anita Nair', 'copy' => 'Transparent formulas parents can understand.', 'bg' => 'linear-gradient(160deg,#B79FFF,#7C3AED)'],
            ['name' => 'Fatima Khan', 'copy' => 'Bedtime became calmer and more consistent.', 'bg' => 'linear-gradient(160deg,#6EF0C0,#00A87A)'],
        ];
    @endphp

    <section class="testimonials-hero">
        <div class="testimonials-hero-inner">
            <div>
                <div class="testimonials-crumb">
                    <a href="{{ route('home') }}">Home</a>
                    <span>/</span>
                    <span>Testimonials</span>
                </div>
                <span class="testimonials-badge">Parent Reviews</span>
                <h1 class="testimonials-title">Real stories from <span>NutriBuddy</span> families</h1>
                <p class="testimonials-sub">Parents use NutriBuddy for daily wellness routines, picky eating support, focus, immunity, and calmer family habits. Here is what they are saying.</p>
                <div class="testimonials-actions">
                    <a class="testimonials-btn" href="{{ route('product') }}">Shop Products</a>
                    <a class="testimonials-link" href="{{ route('diet_chart') }}">Get Diet Chart</a>
                </div>
            </div>

            <div class="testimonials-score-card">
                <div class="score-top">
                    <div class="score-number">4.9</div>
                    <div class="score-copy">
                        <strong>Parent rated</strong>
                        <span>Based on 6,031 verified reviews</span>
                        <div class="score-stars">★★★★★</div>
                    </div>
                </div>
                <div class="score-bars">
                    <div class="score-row"><span>5 ★</span><div class="score-track"><div class="score-fill" style="width:88%"></div></div><span>88%</span></div>
                    <div class="score-row"><span>4 ★</span><div class="score-track"><div class="score-fill" style="width:8%"></div></div><span>8%</span></div>
                    <div class="score-row"><span>3 ★</span><div class="score-track"><div class="score-fill" style="width:2.5%"></div></div><span>2.5%</span></div>
                    <div class="score-row"><span>2 ★</span><div class="score-track"><div class="score-fill" style="width:1%"></div></div><span>1%</span></div>
                    <div class="score-row"><span>1 ★</span><div class="score-track"><div class="score-fill" style="width:.5%"></div></div><span>0.5%</span></div>
                </div>
            </div>
        </div>
    </section>

    <section class="testimonials-page">
        <div class="testimonials-wrap">
            <div class="testimonials-section-head">
                <div>
                    <span class="section-kicker">Family results</span>
                    <h2 class="section-title">Wellness routines parents can actually keep</h2>
                    <p class="section-sub">Clean, scannable stories from families using NutriBuddy as part of their child's everyday nutrition and wellness routine.</p>
                </div>
                <div class="trust-pills">
                    <span class="trust-pill">Verified parents</span>
                    <span class="trust-pill">Kid-approved taste</span>
                    <span class="trust-pill">Daily routine friendly</span>
                </div>
            </div>

            <div class="featured-story">
                <div class="featured-media">
                    <div class="featured-avatar">💬</div>
                    <div class="featured-product">Featured Story · GrowStrong Gummies</div>
                </div>
                <div class="featured-content">
                    <div class="featured-stars">★★★★★</div>
                    <div class="featured-quote">"It finally became a wellness habit my child looks forward to."</div>
                    <p class="featured-text">We tried so many routines before, but taste was always the blocker. NutriBuddy made it easy. My daughter enjoys it, and I feel better knowing we are supporting her daily nutrition in a simple, consistent way.</p>
                    <div class="featured-author">
                        <div class="author-mark">PS</div>
                        <div>
                            <div class="author-name">Priya Sharma</div>
                            <div class="author-meta">Mum of 2 · Delhi · Verified Purchase</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="reviews-grid">
                @foreach($reviews as $review)
                    <article class="review-card">
                        <div class="review-stars">★★★★★</div>
                        <span class="review-tag">{{ $review['tag'] }}</span>
                        <p class="review-text">"{{ $review['text'] }}"</p>
                        <div class="review-author">
                            <div class="review-avatar" style="background: {{ $review['color'] }}; color: var(--dk);">{{ $review['avatar'] }}</div>
                            <div>
                                <div class="review-name">{{ $review['name'] }}</div>
                                <div class="review-meta">{{ $review['meta'] }}</div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="video-review-section">
                <div class="testimonials-section-head">
                    <div>
                        <span class="section-kicker">Video reviews</span>
                        <h2 class="section-title">Short stories from real routines</h2>
                        <p class="section-sub">A cleaner video review layout that feels native to the page and works well across desktop and mobile.</p>
                    </div>
                </div>

                <div class="video-strip">
                    @foreach($videos as $video)
                        <article class="video-card" style="background: {{ $video['bg'] }};">
                            <div class="video-play">▶</div>
                            <div class="video-info">
                                <div class="video-stars">★★★★★</div>
                                <div class="video-name">{{ $video['name'] }}</div>
                                <div class="video-copy">{{ $video['copy'] }}</div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>

            <div class="testimonial-cta">
                <div>
                    <h2>Ready to build your child's daily wellness routine?</h2>
                    <p>Explore gummies by goal, compare formulas, or start with a personalized diet chart to understand what your child needs most.</p>
                </div>
                <a class="testimonials-btn" href="{{ route('product') }}">Explore Products</a>
            </div>
        </div>
    </section>
@endsection
