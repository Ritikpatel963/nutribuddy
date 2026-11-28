<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductComboItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'combo_product_id',
        'child_variant_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function comboProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'combo_product_id');
    }

    public function childVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'child_variant_id');
    }
}
