<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactLead;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ContactLeadController extends Controller
{
    public function index(): View
    {
        return view('admin.ecommerce.contact-leads.index', [
            'leads' => ContactLead::with('assignee')->latest()->get(),
            'users' => User::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, ContactLead $contactLead): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['new', 'in_progress', 'closed'])],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'admin_note' => ['nullable', 'string'],
        ]);

        $contactLead->update($validated);

        return back()->with('success', 'Lead updated successfully.');
    }

    public function destroy(ContactLead $contactLead): RedirectResponse
    {
        $contactLead->delete();

        return back()->with('success', 'Lead deleted successfully.');
    }
}
