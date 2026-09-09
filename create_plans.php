<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$p1 = \App\Models\Product::firstOrCreate(
    ['slug' => 'diet-basic'],
    ['name' => 'Basic Diet Plan', 'price' => 0, 'is_diet_service' => 1, 'stock_quantity' => 1000]
);
$p2 = \App\Models\Product::firstOrCreate(
    ['slug' => 'diet-plus'],
    ['name' => 'Plus Diet Plan', 'price' => 999, 'is_diet_service' => 1, 'stock_quantity' => 1000]
);
$p3 = \App\Models\Product::firstOrCreate(
    ['slug' => 'diet-pro'],
    ['name' => 'Pro Diet Plan', 'price' => 1499, 'is_diet_service' => 1, 'stock_quantity' => 1000]
);
$p4 = \App\Models\Product::firstOrCreate(
    ['slug' => 'diet-premium'],
    ['name' => 'Premium Diet Plan', 'price' => 2499, 'is_diet_service' => 1, 'stock_quantity' => 1000]
);

echo "Products created successfully.\n";
