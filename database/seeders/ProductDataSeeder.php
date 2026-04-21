<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\TaxRate;
use App\Models\Inventory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): array
    {
        // 1. Create a Standard Tax Rate
        $taxRate = TaxRate::firstOrCreate(
            ['code' => 'GST18'],
            [
                'name' => 'Standard GST 18%',
                'rate' => 18.00,
                'description' => 'Standard GST for health products',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        // 2. Define standard categories for NutriBuddy
        $categoriesNames = [
            'Whey Protein',
            'Multivitamins',
            'Pre-Workout',
            'Post-Workout',
            'Healthy Snacks',
            'Fitness Gear'
        ];

        $categories = [];
        foreach ($categoriesNames as $name) {
            $categories[] = Category::firstOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'description' => "Quality sample products for " . $name,
                    'is_active' => true,
                    'sort_order' => 1,
                    'meta_title' => $name . " | NutriBuddy",
                    'meta_description' => "Browse our range of " . $name
                ]
            );
        }

        // 3. Generate products for each category
        foreach ($categories as $category) {
            Product::factory()
                ->count(rand(5, 8))
                ->create([
                    'category_id' => $category->id,
                    'tax_rate_id' => $taxRate->id,
                ])
                ->each(function ($product) {
                    // Create Inventory for each product
                    Inventory::factory()->create([
                        'product_id' => $product->id,
                    ]);
                });
        }

        return $categories;
    }
}
