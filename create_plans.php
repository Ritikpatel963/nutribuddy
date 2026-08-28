<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

function createServiceProduct($slug, $name, $price) {
    $p = \App\Models\Product::where('slug', $slug)->first() ?: new \App\Models\Product();
    $p->forceFill([
        'slug' => $slug,
        'name' => $name,
        'sku' => strtoupper(uniqid('DIET-')),
        'base_price' => $price,
        'is_diet_service' => 1,
        'is_active' => 1,
        'category_id' => 1, // assuming category 1 exists
    ]);
    $p->save();
    return $p;
}

$p1 = createServiceProduct('diet-basic', 'Basic Diet Plan', 499);
$p2 = createServiceProduct('diet-plus', 'Plus Diet Plan', 999);
$p3 = createServiceProduct('diet-pro', 'Pro Diet Plan', 1499);
$p4 = createServiceProduct('diet-premium', 'Premium Diet Plan', 2499);

echo "Products created successfully.\n";
