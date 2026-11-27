<?php

namespace App\Mail;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LowStockAlertMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Product $product,
        public int $currentStock,
        public int $threshold = 5,
        public ?string $variantName = null,
        public ?string $sku = null
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⚠️ Low Stock Alert: ' . $this->product->name . ($this->variantName ? " ({$this->variantName})" : '') . ' is running low',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.ecommerce.low-stock',
            with: [
                'product' => $this->product,
                'currentStock' => $this->currentStock,
                'threshold' => $this->threshold,
                'variantName' => $this->variantName,
                'sku' => $this->sku ?: $this->product->sku,
            ],
        );
    }
}
