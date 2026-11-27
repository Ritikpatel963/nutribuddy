<?php

namespace App\Mail;

use App\Models\DietSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DietSubscriptionActivatedCustomerMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public DietSubscription $subscription) {}

    public function envelope(): Envelope
    {
        $planName = $this->subscription->subscriptionPlan?->name ?? 'Diet Plan';

        return new Envelope(
            subject: "Your NutriBuddy {$planName} Subscription is Active! 🥗",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.diet.subscription-activated',
            with: [
                'subscription' => $this->subscription,
                'user' => $this->subscription->user,
                'plan' => $this->subscription->subscriptionPlan,
                'child' => $this->subscription->child,
            ],
        );
    }
}
