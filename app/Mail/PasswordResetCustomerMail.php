<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetCustomerMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $resetUrl,
        public int $expiresMinutes = 60
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reset Your NutriBuddy Password',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.auth.password-reset',
            with: [
                'user' => $this->user,
                'resetUrl' => $this->resetUrl,
                'expiresMinutes' => $this->expiresMinutes,
            ],
        );
    }
}
