<?php

namespace App\Services\Nutrition;

use App\Models\DietSubscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DietPaymentService
{
    public function createOrder(User $user, array $data): array
    {
        $plan = SubscriptionPlan::where('slug', $data['plan_slug'])
            ->where('is_active', true)
            ->firstOrFail();
        [$priceColumn, $days] = match ($data['cycle']) {
            'monthly' => ['monthly_price', 30],
            'quarterly' => ['quarterly_price', 90],
            'annually' => ['annual_price', 365],
        };
        $price = $plan->{$priceColumn};
        if ($price === null) {
            return ['success' => false, 'message' => 'This billing cycle is not available for the selected plan.', '_status' => 422];
        }
        $amountInPaise = (int) round((float) $price * 100);
        $amountInRupees = round((float) $price, 2);

        $subscription = DietSubscription::create([
            'user_id' => $user->id,
            'child_id' => $data['child_id'],
            'subscription_plan_id' => $plan->id,
            'model' => $plan->name.' ('.ucfirst($data['cycle']).')',
            'status' => 'pending',
            'payment_status' => 'pending',
            'amount_paid' => $amountInRupees,
            'starts_at' => now(),
            'expires_at' => now()->addDays($days),
        ]);

        if ($amountInRupees <= 0) {
            $this->activate($subscription);

            return ['success' => true, 'gateway' => 'free', 'subscription_id' => $subscription->id];
        }

        try {
            return $data['gateway'] === 'razorpay'
                ? $this->createRazorpayOrder($user, $plan, $subscription, $amountInPaise)
                : $this->createCashfreeOrder($user, $plan, $subscription, $amountInRupees);
        } catch (\Throwable $exception) {
            Log::error('Diet plan payment order failed.', [
                'subscription_id' => $subscription->id,
                'exception' => $exception,
            ]);

            return ['success' => false, 'message' => 'Unable to process payment at this time.', '_status' => 500];
        }
    }

    public function verifyRazorpayPayment(User $user, array $data): array
    {
        $subscription = DietSubscription::whereKey($data['subscription_id'])
            ->where('user_id', $user->id)->firstOrFail();

        if (! hash_equals((string) $subscription->razorpay_order_id, $data['razorpay_order_id'])) {
            return ['success' => false, 'message' => 'Invalid payment order.', '_status' => 400];
        }

        $expected = hash_hmac(
            'sha256',
            $data['razorpay_order_id'].'|'.$data['razorpay_payment_id'],
            config('services.razorpay.key_secret')
        );
        if (! hash_equals($expected, $data['razorpay_signature'])) {
            return ['success' => false, 'message' => 'Invalid payment signature', '_status' => 400];
        }

        $subscription->update([
            'status' => 'active',
            'payment_status' => 'paid',
            'razorpay_payment_id' => $data['razorpay_payment_id'],
        ]);
        $this->activateRelatedServices($subscription);

        return ['success' => true, 'message' => 'Subscription activated successfully!'];
    }

    public function confirmCashfreeReturn(?string $orderId, ?string $subscriptionId): array
    {
        if (! $orderId || ! $subscriptionId) {
            return ['success' => false, 'message' => 'Invalid payment return data.'];
        }

        try {
            $subscription = DietSubscription::whereKey($subscriptionId)
                ->where('razorpay_order_id', $orderId)->first();
            if (! $subscription) {
                return ['success' => false, 'message' => 'Invalid payment return data.'];
            }

            $baseUrl = config('services.cashfree.env') === 'production'
                ? 'https://api.cashfree.com/pg/orders/'
                : 'https://sandbox.cashfree.com/pg/orders/';
            $response = Http::acceptJson()->withHeaders($this->cashfreeHeaders())
                ->get($baseUrl.urlencode($orderId));

            if (! $response->successful()) {
                return ['success' => false, 'message' => 'Payment was not successful or was cancelled.'];
            }
            $order = $response->json();
            if (($order['order_id'] ?? null) !== $orderId || strtoupper($order['order_status'] ?? '') !== 'PAID') {
                return ['success' => false, 'message' => 'Payment was not successful or was cancelled.'];
            }

            if ($subscription->status !== 'active' || $subscription->payment_status !== 'paid') {
                $subscription->update([
                    'status' => 'active',
                    'payment_status' => 'paid',
                    'razorpay_payment_id' => $order['cf_order_id'] ?? null,
                ]);
            }
            $this->activateRelatedServices($subscription);

            return ['success' => true, 'child_id' => $subscription->child_id];
        } catch (\Throwable $exception) {
            Log::error('Cashfree diet payment verification failed.', ['exception' => $exception]);

            return ['success' => false, 'message' => 'Error verifying payment.'];
        }
    }

    private function createRazorpayOrder(User $user, SubscriptionPlan $plan, DietSubscription $subscription, int $amount): array
    {
        $response = Http::withBasicAuth(config('services.razorpay.key_id'), config('services.razorpay.key_secret'))
            ->post('https://api.razorpay.com/v1/orders', [
                'amount' => $amount,
                'currency' => 'INR',
                'receipt' => 'diet_'.$subscription->id.'_'.uniqid(),
                'notes' => ['plan_id' => $plan->id, 'user_id' => $user->id, 'subscription_id' => $subscription->id],
            ]);
        if (! $response->successful()) {
            Log::warning('Razorpay rejected diet subscription order.', ['subscription_id' => $subscription->id]);

            return ['success' => false, 'message' => 'Unable to process payment at this time.', '_status' => 502];
        }
        $order = $response->json();
        $subscription->update(['razorpay_order_id' => $order['id']]);

        return [
            'success' => true, 'gateway' => 'razorpay', 'razorpay_order_id' => $order['id'],
            'amount' => $amount, 'currency' => 'INR', 'subscription_id' => $subscription->id,
        ];
    }

    private function createCashfreeOrder(User $user, SubscriptionPlan $plan, DietSubscription $subscription, float $amount): array
    {
        $orderId = 'diet_sub_'.$subscription->id.'_'.uniqid();
        $baseUrl = config('services.cashfree.env') === 'production'
            ? 'https://api.cashfree.com/pg/orders'
            : 'https://sandbox.cashfree.com/pg/orders';
        $response = Http::acceptJson()->withHeaders($this->cashfreeHeaders())->post($baseUrl, [
            'order_id' => $orderId,
            'order_amount' => $amount,
            'order_currency' => 'INR',
            'customer_details' => [
                'customer_id' => 'user_'.$user->id,
                'customer_name' => $user->name,
                'customer_email' => $user->email,
                'customer_phone' => '9999999999',
            ],
            'order_meta' => [
                'return_url' => str_replace('http://', 'https://', route('diet.payment.cashfree.return'))
                    .'?order_id={order_id}&subscription_id='.$subscription->id,
            ],
            'order_note' => 'Diet Subscription '.$plan->name,
        ]);
        if (! $response->successful()) {
            throw new \RuntimeException('Cashfree order creation failed.');
        }
        $order = $response->json();
        $subscription->update(['razorpay_order_id' => $order['order_id']]);

        return [
            'success' => true, 'gateway' => 'cashfree', 'payment_session_id' => $order['payment_session_id'],
            'order_id' => $order['order_id'], 'subscription_id' => $subscription->id,
        ];
    }

    private function activate(DietSubscription $subscription): void
    {
        $subscription->update(['status' => 'active', 'payment_status' => 'paid']);
        $this->activateRelatedServices($subscription);
    }

    private function activateRelatedServices(DietSubscription $subscription): void
    {
        app(NutritionLeadService::class)->createForSubscription($subscription);
        $assessment = $subscription->child?->assessments()->latest()->first();
        if (! $assessment || $assessment->dietPlans()->exists()) {
            return;
        }
        $answers = $assessment->answers()->pluck('answer_value', 'question_id')
            ->map(fn ($value) => json_decode($value, true))->all();
        try {
            $result = app(DietChartGeneratorService::class)->generateDietChart($assessment, $answers);
            if (! ($result['success'] ?? false)) {
                throw new \RuntimeException($result['message'] ?? 'Diet chart generation failed.');
            }
            $subscription->nutritionLead?->update(['status' => 'awaiting_review']);
        } catch (\Throwable $exception) {
            Log::error('Paid diet chart generation failed.', [
                'subscription_id' => $subscription->id,
                'exception' => $exception,
            ]);
        }
    }

    private function cashfreeHeaders(): array
    {
        return [
            'x-client-id' => config('services.cashfree.app_id'),
            'x-client-secret' => config('services.cashfree.secret_key'),
            'x-api-version' => config('services.cashfree.api_version', '2025-01-01'),
        ];
    }
}
