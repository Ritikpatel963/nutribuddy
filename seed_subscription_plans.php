<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Seeding Subscription Plans...\n";

DB::table('subscription_plans')->insert([
    [
        'name' => 'Basic',
        'slug' => 'basic',
        'price' => 499,
        'level' => 'basic',
        'is_active' => true,
        'features' => json_encode(['12 Questions analyzed', 'Basic personalized diet chart']),
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'name' => 'Plus',
        'slug' => 'plus',
        'price' => 999,
        'level' => 'plus',
        'is_active' => true,
        'features' => json_encode(['21 Questions (cumulative)', 'Everything in Basic, plus:', 'Lifestyle tracking', 'Actual eating habits']),
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'name' => 'Pro',
        'slug' => 'pro',
        'price' => 1499,
        'level' => 'pro',
        'is_active' => true,
        'features' => json_encode(['29 Questions (cumulative)', 'Everything in Plus, plus:', 'Health goals integration', 'Clinical screening', 'Gut & digestion check', 'Deficiency screening']),
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'name' => 'Premium',
        'slug' => 'premium',
        'price' => 2499,
        'level' => 'premium',
        'is_active' => true,
        'features' => json_encode(['37 Questions (cumulative)', 'Everything in Pro, plus:', 'Medical history & labs', 'Puberty tracking', 'Budget considerations', 'Monthly progress tracking']),
        'created_at' => now(),
        'updated_at' => now(),
    ],
]);

echo "Done.\n";
