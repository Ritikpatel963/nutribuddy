<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\CartStoreRequest;
use App\Http\Requests\Frontend\CartUpdateRequest;
use App\Models\Cart;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\PricingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index(Request $request, PricingService $pricingService): JsonResponse
    {
        $cart = $this->resolveUserCart($request);
        $this->assignDefaultVariantsToLegacyRows($cart);
        $this->consolidateCartProductRows($cart);

        $cart->load([
            'items.product.taxRate',
            'items.product.primaryImage',
            'items.product.images',
            'items.product.inventory',
            'items.productVariant.inventory',
        ]);
        $pricing = $pricingService->calculate($cart->items);

        // Append available_stock to each cart item
        $cart->items->each(function ($item) {
            $inventory = $item->product_variant_id
                ? ($item->productVariant?->inventory ?? $item->product?->inventory)
                : $item->product?->inventory;

            if ($inventory && $inventory->track_stock) {
                $item->available_stock = max(0, (int) $inventory->stock_qty - (int) $inventory->reserved_qty);
            } else {
                $item->available_stock = null; // null = unlimited
            }
        });

        return response()->json([
            'cart' => $cart,
            'pricing' => $pricing,
        ]);
    }

    public function store(CartStoreRequest $request, PricingService $pricingService): JsonResponse
    {
        $validated = $request->validated();

        $product = Product::where('is_active', true)->findOrFail($validated['product_id']);
        $variantId = $validated['product_variant_id'] ?? null;

        if ($variantId) {
            ProductVariant::where('product_id', $product->id)
                ->where('is_active', true)
                ->findOrFail($variantId);
        }

        $cart = DB::transaction(function () use ($request, $product, $variantId, $validated) {
            $cart = $this->resolveUserCart($request);
            Cart::whereKey($cart->id)->lockForUpdate()->first();

            $matchingItems = $cart->items()
                ->where('product_id', $product->id)
                ->when($variantId, fn ($query) => $query->where('product_variant_id', $variantId), fn ($query) => $query->whereNull('product_variant_id'))
                ->lockForUpdate()
                ->orderBy('id')
                ->get();
            $item = $matchingItems->first();

            if ($item) {
                $newQuantity = (int) $matchingItems->sum('quantity') + (int) $validated['quantity'];

                $this->assertInventoryForQuantity($product->id, $variantId, $newQuantity);
                $item->update([
                    'product_variant_id' => $variantId,
                    'quantity' => $newQuantity,
                ]);

                $duplicateIds = $matchingItems->where('id', '!=', $item->id)->pluck('id');
                if ($duplicateIds->isNotEmpty()) {
                    $cart->items()->whereIn('id', $duplicateIds)->delete();
                }
            } else {
                $this->assertInventoryForQuantity($product->id, $variantId, (int) $validated['quantity']);
                $cart->items()->create([
                    'product_id' => $product->id,
                    'product_variant_id' => $variantId,
                    'quantity' => (int) $validated['quantity'],
                ]);
            }

            return $cart;
        }, 3);

        $cart->load(['items.product.taxRate', 'items.product.primaryImage', 'items.product.images', 'items.productVariant']);
        $pricing = $pricingService->calculate($cart->items);
        $cartCount = (int) $cart->items->sum('quantity');

        return response()->json([
            'message' => 'Item added to cart successfully.',
            'cart' => $cart,
            'pricing' => $pricing,
            'cart_count' => $cartCount,
        ]);
    }

    public function update(CartUpdateRequest $request, int $itemId, PricingService $pricingService): JsonResponse
    {
        $validated = $request->validated();

        $cart = DB::transaction(function () use ($request, $itemId, $validated) {
            $cart = $this->resolveUserCart($request);
            Cart::whereKey($cart->id)->lockForUpdate()->first();
            $item = $cart->items()->lockForUpdate()->findOrFail($itemId);
            $this->assertInventoryForQuantity($item->product_id, $item->product_variant_id, (int) $validated['quantity']);
            $item->update(['quantity' => (int) $validated['quantity']]);

            return $cart;
        }, 3);

        $cart->load(['items.product.taxRate', 'items.product.primaryImage', 'items.product.images', 'items.productVariant']);
        $pricing = $pricingService->calculate($cart->items);
        $cartCount = (int) $cart->items->sum('quantity');

        return response()->json([
            'message' => 'Cart item updated successfully.',
            'cart' => $cart,
            'pricing' => $pricing,
            'cart_count' => $cartCount,
        ]);
    }

    public function destroy(Request $request, int $itemId, PricingService $pricingService): JsonResponse
    {
        $cart = DB::transaction(function () use ($request, $itemId) {
            $cart = $this->resolveUserCart($request);
            Cart::whereKey($cart->id)->lockForUpdate()->first();
            $item = $cart->items()->lockForUpdate()->findOrFail($itemId);
            $item->delete();

            return $cart;
        }, 3);

        $cart->load(['items.product.taxRate', 'items.product.primaryImage', 'items.product.images', 'items.productVariant']);
        $pricing = $pricingService->calculate($cart->items);
        $cartCount = (int) $cart->items->sum('quantity');

        return response()->json([
            'message' => 'Cart item removed successfully.',
            'cart' => $cart,
            'pricing' => $pricing,
            'cart_count' => $cartCount,
        ]);
    }

    private function resolveUserCart(Request $request): Cart
    {
        $user = $request->user();

        return Cart::firstOrCreate(
            ['user_id' => $user->id],
            ['currency' => 'INR']
        );
    }

    private function consolidateCartProductRows(Cart $cart): void
    {
        $cart->loadMissing('items');

        $cart->items
            ->groupBy(fn ($item) => $item->product_id . '::' . ($item->product_variant_id ?: 'base'))
            ->filter(fn ($items) => $items->count() > 1)
            ->each(function ($items) use ($cart) {
                $primary = $items->sortBy('id')->first();
                $variantId = $items->first(fn ($item) => $item->product_variant_id)?->product_variant_id;

                $primary->update([
                    'product_variant_id' => $primary->product_variant_id ?: $variantId,
                    'quantity' => (int) $items->sum('quantity'),
                ]);

                $cart->items()
                    ->whereIn('id', $items->where('id', '!=', $primary->id)->pluck('id'))
                    ->delete();
            });

        $cart->unsetRelation('items');
    }

    private function assignDefaultVariantsToLegacyRows(Cart $cart): void
    {
        $legacyItems = $cart->items()
            ->whereNull('product_variant_id')
            ->with('product.variants')
            ->get();

        foreach ($legacyItems as $item) {
            if (! $item->product?->is_variant_enabled) {
                continue;
            }

            $variant = $item->product->variants
                ->where('is_active', true)
                ->sortBy([
                    ['is_default', 'desc'],
                    ['position', 'asc'],
                    ['id', 'asc'],
                ])
                ->first();

            if (! $variant) {
                continue;
            }

            $existing = $cart->items()
                ->where('product_id', $item->product_id)
                ->where('product_variant_id', $variant->id)
                ->first();

            if ($existing) {
                $existing->increment('quantity', (int) $item->quantity);
                $item->delete();
                continue;
            }

            $item->update(['product_variant_id' => $variant->id]);
        }

        $cart->unsetRelation('items');
    }

    private function assertInventoryForQuantity(int $productId, ?int $variantId, int $requestedQuantity): void
    {
        $inventoryQuery = Inventory::query()->where('product_id', $productId);
        if ($variantId) {
            $inventoryQuery->where('product_variant_id', $variantId);
        } else {
            $inventoryQuery->whereNull('product_variant_id');
        }

        $inventory = $inventoryQuery->first();
        if (! $inventory || ! $inventory->track_stock) {
            return;
        }

        $available = max(0, (int) $inventory->stock_qty - (int) $inventory->reserved_qty);
        abort_if($available < $requestedQuantity, 422, 'Requested quantity is not available in stock.');
    }
}
