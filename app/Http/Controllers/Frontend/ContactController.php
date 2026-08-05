<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ContactLead;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContactController extends Controller
{
    public function index()
    {
        return view('pages.contact');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100', 'regex:/^[A-Za-z\s.\'-]+$/'],
            'last_name'  => ['nullable', 'string', 'max:100', 'regex:/^[A-Za-z\s.\'-]+$/'],
            'email'      => 'required|email|max:255',
            'phone'      => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]{7,20}$/'],
            'subject'    => ['required', 'string', 'max:255', Rule::in([
                'Where is my order?',
                'Question about a product',
                'Help with Diet Chart',
                'Wholesale / Partnership',
                'Other',
            ])],
            'message'    => 'required|string|min:10|max:5000',
        ], [
            'first_name.regex' => 'First name can only contain letters, spaces, dots, apostrophes, and hyphens.',
            'last_name.regex' => 'Last name can only contain letters, spaces, dots, apostrophes, and hyphens.',
            'phone.regex' => 'Please enter a valid phone number.',
            'message.min' => 'Please enter at least 10 characters in your message.',
        ]);

        $lead = ContactLead::create([
            'name'    => trim($validated['first_name'] . ' ' . ($validated['last_name'] ?? '')),
            'email'   => $validated['email'],
            'phone'   => $validated['phone'] ?? null,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'status'  => 'new',
        ]);

        $admins = \App\Models\User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\NewContactLeadNotification($lead));
        }

        return back()->with('contact_success', 'Your message has been sent! We\'ll get back to you within 24 hours. 🎉');
    }
}
