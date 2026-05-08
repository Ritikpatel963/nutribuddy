<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::where('is_active', true)
            ->with(['primaryImage', 'category', 'images', 'reviews', 'taxRate', 'variants.inventory', 'variants' => fn ($query) => $query->where('is_active', true)->orderBy('position')->orderBy('id')])
            ->get();

        return view('pages.all-products', compact('products'));
    }

    public function show($slug)
    {
        $product = Product::with(['category', 'taxRate', 'images', 'variants.inventory', 'variants.product.taxRate', 'reviews.user', 'ingredients.benefits', 'ingredients.category'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->with(['primaryImage', 'category', 'images', 'reviews'])
            ->limit(8)
            ->get();

        if ($relatedProducts->count() < 8) {
            $fallbackProducts = Product::where('id', '!=', $product->id)
                ->where('is_active', true)
                ->whereNotIn('id', $relatedProducts->pluck('id'))
                ->with(['primaryImage', 'category', 'images', 'reviews'])
                ->limit(8 - $relatedProducts->count())
                ->get();

            $relatedProducts = $relatedProducts->concat($fallbackProducts);
        }

        return view('pages.product', compact('product', 'relatedProducts'));
    }
}
