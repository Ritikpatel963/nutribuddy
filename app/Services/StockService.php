<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Log;

class StockService
{
    /**
     * Deduct stock for an order item.
     */
    public function deductStock(OrderItem $item): void
    {
        $this->adjustStock($item, 'deduct');
    }

    /**
     * Return stock for an order item.
     */
    public function returnStock(OrderItem $item): void
    {
        $this->adjustStock($item, 'return');
    }

    /**
     * Adjust stock based on order item quantity.
     */
    private function adjustStock(OrderItem $item, string $action): void
    {
        $multiplier = ($action === 'deduct') ? -1 : 1;
        $quantityChange = $item->quantity * $multiplier;

        // 1. Adjust specific inventory (variant or product)
        $inventoryQuery = Inventory::query()
            ->lockForUpdate()
            ->where('product_id', $item->product_id);

        if ($item->product_variant_id) {
            $inventoryQuery->where('product_variant_id', $item->product_variant_id);
        } else {
            $inventoryQuery->whereNull('product_variant_id');
        }

        $inventory = $inventoryQuery->first();

        if ($inventory && $inventory->track_stock) {
            $newQty = (int) $inventory->stock_qty + $quantityChange;
            if ($action === 'deduct' && $newQty < 0) {
                abort(422, 'Some cart items are out of stock.');
            }

            $inventory->update([
                'stock_qty' => max(0, $newQty),
                'is_in_stock' => $newQty > 0,
            ]);
            
            Log::info("Stock {$action}ed for Product #{$item->product_id} (Variant #{$item->product_variant_id}): {$item->quantity}. New Qty: {$newQty}");
        }

        // 2. If it's a variant, also adjust the main product inventory (null variant row)
        if ($item->product_variant_id) {
            $mainInventory = Inventory::query()
                ->lockForUpdate()
                ->where('product_id', $item->product_id)
                ->whereNull('product_variant_id')
                ->first();

            if ($mainInventory && $mainInventory->track_stock) {
                $newMainQty = (int) $mainInventory->stock_qty + $quantityChange;
                if ($action === 'deduct' && $newMainQty < 0) {
                    abort(422, 'Some cart items are out of stock.');
                }

                $mainInventory->update([
                    'stock_qty' => max(0, $newMainQty),
                    'is_in_stock' => $newMainQty > 0,
                ]);
            }
        }

        // 3. Virtual Bundle Pattern: If item is a combo, adjust inventory of each mapped child variant
        $product = $item->product ?: \App\Models\Product::with('comboItems.childVariant')->find($item->product_id);
        if ($product && $product->is_combo) {
            $comboItems = $product->comboItems ?: \App\Models\ProductComboItem::with('childVariant')->where('combo_product_id', $product->id)->get();
            foreach ($comboItems as $comboItem) {
                $childVariant = $comboItem->childVariant;
                if (! $childVariant) {
                    continue;
                }

                $childQtyChange = ($item->quantity * $comboItem->quantity) * $multiplier;

                // Adjust child variant inventory
                $childVariantInventory = Inventory::query()
                    ->lockForUpdate()
                    ->where('product_id', $childVariant->product_id)
                    ->where('product_variant_id', $childVariant->id)
                    ->first();

                if ($childVariantInventory && $childVariantInventory->track_stock) {
                    $newChildQty = (int) $childVariantInventory->stock_qty + $childQtyChange;
                    if ($action === 'deduct' && $newChildQty < 0) {
                        abort(422, "Item '{$childVariant->name}' in combo is out of stock.");
                    }

                    $childVariantInventory->update([
                        'stock_qty' => max(0, $newChildQty),
                        'is_in_stock' => $newChildQty > 0,
                    ]);

                    Log::info("Combo Child Stock {$action}ed for Child Variant #{$childVariant->id}: " . abs($childQtyChange) . ". New Qty: {$newChildQty}");
                }

                // Adjust child product's parent inventory row
                $childMainInventory = Inventory::query()
                    ->lockForUpdate()
                    ->where('product_id', $childVariant->product_id)
                    ->whereNull('product_variant_id')
                    ->first();

                if ($childMainInventory && $childMainInventory->track_stock) {
                    $newChildMainQty = (int) $childMainInventory->stock_qty + $childQtyChange;
                    if ($action === 'deduct' && $newChildMainQty < 0) {
                        abort(422, "Item in combo is out of stock.");
                    }

                    $childMainInventory->update([
                        'stock_qty' => max(0, $newChildMainQty),
                        'is_in_stock' => $newChildMainQty > 0,
                    ]);
                }
            }
        }
    }
}
