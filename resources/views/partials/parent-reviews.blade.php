@php
    if (isset($product)) {
        // Product specific
        $allActiveReviews = $product->reviews->where('is_active', true);
    } else {
        // Global / Homepage
        $allActiveReviews = \App\Models\ProductReview::where('is_active', true)->get();
    }
    
    $hasDynamicProductReviews = $allActiveReviews->isNotEmpty();
    $gradients = [
        'linear-gradient(160deg,#FF8FAB,#FF4D8F)',
        'linear-gradient(160deg,#7BC8FF,#0099DD)',
        'linear-gradient(160deg,#B79FFF,#7C3AED)',
        'linear-gradient(160deg,#FFD97D,#FF9900)',
        'linear-gradient(160deg,#6EF0C0,#00A87A)',
        'linear-gradient(160deg,#FFB3C6,#FF6B8A)',
    ];

    if ($hasDynamicProductReviews) {
        $videoReviews = $allActiveReviews->whereNotNull('video_path')->values();
        $textReviews = $allActiveReviews->whereNull('video_path')->values();
    } else {
        $videoReviews = collect();
        $textReviews = collect();
    }
@endphp
<style>
.wreviews-viewport {
    overflow: hidden;
    width: 100%;
    padding: 10px 0;
}
.wreviews-track {
    display: flex;
    gap: 22px;
    transition: transform 0.4s cubic-bezier(0.25, 1, 0.5, 1);
    width: 100%;
}
.wreviews-track .wrev {
    flex: 0 0 calc((100% - 44px) / 3);
    margin-top: 0 !important;
}
@media (max-width: 991px) {
    .wreviews-track .wrev {
        flex: 0 0 calc((100% - 22px) / 2);
    }
}
@media (max-width: 576px) {
    .wreviews-track .wrev {
        flex: 0 0 100%;
    }
}
.wreviews-dots .wdot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #e2e8f0;
    border: none;
    cursor: pointer;
    transition: all 0.3s;
    padding: 0;
}
.wreviews-dots .wdot.active {
    background: var(--pk);
    transform: scale(1.2);
}
</style>
    <!-- ══════════════════════════════════════════
                   TESTIMONIALS
              ══════════════════════════════════════════ -->
              <section class="testi-section-new reveal ">
    <section class="testi-section reveal" id="reviews">
        <span class="sec-eye">Parent Reviews</span>
        <h2 class="sec-title" style="text-align:center">10,000+ Happy Families </h2>

        @if($hasDynamicProductReviews)
            @php
                $totalReviews = $allActiveReviews->count();
                $displayAvg = number_format($allActiveReviews->avg('rating'), 1);
            @endphp
            <div class="rev-summary reveal">
                <div class="rev-big">
                    <div class="rev-big-n">{{ $displayAvg }}</div>
                    <div class="rev-big-stars">
                        @for($i=0; $i<5; $i++)
                            {{ $i < round((float)$displayAvg) ? '★' : '☆' }}
                        @endfor
                    </div>
                    <div class="rev-big-l">Based on {{ number_format($totalReviews) }} reviews</div>
                </div>
                <div class="rev-bars">
                    @foreach([5, 4, 3, 2, 1] as $star)
                        @php
                            $starCount = $allActiveReviews->where('rating', $star)->count();
                            $pct = $totalReviews > 0 ? round(($starCount / $totalReviews) * 100, 1) : 0;
                        @endphp
                        <div class="rbar-row">{{ $star }} ★ 
                            <div class="rbar-track">
                                <div class="rbar-fill" style="width:{{ $pct }}%"></div>
                            </div> {{ $pct }}%
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="rev-summary reveal" style="display:flex; justify-content:center; align-items:center; min-height: 150px; background: #fff; border-radius: 16px; border: 1px dashed #cbd5e1; box-shadow: none;">
                <div style="text-align:center;">
                    <div style="font-size: 2rem; margin-bottom: 10px;">⭐</div>
                    <div style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-bottom: 5px;">No customer reviews yet.</div>
                    <div style="color: #64748b; font-size: 0.9rem;">Be the first to review this product.</div>
                </div>
            </div>
        @endif
