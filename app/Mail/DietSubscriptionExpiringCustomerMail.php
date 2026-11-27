<?php

namespace App\Mail;

use App\Models\DietSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DietSubscriptionExpiringCustomerMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public DietSubscription $subscription,
        public int $daysRemaining = 3
    ) {}

    public function envelope(): Envelope
    {
        $planName = $this->subscription->subscriptionPlan?->name ?? 'Diet Plan';

        return new Envelope(
            subject: "Reminder: Your NutriBuddy {$planName} Expires in {$this->daysRemaining} Days ⏳",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.diet.expiring-soon',
            with: [
                'subscription' => $this->subscription,
                'user' => $this->subscription->user,
                'plan' => $this->subscription->subscriptionPlan,
                'child' => $this->subscription->child,
                'daysRemaining' => $this->daysRemaining,
            ],
        );
    }
}
