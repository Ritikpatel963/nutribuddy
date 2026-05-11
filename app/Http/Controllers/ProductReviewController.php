<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductReview;
;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
            'review_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('review_image')) {
            $imagePath = $request->file('review_image')->store('reviews', 'public');
        }

        ProductReview::create([
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            'rating' => $request->rating,
            'comment' => $request->comment,
            'image_path' => $imagePath,
            'is_active' => true, // Assuming auto-approve for now or keep false if needed
        ]);

        return back()->with('success', 'Your review has been submitted and is awaiting approval.');
    }

    public function userIndex(Request $request)
    {
        $userId = Auth::id();
        
        // Get all products purchased by the user
        // An item is considered purchased if the order is delivered or completed
        $purchasedProducts = \App\Models\OrderItem::whereHas('order', function($query) use ($userId) {
                $query->where('user_id', $userId)
                      ->whereIn('status', ['delivered', 'completed']);
            })
            ->with(['product' => function($q) {
                $q->with(['primaryImage', 'reviews' => function($rq) {
                    $rq->where('user_id', Auth::id());
                }]);
            }])
            ->get()
            ->unique('product_id')
            ->pluck('product');

        // Get reviews already written by the user
        $userReviews = ProductReview::where('user_id', $userId)
            ->with('product.primaryImage')
            ->latest()
            ->paginate(10);

        return view('pages.user-panel.reviews', compact('purchasedProducts', 'userReviews'));
    }
}
