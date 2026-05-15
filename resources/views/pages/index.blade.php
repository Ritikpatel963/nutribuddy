@extends('layouts.main')
@section('title', "NutriBuddy – India's #1 Kids Wellness Gummy")

@section('content')
    <!-- ══════════════════════════════════════════
                       HERO SLIDER
                  ══════════════════════════════════════════ -->
    <!-- ── HERO ── -->
    <section class="hero">
        <!-- Slide 1 -->
        <div class="slide slide-1 active" data-slide="0">
            <!-- <img src="/img/img1.jpeg" alt=""> -->
            <div class="blob b1"></div>
            <div class="blob b2"></div>
            <div class="slide-text">
                <div class="slide-badge badge-pk">India's #1 Kids Wellness Gummy</div>
                <h1 class="htitle">Gummies That Turn<br>Kids Into <span class="pop">Superheroes!</span></h1>
                <p class="slide-desc">Packed with Ashwagandha + Vitamin D3 + Zinc, our GrowStrong Gummies make immunity fun,
                    tasty, and totally cool for your little ones aged 2–14.</p>
                <div class="hero-btns">
                    <a href="#products" class="hbtn hbtn-main">Shop Now — ₹599</a>
                    <a href="#quiz" class="hbtn hbtn-ghost">Find My Plan →</a>
                </div>
                <div class="hero-trust">
                    <div class="htrust"> Ayurveda-Backed</div>
                    <div class="htrust">Lab Tested</div>
                    <div class="htrust"> 0 Additives</div>
                    <div class="htrust">Age 2–14</div>
                </div>
            </div>
            <div class="slide-visual">
                <div class="gummy-showcase">
                    <div class="bb bb1">⚡ Boosts Energy</div>
                    <div class="bb bb2">🛡️ Builds Immunity</div>
                    <div class="bb bb3">💪 Stronger Bones</div>
                    <div class="bb bb4">😊 Uplifts Mood</div>
                    <div class="bb bb5">🌙 Better Sleep</div>
                    <span class="fg fg1"></span><span class="fg fg2">🧠</span><span class="fg fg3">⭐</span><span
                        class="fg fg4">🍊</span><span class="fg fg5">✨</span><span class="fg fg6">💊</span>
                    <div class="jar-wrap">

                        <div class="jar-body1">
                            <img src="/img/boii.png" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Slide 2 -->
        <div class="slide slide-2" data-slide="1">
            <div class="blob b1" style="background:rgb(82, 162, 82)"></div>
            <div class="blob b2" style="background:var(--mn)"></div>
            <div class="slide-text">
                <div class="slide-badge badge-sk">Neuroscientist-Designed</div>
                <h1 class="htitle">Unlock Your Child's<br><span class="blue">Brainpower</span><br>With Every Chew!</h1>
                <p class="slide-desc">Brahmi + Omega-3 DHA + Shankhpushpi in every delicious chew. Clinically studied to
                    improve
                    focus, memory & learning by up to 38%.</p>
                <div class="hero-btns">
                    <a href="#products" class="hbtn hbtn-sky">Shop BrainBoost — ₹649</a>
                    <a href="#quiz" class="hbtn hbtn-ghost">Learn More →</a>
                </div>
                <div class="hero-trust">
                    <div class="htrust"> Brahmi Extract</div>
                    <div class="htrust"> Omega-3 DHA</div>
                    <div class="htrust"> Improves Focus</div>
                    <div class="htrust"> Exam Ready</div>
                </div>
            </div>
            <div class="slide-visual">
                <div class="gummy-showcase">
                    <div class="bb bb1" style="border-color:var(--skl)">🎯 Laser Focus</div>
                    <div class="bb bb2" style="border-color:var(--skl)">📚 Better Grades</div>
                    <div class="bb bb3" style="border-color:var(--mnl)">💡 Creativity+</div>
                    <div class="bb bb4" style="border-color:var(--skl)">🧠 Memory Up 38%</div>
                    <div class="bb bb5" style="border-color:var(--pul)">😌 Calm Focus</div>
                    <span class="fg fg1">🐟</span><span class="fg fg2">🧠</span><span class="fg fg3">⭐</span><span
                        class="fg fg4">💙</span><span class="fg fg5">✨</span><span class="fg fg6">🌊</span>
                    <div class="jar-wrap">

                        <div class="jar-body1">
                            <img src="/img/kadda.png" alt="">


                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Slide 3 -->
        <div class="slide slide-3" data-slide="2">
            <div class="blob b1" style="background:var(--ye)"></div>
            <div class="blob b2" style="background:var(--or)"></div>
            <div class="slide-text">
                <div class="slide-badge badge-ye">Bedtime Transformed</div>
                <h1 class="htitle">Sweet Dreams &<br><span class="green">Calm Nights</span><br>Guaranteed!</h1>
                <p class="slide-desc">Chamomile + L-Theanine + Jatamansi. Helps even the most hyperactive kids wind down
                    peacefully and wake up refreshed, energized & happy every morning.</p>
                <div class="hero-btns">
                    <a href="#products" class="hbtn hbtn-or">Shop DreamCalm — ₹549</a>
                    <a href="#quiz" class="hbtn hbtn-ghost">Learn More →</a>
                </div>
                <div class="hero-trust">
                    <div class="htrust">Chamomile</div>
                    <div class="htrust">Deep Sleep</div>
                    <div class="htrust">Less Anxiety</div>
                    <div class="htrust">All-Natural</div>
                </div>
            </div>
            <div class="slide-visual">
                <div class="gummy-showcase">
                    <div class="bb bb1" style="border-color:var(--yel)">😴 Deep Sleep</div>
                    <div class="bb bb2" style="border-color:var(--orl)">😌 Calm Mood</div>
                    <div class="bb bb3" style="border-color:var(--yel)">🌙 Better Nights</div>
                    <div class="bb bb4" style="border-color:var(--mnl)">🌞 Wake Up Fresh</div>
                    <div class="bb bb5" style="border-color:var(--orl)">🦋 Less Anxiety</div>
                    <span class="fg fg1">🌙</span><span class="fg fg2">🌸</span><span class="fg fg3">⭐</span><span
                        class="fg fg4">🍯</span><span class="fg fg5">✨</span><span class="fg fg6">💫</span>
                    <div class="jar-wrap">

                        <div class="jar-body1">
                            <img src="/img/yello-g.png" alt="">


                        </div>
                    </div>
                </div>
            </div>
        </div>

        <button class="sarrow sp" id="prevBtn">‹</button>
        <button class="sarrow sn" id="nextBtn">›</button>
        <div class="slide-dots">
            <button class="dot active" data-dot="0"></button>
            <button class="dot" data-dot="1"></button>
            <button class="dot" data-dot="2"></button>
        </div>
        <div class="slide-counter"><span id="curSlide">1</span>/3</div>
    </section>


    <!-- ══════════════════════════════════════════
                       TRUST BAR (Scrolling Ticker)
                  ══════════════════════════════════════════ -->
    <!-- ── TRUST BAR ── -->
    <div class="tbar">
        <div class="tscroll">
            <div class="titem"><span class="tic"></span> 100% Natural Ingredients</div>
            <div class="titem"><span class="tic">🏆</span> FSSAI Certified</div>
            <div class="titem"><span class="tic">🚫</span> No Artificial Colors</div>
            <div class="titem"><span class="tic">🔬</span> Third-Party Lab Tested</div>
            <div class="titem"><span class="tic">🌱</span> Vegan Options Available</div>
            <div class="titem"><span class="tic">🇮🇳</span> Proudly Made in India</div>
            <div class="titem"><span class="tic">👨‍⚕️</span> Pediatrician Approved</div>
            <div class="titem"><span class="tic">💊</span> Zero Harmful Additives</div>
            <div class="titem"><span class="tic"></span> 100% Natural Ingredients</div>
            <div class="titem"><span class="tic">🏆</span> FSSAI Certified</div>
            <div class="titem"><span class="tic">🚫</span> No Artificial Colors</div>
            <div class="titem"><span class="tic">🔬</span> Third-Party Lab Tested</div>
            <div class="titem"><span class="tic">🌱</span> Vegan Options Available</div>
            <div class="titem"><span class="tic">🇮🇳</span> Proudly Made in India</div>
            <div class="titem"><span class="tic">👨‍⚕️</span> Pediatrician Approved</div>
            <div class="titem"><span class="tic">💊</span> Zero Harmful Additives</div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════
                       TRUST INDICATORS
                  ══════════════════════════════════════════ -->
    <!-- ── TRUST INDICATORS ── -->
    <section class="trust-section reveal" id="trust">
        <span class="sec-eye">Why Parents Trust Us</span>
        <h2 class="sec-title">Numbers That <span class="acc">Speak</span></h2>
        <p class="sec-sub">Backed by science, loved by parents, and trusted by pediatricians across India.</p>
        <div class="trust-grid">
            <div class="tc"><span class="tc-icon"><img src="/img/family.png" alt=""></span>
                <div class="tc-n" data-count="10000">0+</div>
                <div class="tc-l">Happy Families</div>
                <div class="tc-d">Serving kids from Jammu to Kanyakumari, every single month.</div>
            </div>
            <div class="tc"><span class="tc-icon"><img src="/img/smart-city.png" alt=""></span>
                <div class="tc-n" data-count="500">0+</div>
                <div class="tc-l">Cities Across India</div>
                <div class="tc-d">From metros to Tier-2 cities — NutriBuddy ships everywhere.</div>
            </div>
            <div class="tc"><span class="tc-icon"><img src="/img/cycle.png" alt=""></span>
                <div class="tc-n" data-count="98">0%</div>
                <div class="tc-l">Reorder Rate</div>
                <div class="tc-d">When kids love it and parents see results, they keep coming back.</div>
            </div>
            <div class="tc"><span class="tc-icon"><img src="/img/rat.png" alt=""></span>
                <div class="tc-n">4.9</div>
                <div class="tc-l">Average Rating</div>
                <div class="tc-d">Across 6,000+ verified parent reviews on our platform.</div>
            </div>
            <div class="tc"><span class="tc-icon"><img src="/img/labo.png" alt=""></span>
                <div class="tc-n">3</div>
                <div class="tc-l">Yrs R&D Per Product</div>
                <div class="tc-d">We don't rush to market. Every formula is rigorously tested first.</div>
            </div>
            <div class="tc"><span class="tc-icon"><img src="/img/pediatrician.png" alt=""></span>
                <div class="tc-n" data-count="50">0+</div>
                <div class="tc-l">Pediatricians Approved</div>
                <div class="tc-d">Recommended by certified pediatricians and nutritionists nationwide.</div>
            </div>
        </div>

    </section>


    <!-- ── WHY CHOOSE US ── -->
    <section class="why-section reveal" id="why">
        <span class="sec-eye">Why Parents Choose Us</span>
        <h2 class="sec-title" style="color:white">The NutriBuddy <span class="acc"
                style="color:var(--ye)">Difference</span>
        </h2>
        <div class="why-grid">
            <div class="wc wc1">
                <div class="wc-icon" style="background:rgba(0,214,143,.1)"><img src="/img/natural.png" alt="">
                </div>
                <h3>Ayurveda Meets Science</h3>
                <p>Time-tested herbs from Ayurveda, validated by modern clinical research. The best of 5,000 years and 21st
                    century together.</p>
            </div>
            <div class="wc wc2">
                <div class="wc-icon" style="background:rgba(255,77,143,.1)"><img src="/img/observation.png"
                        alt=""></div>
                <h3>Lab-Tested Purity</h3>
                <p>Every batch is third-party tested for heavy metals, microbials, and potency. We publish every single
                    result —
                    transparency is non-negotiable.</p>
            </div>
            <div class="wc wc3">
                <div class="wc-icon" style="background:rgba(0,191,255,.1)"><img src="/img/girl.png" alt="">
                </div>
                <h3>Kid-Safe Formulas</h3>
                <p>Age-appropriate dosing for 2–14 years. Zero artificial preservatives, colors or fillers.
                    Pediatrician-reviewed at every step.</p>
            </div>
            <div class="wc wc4">
                <div class="wc-icon" style="background:rgba(255,214,0,.1)"><img src="/img/tongue.png" alt="">
                </div>
                <h3>Kids Actually Love It</h3>
                <p>98% of kids proactively ask for their NutriBuddy gummy. No battles at supplement time — ever again.
                    Seriously.</p>
            </div>
            <div class="wc wc6">
                <div class="wc-icon" style="background:rgba(255,107,53,.1)"><img src="/img/value.png" alt="">
                </div>
                <h3>Real Value</h3>
                <p>Premium Ayurvedic nutrition at prices every Indian family can afford. Subscribe & Save up to 20% — cancel
                    anytime.</p>
            </div>
        </div>
    </section>




    <!-- ══════════════════════════════════════════
                       PRODUCTS
                  ══════════════════════════════════════════ -->
    <section class="products-section reveal" id="products">
        <span class="sec-eye">Our Products</span>
        <h2 class="sec-title">Nutrition Kids <span class="acc">Actually Love</span></h2>
        <p class="sec-sub">Each product crafted with Ayurvedic wisdom + modern science. Balanced doses, kid-safe, genuinely
            delicious flavors.</p>
        <div class="products-grid">
            @foreach ($featuredProducts as $product)
                @php
                    $catSlug = $product->category->slug ?? 'pk';
                    // Map database slugs to CSS classes if they don't match
