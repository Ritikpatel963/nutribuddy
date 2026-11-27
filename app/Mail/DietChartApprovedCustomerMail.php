<?php

namespace App\Mail;

use App\Models\Nutrition\DietPlan;
use App\Models\NutritionLead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DietChartApprovedCustomerMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public NutritionLead $lead,
        public DietPlan $dietPlan
    ) {}

    public function envelope(): Envelope
    {
        $childName = $this->lead->child?->name ?? 'Your child';

        return new Envelope(
            subject: "Great News! {$childName}’s Personalized Diet Chart is Ready! 📑🥗",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.diet.chart-approved',
            with: [
                'lead' => $this->lead,
                'dietPlan' => $this->dietPlan,
                'customer' => $this->lead->customer,
                'child' => $this->lead->child,
            ],
        );
    }
}
