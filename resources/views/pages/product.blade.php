@extends('layouts.main')
@section('title', "NutriBuddy – India's #1 Kids Wellness Gummy")

@section('content')
    @php
        $variantProducts = $product->variants
            ->filter(fn($variant) => $variant->is_active && !empty($variant->attributes))
            ->values();
        $defVariant =
            $variantProducts->firstWhere('is_default', true) ?:
            $variantProducts->first() ?:
            $product->variants->first();

        $initialPrice = $defVariant ? $defVariant->display_price : $product->display_price;
        $initialComparePrice = $defVariant
            ? $defVariant->display_compare_price ?? 0
            : $product->display_compare_price ?? 0;

        $defAge = $product->age_group ?: $defVariant->attributes['Age Group'] ?? '2–17 Yrs';
        $defPack = $product->pack_size ?: $defVariant->attributes['Pack Size'] ?? '30 Gummies';
        $defFlavour = $product->flavor ?: $defVariant->attributes['Flavour'] ?? '';
        $variantAttributeGroups = [];
        foreach ($variantProducts as $variant) {
            foreach ($variant->attributes ?? [] as $name => $value) {
                $variantAttributeGroups[$name] ??= [];
                if ($value !== '' && !in_array($value, $variantAttributeGroups[$name], true)) {
                    $variantAttributeGroups[$name][] = $value;
                }
            }
        }
        $initialSelectedAttributes = $defVariant?->attributes ?? [];
        $frontendVariants = $variantProducts
            ->map(function ($variant) use ($product) {
                $price = (float) $variant->display_price;
                $comparePrice = (float) ($variant->display_compare_price ?? 0);
                $stockQty = (int) ($variant->inventory?->stock_qty ?? 0);
                return [
                    'id' => $variant->id,
                    'name' => $variant->name,
                    'sku' => $variant->sku,
                    'attributes' => $variant->attributes ?? [],
                    'price' => $price,
                    'compare_price' => $comparePrice,
                    'save_amount' => max(0, $comparePrice - $price),
                    'discount_percent' =>
                        $comparePrice > $price ? round((($comparePrice - $price) / $comparePrice) * 100) : 0,
                    'coins' => !empty($product->coins_reward)
                        ? (int) $product->coins_reward
                        : (int) round($price * 0.05),
                    'stock_qty' => $stockQty,
                    'track_stock' => (bool) ($variant->inventory?->track_stock ?? false),
                    'is_in_stock' => (bool) ($variant->inventory?->is_in_stock ?? true),
                    'available' =>
                        !$variant->inventory?->track_stock ||
                        (($variant->inventory?->is_in_stock ?? true) && $stockQty > 0),
                ];
            })
            ->values();
    @endphp

    <style>
        .variant-group-row.d-none {
            display: none !important;
        }

        .variant-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            align-items: flex-start;
            margin-bottom: 25px;
        }

        .variant-block {
            flex: 0 0 auto;
            width: fit-content;
        }

        .variant-label {
            margin-bottom: 8px;
            font-weight: 700;
            color: #444;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .pdp-variant-panel {
            margin: 22px 0 24px;
            padding: 18px;
            border: 1px solid rgba(20, 126, 89, 0.12);
            border-radius: 22px;
            box-shadow: 0 12px 28px rgba(40, 89, 64, 0.06);
        }

        .pdp-variant-head {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: flex-start;
            margin-bottom: 14px;
        }

        .pdp-variant-title {
            margin: 0;
            color: var(--dk);
            font-family: 'Nunito', sans-serif;
            font-size: 1rem;
            font-weight: 900;
        }

        .pdp-variant-sub {
            margin-top: 3px;
            color: #6c6680;
            font-size: 0.82rem;
            font-weight: 700;
        }

        .pdp-variant-sku {
            flex: 0 0 auto;
            padding: 7px 12px;
            border-radius: 999px;
            background: rgba(255, 214, 0, 0.16);
            color: #865b00;
            font-size: 0.74rem;
            font-weight: 900;
        }

        .pdp-variant-groups {
            display: grid;
            gap: 16px;
        }

        .pdp-option-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .pdp-option-btn {
            min-height: 44px;
            border: 2px solid rgba(53, 158, 111, 0.16);
            border-radius: 14px;
            background: #fff;
            color: #353047;
            padding: 0 15px;
            font-weight: 900;
            cursor: pointer;
            transition: transform .18s ease, border-color .18s ease, box-shadow .18s ease, background .18s ease;
        }

        .pdp-option-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            border-color: rgba(53, 158, 111, 0.42);
            box-shadow: 0 10px 20px rgba(53, 158, 111, 0.1);
        }

        .pdp-option-btn.active {
            color: var(--pk);
            background: var(--pkl);
            border: 2px solid var(--pk);
        }

        .pdp-option-btn:disabled {
            cursor: not-allowed;
            opacity: 0.45;
            text-decoration: line-through;
        }

        .pdp-variant-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 14px;
        }

        .pdp-stock-pill,
        .pdp-selected-pill {
            display: inline-flex;
            align-items: center;
            min-height: 34px;
            padding: 0 12px;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 900;
        }

        .pdp-stock-pill {
            background: #e8f9f1;
            color: #00885d;
        }

        .pdp-stock-pill.out {
            background: #fff0ee;
            color: #d02f1f;
        }

        .pdp-selected-pill {
            background: #f6f2ff;
            color: #6750a4;
        }

        .btn-cart:disabled,
        .btn-buy:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .review-img-thumb {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 18px;
            border: 1px solid #f0f0f0;
            margin-top: 15px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .review-img-thumb:hover {
            transform: scale(1.02);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .star-opt {
            transition: all 0.2s ease;
            display: inline-block;
        }

        .star-opt:hover {
            transform: scale(1.3) rotate(8deg);
            color: #FFD700 !important;
        }

        .review-verified-badge {
            background: #E8F9F1;
            color: #00A87A;
            padding: 4px 10px;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-top: 6px;
            letter-spacing: 0.3px;
        }

        .review-verified-badge i {
            font-size: 0.8rem;
        }

        .wrev-card {
            background: #fff;
            border: 1px solid #f2f2f2;
            border-radius: 24px;
            padding: 30px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .wrev-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.06);
            border-color: #eee;
        }

        .rbar-fill {
            height: 100%;
            border-radius: 50px;
            transition: width 1s ease-in-out;
        }

        .pdp-description-section {
            padding: 34px 5% 22px;
        }

        .pdp-description-wrap {
            max-width: 1240px;
            margin: 0 auto;
            padding: 38px 42px;
            border-radius: 32px;
            background: linear-gradient(180deg, #fffdf7 0%, #ffffff 100%);
            border: 1px solid rgba(255, 196, 0, 0.18);
            box-shadow: 0 16px 40px rgba(30, 24, 64, 0.06);
        }

        .pdp-description-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 14px;
            padding: 8px 16px;
            border-radius: 999px;
            background: rgba(255, 214, 0, 0.14);
            color: #8f5b00;
            font-family: 'Nunito', sans-serif;
            font-size: 0.74rem;
            font-weight: 900;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .pdp-description-title {
            margin: 0 0 14px;
            color: var(--dk);
            font-family: 'Fredoka One', cursive;
            font-size: clamp(1.9rem, 3vw, 2.8rem);
            line-height: 1.15;
        }

        .pdp-description-intro {
            max-width: 780px;
            margin: 0 0 24px;
            color: #5f5877;
            font-family: 'DM Sans', sans-serif;
            font-size: 1rem;
            line-height: 1.8;
        }

        .pdp-description-copy.is-html {
            column-count: 1;
        }

        .pdp-description-copy > * {
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .pdp-description-columns {
            display: grid;
            grid-template-columns: 1fr;
            gap: 8px;
        }

        .pdp-description-column {
            min-width: 0;
        }

        .pdp-description-copy p {
            break-inside: avoid;
            margin: 0 0 18px;
            color: #463f61;
            font-family: 'DM Sans', sans-serif;
            font-size: 1rem;
            line-height: 1.92;
        }

        @media (max-width: 900px) {
            .pdp-description-wrap {
                padding: 30px 24px;
                border-radius: 24px;
            }

        }

        @media (max-width: 640px) {
            .pdp-description-section {
                padding: 24px 4% 14px;
            }

            .pdp-description-title {
                font-size: 1.7rem;
            }

            .pdp-description-intro,
            .pdp-description-copy p {
                font-size: 0.95rem;
                line-height: 1.8;
            }
        }

        .ps-problems-section {
            padding: 88px 5% 76px;
            background:
                radial-gradient(circle at 8% 14%, rgba(255, 77, 143, 0.12), transparent 28%),
                radial-gradient(circle at 92% 8%, rgba(124, 58, 237, 0.1), transparent 30%),
                linear-gradient(180deg, #fff8fb 0%, #f8f3ff 52%, #fffdf7 100%);
        }

        .ps-problems-section .ps-inner {
            max-width: 1220px;
        }

        .ps-problems-section .ps-header {
            max-width: 780px;
            margin: 0 auto 44px;
            text-align: center;
        }

        .ps-problems-section .eyebrow {
            padding: 9px 18px;
            border: 1px solid rgba(255, 77, 143, 0.18);
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.78);
            box-shadow: 0 12px 30px rgba(30, 24, 64, 0.06);
        }

        .ps-problems-section .eyebrow::before,
        .ps-problems-section .eyebrow::after {
            display: none;
        }

        .ps-problems-section .ps-title {
            margin-bottom: 14px;
            font-size: clamp(2rem, 4.4vw, 3.45rem);
            letter-spacing: 0;
        }

        .ps-problems-section .ps-title .acc {
            color: var(--pk);
        }

        .ps-problems-section .ps-title .acc2 {
            color: var(--mn);
        }

        .ps-problems-section .ps-sub {
            max-width: 650px;
            color: #625b76;
            font-size: 1rem;
        }

        .ps-problems-section .problem-grid {
            counter-reset: problem-card;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 22px;
            margin-bottom: 50px;
            align-items: stretch;
        }

        .ps-problems-section .prob-card {
            --prob-accent: var(--pk);
            --prob-soft: var(--pkl);
            position: relative;
            display: grid;
            grid-template-rows: 182px auto 1fr;
            min-height: 382px;
            overflow: hidden;
            padding: 0;
            border: 1px solid rgba(30, 24, 64, 0.08);
            border-radius: 24px;
            background: #fff;
            box-shadow: 0 18px 44px rgba(30, 24, 64, 0.08);
            transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
        }

        .ps-problems-section .prob-card.pc2 {
            --prob-accent: var(--or);
            --prob-soft: var(--orl);
        }

        .ps-problems-section .prob-card.pc3 {
            --prob-accent: var(--pu);
            --prob-soft: var(--pul);
        }

        .ps-problems-section .prob-card::before {
            content: '';
            position: absolute;
            inset: 0 0 auto 0;
            z-index: 2;
            height: 5px;
            border-radius: 0;
            opacity: 1;
            background: linear-gradient(90deg, var(--prob-accent), var(--ye));
        }

        .ps-problems-section .prob-card::after {
            counter-increment: problem-card;
            content: "0" counter(problem-card);
            position: absolute;
            top: 16px;
            left: 16px;
            z-index: 3;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 46px;
            height: 34px;
            padding: 0 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.9);
            color: var(--prob-accent);
            font-family: 'Nunito', sans-serif;
            font-size: 0.78rem;
            font-weight: 900;
            box-shadow: 0 10px 24px rgba(30, 24, 64, 0.12);
        }

        .ps-problems-section .prob-card:hover {
            transform: translateY(-8px);
            border-color: rgba(255, 77, 143, 0.16);
            box-shadow: 0 26px 58px rgba(30, 24, 64, 0.12);
        }

        .ps-problems-section .prob-icon {
            width: 100%;
            height: 182px;
            margin: 0;
            border-radius: 0;
            background: var(--prob-soft);
            overflow: hidden;
            box-shadow: none;
            transform: none;
        }

        .ps-problems-section .prob-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transform: scale(1.02);
            transition: transform 0.35s ease;
        }

        .ps-problems-section .prob-card:hover .prob-icon {
            transform: none;
        }

        .ps-problems-section .prob-card:hover .prob-icon img {
            transform: scale(1.08);
        }

        .ps-problems-section .prob-name {
            margin: 0;
            padding: 22px 22px 0;
            color: var(--dk);
            font-family: 'Nunito', sans-serif;
            font-size: 1.06rem;
            font-weight: 900;
            line-height: 1.3;
        }

        .ps-problems-section .prob-text {
            margin: 0;
            padding: 10px 22px 24px;
            color: #625b76;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.94rem;
            line-height: 1.72;
        }

        .ps-problems-section .ps-divider {
            margin: 46px auto 0;
        }

        .ps-problems-section .div-badge {
            background: linear-gradient(135deg, var(--mn), #00a872);
            box-shadow: 0 12px 28px rgba(0, 214, 143, 0.22);
        }

        @media (max-width: 1024px) {
            .ps-problems-section .problem-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .ps-problems-section {
                padding: 62px 4% 56px;
            }

            .ps-problems-section .ps-header {
                margin-bottom: 30px;
            }

            .ps-problems-section .problem-grid {
                grid-template-columns: 1fr;
                gap: 18px;
            }

            .ps-problems-section .prob-card {
                grid-template-rows: 166px auto 1fr;
                min-height: 0;
                border-radius: 20px;
            }

            .ps-problems-section .prob-icon {
                height: 166px;
            }

            .ps-problems-section .prob-name {
                padding: 18px 18px 0;
                font-size: 1rem;
            }

            .ps-problems-section .prob-text {
                padding: 9px 18px 20px;
                font-size: 0.9rem;
            }
        }
    </style>

    <div class="pdp-hero">
        <!-- LEFT: Gallery -->
        <div class="pdp-gallery">
            <div class="main-img-wrap">
                @if ($product->is_featured)
                    <div class="badge-bestseller">Best Seller</div>
                @endif
                @if ($product->compare_at_price > $product->base_price)
                    @php
                        $discount = round(
                            (($product->compare_at_price - $product->base_price) / $product->compare_at_price) * 100,
                        );
                    @endphp
                    <div class="badge-discount" id="pdpDiscountBadge">{{ $discount }}% OFF</div>
                @else
                    <div class="badge-discount d-none" id="pdpDiscountBadge"></div>
                @endif

                <div class="p-image" style="animation:floatY 4s ease-in-out infinite;display:block;line-height:1">
                    @if ($product->primaryImage)
                        <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" alt="{{ $product->name }}"
                            id="mainPdpImage">
                    @else
                        <img src="{{ asset('img/product2.png') }}" alt="{{ $product->name }}" id="mainPdpImage">
                    @endif
                </div>
            </div>
            <div class="thumb-row">
                @foreach ($product->images as $image)
                    <div class="thumb {{ $image->is_primary ? 'active' : '' }}"
                        onclick="changePdpImage(this, '{{ asset('storage/' . $image->image_path) }}')">
                        <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $product->name }}">
                    </div>
                @endforeach
                @if ($product->images->count() == 0)
                    <div class="thumb active"> <img src="{{ asset('img/product2.png') }}" alt=""></div>
                    <div class="thumb"> <img src="{{ asset('img/p1.jpeg') }}" alt=""></div>
                @endif
            </div>
        </div>

        <!-- RIGHT: Info -->
        <div class="pdp-info">
            <div class="pdp-cat">{{ $product->category->name ?? 'Immunity & Growth' }} · <span id="pdpTopAge">Kids
                    {{ $defAge }}</span></div>
            <h1 class="pdp-name">{{ $product->name }}</h1>
            <div class="pdp-rating">
                <div class="stars">
                    @php
                        $activeReviewsCount = $product->reviews->where('is_active', true)->count();
                        $rating =
                            $activeReviewsCount > 0 ? $product->reviews->where('is_active', true)->avg('rating') : 4.9;
                    @endphp
                    @for ($i = 0; $i < 5; $i++)
                        {{ $i < $rating ? '★' : '☆' }}
                    @endfor
                </div>
                <div class="rating-val">{{ number_format($rating, 1) }}</div>
                <div class="rating-divider"></div>
                <div class="rating-count">{{ $activeReviewsCount > 0 ? number_format($activeReviewsCount) : '2,841' }}
                    Verified Reviews</div>
            </div>

            <!-- Price -->
            <div class="price-box">
                <div class="price-row">
                    <div class="price-now" id="pdpPriceNow">₹{{ number_format($initialPrice, 0) }}</div>
                    @if ($initialComparePrice > $initialPrice)
                        <div class="price-old" id="pdpPriceOld">₹{{ number_format($initialComparePrice, 0) }}</div>
                        @php $initialDiscount = round((($initialComparePrice - $initialPrice) / $initialComparePrice) * 100); @endphp
                        <div class="price-save" id="pdpPriceSave">Save
                            ₹{{ number_format($initialComparePrice - $initialPrice, 0) }} ({{ $initialDiscount }}% Off)
                        </div>
                    @else
                        <div class="price-old d-none" id="pdpPriceOld"></div>
                        <div class="price-save d-none" id="pdpPriceSave"></div>
                    @endif
                </div>
                <div class="price-note">Inclusive of all taxes · Free shipping on this order</div>
                <div class="cashback-row">
                    <span>🪙</span>
                    <span id="pdpCashback">Get
                        {{ !empty($product->coins_reward) ? $product->coins_reward : round($initialPrice * 0.05) }} NB
                        Coins on this purchase!</span>
                </div>
            </div>

            @if ($variantProducts->isNotEmpty() && !empty($variantAttributeGroups))
                <div class="pdp-variant-panel" id="pdpVariantPanel">
                    <div class="pdp-variant-head">
                        <div>
                            <h3 class="pdp-variant-title">Choose Your Option</h3>
                            <div class="pdp-variant-sub">Pick the exact flavour, pack, or size before adding to cart.</div>
                        </div>
                    </div>

                    <div class="pdp-variant-groups">
                        @foreach ($variantAttributeGroups as $attributeName => $values)
                            <div class="variant-block">
                                <div class="variant-label">{{ $attributeName }}</div>
                                <div class="pdp-option-row" data-attribute-group="{{ $attributeName }}">
                                    @foreach ($values as $value)
                                        <button type="button"
                                            class="pdp-option-btn {{ ($initialSelectedAttributes[$attributeName] ?? null) === $value ? 'active' : '' }}"
                                            data-attribute="{{ $attributeName }}" data-value="{{ $value }}">
                                            {{ $value }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="pdp-variant-meta">
                        <span class="pdp-stock-pill" id="pdpVariantStock">Checking stock</span>
                        <span class="pdp-selected-pill" id="pdpVariantSelected">{{ $defVariant?->name }}</span>
                    </div>
                </div>
            @else
                <div class="variant-container">
                    @if ($defFlavour)
                        <div class="variant-block">
                            <div class="variant-label">Flavour:</div>
                            <div class="variant-row">
                                <div class="vopt active">{{ $defFlavour }}</div>
                            </div>
                        </div>
                    @endif

                    @if ($defPack)
                        <div class="variant-block">
                            <div class="variant-label">Pack Size:</div>
                            <div class="variant-row">
                                <div class="vopt active">{{ $defPack }}</div>
                            </div>
                        </div>
                    @endif

                    @if ($defAge)
                        <div class="variant-block">
                            <div class="variant-label">Age Group:</div>
                            <div class="variant-row">
                                <div class="vopt active">{{ $defAge }}</div>
                            </div>
                        </div>
                    @endif

                    @if ($product->dosage)
                        <div class="variant-block">
                            <div class="variant-label">Dosage:</div>
                            <div class="variant-row">
                                <div class="vopt active">{{ $product->dosage }}</div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <div class="variant-block">
                <div class="variant-label">{{ $product->name }} Features </div>
                <div class="variant-row" id="flavorRow">
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
                    @endphp

                    @if (is_array($tags) && count($tags) > 0)
                        @foreach ($tags as $tag)
                            <div class="flavor-opt active">
                                <div class="flavor-emoji">
                                    @if (!empty($tag['icon']))
                                        @php
                                            $isFilePath = str_contains($tag['icon'], 'tags/');
                                        @endphp
                                        @if ($isFilePath)
                                            <img src="{{ asset('storage/' . $tag['icon']) }}" alt=""
                                                style="width: 28px; height: 28px; object-fit: contain;">
                                        @else
                                            <span
                                                style="font-size: 28px; display: inline-block;">{{ $tag['icon'] }}</span>
                                        @endif
                                    @else
                                        <span style="font-size: 28px; display: inline-block;">✨</span>
                                    @endif
                                </div>
                                <div class="flavor-name">{!! nl2br(e(\Illuminate\Support\Str::limit($tag['text'] ?? '', 15))) !!}</div>
                            </div>
                        @endforeach
                    @else
                        <!-- Fallback static features if no tags -->
                        <div class="flavor-opt active">
                            <div class="flavor-emoji"> <img src="{{ asset('img/sugar.png') }}" alt=""></div>
                            <div class="flavor-name">No Added Sugar</div>
                        </div>
                        <div class="flavor-opt active">
                            <div class="flavor-emoji"> <img src="{{ asset('img/no-preservatives.png') }}"
                                    alt="">
                            </div>
                            <div class="flavor-name">No Preservatives</div>
                        </div>
                        <div class="flavor-opt active">
                            <div class="flavor-emoji"> <img src="{{ asset('img/no-artificial-colours.png') }}"
                                    alt=""> </div>
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
                    @endif
                </div>
            </div>

            <!-- Quick Specs: Pack Size & Age -->
            <!-- <div class="pdp-specs-row" style="display:flex;gap:20px;margin: 20px 0;padding:15px;background:#f9f9f9;border-radius:12px;border:1px solid #eee;">
                            <div class="spec-item">
                                <div style="font-size:.72rem;color:#888;text-transform:uppercase;font-weight:800;margin-bottom:4px;letter-spacing:0.5px;">Pack Size</div>
                                <div id="pdpPackSize" style="font-size:1.05rem;color:var(--dk);font-weight:800">{{ $defPack }}</div>
                            </div>
                            <div style="width:1px;background:#ddd"></div>
                            <div class="spec-item">
                                <div style="font-size:.72rem;color:#888;text-transform:uppercase;font-weight:800;margin-bottom:4px;letter-spacing:0.5px;">Age Group</div>
                                <div id="pdpAgeGroup" style="font-size:1.05rem;color:var(--dk);font-weight:800">{{ $defAge }}</div>
                            </div>
                        </div> -->


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
                <button class="btn-cart" id="pdpAddToCartBtn" onclick="handleAddToCart('{{ $product->id }}', this)">Add
                    to Cart</button>
                <button class="btn-buy" id="pdpBuyNowBtn" onclick="handleBuyNow('{{ $product->id }}', this)">Buy
                    Now</button>
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
                        $features = $product->short_description ? explode("\n", $product->short_description) : [];
                        $features = array_filter(array_map('trim', $features));
                    @endphp
                    @if (count($features) > 0)
                        @foreach (array_slice($features, 0, 6) as $feature)
                            <li>
                                <div class="hl-dot"></div>{{ preg_replace('/^[•\-\*]\s*/', '', $feature) }}
                            </li>
                        @endforeach
                    @else
                        {{-- Fallback --}}
                        <li>
                            <div class="hl-dot"></div>Ashwagandha (KSM-66®) + Vitamin D3 + Zinc — clinically proven formula
                        </li>
                        <li>
                            <div class="hl-dot"></div>Supports immunity, height, bone density & overall energy in one gummy
                        </li>
                        <li>
                            <div class="hl-dot"></div>Zero gelatin · 100% Vegetarian · No artificial colours or flavours
                        </li>
                        <li>
                            <div class="hl-dot"></div>Tastes so good kids ask for it every morning — guaranteed!
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>



    <!-- ════════════════════════════════════════════════
                         PRODUCT DESCRIPTION SECTION
                    ════════════════════════════════════════════════ -->
    <!-- Product Description Section -->
    @php
        $productDescription = trim((string) ($product->description ?? ''));
        $productDescriptionHasHtml = $productDescription !== strip_tags($productDescription);
        $productDescriptionBlocks = collect(
            preg_split('/\R{2,}|\R/', $productDescription, -1, PREG_SPLIT_NO_EMPTY)
        )
            ->map(fn ($block) => trim($block))
            ->filter()
            ->values();
        $productDescriptionSplitAt = (int) ceil($productDescriptionBlocks->count() / 2);
    @endphp

    @if ($productDescription !== '')
        <section class="pdp-description-section">
            <div class="pdp-description-wrap">
                <div class="pdp-description-label">Product Details</div>
                @if ($productDescriptionHasHtml)
                    <div class="pdp-description-copy is-html">
                        {!! $productDescription !!}
                    </div>
                @else
                    <div class="pdp-description-columns">
                        <div class="pdp-description-column pdp-description-copy">
                            @foreach ($productDescriptionBlocks->slice(0, $productDescriptionSplitAt) as $block)
                                <p>{{ $block }}</p>
                            @endforeach
                        </div>
                        <div class="pdp-description-column pdp-description-copy">
                            @foreach ($productDescriptionBlocks->slice($productDescriptionSplitAt) as $block)
                                <p>{{ $block }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </section>
    @endif

    <!-- ══ DESCRIPTION & DETAILS ══ -->

    <!-- ══ HOW IT TRANSFORMS ══ -->
    <section class="section-wrap transform-section reveal">
        <div style="max-width:1200px;margin:0 auto;">
            <span class="sec-eye">Real Results</span>
            <h2 class="sec-title">Watch Your Child <span class="acc">Transform</span></h2>
            <p class="sec-sub">90 days of {{ $product->name }} — visible, measurable, life-changing results reported by
                thousands of
                parents.</p> visible, measurable, life-changing results reported by thousands of
            parents.</p>
            <div class="transform-grid">
                <div class="transform-visual">
                    <img src="/img/child-iamges.png" alt="" loading="lazy" decoding="async">
                    <!-- <div
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
                                </div> -->
                </div>
                <div class="transform-list">
                    <div class="tr-item">
                        <div class="tr-icon" style="background:rgba(255,77,143,.12)"><img src="/img/immune.png"
                                alt=""></div>
                        <div class="tr-body">
                            <div class="tr-title">Stronger Immunity</div>
                            <div class="tr-desc">Kids fall sick less often. Parents report 60% fewer sick days in the first
                                3 months
                                of consistent use.</div>
                            <div class="tr-week">Visible by Week 3</div>
                        </div>
                    </div>
                    <div class="tr-item">
                        <div class="tr-icon" style="background:rgba(0,191,255,.12)"><img src="/img/check-height.png"
                                alt=""></div>
                        <div class="tr-body">
                            <div class="tr-title">Height & Growth Spurt</div>
                            <div class="tr-desc">Ashwagandha + Zinc work synergistically to support natural growth hormone
                                function
                                and bone density.</div>
                            <div class="tr-week">Visible by Week 8</div>
                        </div>
                    </div>
                    <div class="tr-item">
                        <div class="tr-icon" style="background:rgba(0,214,143,.12)"><img src="/img/energy-drink.png"
                                alt=""></div>
                        <div class="tr-body">
                            <div class="tr-title">All-Day Energy</div>
                            <div class="tr-desc">No more afternoon crashes. Kids stay energetic and active through school,
                                play, and
                                evening activities.</div>
                            <div class="tr-week">Visible by Week 2</div>
                        </div>
                    </div>
                    <div class="tr-item">
                        <div class="tr-icon" style="background:rgba(255,214,0,.15)"><img src="/img/mental-health.png"
                                alt=""></div>
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
                            <div class="feat-item-icon" style="background:var(--mnl)"><img src="/img/vegan-1.png"
                                    alt=""></div>
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
                            <div class="feat-item-icon" style="background:var(--pkl)"><img src="/img/sug-1.png"
                                    alt=""></div>
                            <div>
                                <div class="feat-item-title">No Refined Sugar</div>
                                <div class="feat-item-desc">We sweeten with Stevia + monk fruit extract — giving a
                                    naturally sweet taste
                                    with zero impact on blood sugar. Kids get the yummy without the sugar crash or tooth
                                    decay.</div>
                            </div>
                        </div>

                        <div class="feat-item">
                            <div class="feat-item-icon" style="background:var(--yel)"><img src="/img/pro-1.png"
                                    alt=""></div>
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
                    <div class="comp-title">NutriBuddy vs. Other Brands</div>
                    <table class="comp-table">
                        <thead>
                            <tr>
                                <th></th>
                                <th class="comp-us-head">NutriBuddy</th>
                                <th style="color:#aaa">Brand 1</th>
                                <th style="color:#aaa">Brand 2</th>
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
    <section class="ps-section ps-problems-section">
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
            <!-- <div class="block-label reveal">
                            <div class="blabel bl-prob">😟 Today's Challenges</div>
                            <div class="bline"></div>
                        </div> -->

            <div class="problem-grid">
                <div class="prob-card pc1 reveal d1">
                    <div class="prob-icon pi1"><img src="/img/weak-boy.JPG" alt="" loading="lazy" decoding="async"></div>
                    <div class="prob-name">Vitamin & Mineral Deficiency</div>
                    <p class="prob-text">Processed food strips away nutrients. 80% of Indian kids are Vitamin D deficient —
                        affecting bones, immunity & mood.</p>
                </div>
                <div class="prob-card pc2 reveal d2">
                    <div class="prob-icon pi2"><img src="/img/BUSY-P.jpg" alt="" loading="lazy" decoding="async"></div>
                    <div class="prob-name">Busy Parent, Skipped Nutrition</div>
                    <p class="prob-text">Between work and school runs, balanced meals slip through the cracks. Convenience
                        wins
                        over nutrition — every single day.</p>
                </div>
                <div class="prob-card pc3 reveal d3">
                    <div class="prob-icon pi3"><img src="/img/hungry-boy.jpg" alt="" loading="lazy" decoding="async"></div>
                    <div class="prob-name">Junk Food Addiction</div>
                    <p class="prob-text">Pizza, chips, sugary drinks — kids crave them and get them. High calories, zero
                        nutrition, and taste buds that reject healthy food.</p>
                </div>
                <div class="prob-card pc1 reveal d1">
                    <div class="prob-icon pi4"><img src="/img/indoor.jpg" alt="" loading="lazy" decoding="async"></div>
                    <div class="prob-name">Less Outdoor Play, More Screens</div>
                    <p class="prob-text">No sunlight means no Vitamin D. No movement means weak bones and low immunity —
                        visible
                        on the outside, starting from within.</p>
                </div>
                <div class="prob-card pc2 reveal d2">
                    <div class="prob-icon pi5"><img src="/img/test-product.jpg" alt="" loading="lazy" decoding="async"></div>
                    <div class="prob-name">Adulterated Food</div>
                    <p class="prob-text">Preservatives, artificial colors, hidden additives — what's really in your child's
                        food?
                        Nobody gives you a guarantee.</p>
                </div>
                <div class="prob-card pc3 reveal d3">
                    <div class="prob-icon pi6"><img src="/img/illness.jpg" alt="" loading="lazy" decoding="async"></div>
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
            <!-- <div class="block-label reveal">
                            <div class="blabel bl-sol">✅ NutriBuddy Solution</div>
                            <div class="bline g"></div>
                        </div> -->


    </section>
    <section>
        <!-- HERO -->
        <div class="sol-hero reveal">
            <div class="sol-hero-text">
                <img src="/img/posr.png" alt="">

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






            <!-- CTA -->
            <!-- <div class="ps-cta reveal">
                                <div class="cta-inner">
                                    <span class="cta-emoji"><img src="/img/nutrigummi.png" alt=""></span>
                                    <h3 class="cta-title">Give Your Child the Best Start</h3>
                                    <p class="cta-sub">Take a 2-minute quiz and get a FREE personalized diet chart — crafted by
                                        certified
                                        Ayurvedic nutritionists. No sign-up, no cost.</p>
                                    <div class="cta-btns">
                                        <a class="btn-main" href="#"> Shop NutriBuddy Now</a>
                                        <a class="btn-ghost" href="#">📋 Get Free Diet Chart →</a>
                                    </div>
                                </div>
                            </div> -->

        </div>
    </section>
    <section class="ps-section">
        <!-- EQUATION -->
        <div class="eq-card reveal">
            <div class="eq-lbl">✨ The NutriBuddy Formula</div>
            <div class="eq-wrap">
                <div class="eq-item">
                    <div class="eq-icon ei1"><img src="/img/natural-organic.png" alt=""></div>
                    <div class="eq-nm">Ayurvedic Wisdom</div>
                </div>
                <div class="eq-op">+</div>
                <div class="eq-item">
                    <div class="eq-icon ei2"><img src="/img/observation.png" alt=""></div>
                    <div class="eq-nm">Modern Science</div>
                </div>
                <div class="eq-op">+</div>
                <div class="eq-item">
                    <div class="eq-icon ei3"><img src="/img/tongue.png" alt=""></div>
                    <div class="eq-nm">Kid-Approved Taste</div>
                </div>
                <div class="eq-op">+</div>
                <div class="eq-item">
                    <div class="eq-icon ei4"><img src="/img/pediatrician.png" alt=""></div>
                    <div class="eq-nm">Pediatrician Verified</div>
                </div>
                <div class="eq-eq">=</div>
                <div class="eq-result">
                    <div class="eq-res-icon"><img src="/img/product2.png" alt=""></div>
                    <div class="eq-res-nm">NutriBuddy</div>
                </div>
            </div>
        </div>

    </section>

    <!-- ════════════════════════════════════════════════
                         NUTRIBUDDY INGREDIENT SECTION
                    ════════════════════════════════════════════════ -->
    @if ($product->ingredients->isNotEmpty())
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
                    <span class="nb-acc-ye">{{ $product->name }}</span> <span class="nb-acc-pk">Gummy?</span>
                </h2>
                <p class="nb-ing-sub">Every single ingredient explained — from ancient Ayurvedic herbs to essential
                    vitamins
                    and
                    minerals. Click any ingredient to learn its full story.</p>
            </div>

            <!-- ── Category Filter (desktop) ── -->
            @php
                $productIngredients = $product->ingredients ?? collect();

                // Build category filters
                $categoryFilters = $productIngredients
                    ->groupBy(function ($ing) {
                        return $ing->category->name ?? 'General';
                    })
                    ->map(function ($group, $name) {
                        return [
                            'key' => \Illuminate\Support\Str::slug($name),
                            'name' => $name,
                            'count' => $group->count(),
                            'dot_color' => 'rgba(0,214,143,.6)',
                        ];
                    })
                    ->values();

                $totalIngredientCount = $productIngredients->count();

                // Build ingredient items for JS
                $ingredientItems = $productIngredients
                    ->map(function ($ing) {
                        return [
                            'id' => $ing->id,
                            'name' => $ing->main_heading,
                            'shortName' => $ing->short_heading,
                            'cat' => \Illuminate\Support\Str::slug($ing->category->name ?? 'general'),
                            'catLabel' => $ing->category->name ?? 'General',
                            'image' => $ing->icon_path
                                ? asset('storage/' . $ing->icon_path)
                                : asset('img/gradient1.webp'),
                            'latin' => $ing->dosage_heading_one ?? '',
                            'dosage' => $ing->dosage_heading_two ?? '',
                            'desc' => $ing->description ?? '',
                            'benefits' => $ing->benefits->pluck('heading')->toArray(),
                        ];
                    })
                    ->values();

                // Summary stats
                $ingredientSummaryStats = [
                    ['value' => $totalIngredientCount, 'label' => 'Active Ingredients', 'color' => '#00d68f'],
                    [
                        'value' => $productIngredients
                            ->where('category.name', '!=', null)
                            ->groupBy('ingredient_category_id')
                            ->count(),
                        'label' => 'Ingredient Categories',
                        'color' => '#ff8c00',
                    ],
                    ['value' => '100%', 'label' => 'Natural Sources', 'color' => '#00bfff'],
                    ['value' => '3rd Party', 'label' => 'Lab Tested', 'color' => '#ff4d8f'],
                ];
            @endphp
            <div class="nb-cat-row">
                <button class="nb-cat-pill nb-active" onclick="nbFilter('all',this)">
                    <span class="nb-cat-dot" style="background:rgba(255,255,255,.5)"></span>All
                    ({{ $totalIngredientCount }})
                </button>
                @foreach ($categoryFilters as $filter)
                    <button class="nb-cat-pill" onclick="nbFilter('{{ $filter['key'] }}',this)">
                        <span class="nb-cat-dot"
                            style="background:{{ $filter['dot_color'] }}"></span>{{ $filter['name'] }}
                        ({{ $filter['count'] }})
                    </button>
                @endforeach
            </div>

            <!-- ── Mobile Tabs ── -->
            <div class="nb-mobile-tabs" id="nbMobTabs">
                <button class="nb-mob-tab nb-sel-mob" onclick="nbMobFilter('all',this)">All
                    ({{ $totalIngredientCount }})</button>
                @foreach ($categoryFilters as $filter)
                    <button class="nb-mob-tab" onclick="nbMobFilter('{{ $filter['key'] }}',this)">{{ $filter['name'] }}
                        ({{ $filter['count'] }})
                    </button>
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
                        <p>Click any ingredient from the list on the left to discover its story, benefits, and why we chose
                            it
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
                        @if (!$loop->last)
                            <div class="nb-sdiv"></div>
                        @endif
                    @endforeach
                </div>
            </div>

            <script id="nbIngredientsData" type="application/json">@json($ingredientItems)</script>

        </section>
    @endif


    <!-- end ingredients -->



    <!-- ══════════════════════════════════════════
                             PARENT REVIEWS
                        ══════════════════════════════════════════ -->
    @include('partials.parent-reviews')

    <!-- ══════════════════════════════════════════
                             FAQ
                        ══════════════════════════════════════════ -->
    @include('partials.faq-section')

    <div class="newsletter reveal">
        <span class="sec-eye">Stay in the Loop</span>
        <h2 class="sec-title">Wellness Tips for Your Little Ones</h2>
        <p class="nl-sub">Join 25,000+ parents getting Ayurvedic parenting tips, exclusive discounts & early product access
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
        let selectedVariantId = '{{ $defVariant ? $defVariant->id : '' }}';
        const pdpVariants = @json($frontendVariants);
        const pdpSelectedAttributes = @json($initialSelectedAttributes);
        const pdpIsLoggedIn = @json(auth()->check());
        const pdpFallbackCartMeta = {
            product_name: @json($product->name),
            variant_name: '',
            image: @json($product->primaryImage ? asset('storage/' . $product->primaryImage->image_path) : asset('img/product2.png')),
            unit_price: Number(@json((float) $initialPrice)),
            product_url: @json(request()->path()),
        };

        function changePdpImage(el, src) {
            document.getElementById('mainPdpImage').src = src;
            document.querySelectorAll('.thumb').forEach(t => t.classList.remove('active'));
            el.classList.add('active');
        }

        function formatMoney(value) {
            return '\u20B9' + Number(value || 0).toLocaleString('en-IN', {
                maximumFractionDigits: 0
            });
        }

        function sameAttributes(variant, selected) {
            const keys = Object.keys(selected);
            if (!keys.length) return false;
            return keys.every(key => String(variant.attributes?.[key] ?? '') === String(selected[key] ?? ''));
        }

        function findMatchingVariant() {
            return pdpVariants.find(variant => sameAttributes(variant, pdpSelectedAttributes)) || null;
        }

        function updateVariantAvailability() {
            document.querySelectorAll('.pdp-option-btn').forEach(button => {
                const attribute = button.dataset.attribute;
                const value = button.dataset.value;
                const trial = {
                    ...pdpSelectedAttributes,
                    [attribute]: value
                };
                const possible = pdpVariants.some(variant => {
                    return Object.keys(trial).every(key => String(variant.attributes?.[key] ?? '') ===
                        String(trial[key] ?? ''));
                });
                button.disabled = !possible;
            });
        }

        function applyVariantToPage(variant) {
            const addBtn = document.getElementById('pdpAddToCartBtn');
            const buyBtn = document.getElementById('pdpBuyNowBtn');
            const stockEl = document.getElementById('pdpVariantStock');
            const selectedEl = document.getElementById('pdpVariantSelected');
            const skuEl = document.getElementById('pdpVariantSku');
            const priceNow = document.getElementById('pdpPriceNow');
            const priceOld = document.getElementById('pdpPriceOld');
            const priceSave = document.getElementById('pdpPriceSave');
            const discountBadge = document.getElementById('pdpDiscountBadge');
            const cashback = document.getElementById('pdpCashback');

            if (!variant) {
                selectedVariantId = '';
                if (!pdpVariants.length) {
                    if (stockEl) {
                        stockEl.textContent = 'In stock';
                        stockEl.classList.remove('out');
                    }
                    if (addBtn) addBtn.disabled = false;
                    if (buyBtn) buyBtn.disabled = false;
                    return;
                }
                if (stockEl) {
                    stockEl.textContent = 'Select available options';
                    stockEl.classList.add('out');
                }
                if (addBtn) addBtn.disabled = true;
                if (buyBtn) buyBtn.disabled = true;
                return;
            }

            selectedVariantId = variant.id;
            if (priceNow) priceNow.textContent = formatMoney(variant.price);
            if (priceOld) {
                priceOld.textContent = variant.compare_price > variant.price ? formatMoney(variant.compare_price) : '';
                priceOld.classList.toggle('d-none', !(variant.compare_price > variant.price));
            }
            if (priceSave) {
                priceSave.textContent = variant.compare_price > variant.price ?
                    `Save ${formatMoney(variant.save_amount)} (${variant.discount_percent}% Off)` :
                    '';
                priceSave.classList.toggle('d-none', !(variant.compare_price > variant.price));
            }
            if (discountBadge) {
                discountBadge.textContent = variant.discount_percent > 0 ? `${variant.discount_percent}% OFF` : '';
                discountBadge.classList.toggle('d-none', !(variant.discount_percent > 0));
            }
            if (cashback) cashback.textContent = `Get ${variant.coins} NB Coins on this purchase!`;
            if (skuEl) skuEl.textContent = `SKU: ${variant.sku}`;
            if (selectedEl) selectedEl.textContent = variant.name;
            if (stockEl) {
                stockEl.classList.toggle('out', !variant.available);
                if (variant.available) {
                    stockEl.textContent = variant.track_stock ? `${variant.stock_qty} in stock` : 'In stock';
                } else {
                    stockEl.textContent = 'Out of stock';
                }
            }
            if (addBtn) addBtn.disabled = !variant.available;
            if (buyBtn) buyBtn.disabled = !variant.available;
        }

        function addPdpGuestCartFallback(productId, quantity = 1, variantId = null) {
            const key = 'nb_pending_cart';
            const itemKey = String(Number(productId || 0));
            let items = [];

            try {
                const parsed = JSON.parse(localStorage.getItem(key) || '[]');
                items = Array.isArray(parsed) ? parsed : [];
            } catch (error) {
                items = [];
            }

            const activeVariant = pdpVariants.find(variant => String(variant.id) === String(variantId));
            const meta = {
                ...pdpFallbackCartMeta,
                variant_name: activeVariant?.name || '',
                unit_price: Number(activeVariant?.price || pdpFallbackCartMeta.unit_price || 0),
            };
            const found = items.find(item => String(Number(item.product_id || 0)) === itemKey);

            if (found) {
                found.quantity = Number(found.quantity || 0) + Number(quantity || 1);
                found.product_variant_id = variantId ? Number(variantId) : null;
                found.product_name = meta.product_name;
                found.variant_name = meta.variant_name;
                found.image = meta.image;
                found.unit_price = meta.unit_price;
                found.product_url = meta.product_url;
            } else {
                items.push({
                    product_id: Number(productId),
                    product_variant_id: variantId ? Number(variantId) : null,
                    quantity: Number(quantity || 1),
                    ...meta,
                });
            }

            localStorage.setItem(key, JSON.stringify(items));

            const count = items.reduce((sum, item) => sum + Number(item.quantity || 0), 0);
            const cartCount = document.getElementById('cartCount');
            if (cartCount) cartCount.textContent = String(count);
            return true;
        }

        function selectPdpOption(button) {
            const attribute = button.dataset.attribute;
            const value = button.dataset.value;
            pdpSelectedAttributes[attribute] = value;
            document.querySelectorAll('.pdp-option-btn').forEach(item => {
                if (item.dataset.attribute === attribute) {
                    item.classList.toggle('active', item === button);
                }
            });
            updateVariantAvailability();
            applyVariantToPage(findMatchingVariant());
        }

        async function handleAddToCart(productId, btn) {
            const variantId = selectedVariantId || null;
            if (pdpVariants.length && !variantId) {
                if (typeof nbToast === 'function') nbToast('Please choose a product option first.', 'error');
                return;
            }
            if (typeof window.addToCart === 'function') {
                if (btn) btn.disabled = true;
                const added = await window.addToCart(productId, 1, variantId, btn);
                if (btn) btn.disabled = false;
                if (added && typeof nbToast === 'function') {
                    nbToast('Product added to cart.', 'success');
                } else if (!added && !pdpIsLoggedIn && addPdpGuestCartFallback(productId, 1, variantId)) {
                    if (typeof nbToast === 'function') nbToast('Product added to cart.', 'success');
                }
            } else {
                console.warn('Global addToCart not found, using fallback');
                if (!pdpIsLoggedIn && addPdpGuestCartFallback(productId, 1, variantId)) {
                    if (typeof nbToast === 'function') nbToast('Product added to cart.', 'success');
                } else if (typeof nbToast === 'function') {
                    nbToast('Cart is still loading. Please try again.', 'warning');
                }
            }
        }

        async function handleBuyNow(productId, btn) {
            const variantId = selectedVariantId || null;
            if (pdpVariants.length && !variantId) {
                if (typeof nbToast === 'function') nbToast('Please choose a product option first.', 'error');
                return;
            }
            if (typeof window.addToCart === 'function') {
                if (btn) btn.disabled = true;
                const added = await window.addToCart(productId, 1, variantId, btn);
                if (btn) btn.disabled = false;
                if (added) {
                    window.location.href = "{{ route('checkout') }}";
                } else if (!pdpIsLoggedIn && addPdpGuestCartFallback(productId, 1, variantId)) {
                    window.location.href = "{{ route('checkout') }}";
                }
            } else {
                if (!pdpIsLoggedIn && addPdpGuestCartFallback(productId, 1, variantId)) {
                    window.location.href = "{{ route('checkout') }}";
                } else if (typeof nbToast === 'function') {
                    nbToast('Cart is still loading. Please try again.', 'warning');
                }
            }
        }

        // Star Rating Interaction
        document.querySelectorAll('.star-opt').forEach(star => {
            star.addEventListener('click', function() {
                const val = this.getAttribute('data-val');
                document.getElementById('ratingValue').value = val;

                // Color stars
                document.querySelectorAll('.star-opt').forEach(s => {
                    if (s.getAttribute('data-val') <= val) {
                        s.style.color = '#FFD700'; // Gold
                    } else {
                        s.style.color = '#ddd';
                    }
                });
            });

            star.addEventListener('mouseover', function() {
                const val = this.getAttribute('data-val');
                document.querySelectorAll('.star-opt').forEach(s => {
                    if (s.getAttribute('data-val') <= val) {
                        s.style.color = '#FFD700';
                    } else {
                        s.style.color = '#ddd';
                    }
                });
            });

            star.addEventListener('mouseout', function() {
                const val = document.getElementById('ratingValue').value;
                document.querySelectorAll('.star-opt').forEach(s => {
                    if (s.getAttribute('data-val') <= val) {
                        s.style.color = '#FFD700';
                    } else {
                        s.style.color = '#ddd';
                    }
                });
            });
        });

        // Default set 5 stars
        window.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.pdp-option-btn').forEach(button => {
                button.addEventListener('click', () => selectPdpOption(button));
            });
            updateVariantAvailability();
            if (pdpVariants.length) {
                applyVariantToPage(findMatchingVariant() || pdpVariants[0] || null);
            } else {
                applyVariantToPage(null);
            }

            const defaultVal = 5;
            document.querySelectorAll('.star-opt').forEach(s => {
                if (s.getAttribute('data-val') <= defaultVal) {
                    s.style.color = '#FFD700';
                }
            });

        });
    </script>
@endpush
