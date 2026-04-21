<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductVariantController extends Controller
{
    public function index(): View
    {
        return view('admin.ecommerce.variants.index', [
            'variants' => ProductVariant::with(['product', 'inventory'])->latest()->get(),
            'products' => Product::where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:255', 'unique:product_variants,sku'],
            'attributes' => ['nullable', 'json'],
            'price' => ['required', 'numeric', 'min:0'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'position' => ['nullable', 'integer', 'min:0'],
            'is_default' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'track_stock' => ['nullable', 'boolean'],
            'stock_qty' => ['nullable', 'integer', 'min:0'],
            'reserved_qty' => ['nullable', 'integer', 'min:0'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0'],
            'is_in_stock' => ['nullable', 'boolean'],
        ]);

        $validated['currency'] = 'INR';
        $validated['attributes'] = isset($validated['attributes']) ? json_decode($validated['attributes'], true) : null;
        $validated['is_default'] = (bool) ($validated['is_default'] ?? false);
        $validated['is_active'] = (bool) ($validated['is_active'] ?? true);

        if ($validated['is_default']) {
            ProductVariant::where('product_id', $validated['product_id'])->update(['is_default' => false]);
        }

        $variant = ProductVariant::create([
            'product_id' => $validated['product_id'],
            'name' => $validated['name'],
            'sku' => $validated['sku'],
            'attributes' => $validated['attributes'],
            'price' => $validated['price'],
            'compare_at_price' => $validated['compare_at_price'] ?? null,
            'cost_price' => $validated['cost_price'] ?? null,
            'currency' => 'INR',
            'is_default' => $validated['is_default'],
            'is_active' => $validated['is_active'],
            'position' => $validated['position'] ?? 0,
        ]);

        Inventory::updateOrCreate(
            ['product_variant_id' => $variant->id],
            [
                'product_id' => $variant->product_id,
                'track_stock' => (bool) ($validated['track_stock'] ?? true),
                'stock_qty' => $validated['stock_qty'] ?? 0,
                'reserved_qty' => $validated['reserved_qty'] ?? 0,
                'low_stock_threshold' => $validated['low_stock_threshold'] ?? 5,
                'is_in_stock' => (bool) ($validated['is_in_stock'] ?? true),
            ]
        );

        Product::whereKey($variant->product_id)->update(['is_variant_enabled' => true, 'product_type' => 'variable']);

        return back()->with('success', 'Variant created successfully.');
    }

    public function update(Request $request, ProductVariant $variant): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:255', Rule::unique('product_variants', 'sku')->ignore($variant->id)],
            'attributes' => ['nullable', 'json'],
            'price' => ['required', 'numeric', 'min:0'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'position' => ['nullable', 'integer', 'min:0'],
            'is_default' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            // Inventory fields
            'stock_qty' => ['nullable', 'integer', 'min:0'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0'],
            'track_stock' => ['nullable', 'boolean'],
            'is_in_stock' => ['nullable', 'boolean'],
        ]);

        $validated['attributes'] = isset($validated['attributes']) ? json_decode($validated['attributes'], true) : null;
        $validated['is_default'] = (bool) ($validated['is_default'] ?? false);
        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

        if ($validated['is_default']) {
            ProductVariant::where('product_id', $variant->product_id)
                ->where('id', '!=', $variant->id)
                ->update(['is_default' => false]);
        }

        $variant->update([
            'name' => $validated['name'],
            'sku' => $validated['sku'],
            'attributes' => $validated['attributes'],
            'price' => $validated['price'],
            'compare_at_price' => $validated['compare_at_price'] ?? null,
            'cost_price' => $validated['cost_price'] ?? null,
            'is_default' => $validated['is_default'],
            'is_active' => $validated['is_active'],
            'position' => $validated['position'] ?? 0,
            'currency' => 'INR',
        ]);

        // Update Inventory if stock fields are present
        if ($request->has('stock_qty')) {
            Inventory::updateOrCreate(
                ['product_variant_id' => $variant->id],
                [
                    'product_id' => $variant->product_id,
                    'track_stock' => (bool) ($request->track_stock ?? false),
                    'stock_qty' => $request->stock_qty,
                    'low_stock_threshold' => $request->low_stock_threshold ?? 5,
                    'is_in_stock' => (bool) ($request->is_in_stock ?? false),
                ]
            );
        }

        return back()->with('success', 'Variant updated successfully.');
    }

    public function updateInventory(Request $request, ProductVariant $variant): RedirectResponse
    {
        $validated = $request->validate([
            'track_stock' => ['nullable', 'boolean'],
            'stock_qty' => ['required', 'integer', 'min:0'],
            'reserved_qty' => ['nullable', 'integer', 'min:0'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0'],
            'is_in_stock' => ['nullable', 'boolean'],
        ]);

        Inventory::updateOrCreate(
            ['product_variant_id' => $variant->id],
            [
                'product_id' => $variant->product_id,
                'track_stock' => (bool) ($validated['track_stock'] ?? false),
                'stock_qty' => $validated['stock_qty'],
                'reserved_qty' => $validated['reserved_qty'] ?? 0,
                'low_stock_threshold' => $validated['low_stock_threshold'] ?? 5,
                'is_in_stock' => (bool) ($validated['is_in_stock'] ?? false),
            ]
        );

        return back()->with('success', 'Variant inventory updated successfully.');
    }

    public function destroy(ProductVariant $variant): RedirectResponse
    {
        $productId = $variant->product_id;

        Inventory::where('product_variant_id', $variant->id)->delete();
        $variant->delete();

        if (! ProductVariant::where('product_id', $productId)->exists()) {
            Product::whereKey($productId)->update(['is_variant_enabled' => false, 'product_type' => 'simple']);
        }

        return back()->with('success', 'Variant deleted successfully.');
    }
}
