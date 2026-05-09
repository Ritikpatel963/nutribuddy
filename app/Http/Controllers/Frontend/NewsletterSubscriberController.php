<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NewsletterSubscriberController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'source' => ['nullable', 'string', Rule::in(['footer', 'newsletter_block', 'website'])],
        ]);

        $subscriber = NewsletterSubscriber::updateOrCreate(
            ['email' => strtolower($validated['email'])],
            [
                'status' => 'subscribed',
                'source' => $validated['source'] ?? 'website',
                'subscribed_at' => now(),
                'unsubscribed_at' => null,
            ]
        );

        return response()->json([
            'message' => $subscriber->wasRecentlyCreated
                ? 'Thanks for subscribing.'
                : 'You are subscribed again.',
        ]);
    }
}