if ($catSlug == 'multivitamins') {
    $catSlug = 'pk';
} elseif ($catSlug == 'whey-protein') {
    $catSlug = 'sk';
} elseif ($catSlug == 'pre-workout') {
    $catSlug = 'pu';
} else {
    $catSlug = 'pk';
                    }
                    $activeVariants = $product->variants
                        ->filter(fn ($variant) => $variant->is_active && !empty($variant->attributes))
                        ->values();
                    $variationLabels = $activeVariants
                        ->map(function ($variant) {
                            $label = collect($variant->attributes ?? [])
                                ->filter(fn ($value) => trim((string) $value) !== '')
                                ->map(fn ($value, $key) => $key . ': ' . $value)
                                ->implode(' / ');

                            return $label ?: $variant->name;
                        })
                        ->filter()
                        ->unique()
                        ->take(4)
                        ->values()
                        ->all();

                    if (empty($variationLabels)) {
                        $variationLabels = collect([
                            $product->flavor ? 'Flavour: ' . $product->flavor : null,
                            $product->pack_size ? 'Pack Size: ' . $product->pack_size : null,
                            $product->age_group ? 'Age Group: ' . $product->age_group : null,
                            $product->dosage ? 'Dosage: ' . $product->dosage : null,
                        ])->filter()->take(4)->values()->all();
                    }

                    $variantGroups = [];
                    foreach ($activeVariants as $variant) {
                        foreach (($variant->attributes ?? []) as $name => $value) {
                            $value = trim((string) $value);
                            if ($value === '') {
                                continue;
                            }
                            $variantGroups[$name] ??= [];
                            if (!in_array($value, $variantGroups[$name], true)) {
                                $variantGroups[$name][] = $value;
                            }
                        }
                    }

                    $selectedVariant = $activeVariants->firstWhere('is_default', true) ?: $activeVariants->first();
                    $selectedAttributes = $selectedVariant?->attributes ?? [];
                    $selectedLabel = collect($selectedAttributes)
                        ->filter(fn ($value) => trim((string) $value) !== '')
                        ->map(fn ($value, $key) => $key . ': ' . $value)
                        ->implode(' / ');
                    $stockQty = (int) ($selectedVariant?->inventory?->stock_qty ?? 0);
                    $trackStock = (bool) ($selectedVariant?->inventory?->track_stock ?? false);
                    $isAvailable = ! $trackStock || (($selectedVariant?->inventory?->is_in_stock ?? true) && $stockQty > 0);
                    $hasVariantOptions = !empty($variantGroups) || !empty($variationLabels);
                    $frontendVariants = $activeVariants
                        ->map(function ($variant) {
                            $stockQty = (int) ($variant->inventory?->stock_qty ?? 0);
                            $trackStock = (bool) ($variant->inventory?->track_stock ?? false);

                            return [
                                'id' => $variant->id,
                                'name' => $variant->name,
                                'attributes' => $variant->attributes ?? [],
                                'price' => (float) $variant->display_price,
                                'compare_price' => (float) ($variant->display_compare_price ?? 0),
                                'stock_qty' => $stockQty,
                                'track_stock' => $trackStock,
                                'available' => ! $trackStock || (($variant->inventory?->is_in_stock ?? true) && $stockQty > 0),
                            ];
                        })
                        ->values()
                        ->all();
                    $cardPrice = (float) ($selectedVariant?->display_price ?? $product->display_price);
                    $cardComparePrice = (float) ($selectedVariant?->display_compare_price ?? $product->display_compare_price ?? 0);
                @endphp
                <div class="pc pc-{{ $catSlug }} {{ $selectedVariant ? 'has-variants' : 'no-variants' }}"
                    data-selected-variant-id="{{ $selectedVariant?->id }}"
                    data-selected-variant-label="{{ $selectedLabel }}"
                    data-variants='{{ json_encode($frontendVariants, JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) }}'>
                    <div class="pc-head pc-head-{{ $catSlug }}">
                        <a href="{{ route('product.show', $product->slug) }}" class="pc-emoji p-image">
                            @if ($product->primaryImage)
                                <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}"
                                    alt="{{ $product->name }}" class="default-img" loading="lazy" decoding="async">
                                @php $secondImage = $product->images->where('is_primary', false)->first(); @endphp
                                @if ($secondImage)
                                    <img src="{{ asset('storage/' . $secondImage->image_path) }}"
                                        alt="{{ $product->name }}" class="hover-img" loading="lazy" decoding="async">
                                @else
                                    <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}"
                                        alt="{{ $product->name }}" class="hover-img" loading="lazy" decoding="async">
                                @endif
                            @else
                                <img src="{{ asset('img/productt.png') }}" alt="{{ $product->name }}"
                                    class="default-img" loading="lazy" decoding="async">
                                <img src="{{ asset('img/productt.png') }}" alt="{{ $product->name }}"
                                    class="hover-img" loading="lazy" decoding="async">
                            @endif
                        </a>
                        @if ($product->is_featured)
                            <div class="pc-badge">Best Seller</div>
                        @endif
                    </div>
                    <div class="pc-body">
                        <div class="pc-stars">
                            @php $rating = $product->reviews->avg('rating') ?? 5; @endphp
                            @for ($i = 0; $i < 5; $i++)
                                {{ $i < $rating ? '★' : '☆' }}
                            @endfor
                            <span style="color:#aaa;font-size:.75rem;font-family:'DM Sans',sans-serif">
                                ({{ $product->reviews->count() > 0 ? $product->reviews->count() : '2,841' }} reviews)
                            </span>
                        </div>
                        <div class="pc-cat cat-{{ $catSlug }}">{{ $product->category->name ?? 'Uncategorized' }}
                        </div>
                        <div class="pc-name"><a href="{{ route('product.show', $product->slug) }}"
                                style="color: inherit; text-decoration: none;">{{ $product->name }}</a></div>
                        @if($hasVariantOptions)
                            <div class="pc-variant-panel">
                                @if(!empty($variantGroups))
                                    <div class="pc-variant-groups">
                                        @foreach($variantGroups as $attributeName => $values)
                                            <div class="pc-variant-block">
                                                <div class="pc-variant-label">{{ $attributeName }}</div>
                                                <div class="pc-option-row" data-attribute-group="{{ $attributeName }}">
                                                    @foreach($values as $value)
                                                        <button type="button"
                                                            class="pc-option-btn {{ ($selectedAttributes[$attributeName] ?? null) === $value ? 'active' : '' }}"
                                                            data-attribute="{{ $attributeName }}"
                                                            data-value="{{ $value }}">
                                                            {{ $value }}
                                                        </button>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    @foreach(array_slice($variationLabels, 0, 3) as $variation)
                                        <div class="pc-option-row">
                                            <button type="button" class="pc-option-btn active">{{ $variation }}</button>
                                        </div>
                                    @endforeach
                                @endif

                                <div class="pc-variant-meta">
                                    <span class="pc-stock-pill {{ $isAvailable ? '' : 'out' }}">
                                        @if($isAvailable)
                                            {{ $trackStock ? $stockQty . ' unit piece' : 'Available' }}
                                        @else
                                            Out of stock
                                        @endif
                                    </span>
                                    <span class="pc-selected-pill" title="{{ $selectedLabel ?: 'Product option' }}">
                                        {{ $selectedLabel ?: 'Product option' }}
                                    </span>
                                </div>
                            </div>
                        @endif
                        <div class="pc-features">
                            @php
                                $tags = $product->tags ?? [];
                                // Backward compatibility for old string tags
                                if (is_string($tags)) {
                                    $tags = array_map(function ($t) {
                                        preg_match(
                                            '/^([\x{1F300}-\x{1F9FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}])?\s*(.*)$/u',
                                            $t,
                                            $m,
                                        );
                                        return ['icon' => $m[1] ?? '', 'text' => $m[2] ?? $t];
                                    }, array_filter(array_map('trim', explode(',', $tags))));
                                }
                                $tags = array_slice($tags, 0, 4);
                            @endphp
                            @if (count($tags) > 0)
                                @foreach (array_chunk($tags, 2) as $chunk)
                                    <div class="newcarda">
                                        @foreach ($chunk as $tag)
                                            <span>
                                                @if (!empty($tag['icon']))
                                                    @php
                                                        $isFilePath = str_contains($tag['icon'], 'tags/');
                                                    @endphp
                                                    <i>
                                                        @if ($isFilePath)
                                                            <img src="{{ asset('storage/' . $tag['icon']) }}"
                                                                style="width:16px; height:16px; object-fit:contain; vertical-align: middle;" loading="lazy" decoding="async">
                                                        @else
                                                            {{ $tag['icon'] }}
                                                        @endif
                                                    </i>
                                                @endif
                                                {{ \Illuminate\Support\Str::limit($tag['text'] ?? '', 15) }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endforeach
                            @else
                                <div class="newcarda">
                                    <span><i>🛡️</i> Boosts Immunity</span>
                                    <span><i>📈</i> Supports Growth</span>
                                </div>
                                <div class="newcarda">
                                    <span><i>⚡</i> Increases Energy</span>
                                    <span><i>😊</i> Improves Mood</span>
                                </div>
                            @endif
                        </div>
                        <div class="pc-foot">
                            <div class="pc-price" data-price-label>
                                ₹{{ number_format($cardPrice, 0) }}
                                @if ($cardComparePrice > $cardPrice)
                                    <s>₹{{ number_format($cardComparePrice, 0) }}</s>
                                @endif
                            </div>
                            <button class="btn-add badd-{{ $catSlug }}" data-id="{{ $product->id }}" data-variant-id="{{ $selectedVariant?->id }}">Add to Cart
                                +</button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>



    <!-- ══════════════════════════════════════════
                       QUIZ CTA
                  ══════════════════════════════════════════ -->
    <div class="quiz-cta reveal" id="quiz">
        <div>
            <span class="quiz-label"> Personalized Nutrition</span>
            <h2 class="quiz-h">Not Sure Which Gummy<br>Is Right for Your Child?</h2>
            <p class="quiz-p">Take our 2-minute wellness quiz and get a FREE personalized diet chart crafted by certified
                Ayurvedic nutritionists. No signup needed.</p>
        </div>
        <button class="quiz-btn">Start Free Quiz →</button>
    </div>





    <!-- ══════════════════════════════════════════
                       INGREDIENTS HIGHLIGHT
                  ══════════════════════════════════════════ -->
    @if ($featuredIngredients->isNotEmpty())
        <section class="ing-section" id="ingredients">
            <div class="stars-bg" id="starsBg"></div>

            <div class="ing-header reveal">
                <span class="sec-eye">Ingredient Transparency</span>
                <h2 class="sec-title">Journey of Every <span class="acc">Ingredient</span></h2>
                <p class="sec-sub" style="color:rgba(255,255,255,.5);margin:0 auto">From ancient forests to your child's
                    gummy
                    — an honest, magical story of every ingredient we use.</p>
            </div>

            <div class="ing-tabs reveal">
                @foreach ($featuredIngredients as $i => $ing)
                    <button class="itab {{ $i === 0 ? 'active' : '' }}" data-ing="{{ $i }}">
                        {{ $ing->main_heading }}
                    </button>
                @endforeach
            </div>

            <div class="ing-panels">
                @foreach ($featuredIngredients as $i => $ing)
                    @php
                        $orbSpeeds = ['8s', '6s', '7s', '8.5s', '7.5s', '8s', '6s'];
                        $orb2 = ['8s', '7s', '9s', '15s', '10s', '8s', '7s'];
                        $orb3 = ['13s', '11s', '14s', '18s', '18s', '13s', '11s'];
                        $num = str_pad($i + 1, 2, '0', STR_PAD_LEFT);
                    @endphp
                    <div class="ing-panel {{ $i === 0 ? 'active' : '' }}" id="ing-panel-{{ $i }}">
                        <div style="display:flex;justify-content:center">
                            <div class="ing-planet"
                                style="background:radial-gradient(circle at 35% 35%,#2A4A2A,#0D2A0D);--pglow:rgba(0,214,143,.35)">
                                @if ($ing->icon_path)
                                    <img class="image-big" src="{{ asset('storage/' . $ing->icon_path) }}"
                                        alt="{{ $ing->main_heading }}">
                                @else
                                    <img class="image-big" src="{{ asset('img/gradient1.webp') }}"
                                        alt="{{ $ing->main_heading }}">
                                @endif
                                <div class="orbit-i" style="--orr:{{ $orbSpeeds[$i] ?? '8s' }}">⭐</div>
                                <div class="orbit-i" style="--orr:{{ $orb2[$i] ?? '8s' }}">⭐</div>
                                <div class="orbit-i" style="--orr:{{ $orb3[$i] ?? '13s' }};font-size:1.1rem">⭐</div>
                            </div>
                        </div>
                        <div class="ing-text">
                            <div class="ing-num">{{ $num }}</div>
                            <div class="ing-pill"
                                style="background:rgba(0,214,143,.12);color:var(--mn);border:1px solid rgba(0,214,143,.2)">
                                {{ $ing->short_heading }}
                            </div>
                            <h3 class="ing-name">{{ $ing->main_heading }}</h3>
                            @if ($ing->dosage_heading_one)
                                <p class="ing-sci">{{ $ing->dosage_heading_one }}</p>
                            @endif
                            @if ($ing->description)
                                <p class="ing-story">{{ $ing->description }}</p>
                            @endif
                            @if ($ing->benefits->isNotEmpty())
                                <div class="ing-powers">
                                    @foreach ($ing->benefits as $benefit)
                                        <div class="ptag">{{ $benefit->heading }}</div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach

            </div><!-- /ing-panels -->
        </section>
    @endif




    <!-- ══════════════════════════════════════════
                     DIET CHART SECTION
                ══════════════════════════════════════════ -->
    <section class="diet-section" id="diet-chart">

        <div class="diet-header">
            <span class="sec-eye">Free for Every Parent</span>
            <h2 class="sec-title">Get Your Child's <span class="acc">Personalized</span><br>Diet Chart </h2>
            <p class="sec-sub">Answer 4 quick questions — get a free 2-day expert diet plan. Subscribe to unlock the full
                7-day plan + PDF download.</p>
        </div>

        <div class="stepper-wrap">

            <!-- Stepper Progress -->
            <div class="stepper-progress">
                <div class="sp-step active" id="sp1">
                    <div class="sp-ball">1</div>
                    <div class="sp-label">Child Info</div>
                </div>
                <div class="sp-line">
                    <div class="sp-line-fill" id="line1"></div>
                </div>
                <div class="sp-step" id="sp2">
                    <div class="sp-ball">2</div>
                    <div class="sp-label">Health Goals</div>
                </div>
                <div class="sp-line">
                    <div class="sp-line-fill" id="line2"></div>
                </div>
                <div class="sp-step" id="sp3">
                    <div class="sp-ball">3</div>
                    <div class="sp-label">Diet Type</div>
                </div>
                <div class="sp-line">
                    <div class="sp-line-fill" id="line3"></div>
                </div>
                <div class="sp-step" id="sp4">
                    <div class="sp-ball">4</div>
                    <div class="sp-label">Your Plan</div>
                </div>
            </div>

            <!-- Diet Card -->
            <div class="diet-card">

                <!-- STEP 1 -->
                <div class="step-panel active" id="panel1">
                    <div class="step-head">
                        <span class="step-emoji"></span>
                        <h3>Tell us about your child</h3>
                        <p>Select age, gender, and optional body measurements for a more accurate plan.</p>
                    </div>

                    <div style="margin-bottom:24px">
                        <div style="font-family:'Fredoka One',cursive;font-size:1rem;color:var(--dk);margin-bottom:14px">
                            Age Group
                        </div>
                        <div class="age-grid" id="ageGrid">
                            <div class="age-card" data-age="2-3" onclick="dcSelectAge(this)">
                                <span class="age-emoji"><img src="/img/girl.png" alt=""></span>
                                <span class="age-range">2–3 yrs</span>
                                <span class="age-label">Toddler</span>
                            </div>
                            <div class="age-card" data-age="4-6" onclick="dcSelectAge(this)">
                                <span class="age-emoji"><img src="/img/b1.png" alt=""></span>
                                <span class="age-range">4–6 yrs</span>
                                <span class="age-label">Pre-School</span>
                            </div>
                            <div class="age-card" data-age="7-9" onclick="dcSelectAge(this)">
                                <span class="age-emoji"><img src="/img/b2.png" alt=""></span>
                                <span class="age-range">7–9 yrs</span>
                                <span class="age-label">Primary School</span>
                            </div>
                            <div class="age-card" data-age="10-12" onclick="dcSelectAge(this)">
                                <span class="age-emoji"><img src="/img/g1.png" alt=""></span>
                                <span class="age-range">10–12 yrs</span>
                                <span class="age-label">Middle School</span>
                            </div>
                            <div class="age-card" data-age="13-14" onclick="dcSelectAge(this)">
                                <span class="age-emoji"><img src="/img/b4.png" alt=""></span>
                                <span class="age-range">13–14 yrs</span>
                                <span class="age-label">Teen</span>
                            </div>
                        </div>
                        <div class="selection-error" id="ageError">⚠️ Please select an age group to continue.</div>
                    </div>

                    <div style="margin-bottom:24px">
                        <div style="font-family:'Fredoka One',cursive;font-size:1rem;color:var(--dk);margin-bottom:14px">
                            Gender
                        </div>
                        <div class="gender-row" id="genderRow">
                            <div class="gender-card" data-gender="boy" onclick="dcSelectGender(this)">
                                <span class="gender-emoji"><img src="/img/boy.png" alt=""></span>
                                <div class="gender-name">Boy</div>
                            </div>
                            <div class="gender-card" data-gender="girl" onclick="dcSelectGender(this)">
                                <span class="gender-emoji"><img src="/img/girl.png" alt=""></span>
                                <div class="gender-name">Girl</div>
                            </div>
                            <!-- <div class="gender-card" data-gender="other" onclick="dcSelectGender(this)">
                                    <span class="gender-emoji"><img src="/img/boyn.png" alt=""></span>
                                    <div class="gender-name">Prefer not to say</div>
                                </div> -->
                        </div>
                        <div class="selection-error" id="genderError">⚠️ Please select a gender to continue.</div>
                    </div>

                    <!-- NEW: Height & Weight -->
                    <div>
                        <div style="font-family:'Fredoka One',cursive;font-size:1rem;color:var(--dk);margin-bottom:6px">
                            Body Measurements
                            <span
                                style="font-size:.72rem;font-family:'Nunito',sans-serif;font-weight:700;color:#aaa;margin-left:6px">(Optional
                                — improves accuracy)</span>
                        </div>
                        <div class="hw-row">
                            <div class="hw-group">
                                <label>Height (cm)</label>
                                <input type="number" id="heightInput" placeholder="e.g. 115" min="50"
                                    max="200">
                            </div>
                            <div class="hw-group">
                                <label>Weight (kg)</label>
                                <input type="number" id="weightInput" placeholder="e.g. 22" min="5"
                                    max="100">
                            </div>
                        </div>
                    </div>

                    <div class="step-nav">
                        <div></div>
                        <button class="btn-next" onclick="dcGoStep(2)">Continue → Health Goals</button>
                    </div>
                </div>

                <!-- STEP 2 -->
                <div class="step-panel" id="panel2">
                    <div class="step-head">
                        <span class="step-emoji">🎯</span>
                        <h3>What are your goals for your child?</h3>
                        <p>Select all that apply — we'll personalize your diet chart accordingly.</p>
                    </div>
                    <div class="problem-grid" id="problemGrid">
                        <div class="prob-tag" data-prob="immunity" onclick="dcToggleProb(this)"><span
                                class="prob-icon">🛡️</span>
                            Boost Immunity</div>
                        <div class="prob-tag" data-prob="growth" onclick="dcToggleProb(this)"><span
                                class="prob-icon">📏</span>
                            Height & Growth</div>
                        <div class="prob-tag" data-prob="brain" onclick="dcToggleProb(this)"><span
                                class="prob-icon">🧠</span> Brain
                            & Focus</div>
                        <div class="prob-tag" data-prob="weight" onclick="dcToggleProb(this)"><span
                                class="prob-icon">⚖️</span>
                            Healthy Weight</div>
                        <div class="prob-tag" data-prob="energy" onclick="dcToggleProb(this)"><span
                                class="prob-icon">⚡</span> More
                            Energy</div>
                        <div class="prob-tag" data-prob="sleep" onclick="dcToggleProb(this)"><span
                                class="prob-icon">😴</span>
                            Better Sleep</div>
                        <div class="prob-tag" data-prob="digestion" onclick="dcToggleProb(this)"><span
                                class="prob-icon"></span>
                            Gut & Digestion</div>
                        <div class="prob-tag" data-prob="bones" onclick="dcToggleProb(this)"><span
                                class="prob-icon">💪</span>
                            Strong Bones</div>
                        <div class="prob-tag" data-prob="mood" onclick="dcToggleProb(this)"><span
                                class="prob-icon">😊</span> Mood &
                            Calm</div>
                        <div class="prob-tag" data-prob="skin" onclick="dcToggleProb(this)"><span
                                class="prob-icon">✨</span> Skin &
                            Hair</div>
                        <div class="prob-tag" data-prob="appetite" onclick="dcToggleProb(this)"><span
                                class="prob-icon">🍽️</span>
                            Picky Eater Fix</div>
                        <div class="prob-tag" data-prob="exam" onclick="dcToggleProb(this)"><span
                                class="prob-icon">📚</span> Exam
                            Performance</div>
                    </div>
                    <div class="selection-error" id="probError">⚠️ Please select at least one health goal.</div>
                    <div class="step-nav">
                        <button class="btn-back" onclick="dcGoStep(1)">← Back</button>
                        <button class="btn-next" onclick="dcGoStep(3)">Next → Diet Preferences</button>
                    </div>
                </div>

                <!-- STEP 3 -->
                <div class="step-panel" id="panel3">
                    <div class="step-head">
                        <span class="step-emoji"></span>
                        <h3>Any food preferences or allergies?</h3>
                        <p>This helps us build a plan your family will actually love and follow.</p>
                    </div>
                    <div style="margin-bottom:24px">
                        <div style="font-family:'Fredoka One',cursive;font-size:1rem;color:var(--dk);margin-bottom:14px">
                            Diet Type
                        </div>
                        <div class="diet-pref-row" id="dietPrefRow">
                            <div class="dpref" data-pref="vegetarian" onclick="dcSelectPref(this)"> Vegetarian</div>
                            <div class="dpref" data-pref="vegan" onclick="dcSelectPref(this)">🌱 Vegan</div>
                            <div class="dpref" data-pref="eggetarian" onclick="dcSelectPref(this)">🥚 Eggetarian</div>
                            <div class="dpref" data-pref="non-veg" onclick="dcSelectPref(this)">🍗 Non-Veg</div>
                        </div>
                        <div class="selection-error" id="prefError">⚠️ Please select a diet preference.</div>
                    </div>
                    <div>
                        <div style="font-family:'Fredoka One',cursive;font-size:1rem;color:var(--dk);margin-bottom:8px">
                            Known Allergies <span
                                style="font-weight:400;font-size:.8rem;font-family:'DM Sans',sans-serif;color:#aaa">(Optional)</span>
                        </div>
                        <div class="allergy-row" id="allergyRow">
                            <div class="atag" data-allergy="dairy" onclick="dcToggleAllergy(this)">🥛 Dairy</div>
                            <div class="atag" data-allergy="gluten" onclick="dcToggleAllergy(this)">🌾 Gluten</div>
                            <div class="atag" data-allergy="nuts" onclick="dcToggleAllergy(this)">🥜 Nuts</div>
                            <div class="atag" data-allergy="soy" onclick="dcToggleAllergy(this)">🫘 Soy</div>
                            <div class="atag" data-allergy="eggs" onclick="dcToggleAllergy(this)">🥚 Eggs</div>
                            <div class="atag" data-allergy="none" onclick="dcToggleAllergy(this)">✅ No Allergies</div>
                        </div>
                    </div>
                    <div class="step-nav">
                        <button class="btn-back" onclick="dcGoStep(2)">← Back</button>
                        <button class="btn-generate" onclick="dcGenerateChart()">✨ Generate My Diet Chart</button>
                    </div>
                </div>

                <!-- Loading -->
                <div class="loading-state" id="loadingState">
                    <div class="loader-ring"></div>
                    <h3>Crafting Your Child's Plan </h3>
                    <p>Our Ayurvedic nutritionists are personalizing this just for you…</p>
                    <div class="loading-facts">
                        <span class="lfact">✅ FSSAI Certified Recipes</span>
                        <span class="lfact">👩‍⚕️ Pediatrician Approved</span>
                        <span class="lfact"> Ayurveda-Backed</span>
                    </div>
                </div>

                <!-- Result -->
                <div class="result-state" id="resultState">

                    <!-- Hero result card -->
                    <div class="result-hero">
                        <div class="result-badge">Personalized Diet Chart</div>
                        <h3 id="resultTitle">Your Child's Nutrition Plan</h3>
                        <p id="resultDesc"></p>
                        <div id="bmiRow"></div>
                        <div class="result-tags" id="resultTags"></div>
                        <div style="margin-top:14px">
                            <span class="free-preview-badge">🎁 Free Preview: Day 1 & Day 2</span>
                        </div>
                    </div>

                    <!-- Day 1 -->
                    <div id="day1Section">
                        <div
                            style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;flex-wrap:wrap;gap:8px">
                            <div style="font-family:'Fredoka One',cursive;font-size:1.2rem;color:var(--dk)">Daily Meal Plan
                            </div>
                        </div>
                        <div class="day-pill">📅 Day 1 — Monday</div>
                        <div class="meal-plan-grid" id="mealGrid1"></div>
                    </div>

                    <!-- Day 2 -->
                    <div id="day2Section">
                        <div class="day-pill"
                            style="background:linear-gradient(135deg,var(--sk),#0088bb);box-shadow:0 4px 14px rgba(0,191,255,.28)">
                            📅
                            Day 2 — Tuesday</div>
                        <div class="meal-plan-grid" id="mealGrid2"></div>
                    </div>

                    <!-- Nutrients -->
                    <div class="nutrients-section">
                        <div class="nutrients-title">Daily Nutrition Targets</div>
                        <div id="nutrientBars"></div>
                    </div>

                    <!-- Product recommendation -->
                    <div class="product-rec" id="productRec"></div>

                    <!-- Parent tips -->
                    <div style="font-family:'Fredoka One',cursive;font-size:1.2rem;color:var(--dk);margin-bottom:16px">💡
                        Parent
                        Tips</div>
                    <div class="tips-grid" id="tipsGrid"></div>

                    <!-- ═══ LOCK BANNER — 7-day plan ═══ -->
                    <div class="lock-banner" id="lockBanner">
                        <span class="lock-icon-lg">🔒</span>
                        <div class="lock-title">Unlock the Full 7-Day Plan</div>
                        <div class="lock-sub">Get Day 3–7 personalized meal plans, weekly grocery list, supplement stack,
                            and a
                            downloadable PDF chart.</div>

                        <div class="plan-cards-grid">
                            <div class="plan-card" id="planBasic" onclick="dcSelectPlan('basic')">
                                <div class="plan-name">7-Day Plan</div>
                                <div class="plan-price">₹99 <span>/ one-time</span></div>
                                <div class="plan-features">
                                    <div class="pf">Full 7-day meal plan</div>
                                    <div class="pf">PDF download</div>
                                    <div class="pf">Email delivery</div>
                                </div>
                            </div>
                            <div class="plan-card active" id="planPro" onclick="dcSelectPlan('pro')">
                                <span class="plan-pop-badge">⭐ Most Popular</span>
                                <div class="plan-name">Monthly Subscription</div>
                                <div class="plan-price">₹199 <span>/ month</span></div>
                                <div class="plan-features">
                                    <div class="pf">New plan every month</div>
                                    <div class="pf">PDF downloads</div>
                                    <div class="pf">Progress tracker</div>
                                    <div class="pf">Priority support</div>
                                </div>
                            </div>
                        </div>

                        <button class="btn-subscribe" onclick="dcOpenModal()">Subscribe & Unlock Full Plan →</button>
                    </div>

                    <!-- Success banner (shown after payment) -->
                    <div class="success-banner" id="successBanner">
                        <div style="font-size:3rem;margin-bottom:12px">🎉</div>
                        <h3>Payment Successful! Your PDF is Ready.</h3>
                        <p>The complete 7-day plan has been sent to your email. You can also download it directly below.</p>
                        <button class="btn-generate" onclick="dcDownload()">⬇ Download PDF Chart</button>
                    </div>

                    <div class="result-actions">
                        <button class="btn-restart" onclick="dcRestart()">↩ Make Another Plan</button>
                    </div>

                </div><!-- /result-state -->
            </div><!-- /diet-card -->
        </div><!-- /stepper-wrap -->
    </section>
    <!-- ══════════════════════════════════════════
                     SUBSCRIPTION MODAL
                ══════════════════════════════════════════ -->
    <div class="modal-overlay" id="dcModalOverlay" onclick="dcCloseModalOutside(event)">
        <div class="modal-box">
            <button class="modal-close-btn" onclick="dcCloseModal()">✕</button>

            <h2 class="modal-h" id="dcModalTitle">Unlock Your 7-Day Plan </h2>
            <p class="modal-sub" id="dcModalSub">Enter your details to receive the complete personalized diet chart via
                email.
            </p>

            <div class="mfield">
                <label>Parent's name</label>
                <input type="text" id="dcName" placeholder="e.g. Priya Sharma">
            </div>
            <div class="mfield">
                <label>Email address</label>
                <input type="email" id="dcEmail" placeholder="hello@gmail.com">
            </div>
            <div class="mfield">
                <label>Phone number</label>
                <input type="tel" id="dcPhone" placeholder="+91 98765 43210">
            </div>

            <div class="order-summary-box">
                <div
                    style="font-family:'Nunito',sans-serif;font-weight:800;font-size:.75rem;color:#888;text-transform:uppercase;letter-spacing:1px;margin-bottom:10px">
                    Order Summary</div>
                <div class="os-row">
                    <span class="os-lbl" id="dcOrderItem">7-Day Diet Chart (Monthly)</span>
                    <span class="os-val" id="dcOrderPrice">₹199</span>
                </div>
            </div>

            <button class="btn-generate" style="width:100%;justify-content:center;font-size:.95rem"
                onclick="dcProcessPayment()">
                Pay & Download Chart →
            </button>

            <div class="secure-note">🔒 Secure payment &nbsp;·&nbsp; Instant PDF delivery &nbsp;·&nbsp; Cancel anytime
            </div>
        </div>
    </div>


    <!-- ══════════════════════════════════════════
                       HOW IT WORKS
                  ══════════════════════════════════════════ -->
    <section class="how-section reveal">
        <div class="how-layout">
        <div class="how-head">
            <span class="sec-eye">Simple Process</span>
            <h2 class="sec-title">How It <span class="acc">Works</span></h2>
            <p>From a quick parent quiz to a plan you can actually follow, NutriBuddy keeps each step simple.</p>
            <div class="how-stats">
                <div><strong>2 min</strong><span>quick quiz</span></div>
                <div><strong>Free</strong><span>diet plan</span></div>
            </div>
        </div>
        <div class="steps">
            <div class="step-new">
                <div class="step-top">
                    <div class="sball s1"><img src="/img/quiz.png" alt=""></div>
                    <div class="snum">Step 01</div>
                </div>
                <div class="stitle">Take the Quiz</div>
                <div class="sdesc">5 quick questions about your child's age, health goals, and diet preferences.</div>
            </div>
            <div class="step-new">
                <div class="step-top">
                    <div class="sball s2"><img src="/img/plan.png" alt=""></div>
                    <div class="snum">Step 02</div>
                </div>
                <div class="stitle">Get Your Plan</div>
                <div class="sdesc">Personalized supplement plan by Ayurvedic nutritionists — completely free!</div>
            </div>
            <div class="step-new">
                <div class="step-top">
                    <div class="sball s3"><img src="/img/order.png" alt=""></div>
                    <div class="snum">Step 03</div>
                </div>
                <div class="stitle">Order & Save</div>
                <div class="sdesc">Subscribe & Save for up to 20% off. Delivered fresh to your doorstep.</div>
            </div>
            <div class="step-new">
                <div class="step-top">
                    <div class="sball s4"><img src="/img/rising.png" alt=""></div>
                    <div class="snum">Step 04</div>
                </div>
                <div class="stitle">Track Progress</div>
                <div class="sdesc">Log milestones on your parent dashboard and chat directly with our team.</div>
            </div>
        </div>
        <div class="how-cta-row">
            <a href="#diet-chart" class="hbtn hbtn-main">Start Free Quiz</a>
            <span>No sign-up required to begin</span>
        </div>
        </div>
    </section>




    <!-- ══════════════════════════════════════════
                       CERTIFICATIONS CAROUSEL
                  ══════════════════════════════════════════ -->
    <!-- <section class="imgcar-section reveal" id="certificates">
                    <div class="imgcar-header">
                      <span class="sec-eye">Our Certifications</span>
                      <h2 class="sec-title">Certified, Tested &amp; <span class="acc">Trusted</span> 🔬</h2>
                      <p class="sec-sub" style="max-width:500px;margin:0 auto">Every product is backed by rigorous testing and globally
                        recognised certifications.</p>
                    </div>

                    <div class="imgcar-wrapper">
                      <button class="imgcar-arrow imgcar-arrow-prev" id="imgcarPrev" aria-label="Previous">‹</button>
                      <div class="imgcar-viewport" id="imgcarViewport">
                        <div class="imgcar-pbar" id="imgcarPbar"></div>
                        <div class="imgcar-track" id="imgcarTrack">
                          <div class="imgcar-item"><img src="/img/cert-fssai.png" alt="FSSAI Certified"
                              onerror="this.src='https://placehold.co/300x180/FFD6E8/C0306F?text=FSSAI'"></div>
                          <div class="imgcar-item"><img src="/img/cert-nabl.png" alt="NABL Lab Tested"
                              onerror="this.src='https://placehold.co/300x180/EDE9FE/5B21B6?text=NABL+Lab'"></div>
                          <div class="imgcar-item"><img src="/img/cert-nongmo.png" alt="Non-GMO Verified"
                              onerror="this.src='https://placehold.co/300x180/D0FFF2/007755?text=Non-GMO'"></div>
                          <div class="imgcar-item"><img src="/img/cert-iap.png" alt="Pediatrician Approved"
                              onerror="this.src='https://placehold.co/300x180/DCFBFF/0077AA?text=Pediatrician'"></div>
                          <div class="imgcar-item"><img src="/img/cert-cruelty.png" alt="Cruelty Free"
                              onerror="this.src='https://placehold.co/300x180/FFFBE0/907000?text=Cruelty+Free'"></div>
                          <div class="imgcar-item"><img src="/img/cert-iso.png" alt="ISO 22000 GMP"
                              onerror="this.src='https://placehold.co/300x180/FFE8DF/A03010?text=ISO+22000'"></div>
                        </div>
                      </div>
                      <button class="imgcar-arrow imgcar-arrow-next" id="imgcarNext" aria-label="Next">›</button>
                    </div>

                    <div class="imgcar-dots" id="imgcarDots"></div>
                  </section> -->

    <!-- ══════════════════════════════════════════
                       TESTIMONIALS
                  ══════════════════════════════════════════ -->
    @include('partials.parent-reviews')

    <!-- ══════════════════════════════════════════
                       FAQ
                  ══════════════════════════════════════════ -->
    @include('partials.faq-section')
    <!-- ══════════════════════════════════════════
                       NEWSLETTER
                  ══════════════════════════════════════════ -->
    <div class="newsletter reveal">
        <span class="sec-eye">Stay in the Loop</span>
        <h2 class="sec-title">Wellness Tips for Your Little Ones</h2>
        <p class="nl-sub">Join 25,000+ parents getting Ayurvedic parenting tips, exclusive discounts & early product
            access
            every week.</p>
        <form class="nl-form newsletterSubscribeForm" action="{{ route('newsletter.subscribe') }}" method="POST">
            @csrf
            <input type="hidden" name="source" value="newsletter_block">
            <input class="nl-input" type="email" name="email" maxlength="50" placeholder="Enter your email address" required>
            <button class="hbtn hbtn-main" type="submit" style="padding:13px 28px;font-size:.9rem">Subscribe</button>
            <div class="newsletterSubscribeMessage" style="display:none;width:100%;margin-top:8px;font-size:.82rem;font-weight:800;text-align:center;"></div>
        </form>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.pc-variant-panel').forEach(panel => {
                const card = panel.closest('.pc');
                const addButton = card?.querySelector('.btn-add');
                const variants = (() => {
                    try {
                        return JSON.parse(card?.dataset.variants || '[]');
                    } catch (_) {
                        return [];
                    }
                })();

                function findSelectedVariant() {
                    const selected = Object.fromEntries(
                        Array.from(panel.querySelectorAll('.pc-option-btn.active[data-attribute]'))
                            .map(item => [item.dataset.attribute, item.dataset.value])
                    );

                    return variants.find(variant => {
                        return Object.entries(selected).every(([name, value]) => {
                            return String(variant.attributes?.[name] ?? '') === String(value ?? '');
                        });
                    }) || null;
                }

                function applySelectedVariant() {
                    const selected = Array.from(panel.querySelectorAll('.pc-option-btn.active[data-attribute]'))
                        .map(item => `${item.dataset.attribute}: ${item.dataset.value}`)
                        .join(' / ');
                    const selectedPill = panel.querySelector('.pc-selected-pill');
                    const stockPill = panel.querySelector('.pc-stock-pill');
                    const variant = findSelectedVariant();

                    if (selectedPill && selected) {
                        selectedPill.textContent = selected;
                        selectedPill.title = selected;
                    }

                    if (card) card.dataset.selectedVariantId = variant?.id || '';
                    if (card) card.dataset.selectedVariantLabel = selected || variant?.name || '';
                    if (addButton) addButton.dataset.variantId = variant?.id || '';

                    if (card && variant) {
                        const priceLabel = card.querySelector('[data-price-label]');
                        const price = Number(variant.price || 0);
                        const comparePrice = Number(variant.compare_price || 0);

                        if (priceLabel) {
                            priceLabel.innerHTML = `₹${price.toLocaleString('en-IN', { maximumFractionDigits: 0 })}`;
                            if (comparePrice > price) {
                                priceLabel.insertAdjacentHTML('beforeend', ` <s>₹${comparePrice.toLocaleString('en-IN', { maximumFractionDigits: 0 })}</s>`);
                            }
                        }
                    }

                    if (stockPill && variant) {
                        stockPill.classList.toggle('out', !variant.available);
                        stockPill.textContent = variant.available
                            ? (variant.track_stock ? `${variant.stock_qty} unit piece` : 'Available')
                            : 'Out of stock';
                    }
                }

                applySelectedVariant();

                panel.querySelectorAll('.pc-option-btn[data-attribute]').forEach(button => {
                    button.addEventListener('click', event => {
                        event.preventDefault();
                        event.stopPropagation();

                        const attribute = button.dataset.attribute;
                        panel.querySelectorAll(`.pc-option-btn[data-attribute="${CSS.escape(attribute)}"]`).forEach(item => {
                            item.classList.toggle('active', item === button);
                        });

                        applySelectedVariant();
                    });
                });
            });
        });
    </script>
@endpush
