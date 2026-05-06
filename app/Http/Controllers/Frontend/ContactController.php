<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ContactLead;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        return view('pages.contact');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'nullable|string|max:100',
            'email'      => 'required|email|max:255',
            'phone'      => 'nullable|string|max:20',
            'subject'    => 'required|string|max:255',
            'message'    => 'required|string',
        ]);

        ContactLead::create([
            'name'    => trim($validated['first_name'] . ' ' . ($validated['last_name'] ?? '')),
            'email'   => $validated['email'],
            'phone'   => $validated['phone'] ?? null,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'status'  => 'new',
        ]);

        return back()->with('contact_success', 'Your message has been sent! We\'ll get back to you within 24 hours. 🎉');
    }
}
