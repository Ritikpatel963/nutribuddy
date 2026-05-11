<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProducts = collect();

        if ($this->productTablesAreReady()) {
            try {
                $featuredProducts = Product::where('is_active', true)
                    ->with(['primaryImage', 'category', 'images', 'reviews', 'variants.inventory'])
                    ->latest()
                    ->limit(3)
                    ->get();
            } catch (\Throwable) {
                $featuredProducts = collect();
            }
        }

        $featuredIngredients = collect();

        if ($this->ingredientTablesAreReady()) {
            try {
                $featuredIngredients = Ingredient::where('is_active', true)
                    ->where('is_featured', true)
                    ->with('benefits')
                    ->orderBy('sort_order')
                    ->get();
            } catch (\Throwable) {
                $featuredIngredients = collect();
            }
        }

        return view('pages.index', compact('featuredProducts', 'featuredIngredients'));
    }

    protected function productTablesAreReady(): bool
    {
        try {
            return Schema::hasTable('products')
                && Schema::hasTable('categories')
                && Schema::hasTable('product_images')
                && Schema::hasTable('product_reviews');
        } catch (\Throwable) {
            return false;
        }
    }

    protected function ingredientTablesAreReady(): bool
    {
        try {
            return Schema::hasTable('ingredients')
                && Schema::hasTable('ingredient_benefits');
        } catch (\Throwable) {
            return false;
        }
    }

    public function calendar()
    {
        return view('calendar');
    }

    public function chatMessage()
    {
        return view('chatMessage');
    }

    public function chatempty()
    {
        return view('chatempty');
    }

    public function veiwDetails()
    {
        return view('veiwDetails');
    }

    public function email()
    {
        return view('email');
    }

    public function error1()
    {
        return view('error');
    }

    public function faq()
    {
        return view('faq');
    }

    public function gallery()
    {
        return view('gallery');
    }

    public function kanban()
    {
        return view('kanban');
    }

    public function pricing()
    {
        return view('pricing');
    }

    public function termsCondition()
    {
        return view('termsCondition');
    }

    public function widgets()
    {
        return view('widgets');
    }

    public function chatProfile()
    {
        return view('chatProfile');
    }

    public function blankPage()
    {
        return view('blankPage');
    }

    public function comingSoon()
    {
        return view('comingSoon');
    }

    public function starred()
    {
        return view('starred');
    }

    public function testimonials()
    {
        return view('testimonials');
    }

    public function maintenance()
    {
        return view('maintenance');
    }
}
