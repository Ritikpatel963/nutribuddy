@extends('layouts.main')
@section('title', 'All Products - NutriBuddy')

@section('content')
    @php
        $productsList = $products->values();
        $categoryCounts = $productsList
            ->groupBy(fn ($p) => $p->category->name ?? 'Uncategorized')
            ->map(fn ($items) => $items->count())
            ->sortKeys();
        $prices  = $productsList->map(fn ($p) => (float) $p->display_price);
        $minPrice = (int) floor($prices->min() ?? 0);
        $maxPrice = (int) ceil($prices->max() ?? 0);
    @endphp

    {{-- Hero --}}
    <section class="product-listing-hero">
        <div class="product-listing-hero-inner">
            <div class="product-listing-breadcrumb">
                <a href="{{ url('/') }}">Home</a><span>/</span><span>Products</span>
            </div>
            <span class="product-listing-hero-badge">Shop NutriBuddy</span>
            <h1 class="product-listing-hero-title">Find the Right Wellness Gummies</h1>
            <p class="product-listing-hero-sub">Browse every NutriBuddy product with filters for category, price and rating.</p>
        </div>
    </section>

    {{-- Main layout: sidebar + grid --}}
    <section class="plp-page" id="products">
        <div class="plp-layout">

            {{-- ── SIDEBAR ── --}}
            <aside class="plp-sidebar">
                <div class="plp-sidebar-head">
                    <h2>Filters</h2>
                    <button type="button" id="plpClear">Clear</button>
                </div>

                <div class="plp-filter-block">
                    <h3>Search</h3>
                    <input type="search" id="plpSearch" placeholder="Search products" autocomplete="off">
                </div>

                <div class="plp-filter-block">
                    <h3>Categories</h3>
                    <button type="button" class="plp-cat-btn active" data-cat="all">
                        <span>All Products</span><strong>{{ $productsList->count() }}</strong>
                    </button>
                    @foreach ($categoryCounts as $catName => $catCount)
                        <button type="button" class="plp-cat-btn" data-cat="{{ \Illuminate\Support\Str::slug($catName) }}">
                            <span>{{ $catName }}</span><strong>{{ $catCount }}</strong>
                        </button>
                    @endforeach
                </div>

                <div class="plp-filter-block">
                    <h3>Price Range</h3>
                    <div class="plp-price-row">
                        <input type="number" id="plpMinPrice" value="{{ $minPrice }}" min="{{ $minPrice }}" max="{{ $maxPrice }}">
                        <span>to</span>
                        <input type="number" id="plpMaxPrice" value="{{ $maxPrice }}" min="{{ $minPrice }}" max="{{ $maxPrice }}">
                    </div>
                    <input type="range" id="plpPriceRange" min="{{ $minPrice }}" max="{{ $maxPrice }}" value="{{ $maxPrice }}">
                    <p>Up to Rs. <span id="plpPriceLabel">{{ number_format($maxPrice) }}</span></p>
                </div>
            </aside>

            {{-- ── RESULTS AREA ── --}}
            <div class="plp-results">

                {{-- Toolbar --}}
                <div class="plp-toolbar">
                    <div>
                        <strong><span id="plpVisible">{{ $productsList->count() }}</span> products found</strong>
                        <p>Showing <span id="plpRange">{{ $productsList->isNotEmpty() ? '1-'.$productsList->count() : '0-0' }}</span> of {{ $productsList->count() }}</p>
                    </div>
                    <select id="plpSort" aria-label="Sort products">
                        <option value="default">Default</option>
                        <option value="featured">Featured first</option>
                        <option value="price-low">Price low to high</option>
                        <option value="price-high">Price high to low</option>
                        <option value="name">Name A to Z</option>
                    </select>
                </div>

                {{-- 3-column product grid --}}
                <div class="plp-grid" id="plpGrid">
                    @foreach ($productsList as $index => $product)
                        @php
                            $catSlug = $product->category->slug ?? 'pk';
                            if ($catSlug === 'multivitamins')    { $catSlug = 'pk'; }
                            elseif ($catSlug === 'whey-protein') { $catSlug = 'sk'; }
                            elseif ($catSlug === 'pre-workout')  { $catSlug = 'pu'; }
                            else                                 { $catSlug = 'pk'; }

                            $categoryName = $product->category->name ?? 'Uncategorized';
                            $categoryKey  = \Illuminate\Support\Str::slug($categoryName);

                            $activeVariants = $product->variants
                                ->filter(fn ($v) => $v->is_active && !empty($v->attributes))
                                ->values();

                            $variantGroups = [];
                            foreach ($activeVariants as $variant) {
                                foreach (($variant->attributes ?? []) as $name => $value) {
                                    $value = trim((string) $value);
                                    if ($value === '') continue;
                                    $variantGroups[$name] ??= [];
                                    if (!in_array($value, $variantGroups[$name], true))
                                        $variantGroups[$name][] = $value;
                                }
                            }

                            $selectedVariant    = $activeVariants->firstWhere('is_default', true) ?: $activeVariants->first();
                            $selectedAttributes = $selectedVariant?->attributes ?? [];
                            $selectedLabel      = collect($selectedAttributes)
                                ->filter(fn ($v) => trim((string) $v) !== '')
                                ->map(fn ($v, $k) => $k . ': ' . $v)
                                ->implode(' / ');

                            $stockQty    = (int)  ($selectedVariant?->inventory?->stock_qty  ?? 0);
                            $trackStock  = (bool) ($selectedVariant?->inventory?->track_stock ?? false);
                            $isAvailable = !$trackStock || (($selectedVariant?->inventory?->is_in_stock ?? true) && $stockQty > 0);

                            $frontendVariants = $activeVariants->map(function ($v) {
                                $sq = (int)  ($v->inventory?->stock_qty  ?? 0);
                                $ts = (bool) ($v->inventory?->track_stock ?? false);
                                return [
                                    'id'            => $v->id,
                                    'name'          => $v->name,
                                    'attributes'    => $v->attributes ?? [],
                                    'price'         => (float) $v->display_price,
                                    'compare_price' => (float) ($v->display_compare_price ?? 0),
                                    'stock_qty'     => $sq,
                                    'track_stock'   => $ts,
                                    'available'     => !$ts || (($v->inventory?->is_in_stock ?? true) && $sq > 0),
                                ];
                            })->values()->all();

                            $cardPrice        = (float) ($selectedVariant?->display_price         ?? $product->display_price);
                            $cardComparePrice = (float) ($selectedVariant?->display_compare_price ?? $product->display_compare_price ?? 0);
                            $rating           = $product->reviews->avg('rating') ?? 5;
                            $reviewCount      = $product->reviews->count() > 0 ? $product->reviews->count() : 2;
                            $defaultImage     = $product->primaryImage ?: $product->images->first();
                            $hoverImage       = $product->images->where('id', '!=', $defaultImage?->id)->first() ?: $defaultImage;
                        @endphp

                        <div class="pc pc-{{ $catSlug }} plp-card {{ $selectedVariant ? 'has-variants' : 'no-variants' }}"
                            data-order="{{ $index }}"
                            data-cat="{{ $categoryKey }}"
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
                                        <img src="{{ asset('storage/' . $hoverImage->image_path) }}"  alt="{{ $product->name }}" class="hover-img"   loading="lazy" decoding="async">
                                    @else
                                        <img src="{{ asset('img/productt.png') }}" alt="{{ $product->name }}" class="default-img" loading="lazy" decoding="async">
                                        <img src="{{ asset('img/productt.png') }}" alt="{{ $product->name }}" class="hover-img"   loading="lazy" decoding="async">
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
                                    <a href="{{ route('product.show', $product->slug) }}" style="color:inherit;text-decoration:none">{{ $product->name }}</a>
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
                                                                data-value="{{ $value }}">{{ $value }}</button>
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
                                    <button class="btn-add badd-{{ $catSlug }}"
                                        data-id="{{ $product->id }}"
                                        data-variant-id="{{ $selectedVariant?->id }}">Add to Cart +</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="plp-empty" id="plpEmpty" hidden>
                    <h3>No products found</h3>
                    <p>Please clear filters or search another product.</p>
                </div>
            </div>{{-- /plp-results --}}
        </div>{{-- /plp-layout --}}
    </section>
