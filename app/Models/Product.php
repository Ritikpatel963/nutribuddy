<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $appends = ['display_price', 'display_compare_price'];

    protected $fillable = [
        'category_id',
        'tax_rate_id',
        'name',
        'slug',
        'sku',
        'product_type',
        'is_variant_enabled',
        'brand',
        'hsn_code',
        'short_description',
        'description',
        'accordion_tabs',
        'base_price',
        'compare_at_price',
        'cost_price',
        'shipping_price',
        'currency',
        'is_active',
        'is_combo',
        'combo_tagline',
        'combo_badge',
        'is_featured',
        'published_at',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'variant_types',
        'tags',
        'flavor',
        'pack_size',
        'age_group',
        'coins_reward',
        'dosage',
        'routine',
        'card_image_path',
        'card_hover_image_path',
        'ps_brand_title',
        'ps_product_title',
        'ps_tagline_items',
        'ps_left_label',
        'ps_left_cards',
        'ps_center_image',
        'ps_right_label',
        'ps_right_cards',
        'ps_shelf_left_image',
        'ps_shelf_right_image',
        'transform_description',
        'transform_main_image',
        'transform_before_image',
        'transform_before_label',
        'transform_before_sublabel',
        'transform_after_image',
        'transform_after_label',
        'transform_after_sublabel',
        'transform_results',
        'benefits_cards',
        'problem_heading',
        'problem_title',
        'problem_subtitle',
        'problem_cards',
        'features_heading',
        'features_title',
        'features_subtitle',
        'features_items',
        'features_comparison_title',
        'features_comparison_rows',
        'formula_eyebrow',
        'formula_heading',
        'formula_subheading',
        'formula_tag',
        'formula_bg_word',
        'formula_image',
        'formula_stat_left_num',
        'formula_stat_left_title',
        'formula_stat_left_sub',
        'formula_stat_right_num',
        'formula_stat_right_title',
        'formula_stat_right_sub',
        'formula_badges',
        'formula_caption',
        'formula_button_primary_text',
        'formula_button_primary_url',
        'formula_button_secondary_text',
        'formula_button_secondary_url',
        'formula_footnote',
        'banner_image',
        'faq_title',
        'faqs',
    ];

    protected function casts(): array
    {
        return [
            'is_variant_enabled' => 'boolean',
            'is_active' => 'boolean',
            'is_combo' => 'boolean',
            'is_featured' => 'boolean',
            'base_price' => 'decimal:2',
            'compare_at_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'shipping_price' => 'decimal:2',
            'published_at' => 'datetime',
            'variant_types' => 'array',
            'tags' => 'array',
            'accordion_tabs' => 'array',
            'ps_tagline_items' => 'array',
            'ps_left_cards' => 'array',
            'ps_right_cards' => 'array',
            'transform_results' => 'array',
            'benefits_cards' => 'array',
            'problem_cards' => 'array',
            'features_items' => 'array',
            'features_comparison_rows' => 'array',
            'formula_badges' => 'array',
            'faqs' => 'array',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class)->withTrashed();
    }

    public function taxRate(): BelongsTo
    {
        return $this->belongsTo(TaxRate::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function comboItems(): HasMany
    {
        return $this->hasMany(ProductComboItem::class, 'combo_product_id');
    }

    public function childVariants(): BelongsToMany
    {
        return $this->belongsToMany(ProductVariant::class, 'product_combo_items', 'combo_product_id', 'child_variant_id')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function subscriptionPlans(): HasMany
    {
        return $this->hasMany(ProductSubscriptionPlan::class)->orderBy('sort_order')->orderBy('duration_months');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class);
    }

    /**
     * Get the price to be displayed on storefront.
     * Includes GST if 'show_in_checkout' is false.
     */
    public function getDisplayPriceAttribute(): float
    {
        $price = (float) $this->base_price;

        if ($this->is_variant_enabled) {
            $activeVariants = $this->relationLoaded('variants')
                ? $this->variants->filter(fn ($v) => $v->is_active)
                : $this->variants()->where('is_active', true)->get();

            if ($activeVariants->isNotEmpty()) {
                $defaultVariant = $activeVariants->firstWhere('is_default', true) ?? $activeVariants->first();
                $price = (float) $defaultVariant->price;
            }
        }

        $taxRate = $this->taxRate;

        if ($taxRate && ! (bool) $taxRate->show_in_checkout) {
            $rate = (float) $taxRate->rate;
            $price += ($price * $rate) / 100;
        }

        return (float) round($price, 0);
    }

    /**
     * Get the compare price to be displayed on storefront.
     * Includes GST if 'show_in_checkout' is false.
     */
    public function getDisplayComparePriceAttribute(): float
    {
        $price = (float) ($this->compare_at_price ?? 0);

        if ($this->is_variant_enabled) {
            $activeVariants = $this->relationLoaded('variants')
                ? $this->variants->filter(fn ($v) => $v->is_active)
                : $this->variants()->where('is_active', true)->get();

            if ($activeVariants->isNotEmpty()) {
                $defaultVariant = $activeVariants->firstWhere('is_default', true) ?? $activeVariants->first();
                $price = (float) ($defaultVariant->compare_at_price ?? 0);
            }
        }

        if ($price <= 0) {
            return 0.0;
        }

        $taxRate = $this->taxRate;

        if ($taxRate && ! (bool) $taxRate->show_in_checkout) {
            $rate = (float) $taxRate->rate;
            $price += ($price * $rate) / 100;
        }

        return (float) round($price, 0);
    }
}
