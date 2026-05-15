<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$products = App\Models\Product::with(['variants.product.taxRate', 'variants.inventory', 'taxRate', 'inventory'])
    ->where('is_active', true)
    ->get();

foreach ($products as $p) {
    echo "=== PRODUCT: {$p->name} (ID:{$p->id}) ===\n";
    echo "  base_price       = {$p->base_price}\n";
    echo "  compare_at_price = {$p->compare_at_price}\n";
    echo "  is_variant_enabled = " . ($p->is_variant_enabled ? 'YES' : 'NO') . "\n";
    echo "  display_price        = {$p->display_price}\n";
    echo "  display_compare_price = {$p->display_compare_price}\n";
    if ($p->taxRate) {
        echo "  tax_rate = {$p->taxRate->rate}%, show_in_checkout = " . ($p->taxRate->show_in_checkout ? 'YES' : 'NO') . "\n";
    } else {
        echo "  tax_rate = NONE\n";
    }
    if ($p->inventory) {
        echo "  stock: qty={$p->inventory->stock_qty}, reserved={$p->inventory->reserved_qty}, track={$p->inventory->track_stock}\n";
    }
    
    foreach ($p->variants as $v) {
        $stock = $v->inventory;
        echo "  --- Variant: {$v->name} (ID:{$v->id})\n";
        echo "      price            = {$v->price}\n";
        echo "      compare_at_price = {$v->compare_at_price}\n";
        echo "      is_default       = " . ($v->is_default ? 'YES' : 'NO') . "\n";
        echo "      is_active        = " . ($v->is_active ? 'YES' : 'NO') . "\n";
        echo "      display_price        = {$v->display_price}\n";
        echo "      display_compare_price = {$v->display_compare_price}\n";
        if ($stock) {
            echo "      stock: qty={$stock->stock_qty}, reserved={$stock->reserved_qty}, track=" . ($stock->track_stock ? 'YES' : 'NO') . "\n";
        }
    }
    echo "\n";
}
