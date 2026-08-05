@extends('layouts.main')

@section('title', 'Testimonials - NutriBuddy')

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
                @php
                    $allReviews = \App\Models\ProductReview::where('is_active', true);
                    $totalReviews = $allReviews->count();
                @endphp
                @if($totalReviews > 0)
                    @php
                        $displayAvg = number_format($allReviews->avg('rating'), 1);
                    @endphp
                    <div class="score-top">
                        <div class="score-number">{{ $displayAvg }}</div>
                        <div class="score-copy">
                            <strong>Parent rated</strong>
                            <span>Based on {{ number_format($totalReviews) }} verified reviews</span>
                            <div class="score-stars">
                                @for($i=0; $i<5; $i++)
                                    {{ $i < round((float)$displayAvg) ? '★' : '☆' }}
                                @endfor
                            </div>
                        </div>
                    </div>
                    <div class="score-bars">
                        @foreach([5, 4, 3, 2, 1] as $star)
                            @php
                                $starCount = $allReviews->clone()->where('rating', $star)->count();
                                $pct = round(($starCount / $totalReviews) * 100, 1);
                            @endphp
                            <div class="score-row"><span>{{ $star }} ★</span><div class="score-track"><div class="score-fill" style="width:{{ $pct }}%"></div></div><span>{{ $pct }}%</span></div>
                        @endforeach
                    </div>
                @else
                    <div class="score-top" style="display:flex; justify-content:center; align-items:center; min-height: 120px;">
                        <div class="score-copy" style="text-align:center;">
                            <strong>No customer reviews yet.</strong>
                            <span>Check back later as our families share their stories!</span>
                        </div>
                    </div>
                @endif
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
                    <span class="testimonials-trust-pill">Verified parents</span>
                    <span class="testimonials-trust-pill">Kid-approved taste</span>
                    <span class="testimonials-trust-pill">Daily routine friendly</span>
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
