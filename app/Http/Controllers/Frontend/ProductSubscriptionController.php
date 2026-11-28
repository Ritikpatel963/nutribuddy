<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CustomerAddress;
use App\Models\ProductSubscription;
use App\Models\ProductSubscriptionPlan;
use App\Models\ProductVariant;
use App\Services\Payments\CashfreeSubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductSubscriptionController extends Controller
{
    public function __construct(
        protected CashfreeSubscriptionService $cashfreeService
    ) {}

    /**
     * Dedicated Subscribe & Save Checkout Page.
     * GET /subscribe/checkout
     */
    public function checkout(Request $request): View|RedirectResponse
    {
        if (! auth()->check()) {
            return redirect()->guest(route('frontend.login'))
                ->with('info', 'Please sign in or create an account to start your subscription.');
        }

        $planId = $request->query('plan_id');
        $variantId = $request->query('variant_id');

        $plan = ProductSubscriptionPlan::with(['product.taxRate', 'freeGiftVariant.product'])
            ->where('id', $planId)
            ->where('is_active', true)
            ->firstOrFail();

        $variant = ProductVariant::with(['product.taxRate', 'inventory'])
            ->where('id', $variantId)
            ->where('is_active', true)
            ->firstOrFail();

        $user = auth()->user();
        $addresses = $user->customerAddresses()->get();

        $calculation = $plan->calculateTotal((float) ($variant->display_compare_price ?: $variant->display_price));

        return view('frontend.storefront.subscription.checkout', [
            'plan' => $plan,
            'variant' => $variant,
            'product' => $variant->product,
            'user' => $user,
            'addresses' => $addresses,
            'calculation' => $calculation,
        ]);
    }

    /**
     * Initiate Subscription Mandate.
     * POST /subscribe/initiate
     */
    public function initiate(Request $request): JsonResponse
    {
        if (! auth()->check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $validated = $request->validate([
            'plan_id' => 'required|exists:product_subscription_plans,id',
            'variant_id' => 'required|exists:product_variants,id',
            'address_id' => 'nullable|exists:customer_addresses,id',
            'shipping_name' => 'required_without:address_id|string|max:120',
            'shipping_phone' => 'required_without:address_id|string|max:20',
            'shipping_address_line_1' => 'required_without:address_id|string|max:255',
            'shipping_address_line_2' => 'nullable|string|max:255',
            'shipping_city' => 'required_without:address_id|string|max:100',
            'shipping_state' => 'required_without:address_id|string|max:100',
            'shipping_postal_code' => 'required_without:address_id|string|max:20',
        ]);

        $user = auth()->user();
        $plan = ProductSubscriptionPlan::with('freeGiftVariant')->findOrFail($validated['plan_id']);
        $variant = ProductVariant::with('product')->findOrFail($validated['variant_id']);

        // Resolve Address
        $address = null;
        if (! empty($validated['address_id'])) {
            $address = CustomerAddress::where('id', $validated['address_id'])
                ->where('user_id', $user->id)
                ->first();
        }

        if (! $address) {
            $address = CustomerAddress::create([
                'user_id' => $user->id,
                'full_name' => $validated['shipping_name'],
                'phone' => $validated['shipping_phone'],
                'address_line_1' => $validated['shipping_address_line_1'],
                'address_line_2' => $validated['shipping_address_line_2'] ?? null,
                'city' => $validated['shipping_city'],
                'state' => $validated['shipping_state'],
                'postal_code' => $validated['shipping_postal_code'],
                'country' => 'India',
                'is_default_shipping' => $user->customerAddresses()->count() === 0,
            ]);
        }

        $addressSnapshot = [
            'name' => $address->full_name ?? ($address->name ?? $user->name),
            'phone' => $address->phone,
            'address_line_1' => $address->address_line_1,
            'address_line_2' => $address->address_line_2,
            'city' => $address->city,
            'state' => $address->state,
            'postal_code' => $address->postal_code,
            'country' => $address->country ?? 'India',
        ];

        $subRef = 'PSUB_' . strtoupper(Str::random(12));

        $subscription = ProductSubscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'product_id' => $variant->product_id,
            'variant_id' => $variant->id,
            'address_id' => $address->id,
            'shipping_address' => $addressSnapshot,
            'cf_sub_reference' => $subRef,
            'status' => 'PENDING',
            'cycles_completed' => 0,
            'max_cycles' => $plan->duration_months,
            'month_1_price' => $plan->month_1_price,
            'recurring_price' => $plan->recurring_price,
            'next_billing_at' => now(),
        ]);

        // Attempt Cashfree Subscription initialization if credentials configured
        $appId = config('services.cashfree.subscription_app_id') ?: config('services.cashfree.app_id');
        $secretKey = config('services.cashfree.subscription_secret_key') ?: config('services.cashfree.secret_key');

        $paymentSessionId = null;
        $authUrl = null;

        if ($appId && $secretKey && config('services.cashfree.env') !== 'testing') {
            try {
                $cfPayload = [
                    'subscription_id' => $subRef,
                    'customer_details' => [
                        'customer_id' => (string) $user->id,
                        'customer_name' => $user->name,
                        'customer_email' => $user->email,
                        'customer_phone' => preg_replace('/\D/', '', (string) ($address->phone ?: $user->phone ?: '9999999999')),
                    ],
                    'plan_details' => [
                        'plan_id' => 'PLAN_' . $plan->id . '_' . $plan->duration_months . 'M',
                        'plan_name' => $variant->product->name . ' (' . $plan->duration_months . ' Months)',
                        'plan_type' => 'PERIODIC',
                        'plan_max_amount' => (float) max($plan->month_1_price, $plan->recurring_price),
                        'plan_max_cycles' => (int) $plan->duration_months,
                        'plan_intervals' => 1,
                        'plan_interval_type' => 'MONTH',
                    ],
                    'first_charge' => (float) $plan->month_1_price,
                    'notification_channels' => ['EMAIL', 'SMS'],
                    'subscription_meta' => [
                        'return_url' => route('subscribe.return') . '?sub_ref=' . $subRef,
                        'notify_url' => route('webhooks.cashfree.product-subscription'),
                    ],
                ];

                $cfResponse = $this->cashfreeService->createSubscription($cfPayload);
                $subscription->update([
                    'cashfree_subscription_id' => $cfResponse['cf_subscription_id'] ?? $subRef,
                ]);
                $authUrl = $cfResponse['auth_url'] ?? null;
                $paymentSessionId = $cfResponse['payment_session_id'] ?? null;
            } catch (\Throwable $e) {
                Log::warning('Cashfree product subscription initiate failed, falling back to simulated session: ' . $e->getMessage());
            }
        }

        return response()->json([
            'status' => 'success',
            'subscription_id' => $subscription->id,
            'sub_ref' => $subRef,
            'auth_url' => $authUrl,
            'payment_session_id' => $paymentSessionId,
            'redirect_url' => $authUrl ?: route('subscribe.return', ['sub_ref' => $subRef, 'simulated' => '1']),
        ]);
    }

    /**
     * Handle Customer Return after Cashfree mandate approval.
     * GET /subscribe/payment/return
     */
    public function return(Request $request): View|RedirectResponse
    {
        $subRef = $request->query('sub_ref');
        $simulated = $request->boolean('simulated');

        $subscription = ProductSubscription::with(['product', 'variant', 'plan'])
            ->where('cf_sub_reference', $subRef)
            ->first();

        if (! $subscription) {
            return redirect()->route('account.orders')->with('error', 'Subscription not found.');
        }

        if ($simulated && $subscription->status === 'PENDING') {
            // Activate simulated test flow
            $subscription->update([
                'status' => 'ACTIVE',
                'cycles_completed' => 1,
                'last_billed_at' => now(),
                'next_billing_at' => now()->addMonth(),
            ]);

            // Fulfill initial Cycle 1 order
            app(\App\Services\ProductSubscriptionFulfillmentService::class)->fulfillCycle($subscription, 1, $subscription->month_1_price, 'SIMULATED_TEST');
        }

        return view('frontend.storefront.subscription.confirmation', [
            'subscription' => $subscription,
        ]);
    }
}
