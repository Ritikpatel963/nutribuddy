@extends('layouts.main')
@section('title', 'All Products - NutriBuddy')

@section('content')
    @php
        $productsList = $products->values();
        $categoryCounts = $productsList
            ->groupBy(fn ($product) => $product->category->name ?? 'Uncategorized')
            ->map(fn ($items) => $items->count())
            ->sortKeys();
        $prices = $productsList->map(fn ($product) => (float) $product->display_price);
        $minPrice = (int) floor($prices->min() ?? 0);
        $maxPrice = (int) ceil($prices->max() ?? 0);
    @endphp

    <section class="product-listing-hero">
        <div class="product-listing-hero-inner">
            <div class="product-listing-breadcrumb">
                <a href="{{ url('/') }}">Home</a>
                <span>/</span>
                <span>Products</span>
            </div>
            <span class="product-listing-hero-badge">Shop NutriBuddy</span>
            <h1 class="product-listing-hero-title">Find the Right Wellness Gummies</h1>
            <p class="product-listing-hero-sub">Browse every NutriBuddy product with filters for category, price, rating and featured picks.</p>
        </div>
    </section>

    <section class="product-listing-page" id="products">
        <div class="product-listing-layout">
            <aside class="product-filter-sidebar">
                <div class="filter-head">
                    <h2>Filters</h2>
                    <button type="button" id="clearProductFilters">Clear</button>
                </div>

                <div class="filter-block">
                    <h3>Search</h3>
                    <input type="search" id="productSearchInput" placeholder="Search products" autocomplete="off">
                </div>

                <div class="filter-block">
                    <h3>Categories</h3>
                    <button type="button" class="category-filter-btn active" data-category="all">
                        <span>All Products</span>
                        <strong>{{ $productsList->count() }}</strong>
                    </button>
                    @foreach ($categoryCounts as $categoryName => $count)
                        <button type="button" class="category-filter-btn" data-category="{{ \Illuminate\Support\Str::slug($categoryName) }}">
                            <span>{{ $categoryName }}</span>
                            <strong>{{ $count }}</strong>
                        </button>
                    @endforeach
                </div>

                <div class="filter-block">
                    <h3>Price Range</h3>
                    <div class="price-filter-inputs">
                        <input type="number" id="minPriceInput" value="{{ $minPrice }}" min="{{ $minPrice }}" max="{{ $maxPrice }}">
                        <span>to</span>
                        <input type="number" id="maxPriceInput" value="{{ $maxPrice }}" min="{{ $minPrice }}" max="{{ $maxPrice }}">
                    </div>
                    <input type="range" id="productPriceRange" min="{{ $minPrice }}" max="{{ $maxPrice }}" value="{{ $maxPrice }}">
                    <p>Up to Rs. <span id="priceRangeLabel">{{ number_format($maxPrice) }}</span></p>
                </div>
            </aside>

            <div class="product-listing-results">
                <div class="product-result-toolbar">
                    <div>
                        <strong><span id="visibleProductTotal">{{ $productsList->count() }}</span> products found</strong>
                        <p>Showing <span id="visibleProductRange">{{ $productsList->isNotEmpty() ? '1-' . $productsList->count() : '0-0' }}</span> of {{ $productsList->count() }}</p>
                    </div>
                    <select id="productSortSelect" aria-label="Sort products">
                        <option value="default">Default</option>
                        <option value="featured">Featured first</option>
                        <option value="price-low">Price low to high</option>
                        <option value="price-high">Price high to low</option>
                        <option value="name">Name A to Z</option>
                    </select>
                </div>

                <div class="products-grid product-listing-grid" id="productFilterGrid">
                    @foreach ($productsList as $index => $product)
                        @php
                            $catSlug = $product->category->slug ?? 'pk';
                            if ($catSlug == 'multivitamins') {
                                $catSlug = 'pk';
                            } elseif ($catSlug == 'whey-protein') {
                                $catSlug = 'sk';
                            } elseif ($catSlug == 'pre-workout') {
                                $catSlug = 'pu';
                            } else {
                                $catSlug = 'pk';
                            }

                            $categoryName = $product->category->name ?? 'Uncategorized';
                            $categoryKey = \Illuminate\Support\Str::slug($categoryName);
                            $activeVariants = $product->variants
                                ->filter(fn ($variant) => $variant->is_active && !empty($variant->attributes))
                                ->values();
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
                            $rating = $product->reviews->avg('rating') ?? 5;
                            $reviewCount = $product->reviews->count() > 0 ? $product->reviews->count() : 2;
                            $defaultImage = $product->primaryImage ?: $product->images->first();
                            $hoverImage = $product->images
                                ->where('id', '!=', $defaultImage?->id)
                                ->first() ?: $defaultImage;
                        @endphp

                        <div class="pc pc-{{ $catSlug }} product-filter-card {{ $selectedVariant ? 'has-variants' : 'no-variants' }}"
                            data-order="{{ $index }}"
                            data-category="{{ $categoryKey }}"
                            data-search="{{ strtolower($product->name . ' ' . $categoryName) }}"
                            data-price="{{ $cardPrice }}"
                            data-featured="{{ $product->is_featured ? 1 : 0 }}"
                            data-rating="{{ $rating }}"
                            data-name="{{ strtolower($product->name) }}"
                            data-selected-variant-id="{{ $selectedVariant?->id }}"
                            data-selected-variant-label="{{ $selectedLabel }}"
                            data-variants='{{ json_encode($frontendVariants, JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) }}'>
                            <div class="pc-head pc-head-{{ $catSlug }}">
                                <a href="{{ route('product.show', $product->slug) }}" class="pc-emoji p-image">
                                    @if ($defaultImage)
                                        <img src="{{ asset('storage/' . $defaultImage->image_path) }}" alt="{{ $product->name }}" class="default-img" loading="lazy" decoding="async">
                                        <img src="{{ asset('storage/' . $hoverImage->image_path) }}" alt="{{ $product->name }}" class="hover-img" loading="lazy" decoding="async">
                                    @else
                                        <img src="{{ asset('img/productt.png') }}" alt="{{ $product->name }}" class="default-img" loading="lazy" decoding="async">
                                        <img src="{{ asset('img/productt.png') }}" alt="{{ $product->name }}" class="hover-img" loading="lazy" decoding="async">
                                    @endif
                                </a>
                                <div class="pc-badge">{{ $cardComparePrice > $cardPrice ? 'Offer' : ($product->is_featured ? 'Best Seller' : 'Offer') }}</div>
                            </div>

                            <div class="pc-body">
                                <div class="pc-stars">
                                    @for ($i = 0; $i < 5; $i++)
                                        {!! $i < $rating ? '&#9733;' : '&#9734;' !!}
                                    @endfor
                                    <span style="color:#aaa;font-size:.75rem;font-family:'DM Sans',sans-serif">({{ $reviewCount }} reviews)</span>
                                </div>
                                <div class="pc-cat cat-{{ $catSlug }}">{{ $categoryName }}</div>
                                <div class="pc-name">
                                    <a href="{{ route('product.show', $product->slug) }}" style="color: inherit; text-decoration: none;">{{ $product->name }}</a>
                                </div>

                                @if (!empty($variantGroups))
                                    <div class="pc-variant-panel">
                                        <div class="pc-variant-groups">
                                            @foreach ($variantGroups as $attributeName => $values)
                                                <div class="pc-variant-block">
                                                    <div class="pc-variant-label">{{ $attributeName }}</div>
                                                    <div class="pc-option-row" data-attribute-group="{{ $attributeName }}">
                                                        @foreach ($values as $value)
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
                                        <div class="pc-variant-meta">
                                            <span class="pc-stock-pill {{ $isAvailable ? '' : 'out' }}">
                                                {{ $isAvailable ? ($trackStock ? $stockQty . ' pcs' : 'Available') : 'Out of stock' }}
                                            </span>
                                            <span class="pc-selected-pill" title="{{ $selectedLabel ?: 'Product option' }}">
                                                {{ $selectedLabel ?: 'Product option' }}
                                            </span>
                                        </div>
                                    </div>
                                @endif

                                <div class="pc-foot">
                                    <div class="pc-price" data-price-label>
                                        &#8377;{{ number_format($cardPrice, 0) }}
                                        @if ($cardComparePrice > $cardPrice)
                                            <s>&#8377;{{ number_format($cardComparePrice, 0) }}</s>
                                        @endif
                                    </div>
                                    <button class="btn-add badd-{{ $catSlug }}" data-id="{{ $product->id }}" data-variant-id="{{ $selectedVariant?->id }}">Add to Cart +</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="product-filter-empty" id="productFilterEmpty" hidden>
                    <h3>No products found</h3>
                    <p>Please clear filters or search another product.</p>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    window.addEventListener('DOMContentLoaded', () => {
        const cards = Array.from(document.querySelectorAll('.product-filter-card'));
        const grid = document.getElementById('productFilterGrid');
        const categoryButtons = Array.from(document.querySelectorAll('.category-filter-btn'));
        const searchInput = document.getElementById('productSearchInput');
        const minInput = document.getElementById('minPriceInput');
        const maxInput = document.getElementById('maxPriceInput');
        const rangeInput = document.getElementById('productPriceRange');
        const priceLabel = document.getElementById('priceRangeLabel');
        const sortSelect = document.getElementById('productSortSelect');
        const clearButton = document.getElementById('clearProductFilters');
        const totalLabel = document.getElementById('visibleProductTotal');
        const rangeLabel = document.getElementById('visibleProductRange');
        const emptyState = document.getElementById('productFilterEmpty');
        const initialMax = rangeInput ? Number(rangeInput.max) : 0;
        const initialMin = rangeInput ? Number(rangeInput.min) : 0;
        let selectedCategory = 'all';

        const sortCards = () => {
            if (!grid || !sortSelect) return;
            const sorted = [...cards].sort((a, b) => {
                if (sortSelect.value === 'price-low') return Number(a.dataset.price) - Number(b.dataset.price);
                if (sortSelect.value === 'price-high') return Number(b.dataset.price) - Number(a.dataset.price);
                if (sortSelect.value === 'name') return a.dataset.name.localeCompare(b.dataset.name);
                if (sortSelect.value === 'featured') return Number(b.dataset.featured) - Number(a.dataset.featured);
                // default: preserve server order
                return Number(a.dataset.order) - Number(b.dataset.order);
            });
            sorted.forEach((card) => grid.appendChild(card));
        };

        const updateFilters = () => {
            const searchTerm = (searchInput?.value || '').trim().toLowerCase();
            const minPrice = Number(minInput?.value || initialMin);
            const maxPrice = Number(maxInput?.value || initialMax);
            let visible = 0;

            categoryButtons.forEach((button) => {
                button.classList.toggle('active', button.dataset.category === selectedCategory);
            });

            cards.forEach((card) => {
                const cardPrice = Number(card.dataset.price);
                const matchesCategory = selectedCategory === 'all' || card.dataset.category === selectedCategory;
                const matchesSearch = !searchTerm || card.dataset.search.includes(searchTerm);
                const matchesPrice = cardPrice >= minPrice && cardPrice <= maxPrice;
                const showCard = matchesCategory && matchesSearch && matchesPrice;

                card.hidden = !showCard;
                if (showCard) visible++;
            });

            if (priceLabel) priceLabel.textContent = maxPrice.toLocaleString('en-IN');
            if (totalLabel) totalLabel.textContent = visible;
            if (rangeLabel) rangeLabel.textContent = visible ? `1-${visible}` : '0-0';
            if (emptyState) emptyState.hidden = visible !== 0;
            sortCards();
        };

        categoryButtons.forEach((button) => {
            button.addEventListener('click', () => {
                selectedCategory = button.dataset.category;
                updateFilters();
            });
        });

        searchInput?.addEventListener('input', updateFilters);
        sortSelect?.addEventListener('change', updateFilters);
        minInput?.addEventListener('input', updateFilters);
        maxInput?.addEventListener('input', () => {
            if (rangeInput) rangeInput.value = maxInput.value;
            updateFilters();
        });
        rangeInput?.addEventListener('input', () => {
            if (maxInput) maxInput.value = rangeInput.value;
            updateFilters();
        });
        clearButton?.addEventListener('click', () => {
            selectedCategory = 'all';
            if (searchInput) searchInput.value = '';
            if (minInput) minInput.value = initialMin;
            if (maxInput) maxInput.value = initialMax;
            if (rangeInput) rangeInput.value = initialMax;
            if (sortSelect) sortSelect.value = 'default';
            updateFilters();
        });

        updateFilters();
    });
</script>
@endpush
