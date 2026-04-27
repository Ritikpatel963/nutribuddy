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
    public function index(Request $request): View
    {
        $query = Product::with(['category', 'taxRate', 'variants', 'inventory', 'images']);

        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        return view('admin.ecommerce.products.index', [
            'products' => $query->latest()->get(),
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
            'variant_types' => ['nullable', 'string'],
            'flavor' => ['nullable', 'string', 'max:255'],
            'pack_size' => ['nullable', 'string', 'max:255'],
            'age_group' => ['nullable', 'string', 'max:255'],
            'stock_qty' => ['required', 'integer', 'min:0'],
            'track_stock' => ['nullable', 'boolean'],
            // Inventory fields
            'is_in_stock' => ['nullable', 'boolean'],
            'tags' => ['nullable', 'array'],
            'images' => ['nullable', 'array'],
            'images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['currency'] = 'INR';
        $validated['is_variant_enabled'] = (bool) ($validated['is_variant_enabled'] ?? false);
        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);
        $validated['is_featured'] = (bool) ($validated['is_featured'] ?? false);
        $validated['variant_types'] = json_decode($validated['variant_types'] ?? '[]', true);

        $product = Product::create($validated);

        // Handle Tags (JSON structure with optional image upload)
        if ($request->has('tags')) {
            $tags = $request->input('tags');
            if ($request->hasFile('tag_images')) {
                foreach ($request->file('tag_images') as $index => $file) {
                    $path = $file->store('tags', 'public');
                    $tags[$index]['icon'] = $path;
                }
            }
            $product->update(['tags' => $tags]);
        }

        // Create Main Inventory and Default Variant (Hidden)
        $variantName = $product->name;
        if ($product->flavor) $variantName .= ' - ' . $product->flavor;
        if ($product->pack_size) $variantName .= ' ' . $product->pack_size;
        if ($product->age_group) $variantName .= ' (' . $product->age_group . ')';

        $variant = \App\Models\ProductVariant::create([
            'product_id'       => $product->id,
            'name'             => $variantName,
            'sku'              => $product->sku . '-DEF',
            'attributes'       => [
                'Flavour'    => $product->flavor ?? '',
                'Pack Size'  => $product->pack_size ?? '',
                'Age Group'  => $product->age_group ?? '',
            ],
            'price'            => $product->base_price,
            'compare_at_price' => $product->compare_at_price,
            'is_active'        => true,
        ]);

        Inventory::create([
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'track_stock' => (bool) ($request->track_stock ?? true),
            'stock_qty' => $request->stock_qty ?? 0,
            'is_in_stock' => ($request->stock_qty ?? 0) > 0,
        ]);

        Inventory::create([
            'product_id' => $product->id,
            'product_variant_id' => null,
            'track_stock' => (bool) ($request->track_stock ?? true),
            'stock_qty' => $request->stock_qty ?? 0,
            'is_in_stock' => ($request->stock_qty ?? 0) > 0,
        ]);

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
        $product->load(['inventory', 'variants', 'variants.inventory', 'images']);
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
            'variant_types' => ['nullable', 'string'],
            'flavor' => ['nullable', 'string', 'max:255'],
            'pack_size' => ['nullable', 'string', 'max:255'],
            'age_group' => ['nullable', 'string', 'max:255'],
            'stock_qty' => ['required', 'integer', 'min:0'],
            'track_stock' => ['nullable', 'boolean'],
            // Inventory fields
            'is_in_stock' => ['nullable', 'boolean'],
            'tags' => ['nullable', 'array'],
            'images' => ['nullable', 'array'],
            'images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['currency'] = 'INR';
        $validated['is_variant_enabled'] = (bool) ($validated['is_variant_enabled'] ?? false);
        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);
        $validated['is_featured'] = (bool) ($validated['is_featured'] ?? false);
        $validated['variant_types'] = json_decode($validated['variant_types'] ?? '[]', true);

        $product->update($validated);

        // Handle Tags (JSON structure with optional image upload)
        if ($request->has('tags')) {
            $tags = $request->input('tags');
            if ($request->hasFile('tag_images')) {
                foreach ($request->file('tag_images') as $index => $file) {
                    $path = $file->store('tags', 'public');
                    $tags[$index]['icon'] = $path;
                }
            }
            $product->update(['tags' => $tags]);
        }

        // Update Default Variant (Hidden) and Inventory
        $variantName = $product->name;
        if ($product->flavor) $variantName .= ' - ' . $product->flavor;
        if ($product->pack_size) $variantName .= ' ' . $product->pack_size;
        if ($product->age_group) $variantName .= ' (' . $product->age_group . ')';

        $variant = $product->variants()->first();
        if (!$variant) {
            $variant = \App\Models\ProductVariant::create([
                'product_id' => $product->id,
                'name' => $variantName,
                'sku' => $product->sku . '-DEF',
                'attributes' => [
                    'Flavour' => $product->flavor ?? '',
                    'Pack Size' => $product->pack_size ?? '',
                    'Age Group' => $product->age_group ?? '',
                ],
                'price' => $product->base_price,
                'compare_at_price' => $product->compare_at_price,
                'is_active' => true,
            ]);
        } else {
            $variant->update([
                'name' => $variantName,
                'attributes' => [
                    'Flavour' => $product->flavor ?? '',
                    'Pack Size' => $product->pack_size ?? '',
                    'Age Group' => $product->age_group ?? '',
                ],
                'price' => $product->base_price,
                'compare_at_price' => $product->compare_at_price,
            ]);
        }

        // Sync inventories
        Inventory::updateOrCreate(
            ['product_variant_id' => $variant->id],
            [
                'product_id' => $product->id,
                'track_stock' => (bool) ($request->track_stock ?? true),
                'stock_qty' => $request->stock_qty,
                'is_in_stock' => $request->stock_qty > 0,
            ]
        );

        Inventory::updateOrCreate(
            ['product_id' => $product->id, 'product_variant_id' => null],
            [
                'track_stock' => (bool) ($request->track_stock ?? true),
                'stock_qty' => $request->stock_qty,
                'is_in_stock' => $request->stock_qty > 0,
            ]
        );

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
    public function quickUpdate(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'flavour' => ['nullable', 'string', 'max:255'],
            'pack_size' => ['nullable', 'string', 'max:255'],
            'age_group' => ['nullable', 'string', 'max:255'],
            'stock_qty' => ['required', 'integer', 'min:0'],
        ]);

        // If it's a simple product or we're updating the default variant
        $variant = $product->variants()->first();
        if (!$variant) {
            // Create a default variant if it doesn't exist
            $variant = \App\Models\ProductVariant::create([
                'product_id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku . '-DEF',
                'attributes' => [
                    'Flavour' => $validated['flavour'] ?? '',
                    'Pack Size' => $validated['pack_size'] ?? '',
                    'Age Group' => $validated['age_group'] ?? '',
                ],
                'price' => $product->base_price,
                'is_active' => true,
            ]);
        } else {
            // Update the existing (likely default) variant
            $attributes = $variant->attributes ?? [];
            $attributes['Flavour'] = $validated['flavour'] ?? '';
            $attributes['Pack Size'] = $validated['pack_size'] ?? '';
            $attributes['Age Group'] = $validated['age_group'] ?? '';
            
            $variant->update([
                'attributes' => $attributes,
            ]);
        }

        // Update inventory for this variant
        Inventory::updateOrCreate(
            ['product_variant_id' => $variant->id],
            [
                'product_id' => $product->id,
                'stock_qty' => $validated['stock_qty'],
                'is_in_stock' => $validated['stock_qty'] > 0,
            ]
        );

        // Update main product inventory as well for consistency
        Inventory::updateOrCreate(
            ['product_id' => $product->id, 'product_variant_id' => null],
            [
                'stock_qty' => $validated['stock_qty'],
                'is_in_stock' => $validated['stock_qty'] > 0,
            ]
        );

        return back()->with('success', 'Product details updated successfully.');
    }
    public function addVariant(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'flavour' => ['required', 'string', 'max:255'],
            'pack_size' => ['required', 'string', 'max:255'],
            'age_group' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0'],
            'stock_qty' => ['required', 'integer', 'min:0'],
            'sku' => ['required', 'string', 'max:255', 'unique:product_variants,sku'],
        ]);

        $variantName = $product->name . ' - ' . $validated['flavour'];
        if ($validated['pack_size']) $variantName .= ' ' . $validated['pack_size'];
        if ($validated['age_group']) $variantName .= ' (' . $validated['age_group'] . ')';

        $variant = \App\Models\ProductVariant::create([
            'product_id' => $product->id,
            'name' => $variantName,
            'sku' => $validated['sku'],
            'attributes' => [
                'Flavour' => $validated['flavour'],
                'Pack Size' => $validated['pack_size'],
                'Age Group' => $validated['age_group'] ?? '',
            ],
            'price' => $validated['price'],
            'compare_at_price' => $validated['compare_at_price'],
            'is_active' => true,
        ]);

        Inventory::create([
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'track_stock' => true,
            'stock_qty' => $validated['stock_qty'],
            'is_in_stock' => $validated['stock_qty'] > 0,
        ]);

        // Mark product as variable if it wasn't already
        if (!$product->is_variant_enabled) {
            $product->update(['is_variant_enabled' => true, 'product_type' => 'variable']);
        }

        return back()->with('success', 'New variant added successfully.');
    }
}
