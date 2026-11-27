<?php

namespace App\Mail;

use App\Models\DietSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DietSubscriptionRenewalSuccessMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public DietSubscription $subscription,
        public float $amountPaid,
        public ?string $paymentMethod = 'UPI AutoPay / Card Mandate'
    ) {}

    public function envelope(): Envelope
    {
        $planName = $this->subscription->subscriptionPlan?->name ?? 'Diet Plan';

        return new Envelope(
            subject: "Subscription Renewed: Your NutriBuddy {$planName} is Active 🔄",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.diet.renewal-success',
            with: [
                'subscription' => $this->subscription,
                'user' => $this->subscription->user,
                'plan' => $this->subscription->subscriptionPlan,
                'child' => $this->subscription->child,
                'amountPaid' => $this->amountPaid,
                'paymentMethod' => $this->paymentMethod,
            ],
        );
    }
}
