<?php

namespace App\Http\Controllers\Api\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\ProductSubscription;
use App\Services\Payments\CashfreeSubscriptionService;
use App\Services\ProductSubscriptionFulfillmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductSubscriptionWebhookController extends Controller
{
    public function __construct(
        protected CashfreeSubscriptionService $cashfreeService,
        protected ProductSubscriptionFulfillmentService $fulfillmentService
    ) {}

    /**
     * Handle Cashfree Product Subscription Webhook.
     * POST /webhooks/cashfree/product-subscription
     */
    public function handle(Request $request): JsonResponse
    {
        $rawPayload = $request->getContent();
        $signature = $request->header('x-webhook-signature') ?: $request->header('x-cashfree-signature');
        $timestamp = $request->header('x-webhook-timestamp') ?: $request->header('x-cashfree-timestamp');

        // Verify webhook signature (skip only if explicitly in local testing without keys)
        $isProduction = config('app.env') === 'production';
        if ($isProduction && ! $this->cashfreeService->verifyWebhookSignature($rawPayload, $signature, $timestamp)) {
            Log::warning('Cashfree product subscription webhook signature verification failed.');
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $payload = json_decode($rawPayload, true) ?: $request->all();
        $eventType = strtoupper($payload['type'] ?? $payload['event'] ?? '');

        Log::info('Cashfree product subscription webhook received', [
            'type' => $eventType,
            'payload' => $payload,
        ]);

        $subId = $payload['data']['subscription_details']['subscription_id']
            ?? $payload['data']['subscription']['subscription_id']
            ?? $payload['data']['subscription_id']
            ?? null;

        if (! $subId) {
            return response()->json(['status' => 'ignored_no_subscription_id']);
        }

        $subscription = ProductSubscription::with(['plan', 'variant.product'])
            ->where('cashfree_subscription_id', $subId)
            ->orWhere('cf_sub_reference', $subId)
            ->first();

        if (! $subscription) {
            Log::warning("Product subscription not found for Cashfree ID: {$subId}");
            return response()->json(['status' => 'subscription_not_found'], 404);
        }

        // Handle Payment Success
        if (in_array($eventType, ['SUBSCRIPTION_PAYMENT_SUCCESS', 'PAYMENT_SUCCESS_WEBHOOK', 'PAYMENT_SUCCESS', 'SUBSCRIPTION_CHARGE_SUCCESS'], true)) {
            $paymentData = $payload['data']['payment'] ?? $payload['data'] ?? [];
            $cfPaymentId = $paymentData['cf_payment_id'] ?? $paymentData['payment_id'] ?? ('PAY_' . time());
            $amount = (float) ($paymentData['payment_amount'] ?? $paymentData['amount'] ?? ($subscription->cycles_completed === 0 ? $subscription->month_1_price : $subscription->recurring_price));
            $cycle = (int) ($payload['data']['subscription_details']['current_cycle'] ?? ($subscription->cycles_completed + 1));

            $order = $this->fulfillmentService->fulfillCycle($subscription, $cycle, $amount, $cfPaymentId);

            return response()->json([
                'status' => 'success',
                'order_id' => $order?->id,
                'cycle' => $cycle,
            ]);
        }

        // Handle Cancellation or Expiry
        if (in_array($eventType, ['SUBSCRIPTION_CANCELLED', 'SUBSCRIPTION_TERMINATED'], true)) {
            $subscription->update([
                'status' => 'CANCELLED',
                'cancelled_at' => now(),
            ]);
            return response()->json(['status' => 'cancelled']);
        }

        return response()->json(['status' => 'event_acknowledged']);
    }
}
