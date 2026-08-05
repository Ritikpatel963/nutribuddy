@extends('layouts.main')

@section('title', 'Testimonials - NutriBuddy')

@section('content')
    @php
        $allReviews = \App\Models\ProductReview::with(['user', 'product'])->where('is_active', true)->latest()->get();
        
        // Prioritize a review that has a physical image file on disk
        $featuredReview = $allReviews->first(function ($review) {
            return $review->image_path && Storage::disk('public')->exists($review->image_path);
        });

        $textReviews = $allReviews->whereNull('video_path')->values();
        $videoReviews = $allReviews->whereNotNull('video_path')->values();
        
        if (!$featuredReview) {
            $featuredReview = $textReviews->first();
        }
        
        $gradients = [
            'linear-gradient(160deg,#FF8FAB,#FF4D8F)',
            'linear-gradient(160deg,#7BC8FF,#0099DD)',
            'linear-gradient(160deg,#B79FFF,#7C3AED)',
            'linear-gradient(160deg,#6EF0C0,#00A87A)',
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
                                $starCount = $allReviews->where('rating', $star)->count();
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

            @if($featuredReview)
            <div class="featured-story">
                <div class="featured-media" style="position: relative; overflow: hidden;">
                    @if($featuredReview->image_path && Storage::disk('public')->exists($featuredReview->image_path))
                        <img src="{{ asset('storage/' . $featuredReview->image_path) }}" alt="Featured Review" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; z-index: 0;">
                        <!-- Overlay for text readability -->
                        <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, transparent 60%); z-index: 1;"></div>
                    @else
                        <div class="featured-avatar" style="position: relative; z-index: 2;">💬</div>
                    @endif
                    <div class="featured-product" style="position: relative; z-index: 2; margin-top: auto;">Featured Story · {{ $featuredReview->product?->name ?? 'NutriBuddy' }}</div>
                </div>    
                
                <div class="featured-content">
                    <div class="featured-stars">
                        @for($i=0; $i<5; $i++) {{ $i < $featuredReview->rating ? '★' : '☆' }} @endfor
                    </div>
                    <div class="featured-quote">"{{ Str::limit($featuredReview->comment, 60) }}"</div>
                    <p class="featured-text">{{ $featuredReview->comment }}</p>
                    <div class="featured-author">
                        <div class="author-mark">{{ strtoupper(substr($featuredReview->user?->name ?? 'A', 0, 2)) }}</div>
                        <div>
                            <div class="author-name">{{ $featuredReview->user?->name ?? 'Anonymous Parent' }}</div>
                            <div class="author-meta">Verified Purchase</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="reviews-grid">
                @foreach($textReviews->reject(fn($r) => $featuredReview && $r->id === $featuredReview->id)->values() as $index => $review)
                    @php $bgColor = ['#FFE8F5', '#E8F5FF', '#EDE9FE', '#FFF4D6', '#E7FFF5', '#FFEAF0'][$index % 6]; @endphp
                    <article class="review-card">
                        <div class="review-stars">
                            @for($i=0; $i<5; $i++) {{ $i < $review->rating ? '★' : '☆' }} @endfor
                        </div>
                        <span class="review-tag">Parent Review</span>
                        @if($review->image_path && Storage::disk('public')->exists($review->image_path))
                            <div style="margin: 15px 0; border-radius: 8px; overflow: hidden; max-height: 200px;">
                                <img src="{{ asset('storage/' . $review->image_path) }}" alt="Review Image" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        @endif
                        <p class="review-text">"{{ $review->comment }}"</p>
                        <div class="review-author">
                            <div class="review-avatar" style="background: {{ $bgColor }}; color: var(--dk);">
                                {{ strtoupper(substr($review->user?->name ?? 'A', 0, 2)) }}
                            </div>
                            <div>
                                <div class="review-name">{{ $review->user?->name ?? 'Anonymous Parent' }}</div>
                                <div class="review-meta">Verified Purchase</div>
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
                    @foreach($videoReviews as $index => $video)
                        @php $bg = $gradients[$index % count($gradients)]; @endphp
                        <article class="video-card" style="background: {{ $bg }}; position: relative; overflow: hidden; cursor: pointer;" onclick="toggleTestimonialVideo(this, event)">
                            <video loop playsinline preload="metadata" style="position: absolute; top:0; left:0; width:100%; height:100%; object-fit:cover; opacity: 0.6; transition: opacity 0.3s;">
                                @if($video->video_path && Storage::disk('public')->exists($video->video_path))
                                    <source src="{{ asset('storage/' . $video->video_path) }}" type="video/mp4">
                                @endif
                            </video>
                            <div style="display: flex; justify-content: space-between; position: relative; z-index: 2; align-items: center;">
                                <div class="video-mute-toggle" style="background: rgba(255,255,255,0.22); border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(8px); cursor: pointer;" onclick="toggleMute(this, event)">🔇</div>
                                <div class="video-play">▶</div>
                            </div>
                            <div class="video-info" style="position: relative; z-index: 2; pointer-events: none;">
                                <div class="video-stars">
                                    @for($i=0; $i<5; $i++) {{ $i < $video->rating ? '★' : '☆' }} @endfor
                                </div>
                                <div class="video-name">{{ $video->user?->name ?? 'Anonymous Parent' }}</div>
                                <div class="video-copy">{{ Str::limit($video->comment, 50) }}</div>
                            </div>
                        </article>
                    @endforeach
                </div>
                <script>
                    function toggleTestimonialVideo(card, e) {
                        const video = card.querySelector('video');
                        const playBtn = card.querySelector('.video-play');
                        const muteBtn = card.querySelector('.video-mute-toggle');
                        if (!video) return;
                        
                        if (video.paused) {
                            // Pause all others
                            document.querySelectorAll('.video-card video').forEach(v => { 
                                v.pause(); 
                                v.muted = true; 
                                v.style.opacity = '0.6';
                                v.parentElement.querySelector('.video-play').innerText = '▶';
                                v.parentElement.querySelector('.video-mute-toggle').innerText = '🔇';
                            });
                            
                            video.muted = false; // start with sound
                            muteBtn.innerText = '🔊';
                            video.play();
                            playBtn.innerText = 'II';
                            video.style.opacity = '1';
                        } else {
                            video.pause();
                            video.muted = true;
                            muteBtn.innerText = '🔇';
                            playBtn.innerText = '▶';
                            video.style.opacity = '0.6';
                        }
                    }

                    function toggleMute(btn, e) {
                        e.stopPropagation(); // prevent clicking the card
                        const card = btn.closest('.video-card');
                        const video = card.querySelector('video');
                        if (!video) return;

                        if (video.muted) {
                            video.muted = false;
                            btn.innerText = '🔊';
                        } else {
                            video.muted = true;
                            btn.innerText = '🔇';
                        }
                    }
                </script>
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
