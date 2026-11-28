<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductSubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'duration_months',
        'month_1_price',
        'recurring_price',
        'badge_text',
        'free_gift_variant_id',
        'free_gift_label',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'duration_months' => 'integer',
        'month_1_price' => 'decimal:2',
        'recurring_price' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function freeGiftVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'free_gift_variant_id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(ProductSubscription::class, 'plan_id');
    }

    public function calculateTotal(float $comparePrice = 0): array
    {
        $months = (int) $this->duration_months;
        $m1 = (float) $this->month_1_price;
        $rec = (float) $this->recurring_price;
        $total = $m1 + ($rec * max(0, $months - 1));
        $mrpTotal = ($comparePrice > 0 ? $comparePrice : $m1) * $months;
        $saving = max(0, $mrpTotal - $total);

        return [
            'months' => $months,
            'total' => round($total, 2),
            'mrp_total' => round($mrpTotal, 2),
            'saving' => round($saving, 2),
        ];
    }
}
