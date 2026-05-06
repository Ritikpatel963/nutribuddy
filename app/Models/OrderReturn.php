<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'return_number',
        'reason',
        'media_paths',
        'status',
        'refund_amount',
        'admin_note',
    ];

    protected function casts(): array
    {
        return [
            'refund_amount' => 'decimal:2',
            'media_paths' => 'array',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
