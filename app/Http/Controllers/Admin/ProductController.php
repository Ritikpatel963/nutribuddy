<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\TaxRate;
use Illuminate\Support\Facades\Storage;
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
            'attributes' => Attribute::where('is_active', true)->orderBy('position')->orderBy('name')->get(),
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
            'dosage' => ['nullable', 'string', 'max:255'],
            'coins_reward' => ['nullable', 'integer', 'min:0'],
            'stock_qty' => ['required', 'integer', 'min:0'],
            'track_stock' => ['nullable', 'boolean'],
            // Inventory fields
            'is_in_stock' => ['nullable', 'boolean'],
            'tags' => ['nullable', 'array'],
            'product_attributes' => ['nullable', 'array'],
            'product_attribute_values' => ['nullable', 'array'],
            'variations' => ['nullable', 'array'],
            'variations.*.id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'variations.*.name' => ['nullable', 'string', 'max:255'],
            'variations.*.sku' => ['required_with:variations', 'string', 'max:255'],
            'variations.*.attributes' => ['nullable', 'array'],
            'variations.*.price' => ['required_with:variations', 'numeric', 'min:0'],
            'variations.*.compare_at_price' => ['nullable', 'numeric', 'min:0'],
            'variations.*.cost_price' => ['nullable', 'numeric', 'min:0'],
            'variations.*.stock_qty' => ['nullable', 'integer', 'min:0'],
            'variations.*.track_stock' => ['nullable', 'boolean'],
            'variations.*.is_in_stock' => ['nullable', 'boolean'],
            'variations.*.is_default' => ['nullable', 'boolean'],
            'variations.*.is_active' => ['nullable', 'boolean'],
            'images' => ['nullable', 'array'],
            'images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ]);

        $variations = $this->normalizedVariations($validated['variations'] ?? []);
        $hasVariations = ! empty($variations);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['currency'] = 'INR';
        $validated['product_type'] = $hasVariations ? 'variable' : 'simple';
        $validated['is_variant_enabled'] = $hasVariations;
        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);
        $validated['is_featured'] = (bool) ($validated['is_featured'] ?? false);
        $validated['variant_types'] = json_decode($validated['variant_types'] ?? '[]', true);

        unset($validated['product_attributes'], $validated['product_attribute_values'], $validated['variations']);

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

        if ($hasVariations) {
            $this->syncProductVariations($product, $variations);
        } else {
            $variant = $this->ensureSimpleVariant($product);

            Inventory::create([
                'product_id' => $product->id,
                'product_variant_id' => $variant->id,
                'track_stock' => (bool) ($request->track_stock ?? true),
                'stock_qty' => $request->stock_qty ?? 0,
                'is_in_stock' => ($request->stock_qty ?? 0) > 0,
            ]);
        }

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
            'attributes' => Attribute::where('is_active', true)->orderBy('position')->orderBy('name')->get(),
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
            'dosage' => ['nullable', 'string', 'max:255'],
            'coins_reward' => ['nullable', 'integer', 'min:0'],
            'stock_qty' => ['required', 'integer', 'min:0'],
            'track_stock' => ['nullable', 'boolean'],
            // Inventory fields
            'is_in_stock' => ['nullable', 'boolean'],
            'tags' => ['nullable', 'array'],
            'product_attributes' => ['nullable', 'array'],
            'product_attribute_values' => ['nullable', 'array'],
            'variations' => ['nullable', 'array'],
            'variations.*.id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'variations.*.name' => ['nullable', 'string', 'max:255'],
            'variations.*.sku' => ['required_with:variations', 'string', 'max:255'],
            'variations.*.attributes' => ['nullable', 'array'],
            'variations.*.price' => ['required_with:variations', 'numeric', 'min:0'],
            'variations.*.compare_at_price' => ['nullable', 'numeric', 'min:0'],
            'variations.*.cost_price' => ['nullable', 'numeric', 'min:0'],
            'variations.*.stock_qty' => ['nullable', 'integer', 'min:0'],
            'variations.*.track_stock' => ['nullable', 'boolean'],
            'variations.*.is_in_stock' => ['nullable', 'boolean'],
            'variations.*.is_default' => ['nullable', 'boolean'],
            'variations.*.is_active' => ['nullable', 'boolean'],
            'images' => ['nullable', 'array'],
            'images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ]);

        $variations = $this->normalizedVariations($validated['variations'] ?? []);
        $hasVariations = ! empty($variations);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['currency'] = 'INR';
        $validated['product_type'] = $hasVariations ? 'variable' : 'simple';
        $validated['is_variant_enabled'] = $hasVariations;
        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);
        $validated['is_featured'] = (bool) ($validated['is_featured'] ?? false);
        $validated['variant_types'] = json_decode($validated['variant_types'] ?? '[]', true);

        unset($validated['product_attributes'], $validated['product_attribute_values'], $validated['variations']);

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

        if ($hasVariations) {
            $this->syncProductVariations($product, $variations, true);
        } else {
            $product->variants()->whereNotNull('attributes')->get()->each(function (ProductVariant $variant) {
                Inventory::where('product_variant_id', $variant->id)->delete();
                $variant->delete();
            });
            $variant = $this->ensureSimpleVariant($product);

            Inventory::updateOrCreate(
                ['product_variant_id' => $variant->id],
                [
                    'product_id' => $product->id,
                    'track_stock' => (bool) ($request->track_stock ?? true),
                    'stock_qty' => $request->stock_qty,
                    'is_in_stock' => $request->stock_qty > 0,
                ]
            );
        }

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
        Storage::disk('public')->delete($image->image_path);

        $image->delete();

        return back()->with('success', 'Image removed successfully.');
    }
    public function quickUpdate(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'flavour' => ['nullable', 'string', 'max:255'],
            'pack_size' => ['nullable', 'string', 'max:255'],
            'age_group' => ['nullable', 'string', 'max:255'],
            'dosage' => ['nullable', 'string', 'max:255'],
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

        $product->update(['dosage' => $validated['dosage'] ?? $product->dosage]);
        
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

    private function normalizedVariations(array $variations): array
    {
        return collect($variations)
            ->map(function (array $variation) {
                $attributes = collect($variation['attributes'] ?? [])
                    ->map(fn ($value) => trim((string) $value))
                    ->filter()
                    ->all();

                if (empty($attributes) || empty($variation['sku'])) {
                    return null;
                }

                $variation['attributes'] = $attributes;
                return $variation;
            })
            ->filter()
            ->values()
            ->all();
    }

    private function syncProductVariations(Product $product, array $variations, bool $deleteMissing = false): void
    {
        $keptVariantIds = [];
        $hasDefault = collect($variations)->contains(fn ($variation) => (bool) ($variation['is_default'] ?? false));

        foreach ($variations as $index => $variationData) {
            $attributes = $variationData['attributes'];
            $name = $variationData['name'] ?: $this->variationName($attributes);
            $variantId = $variationData['id'] ?? null;

            $variant = $variantId
                ? $product->variants()->whereKey($variantId)->first()
                : null;

            if (! $variant) {
                $variant = new ProductVariant(['product_id' => $product->id]);
            }

            $isDefault = $hasDefault
                ? (bool) ($variationData['is_default'] ?? false)
                : $index === 0;

            $variant->fill([
                'product_id' => $product->id,
                'name' => $name,
                'sku' => $variationData['sku'],
                'attributes' => $attributes,
                'price' => $variationData['price'],
                'compare_at_price' => $variationData['compare_at_price'] ?? null,
                'cost_price' => $variationData['cost_price'] ?? null,
                'currency' => 'INR',
                'is_default' => $isDefault,
                'is_active' => (bool) ($variationData['is_active'] ?? false),
                'position' => $index,
            ]);
            $variant->save();

            $keptVariantIds[] = $variant->id;

            Inventory::updateOrCreate(
                ['product_variant_id' => $variant->id],
                [
                    'product_id' => $product->id,
                    'track_stock' => (bool) ($variationData['track_stock'] ?? false),
                    'stock_qty' => $variationData['stock_qty'] ?? 0,
                    'reserved_qty' => 0,
                    'low_stock_threshold' => 5,
                    'is_in_stock' => (bool) ($variationData['is_in_stock'] ?? false),
                ]
            );
        }

        if ($deleteMissing) {
            $variantsToDelete = $product->variants()
                ->when($keptVariantIds, fn ($query) => $query->whereNotIn('id', $keptVariantIds))
                ->get();

            foreach ($variantsToDelete as $variant) {
                Inventory::where('product_variant_id', $variant->id)->delete();
                $variant->delete();
            }
        }
    }

    private function ensureSimpleVariant(Product $product): ProductVariant
    {
        $variant = $product->variants()->where(function ($query) {
            $query->whereNull('attributes')->orWhereJsonLength('attributes', 0);
        })->first();

        if (! $variant) {
            $variant = new ProductVariant(['product_id' => $product->id]);
        }

        $variant->fill([
            'product_id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku . '-DEF',
            'attributes' => null,
            'price' => $product->base_price,
            'compare_at_price' => $product->compare_at_price,
            'cost_price' => $product->cost_price,
            'currency' => 'INR',
            'is_default' => true,
            'is_active' => true,
            'position' => 0,
        ]);
        $variant->save();

        return $variant;
    }

    private function variationName(array $attributes): string
    {
        return collect($attributes)
            ->map(fn ($value, $name) => "{$name}: {$value}")
            ->implode(' / ');
    }
}