@endsection

@push('styles')
<style>
/* ── Product Listing Page ── */
.plp-page {
    background: var(--cr);
    padding: 56px 3% 80px;
}

.plp-layout {
    display: grid;
    grid-template-columns: 260px minmax(0, 1fr);
    gap: 28px;
    max-width: 1500px;
    margin: 0 auto;
}

/* ── Sidebar ── */
.plp-sidebar {
    align-self: start;
    background: #fff;
    border: 2.5px solid var(--pkl);
    border-radius: 26px;
    box-shadow: 0 18px 54px rgba(255,77,143,.08);
    padding: 24px 22px;
    position: sticky;
    top: 110px;
}

.plp-sidebar-head {
    align-items: center;
    display: flex;
    justify-content: space-between;
    padding-bottom: 20px;
}

.plp-sidebar-head h2 {
    color: var(--dk);
    font-family: 'Nunito', sans-serif;
    font-size: 1.15rem;
    font-weight: 900;
}

.plp-sidebar-head button {
    background: var(--pkl);
    border: 0;
    border-radius: 999px;
    color: var(--pk);
    cursor: pointer;
    font-family: 'Nunito', sans-serif;
    font-size: .78rem;
    font-weight: 900;
    padding: 8px 16px;
    transition: background .2s;
}

