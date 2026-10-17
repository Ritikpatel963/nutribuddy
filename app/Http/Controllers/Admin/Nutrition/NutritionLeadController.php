<?php

namespace App\Http\Controllers\Admin\Nutrition;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssignNutritionLeadRequest;
use App\Http\Requests\Admin\BulkAssignNutritionLeadsRequest;
use App\Models\NutritionLead;
use App\Models\User;
use App\Services\Nutrition\NutritionLeadAssignmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NutritionLeadController extends Controller
{
    public function index(Request $request): View
    {
        $leads = NutritionLead::with(['customer', 'child', 'subscription.subscriptionPlan', 'assignee'])
            ->when($request->filled('search'), fn ($query) => $query->whereHas('customer', fn ($q) => $q->where('name', 'like', '%' . $request->search . '%')->orWhere('email', 'like', '%' . $request->search . '%')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->when($request->filled('assigned_to'), fn ($query) => $query->where('assigned_to', $request->assigned_to))
            ->latest()->paginate(15)->withQueryString();

        return view('admin.nutrition.leads.index', ['leads' => $leads, 'nutritionists' => $this->nutritionists()]);
    }

    public function assign(AssignNutritionLeadRequest $request, NutritionLead $lead, NutritionLeadAssignmentService $assignments): RedirectResponse
    {
        $assignments->assign($lead, (int) $request->validated('assigned_to'));
        return back()->with('success', 'Lead assigned successfully.');
    }

    public function bulkAssign(BulkAssignNutritionLeadsRequest $request, NutritionLeadAssignmentService $assignments): RedirectResponse
    {
        $validated = $request->validated();
        $assignments->bulkAssign($validated['lead_ids'], (int) $validated['assigned_to']);
        return back()->with('success', count($validated['lead_ids']) . ' lead(s) assigned successfully.');
    }

    private function nutritionists()
    {
        return User::where('role', 'admin')->where('is_active', true)->orderBy('name')->get();
    }

}
