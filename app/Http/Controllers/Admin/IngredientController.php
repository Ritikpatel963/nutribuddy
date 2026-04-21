<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\IngredientCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class IngredientController extends Controller
{
    public function index(): View
    {
        return view('admin.ecommerce.ingredients.index', [
            'ingredients' => Ingredient::with(['category', 'benefits'])->latest()->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.ecommerce.ingredients.create', [
            'categories' => IngredientCategory::where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ingredient_category_id' => ['nullable', 'exists:ingredient_categories,id'],
            'main_heading' => ['required', 'string', 'max:255'],
            'short_heading' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'dosage_heading_one' => ['nullable', 'string', 'max:255'],
            'dosage_heading_two' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'icon' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:5120'],
            'benefits' => ['nullable', 'array'],
            'benefits.*' => ['nullable', 'string', 'max:255'],
        ]);

        if ($request->hasFile('icon')) {
            $validated['icon_path'] = $request->file('icon')->store('ingredients/icons', 'public');
        }

        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        $validated['is_active'] = (bool) ($validated['is_active'] ?? true);

        unset($validated['icon'], $validated['benefits']);

        $ingredient = Ingredient::create($validated);

        $this->syncBenefits($ingredient, $request->input('benefits', []));

        return redirect()->route('admin.ecommerce.ingredients.index')->with('success', 'Ingredient created successfully.');
    }

    public function show(Ingredient $ingredient): View
    {
        return view('admin.ecommerce.ingredients.show', [
            'ingredient' => $ingredient->load(['category', 'benefits']),
        ]);
    }

    public function edit(Ingredient $ingredient): View
    {
        return view('admin.ecommerce.ingredients.edit', [
            'ingredient' => $ingredient->load('benefits'),
            'categories' => IngredientCategory::where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Ingredient $ingredient): RedirectResponse
    {
        $validated = $request->validate([
            'ingredient_category_id' => ['nullable', 'exists:ingredient_categories,id'],
            'main_heading' => ['required', 'string', 'max:255'],
            'short_heading' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'dosage_heading_one' => ['nullable', 'string', 'max:255'],
            'dosage_heading_two' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'icon' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:5120'],
            'benefits' => ['nullable', 'array'],
            'benefits.*' => ['nullable', 'string', 'max:255'],
        ]);

        if ($request->hasFile('icon')) {
            if ($ingredient->icon_path && Storage::disk('public')->exists($ingredient->icon_path)) {
                Storage::disk('public')->delete($ingredient->icon_path);
            }
            $validated['icon_path'] = $request->file('icon')->store('ingredients/icons', 'public');
        }

        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

        unset($validated['icon'], $validated['benefits']);

        $ingredient->update($validated);

        $this->syncBenefits($ingredient, $request->input('benefits', []));

        return redirect()->route('admin.ecommerce.ingredients.index')->with('success', 'Ingredient updated successfully.');
    }

    public function destroy(Ingredient $ingredient): RedirectResponse
    {
        if ($ingredient->icon_path && Storage::disk('public')->exists($ingredient->icon_path)) {
            Storage::disk('public')->delete($ingredient->icon_path);
        }

        $ingredient->delete();

        return back()->with('success', 'Ingredient deleted successfully.');
    }

    private function syncBenefits(Ingredient $ingredient, array $benefits): void
    {
        $ingredient->benefits()->delete();

        $rows = collect($benefits)
            ->filter(fn ($heading) => filled($heading))
            ->values()
            ->map(fn ($heading, $index) => [
                'heading' => trim((string) $heading),
                'sort_order' => $index,
            ])
            ->all();

        if (!empty($rows)) {
            $ingredient->benefits()->createMany($rows);
        }
    }
}
