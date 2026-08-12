<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IngredientCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class IngredientCategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.ecommerce.ingredient-categories.index', [
            'categories' => IngredientCategory::withCount('ingredients')->latest()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:ingredient_categories,slug'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['slug'] = $this->uniqueSlug($validated['slug'] ?? $validated['name']);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        $validated['is_active'] = (bool) ($validated['is_active'] ?? true);

        IngredientCategory::create($validated);

        return back()->with('success', 'Ingredient category created successfully.');
    }

    public function update(Request $request, IngredientCategory $ingredientCategory): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:ingredient_categories,slug,' . $ingredientCategory->id],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['slug'] = $this->uniqueSlug($validated['slug'] ?? $validated['name'], $ingredientCategory->id);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

        $ingredientCategory->update($validated);

        return back()->with('success', 'Ingredient category updated successfully.');
    }

    public function destroy(IngredientCategory $ingredientCategory): RedirectResponse
    {
        if ($ingredientCategory->ingredients()->exists()) {
            return back()->with('error', 'Cannot delete category with ingredients.');
        }

        $ingredientCategory->delete();

        return back()->with('success', 'Ingredient category deleted successfully.');
    }

    private function uniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($value) ?: 'category';
        $slug = $baseSlug;
        $suffix = 2;

        while (
            IngredientCategory::withTrashed()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
                ->exists()
        ) {
            $slug = "{$baseSlug}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
