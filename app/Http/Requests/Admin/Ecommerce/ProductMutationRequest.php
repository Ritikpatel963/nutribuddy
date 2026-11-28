<?php

namespace App\Http\Requests\Admin\Ecommerce;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

abstract class ProductMutationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin')?->can('manage products') ?? false;
    }

    protected function productRules(?Product $product = null): array
    {
        return [
            'category_id' => ['nullable', 'exists:categories,id'], 'tax_rate_id' => ['nullable', 'exists:tax_rates,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('products', 'slug')->ignore($product?->id)],
            'sku' => ['nullable', 'string', 'max:255', Rule::unique('products', 'sku')->ignore($product?->id)],
            'product_type' => ['required', Rule::in(['simple', 'variable'])], 'is_variant_enabled' => ['nullable', 'boolean'],
            'brand' => ['nullable', 'string', 'max:255'], 'hsn_code' => ['nullable', 'string', 'max:50'],
            'short_description' => ['nullable', 'string'], 'description' => ['nullable', 'string'],
            'accordion_tabs' => ['nullable', 'array'],
            'accordion_tabs.*.title' => ['nullable', 'string', 'max:255'],
            'accordion_tabs.*.content' => ['nullable', 'string'],
            'base_price' => ['required', 'numeric', 'min:0'], 'compare_at_price' => ['nullable', 'numeric', 'min:0'],
            'cost_price' => ['nullable', 'numeric', 'min:0'], 'shipping_price' => ['nullable', 'numeric', 'min:0'],
            'meta_title' => ['nullable', 'string', 'max:255'], 'meta_description' => ['nullable', 'string'],
            'meta_keywords' => ['nullable', 'string'], 'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'], 'variant_types' => ['nullable', 'string'],
            'flavor' => ['nullable', 'string', 'max:255'], 'pack_size' => ['nullable', 'string', 'max:255'],
            'age_group' => ['nullable', 'string', 'max:255'], 'dosage' => ['nullable', 'string', 'max:255'],
            'routine' => ['nullable', 'string', 'max:255'], 'coins_reward' => ['nullable', 'integer', 'min:0'],
            'stock_qty' => ['nullable', 'integer', 'min:0'], 'track_stock' => ['nullable', 'boolean'],
            'is_in_stock' => ['nullable', 'boolean'], 'tags' => ['nullable', 'array'],
            'product_attributes' => ['nullable', 'array'], 'product_attribute_values' => ['nullable', 'array'],
            'variations' => ['nullable', 'array'], 'variations.*.id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'variations.*.name' => ['nullable', 'string', 'max:255'], 'variations.*.sku' => ['nullable', 'string', 'max:255'],
            'variations.*.attributes' => ['nullable', 'array'], 'variations.*.price' => ['required_with:variations', 'numeric', 'min:0'],
            'variations.*.compare_at_price' => ['nullable', 'numeric', 'min:0'], 'variations.*.cost_price' => ['nullable', 'numeric', 'min:0'],
            'variations.*.stock_qty' => ['nullable', 'integer', 'min:0'], 'variations.*.track_stock' => ['nullable', 'boolean'],
            'variations.*.is_in_stock' => ['nullable', 'boolean'], 'variations.*.is_default' => ['nullable', 'boolean'],
            'variations.*.is_active' => ['nullable', 'boolean'], 'images' => ['nullable', 'array'],
            'images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'card_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'card_hover_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'tag_images' => ['nullable', 'array'], 'tag_images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];
    }

    protected function transformSectionRules(): array
    {
        return [
            'transform_description' => ['nullable', 'string', 'max:1000'],
            'transform_main_image' => ['nullable', 'string', 'max:255'],
            'transform_main_image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'transform_before_image' => ['nullable', 'string', 'max:255'],
            'transform_before_image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'transform_before_label' => ['nullable', 'string', 'max:100'],
            'transform_before_sublabel' => ['nullable', 'string', 'max:150'],
            'transform_after_image' => ['nullable', 'string', 'max:255'],
            'transform_after_image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'transform_after_label' => ['nullable', 'string', 'max:100'],
            'transform_after_sublabel' => ['nullable', 'string', 'max:150'],
            'transform_results' => ['nullable', 'array'],
            'transform_results.*.image' => ['nullable', 'string', 'max:255'],
            'transform_results.*.title' => ['nullable', 'string', 'max:150'],
            'transform_results.*.description' => ['nullable', 'string', 'max:1000'],
            'transform_results.*.week' => ['nullable', 'string', 'max:80'],
            'transform_result_images' => ['nullable', 'array'],
            'transform_result_images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'benefits_cards' => ['nullable', 'array'], 'benefits_cards.*.title' => ['nullable', 'string', 'max:150'],
            'benefits_cards.*.description' => ['nullable', 'string', 'max:1000'], 'benefits_card_images' => ['nullable', 'array'],
            'benefits_card_images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'problem_heading' => ['nullable', 'string', 'max:255'], 'problem_title' => ['nullable', 'string', 'max:500'],
            'problem_subtitle' => ['nullable', 'string', 'max:1000'], 'problem_cards' => ['nullable', 'array'],
            'problem_cards.*.title' => ['nullable', 'string', 'max:150'], 'problem_cards.*.description' => ['nullable', 'string', 'max:1000'],
            'problem_card_images' => ['nullable', 'array'], 'problem_card_images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];
    }

    protected function problemSolutionRules(): array
    {
        return [
            'ps_brand_title' => ['nullable', 'string', 'max:255'], 'ps_product_title' => ['nullable', 'string', 'max:255'],
            'ps_tagline_items' => ['nullable', 'array'], 'ps_tagline_items.*' => ['nullable', 'string', 'max:120'],
            'ps_left_label' => ['nullable', 'string', 'max:255'], 'ps_left_cards' => ['nullable', 'array'],
            'ps_left_cards.*.title' => ['nullable', 'string', 'max:120'], 'ps_left_cards.*.text' => ['nullable', 'string', 'max:255'],
            'ps_left_cards.*.icon' => ['nullable', 'string', 'max:255'], 'ps_left_card_images' => ['nullable', 'array'],
            'ps_left_card_images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'ps_center_image' => ['nullable', 'string', 'max:255'], 'ps_center_image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'ps_right_label' => ['nullable', 'string', 'max:255'], 'ps_right_cards' => ['nullable', 'array'],
            'ps_right_cards.*.title' => ['nullable', 'string', 'max:120'], 'ps_right_cards.*.text' => ['nullable', 'string', 'max:255'],
            'ps_right_cards.*.icon' => ['nullable', 'string', 'max:255'], 'ps_right_card_images' => ['nullable', 'array'],
            'ps_right_card_images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'ps_shelf_left_image' => ['nullable', 'string', 'max:255'], 'ps_shelf_left_image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'ps_shelf_right_image' => ['nullable', 'string', 'max:255'], 'ps_shelf_right_image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];
    }

    protected function featuresSectionRules(): array
    {
        return [
            'features_heading' => ['nullable', 'string', 'max:255'],
            'features_title' => ['nullable', 'string', 'max:500'],
            'features_subtitle' => ['nullable', 'string', 'max:1000'],
            'features_items' => ['nullable', 'array'],
            'features_items.*.icon' => ['nullable', 'string', 'max:255'],
            'features_items.*.title' => ['nullable', 'string', 'max:150'],
            'features_items.*.description' => ['nullable', 'string', 'max:1000'],
            'features_item_images' => ['nullable', 'array'],
            'features_item_images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'features_comparison_title' => ['nullable', 'string', 'max:255'],
            'features_comparison_rows' => ['nullable', 'array'],
            'features_comparison_rows.*.feature' => ['nullable', 'string', 'max:150'],
            'features_comparison_rows.*.us' => ['nullable', 'string', 'max:50'],
            'features_comparison_rows.*.others' => ['nullable', 'string', 'max:50'],
        ];
    }

    protected function formulaSectionRules(): array
    {
        return [
            'formula_eyebrow' => ['nullable', 'string', 'max:255'],
            'formula_heading' => ['nullable', 'string', 'max:500'],
            'formula_subheading' => ['nullable', 'string', 'max:255'],
            'formula_tag' => ['nullable', 'string', 'max:255'],
            'formula_bg_word' => ['nullable', 'string', 'max:50'],
            'formula_image' => ['nullable', 'string', 'max:255'],
            'formula_image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'formula_stat_left_num' => ['nullable', 'string', 'max:50'],
            'formula_stat_left_title' => ['nullable', 'string', 'max:150'],
            'formula_stat_left_sub' => ['nullable', 'string', 'max:150'],
            'formula_stat_right_num' => ['nullable', 'string', 'max:50'],
            'formula_stat_right_title' => ['nullable', 'string', 'max:150'],
            'formula_stat_right_sub' => ['nullable', 'string', 'max:150'],
            'formula_badges' => ['nullable', 'array'],
            'formula_badges.*' => ['nullable', 'string', 'max:100'],
            'formula_caption' => ['nullable', 'string', 'max:500'],
            'formula_button_primary_text' => ['nullable', 'string', 'max:100'],
            'formula_button_primary_url' => ['nullable', 'string', 'max:255'],
            'formula_button_secondary_text' => ['nullable', 'string', 'max:100'],
            'formula_button_secondary_url' => ['nullable', 'string', 'max:255'],
            'formula_footnote' => ['nullable', 'string', 'max:255'],
            'banner_image' => ['nullable', 'string', 'max:255'],
            'banner_image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ];
    }

    protected function faqSectionRules(): array
    {
        return [
            'faq_title' => ['nullable', 'string', 'max:255'],
            'faqs' => ['nullable', 'array'],
            'faqs.*.question' => ['nullable', 'string', 'max:500'],
            'faqs.*.answer' => ['nullable', 'string'],
        ];
    }

    protected function comboRules(): array
    {
        return [
            'is_combo' => ['nullable', 'boolean'],
            'combo_tagline' => ['nullable', 'string', 'max:255'],
            'combo_badge' => ['nullable', 'string', 'max:100'],
            'combo_items' => ['nullable', 'array'],
            'combo_items.*.child_variant_id' => ['required_with:combo_items', 'integer', 'exists:product_variants,id'],
            'combo_items.*.quantity' => ['required_with:combo_items', 'integer', 'min:1'],
        ];
    }

    protected function subscriptionRules(): array
    {
        return [
            'has_custom_subscriptions' => ['nullable', 'boolean'],
            'subscription_plans' => ['nullable', 'array'],
            'subscription_plans.*.id' => ['nullable', 'integer', 'exists:product_subscription_plans,id'],
            'subscription_plans.*.duration_months' => ['required_with:subscription_plans', 'integer', 'min:1', 'max:36'],
            'subscription_plans.*.month_1_price' => ['required_with:subscription_plans', 'numeric', 'min:0'],
            'subscription_plans.*.recurring_price' => ['required_with:subscription_plans', 'numeric', 'min:0'],
            'subscription_plans.*.badge_text' => ['nullable', 'string', 'max:100'],
            'subscription_plans.*.free_gift_variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'subscription_plans.*.free_gift_label' => ['nullable', 'string', 'max:255'],
            'subscription_plans.*.is_active' => ['nullable', 'boolean'],
            'subscription_plans.*.sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
