<?php

namespace App\Mail;

use App\Models\DietSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DietSubscriptionPaymentFailedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public DietSubscription $subscription,
        public ?string $errorMessage = null,
        public ?string $retryDate = null
    ) {}

    public function envelope(): Envelope
    {
        $planName = $this->subscription->subscriptionPlan?->name ?? 'Diet Plan';

        return new Envelope(
            subject: "Action Required: Payment Failed for Your NutriBuddy {$planName} ⚠️",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.diet.payment-failed',
            with: [
                'subscription' => $this->subscription,
                'user' => $this->subscription->user,
                'plan' => $this->subscription->subscriptionPlan,
                'child' => $this->subscription->child,
                'errorMessage' => $this->errorMessage,
                'retryDate' => $this->retryDate,
            ],
        );
    }
}
