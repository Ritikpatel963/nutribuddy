@extends('layouts.main')
@section('title', $product->meta_title ?? $product->name . " – India's #1 Kids Wellness Gummy")
@section('meta_description', $product->meta_description ?? $product->short_description)
@section('meta_keywords', $product->meta_keywords)

@section('content')
    <!-- ══ PRODUCT HERO ══ -->
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
                        <img src="{{ asset('img/productt.png') }}" alt="{{ $product->name }}" id="mainPdpImage">
                    @endif
                </div>
            </div>
            <div class="thumb-row">
                @foreach($product->images as $image)
                    <div class="thumb {{ $image->is_primary ? 'active' : '' }}" onclick="changePdpImage(this, '{{ asset('storage/' . $image->image_path) }}')">
                        <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $product->name }}">
                    </div>
                @endforeach
            </div>
        </div>

        <!-- RIGHT: Info -->
        <div class="pdp-info">
            <div class="pdp-cat">{{ $product->category->name ?? 'Uncategorized' }} · Kids 2–14 yrs</div>
            <h1 class="pdp-name">{{ $product->name }}</h1>
            <div class="pdp-rating">
                <div class="stars">
                    @php $rating = $product->reviews->avg('rating') ?? 5; @endphp
                    @for($i=0; $i<5; $i++)
                        {{ $i < $rating ? '★' : '☆' }}
                    @endfor
                </div>
                <div class="rating-val">{{ number_format($rating, 1) }}</div>
                <div class="rating-divider"></div>
                <div class="rating-count">{{ $product->reviews->count() }} Verified Reviews</div>
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
                @if($product->is_variant_enabled && $product->variants->count() > 0)
                <!-- Variant Selectors -->
                <div class="variant-block">
                    <div class="variant-label">Options: </div>
                    <div class="variant-row">
                        @foreach($product->variants as $variant)
                            <div class="vopt {{ $loop->first ? 'active' : '' }}" 
                                 onclick="selectVariant(this, '{{ $variant->id }}', '{{ $variant->price }}', '{{ $variant->compare_at_price }}')">
                                {{ $variant->name }}
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Static info for theme consistency -->
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
                    </div>
                </div>
            </div>

            <!-- Pincode Check -->
            <div class="pincode-row">
                <div class="pincode-label">📍</div>
                <input type="text" maxlength="6" placeholder="Enter pincode to check delivery date" id="pincodeInput">
                <button onclick="checkPincode()">Check</button>
            </div>
            <div id="pincode-result" style="font-size:.82rem;color:var(--mn);font-weight:700;margin-bottom:14px;display:none;padding: 0 4px;">✅ Delivery by Tomorrow!</div>

            <!-- CTAs -->
            <div class="cta-row">
                <button class="btn-cart" onclick="addToCart('{{ $product->id }}')">Add to Cart</button>
                <button class="btn-buy" onclick="buyNow('{{ $product->id }}')">Buy Now</button>
            </div>

            <!-- Guarantees -->
            <div class="guarantees">
                <div class="guarantee"><div class="g-icon">🚚</div><div class="g-title">Free Shipping</div><div class="g-sub">On orders ₹200+</div></div>
                <div class="guarantee"><div class="g-icon">🔄</div><div class="g-title">30-Day Return</div><div class="g-sub">No questions asked</div></div>
                <div class="guarantee"><div class="g-icon">🔒</div><div class="g-title">Secure Payment</div><div class="g-sub">UPI · Cards · COD</div></div>
            </div>

            <!-- Product Highlights -->
            <div class="highlights">
                <h4>Why Parents Love {{ $product->name }}</h4>
                <ul class="highlight-list">
                    @php 
                        $features = explode("\n", $product->description);
                        $features = array_filter(array_map('trim', $features));
                    @endphp
                    @foreach(array_slice($features, 0, 6) as $feature)
                        <li><div class="hl-dot"></div>{{ preg_replace('/^[•\-\*]\s*/', '', $feature) }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <!-- ══ DESCRIPTION & DETAILS ══ -->
    <section class="section-wrap reveal">
        <div style="max-width:1200px;margin:0 auto;">
            <h2 class="sec-title">Product <span class="acc">Details</span></h2>
            <div class="pdp-description">
                {!! nl2br(e($product->description)) !!}
            </div>
        </div>
    </section>

    <!-- ══ RELATED PRODUCTS ══ -->
    @if($relatedProducts->count() > 0)
    <section class="section-wrap reveal">
        <div style="max-width:1200px;margin:0 auto;">
            <h2 class="sec-title">You May Also <span class="acc">Like</span></h2>
            <div class="products-grid">
                @foreach($relatedProducts as $rel)
                <div class="pc pc-{{ $rel->category->slug ?? 'pk' }}">
                    <div class="pc-head pc-head-{{ $rel->category->slug ?? 'pk' }}">
                        <a href="{{ route('product.show', $rel->slug) }}" class="pc-emoji p-image">
                            @if($rel->primaryImage)
                                <img src="{{ asset('storage/' . $rel->primaryImage->image_path) }}" alt="{{ $rel->name }}" class="default-img">
                            @else
                                <img src="{{ asset('img/productt.png') }}" alt="{{ $rel->name }}" class="default-img">
                            @endif
                        </a>
                    </div>
                    <div class="pc-body">
                        <div class="pc-cat cat-{{ $rel->category->slug ?? 'pk' }}">{{ $rel->category->name }}</div>
                        <div class="pc-name"><a href="{{ route('product.show', $rel->slug) }}" style="color:inherit;text-decoration:none">{{ $rel->name }}</a></div>
                        <div class="pc-foot">
                            <div class="pc-price">₹{{ number_format($rel->base_price, 0) }}</div>
                            <button class="btn-add badd-{{ $rel->category->slug ?? 'pk' }}" onclick="addToCart('{{ $rel->id }}')">Add +</button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

@endsection

@push('scripts')
<script>
    function changePdpImage(el, src) {
        document.getElementById('mainPdpImage').src = src;
        document.querySelectorAll('.thumb').forEach(t => t.classList.remove('active'));
        el.classList.add('active');
    }

    function selectVariant(el, id, price, comparePrice) {
        document.querySelectorAll('.vopt').forEach(v => v.classList.remove('active'));
        el.classList.add('active');
        
        // Update price display if needed
        // For now, let's just highlight the selection
    }

    function checkPincode() {
        const pin = document.getElementById('pincodeInput').value;
        if(pin.length === 6) {
            document.getElementById('pincode-result').style.display = 'block';
        }
    }

    function addToCart(productId) {
        // Implement AJAX add to cart
        alert('Product added to cart!');
    }

    function buyNow(productId) {
        // Redirect to checkout with this product
        window.location.href = "{{ route('checkout') }}?product_id=" + productId;
    }
</script>
@endpush
