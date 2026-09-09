<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssignNutritionLeadRequest;
use App\Http\Requests\Admin\BulkAssignNutritionLeadsRequest;
use App\Models\NutritionLead;
use App\Models\User;
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

    public function assign(AssignNutritionLeadRequest $request, NutritionLead $lead): RedirectResponse
    {
        $this->ensureNutritionist((int) $request->assigned_to);
        $lead->update(['assigned_to' => $request->assigned_to, 'assigned_at' => now(), 'status' => 'assigned']);
        return back()->with('success', 'Lead assigned successfully.');
    }

    public function bulkAssign(BulkAssignNutritionLeadsRequest $request): RedirectResponse
    {
        $this->ensureNutritionist((int) $request->assigned_to);
        NutritionLead::whereIn('id', $request->lead_ids)->update(['assigned_to' => $request->assigned_to, 'assigned_at' => now(), 'status' => 'assigned']);
        return back()->with('success', count($request->lead_ids) . ' lead(s) assigned successfully.');
    }

    private function nutritionists()
    {
        return User::permission('manage diet chart', 'admin')->where('is_active', true)->orderBy('name')->get();
    }

    private function ensureNutritionist(int $userId): void
    {
        abort_unless($this->nutritionists()->contains('id', $userId), 422, 'The selected user does not have diet-chart access.');
    }
}
