<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'product_id',
        'variant_id',
        'address_id',
        'shipping_address',
        'cashfree_subscription_id',
        'cf_sub_reference',
        'status',
        'cycles_completed',
        'max_cycles',
        'month_1_price',
        'recurring_price',
        'next_billing_at',
        'last_billed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'shipping_address' => 'array',
        'cycles_completed' => 'integer',
        'max_cycles' => 'integer',
        'month_1_price' => 'decimal:2',
        'recurring_price' => 'decimal:2',
        'next_billing_at' => 'datetime',
        'last_billed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(ProductSubscriptionPlan::class, 'plan_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(CustomerAddress::class, 'address_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'product_subscription_id');
    }

    public function isActive(): bool
    {
        return in_array(strtoupper($this->status), ['ACTIVE', 'INITIALIZED'], true)
            && $this->cycles_completed < $this->max_cycles;
    }
}