.plp-sidebar-head button:hover { background: var(--pk); color: #fff; }

.plp-filter-block {
    border-top: 1px solid rgba(13,0,32,.08);
    padding: 18px 0;
}

.plp-filter-block:last-child { padding-bottom: 0; }

.plp-filter-block h3 {
    color: var(--dk);
    font-family: 'Nunito', sans-serif;
    font-size: .8rem;
    font-weight: 900;
    letter-spacing: 2px;
    margin-bottom: 12px;
    text-transform: uppercase;
}

#plpSearch {
    background: #fff;
    border: 2px solid rgba(255,214,232,.95);
    border-radius: 14px;
    color: var(--dk);
    font-family: 'DM Sans', sans-serif;
    font-size: .9rem;
    height: 44px;
    outline: none;
    padding: 0 14px;
    width: 100%;
}

#plpSearch:focus { border-color: var(--pk); box-shadow: 0 0 0 4px rgba(255,77,143,.08); }

.plp-cat-btn {
    align-items: center;
    background: #fff;
    border: 2px solid rgba(255,214,232,.95);
    border-radius: 16px;
    color: var(--dk);
    cursor: pointer;
    display: flex;
    font-family: 'Nunito', sans-serif;
    font-size: .88rem;
    font-weight: 900;
    justify-content: space-between;
    margin-bottom: 9px;
    min-height: 46px;
    padding: 8px 12px;
    transition: all .2s;
    width: 100%;
}

.plp-cat-btn strong {
    align-items: center;
    background: #fff1f7;
    border-radius: 50%;
    color: var(--pk);
    display: inline-flex;
    flex-shrink: 0;
    height: 28px;
    justify-content: center;
    width: 28px;
    font-size: .8rem;
}

.plp-cat-btn.active, .plp-cat-btn:hover {
    background: var(--pkl);
    border-color: var(--pk);
    color: var(--pk);
}

.plp-price-row {
    align-items: center;
    display: grid;
    gap: 8px;
    grid-template-columns: 1fr auto 1fr;
    margin-bottom: 8px;
}

.plp-price-row input,
.plp-filter-block p {
    color: #5d5a70;
    font-family: 'Nunito', sans-serif;
    font-weight: 700;
    font-size: .85rem;
}

.plp-price-row input {
    background: #fff;
    border: 2px solid rgba(255,214,232,.95);
    border-radius: 12px;
    height: 40px;
    outline: none;
    padding: 0 10px;
    width: 100%;
}

.plp-price-row input:focus { border-color: var(--pk); }

#plpPriceRange {
    accent-color: var(--pk);
    margin-top: 6px;
    width: 100%;
}

/* ── Results ── */
.plp-results { min-width: 0; }

