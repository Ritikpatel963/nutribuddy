# Implementation Ruleset: Combo Packs & Product Subscriptions (Zero-Regression)

This ruleset defines strict engineering boundaries for integrating **Combo Packs** and **Product Subscribe & Save** on NutriBuddy without altering existing one-time cart, `/checkout`, or legacy order workflows.

---

## 1. Non-Negotiable Isolation Rules

1. **Do Not Mutate Existing One-Time Flows:**
   * Keep `value="once"` (One-Time Purchase), `handleAddToCart()`, `handleBuyNow()`, `/cart/add`, and `/checkout` behavior intact for existing flows.
   * Never route subscription checkouts through the standard `/checkout` one-time cart session.
2. **Additive Changes Only:**
   * All database changes must be new migrations (adding nullable/defaulted columns or new tables). Never drop or rename existing columns used by `products`, `product_variants`, `carts`, or `orders`.
3. **Single Source of Truth for Pricing & Perks:**
   * Always compute subscription schedules, savings against MRP, and free bonus gifts (such as `Free Tasty Kadha`) from database-backed plan records passed via `#nbPdpConfig`—never hardcode alternate prices inside Blade templates.
4. **Order Fulfillment Parity:**
   * Recurring subscription charges must create standard rows in the existing `orders` table upon confirmed payment webhook so warehouse and admin order views require zero special-case rewrites.

---

## 2. Combo Packs Rules (Virtual Bundle Pattern)

* **Catalog Representation:** Store each combo as a record in `products` with `is_combo = true`, a primary sellable `product_variant` row, optional `combo_tagline`, and optional `combo_badge`.
* **Child Mapping:** Link constituent SKUs and per-combo quantities in `product_combo_items` (`combo_product_id`, `child_variant_id`, `quantity`).
* **Listing & Tabs:** Filter `is_combo = false` for **Individual products** and `is_combo = true` for **Combo packs**, displaying child item summary, item count badge, selling price, MRP/savings, and lowest monthly subscription teaser.
* **PDP & Cart Compatibility:** Reuse `/product/{slug}`, `ProductController::show()`, and existing Add to Cart / Buy Now actions using the combo's primary variant.
* **Stock Deduction:** On order placement, if a line item's product has `is_combo = true`, decrement `stock_qty` on each mapped `child_variant_id` by `item.quantity * combo_item.quantity`.

---

## 3. Product Subscribe & Save Rules (Isolated Pipeline)

* **Plan Storage:** Store duration tiers (`2`, `3`, `4` months), `month_1_price`, `recurring_price`, `badge_text`, and optional `free_gift_variant_id` / `free_gift_label` in `product_subscription_plans`.
* **Subscription Contract Storage:** Track active mandates in `product_subscriptions` (`user_id`, `plan_id`, `address_id`, `cashfree_subscription_id`, `status`, `cycles_completed`, `next_billing_at`).
* **Frontend CTA Wiring (`allfile.js`):**
  * When `value="once"` is active, execute standard Add to Cart / Buy Now logic unchanged.
  * When `value="subscribe"` is active, validate a selected plan tier and redirect `#pdpSubscriptionCta` to `/subscribe/checkout?plan_id={id}&variant_id={variant_id}`.
* **Dedicated Checkout & Mandate Creation:**
  * Handle `/subscribe/checkout` and `POST /subscribe/initiate` in a dedicated `ProductSubscriptionController`.
  * Reuse `CashfreeSubscriptionService` to initialize the customer mandate with Month 1 pricing, recurring monthly billing, and max cycle count.
* **Webhook & Recurring Billing:**
  * Process payment success events on a dedicated webhook endpoint (`POST /webhooks/cashfree/product-subscription`) with signature verification and idempotency checks (ignore duplicate event IDs).
  * Increment `cycles_completed`, advance `next_billing_at`, insert the fulfilled order into `orders` (attaching `free_gift_variant_id` at ₹0 when configured), and mark the subscription completed once `cycles_completed >= duration_months`.

---

## 4. Verification & Acceptance Checklist

* [ ] Individual product One-Time Add to Cart, Buy Now, and `/checkout` work identically to before.
* [ ] Switching between **Individual products** and **Combo packs** renders accurate pricing, savings, and subscription-from labels.
* [ ] Purchasing a combo decrements underlying child variant inventories accurately.
* [ ] Selecting a 2, 3, or 4-month plan on PDP updates summary totals and redirects to `/subscribe/checkout` with valid `plan_id` and `variant_id`.
* [ ] Simulated subscription payment webhook creates an `orders` record and updates `cycles_completed` without duplicate orders on replay.
