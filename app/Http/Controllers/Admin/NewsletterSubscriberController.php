<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
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

    public function update(Request $request, NewsletterSubscriber $newsletter): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'source' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['subscribed', 'unsubscribed'])],
        ]);

        if ($validated['status'] === 'subscribed') {
            $validated['subscribed_at'] = $newsletter->subscribed_at ?? now();
            $validated['unsubscribed_at'] = null;
        } else {
            $validated['unsubscribed_at'] = now();
        }

        $newsletter->update($validated);

        return back()->with('success', 'Subscriber updated successfully.');
    }

    public function destroy(NewsletterSubscriber $newsletter): RedirectResponse
    {
        $newsletter->delete();

        return back()->with('success', 'Subscriber deleted successfully.');
    }

    public function export(Request $request): StreamedResponse
    {
        $status = $request->string('status')->toString();

        $fileName = 'newsletter-subscribers-' . now()->format('Y-m-d-His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        return response()->streamDownload(function () use ($status) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Email', 'Name', 'Status', 'Source', 'Subscribed At', 'Unsubscribed At', 'Created At']);

            NewsletterSubscriber::query()
                ->when(in_array($status, ['subscribed', 'unsubscribed'], true), fn ($query) => $query->where('status', $status))
                ->orderBy('email')
                ->chunk(200, function ($subscribers) use ($handle) {
                    foreach ($subscribers as $subscriber) {
                        fputcsv($handle, [
                            $subscriber->email,
                            $subscriber->name,
                            $subscriber->status,
                            $subscriber->source,
                            optional($subscriber->subscribed_at)->format('Y-m-d H:i:s'),
                            optional($subscriber->unsubscribed_at)->format('Y-m-d H:i:s'),
                            optional($subscriber->created_at)->format('Y-m-d H:i:s'),
                        ]);
                    }
                });

            fclose($handle);
        }, $fileName, $headers);
    }
}
