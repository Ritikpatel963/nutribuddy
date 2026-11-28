<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductSubscriptionPlan;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class ProductSubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        // Global default subscription plans (product_id = null)
        $defaultPlans = [
            [
                'duration_months' => 2,
                'month_1_price' => 649.00,
                'recurring_price' => 599.00,
                'badge_text' => null,
                'free_gift_label' => null,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'duration_months' => 3,
                'month_1_price' => 649.00,
                'recurring_price' => 579.00,
                'badge_text' => 'Recommended',
                'free_gift_label' => null,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'duration_months' => 4,
                'month_1_price' => 649.00,
                'recurring_price' => 569.00,
                'badge_text' => 'Best value',
                'free_gift_label' => 'Free Tasty Kadha',
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($defaultPlans as $planData) {
            ProductSubscriptionPlan::updateOrCreate(
                [
                    'product_id' => null,
                    'duration_months' => $planData['duration_months'],
                ],
                $planData
            );
        }
    }
}
