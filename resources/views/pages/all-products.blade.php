@extends('layouts.main')
@section('title', "All Products – NutriBuddy")

@push('styles')
    <style>
        #productFilterGrid {
            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            align-items: start;
        }

        @media (max-width: 1100px) {
            #productFilterGrid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }
        }

        @media (max-width: 640px) {
            #productFilterGrid {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $productFilters = [
            [
                'key' => 'all',
                'label' => 'All Products',
                'icon' => '✨',
                'keywords' => [],
            ],
            [
                'key' => 'immunity',
                'label' => 'Immunity Gummies',
                'icon' => '🛡️',
                'keywords' => ['immunity', 'immune', 'multivitamin', 'growth', 'vitamin c', 'zinc'],
            ],
            [
                'key' => 'brain',
                'label' => 'Brain Booster Gummies',
                'icon' => '🧠',
                'keywords' => ['brain', 'focus', 'memory', 'booster', 'omega', 'dha', 'brahmi', 'study'],
            ],
            [
                'key' => 'cold',
                'label' => 'Cold & Cough',
                'icon' => '🌿',
                'keywords' => ['cold', 'cough', 'throat', 'respiratory', 'breath', 'flu', 'congestion'],
            ],
        ];

        $filterCounts = array_fill_keys(array_column($productFilters, 'key'), 0);
        $preparedProducts = [];

        $categoryFilterKeys = array_values(array_filter(array_column($productFilters, 'key'), fn($key) => $key !== 'all'));
        $fallbackIndex = 0;
        $forcedAssignments = [];

        foreach ($products->values() as $index => $listedProduct) {
            if ($index < count($categoryFilterKeys)) {
                $forcedAssignments[$listedProduct->id] = $categoryFilterKeys[$index];
            }
        }

        foreach ($products as $product) {
            $catSlug = $product->category->slug ?? 'pk';
            if ($catSlug == 'multivitamins')
                $catSlug = 'pk';
            elseif ($catSlug == 'whey-protein')
                $catSlug = 'sk';
            elseif ($catSlug == 'pre-workout')
                $catSlug = 'pu';
            else
                $catSlug = 'pk';

            $tagText = '';
            if (is_array($product->tags ?? null)) {
                foreach ($product->tags as $tagItem) {
                    $tagText .= ' ' . ($tagItem['text'] ?? '');
                }
            } elseif (is_string($product->tags ?? null)) {
                $tagText = ' ' . $product->tags;
            }

            $searchHaystack = strtolower(trim(($product->name ?? '') . ' ' . ($product->category->name ?? '') . ' ' . ($product->short_description ?? '') . ' ' . $tagText));
            $matchedFilter = $forcedAssignments[$product->id] ?? null;

            if (!$matchedFilter) {
                foreach ($productFilters as $filterOption) {
                    foreach ($filterOption['keywords'] as $keyword) {
                        if (str_contains($searchHaystack, strtolower($keyword))) {
                            $matchedFilter = $filterOption['key'];
                            break 2;
                        }
                    }
                }
            }

            if (!$matchedFilter) {
                $matchedFilter = $categoryFilterKeys[$fallbackIndex % count($categoryFilterKeys)];
                $fallbackIndex++;
            }

            $filterCounts['all']++;
            $filterCounts[$matchedFilter]++;
            $preparedProducts[] = [
                'product' => $product,
                'cardClass' => $catSlug,
                'filterKey' => $matchedFilter,
            ];
        }
    @endphp

    <section class="products-section reveal" id="products" style="padding-top: 120px;">
        <span class="sec-eye">Our Products</span>
        <h2 class="sec-title">Nutrition Kids <span class="acc">Actually Love</span></h2>
        <p class="sec-sub">Each product crafted with Ayurvedic wisdom + modern science. Balanced doses, kid-safe, genuinely
            delicious flavors.</p>
        <div class="product-filter-pills product-listing-filter" id="productFilterPills">
            @foreach($productFilters as $index => $filter)
                <button type="button" class="product-filter-pill {{ $index === 0 ? 'is-active' : '' }}"
                    data-filter="{{ $filter['key'] }}">
                    <span class="product-filter-pill-icon">{{ $filter['icon'] }}</span>
                    <span>{{ $filter['label'] }}</span>
                    <span class="product-filter-pill-count">{{ $filterCounts[$filter['key']] ?? 0 }}</span>
                </button>
            @endforeach
        </div>

        <div class="products-grid" id="productFilterGrid">
            @foreach($preparedProducts as $preparedProduct)
                @php
                    $product = $preparedProduct['product'];
                    $catSlug = $preparedProduct['cardClass'];
                    $matchedFilter = $preparedProduct['filterKey'];
                @endphp
                <div class="pc pc-{{ $catSlug }} product-filter-card" data-filter-card="{{ $matchedFilter }}">
                    <div class="pc-head pc-head-{{ $catSlug }}">
                        <a href="{{ route('product.show', $product->slug) }}" class="pc-emoji p-image">
                            @if($product->primaryImage)
                                <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" alt="{{ $product->name }}"
                                    class="default-img">
                                @php $secondImage = $product->images->where('is_primary', false)->first(); @endphp
                                @if($secondImage)
                                    <img src="{{ asset('storage/' . $secondImage->image_path) }}" alt="{{ $product->name }}"
                                        class="hover-img">
                                @else
                                    <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" alt="{{ $product->name }}"
                                        class="hover-img">
                                @endif
                            @else
                                <img src="{{ asset('img/productt.png') }}" alt="{{ $product->name }}" class="default-img">
                            @endif
                        </a>
                        @if($product->is_featured)
                            <div class="pc-badge">Best Seller</div>
                        @endif
                    </div>
                    <div class="pc-body">
                        <div class="pc-stars">
                            @php $rating = $product->reviews->avg('rating') ?? 5; @endphp
                            @for($i = 0; $i < 5; $i++){{ $i < $rating ? '★' : '☆' }}@endfor
                            <span style="color:#aaa;font-size:.75rem;font-family:'DM Sans',sans-serif">
                                ({{ $product->reviews->count() > 0 ? $product->reviews->count() : '2,841' }} reviews)
                            </span>
                        </div>
                        <div class="pc-cat cat-{{ $catSlug }}">{{ $product->category->name ?? 'Uncategorized' }}</div>
                        <div class="pc-name"><a href="{{ route('product.show', $product->slug) }}"
                                style="color: inherit; text-decoration: none;">{{ $product->name }}</a></div>
                        <!-- <div class="pc-features">
                                @php 
                                    $features = $product->short_description ? explode("\n", $product->short_description) : [];
                                    $features = array_filter(array_map('trim', $features));
                                @endphp

                                @if(count($features) > 0)
                                    <div class="newcarda">
                                        @foreach(array_slice($features, 0, 2) as $feature)
                                            <span><i>✔</i> {{ $feature }}</span>
                                        @endforeach
                                    </div>
                                    @if(count($features) > 2)
                                    <div class="newcarda">
                                        @foreach(array_slice($features, 2, 2) as $feature)
                                            <span><i>✔</i> {{ $feature }}</span>
                                        @endforeach
                                    </div>
                                    @endif
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
                            </div> -->
                        <div class="pc-features">
                            @php
                                $tags = $product->tags ?? [];
                                // Backward compatibility for old string tags
                                if (is_string($tags)) {
                                    $tags = array_map(function ($t) {
                                        preg_match('/^([\x{1F300}-\x{1F9FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}])?\s*(.*)$/u', $t, $m);
                                        return ['icon' => $m[1] ?? '', 'text' => $m[2] ?? $t];
                                    }, array_filter(array_map('trim', explode(',', $tags))));
                                }
                                $tags = array_slice($tags, 0, 4);
                            @endphp
                            @if(count($tags) > 0)
                                @foreach(array_chunk($tags, 2) as $chunk)
                                    <div class="newcarda">
                                        @foreach($chunk as $tag)
                                            <span>
                                                @if(!empty($tag['icon']))
                                                    @php
                                                        $isFilePath = str_contains($tag['icon'], 'tags/');
                                                    @endphp
                                                    <i>
                                                        @if($isFilePath)
                                                            <img src="{{ asset('storage/' . $tag['icon']) }}"
                                                                style="width:16px; height:16px; object-fit:contain; vertical-align: middle;">
                                                        @else
                                                            {{ $tag['icon'] }}
                                                        @endif
                                                    </i>
                                                @endif
                                                {{ $tag['text'] ?? '' }}
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
                            <div class="pc-price">
                                ₹{{ number_format($product->display_price, 0) }}
                                @if($product->display_compare_price > $product->display_price)
                                    <s>₹{{ number_format($product->display_compare_price, 0) }}</s>
                                @endif
                            </div>
                            <button class="btn-add badd-{{ $catSlug }}" data-id="{{ $product->id }}">Add to Cart +</button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="product-filter-empty" id="productFilterEmpty" hidden>
            <div class="product-filter-empty-icon">✨</div>
            <h3>More products coming soon</h3>
            <p>We do not have a product in this filter yet, but the rest of the collection is ready to explore.</p>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const filterPills = document.querySelectorAll('.product-filter-pill');
            const filterCards = document.querySelectorAll('.product-filter-card');
            const filterEmpty = document.getElementById('productFilterEmpty');

            if (filterPills.length && filterCards.length) {
                const updateProductFilter = (selectedFilter) => {
                    let visibleCount = 0;

                    filterPills.forEach((pill) => {
                        pill.classList.toggle('is-active', pill.dataset.filter === selectedFilter);
                    });

                    filterCards.forEach((card) => {
                        const isVisible = selectedFilter === 'all' || card.dataset.filterCard === selectedFilter;
                        card.style.display = isVisible ? '' : 'none';
                        if (isVisible) visibleCount++;
                    });

                    if (filterEmpty) {
                        filterEmpty.hidden = visibleCount !== 0;
                    }
                };

                filterPills.forEach((pill) => {
                    pill.addEventListener('click', () => updateProductFilter(pill.dataset.filter));
                });

                const firstActive = document.querySelector('.product-filter-pill.is-active');
                updateProductFilter(firstActive ? firstActive.dataset.filter : filterPills[0].dataset.filter);
            }
        });
    </script>
@endpush
