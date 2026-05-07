<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SupportTicketController extends Controller
{
    public function index(): View
    {
        return view('admin.ecommerce.support-tickets.index', [
            'tickets' => SupportTicket::with('user')->latest()->get(),
            'users' => User::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['nullable', 'exists:users,id'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'status' => ['required', Rule::in(['open', 'in_progress', 'resolved', 'closed'])],
            'priority' => ['required', Rule::in(['low', 'medium', 'high'])],
            'admin_note' => ['nullable', 'string'],
        ]);

        $validated['ticket_number'] = 'NB-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        $validated['last_replied_at'] = now();

        SupportTicket::create($validated);

        return back()->with('success', 'Support ticket created successfully.');
    }

    public function update(Request $request, SupportTicket $supportTicket): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['open', 'in_progress', 'resolved', 'closed'])],
            'priority' => ['required', Rule::in(['low', 'medium', 'high'])],
            'admin_note' => ['nullable', 'string'],
        ]);

        $validated['last_replied_at'] = now();

        $supportTicket->update($validated);

        return back()->with('success', 'Support ticket updated successfully.');
    }

    public function show(SupportTicket $supportTicket): View
    {
        $supportTicket->load('messages.user');
        
        return view('admin.ecommerce.support-tickets.show', [
            'ticket' => $supportTicket,
        ]);
    }

    public function reply(Request $request, SupportTicket $supportTicket): RedirectResponse
    {
        $request->validate([
            'message' => ['required', 'string'],
        ]);

        $supportTicket->messages()->create([
            'user_id' => auth()->id(),
            'is_admin' => true,
            'message' => $request->message,
        ]);

        $supportTicket->update(['last_replied_at' => now(), 'status' => 'in_progress']);

        return back()->with('success', 'Reply sent successfully.');
    }

    public function destroy(SupportTicket $supportTicket): RedirectResponse
    {
        $supportTicket->delete();

        return redirect()->route('admin.ecommerce.support-tickets.index')->with('success', 'Support ticket deleted successfully.');
    }
}
