<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = ProductReview::with(['product', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('admin.ecommerce.reviews.index', compact('reviews'));
    }

    public function update(Request $request, ProductReview $review)
    {
        $request->validate([
            'is_active' => 'boolean',
        ]);

        $review->update([
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->back()->with('success', 'Review status updated successfully.');
    }

    public function destroy(ProductReview $review)
    {
        $review->delete();

        return redirect()->back()->with('success', 'Review deleted successfully.');
    }
}
