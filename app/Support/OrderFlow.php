<?php

namespace App\Support;

class OrderFlow
{
    public const ORDER_STATUSES = [
        'pending',
        'confirmed',
        'processing',
        'packed',
        'shipped',
        'delivered',
        'cancelled',
        'returned',
    ];

    public const FULFILLMENT_STATUSES = [
        'unfulfilled',
        'partially_fulfilled',
        'fulfilled',
    ];

    public const PAYMENT_STATUSES = [
        'pending',
        'paid',
        'failed',
        'refunded',
        'partially_refunded',
    ];

    public const RETURN_STATUSES = [
        'pending',
        'approved',
        'rejected',
        'completed',
    ];

    public const RETURN_REASONS = [
        'Defective Product',
        'Wrong Product Sent',
        'Product Damaged on Arrival',
        'Expired Product',
        'Quality not as expected',
        'Changed my mind',
        'Others',
    ];

    public const STATUS_TRANSITIONS = [
        'pending' => ['confirmed', 'cancelled'],
        'confirmed' => ['processing', 'cancelled'],
        'processing' => ['packed', 'cancelled'],
        'packed' => ['shipped', 'cancelled'],
        'shipped' => ['delivered'],
        'delivered' => ['returned'],
        'cancelled' => [],
        'returned' => [],
    ];

    public static function canMoveTo(string $from, string $to): bool
    {
        // Admins should be able to move to any status if needed.
        // We still keep the STATUS_TRANSITIONS for potential frontend or automated logic.
        return true;
    }
}
