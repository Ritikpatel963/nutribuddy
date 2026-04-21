<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\TaxRate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        return view('admin.ecommerce.products.index', [
            'products' => Product::with(['category', 'taxRate', 'variants', 'inventory', 'images'])->latest()->get(),
            'categories' => Category::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'taxRates' => TaxRate::where('is_active', true)->orderBy('sort_order')->get(['id', 'name', 'rate']),
        ]);
    }

    public function create(): View
    {
        return view('admin.ecommerce.products.create', [
            'categories' => Category::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'taxRates' => TaxRate::where('is_active', true)->orderBy('sort_order')->get(['id', 'name', 'rate']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'tax_rate_id' => ['nullable', 'exists:tax_rates,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:products,slug'],
            'sku' => ['required', 'string', 'max:255', 'unique:products,sku'],
            'product_type' => ['required', Rule::in(['simple', 'variable'])],
            'is_variant_enabled' => ['nullable', 'boolean'],
            'brand' => ['nullable', 'string', 'max:255'],
            'hsn_code' => ['nullable', 'string', 'max:50'],
            'short_description' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'shipping_price' => ['nullable', 'numeric', 'min:0'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'meta_keywords' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            // Inventory fields
            'is_in_stock' => ['nullable', 'boolean'],
            'images' => ['nullable', 'array'],
            'images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['currency'] = 'INR';
        $validated['is_variant_enabled'] = (bool) ($validated['is_variant_enabled'] ?? false);
        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);
        $validated['is_featured'] = (bool) ($validated['is_featured'] ?? false);

        $product = Product::create($validated);

        // Create Inventory if stock_qty is provided
        if ($request->has('stock_qty')) {
            Inventory::create([
                'product_id' => $product->id,
                'product_variant_id' => null,
                'track_stock' => (bool) ($request->track_stock ?? true),
                'stock_qty' => $request->stock_qty ?? 0,
                'low_stock_threshold' => $request->low_stock_threshold ?? 5,
                'is_in_stock' => (bool) ($request->is_in_stock ?? true),
            ]);
        }

        // Handle Image Uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');
                \App\Models\ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => $index === 0,
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()->route('admin.ecommerce.products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product): View
    {
        $product->load('inventory');
        return view('admin.ecommerce.products.edit', [
            'product' => $product,
            'categories' => Category::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'taxRates' => TaxRate::where('is_active', true)->orderBy('sort_order')->get(['id', 'name', 'rate']),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'tax_rate_id' => ['nullable', 'exists:tax_rates,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:products,slug,' . $product->id],
            'sku' => ['required', 'string', 'max:255', 'unique:products,sku,' . $product->id],
            'product_type' => ['required', Rule::in(['simple', 'variable'])],
            'is_variant_enabled' => ['nullable', 'boolean'],
            'brand' => ['nullable', 'string', 'max:255'],
            'hsn_code' => ['nullable', 'string', 'max:50'],
            'short_description' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'shipping_price' => ['nullable', 'numeric', 'min:0'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'meta_keywords' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            // Inventory fields
            'is_in_stock' => ['nullable', 'boolean'],
            'images' => ['nullable', 'array'],
            'images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['currency'] = 'INR';
        $validated['is_variant_enabled'] = (bool) ($validated['is_variant_enabled'] ?? false);
        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);
        $validated['is_featured'] = (bool) ($validated['is_featured'] ?? false);

        $product->update($validated);

        // Update Inventory if inventory fields are present
        if ($request->has('stock_qty')) {
            Inventory::updateOrCreate(
                ['product_id' => $product->id, 'product_variant_id' => null],
                [
                    'track_stock' => (bool) ($request->track_stock ?? false),
                    'stock_qty' => $request->stock_qty,
                    'low_stock_threshold' => $request->low_stock_threshold ?? 5,
                    'is_in_stock' => (bool) ($request->is_in_stock ?? false),
                ]
            );
        }

        // Handle Additional Image Uploads
        if ($request->hasFile('images')) {
            $lastSortOrder = $product->images()->max('sort_order') ?? -1;
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');
                \App\Models\ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => !$product->images()->where('is_primary', true)->exists() && $index === 0,
                    'sort_order' => $lastSortOrder + $index + 1,
                ]);
            }
        }

        return redirect()->route('admin.ecommerce.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return back()->with('success', 'Product deleted successfully.');
    }

    public function updateInventory(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'track_stock' => ['nullable', 'boolean'],
            'stock_qty' => ['required', 'integer', 'min:0'],
            'reserved_qty' => ['nullable', 'integer', 'min:0'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0'],
            'is_in_stock' => ['nullable', 'boolean'],
        ]);

        Inventory::updateOrCreate(
            ['product_id' => $product->id, 'product_variant_id' => null],
            [
                'track_stock' => (bool) ($validated['track_stock'] ?? false),
                'stock_qty' => $validated['stock_qty'],
                'reserved_qty' => $validated['reserved_qty'] ?? 0,
                'low_stock_threshold' => $validated['low_stock_threshold'] ?? 5,
                'is_in_stock' => (bool) ($validated['is_in_stock'] ?? false),
            ]
        );

        return back()->with('success', 'Product inventory updated successfully.');
    }

    public function deleteImage(\App\Models\ProductImage $image): RedirectResponse
    {
        if (file_exists(storage_path('app/public/' . $image->image_path))) {
            unlink(storage_path('app/public/' . $image->image_path));
        }
        $image->delete();
        return back()->with('success', 'Image removed successfully.');
    }
}
