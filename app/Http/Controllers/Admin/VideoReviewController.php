<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Http\Request;

class VideoReviewController extends Controller
{
    public function index()
    {
        $reviews = ProductReview::with(['product', 'user'])
            ->whereNotNull('video_path')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('admin.ecommerce.video-reviews.index', compact('reviews'));
    }

    public function update(Request $request, ProductReview $video_review)
    {
        $video_review->update([
            'is_active' => $request->boolean('is_active'),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => $video_review->is_active,
            ]);
        }

        return redirect()->back()->with('success', 'Video review status updated successfully.');
    }

    public function destroy(ProductReview $video_review)
    {
        $video_review->delete();

        return redirect()->back()->with('success', 'Video review deleted successfully.');
    }
}
