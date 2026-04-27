<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class NewsletterSubscriberController extends Controller
{
    public function index(): View
    {
        return view('admin.ecommerce.newsletter.index', [
            'subscribers' => NewsletterSubscriber::latest()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255', 'unique:newsletter_subscribers,email'],
            'name' => ['nullable', 'string', 'max:255'],
            'source' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['subscribed', 'unsubscribed'])],
        ]);

        $validated['subscribed_at'] = now();
        $validated['unsubscribed_at'] = $validated['status'] === 'unsubscribed' ? now() : null;

        NewsletterSubscriber::create($validated);

        return back()->with('success', 'Subscriber added successfully.');
    }

    public function update(Request $request, NewsletterSubscriber $newsletterSubscriber): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'source' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['subscribed', 'unsubscribed'])],
        ]);

        if ($validated['status'] === 'subscribed') {
            $validated['subscribed_at'] = $newsletterSubscriber->subscribed_at ?? now();
            $validated['unsubscribed_at'] = null;
        } else {
            $validated['unsubscribed_at'] = now();
        }

        $newsletterSubscriber->update($validated);

        return back()->with('success', 'Subscriber updated successfully.');
    }

    public function destroy(NewsletterSubscriber $newsletterSubscriber): RedirectResponse
    {
        $newsletterSubscriber->delete();

        return back()->with('success', 'Subscriber deleted successfully.');
    }
}
