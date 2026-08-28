<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\DietSubscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DietPaymentController extends Controller
{
    /**
     * Start the payment process for a diet subscription.
     */
    public function createOrder(Request $request): JsonResponse
    {
        $request->validate([
            'plan_slug' => 'required|string|exists:subscription_plans,slug',
            'gateway'   => 'required|string|in:razorpay,cashfree',
            'cycle'     => 'required|string|in:monthly,quarterly,annually',
        ]);

        $plan = SubscriptionPlan::where('slug', $request->plan_slug)->firstOrFail();
        
        $price = 0;
        $days = 30;
        
        if ($request->cycle === 'monthly') {
            $price = $plan->monthly_price;
            $days = 30;
        } elseif ($request->cycle === 'quarterly') {
            $price = $plan->quarterly_price;
            $days = 90;
        } elseif ($request->cycle === 'annually') {
            $price = $plan->annual_price;
            $days = 365;
        }

        $amountInPaise = round((float) $price * 100);
        $amountInRupees = round((float) $price, 2);
        
        // Create a pending subscription record first so we have an ID for Cashfree return
        $subscription = DietSubscription::create([
            'user_id' => auth()->id(),
            'subscription_plan_id' => $plan->id,
            'model' => $plan->name . ' (' . ucfirst($request->cycle) . ')',
            'status' => 'pending',
            'payment_status' => 'pending',
            'amount_paid' => $amountInRupees,
            'starts_at' => now(),
            'expires_at' => now()->addDays($days),
        ]);

        if ($amountInRupees <= 0) {
            $subscription->update([
                'status' => 'active',
                'payment_status' => 'paid',
            ]);

            return response()->json([
                'success' => true,
                'gateway' => 'free',
                'subscription_id' => $subscription->id,
            ]);
        }

        try {
            if ($request->gateway === 'razorpay') {
                $response = Http::withoutVerifying()
                    ->withBasicAuth(config('services.razorpay.key_id'), config('services.razorpay.key_secret'))
                    ->post('https://api.razorpay.com/v1/orders', [
                        'amount' => $amountInPaise,
                        'currency' => 'INR',
                        'receipt' => 'diet_' . $subscription->id . '_' . uniqid(),
                        'notes' => [
                            'plan_id' => $plan->id,
                            'user_id' => auth()->id() ?? 'guest',
                            'subscription_id' => $subscription->id,
                        ],
                    ]);

                if (!$response->successful()) {
                    throw new \Exception('Razorpay API error: ' . $response->body());
                }

                $razorpayOrder = $response->json();
                $subscription->update(['razorpay_order_id' => $razorpayOrder['id']]);

                return response()->json([
                    'success' => true,
                    'gateway' => 'razorpay',
                    'razorpay_order_id' => $razorpayOrder['id'],
                    'amount' => $amountInPaise,
                    'currency' => 'INR',
                    'subscription_id' => $subscription->id,
                ]);
            } else {
                // Cashfree
                $orderId = 'diet_sub_' . $subscription->id . '_' . uniqid();
                
                $baseUrl = config('services.cashfree.env') === 'production'
                    ? 'https://api.cashfree.com/pg/orders'
                    : 'https://sandbox.cashfree.com/pg/orders';

                $response = Http::withoutVerifying()->acceptJson()
                    ->withHeaders([
                        'x-client-id' => config('services.cashfree.app_id'),
                        'x-client-secret' => config('services.cashfree.secret_key'),
                        'x-api-version' => config('services.cashfree.api_version', '2025-01-01'),
                    ])
                    ->post($baseUrl, [
                        'order_id' => $orderId,
                        'order_amount' => $amountInRupees,
                        'order_currency' => 'INR',
                        'customer_details' => [
                            'customer_id' => 'user_' . (auth()->id() ?? 'guest'),
                            'customer_name' => auth()->user()->name ?? 'Guest User',
                            'customer_email' => auth()->user()->email ?? 'guest@nutribuddy.local',
                            'customer_phone' => '9999999999',
                        ],
                        'order_meta' => [
                            'return_url' => str_replace('http://', 'https://', route('diet.payment.cashfree.return')) . '?order_id={order_id}&subscription_id=' . $subscription->id,
                        ],
                        'order_note' => 'Diet Subscription ' . $plan->name,
                    ]);

                if (!$response->successful()) {
                    throw new \Exception('Cashfree API error: ' . $response->body());
                }

                $cashfreeOrder = $response->json();
                $subscription->update(['razorpay_order_id' => $cashfreeOrder['order_id']]); // Reusing column for CF order ID

                return response()->json([
                    'success' => true,
                    'gateway' => 'cashfree',
                    'payment_session_id' => $cashfreeOrder['payment_session_id'],
                    'order_id' => $cashfreeOrder['order_id'],
                    'subscription_id' => $subscription->id,
                ]);
            }

        } catch (\Throwable $e) {
            Log::error('Diet Plan Payment Order Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Unable to process payment at this time.'], 500);
        }
    }

    public function verifyPayment(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'subscription_id' => 'required|exists:diet_subscriptions,id',
            'razorpay_order_id' => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature' => 'required|string',
        ]);

        $expectedSignature = hash_hmac(
            'sha256',
            $validated['razorpay_order_id'] . '|' . $validated['razorpay_payment_id'],
            config('services.razorpay.key_secret')
        );

        if (!hash_equals($expectedSignature, $validated['razorpay_signature'])) {
            return response()->json(['success' => false, 'message' => 'Invalid payment signature'], 400);
        }

        $subscription = DietSubscription::findOrFail($validated['subscription_id']);
        $subscription->update([
            'status' => 'active',
            'payment_status' => 'paid',
            'razorpay_payment_id' => $validated['razorpay_payment_id'],
        ]);

        return response()->json(['success' => true, 'message' => 'Subscription activated successfully!']);
    }

    public function handleCashfreeReturn(Request $request)
    {
        $orderId = $request->order_id;
        $subscriptionId = $request->subscription_id;

        if (!$orderId || !$subscriptionId) {
            return redirect()->route('diet_chart')->with('error', 'Invalid payment return data.');
        }

        try {
            $baseUrl = config('services.cashfree.env') === 'production'
                ? 'https://api.cashfree.com/pg/orders/'
                : 'https://sandbox.cashfree.com/pg/orders/';

            $response = Http::withoutVerifying()->acceptJson()
                ->withHeaders([
                    'x-client-id' => config('services.cashfree.app_id'),
                    'x-client-secret' => config('services.cashfree.secret_key'),
                    'x-api-version' => config('services.cashfree.api_version', '2025-01-01'),
                ])
                ->get($baseUrl . urlencode($orderId));

            if ($response->successful()) {
                $cfOrder = $response->json();
                
                if (strtoupper($cfOrder['order_status']) === 'PAID') {
                    $subscription = DietSubscription::findOrFail($subscriptionId);
                    
                    if ($subscription->status !== 'active') {
                        $subscription->update([
                            'status' => 'active',
                            'payment_status' => 'paid',
                            'razorpay_payment_id' => $cfOrder['cf_order_id'] ?? null, // Reusing column
                        ]);
                    }

                    return redirect()->route('diet_chart', ['success' => 'true']);
                }
            }

            return redirect()->route('diet_chart')->with('error', 'Payment was not successful or was cancelled.');
        } catch (\Throwable $e) {
            Log::error('Cashfree Diet Return Error: ' . $e->getMessage());
            return redirect()->route('diet_chart')->with('error', 'Error verifying payment.');
        }
    }
}
