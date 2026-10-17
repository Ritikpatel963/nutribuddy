<?php

namespace App\Services\Storefront;

use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Support\Facades\Schema;

class HomePageDataService
{
    /** @return array{featuredProducts: \Illuminate\Support\Collection, featuredIngredients: \Illuminate\Support\Collection} */
    public function get(): array
    {
        $featuredProducts = collect();
        if ($this->productTablesAreReady()) {
            try {
                $featuredProducts = Product::where('is_active', true)
                    ->with(['primaryImage', 'category', 'images', 'reviews', 'taxRate', 'variants.inventory', 'variants.product.taxRate'])
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

        return compact('featuredProducts', 'featuredIngredients');
    }

    private function productTablesAreReady(): bool
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

    private function ingredientTablesAreReady(): bool
    {
        try {
            return Schema::hasTable('ingredients')
                && Schema::hasTable('ingredient_benefits');
        } catch (\Throwable) {
            return false;
        }
    }
}
