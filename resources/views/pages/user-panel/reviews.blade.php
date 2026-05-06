@extends('layouts.user-panel')
@section('title', 'My Reviews — NutriBuddy Kids')
@section('panel-page-class', 'panel-reviews')
@section('panel-content')
    <style>
        .compact-list-item {
            background: #fff;
            border: 1px solid #eee;
            border-radius: 15px;
            padding: 15px 20px;
            margin-bottom: 12px;
            transition: all 0.2s;
        }
        .compact-list-item:hover {
            border-color: var(--pk);
            background: #fffcfd;
        }
        .star-opt { 
            transition: all 0.2s ease; 
            display: inline-block; 
            cursor: pointer;
            font-size: 1.5rem;
            color: #eee;
        }
        .review-img-upload {
            position: relative; 
            width: 100%; 
            height: 80px; 
            border: 2px dashed #eee; 
            border-radius: 12px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            background: #fafafa; 
            cursor: pointer; 
        }
        .review-status-badge {
            padding: 3px 10px; 
            border-radius: 50px; 
            font-size: 0.7rem; 
            font-weight: 700; 
            display: inline-flex; 
            align-items: center; 
            gap: 4px;
        }
        .p-img-compact {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
        }
    </style>

    <div class="panel-header" style="margin-bottom: 30px;">
        <h2 style="font-family: 'Fredoka One', cursive; color: var(--dk); font-size: 1.8rem; margin: 0;">Product <span style="color: var(--pk)">Reviews</span></h2>
        <p style="color: #888; font-size: 0.95rem;">Manage and track your product feedback</p>
    </div>

    <div class="row">
        <div class="col-12">
            <h5 style="font-family: 'Fredoka One', cursive; color: var(--dk); margin-bottom: 20px; font-size: 1.1rem; border-left: 4px solid var(--pk); padding-left: 12px;">Pending Your Review</h5>

            @forelse($purchasedProducts as $product)
                @php
                    $existingReview = $product->reviews->first();
                @endphp
                <div class="compact-list-item">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <img src="{{ $product->primaryImage ? asset('storage/' . $product->primaryImage->image_path) : asset('img/product2.png') }}" class="p-img-compact">
                        </div>
                        <div class="col">
                            <h6 style="font-weight: 800; margin: 0; color: var(--dk);">{{ $product->name }}</h6>
                            <span style="font-size: 0.75rem; color: #aaa;">{{ $product->category->name ?? 'Immunity & Growth' }}</span>
                        </div>
                        <div class="col-auto">
                            @if($existingReview)
                                <span class="review-status-badge" style="background: #E8F9F1; color: #00A87A;">✅ Reviewed</span>
                            @else
                                <button class="btn-main" onclick="toggleReviewForm('{{ $product->id }}')" style="padding: 8px 20px; border-radius: 50px; font-size: 0.85rem; background: var(--pk);">
                                    Rate Now
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Hidden Compact Form -->
                    <div id="reviewForm_{{ $product->id }}" style="display: none; margin-top: 15px; padding-top: 15px; border-top: 1px dashed #eee;">
                        <form action="{{ route('reviews.store', $product->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-4 text-center">
                                    <label style="display:block; font-weight:700; font-size:0.75rem; color:#888; margin-bottom:5px;">YOUR RATING</label>
                                    <div class="rating-input_{{ $product->id }}">
                                        @for($i=1; $i<=5; $i++)
                                            <span data-val="{{ $i }}" class="star-opt star-opt-{{ $product->id }}">★</span>
                                        @endfor
                                    </div>
                                    <input type="hidden" name="rating" id="ratingValue_{{ $product->id }}" value="5">
                                </div>
                                <div class="col-md-5">
                                    <textarea name="comment" rows="2" style="width:100%; padding:10px 15px; border-radius:12px; border:1px solid #eee; font-size: 0.9rem; background: #fafafa;" placeholder="Tell us what you think..." required></textarea>
                                </div>
                                <div class="col-md-3">
                                    <div class="review-img-upload" onclick="this.querySelector('input').click()" style="margin-bottom: 10px;">
                                        <span style="font-size: 0.75rem; color: #999; font-weight: 600;">+ Add Photo</span>
                                        <input type="file" name="review_image" accept="image/*" style="display: none;">
                                    </div>
                                    <button type="submit" class="btn-main w-100" style="padding: 10px; border-radius: 12px; font-size: 0.85rem;">Post 🚀</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @empty
                <div style="text-align: center; padding: 40px; background: #fafafa; border-radius: 15px; border: 1px dashed #ddd;">
                    <p style="color: #999; margin: 0;">No products to review yet.</p>
                </div>
            @endforelse

            <!-- MODERN CARD-BASED HISTORY -->
            @if($userReviews->count() > 0)
                <h5 style="font-family: 'Fredoka One', cursive; color: var(--dk); margin: 50px 0 25px; font-size: 1.15rem; border-left: 4px solid var(--mn); padding-left: 15px;">My Review History</h5>
                
                <div class="review-history-grid" style="display: grid; gap: 20px;">
                    @foreach($userReviews as $review)
                        <div class="history-card" style="background: #fff; border: 1px solid #eee; border-radius: 20px; padding: 25px; transition: all 0.3s ease; box-shadow: 0 2px 10px rgba(0,0,0,0.02);">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 18px; flex-wrap: wrap; gap: 15px;">
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <div style="width: 55px; height: 55px; border-radius: 12px; overflow: hidden; border: 1px solid #f0f0f0; flex-shrink: 0;">
                                        <img src="{{ $review->product->primaryImage ? asset('storage/' . $review->product->primaryImage->image_path) : asset('img/product2.png') }}" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                    <div>
                                        <h4 style="font-family: 'Nunito', sans-serif; font-weight: 900; color: var(--dk); margin: 0; font-size: 1rem;">{{ $review->product->name }}</h4>
                                        <div style="color: #FFD700; font-size: 0.9rem; margin-top: 4px; letter-spacing: 1px;">
                                            @for($i=0; $i<5; $i++)
                                                {{ $i < $review->rating ? '★' : '☆' }}
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                                <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 5px;">
                                    <span class="review-status-badge" style="background: {{ $review->is_active ? '#E8F9F1' : '#FFF8E1' }}; color: {{ $review->is_active ? '#00A87A' : '#f57c00' }}; padding: 4px 12px; font-size: 0.65rem; text-transform: uppercase; letter-spacing: 1px;">
                                        {{ $review->is_active ? 'Published' : 'Pending Approval' }}
                                    </span>
                                    <span style="font-size: 0.75rem; color: #bbb; font-weight: 600;">{{ $review->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>

                            <div style="background: #f9f9fb; border-radius: 15px; padding: 18px; position: relative; margin-bottom: 15px;">
                                <div style="font-size: 1.5rem; color: #e0e0e0; position: absolute; top: 10px; left: 10px; font-family: serif; line-height: 1;">"</div>
                                <p style="font-size: 0.95rem; color: #555; line-height: 1.6; margin: 0; padding-left: 15px; font-style: italic;">
                                    {{ $review->comment }}
                                </p>
                            </div>

                            @if($review->image_path)
                                <div style="margin-top: 15px; display: flex; gap: 10px;">
                                    <div style="width: 80px; height: 80px; border-radius: 12px; overflow: hidden; border: 2px solid #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.1); cursor: pointer;" onclick="window.open('{{ asset('storage/' . $review->image_path) }}')">
                                        <img src="{{ asset('storage/' . $review->image_path) }}" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
                
                <style>
                    .history-card:hover {
                        transform: translateY(-4px);
                        border-color: var(--pk);
                        box-shadow: 0 10px 25px rgba(255, 77, 143, 0.08) !important;
                    }
                </style>
                <div style="margin-top: 20px;">
                    {{ $userReviews->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
        function toggleReviewForm(productId) {
            const form = document.getElementById('reviewForm_' + productId);
            if (form.style.display === 'none') {
                form.style.display = 'block';
                form.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                form.style.display = 'none';
            }
        }

        // Star Rating Interaction
        document.querySelectorAll('.star-opt').forEach(star => {
            const productId = star.parentElement.className.split('_')[1];
            
            star.addEventListener('click', function() {
                const val = this.getAttribute('data-val');
                document.getElementById('ratingValue_' + productId).value = val;
                
                // Color stars
                document.querySelectorAll('.star-opt-' + productId).forEach(s => {
                    if(parseInt(s.getAttribute('data-val')) <= parseInt(val)) {
                        s.style.color = '#FFD700'; // Gold
                    } else {
                        s.style.color = '#eee';
                    }
                });
            });
            
            star.addEventListener('mouseover', function() {
                const val = this.getAttribute('data-val');
                document.querySelectorAll('.star-opt-' + productId).forEach(s => {
                    if(parseInt(s.getAttribute('data-val')) <= parseInt(val)) {
                        s.style.color = '#FFD700';
                    } else {
                        s.style.color = '#eee';
                    }
                });
            });
            
            star.addEventListener('mouseout', function() {
                const val = document.getElementById('ratingValue_' + productId).value;
                document.querySelectorAll('.star-opt-' + productId).forEach(s => {
                    if(parseInt(s.getAttribute('data-val')) <= parseInt(val)) {
                        s.style.color = '#FFD700';
                    } else {
                        s.style.color = '#eee';
                    }
                });
            });
        });

        // Default set 5 stars for all forms
        window.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[id^="ratingValue_"]').forEach(input => {
                const productId = input.id.split('_')[1];
                const defaultVal = 5;
                document.querySelectorAll('.star-opt-' + productId).forEach(s => {
                    if(parseInt(s.getAttribute('data-val')) <= defaultVal) {
                        s.style.color = '#FFD700';
                    }
                });
            });
        });
    </script>
@endsection
