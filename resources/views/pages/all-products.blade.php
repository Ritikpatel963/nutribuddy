@extends('layouts.main')
@section('title', "All Products – NutriBuddy")

@section('content')
    <section class="products-section reveal" id="products" style="padding-top: 120px;">
        <span class="sec-eye">Our Products</span>
        <h2 class="sec-title">Nutrition Kids <span class="acc">Actually Love</span></h2>
        <p class="sec-sub">Each product crafted with Ayurvedic wisdom + modern science. Balanced doses, kid-safe, genuinely
            delicious flavors.</p>
        <div class="products-grid">
            @foreach($products as $product)
            @php 
                $catSlug = $product->category->slug ?? 'pk';
                // Map database slugs to CSS classes if they don't match
                if ($catSlug == 'multivitamins') $catSlug = 'pk';
                elseif ($catSlug == 'whey-protein') $catSlug = 'sk';
                elseif ($catSlug == 'pre-workout') $catSlug = 'pu';
                else $catSlug = 'pk';
            @endphp
            <div class="pc pc-{{ $catSlug }}">
                <div class="pc-head pc-head-{{ $catSlug }}">
                    <a href="{{ route('product.show', $product->slug) }}" class="pc-emoji p-image">
                        @if($product->primaryImage)
                            <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" alt="{{ $product->name }}" class="default-img">
                            @php $secondImage = $product->images->where('is_primary', false)->first(); @endphp
                            @if($secondImage)
                                <img src="{{ asset('storage/' . $secondImage->image_path) }}" alt="{{ $product->name }}" class="hover-img">
                            @else
                                <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" alt="{{ $product->name }}" class="hover-img">
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
                        @for($i=0; $i<5; $i++){{ $i < $rating ? '★' : '☆' }}@endfor
                        <span style="color:#aaa;font-size:.75rem;font-family:'DM Sans',sans-serif">
                            ({{ $product->reviews->count() > 0 ? $product->reviews->count() : '2,841' }} reviews)
                        </span>
                    </div>
                    <div class="pc-cat cat-{{ $catSlug }}">{{ $product->category->name ?? 'Uncategorized' }}</div>
                    <div class="pc-name"><a href="{{ route('product.show', $product->slug) }}" style="color: inherit; text-decoration: none;">{{ $product->name }}</a></div>
                    <div class="pc-features">
                        
                            <div class="newcarda">
                                <span><i>🛡️</i> Boosts Immunity</span>
                                <span><i>📈</i> Supports Growth</span>
                            </div>
                            <div class="newcarda">
                                <span><i>⚡</i> Increases Energy</span>
                                <span><i>😊</i> Improves Mood</span>
                            </div>
                    </div>
                    <div class="pc-foot">
                        <div class="pc-price">
                            ₹{{ number_format($product->base_price, 0) }} 
                            @if($product->compare_at_price > $product->base_price)
                                <s>₹{{ number_format($product->compare_at_price, 0) }}</s>
                            @endif
                        </div>
                        <button class="btn-add badd-{{ $catSlug }}" data-id="{{ $product->id }}">Add to Cart +</button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>
@endsection