.plp-toolbar {
    align-items: center;
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 8px 28px rgba(13,0,32,.06);
    display: flex;
    gap: 16px;
    justify-content: space-between;
    margin-bottom: 24px;
    padding: 16px 20px;
}

.plp-toolbar strong {
    color: var(--dk);
    font-family: 'Nunito', sans-serif;
    font-size: .92rem;
    font-weight: 900;
}

.plp-toolbar p {
    color: #626278;
    font-family: 'Nunito', sans-serif;
    font-size: .82rem;
    font-weight: 700;
    margin-top: 2px;
}

#plpSort {
    appearance: auto;
    background: #fff;
    border: 2px solid rgba(255,214,232,.95);
    border-radius: 14px;
    color: var(--dk);
    font-family: 'DM Sans', sans-serif;
    font-size: .88rem;
    min-width: 155px;
    outline: none;
    padding: 9px 12px;
}

/* ── 3-column grid ── */
.plp-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 22px;
    align-items: start;
}

/* Image fills card head — same as homepage */
.plp-grid .pc-head {
    height: 250px;
    padding: 0;
    overflow: hidden;
    position: relative;
}

.plp-grid .pc-head .pc-emoji.p-image {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
}

.plp-grid .pc-head .pc-emoji.p-image img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    padding: 0;
}

.plp-grid .pc { box-shadow: 0 4px 20px rgba(13,0,32,.06); }

.plp-empty {
    margin-top: 24px;
    padding: 36px 24px;
    text-align: center;
    background: #fff;
    border: 1px dashed rgba(36,45,105,.18);
    border-radius: 22px;
}

