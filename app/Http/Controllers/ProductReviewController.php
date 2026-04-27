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
        ]);

        ProductReview::create([
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            'rating' => $request->rating,
            'comment' => $request->comment,
            'is_active' => false, // Needs admin approval
        ]);

        return back()->with('success', 'Your review has been submitted and is awaiting approval.');
    }
}
