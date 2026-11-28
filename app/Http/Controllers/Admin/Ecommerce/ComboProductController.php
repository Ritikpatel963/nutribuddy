<?php

namespace App\Http\Controllers\Admin\Ecommerce;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Ecommerce\StoreProductRequest;
use App\Http\Requests\Admin\Ecommerce\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\TaxRate;
use App\Services\Ecommerce\ProductContentService;
use App\Services\Ecommerce\ProductManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ComboProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::where('is_combo', true)
            ->with(['category', 'taxRate', 'variants.inventory', 'comboItems.childVariant.product', 'subscriptionPlans', 'images']);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        return view('admin.ecommerce.combos.index', [
            'combos' => $query->latest()->get(),
            'categories' => Category::where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function create(): View
    {
        return view('admin.ecommerce.combos.create', [
            'categories' => Category::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'taxRates' => TaxRate::where('is_active', true)->orderBy('sort_order')->get(['id', 'name', 'rate']),
            'availableVariants' => ProductVariant::with(['product:id,name', 'inventory:id,product_variant_id,stock_qty'])
                ->where('is_active', true)
                ->get(),
        ]);
    }

    public function store(StoreProductRequest $request, ProductManagementService $products): RedirectResponse
    {
        try {
            $request->merge(['is_combo' => 1]);
            $validated = $request->validated();
            $validated['is_combo'] = 1;

            $products->store($request, $validated);

            return redirect()->route('admin.ecommerce.combos.index')->with('success', 'Combo pack created successfully.');
        } catch (\Throwable $e) {
            report($e);

            return back()->withInput()->with('error', 'Unable to create combo pack: '.$e->getMessage());
        }
    }

    public function edit(Product $product, ProductContentService $content): View
    {
        $product->load(['comboItems.childVariant.product', 'subscriptionPlans', 'images']);

        return view('admin.ecommerce.combos.edit', [
            'product' => $product,
            'categories' => Category::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'taxRates' => TaxRate::where('is_active', true)->orderBy('sort_order')->get(['id', 'name', 'rate']),
            'availableVariants' => ProductVariant::with(['product:id,name', 'inventory:id,product_variant_id,stock_qty'])
                ->where('is_active', true)
                ->where('product_id', '!=', $product->id)
                ->get(),
            'problemSolutionDefaults' => $content->defaultProblemSolutionContent(),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product, ProductManagementService $products): RedirectResponse
    {
        try {
            $request->merge(['is_combo' => 1]);
            $validated = $request->validated();
            $validated['is_combo'] = 1;

            $products->update($request, $product, $validated);

            return redirect()->route('admin.ecommerce.combos.index')->with('success', 'Combo pack updated successfully.');
        } catch (\Throwable $e) {
            report($e);

            return back()->withInput()->with('error', 'Unable to update combo pack: '.$e->getMessage());
        }
    }

    public function destroy(Product $product, ProductManagementService $products): RedirectResponse
    {
        $products->destroy($product);

        return redirect()->route('admin.ecommerce.combos.index')->with('success', 'Combo pack deleted successfully.');
    }
}