</section>
        @if($videoReviews->isNotEmpty())
        <div class="reels-section-wrap">

            <!-- Header row with title + nav buttons -->
            <div class="reels-header">
                <p class="reels-title">Parent Video Reviews</p>
                <div class="reels-nav">
                    <button class="reels-btn" id="reelPrev" aria-label="Previous">‹</button>
                    <button class="reels-btn reels-btn-next" id="reelNext" aria-label="Next">›</button>
                </div>
            </div>

            <!-- Viewport clips the track -->
            <div class="reels-viewport" id="reelsViewport">
                <div class="reels-row" id="reelsRow">

                    @if($videoReviews->isNotEmpty())
                        @foreach($videoReviews as $index => $review)
                            @php
                                $grad = $gradients[$index % count($gradients)];
                            @endphp
                            <div class="reel" data-reel="{{ $index }}" style="background:{{ $grad }}">
                                <div class="reel-prog">
                                    <div class="reel-bar" id="rb{{ $index }}"></div>
                                </div>
                                <div class="reel-bg">
                                    <video muted loop playsinline preload="auto">
                                        <source src="{{ asset('storage/' . $review->video_path) }}" type="video/mp4">
                                    </video>
                                </div>
                                <div class="reel-ov"></div>
                                <div class="reel-play-btn" id="rp{{ $index }}">▶</div>
                                <div class="reel-info">
                                    <div class="reel-stars">
                                        @for($i = 0; $i < 5; $i++)
                                            {{ $i < $review->rating ? '★' : '☆' }}
                                        @endfor
                                    </div>
                                    <div class="reel-ava">👱‍♀️</div>
                                    <div class="reel-name">{{ $review->user?->name ?? 'Anonymous Parent' }}</div>
                                    <div class="reel-txt">"{{ $review->comment }}"</div>
                                </div>
                            </div>
                        @endforeach
                    @endif

                </div><!-- /reels-row -->
            </div><!-- /reels-viewport -->

            <!-- Dot indicators -->
            <div class="reels-dots" id="reelsDots">
                @if($videoReviews->isNotEmpty())
                    @foreach($videoReviews as $index => $review)
                        <button class="reels-dot {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}"></button>
                    @endforeach
                @endif
            </div>

        </div><!-- /reels-section-wrap -->
        @endif

        @if($textReviews->isNotEmpty())
        <div class="wreviews-section-wrap" style="position: relative; margin-top: 48px;">
            @if($textReviews->isNotEmpty() && $textReviews->count() > 3)
                <div class="wreviews-header" style="display: flex; justify-content: flex-end; gap: 10px; margin-bottom: 15px;">
                    <button class="reels-btn" id="wrevPrev" aria-label="Previous" style="width: 40px; height: 40px; border-radius: 50%; font-size: 1.2rem; display: flex; align-items: center; justify-content: center; background: #fff; border: 1.5px solid #eee; cursor: pointer; color: var(--dk); transition: all 0.3s;">‹</button>
                    <button class="reels-btn reels-btn-next" id="wrevNext" aria-label="Next" style="width: 40px; height: 40px; border-radius: 50%; font-size: 1.2rem; display: flex; align-items: center; justify-content: center; background: #fff; border: 1.5px solid #eee; cursor: pointer; color: var(--dk); transition: all 0.3s;">›</button>
                </div>
            @endif

            <div class="wreviews-viewport" id="wreviewsViewport">
                <div class="wreviews-track" id="wreviewsTrack">
                    @if($textReviews->isNotEmpty())
                        @foreach($textReviews as $review)
                            <div class="wrev">
                                <div class="wrev-stars">
                                    @for($i = 0; $i < 5; $i++)
                                        {{ $i < $review->rating ? '★' : '☆' }}
                                    @endfor
                                </div>
                                <p class="wrev-txt">{{ $review->comment }}</p>
                                <div class="wrev-author">
                                    <div class="wrev-ava" style="background:{{ $loop->index % 2 == 0 ? '#FFE8F5' : '#E8F5FF' }}"></div>
                                    <div>
                                        <div class="wrev-name">{{ $review->user?->name ?? 'Anonymous Parent' }}</div>
                                        <div class="wrev-meta">Verified Parent</div>
                                        <div class="wrev-badge">✓ Verified Purchase</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
            
            @if($textReviews->isNotEmpty() && $textReviews->count() > 3)
                <div class="wreviews-dots" id="wreviewsDots" style="display: flex; justify-content: center; gap: 8px; margin-top: 20px;">
                    <!-- Dots will be generated dynamically by JS -->
                </div>
            @endif
        </div>
        @endif
    </section>
