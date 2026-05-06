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
        $review->update([
            'is_active' => $request->boolean('is_active'),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => $review->is_active,
            ]);
        }

        return redirect()->back()->with('success', 'Review status updated successfully.');
    }

    public function destroy(ProductReview $review)
    {
        $review->delete();

        return redirect()->back()->with('success', 'Review deleted successfully.');
    }
}
