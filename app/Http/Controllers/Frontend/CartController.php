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

class CartController extends Controller
{
    public function index(Request $request, PricingService $pricingService): JsonResponse
    {
        $cart = $this->resolveUserCart($request)->load(['items.product.taxRate', 'items.productVariant']);
        $pricing = $pricingService->calculate($cart->items);

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

        $cart = $this->resolveUserCart($request);
        $item = $cart->items()
            ->where('product_id', $product->id)
            ->where('product_variant_id', $variantId)
            ->first();

        if ($item) {
            $newQuantity = $item->quantity + (int) $validated['quantity'];
            $this->assertInventoryForQuantity($product->id, $variantId, $newQuantity);
            $item->update(['quantity' => $newQuantity]);
        } else {
            $this->assertInventoryForQuantity($product->id, $variantId, (int) $validated['quantity']);
            $cart->items()->create([
                'product_id' => $product->id,
                'product_variant_id' => $variantId,
                'quantity' => (int) $validated['quantity'],
            ]);
        }

        $cart->load(['items.product.taxRate', 'items.productVariant']);
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

        $cart = $this->resolveUserCart($request);
        $item = $cart->items()->findOrFail($itemId);
        $this->assertInventoryForQuantity($item->product_id, $item->product_variant_id, (int) $validated['quantity']);
        $item->update(['quantity' => (int) $validated['quantity']]);

        $cart->load(['items.product.taxRate', 'items.productVariant']);
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
        $cart = $this->resolveUserCart($request);
        $item = $cart->items()->findOrFail($itemId);
        $item->delete();

        $cart->load(['items.product.taxRate', 'items.productVariant']);
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
