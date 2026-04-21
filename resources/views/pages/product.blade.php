@extends('layouts.main')
@section('title', "NutriBuddy – India's #1 Kids Wellness Gummy")

@section('content')
    <div class="pdp-hero">

        <!-- LEFT: Gallery -->
        <div class="pdp-gallery">
            <div class="main-img-wrap">
                @if($product->is_featured)
                    <div class="badge-bestseller">Best Seller</div>
                @endif
                @if($product->compare_at_price > $product->base_price)
                    @php 
                        $discount = round((($product->compare_at_price - $product->base_price) / $product->compare_at_price) * 100);
                    @endphp
                    <div class="badge-discount">{{ $discount }}% OFF</div>
                @endif
                
                <div class="p-image" style="animation:floatY 4s ease-in-out infinite;display:block;line-height:1">
                    @if($product->primaryImage)
                        <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" alt="{{ $product->name }}" id="mainPdpImage">
                    @else
                        <img src="{{ asset('img/product2.png') }}" alt="{{ $product->name }}" id="mainPdpImage">
                    @endif
                </div>
            </div>
            <div class="thumb-row">
                @foreach($product->images as $image)
                    <div class="thumb {{ $image->is_primary ? 'active' : '' }}" onclick="changePdpImage(this, '{{ asset('storage/' . $image->image_path) }}')">
                        <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $product->name }}">
                    </div>
                @endforeach
                @if($product->images->count() == 0)
                    <div class="thumb active"> <img src="{{ asset('img/product2.png') }}" alt=""></div>
                    <div class="thumb"> <img src="{{ asset('img/p1.jpeg') }}" alt=""></div>
                @endif
            </div>
        </div>

        <!-- RIGHT: Info -->
        <div class="pdp-info">
            <div class="pdp-cat">{{ $product->category->name ?? 'Immunity & Growth' }} · Kids 2–14 yrs</div>
            <h1 class="pdp-name">{{ $product->name }}</h1>
            <div class="pdp-rating">
                <div class="stars">
                    @php $rating = $product->reviews->avg('rating') ?? 4.9; @endphp
                    @for($i=0; $i<5; $i++)
                        {{ $i < $rating ? '★' : '☆' }}
                    @endfor
                </div>
                <div class="rating-val">{{ number_format($rating, 1) }}</div>
                <div class="rating-divider"></div>
                <div class="rating-count">{{ $product->reviews->count() > 0 ? $product->reviews->count() : '2,841' }} Verified Reviews</div>
            </div>

            <!-- Price -->
            <div class="price-box">
                <div class="price-row">
                    <div class="price-now">₹{{ number_format($product->base_price, 0) }}</div>
                    @if($product->compare_at_price > $product->base_price)
                        <div class="price-old">₹{{ number_format($product->compare_at_price, 0) }}</div>
                        <div class="price-save">Save ₹{{ number_format($product->compare_at_price - $product->base_price, 0) }}</div>
                    @endif
                </div>
                <div class="price-note">Inclusive of all taxes · Free shipping on this order</div>
                <div class="cashback-row">
                    <span>🪙</span>
                    <span>Get {{ round($product->base_price * 0.05) }} NB Coins on this purchase!</span>
                </div>
            </div>
            <div class="arrange">
                <!-- Age Group -->
                <div class="variant-block">
                    <div class="variant-label">Age Group: </div>
                    <div class="variant-row">
                        <div class="vopt active">2–17 Yrs</div>
                    </div>
                </div>

                @if($product->is_variant_enabled && $product->variants->count() > 0)
                <!-- Variant Selector -->
                <div class="variant-block">
                    <div class="variant-label">Select Options: </div>
                    <div class="variant-row">
                        @foreach($product->variants as $variant)
                            <div class="qty-opt {{ $loop->first ? 'active' : '' }}" onclick="selectVariant(this,'{{ $variant->id }}')">
                                <div class="flavor-name">{{ $variant->name }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @else
                <!-- Static Fallback for Flavor/Pack -->
                <div class="variant-block">
                    <div class="variant-label">Pack Size: </div>
                    <div class="variant-row">
                        <div class="qty-opt active">30 Gummies</div>
                    </div>
                </div>
                @endif
            </div>

            <div class="variant-block">
                <div class="variant-label">{{ $product->name }} Features </div>
                <div class="variant-row" id="flavorRow">
                    <div class="flavor-opt active">
                        <div class="flavor-emoji"> <img src="{{ asset('img/sugar.png') }}" alt=""></div>
                        <div class="flavor-name">No Added Sugar</div>
                    </div>
                    <div class="flavor-opt active">
                        <div class="flavor-emoji"> <img src="{{ asset('img/no-preservatives.png') }}" alt=""></div>
                        <div class="flavor-name">No Preservatives</div>
                    </div>
                    <div class="flavor-opt active">
                        <div class="flavor-emoji"> <img src="{{ asset('img/no-artificial-colours.png') }}" alt=""> </div>
                        <div class="flavor-name">No Colours<br>Added</div>
                    </div>
                    <div class="flavor-opt active">
                        <div class="flavor-emoji"><img src="{{ asset('img/natural.png') }}" alt=""></div>
                        <div class="flavor-name">Rooted in <br> Ayurveda</div>
                    </div>
                    <div class="flavor-opt active">
                        <div class="flavor-emoji"><img src="{{ asset('img/tag.png') }}" alt=""></div>
                        <div class="flavor-name">No Gelatin <br> Plant Based Pectin</div>
                    </div>
                </div>
            </div>


            <!-- Pincode Check -->
            <div class="pincode-row">
                <div class="pincode-label">📍</div>
                <input type="text" maxlength="6" placeholder="Enter pincode to check delivery date"
                    id="pincodeInput">
                <button onclick="checkPincode()">Check</button>
            </div>
            <div id="pincode-result"
                style="font-size:.82rem;color:var(--mn);font-weight:700;margin-bottom:14px;display:none;padding: 0 4px;">✅
                Delivery by Tomorrow!</div>

            <!-- CTAs -->
            <div class="cta-row">
                <button class="btn-cart" onclick="addToCart('{{ $product->id }}')">Add to Cart</button>
                <button class="btn-buy" onclick="buyNow('{{ $product->id }}')">Buy Now</button>
            </div>

            <!-- Guarantees -->
            <div class="guarantees">
                <div class="guarantee">
                    <div class="g-icon">🚚</div>
                    <div class="g-title">Free Shipping</div>
                    <div class="g-sub">On orders ₹200+</div>
                </div>
                <div class="guarantee">
                    <div class="g-icon">🔄</div>
                    <div class="g-title">30-Day Return</div>
                    <div class="g-sub">No questions asked</div>
                </div>
                <div class="guarantee">
                    <div class="g-icon">🔒</div>
                    <div class="g-title">Secure Payment</div>
                    <div class="g-sub">UPI · Cards · COD</div>
                </div>
            </div>

            <!-- Product Highlights -->
            <div class="highlights">
                <h4>Why Parents Love {{ $product->name }}</h4>
                <ul class="highlight-list">
                    @php 
                        $features = $product->description ? explode("\n", $product->description) : [];
                        $features = array_filter(array_map('trim', $features));
                    @endphp
                    @if(count($features) > 0)
                        @foreach(array_slice($features, 0, 6) as $feature)
                            <li><div class="hl-dot"></div>{{ preg_replace('/^[•\-\*]\s*/', '', $feature) }}</li>
                        @endforeach
                    @else
                        {{-- Fallback --}}
                        <li><div class="hl-dot"></div>Ashwagandha (KSM-66®) + Vitamin D3 + Zinc — clinically proven formula</li>
                        <li><div class="hl-dot"></div>Supports immunity, height, bone density & overall energy in one gummy</li>
                        <li><div class="hl-dot"></div>Zero gelatin · 100% Vegetarian · No artificial colours or flavours</li>
                        <li><div class="hl-dot"></div>Tastes so good kids ask for it every morning — guaranteed!</li>
                    @endif
                </ul>
            </div>
        </div>
    </div>


    <!-- ══ DESCRIPTION & DETAILS ══ -->
    <section class="section-wrap reveal">
        <div style="max-width:1200px;margin:0 auto;">
            <h2 class="sec-title">Product <span class="acc">Details</span></h2>
            <div class="pdp-description" style="line-height: 1.8; color: var(--dk); font-size: 1.1rem;">
                {!! nl2br(e($product->description)) !!}
            </div>
        </div>
    </section>

    <!-- ══ HOW IT TRANSFORMS ══ -->
    <section class="section-wrap transform-section reveal">
        <div style="max-width:1200px;margin:0 auto;">
            <span class="sec-eye">Real Results</span>
            <h2 class="sec-title">Watch Your Child <span class="acc">Transform</span></h2>
            <p class="sec-sub">90 days of {{ $product->name }} — visible, measurable, life-changing results reported by thousands of
                parents.</p>�� visible, measurable, life-changing results reported by thousands of
                parents.</p>
            <div class="transform-grid">
                <div class="transform-visual">
                    <div
                        style="font-size:10rem;animation:floatY 4s ease-in-out infinite;position:relative;z-index:2;line-height:1">
                        </div>
                    <div class="before-after">
                        <div class="ba-card">
                            <div class="ba-label">Before</div>
                            <div class="ba-val">😔 Tired</div>
                        </div>
                        <div class="ba-arrow">→</div>
                        <div class="ba-card after">
                            <div class="ba-label">After 90 Days</div>
                            <div class="ba-val">🦸 Superhero!</div>
                        </div>
                    </div>
                </div>
                <div class="transform-list">
                    <div class="tr-item">
                        <div class="tr-icon" style="background:rgba(255,77,143,.12)">🛡️</div>
                        <div class="tr-body">
                            <div class="tr-title">Stronger Immunity</div>
                            <div class="tr-desc">Kids fall sick less often. Parents report 60% fewer sick days in the first
                                3 months
                                of consistent use.</div>
                            <div class="tr-week">Visible by Week 3</div>
                        </div>
                    </div>
                    <div class="tr-item">
                        <div class="tr-icon" style="background:rgba(0,191,255,.12)">📏</div>
                        <div class="tr-body">
                            <div class="tr-title">Height & Growth Spurt</div>
                            <div class="tr-desc">Ashwagandha + Zinc work synergistically to support natural growth hormone
                                function
                                and bone density.</div>
                            <div class="tr-week">Visible by Week 8</div>
                        </div>
                    </div>
                    <div class="tr-item">
                        <div class="tr-icon" style="background:rgba(0,214,143,.12)">⚡</div>
                        <div class="tr-body">
                            <div class="tr-title">All-Day Energy</div>
                            <div class="tr-desc">No more afternoon crashes. Kids stay energetic and active through school,
                                play, and
                                evening activities.</div>
                            <div class="tr-week">Visible by Week 2</div>
                        </div>
                    </div>
                    <div class="tr-item">
                        <div class="tr-icon" style="background:rgba(255,214,0,.15)">😊</div>
                        <div class="tr-body">
                            <div class="tr-title">Better Mood & Calm</div>
                            <div class="tr-desc">Adaptogenic Ashwagandha reduces cortisol — kids feel less stressed, sleep
                                better, and
                                wake up happier.</div>
                            <div class="tr-week">Visible by Week 4</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!--  -->

    <!-- ══ PEDIATRICIAN VIDEO ══ -->
    <section class="doc-section reveal">
        <div style="max-width:1100px;margin:0 auto;">
            <span class="sec-eye">Expert Endorsement</span>
            <h2 class="sec-title">What <span class="acc">Pediatricians</span> Say</h2>
            <p class="sec-sub" style="color:rgba(255,255,255,.5)">50+ certified pediatricians and nutritionists recommend
                NutriBuddy to their own patients and families.</p>
            <div class="doc-grid">
                <div>
                    <div class="doc-video-wrap"
                        onclick="this.innerHTML='<iframe width=\'100%\' height=\'100%\' src=\'https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1\' frameborder=\'0\' allow=\'autoplay\'></iframe>'">
                        <div class="doc-play">▶</div>
                        <div class="doc-video-label">Dr. Anita Nair — Pediatrician, Bangalore<br>Watch her recommendation
                            (2 min)
                        </div>
                    </div>
                    <div style="margin-top:16px;display:flex;gap:20px;justify-content:center;">
                        <div style="text-align:center;">
                            <div style="font-family:'Fredoka One',cursive;font-size:1.8rem;color:var(--pk)">50+</div>
                            <div style="color:rgba(255,255,255,.5);font-size:.78rem">Pediatricians</div>
                        </div>
                        <div style="text-align:center;">
                            <div style="font-family:'Fredoka One',cursive;font-size:1.8rem;color:var(--ye)">3 Yrs</div>
                            <div style="color:rgba(255,255,255,.5);font-size:.78rem">R&D Per Product</div>
                        </div>
                        <div style="text-align:center;">
                            <div style="font-family:'Fredoka One',cursive;font-size:1.8rem;color:var(--mn)">10K+</div>
                            <div style="color:rgba(255,255,255,.5);font-size:.78rem">Happy Families</div>
                        </div>
                    </div>
                </div>
                <div class="doc-info">
                    <div class="doc-card">
                        <div class="doc-name">Dr. Anita Nair</div>
                        <div class="doc-cred">MBBS, DCH · Pediatrician, Bangalore · 18 yrs experience</div>
                        <div class="doc-quote">As a pediatrician, I'm very selective about what I recommend. NutriBuddy's
                            completely
                            transparent formulas and third-party testing give me total confidence to recommend it to my
                            patients.
                        </div>
                    </div>
                    <div class="doc-card">
                        <div class="doc-name">Dr. Rajesh Kapoor</div>
                        <div class="doc-cred">MD Pediatrics · AIIMS Alumni · Delhi</div>
                        <div class="doc-quote">The KSM-66® Ashwagandha dosage is clinically appropriate and the
                            bioavailability of
                            their Zinc Bisglycinate is genuinely impressive. This is science-backed, not just marketing.
                        </div>
                    </div>
                    <div class="doc-card">
                        <div class="doc-name">Dt. Meena Iyer</div>
                        <div class="doc-cred">Certified Pediatric Nutritionist · Chennai</div>
                        <div class="doc-quote">I give it to my own children. The natural fruit extracts, zero artificial
                            additives,
                            and the Ayurvedic formulation align perfectly with what I recommend to every family I counsel.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <!--  -->
    <!-- ══════════════════════════════
             FEATURES — NO GELATIN etc.
        ══════════════════════════════ -->
    <section class="features-section reveal" id="features">
        <div class="feat-inner">
            <div class="feat-layout">
                <div>
                    <span class="sec-eye"> What's NOT in it</span>
                    <h2 class="feat-title">Pure as<br><span class="acc">Nature Intended</span> 🍃</h2>
                    <p class="feat-sub">We obsessed over every ingredient that goes in — and even more over what we keep
                        OUT.
                        Because your child's body deserves only the best.</p>
                    <div class="feat-list">
                        <div class="feat-item">
                            <div class="feat-item-icon" style="background:var(--mnl)">🚫</div>
                            <div>
                                <div class="feat-item-title">Zero Gelatin — 100% Vegetarian</div>
                                <div class="feat-item-desc">Most international gummies use animal gelatin (pig or bovine).
                                    All
                                    NutriBuddy gummies use plant-based pectin. Completely safe for every Indian family
                                    regardless of
                                    dietary beliefs.</div>
                            </div>
                        </div>
                        <div class="feat-item">
                            <div class="feat-item-icon" style="background:var(--pkl)">🍭</div>
                            <div>
                                <div class="feat-item-title">No Refined Sugar</div>
                                <div class="feat-item-desc">We sweeten with Stevia + monk fruit extract — giving a
                                    naturally sweet taste
                                    with zero impact on blood sugar. Kids get the yummy without the sugar crash or tooth
                                    decay.</div>
                            </div>
                        </div>

                        <div class="feat-item">
                            <div class="feat-item-icon" style="background:var(--yel)">🎨</div>
                            <div>
                                <div class="feat-item-title">No Artificial Colors or Flavors</div>
                                <div class="feat-item-desc">Our vibrant colors come from beetroot, turmeric, and spirulina.
                                    Our fruity
                                    burst flavors come from real fruit concentrates — not synthetic flavor chemicals tied to
                                    hyperactivity
                                    in children.</div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Comparison Table -->
                <div class="comparison-box">
                    <div class="comp-title">🏆 NutriBuddy vs. Other Brands</div>
                    <table class="comp-table">
                        <thead>
                            <tr>
                                <th></th>
                                <th class="comp-us-head">NutriBuddy</th>
                                <th style="color:#aaa">Little Joys</th>
                                <th style="color:#aaa">Gritzo</th>
                                <th style="color:#aaa">Others</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="comp-us">
                                <td>Ayurvedic herbs</td>
                                <td><span class="check">✓</span></td>
                                <td><span class="cross">✗</span></td>
                                <td><span class="cross">✗</span></td>
                                <td><span class="cross">✗</span></td>
                            </tr>
                            <tr>
                                <td>Zero Gelatin</td>
                                <td class="comp-us"><span class="check">✓</span></td>
                                <td><span class="cross">✗</span></td>
                                <td><span class="check">✓</span></td>
                                <td><span class="cross">✗</span></td>
                            </tr>
                            <tr class="comp-us">
                                <td>No refined sugar</td>
                                <td><span class="check">✓</span></td>
                                <td><span class="check">✓</span></td>
                                <td><span class="check">✓</span></td>
                                <td><span class="cross">✗</span></td>
                            </tr>
                            <tr>
                                <td>Third-party lab tested</td>
                                <td class="comp-us"><span class="check">✓</span></td>
                                <td><span class="check">✓</span></td>
                                <td><span class="check">✓</span></td>
                                <td><span class="cross">✗</span></td>
                            </tr>
                            <tr class="comp-us">
                                <td>Transparent batch results</td>
                                <td><span class="check">✓</span></td>
                                <td><span class="cross">✗</span></td>
                                <td><span class="cross">✗</span></td>
                                <td><span class="cross">✗</span></td>
                            </tr>
                            <tr>
                                <td>Pediatrician approved</td>
                                <td class="comp-us"><span class="check">✓</span></td>
                                <td><span class="cross">✗</span></td>
                                <td><span class="check">✓</span></td>
                                <td><span class="cross">✗</span></td>
                            </tr>
                            <tr class="comp-us">
                                <td>Age 2+ safe</td>
                                <td><span class="check">✓</span></td>
                                <td>4+</td>
                                <td>4+</td>
                                <td><span class="cross">✗</span></td>
                            </tr>
                            <tr>
                                <td>Price per day</td>
                                <td class="comp-us" style="color:var(--mn);font-weight:800">~₹20</td>
                                <td>~₹28</td>
                                <td>~₹35</td>
                                <td>Varies</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════════
               HOW IT WORKS
          ══════════════════════════════════════════ -->
    <!-- <section class="how-section reveal">
            <span class="sec-eye" style="display:block;text-align:center">Simple Process</span>
            <h2 class="sec-title">How It <span class="acc">Works</span></h2>
            <div class="steps">
              <div class="step-new">
                <div class="sball s1 "><img src="img/quiz.png" alt=""></div>
                <div class="snum">Step 01</div>
                <div class="stitle">Take the Quiz</div>
                <div class="sdesc">5 quick questions about your child's age, health goals, and diet preferences.</div>
              </div>
              <div class="step-new">
                   <div class="sball s2"><img src="img/plan.png" alt=""></div>
                <div class="snum">Step 02</div>
                <div class="stitle">Get Your Plan</div>
                <div class="sdesc">Personalized supplement plan by Ayurvedic nutritionists — completely free!</div>
              </div>
              <div class="step-new">
                 <div class="sball s3"><img src="img/order.png" alt=""></div>
                <div class="snum">Step 03</div>
                <div class="stitle">Order & Save</div>
                <div class="sdesc">Subscribe & Save for up to 20% off. Delivered fresh to your doorstep.</div>
              </div>
              <div class="step-new">
                  <div class="sball s4"><img src="img/rising.png" alt=""></div>
                <div class="snum">Step 04</div>
                <div class="stitle">Track Progress</div>
                <div class="sdesc">Log milestones on your parent dashboard and chat directly with our team.</div>
              </div>
            </div>
          </section> -->




    <!-- SECTION problem and solution -->

    <!-- SECTION -->
    <section class="ps-section">
        <div class="ps-inner">

            <!-- HEADER -->
            <div class="ps-header reveal">
                <div class="eyebrow">The Real Picture</div>
                <h2 class="ps-title">Kids Face <span class="acc">Real Problems</span> —<br>We Built a <span
                        class="acc2">Real
                        Solution</span></h2>
                <p class="ps-sub">Today's kids miss out on essential nutrition every day. We see the gap — and we've closed
                    it.
                </p>
            </div>

            <!-- PROBLEMS -->
            <div class="block-label reveal">
                <div class="blabel bl-prob">😟 Today's Challenges</div>
                <div class="bline"></div>
            </div>

            <div class="problem-grid">
                <div class="prob-card pc1 reveal d1">
                    <div class="prob-icon pi1"><img src="img/weak-boy.JPG" alt=""></div>
                    <div class="prob-name">Vitamin & Mineral Deficiency</div>
                    <p class="prob-text">Processed food strips away nutrients. 80% of Indian kids are Vitamin D deficient —
                        affecting bones, immunity & mood.</p>
                </div>
                <div class="prob-card pc2 reveal d2">
                    <div class="prob-icon pi2"><img src="img/BUSY-P.jpg" alt=""></div>
                    <div class="prob-name">Busy Parent, Skipped Nutrition</div>
                    <p class="prob-text">Between work and school runs, balanced meals slip through the cracks. Convenience
                        wins
                        over nutrition — every single day.</p>
                </div>
                <div class="prob-card pc3 reveal d3">
                    <div class="prob-icon pi3"><img src="img/hungry-boy.jpg" alt=""></div>
                    <div class="prob-name">Junk Food Addiction</div>
                    <p class="prob-text">Pizza, chips, sugary drinks — kids crave them and get them. High calories, zero
                        nutrition, and taste buds that reject healthy food.</p>
                </div>
                <div class="prob-card pc1 reveal d1">
                    <div class="prob-icon pi4"><img src="img/indoor.jpg" alt=""></div>
                    <div class="prob-name">Less Outdoor Play, More Screens</div>
                    <p class="prob-text">No sunlight means no Vitamin D. No movement means weak bones and low immunity —
                        visible
                        on the outside, starting from within.</p>
                </div>
                <div class="prob-card pc2 reveal d2">
                    <div class="prob-icon pi5"><img src="img/test-product.jpg" alt=""></div>
                    <div class="prob-name">Adulterated Food</div>
                    <p class="prob-text">Preservatives, artificial colors, hidden additives — what's really in your child's
                        food?
                        Nobody gives you a guarantee.</p>
                </div>
                <div class="prob-card pc3 reveal d3">
                    <div class="prob-icon pi6"><img src="img/illness.jpg" alt=""></div>
                    <div class="prob-name">Weak Immunity — Frequent Illness</div>
                    <p class="prob-text">The end result: kids fall sick repeatedly. School missed, exams affected, parents
                        stressed. A cycle that's hard to break.</p>
                </div>
            </div>

            <!-- DIVIDER -->
            <div class="ps-divider reveal">
                <div class="div-arrow">↓</div>
                <div class="div-badge"> Here's Our Answer</div>
                <div class="div-arrow">↓</div>
            </div>

            <!-- SOLUTION -->
            <div class="block-label reveal">
                <div class="blabel bl-sol">✅ NutriBuddy Solution</div>
                <div class="bline g"></div>
            </div>

            <!-- HERO -->
            <div class="sol-hero reveal">
                <div class="sol-hero-text">
                    <img src="img/posr.jpeg" alt="">

                    <!-- <div class="sol-badge">🏆 India's #1 Kids Wellness Gummy</div>
                  <h3 class="sol-title">One Gummy.<br><span class="hy">Complete Nutrition.</span><br><span class="hm">Zero
                      Compromise.</span></h3>
                  <p class="sol-desc">A simple, delicious, science-backed answer to every problem above. Kids love taking it —
                    parents love the results.</p>
                  <div class="sol-pills">
                    <div class="spill"> 100% Natural</div>
                    <div class="spill">🧪 Lab Tested</div>
                    <div class="spill">🩺 Pediatrician Approved</div>
                    <div class="spill">😋 Kids Love It</div>
                  </div>
                </div> -->

                </div>

                <!-- EQUATION -->
                <div class="eq-card reveal">
                    <div class="eq-lbl">✨ The NutriBuddy Formula</div>
                    <div class="eq-wrap">
                        <div class="eq-item">
                            <div class="eq-icon ei1">🏺</div>
                            <div class="eq-nm">Ayurvedic Wisdom</div>
                        </div>
                        <div class="eq-op">+</div>
                        <div class="eq-item">
                            <div class="eq-icon ei2">🔬</div>
                            <div class="eq-nm">Modern Science</div>
                        </div>
                        <div class="eq-op">+</div>
                        <div class="eq-item">
                            <div class="eq-icon ei3">👅</div>
                            <div class="eq-nm">Kid-Approved Taste</div>
                        </div>
                        <div class="eq-op">+</div>
                        <div class="eq-item">
                            <div class="eq-icon ei4">🩺</div>
                            <div class="eq-nm">Pediatrician Verified</div>
                        </div>
                        <div class="eq-eq">=</div>
                        <div class="eq-result">
                            <div class="eq-res-icon"></div>
                            <div class="eq-res-nm">NutriBuddy</div>
                        </div>
                    </div>
                </div>




                <!-- CTA -->
                <div class="ps-cta reveal">
                    <div class="cta-inner">
                        <span class="cta-emoji"><img src="img/nutrigummi.png" alt=""></span>
                        <h3 class="cta-title">Give Your Child the Best Start</h3>
                        <p class="cta-sub">Take a 2-minute quiz and get a FREE personalized diet chart — crafted by
                            certified
                            Ayurvedic nutritionists. No sign-up, no cost.</p>
                        <div class="cta-btns">
                            <a class="btn-main" href="#"> Shop NutriBuddy Now</a>
                            <a class="btn-ghost" href="#">📋 Get Free Diet Chart →</a>
                        </div>
                    </div>
                </div>

            </div>
    </section>


    <!-- ════════════════════════════════════════════════
             NUTRIBUDDY INGREDIENT SECTION
        ════════════════════════════════════════════════ -->
    <section id="nb-ingredients">

        <!-- Mesh BG -->
        <div class="nb-mesh">
            <div class="nb-blob nb-blob-1"></div>
            <div class="nb-blob nb-blob-2"></div>
            <div class="nb-blob nb-blob-3"></div>
            <div class="nb-blob nb-blob-4"></div>
            <!-- Stars -->
            <div class="nb-star" style="width:3px;height:3px;top:12%;left:8%;--dur:5s;--del:0s"></div>
            <div class="nb-star" style="width:4px;height:4px;top:28%;left:22%;--dur:7s;--del:1s"></div>
            <div class="nb-star" style="width:2px;height:2px;top:55%;left:75%;--dur:4s;--del:.5s"></div>
            <div class="nb-star" style="width:5px;height:5px;top:78%;left:90%;--dur:8s;--del:2s"></div>
            <div class="nb-star" style="width:3px;height:3px;top:40%;left:5%;--dur:6s;--del:1.5s"></div>
            <div class="nb-star" style="width:4px;height:4px;top:90%;left:40%;--dur:5s;--del:3s"></div>
            <div class="nb-star" style="width:2px;height:2px;top:18%;left:88%;--dur:9s;--del:.8s"></div>
            <div class="nb-star" style="width:3px;height:3px;top:65%;left:52%;--dur:6s;--del:2.5s"></div>
        </div>

        <!-- ── Header ── -->
        <div class="nb-ing-header">
            <div class="nb-eyebrow">🔬 Ingredient Transparency</div>
            <h2 class="nb-ing-title">
                What Goes Into Every<br>
                <span class="nb-acc-ye">GrowStrong</span> <span class="nb-acc-pk">Gummy?</span>
            </h2>
            <p class="nb-ing-sub">Every single ingredient explained — from ancient Ayurvedic herbs to essential vitamins
                and
                minerals. Click any ingredient to learn its full story.</p>
        </div>

        <!-- ── Category Filter (desktop) ── -->
        @php
            $categoryFilters = $ingredientCategoryFilters ?? collect();
            $totalIngredientCount = $ingredientTotalCount ?? 0;
            $ingredientItems = $ingredientItems ?? collect();
            $ingredientSummaryStats = $ingredientSummaryStats ?? [];
        @endphp
        <div class="nb-cat-row">
            <button class="nb-cat-pill nb-active" onclick="nbFilter('all',this)">
                <span class="nb-cat-dot" style="background:rgba(255,255,255,.5)"></span>All ({{ $totalIngredientCount }})
            </button>
            @foreach ($categoryFilters as $filter)
                <button class="nb-cat-pill" onclick="nbFilter('{{ $filter['key'] }}',this)">
                    <span class="nb-cat-dot" style="background:{{ $filter['dot_color'] }}"></span>{{ $filter['name'] }} ({{ $filter['count'] }})
                </button>
            @endforeach
        </div>

        <!-- ── Mobile Tabs ── -->
        <div class="nb-mobile-tabs" id="nbMobTabs">
            <button class="nb-mob-tab nb-sel-mob" onclick="nbMobFilter('all',this)">All ({{ $totalIngredientCount }})</button>
            @foreach ($categoryFilters as $filter)
                <button class="nb-mob-tab" onclick="nbMobFilter('{{ $filter['key'] }}',this)">{{ $filter['name'] }} ({{ $filter['count'] }})</button>
            @endforeach
        </div>

        <!-- ── Mobile Accordion Cards ── -->
        <div class="nb-mob-cards" id="nbMobCards">
            <!-- Generated by JS -->
        </div>

        <!-- ── Desktop: Two-column layout ── -->
        <div class="nb-ing-body">

            <!-- LEFT LIST -->
            <div class="nb-list-panel">
                <div class="nb-list-head">
                    <div class="nb-list-head-icon">📋</div>
                    <div>
                        <h4>Full Ingredient List</h4>
                        <p>{{ $totalIngredientCount }} ingredients · click to explore</p>
                    </div>
                </div>
                <div class="nb-list-scroll" id="nbList">
                    <!-- Rendered by JS -->
                </div>
            </div>

            <!-- RIGHT DETAIL -->
            <div class="nb-detail-wrap">
                <div class="nb-detail-empty" id="nbDetailEmpty">
                    <div class="nb-empty-ico">🔬</div>
                    <h3>Select an Ingredient</h3>
                    <p>Click any ingredient from the list on the left to discover its story, benefits, and why we chose it
                        for
                        your child.</p>
                </div>
                <div id="nbDetailCards">
                    <!-- Rendered by JS -->
                </div>
            </div>
        </div><!-- /nb-ing-body -->

        <!-- ── Summary Bar ── -->
        <div class="nb-summary-bar">
            <div class="nb-summary-inner">
                @foreach ($ingredientSummaryStats as $stat)
                    <div class="nb-stat">
                        <div class="nb-stat-n" style="color:{{ $stat['color'] }}">{{ $stat['value'] }}</div>
                        <div class="nb-stat-l">{{ $stat['label'] }}</div>
                    </div>
                    @if (! $loop->last)
                        <div class="nb-sdiv"></div>
                    @endif
                @endforeach
            </div>
        </div>

        <script id="nbIngredientsData" type="application/json">@json($ingredientItems)</script>

    </section>


    <!-- end ingredients -->

    <!-- ══════════════════════════════════════════
               TESTIMONIALS
          ══════════════════════════════════════════ -->
    <section class="testi-section reveal" id="reviews">
        <span class="sec-eye">Parent Reviews</span>
        <h2 class="sec-title" style="text-align:center">10,000+ Happy Families</h2>

        <div class="rev-summary reveal">
            <div class="rev-big">
                <div class="rev-big-n">4.9</div>
                <div class="rev-big-stars">★★★★★</div>
                <div class="rev-big-l">Based on 6,031 reviews</div>
            </div>
            <div class="rev-bars">
                <div class="rbar-row">5 ★ <div class="rbar-track">
                        <div class="rbar-fill" style="width:88%"></div>
                    </div> 88%</div>
                <div class="rbar-row">4 ★ <div class="rbar-track">
                        <div class="rbar-fill" style="width:8%"></div>
                    </div> 8%</div>
                <div class="rbar-row">3 ★ <div class="rbar-track">
                        <div class="rbar-fill" style="width:2.5%"></div>
                    </div> 2.5%</div>
                <div class="rbar-row">2 ★ <div class="rbar-track">
                        <div class="rbar-fill" style="width:1%"></div>
                    </div> 1%</div>
                <div class="rbar-row">1 ★ <div class="rbar-track">
                        <div class="rbar-fill" style="width:.5%"></div>
                    </div> 0.5%</div>
            </div>
            <div style="display:flex;flex-direction:column;gap:10px;min-width:180px">
                <div
                    style="text-align:center;font-family:'Fredoka One',cursive;font-size:1rem;color:var(--dk);margin-bottom:4px">
                    Top Tags</div>
                <div style="display:flex;flex-wrap:wrap;gap:8px">
                    <span
                        style="background:var(--pkl);color:var(--pk);border-radius:50px;padding:5px 12px;font-family:'Nunito',sans-serif;font-weight:800;font-size:.75rem">Tastes
                        Great</span>
                    <span
                        style="background:var(--skl);color:#0088bb;border-radius:50px;padding:5px 12px;font-family:'Nunito',sans-serif;font-weight:800;font-size:.75rem">Really
                        Works</span>
                    <span
                        style="background:var(--mnl);color:var(--mn);border-radius:50px;padding:5px 12px;font-family:'Nunito',sans-serif;font-weight:800;font-size:.75rem">Fast
                        Results</span>
                    <span
                        style="background:var(--yel);color:#907000;border-radius:50px;padding:5px 12px;font-family:'Nunito',sans-serif;font-weight:800;font-size:.75rem">Great
                        Value</span>
                </div>
            </div>
        </div>

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

                    <div class="reel" data-reel="0" style="background:linear-gradient(160deg,#FF8FAB,#FF4D8F)">
                        <div class="reel-prog">
                            <div class="reel-bar" id="rb0"></div>
                        </div>
                        <div class="reel-bg"><video autoplay muted loop playsinline>
                                <source src="img/v.mp4" type="video/mp4">
                            </video></div>
                        <div class="reel-ov"></div>
                        <div class="reel-play-btn" id="rp0">▶</div>
                        <div class="reel-info">
                            <div class="reel-name">Priya Sharma</div>
                            <div class="reel-txt">"My daughter hasn't missed school since starting GrowStrong!"</div>
                        </div>
                    </div>

                    <div class="reel" data-reel="1" style="background:linear-gradient(160deg,#7BC8FF,#0099DD)">
                        <div class="reel-prog">
                            <div class="reel-bar" id="rb1"></div>
                        </div>
                        <div class="reel-bg"><video autoplay muted loop playsinline>
                                <source src="img/v.mp4" type="video/mp4">
                            </video></div>
                        <div class="reel-ov"></div>
                        <div class="reel-play-btn" id="rp1">▶</div>
                        <div class="reel-info">
                            <div class="reel-name">Rahul Mehta</div>
                            <div class="reel-txt">"BrainBoost changed exam season for us. His focus is insane."</div>
                        </div>
                    </div>

                    <div class="reel" data-reel="2" style="background:linear-gradient(160deg,#B79FFF,#7C3AED)">
                        <div class="reel-prog">
                            <div class="reel-bar" id="rb2"></div>
                        </div>
                        <div class="reel-bg"><video autoplay muted loop playsinline>
                                <source src="img/v.mp4" type="video/mp4">
                            </video></div>
                        <div class="reel-ov"></div>
                        <div class="reel-play-btn" id="rp2">▶</div>
                        <div class="reel-info">
                            <div class="reel-name">Dr. Anita Nair</div>
                            <div class="reel-txt">"As a pediatrician, I recommend NutriBuddy with full confidence."</div>
                        </div>
                    </div>

                    <div class="reel" data-reel="3" style="background:linear-gradient(160deg,#FFD97D,#FF9900)">
                        <div class="reel-prog">
                            <div class="reel-bar" id="rb3"></div>
                        </div>
                        <div class="reel-bg"><video autoplay muted loop playsinline>
                                <source src="img/v.mp4" type="video/mp4">
                            </video></div>
                        <div class="reel-ov"></div>
                        <div class="reel-play-btn" id="rp3">▶</div>
                        <div class="reel-info">
                            <div class="reel-name">Fatima Khan</div>
                            <div class="reel-txt">"DreamCalm turned bedtime from nightmare into our fav time."</div>
                        </div>
                    </div>

                    <div class="reel" data-reel="4" style="background:linear-gradient(160deg,#6EF0C0,#00A87A)">
                        <div class="reel-prog">
                            <div class="reel-bar" id="rb4"></div>
                        </div>
                        <div class="reel-bg"><video autoplay muted loop playsinline>
                                <source src="img/v.mp4" type="video/mp4">
                            </video></div>
                        <div class="reel-ov"></div>
                        <div class="reel-play-btn" id="rp4">▶</div>
                        <div class="reel-info">
                            <div class="reel-name">Vikram Patel</div>
                            <div class="reel-txt">"Both kids on different NutriBuddy plans. Life-changing."</div>
                        </div>
                    </div>

                    <div class="reel" data-reel="5" style="background:linear-gradient(160deg,#FFB3C6,#FF6B8A)">
                        <div class="reel-prog">
                            <div class="reel-bar" id="rb5"></div>
                        </div>
                        <div class="reel-bg"><video autoplay muted loop playsinline>
                                <source src="img/v.mp4" type="video/mp4">
                            </video></div>
                        <div class="reel-ov"></div>
                        <div class="reel-play-btn" id="rp5">▶</div>
                        <div class="reel-info">
                            <div class="reel-name">Sneha Joshi</div>
                            <div class="reel-txt">"My toddler asks for his gummy before breakfast. That's a win."</div>
                        </div>
                    </div>

                </div><!-- /reels-row -->
            </div><!-- /reels-viewport -->

            <!-- Dot indicators -->
            <div class="reels-dots" id="reelsDots">
                <button class="reels-dot active" data-index="0"></button>
                <button class="reels-dot" data-index="1"></button>
                <button class="reels-dot" data-index="2"></button>
                <button class="reels-dot" data-index="3"></button>
                <button class="reels-dot" data-index="4"></button>
                <button class="reels-dot" data-index="5"></button>
            </div>

        </div><!-- /reels-section-wrap -->

        <div class="wreviews">
            <div class="wrev">
                <div class="wrev-stars">★★★★★</div>
                <p class="wrev-txt">My 7-year-old was constantly falling sick. After 2 months of GrowStrong, she hasn't
                    missed a
                    single day of school — and she asks for it every morning!</p>
                <div class="wrev-author">
                    <div class="wrev-ava" style="background:#FFE8F5"></div>
                    <div>
                        <div class="wrev-name">Priya Sharma</div>
                        <div class="wrev-meta">Mum of 2 · Delhi</div>
                        <div class="wrev-badge">✓ Verified Purchase</div>
                    </div>
                </div>
            </div>
            <div class="wrev">
                <div class="wrev-stars">★★★★★</div>
                <p class="wrev-txt">BrainBoost Chews have been a game-changer for exam prep. My son's class teacher
                    actually
                    called to ask what changed — his focus is incredible!</p>
                <div class="wrev-author">
                    <div class="wrev-ava" style="background:#E8F5FF"></div>
                    <div>
                        <div class="wrev-name">Rahul Mehta</div>
                        <div class="wrev-meta">Dad of 1 · Mumbai</div>
                        <div class="wrev-badge">✓ Verified Purchase</div>
                    </div>
                </div>
            </div>
            <div class="wrev">
                <div class="wrev-stars">★★★★★</div>
                <p class="wrev-txt">As a pediatrician, I'm very selective about what I recommend. NutriBuddy's completely
                    transparent formulas and third-party testing give me total confidence.</p>
                <div class="wrev-author">
                    <div class="wrev-ava" style="background:#EDE9FE"></div>
                    <div>
                        <div class="wrev-name">Dr. Anita Nair</div>
                        <div class="wrev-meta">Pediatrician · Bangalore</div>
                        <div class="wrev-badge">✓ Medical Expert</div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- ══════════════════════════════════════════
               FAQ
          ══════════════════════════════════════════ -->
    <section class="faq-section reveal">
        <span class="sec-eye">Got Questions?</span>
        <h2 class="sec-title">Parents <span class="acc">Ask</span> Us</h2>
        <div class="faq-list">
            <div class="faq-item open">
                <button class="faq-q">Are NutriBuddy products safe for young children?<span
                        class="faq-tog">+</span></button>
                <div class="faq-ans">
                    <p>Absolutely. All products are formulated for children aged 2–14 with age-appropriate dosages,
                        third-party
                        lab tested every batch, and reviewed by certified pediatricians. Zero artificial colors, flavors, or
                        harmful
                        preservatives — ever.</p>
                </div>
            </div>
            <div class="faq-item">
                <button class="faq-q">How long before I see results?<span class="faq-tog">+</span></button>
                <div class="faq-ans">
                    <p>Most parents notice improvements in energy and mood within 2–3 weeks. For immunity and growth
                        benefits,
                        60–90 days of consistent use shows the best results. We recommend tracking milestones on your parent
                        dashboard.</p>
                </div>
            </div>
            <div class="faq-item">
                <button class="faq-q">Are these vegetarian or vegan?<span class="faq-tog">+</span></button>
                <div class="faq-ans">
                    <p>All our gummies and chews are 100% vegetarian. Select vegan options are clearly labeled on each
                        product
                        page. Zero gelatin or animal-derived ingredients — ever.</p>
                </div>
            </div>
            <div class="faq-item">
                <button class="faq-q">Can my child take multiple products together?<span
                        class="faq-tog">+</span></button>
                <div class="faq-ans">
                    <p>Yes! Our products are designed to complement each other beautifully. Take our personalized quiz to
                        build
                        the right supplement stack for your child's specific age, goals, and health needs.</p>
                </div>
            </div>
            <div class="faq-item">
                <button class="faq-q">What if my child doesn't like the taste?<span class="faq-tog">+</span></button>
                <div class="faq-ans">
                    <p>We offer a 30-day taste guarantee. If your child genuinely doesn't enjoy the flavor, we'll refund you
                        completely — no questions asked. We're that confident. (But honestly, 98% of kids love it!)</p>
                </div>
            </div>
            <div class="faq-item">
                <button class="faq-q">Is there a subscription option?<span class="faq-tog">+</span></button>
                <div class="faq-ans">
                    <p>Yes! Subscribe & Save gives you 20% off every order, free delivery, priority restocking during
                        shortages,
                        and exclusive member discounts. Cancel or pause anytime — no penalties whatsoever.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════════
               NEWSLETTER
          ══════════════════════════════════════════ -->
    <div class="newsletter reveal">
        <span class="sec-eye">Stay in the Loop</span>
        <h2 class="sec-title">Wellness Tips for Your Little Ones</h2>
        <p class="nl-sub">Join 25,000+ parents getting Ayurvedic parenting tips, exclusive discounts & early product access
            every week.</p>
        <div class="nl-form">
            <input class="nl-input" type="email" placeholder="Enter your email address">
            <button class="hbtn hbtn-main" style="padding:13px 28px;font-size:.9rem">Subscribe</button>
        </div>
    </div>

@endsection
