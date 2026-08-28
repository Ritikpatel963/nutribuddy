<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DietPlanController extends Controller
{
    /**
     * Display a listing of the diet service plans.
     */
    public function index(): View
    {
        $plans = SubscriptionPlan::all();
        return view('admin.diet-plans.index', compact('plans'));
    }

    /**
     * Show the form for editing the specified plan.
     */
    public function edit($id): View
    {
        $diet_plan = SubscriptionPlan::findOrFail($id);
        return view('admin.diet-plans.edit', compact('diet_plan'));
    }

    /**
     * Update the specified plan in storage.
     */
    public function update(Request $request, $id)
    {
        $diet_plan = SubscriptionPlan::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'monthly_price' => ['nullable', 'numeric', 'min:0'],
            'quarterly_price' => ['nullable', 'numeric', 'min:0'],
            'annual_price' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $diet_plan->update([
            'name' => $validated['name'],
            'monthly_price' => $validated['monthly_price'] ?? null,
            'quarterly_price' => $validated['quarterly_price'] ?? null,
            'annual_price' => $validated['annual_price'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.ecommerce.diet-plans.index')
            ->with('success', 'Diet Plan updated successfully.');
    }
}
