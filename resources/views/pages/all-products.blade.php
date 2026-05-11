@extends('layouts.main')
@section('title', 'All Products - NutriBuddy')

@push('styles')
    <style>
        .product-listing-section {
            opacity: 1;
            padding: 70px 6% 80px;
            transform: none;
            visibility: visible;
        }

        .product-listing-hero {
            background: #0d0028;
            overflow: hidden;
            padding: 100px 5% 60px;
            position: relative;
        }

        .product-listing-hero::before {
            animation: blobMorph 10s ease-in-out infinite;
            background: radial-gradient(circle, rgba(255, 77, 143, .12), transparent 70%);
            border-radius: 62% 38% 56% 44%/48% 62% 38% 52%;
            content: '';
            height: 560px;
            pointer-events: none;
            position: absolute;
            right: -120px;
            top: -160px;
            width: 560px;
        }

        .product-listing-hero::after {
            animation: blobMorph 14s ease-in-out infinite reverse;
            background: radial-gradient(circle, rgba(124, 58, 237, .09), transparent 70%);
            border-radius: 38% 62% 44% 56%/62% 38% 55% 45%;
            bottom: -80px;
            content: '';
            height: 380px;
            left: -80px;
            pointer-events: none;
            position: absolute;
            width: 380px;
        }

        .product-listing-hero-inner {
            margin: 0 auto;
            max-width: 900px;
            padding-top: 23px;
            position: relative;
            z-index: 2;
        }

        .product-listing-breadcrumb {
            align-items: center;
            color: #ffffff;
            display: flex;
            flex-wrap: wrap;
            font-size: .9rem;
            gap: 12px;
            margin-bottom: 24px;
        }

        .product-listing-breadcrumb a {
            color: var(--pk);
            font-weight: 700;
            text-decoration: none;
        }

        .product-listing-breadcrumb a:hover {
            text-decoration: underline;
        }

        .product-listing-hero-badge {
            background: var(--pkl);
            border-radius: 50px;
            color: var(--pk);
            display: inline-block;
            font-family: 'Nunito', sans-serif;
            font-size: .75rem;
            font-weight: 900;
            margin-bottom: 16px;
            padding: 8px 16px;
        }

        .product-listing-hero-title {
            color: var(--wh);
            font-family: 'Fredoka One', cursive;
            font-size: clamp(1.8rem, 5vw, 2.8rem);
            line-height: 1.3;
            margin-bottom: 20px;
        }

        .product-listing-hero-sub {
            color: #ffffff;
            font-size: 1rem;
            line-height: 1.7;
            margin: 0;
            max-width: 650px;
        }

        .shop-shell {
            display: grid;
            gap: 28px;
            grid-template-columns: minmax(260px, 310px) minmax(0, 1fr);
            margin: 42px auto 0;
            max-width: 1380px;
        }

        .shop-sidebar {
            align-self: start;
            background: rgba(255, 255, 255, .92);
            border: 2px solid rgba(255, 214, 232, .9);
            border-radius: 28px;
            box-shadow: 0 18px 44px rgba(255, 77, 143, .08);
            padding: 22px;
            position: sticky;
            top: 96px;
        }

        .shop-sidebar-head,
        .shop-toolbar {
            align-items: center;
            display: flex;
            gap: 12px;
            justify-content: space-between;
        }

        .shop-sidebar-title {
            color: var(--dk);
            font-family: 'Nunito', sans-serif;
            font-size: 1.05rem;
            font-weight: 900;
        }

        .shop-clear-btn,
        .shop-filter-toggle {
            align-items: center;
            border: 0;
            border-radius: 999px;
            cursor: pointer;
            display: inline-flex;
            font-family: 'Nunito', sans-serif;
            font-size: .78rem;
            font-weight: 900;
            justify-content: center;
            min-height: 38px;
            padding: 0 14px;
        }

        .shop-clear-btn {
            background: var(--pkl);
            color: var(--pk);
        }

        .shop-filter-toggle {
            background: var(--dk);
            color: #fff;
            display: none;
        }

        .shop-filter-group {
            border-top: 1px solid rgba(13, 0, 32, .08);
            margin-top: 20px;
            padding-top: 20px;
        }

        .shop-filter-label {
            color: var(--dk);
            font-family: 'Nunito', sans-serif;
            font-size: .82rem;
            font-weight: 900;
            letter-spacing: .08em;
            margin-bottom: 12px;
            text-transform: uppercase;
        }

        .shop-search {
            position: relative;
        }

        .shop-search input,
        .shop-select,
        .shop-price-input {
            background: #fff;
            border: 2px solid #f1e5ef;
            border-radius: 16px;
            color: var(--dk);
            font-family: 'DM Sans', sans-serif;
            min-height: 46px;
            outline: none;
            padding: 0 14px;
            width: 100%;
        }

        .shop-search input:focus,
        .shop-select:focus,
        .shop-price-input:focus {
            border-color: var(--pk);
            box-shadow: 0 0 0 4px rgba(255, 77, 143, .1);
        }

        .shop-category-list {
            display: grid;
            gap: 9px;
        }

        .shop-category-btn {
            align-items: center;
            background: #fff;
            border: 2px solid #f1e5ef;
            border-radius: 16px;
            color: var(--dk);
            cursor: pointer;
            display: flex;
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            justify-content: space-between;
            min-height: 48px;
            padding: 0 12px;
            text-align: left;
            transition: all .2s ease;
        }

        .shop-category-btn:hover,
        .shop-category-btn.is-active {
            background: var(--pkl);
            border-color: var(--pk);
            color: var(--pk);
        }

        .shop-category-count {
            align-items: center;
            background: rgba(255, 77, 143, .12);
            border-radius: 999px;
            display: inline-flex;
            font-size: .72rem;
            justify-content: center;
            min-width: 30px;
            padding: 4px 8px;
        }

        .shop-price-row {
            align-items: center;
            display: grid;
            gap: 10px;
            grid-template-columns: 1fr auto 1fr;
        }

        .shop-range {
            accent-color: var(--pk);
            width: 100%;
        }

        .shop-range-text,
        .shop-result-meta {
            color: var(--muted);
            font-size: .84rem;
            font-weight: 700;
        }

        .shop-main {
            min-width: 0;
        }

        .shop-toolbar {
            background: rgba(255, 255, 255, .82);
            border: 1px solid rgba(255, 255, 255, .95);
            border-radius: 22px;
            box-shadow: 0 14px 36px rgba(13, 0, 32, .06);
            flex-wrap: wrap;
            padding: 16px;
        }

        .shop-toolbar-actions {
            align-items: center;
            display: flex;
            gap: 12px;
            margin-left: auto;
        }

        #productFilterGrid {
            align-items: stretch;
            display: grid;
            gap: 26px;
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            margin-top: 24px;
        }

        .product-listing-section .product-filter-card,
        .product-listing-section .pc,
        .product-listing-section .pc-head,
        .product-listing-section .pc-body,
        .product-listing-section .pc-emoji.p-image img {
            opacity: 1;
            visibility: visible;
        }

        .product-listing-section .products-grid {
            align-items: stretch;
        }

        .product-listing-section .product-filter-card {
            align-self: stretch;
            height: 100% !important;
            min-height: 0 !important;
        }

        .product-listing-section .product-filter-card .pc-foot {
            margin-top: auto !important;
        }

        .product-listing-section .pc-variant-panel {
            background: linear-gradient(180deg, #fffdf7 0%, #ffffff 100%);
            border: 1px solid rgba(255, 77, 143, .14);
            border-radius: 18px;
            box-shadow: 0 10px 24px rgba(13, 0, 32, .035);
            display: grid;
            gap: 10px;
            margin: 12px 0 18px;
            padding: 12px;
        }

        .product-listing-section .pc-variant-groups {
            display: grid;
            gap: 9px;
        }

        .product-listing-section .pc-variant-label {
            color: #444;
            font-family: 'Nunito', sans-serif;
            font-size: .68rem;
            font-weight: 900;
            letter-spacing: .04em;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .product-listing-section .pc-option-row {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .product-listing-section .pc-option-btn {
            background: #fff;
            border: 1px solid rgba(53, 158, 111, .18);
            border-radius: 999px;
            color: #353047;
            cursor: pointer;
            font-family: 'Nunito', sans-serif;
            font-size: .7rem;
            font-weight: 900;
            min-height: 30px;
            padding: 4px 10px;
            transition: border-color .18s ease, background .18s ease, color .18s ease;
        }

        .product-listing-section .pc-option-btn.active {
            background: var(--pkl);
            border-color: var(--pk);
            color: var(--pk);
        }

        .product-listing-section .pc-variant-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
        }

        .product-listing-section .pc-stock-pill,
        .product-listing-section .pc-selected-pill {
            align-items: center;
            border-radius: 999px;
            display: inline-flex;
            font-family: 'Nunito', sans-serif;
            font-size: .66rem;
            font-weight: 900;
            min-height: 26px;
            padding: 4px 8px;
        }

        .product-listing-section .pc-stock-pill {
            background: #e8f9f1;
            color: #00885d;
        }

        .product-listing-section .pc-stock-pill.out {
            background: #fff0ee;
            color: #d02f1f;
        }

        .product-listing-section .pc-selected-pill {
            background: #f6f2ff;
            color: #6750a4;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .product-filter-empty {
            margin-top: 24px;
        }

        .shop-drawer-close {
            display: none;
        }

        .shop-filter-overlay {
            background: rgba(13, 0, 32, .45);
            inset: 0;
            opacity: 0;
            pointer-events: none;
            position: fixed;
            transition: opacity .2s ease;
            z-index: 9997;
        }

        body.shop-filter-open .shop-filter-overlay {
            opacity: 1;
            pointer-events: auto;
        }

        @media (max-width: 1180px) {
            .shop-shell {
                grid-template-columns: minmax(240px, 280px) minmax(0, 1fr);
            }

            #productFilterGrid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }
        }

        @media (max-width: 860px) {
            .product-listing-hero {
                padding: 90px 6% 48px;
            }

            .product-listing-section {
                padding-top: 56px;
                padding-left: 4%;
                padding-right: 4%;
            }

            .shop-shell {
                display: block;
            }

            .shop-filter-toggle,
            .shop-drawer-close {
                display: inline-flex;
            }

            .shop-sidebar {
                border-radius: 0 26px 26px 0;
                bottom: 0;
                left: 0;
                max-width: 360px;
                overflow-y: auto;
                position: fixed;
                top: 0;
                transform: translateX(-105%);
                transition: transform .25s ease;
                width: 88vw;
                z-index: 9998;
            }

            body.shop-filter-open .shop-sidebar {
                transform: translateX(0);
            }

            .shop-toolbar {
                margin-top: 30px;
            }
        }

        @media (max-width: 640px) {
            #productFilterGrid {
                grid-template-columns: 1fr !important;
            }

            .shop-toolbar-actions {
                width: 100%;
            }

            .shop-select {
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $preparedProducts = [];

        foreach ($products as $product) {
            $categorySlug = $product->category->slug ?? 'uncategorized';
            $catClass = match ($categorySlug) {
                'whey-protein' => 'sk',
                'pre-workout' => 'pu',
                default => 'pk',
            };

            $tags = $product->tags ?? [];
            if (is_string($tags)) {
                $tags = array_map(function ($tag) {
                    preg_match('/^([\x{1F300}-\x{1F9FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}])?\s*(.*)$/u', $tag, $match);
                    return ['icon' => $match[1] ?? '', 'text' => $match[2] ?? $tag];
                }, array_filter(array_map('trim', explode(',', $tags))));
            }

            $tagText = collect($tags)->map(fn ($tag) => $tag['text'] ?? '')->implode(' ');
            $rating = (float) ($product->reviews_avg_rating ?? 5);
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
            $frontendVariants = $activeVariants
                ->map(function ($variant) {
                    $stockQty = (int) ($variant->inventory?->stock_qty ?? 0);
                    $trackStock = (bool) ($variant->inventory?->track_stock ?? false);

                    return [
                        'id' => $variant->id,
                        'name' => $variant->name,
                        'attributes' => $variant->attributes ?? [],
                        'price' => (float) $variant->display_price,
                        'stock_qty' => $stockQty,
                        'track_stock' => $trackStock,
                        'available' => ! $trackStock || (($variant->inventory?->is_in_stock ?? true) && $stockQty > 0),
                    ];
                })
                ->values()
                ->all();

            $preparedProducts[] = [
                'product' => $product,
                'categorySlug' => $categorySlug,
                'cardClass' => $catClass,
                'search' => strtolower(trim(($product->name ?? '') . ' ' . ($product->category->name ?? '') . ' ' . ($product->short_description ?? '') . ' ' . $tagText)),
                'price' => (float) $product->display_price,
                'rating' => $rating,
                'variations' => $variationLabels,
                'variantGroups' => $variantGroups,
                'selectedVariant' => $selectedVariant,
                'selectedAttributes' => $selectedAttributes,
                'selectedLabel' => $selectedLabel,
                'stockQty' => $stockQty,
                'trackStock' => $trackStock,
                'isAvailable' => $isAvailable,
                'variantsJson' => json_encode($frontendVariants, JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT),
            ];
        }
    @endphp

    <section class="product-listing-hero">
        <div class="product-listing-hero-inner">
            <div class="product-listing-breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span>/</span>
                <span>Products</span>
            </div>
            <span class="product-listing-hero-badge">Shop NutriBuddy</span>
            <h1 class="product-listing-hero-title">Find the Right Wellness Gummies</h1>
            <p class="product-listing-hero-sub">Browse every NutriBuddy product with filters for category, price, rating and featured picks.</p>
        </div>
    </section>

    <section class="products-section product-listing-section" id="products">
        <div class="shop-shell">
            <aside class="shop-sidebar" id="shopSidebar" aria-label="Product filters">
                <div class="shop-sidebar-head">
                    <div class="shop-sidebar-title">Filters</div>
                    <button type="button" class="shop-clear-btn" id="shopClearFilters">Clear</button>
                    <button type="button" class="shop-clear-btn shop-drawer-close" id="shopCloseFilters">Close</button>
                </div>

                <div class="shop-filter-group">
                    <div class="shop-filter-label">Search</div>
                    <div class="shop-search">
                        <input type="search" id="shopSearch" placeholder="Search products">
                    </div>
                </div>

                <div class="shop-filter-group">
                    <div class="shop-filter-label">Categories</div>
                    <div class="shop-category-list" id="shopCategoryList">
                        <button type="button" class="shop-category-btn is-active" data-category="all">
                            <span>All Products</span>
                            <span class="shop-category-count">{{ $totalProducts }}</span>
                        </button>
                        @foreach ($categoryCounts as $category)
                            <button type="button" class="shop-category-btn" data-category="{{ $category['slug'] }}">
                                <span>{{ $category['name'] }}</span>
                                <span class="shop-category-count">{{ $category['count'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="shop-filter-group">
                    <div class="shop-filter-label">Price Range</div>
                    <div class="shop-price-row">
                        <input type="number" class="shop-price-input" id="shopMinPrice" min="{{ $minPrice }}" max="{{ $maxPrice }}" value="{{ $minPrice }}" aria-label="Minimum price">
                        <span class="shop-range-text">to</span>
                        <input type="number" class="shop-price-input" id="shopMaxPrice" min="{{ $minPrice }}" max="{{ $maxPrice }}" value="{{ $maxPrice }}" aria-label="Maximum price">
                    </div>
                    <div class="mt-12">
                        <input type="range" class="shop-range" id="shopPriceRange" min="{{ $minPrice }}" max="{{ $maxPrice }}" value="{{ $maxPrice }}">
                    </div>
                    <div class="shop-range-text mt-8">Up to Rs. <span id="shopPriceText">{{ number_format($maxPrice, 0) }}</span></div>
                </div>

                <div class="shop-filter-group">
                    <div class="shop-filter-label">Rating</div>
                    <select class="shop-select" id="shopRating">
                        <option value="0">Any rating</option>
                        <option value="4">4 stars and above</option>
                        <option value="4.5">4.5 stars and above</option>
                    </select>
                </div>
            </aside>

            <div class="shop-main">
                <div class="shop-toolbar">
                    <button type="button" class="shop-filter-toggle" id="shopOpenFilters">Filters</button>
                    <div>
                        <div class="shop-result-meta"><span id="shopResultCount">{{ $products->count() }}</span> products found</div>
                        <div class="text-xs" style="color: var(--muted); font-weight: 700;">Showing {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} of {{ $products->total() }}</div>
                    </div>
                    <div class="shop-toolbar-actions">
                        <select class="shop-select" id="shopSort" aria-label="Sort products">
                            <option value="featured">Featured first</option>
                            <option value="price-low">Price: low to high</option>
                            <option value="price-high">Price: high to low</option>
                            <option value="rating">Top rated</option>
                            <option value="name">Name A-Z</option>
                        </select>
                    </div>
                </div>

                <div class="products-grid" id="productFilterGrid">
                    @foreach($preparedProducts as $preparedProduct)
                        @php
                            $product = $preparedProduct['product'];
                            $catSlug = $preparedProduct['cardClass'];
                            $variations = $preparedProduct['variations'];
                            $variantGroups = $preparedProduct['variantGroups'];
                            $selectedAttributes = $preparedProduct['selectedAttributes'];
                            $secondImage = $product->images->where('is_primary', false)->first();
                            $primaryImage = $product->primaryImage;
                            $hasDiscount = $product->display_compare_price > $product->display_price;
                            $hasVariantOptions = !empty($variantGroups) || !empty($variations);
                        @endphp
                        <div class="pc pc-{{ $catSlug }} product-filter-card {{ $hasVariantOptions ? 'has-variants' : 'no-variants' }}"
                            data-category="{{ $preparedProduct['categorySlug'] }}"
                            data-search="{{ e($preparedProduct['search']) }}"
                            data-price="{{ $preparedProduct['price'] }}"
                            data-rating="{{ $preparedProduct['rating'] }}"
                            data-featured="{{ $product->is_featured ? 1 : 0 }}"
                            data-discount="{{ $hasDiscount ? 1 : 0 }}"
                            data-selected-variant-id="{{ $preparedProduct['selectedVariant']?->id }}"
                            data-variants='{{ $preparedProduct['variantsJson'] }}'
                            data-name="{{ e(strtolower($product->name)) }}">
                            <div class="pc-head pc-head-{{ $catSlug }}">
                                <a href="{{ route('product.show', $product->slug) }}" class="pc-emoji p-image">
                                    @if($primaryImage)
                                        <img src="{{ asset('storage/' . $primaryImage->image_path) }}" alt="{{ $product->name }}" class="default-img" loading="lazy" decoding="async">
                                        <img src="{{ asset('storage/' . ($secondImage?->image_path ?? $primaryImage->image_path)) }}" alt="{{ $product->name }}" class="hover-img" loading="lazy" decoding="async">
                                    @else
                                        <img src="{{ asset('img/productt.png') }}" alt="{{ $product->name }}" class="default-img" loading="lazy" decoding="async">
                                        <img src="{{ asset('img/productt.png') }}" alt="{{ $product->name }}" class="hover-img" loading="lazy" decoding="async">
                                    @endif
                                </a>
                                @if($product->is_featured)
                                    <div class="pc-badge">Best Seller</div>
                                @elseif($hasDiscount)
                                    <div class="pc-badge">Offer</div>
                                @endif
                            </div>
                            <div class="pc-body">
                                <div class="pc-stars">
                                    @for($i = 0; $i < 5; $i++){!! $i < $preparedProduct['rating'] ? '&#9733;' : '&#9734;' !!}@endfor
                                    <span style="color:#aaa;font-size:.75rem;font-family:'DM Sans',sans-serif">
                                        ({{ $product->reviews_count > 0 ? number_format($product->reviews_count) : '2,841' }} reviews)
                                    </span>
                                </div>
                                <div class="pc-cat cat-{{ $catSlug }}">{{ $product->category->name ?? 'Uncategorized' }}</div>
                                <div class="pc-name"><a href="{{ route('product.show', $product->slug) }}" style="color: inherit; text-decoration: none;">{{ $product->name }}</a></div>

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
                                            @foreach(array_slice($variations, 0, 3) as $variation)
                                                <div class="pc-option-row">
                                                    <button type="button" class="pc-option-btn active">{{ $variation }}</button>
                                                </div>
                                            @endforeach
                                        @endif

                                        <div class="pc-variant-meta">
                                            <span class="pc-stock-pill {{ $preparedProduct['isAvailable'] ? '' : 'out' }}">
                                                @if($preparedProduct['isAvailable'])
                                                    {{ $preparedProduct['trackStock'] ? $preparedProduct['stockQty'] . ' unit in stock' : 'In stock' }}
                                                @else
                                                    Out of stock
                                                @endif
                                            </span>
                                            <span class="pc-selected-pill" title="{{ $preparedProduct['selectedLabel'] ?: 'Product option' }}">
                                                {{ $preparedProduct['selectedLabel'] ?: 'Product option' }}
                                            </span>
                                        </div>
                                    </div>
                                @endif

                                <div class="pc-foot">
                                    <div class="pc-price">
                                        Rs. {{ number_format($product->display_price, 0) }}
                                        @if($hasDiscount)
                                            <s>Rs. {{ number_format($product->display_compare_price, 0) }}</s>
                                        @endif
                                    </div>
                                    <button class="btn-add badd-{{ $catSlug }}" data-id="{{ $product->id }}" data-variant-id="{{ $preparedProduct['selectedVariant']?->id }}">Add to Cart +</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="product-filter-empty" id="productFilterEmpty" hidden>
                    <div class="product-filter-empty-icon">+</div>
                    <h3>No products match these filters</h3>
                    <p>Try clearing filters or widening the price range.</p>
                </div>

                <div class="mt-32">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </section>

    <div class="shop-filter-overlay" id="shopFilterOverlay"></div>
@endsection

@push('scripts')
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const cards = Array.from(document.querySelectorAll('.product-filter-card'));
            const grid = document.getElementById('productFilterGrid');
            const empty = document.getElementById('productFilterEmpty');
            const resultCount = document.getElementById('shopResultCount');
            const search = document.getElementById('shopSearch');
            const categoryButtons = Array.from(document.querySelectorAll('.shop-category-btn'));
            const minPrice = document.getElementById('shopMinPrice');
            const maxPrice = document.getElementById('shopMaxPrice');
            const priceRange = document.getElementById('shopPriceRange');
            const priceText = document.getElementById('shopPriceText');
            const rating = document.getElementById('shopRating');
            const sort = document.getElementById('shopSort');
            const clear = document.getElementById('shopClearFilters');
            const openFilters = document.getElementById('shopOpenFilters');
            const closeFilters = document.getElementById('shopCloseFilters');
            const overlay = document.getElementById('shopFilterOverlay');
            const initialMin = Number(minPrice?.min || 0);
            const initialMax = Number(maxPrice?.max || 0);
            let activeCategory = 'all';

            function numberValue(input, fallback) {
                const value = Number(input?.value);
                return Number.isFinite(value) ? value : fallback;
            }

            function syncPriceRangeFromInputs() {
                let min = numberValue(minPrice, initialMin);
                let max = numberValue(maxPrice, initialMax);
                if (min > max) [min, max] = [max, min];
                if (minPrice) minPrice.value = min;
                if (maxPrice) maxPrice.value = max;
                if (priceRange) priceRange.value = max;
                if (priceText) priceText.textContent = Number(max).toLocaleString('en-IN');
            }

            function sortedVisibleCards(visibleCards) {
                const mode = sort?.value || 'featured';
                return visibleCards.sort((a, b) => {
                    if (mode === 'price-low') return Number(a.dataset.price) - Number(b.dataset.price);
                    if (mode === 'price-high') return Number(b.dataset.price) - Number(a.dataset.price);
                    if (mode === 'rating') return Number(b.dataset.rating) - Number(a.dataset.rating);
                    if (mode === 'name') return String(a.dataset.name).localeCompare(String(b.dataset.name));
                    return Number(b.dataset.featured) - Number(a.dataset.featured);
                });
            }

            function applyFilters() {
                syncPriceRangeFromInputs();
                const q = String(search?.value || '').trim().toLowerCase();
                const min = numberValue(minPrice, initialMin);
                const max = numberValue(maxPrice, initialMax);
                const minRating = Number(rating?.value || 0);

                const visible = cards.filter(card => {
                    const price = Number(card.dataset.price || 0);
                    if (activeCategory !== 'all' && card.dataset.category !== activeCategory) return false;
                    if (q && !String(card.dataset.search || '').includes(q)) return false;
                    if (price < min || price > max) return false;
                    if (Number(card.dataset.rating || 0) < minRating) return false;
                    return true;
                });

                cards.forEach(card => card.style.display = 'none');
                sortedVisibleCards(visible).forEach(card => {
                    card.style.display = '';
                    grid.appendChild(card);
                });

                if (resultCount) resultCount.textContent = visible.length;
                if (empty) empty.hidden = visible.length !== 0;
            }

            categoryButtons.forEach(button => {
                button.addEventListener('click', () => {
                    activeCategory = button.dataset.category || 'all';
                    categoryButtons.forEach(item => item.classList.toggle('is-active', item === button));
                    applyFilters();
                    document.body.classList.remove('shop-filter-open');
                });
            });

            [search, minPrice, maxPrice, rating, sort].forEach(input => {
                input?.addEventListener('input', applyFilters);
                input?.addEventListener('change', applyFilters);
            });

            priceRange?.addEventListener('input', () => {
                if (maxPrice) maxPrice.value = priceRange.value;
                applyFilters();
            });

            clear?.addEventListener('click', () => {
                activeCategory = 'all';
                categoryButtons.forEach(button => button.classList.toggle('is-active', button.dataset.category === 'all'));
                if (search) search.value = '';
                if (minPrice) minPrice.value = initialMin;
                if (maxPrice) maxPrice.value = initialMax;
                if (priceRange) priceRange.value = initialMax;
                if (rating) rating.value = '0';
                if (sort) sort.value = 'featured';
                applyFilters();
            });

            openFilters?.addEventListener('click', () => document.body.classList.add('shop-filter-open'));
            closeFilters?.addEventListener('click', () => document.body.classList.remove('shop-filter-open'));
            overlay?.addEventListener('click', () => document.body.classList.remove('shop-filter-open'));

            document.querySelectorAll('.pc-variant-panel').forEach(panel => {
                const card = panel.closest('.product-filter-card');
                const addButton = card?.querySelector('.btn-add');
                const variants = (() => {
                    try {
                        return JSON.parse(card?.dataset.variants || '[]');
                    } catch (_) {
                        return [];
                    }
                })();

                function selectedAttributes() {
                    return Object.fromEntries(
                        Array.from(panel.querySelectorAll('.pc-option-btn.active[data-attribute]'))
                            .map(item => [item.dataset.attribute, item.dataset.value])
                    );
                }

                function findSelectedVariant() {
                    const selected = selectedAttributes();
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
                    if (addButton) addButton.dataset.variantId = variant?.id || '';

                    if (stockPill && variant) {
                        stockPill.classList.toggle('out', !variant.available);
                        stockPill.textContent = variant.available
                            ? (variant.track_stock ? `${variant.stock_qty} unit in stock` : 'In stock')
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

            applyFilters();
        });
    </script>
@endpush
