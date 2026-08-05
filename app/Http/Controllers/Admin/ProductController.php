<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\TaxRate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
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
            'trashCount' => Product::onlyTrashed()->count(),
            'categories' => Category::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'taxRates' => TaxRate::where('is_active', true)->orderBy('sort_order')->get(['id', 'name', 'rate']),
        ]);
    }

    public function trash(Request $request): View
    {
        $query = Product::onlyTrashed()->with(['category', 'taxRate', 'variants', 'inventory', 'images']);

        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        return view('admin.ecommerce.products.trash', [
            'products' => $query->latest('deleted_at')->get(),
            'activeCount' => Product::count(),
            'categories' => Category::where('is_active', true)->orderBy('name')->get(['id', 'name']),
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
            'sku' => ['nullable', 'string', 'max:255', 'unique:products,sku'],
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
            'routine' => ['nullable', 'string', 'max:255'],
            'coins_reward' => ['nullable', 'integer', 'min:0'],
            'stock_qty' => ['nullable', 'integer', 'min:0'],
            'track_stock' => ['nullable', 'boolean'],
            // Inventory fields
            'is_in_stock' => ['nullable', 'boolean'],
            'tags' => ['nullable', 'array'],
            'product_attributes' => ['nullable', 'array'],
            'product_attribute_values' => ['nullable', 'array'],
            'variations' => ['nullable', 'array'],
            'variations.*.id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'variations.*.name' => ['nullable', 'string', 'max:255'],
            'variations.*.sku' => ['nullable', 'string', 'max:255'],
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
            'card_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'card_hover_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'tag_images' => ['nullable', 'array'],
            'tag_images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ] + $this->transformSectionValidationRules());

        $variations = $this->normalizedVariations($validated['variations'] ?? []);
        $this->validateVariationSkus($variations);
        $hasVariations = ! empty($variations);
        $parentStockQty = $hasVariations
            ? collect($variations)->sum(fn ($variation) => (int) ($variation['stock_qty'] ?? 0))
            : (int) $request->input('stock_qty', 0);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['sku'] = $this->uniqueProductSku($validated['sku'] ?? null, $validated['name']);
        $validated['currency'] = 'INR';
        $validated['product_type'] = $hasVariations ? 'variable' : 'simple';
        $validated['is_variant_enabled'] = $hasVariations;
        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);
        $validated['is_featured'] = (bool) ($validated['is_featured'] ?? false);
        $variantTypes = [];
        $submittedAttrValues = $request->input('product_attribute_values') ?? [];
        if (!empty($submittedAttrValues)) {
            $attributesList = \App\Models\Attribute::whereIn('id', array_keys($submittedAttrValues))->get();
            foreach ($attributesList as $attr) {
                $variantTypes[$attr->name] = $submittedAttrValues[$attr->id] ?? [];
            }
        }
        $validated['variant_types'] = $variantTypes;

        unset($validated['product_attributes'], $validated['product_attribute_values'], $validated['variations']);
        unset($validated['images'], $validated['card_image'], $validated['card_hover_image'], $validated['tag_images']);
        $this->unsetTransformSectionFields($validated);

        $product = Product::create($validated);
        $this->storeProductCardImages($request, $product);
        $this->saveTransformSectionContent($request, $product);

        $product->update(['tags' => $this->prepareProductTags($request)]);

        if ($hasVariations) {
            $this->syncProductVariations($product, $variations);
        } else {
            $variant = $this->ensureSimpleVariant($product);

            Inventory::create([
                'product_id' => $product->id,
                'product_variant_id' => $variant->id,
                'track_stock' => (bool) ($request->track_stock ?? true),
                'stock_qty' => $parentStockQty,
                'is_in_stock' => $parentStockQty > 0,
            ]);
        }

        Inventory::create([
            'product_id' => $product->id,
            'product_variant_id' => null,
            'track_stock' => (bool) ($request->track_stock ?? true),
            'stock_qty' => $parentStockQty,
            'is_in_stock' => $parentStockQty > 0,
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

        $this->forgetStorefrontCatalogCache();

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
            'problemSolutionDefaults' => $this->defaultProblemSolutionContent(),
        ]);
    }

    public function problemSolutionIndex(Request $request): View
    {
        $products = Product::with(['category', 'images'])
            ->latest()
            ->get();

        $product = $request->filled('product_id')
            ? $products->firstWhere('id', (int) $request->product_id)
            : $products->first();

        if (! $product && $products->isNotEmpty()) {
            $product = $products->first();
        }

        return view('admin.ecommerce.products.problem-solution', [
            'product' => $product,
            'products' => $products,
            'defaults' => $this->defaultProblemSolutionContent(),
            'isProblemSolutionHub' => true,
        ]);
    }

    public function problemSolution(Product $product): View
    {
        return view('admin.ecommerce.products.problem-solution', [
            'product' => $product,
            'defaults' => $this->defaultProblemSolutionContent(),
        ]);
    }

    public function updateProblemSolution(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'ps_brand_title' => ['nullable', 'string', 'max:255'],
            'ps_product_title' => ['nullable', 'string', 'max:255'],
            'ps_tagline_items' => ['nullable', 'array'],
            'ps_tagline_items.*' => ['nullable', 'string', 'max:120'],
            'ps_left_label' => ['nullable', 'string', 'max:255'],
            'ps_left_cards' => ['nullable', 'array'],
            'ps_left_cards.*.title' => ['nullable', 'string', 'max:120'],
            'ps_left_cards.*.text' => ['nullable', 'string', 'max:255'],
            'ps_left_cards.*.icon' => ['nullable', 'string', 'max:255'],
            'ps_left_card_images' => ['nullable', 'array'],
            'ps_left_card_images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'ps_center_image' => ['nullable', 'string', 'max:255'],
            'ps_center_image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'ps_right_label' => ['nullable', 'string', 'max:255'],
            'ps_right_cards' => ['nullable', 'array'],
            'ps_right_cards.*.title' => ['nullable', 'string', 'max:120'],
            'ps_right_cards.*.text' => ['nullable', 'string', 'max:255'],
            'ps_right_cards.*.icon' => ['nullable', 'string', 'max:255'],
            'ps_right_card_images' => ['nullable', 'array'],
            'ps_right_card_images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'ps_shelf_left_image' => ['nullable', 'string', 'max:255'],
            'ps_shelf_left_image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'ps_shelf_right_image' => ['nullable', 'string', 'max:255'],
            'ps_shelf_right_image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $updates = [
            'ps_brand_title' => trim((string) ($validated['ps_brand_title'] ?? '')) ?: null,
            'ps_product_title' => trim((string) ($validated['ps_product_title'] ?? '')) ?: null,
            'ps_tagline_items' => $this->prepareProblemSolutionTagline($validated['ps_tagline_items'] ?? []),
            'ps_left_label' => trim((string) ($validated['ps_left_label'] ?? '')) ?: null,
            'ps_left_cards' => $this->prepareProblemSolutionCards($request, 'ps_left_cards', 'ps_left_card_images'),
            'ps_center_image' => $validated['ps_center_image'] ?? null,
            'ps_right_label' => trim((string) ($validated['ps_right_label'] ?? '')) ?: null,
            'ps_right_cards' => $this->prepareProblemSolutionCards($request, 'ps_right_cards', 'ps_right_card_images'),
            'ps_shelf_left_image' => $validated['ps_shelf_left_image'] ?? null,
            'ps_shelf_right_image' => $validated['ps_shelf_right_image'] ?? null,
        ];

        $this->replaceProblemSolutionImage($request, $product, $updates, 'ps_center_image', 'ps_center_image_file');
        $this->replaceProblemSolutionImage($request, $product, $updates, 'ps_shelf_left_image', 'ps_shelf_left_image_file');
        $this->replaceProblemSolutionImage($request, $product, $updates, 'ps_shelf_right_image', 'ps_shelf_right_image_file');

        $product->forceFill($updates)->save();
        $this->forgetStorefrontCatalogCache();

        $redirectRoute = $request->boolean('return_to_hub')
            ? route('admin.ecommerce.products.problem-solution.index', ['product_id' => $product->id])
            : route('admin.ecommerce.products.problem-solution.edit', $product);

        return redirect($redirectRoute)->with('success', 'Problem solution section updated successfully.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'tax_rate_id' => ['nullable', 'exists:tax_rates,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:products,slug,' . $product->id],
            'sku' => ['nullable', 'string', 'max:255', Rule::unique('products', 'sku')->ignore($product->id)],
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
            'routine' => ['nullable', 'string', 'max:255'],
            'coins_reward' => ['nullable', 'integer', 'min:0'],
            'stock_qty' => ['nullable', 'integer', 'min:0'],
            'track_stock' => ['nullable', 'boolean'],
            // Inventory fields
            'is_in_stock' => ['nullable', 'boolean'],
            'tags' => ['nullable', 'array'],
            'product_attributes' => ['nullable', 'array'],
            'product_attribute_values' => ['nullable', 'array'],
            'variations' => ['nullable', 'array'],
            'variations.*.id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'variations.*.name' => ['nullable', 'string', 'max:255'],
            'variations.*.sku' => ['nullable', 'string', 'max:255'],
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
            'card_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'card_hover_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'tag_images' => ['nullable', 'array'],
            'tag_images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ] + $this->problemSolutionValidationRules() + $this->transformSectionValidationRules());

        $variations = $this->normalizedVariations($validated['variations'] ?? []);
        $this->validateVariationSkus($variations, $product);
        $hasVariations = ! empty($variations);
        $parentStockQty = $hasVariations
            ? collect($variations)->sum(fn ($variation) => (int) ($variation['stock_qty'] ?? 0))
            : (int) $request->input('stock_qty', 0);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['sku'] = $this->uniqueProductSku($validated['sku'] ?? null, $validated['name'], $product->id);
        $validated['currency'] = 'INR';
        $validated['product_type'] = $hasVariations ? 'variable' : 'simple';
        $validated['is_variant_enabled'] = $hasVariations;
        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);
        $validated['is_featured'] = (bool) ($validated['is_featured'] ?? false);
        $variantTypes = [];
        $submittedAttrValues = $request->input('product_attribute_values') ?? [];
        if (!empty($submittedAttrValues)) {
            $attributesList = \App\Models\Attribute::whereIn('id', array_keys($submittedAttrValues))->get();
            foreach ($attributesList as $attr) {
                $variantTypes[$attr->name] = $submittedAttrValues[$attr->id] ?? [];
            }
        }
        $validated['variant_types'] = $variantTypes;

        unset($validated['product_attributes'], $validated['product_attribute_values'], $validated['variations']);
        unset($validated['images'], $validated['card_image'], $validated['card_hover_image'], $validated['tag_images']);
        $this->unsetProblemSolutionFields($validated);
        $this->unsetTransformSectionFields($validated);

        $product->update($validated);
        $this->storeProductCardImages($request, $product);

        $product->update(['tags' => $this->prepareProductTags($request)]);
        $this->saveProblemSolutionContent($request, $product);
        $this->saveTransformSectionContent($request, $product);

        if ($hasVariations) {
            $this->syncProductVariations($product, $variations, true);
        } else {
            $this->deactivateMissingProductVariations($product);
            $variant = $this->ensureSimpleVariant($product);

            Inventory::updateOrCreate(
                ['product_variant_id' => $variant->id],
                [
                    'product_id' => $product->id,
                    'track_stock' => (bool) ($request->track_stock ?? true),
                    'stock_qty' => $parentStockQty,
                    'is_in_stock' => $parentStockQty > 0,
                ]
            );
        }

        Inventory::updateOrCreate(
            ['product_id' => $product->id, 'product_variant_id' => null],
            [
                'track_stock' => (bool) ($request->track_stock ?? true),
                'stock_qty' => $parentStockQty,
                'is_in_stock' => $parentStockQty > 0,
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

        $this->forgetStorefrontCatalogCache();

        return redirect()->route('admin.ecommerce.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();
        $this->forgetStorefrontCatalogCache();

        return back()->with('success', 'Product moved to trash successfully.');
    }

    public function restore(int $product): RedirectResponse
    {
        $trashedProduct = Product::onlyTrashed()->findOrFail($product);
        $trashedProduct->restore();
        $this->forgetStorefrontCatalogCache();

        return back()->with('success', 'Product restored successfully.');
    }

    public function forceDestroy(int $product): RedirectResponse
    {
        $trashedProduct = Product::onlyTrashed()->with(['images'])->findOrFail($product);

        $this->permanentlyDeleteProduct($trashedProduct);
        $this->forgetStorefrontCatalogCache();

        return back()->with('success', 'Product permanently deleted successfully.');
    }

    public function bulkForceDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_ids' => ['required', 'array', 'min:1'],
            'product_ids.*' => ['integer'],
        ]);

        $products = Product::onlyTrashed()
            ->with(['images'])
            ->whereIn('id', $validated['product_ids'])
            ->get();

        foreach ($products as $product) {
            $this->permanentlyDeleteProduct($product);
        }
        $this->forgetStorefrontCatalogCache();

        return back()->with('success', $products->count() . ' product(s) permanently deleted successfully.');
    }

    private function permanentlyDeleteProduct(Product $product): void
    {
        if ($product->card_image_path) {
            Storage::disk('public')->delete($product->card_image_path);
        }

        if ($product->card_hover_image_path) {
            Storage::disk('public')->delete($product->card_hover_image_path);
        }

        $this->deleteTransformImage($product->transform_main_image);
        collect($product->transform_results ?? [])
            ->pluck('image')
            ->each(fn ($path) => $this->deleteTransformImage($path));

        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $product->forceDelete();
    }

    private function storeProductCardImages(Request $request, Product $product): void
    {
        $updates = [];

        if ($request->hasFile('card_image')) {
            if ($product->card_image_path) {
                Storage::disk('public')->delete($product->card_image_path);
            }

            $updates['card_image_path'] = $request->file('card_image')->store('product-cards', 'public');
        }

        if ($request->hasFile('card_hover_image')) {
            if ($product->card_hover_image_path) {
                Storage::disk('public')->delete($product->card_hover_image_path);
            }

            $updates['card_hover_image_path'] = $request->file('card_hover_image')->store('product-cards', 'public');
        }

        if ($updates) {
            $product->forceFill($updates)->save();
        }
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
        $this->forgetStorefrontCatalogCache();

        return back()->with('success', 'Product inventory updated successfully.');
    }

    public function deleteImage(\App\Models\ProductImage $image): RedirectResponse
    {
        Storage::disk('public')->delete($image->image_path);

        $image->delete();
        $this->forgetStorefrontCatalogCache();

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
                'sku' => $this->uniqueVariantSku($product->sku . '-DEF'),
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
        $this->forgetStorefrontCatalogCache();
        
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
            'sku' => ['nullable', 'string', 'max:255', 'unique:product_variants,sku'],
        ]);

        $variantName = $product->name . ' - ' . $validated['flavour'];
        if ($validated['pack_size']) $variantName .= ' ' . $validated['pack_size'];
        if ($validated['age_group']) $variantName .= ' (' . $validated['age_group'] . ')';

        $variant = \App\Models\ProductVariant::create([
            'product_id' => $product->id,
            'name' => $variantName,
            'sku' => $this->uniqueVariantSku($validated['sku'] ?? null, $variantName),
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
        $this->forgetStorefrontCatalogCache();

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

                if (empty($attributes)) {
                    return null;
                }

                $variation['sku'] = trim((string) ($variation['sku'] ?? ''));
                $variation['attributes'] = $attributes;
                return $variation;
            })
            ->filter()
            ->values()
            ->all();
    }

    private function validateVariationSkus(array $variations, ?Product $product = null): void
    {
        $errors = [];
        $seen = [];
        $ownedVariantIds = $product
            ? $product->variants()->pluck('id')->map(fn ($id) => (int) $id)->all()
            : [];

        foreach ($variations as $index => $variation) {
            $sku = trim((string) ($variation['sku'] ?? ''));
            if ($sku === '') {
                continue;
            }

            $normalizedSku = Str::lower($sku);
            if (isset($seen[$normalizedSku])) {
                $errors["variations.$index.sku"] = "The variation SKU '{$sku}' is already used in this product form.";
                continue;
            }
            $seen[$normalizedSku] = $index;

            $variantId = isset($variation['id']) ? (int) $variation['id'] : null;
            if ($variantId && ! $product) {
                $errors["variations.$index.sku"] = 'Existing variation IDs cannot be submitted when creating a new product.';
                continue;
            }

            if ($variantId && $product && ! in_array($variantId, $ownedVariantIds, true)) {
                $errors["variations.$index.sku"] = "The variation SKU '{$sku}' belongs to another product variant.";
                continue;
            }

            $existingVariant = ProductVariant::where('sku', $sku)
                ->when($variantId, fn ($query) => $query->whereKeyNot($variantId))
                ->first();

            if ($existingVariant && (! $product || (int) $existingVariant->product_id !== (int) $product->id)) {
                $errors["variations.$index.sku"] = "The variation SKU '{$sku}' has already been taken.";
            }
        }

        if ($errors) {
            throw ValidationException::withMessages($errors);
        }
    }

    private function syncProductVariations(Product $product, array $variations, bool $deleteMissing = false): void
    {
        $keptVariantIds = [];
        $hasDefault = collect($variations)->contains(fn ($variation) => (bool) ($variation['is_default'] ?? false));

        // Map submitted attribute values to name arrays for validation
        $allowedValuesByName = [];
        $submittedAttrValues = request()->input('product_attribute_values') ?? [];
        if (!empty($submittedAttrValues)) {
            $attributesList = \App\Models\Attribute::whereIn('id', array_keys($submittedAttrValues))->get();
            foreach ($attributesList as $attr) {
                $allowedValuesByName[strtolower($attr->name)] = array_map(function($v) {
                    return strtolower(trim((string)$v));
                }, $submittedAttrValues[$attr->id] ?? []);
            }
        }

        foreach ($variations as $index => $variationData) {
            $attributes = $variationData['attributes'];
            $name = $variationData['name'] ?: $this->variationName($attributes);
            $variantId = $variationData['id'] ?? null;

            $variant = $variantId
                ? $product->variants()->whereKey($variantId)->first()
                : null;

            if (! $variant && ! empty($variationData['sku'])) {
                $variant = $product->variants()->where('sku', $variationData['sku'])->first();
            }

            if (! $variant) {
                $variant = new ProductVariant(['product_id' => $product->id]);
            }

            $isDefault = $hasDefault
                ? (bool) ($variationData['is_default'] ?? false)
                : $index === 0;

            $isActive = (bool) ($variationData['is_active'] ?? false);
            if ($isActive && !empty($allowedValuesByName)) {
                foreach ($attributes as $key => $value) {
                    $keyLower = strtolower($key);
                    $valueLower = strtolower(trim((string)$value));
                    if (isset($allowedValuesByName[$keyLower])) {
                        $matched = false;
                        foreach ($allowedValuesByName[$keyLower] as $allowedVal) {
                            if ($valueLower === $allowedVal) {
                                $matched = true;
                                break;
                            }
                            if (str_contains($valueLower, 'grape') && str_contains($allowedVal, 'grape')) {
                                $matched = true;
                                break;
                            }
                            if (str_contains($valueLower, 'banana') && str_contains($allowedVal, 'banana')) {
                                $matched = true;
                                break;
                            }
                            if (str_contains($valueLower, 'mango') && str_contains($allowedVal, 'mango')) {
                                $matched = true;
                                break;
                            }
                            if (str_contains($valueLower, 'apple') && str_contains($allowedVal, 'apple')) {
                                $matched = true;
                                break;
                            }
                        }
                        if (!$matched) {
                            $isActive = false;
                            break;
                        }
                    }
                }
            }

            $variant->fill([
                'product_id' => $product->id,
                'name' => $name,
                'sku' => $this->uniqueVariantSku($variationData['sku'] ?? null, $name, $variant->exists ? $variant->id : null),
                'attributes' => $attributes,
                'price' => $variationData['price'],
                'compare_at_price' => $variationData['compare_at_price'] ?? null,
                'cost_price' => $variationData['cost_price'] ?? null,
                'currency' => 'INR',
                'is_default' => $isDefault,
                'is_active' => $isActive,
                'position' => $index,
            ]);
            $variant->save();

            $keptVariantIds[] = $variant->id;

            Inventory::updateOrCreate(
                ['product_variant_id' => $variant->id],
                [
                    'product_id' => $product->id,
                    'track_stock' => true,
                    'stock_qty' => $variationData['stock_qty'] ?? 0,
                    'reserved_qty' => 0,
                    'low_stock_threshold' => 5,
                    'is_in_stock' => (int) ($variationData['stock_qty'] ?? 0) > 0,
                ]
            );
        }

        if ($deleteMissing) {
            $variantsToDeactivate = $product->variants()
                ->when($keptVariantIds, fn ($query) => $query->whereNotIn('id', $keptVariantIds))
                ->get();

            $this->deactivateProductVariants($variantsToDeactivate);
        }
    }

    private function deactivateMissingProductVariations(Product $product): void
    {
        $this->deactivateProductVariants($product->variants()->whereNotNull('attributes')->get());
    }

    private function deactivateProductVariants($variants): void
    {
        foreach ($variants as $variant) {
            $inOrders = \Illuminate\Support\Facades\DB::table('order_items')->where('product_variant_id', $variant->id)->exists();
            $inCarts = \Illuminate\Support\Facades\DB::table('cart_items')->where('product_variant_id', $variant->id)->exists();

            if (!$inOrders && !$inCarts) {
                Inventory::where('product_variant_id', $variant->id)->delete();
                $variant->delete();
            } else {
                $variant->forceFill([
                    'is_active' => false,
                    'is_default' => false,
                ])->save();

                Inventory::where('product_variant_id', $variant->id)->update([
                    'stock_qty' => 0,
                    'is_in_stock' => false,
                ]);
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
            'sku' => $this->uniqueVariantSku($product->sku . '-DEF', $product->name, $variant->exists ? $variant->id : null),
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

    private function uniqueProductSku(?string $sku, string $name, ?int $ignoreProductId = null): string
    {
        $base = trim((string) $sku);
        if ($base === '') {
            $base = 'NB-' . Str::upper(Str::random(4)) . '-' . Str::slug($name ?: 'product');
        }

        return $this->uniqueSkuForModel(Product::class, $base, $ignoreProductId);
    }

    private function prepareProductTags(Request $request): array
    {
        $tags = $request->input('tags', []);
        $tags = is_array($tags) ? $tags : [];

        if ($request->hasFile('tag_images')) {
            foreach ($request->file('tag_images') as $index => $file) {
                if (! $file) {
                    continue;
                }

                if (! isset($tags[$index]) || ! is_array($tags[$index])) {
                    $tags[$index] = [];
                }

                $tags[$index]['icon'] = $file->store('tags', 'public');
            }
        }

        return collect($tags)
            ->filter(fn ($tag) => is_array($tag) && trim((string) ($tag['text'] ?? '')) !== '')
            ->map(fn ($tag) => [
                'icon' => trim((string) ($tag['icon'] ?? '')),
                'text' => trim((string) ($tag['text'] ?? '')),
            ])
            ->values()
            ->all();
    }

    private function transformSectionValidationRules(): array
    {
        return [
            'transform_description' => ['nullable', 'string', 'max:1000'],
            'transform_main_image' => ['nullable', 'string', 'max:255'],
            'transform_main_image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'transform_results' => ['nullable', 'array'],
            'transform_results.*.image' => ['nullable', 'string', 'max:255'],
            'transform_results.*.title' => ['nullable', 'string', 'max:150'],
            'transform_results.*.description' => ['nullable', 'string', 'max:1000'],
            'transform_results.*.week' => ['nullable', 'string', 'max:80'],
            'transform_result_images' => ['nullable', 'array'],
            'transform_result_images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];
    }

    private function unsetTransformSectionFields(array &$validated): void
    {
        foreach (array_keys($this->transformSectionValidationRules()) as $field) {
            unset($validated[$field]);
        }
    }

    private function saveTransformSectionContent(Request $request, Product $product): void
    {
        $mainImage = trim((string) $request->input('transform_main_image', '')) ?: null;

        if ($request->hasFile('transform_main_image_file')) {
            $this->deleteTransformImage($product->transform_main_image);
            $mainImage = $request->file('transform_main_image_file')->store('product-transforms', 'public');
        }

        $results = $request->input('transform_results', []);
        $results = is_array($results) ? $results : [];

        if ($request->hasFile('transform_result_images')) {
            foreach ($request->file('transform_result_images') as $index => $file) {
                if (! $file) {
                    continue;
                }

                if (! isset($results[$index]) || ! is_array($results[$index])) {
                    $results[$index] = [];
                }

                $results[$index]['image'] = $file->store('product-transforms/results', 'public');
            }
        }

        $results = collect($results)
            ->filter(fn ($result) => is_array($result) && (
                trim((string) ($result['image'] ?? '')) !== '' ||
                trim((string) ($result['title'] ?? '')) !== '' ||
                trim((string) ($result['description'] ?? '')) !== '' ||
                trim((string) ($result['week'] ?? '')) !== ''
            ))
            ->map(fn ($result) => [
                'image' => trim((string) ($result['image'] ?? '')),
                'title' => trim((string) ($result['title'] ?? '')),
                'description' => trim((string) ($result['description'] ?? '')),
                'week' => trim((string) ($result['week'] ?? '')),
            ])
            ->values()
            ->all();

        $keptImages = collect($results)->pluck('image')->filter()->all();
        collect($product->transform_results ?? [])
            ->pluck('image')
            ->filter(fn ($path) => $path && ! in_array($path, $keptImages, true))
            ->each(fn ($path) => $this->deleteTransformImage($path));

        $product->forceFill([
            'transform_description' => trim((string) $request->input('transform_description', '')) ?: null,
            'transform_main_image' => $mainImage,
            'transform_results' => $results,
        ])->save();
    }

    private function deleteTransformImage(?string $path): void
    {
        if ($path && ! Str::startsWith($path, ['img/', 'assets/'])) {
            Storage::disk('public')->delete($path);
        }
    }

    private function problemSolutionValidationRules(): array
    {
        return [
            'ps_brand_title' => ['nullable', 'string', 'max:255'],
            'ps_product_title' => ['nullable', 'string', 'max:255'],
            'ps_tagline_items' => ['nullable', 'array'],
            'ps_tagline_items.*' => ['nullable', 'string', 'max:120'],
            'ps_left_label' => ['nullable', 'string', 'max:255'],
            'ps_left_cards' => ['nullable', 'array'],
            'ps_left_cards.*.title' => ['nullable', 'string', 'max:120'],
            'ps_left_cards.*.text' => ['nullable', 'string', 'max:255'],
            'ps_left_cards.*.icon' => ['nullable', 'string', 'max:255'],
            'ps_left_card_images' => ['nullable', 'array'],
            'ps_left_card_images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'ps_center_image' => ['nullable', 'string', 'max:255'],
            'ps_center_image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'ps_right_label' => ['nullable', 'string', 'max:255'],
            'ps_right_cards' => ['nullable', 'array'],
            'ps_right_cards.*.title' => ['nullable', 'string', 'max:120'],
            'ps_right_cards.*.text' => ['nullable', 'string', 'max:255'],
            'ps_right_cards.*.icon' => ['nullable', 'string', 'max:255'],
            'ps_right_card_images' => ['nullable', 'array'],
            'ps_right_card_images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'ps_shelf_left_image' => ['nullable', 'string', 'max:255'],
            'ps_shelf_left_image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'ps_shelf_right_image' => ['nullable', 'string', 'max:255'],
            'ps_shelf_right_image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];
    }

    private function unsetProblemSolutionFields(array &$validated): void
    {
        foreach (array_keys($this->problemSolutionValidationRules()) as $field) {
            unset($validated[$field]);
        }
    }

    private function saveProblemSolutionContent(Request $request, Product $product): void
    {
        $updates = [
            'ps_brand_title' => trim((string) $request->input('ps_brand_title', '')) ?: null,
            'ps_product_title' => trim((string) $request->input('ps_product_title', '')) ?: null,
            'ps_tagline_items' => $this->prepareProblemSolutionTagline($request->input('ps_tagline_items', [])),
            'ps_left_label' => trim((string) $request->input('ps_left_label', '')) ?: null,
            'ps_left_cards' => $this->prepareProblemSolutionCards($request, 'ps_left_cards', 'ps_left_card_images'),
            'ps_center_image' => $request->input('ps_center_image'),
            'ps_right_label' => trim((string) $request->input('ps_right_label', '')) ?: null,
            'ps_right_cards' => $this->prepareProblemSolutionCards($request, 'ps_right_cards', 'ps_right_card_images'),
            'ps_shelf_left_image' => $request->input('ps_shelf_left_image'),
            'ps_shelf_right_image' => $request->input('ps_shelf_right_image'),
        ];

        $this->replaceProblemSolutionImage($request, $product, $updates, 'ps_center_image', 'ps_center_image_file');
        $this->replaceProblemSolutionImage($request, $product, $updates, 'ps_shelf_left_image', 'ps_shelf_left_image_file');
        $this->replaceProblemSolutionImage($request, $product, $updates, 'ps_shelf_right_image', 'ps_shelf_right_image_file');

        $product->forceFill($updates)->save();
    }

    private function prepareProblemSolutionTagline(array $items): array
    {
        return collect($items)
            ->map(fn ($item) => trim((string) $item))
            ->filter()
            ->values()
            ->all();
    }

    private function prepareProblemSolutionCards(Request $request, string $field, string $fileField): array
    {
        $cards = $request->input($field, []);
        $cards = is_array($cards) ? $cards : [];

        if ($request->hasFile($fileField)) {
            foreach ($request->file($fileField) as $index => $file) {
                if (! $file) {
                    continue;
                }

                if (! isset($cards[$index]) || ! is_array($cards[$index])) {
                    $cards[$index] = [];
                }

                $cards[$index]['icon'] = $file->store('problem-solution', 'public');
            }
        }

        return collect($cards)
            ->filter(fn ($card) => is_array($card) && (
                trim((string) ($card['title'] ?? '')) !== '' ||
                trim((string) ($card['text'] ?? '')) !== '' ||
                trim((string) ($card['icon'] ?? '')) !== ''
            ))
            ->map(fn ($card) => [
                'icon' => trim((string) ($card['icon'] ?? '')),
                'title' => trim((string) ($card['title'] ?? '')),
                'text' => trim((string) ($card['text'] ?? '')),
            ])
            ->values()
            ->all();
    }

    private function replaceProblemSolutionImage(Request $request, Product $product, array &$updates, string $column, string $fileField): void
    {
        if (! $request->hasFile($fileField)) {
            $updates[$column] = trim((string) ($updates[$column] ?? '')) ?: null;
            return;
        }

        if ($product->{$column}) {
            Storage::disk('public')->delete($product->{$column});
        }

        $updates[$column] = $request->file($fileField)->store('problem-solution', 'public');
    }

    private function defaultProblemSolutionContent(): array
    {
        return [
            'brand_title' => 'Nutribuddy',
            'product_title' => 'Immunity Booster Gummies',
            'tagline_items' => ['Daily Nutrition', 'Stronger Immunity', 'Healthier You'],
            'left_label' => "Power Of\nNature",
            'left_cards' => [
                ['icon' => 'img/haldi.webp', 'title' => 'TURMERIC', 'text' => "Fights germs &\nsupports immunity"],
                ['icon' => 'img/Amla.webp', 'title' => 'AMLA', 'text' => "Rich in Vitamin C,\nstrengthens body defenses"],
                ['icon' => 'img/adrak.png', 'title' => 'GINGER', 'text' => "Soothes throat &\nhelps fight infections"],
            ],
            'center_image' => 'img/product2.png',
            'right_label' => "Daily Goodness\nIn Every Gummy!",
            'right_cards' => [
                ['icon' => 'img/new-btn-2.png', 'title' => 'VITAMINS & MINERALS', 'text' => 'Daily nutrition to build strong immunity'],
                ['icon' => 'img/bb1.png', 'title' => 'NATURAL & SAFE', 'text' => 'Made with natural ingredients'],
                ['icon' => 'img/c4.png', 'title' => 'YUMMY & FUN', 'text' => 'Delicious gummies kids will love'],
                ['icon' => 'img/new-btn-3.png', 'title' => 'MODERN SCIENCE', 'text' => 'Formulated with care and research'],
            ],
            'shelf_left_image' => 'img/Amla.webp',
            'shelf_right_image' => 'img/haldi.webp',
        ];
    }

    private function uniqueVariantSku(?string $sku, ?string $name = null, ?int $ignoreVariantId = null): string
    {
        $base = trim((string) $sku);
        if ($base === '') {
            $base = 'NBV-' . Str::upper(Str::random(4)) . '-' . Str::slug($name ?: 'variant');
        }

        return $this->uniqueSkuForModel(ProductVariant::class, $base, $ignoreVariantId);
    }

    private function uniqueSkuForModel(string $modelClass, string $baseSku, ?int $ignoreId = null): string
    {
        $baseSku = Str::limit(trim($baseSku), 220, '');
        $candidate = $baseSku !== '' ? $baseSku : 'NB-' . Str::upper(Str::random(8));
        $counter = 1;

        while (
            $modelClass::where('sku', $candidate)
                ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
                ->exists()
        ) {
            $suffix = '-' . $counter++;
            $candidate = Str::limit($baseSku, 255 - strlen($suffix), '') . $suffix;
        }

        return $candidate;
    }

    private function variationName(array $attributes): string
    {
        return collect($attributes)
            ->map(fn ($value, $name) => "{$name}: {$value}")
            ->implode(' / ');
    }

    private function forgetStorefrontCatalogCache(): void
    {
        Cache::forget('storefront.product_catalog_meta.v1');
        Cache::forget('storefront.product_catalog_meta.v2');
    }
}
