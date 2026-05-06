<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SupportTicket;
use Illuminate\Support\Str;

class UserSupportTicketController extends Controller
{
    public function index()
    {
        $tickets = SupportTicket::where('user_id', auth()->id())->latest()->get();
        return view('pages.user-panel.support-tickets', compact('tickets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'priority' => 'required|in:low,medium,high',
            'message' => 'required|string',
        ]);

        SupportTicket::create([
            'ticket_number' => 'TKT-' . strtoupper(Str::random(8)),
            'user_id' => auth()->id(),
            'subject' => $request->subject,
            'priority' => $request->priority,
            'message' => $request->message,
            'status' => 'open',
        ]);

        return redirect()->back()->with('success', 'Support ticket created successfully!');
    }
}