.plp-empty h3 { font-family: 'Nunito', sans-serif; font-size: 1.1rem; color: var(--dk); margin-bottom: 8px; }
.plp-empty p  { color: #6e6a7b; }
.plp-card[hidden] { display: none !important; }

/* ── Responsive ── */
@media (max-width: 1200px) {
    .plp-layout { grid-template-columns: 230px minmax(0, 1fr); gap: 20px; }
    .plp-grid   { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; }
}

@media (max-width: 992px) {
    .plp-layout { grid-template-columns: 1fr; }
    .plp-sidebar { position: static; margin-bottom: 20px; }
    .plp-grid   { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; }
}

@media (max-width: 700px) {
    .plp-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .plp-grid .pc-head { height: 200px; }
}

@media (max-width: 480px) {
    .plp-page { padding: 36px 4% 60px; }
    .plp-grid { grid-template-columns: 1fr; }
    .plp-grid .pc-head { height: 220px; }
    .plp-toolbar { flex-direction: column; align-items: stretch; }
}
</style>
@endpush

@push('scripts')
<script>
window.addEventListener('DOMContentLoaded', () => {
    const cards    = Array.from(document.querySelectorAll('.plp-card'));
    const grid     = document.getElementById('plpGrid');
    const catBtns  = Array.from(document.querySelectorAll('.plp-cat-btn'));
    const search   = document.getElementById('plpSearch');
    const minInput = document.getElementById('plpMinPrice');
    const maxInput = document.getElementById('plpMaxPrice');
    const range    = document.getElementById('plpPriceRange');
    const label    = document.getElementById('plpPriceLabel');
    const sort     = document.getElementById('plpSort');
    const clearBtn = document.getElementById('plpClear');
    const visible  = document.getElementById('plpVisible');
    const rangeEl  = document.getElementById('plpRange');
    const empty    = document.getElementById('plpEmpty');
    const initMax  = range ? Number(range.max) : 0;
    const initMin  = range ? Number(range.min) : 0;
    let activeCat  = 'all';

    const sortCards = () => {
        if (!grid || !sort) return;
        [...cards].sort((a, b) => {
            if (sort.value === 'price-low')  return +a.dataset.price    - +b.dataset.price;
            if (sort.value === 'price-high') return +b.dataset.price    - +a.dataset.price;
            if (sort.value === 'name')       return a.dataset.name.localeCompare(b.dataset.name);
            if (sort.value === 'featured')   return +b.dataset.featured - +a.dataset.featured;
            return +a.dataset.order - +b.dataset.order;
        }).forEach(c => grid.appendChild(c));
    };

    const filter = () => {
        const q      = (search?.value || '').trim().toLowerCase();
        const minP   = Number(minInput?.value || initMin);
        const maxP   = Number(maxInput?.value || initMax);
        let   count  = 0;

        catBtns.forEach(b => b.classList.toggle('active', b.dataset.cat === activeCat));

        cards.forEach(card => {
            const price = Number(card.dataset.price);
            const show  = (activeCat === 'all' || card.dataset.cat === activeCat)
                       && (!q || card.dataset.search.includes(q))
                       && price >= minP && price <= maxP;
            card.hidden = !show;
            if (show) count++;
        });

        if (label)   label.textContent   = Number(maxInput?.value || initMax).toLocaleString('en-IN');
        if (visible) visible.textContent = count;
        if (rangeEl) rangeEl.textContent = count ? `1-${count}` : '0-0';
        if (empty)   empty.hidden        = count > 0;
        sortCards();
    };

    catBtns.forEach(b => b.addEventListener('click', () => { activeCat = b.dataset.cat; filter(); }));
    search?.addEventListener('input', filter);
    sort?.addEventListener('change', filter);
    minInput?.addEventListener('input', filter);
    maxInput?.addEventListener('input', () => { if (range) range.value = maxInput.value; filter(); });
    range?.addEventListener('input',    () => { if (maxInput) maxInput.value = range.value; filter(); });

    clearBtn?.addEventListener('click', () => {
        activeCat = 'all';
        if (search)   search.value   = '';
        if (minInput) minInput.value = initMin;
        if (maxInput) maxInput.value = initMax;
        if (range)    range.value    = initMax;
        if (sort)     sort.value     = 'default';
        filter();
    });

    filter();

    /* ── variant panel interaction ── */
    document.querySelectorAll('.pc-variant-panel').forEach(panel => {
        const card   = panel.closest('.pc');
        const addBtn = card?.querySelector('.btn-add');
        const variants = (() => { try { return JSON.parse(card?.dataset.variants || '[]'); } catch { return []; } })();

        const findVariant = () => {
            const sel = Object.fromEntries(
                Array.from(panel.querySelectorAll('.pc-option-btn.active[data-attribute]'))
                    .map(b => [b.dataset.attribute, b.dataset.value])
            );
            return variants.find(v => Object.entries(sel).every(([k, val]) => String(v.attributes?.[k] ?? '') === String(val))) || null;
        };

        const apply = () => {
            const v   = findVariant();
            const sel = Array.from(panel.querySelectorAll('.pc-option-btn.active[data-attribute]'))
                            .map(b => `${b.dataset.attribute}: ${b.dataset.value}`).join(' / ');
            const sp  = panel.querySelector('.pc-selected-pill');
            const sk  = panel.querySelector('.pc-stock-pill');
            const pl  = card?.querySelector('[data-price-label]');

            if (sp && sel) { sp.textContent = sel; sp.title = sel; }
            if (card)  card.dataset.selectedVariantId = v?.id || '';
            if (addBtn) addBtn.dataset.variantId = v?.id || '';

            if (v && pl) {
                const p = Number(v.price || 0), cp = Number(v.compare_price || 0);
                pl.innerHTML = `&#8377;${p.toLocaleString('en-IN', {maximumFractionDigits:0})}`;
                if (cp > p) pl.insertAdjacentHTML('beforeend', ` <s>&#8377;${cp.toLocaleString('en-IN', {maximumFractionDigits:0})}</s>`);
            }
            if (sk && v) {
                sk.classList.toggle('out', !v.available);
                sk.textContent = v.available ? (v.track_stock ? `${v.stock_qty} pcs` : 'Available') : 'Out of stock';
            }
        };

        apply();
        panel.querySelectorAll('.pc-option-btn[data-attribute]').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault(); e.stopPropagation();
                const attr = btn.dataset.attribute;
                panel.querySelectorAll(`.pc-option-btn[data-attribute="${CSS.escape(attr)}"]`)
                     .forEach(b => b.classList.toggle('active', b === btn));
                apply();
            });
        });
    });
});
</script>
@endpush
