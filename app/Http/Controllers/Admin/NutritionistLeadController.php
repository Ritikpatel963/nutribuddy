<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Nutrition\DietPlan;
use App\Models\NutritionLead;
use App\Models\Product;
use App\Services\Nutrition\FoodFilterPipeline;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NutritionistLeadController extends Controller
{
    public function index(): View
    {
        $isSuperAdmin = auth('admin')->user()->hasRole('Super Admin', 'admin');
        $leads = NutritionLead::with(['customer', 'child', 'subscription.subscriptionPlan'])
            ->when(! $isSuperAdmin, fn ($query) => $query->where('assigned_to', auth('admin')->id()))
            ->latest('assigned_at')->paginate(15);

        return view('admin.nutrition.leads.queue', compact('leads', 'isSuperAdmin'));
    }

    public function show(NutritionLead $lead): View
    {
        abort_unless($this->canReview($lead), 403);
        $lead->load(['customer', 'child.assessments.answers.question', 'child.assessments.dietPlans.meals.items.food', 'subscription.subscriptionPlan']);
        $dietPlan = $lead->child?->assessments->sortByDesc('created_at')->first()?->dietPlans->sortByDesc('created_at')->first();
        $foodsByMeal = collect();
        if ($dietPlan) {
            $assessment = $dietPlan->assessment;
            $eligible = app(FoodFilterPipeline::class)->getEligibleFoods($assessment);
            $foodsByMeal = $dietPlan->meals->mapWithKeys(fn ($meal) => [$meal->id => app(FoodFilterPipeline::class)->forSlot($eligible, $meal->meal_slot)]);
            $slotsPerDay = match ($assessment->meal_pattern) {
                '3 meals' => 3,
                '2 + snacks' => 4,
                default => 5,
            };
            $dayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            $mealsByDay = $dietPlan->meals->sortBy('sort_order')->groupBy(
                fn ($meal) => $dayNames[intdiv($meal->sort_order, $slotsPerDay)] ?? 'Day ' . (intdiv($meal->sort_order, $slotsPerDay) + 1)
            );
        }
        $products = Product::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        return view('admin.nutrition.leads.show', compact('lead', 'dietPlan', 'foodsByMeal', 'mealsByDay', 'products'));
    }

    public function update(Request $request, NutritionLead $lead, DietPlan $dietPlan): RedirectResponse
    {
        abort_unless($this->canReview($lead), 403);
        abort_unless($dietPlan->assessment?->child_id === $lead->child_id, 404);
        $data = $request->validate(['foods' => ['required', 'array'], 'foods.*' => ['integer', 'exists:foods,id'], 'supplement_product_ids' => ['nullable', 'array'], 'supplement_product_ids.*' => ['integer', 'exists:products,id']]);
        $eligible = app(FoodFilterPipeline::class)->getEligibleFoods($dietPlan->assessment);

        foreach ($dietPlan->meals as $meal) {
            $foodId = $data['foods'][$meal->id] ?? null;
            if (! $foodId || ! app(FoodFilterPipeline::class)->forSlot($eligible, $meal->meal_slot)->contains('id', $foodId)) abort(422, 'Selected food is not safe or suitable for this meal slot.');
            $meal->items()->update(['food_id' => $foodId]);
        }

        $dietPlan->update(['review_status' => 'pending_review', 'plan_data' => array_merge($dietPlan->plan_data ?? [], ['supplement_product_ids' => $data['supplement_product_ids'] ?? []])]);
        return back()->with('success', 'Diet chart updated and ready for approval.');
    }

    public function generate(NutritionLead $lead): RedirectResponse
    {
        abort_unless($this->canReview($lead), 403);
        $assessment = $lead->child?->assessments()->latest()->first();
        abort_unless($assessment, 422, 'The questionnaire has not been completed.');
        abort_if($assessment->dietPlans()->exists(), 422, 'A chart already exists for this assessment.');

        $answers = $assessment->answers()->pluck('answer_value', 'question_id')->map(fn ($value) => json_decode($value, true))->all();
        try {
            $result = app(\App\Services\Nutrition\DietChartGeneratorService::class)->generateDietChart($assessment, $answers);
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
        if (! ($result['success'] ?? false)) return back()->with('error', $result['message'] ?? 'Diet chart generation failed.');

        $lead->update(['status' => 'awaiting_review']);
        return back()->with('success', 'Rule-based diet chart generated and ready for review.');
    }

    public function updateProducts(Request $request, NutritionLead $lead, DietPlan $dietPlan): RedirectResponse
    {
        abort_unless($this->canReview($lead) && $dietPlan->assessment?->child_id === $lead->child_id, 404);
        $data = $request->validate(['supplement_product_ids' => ['nullable', 'array'], 'supplement_product_ids.*' => ['integer', 'exists:products,id']]);
        $dietPlan->update(['plan_data' => array_merge($dietPlan->plan_data ?? [], ['supplement_product_ids' => $data['supplement_product_ids'] ?? []])]);

        return back()->with('success', 'Recommended products saved.');
    }

    public function approve(Request $request, NutritionLead $lead, DietPlan $dietPlan): RedirectResponse
    {
        abort_unless($this->canReview($lead), 403);
        abort_unless($dietPlan->assessment?->child_id === $lead->child_id, 404);
        $data = $request->validate(['review_notes' => ['nullable', 'string', 'max:2000']]);

        $dietPlan->update(['status' => 'active', 'review_status' => 'approved', 'reviewed_by' => auth('admin')->id(), 'reviewed_at' => now(), 'review_notes' => $data['review_notes'] ?? null]);
        $lead->update(['status' => 'completed', 'completed_at' => now()]);

        return redirect()->route('admin.nutrition.lead-queue.index')->with('success', 'Diet chart approved and released to the customer.');
    }

    private function canReview(NutritionLead $lead): bool
    {
        return auth('admin')->user()->hasRole('Super Admin', 'admin') || $lead->assigned_to === auth('admin')->id();
    }
}
