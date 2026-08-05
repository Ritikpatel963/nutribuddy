<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Services\Notifications\AiSensyService;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class CashfreePaymentService
{
    public function __construct(private readonly AiSensyService $aiSensy)
    {
    }

    public function createOrder(Order $order): array
    {
        $payment = $order->payments()->where('provider', 'cashfree')->latest()->first();
        $existingSessionId = $payment?->gateway_payload['payment_session_id'] ?? null;

        if ($existingSessionId) {
            return [
                'payment_session_id' => $existingSessionId,
                'cashfree_order' => $payment->gateway_payload,
            ];
        }

        $payload = [
            'order_id' => $order->order_number,
            'order_amount' => round((float) $order->grand_total, 2),
            'order_currency' => $order->currency ?: 'INR',
            'customer_details' => [
                'customer_id' => 'user_' . $order->user_id,
                'customer_name' => $order->customer_name,
                'customer_email' => $order->customer_email ?: 'customer-' . $order->id . '@nutribuddy.local',
                'customer_phone' => preg_replace('/\D+/', '', (string) $order->customer_phone),
            ],
            'order_meta' => [
                'return_url' => route('cashfree.return') . '?order_id={order_id}',
                'notify_url' => route('cashfree.webhook'),
            ],
            'order_note' => 'NutriBuddy order ' . $order->order_number,
            'order_tags' => [
                'local_order_id' => (string) $order->id,
                'checkout_token' => (string) $order->checkout_token,
            ],
        ];

        $response = $this->client()
            ->withHeaders(['x-idempotency-key' => (string) Str::uuid()])
            ->post($this->baseUrl() . '/orders', $payload);

        if (! $response->successful()) {
            Log::error('Cashfree order creation failed.', [
                'order_id' => $order->id,
                'status' => $response->status(),
                'body' => $response->json() ?: $response->body(),
            ]);

            throw new RuntimeException('Unable to start Cashfree payment. Please try again.');
        }

        $cashfreeOrder = $response->json();

        $order->payments()->updateOrCreate(
            ['provider' => 'cashfree', 'transaction_type' => 'capture'],
            [
                'status' => 'initiated',
                'currency' => $order->currency ?: 'INR',
                'amount' => $order->grand_total,
                'gateway_order_id' => $cashfreeOrder['order_id'] ?? $order->order_number,
                'gateway_payment_id' => isset($cashfreeOrder['cf_order_id']) ? (string) $cashfreeOrder['cf_order_id'] : null,
                'gateway_payload' => $cashfreeOrder,
                'notes' => 'Cashfree payment initiated',
            ]
        );

        return [
            'payment_session_id' => $cashfreeOrder['payment_session_id'] ?? null,
            'cashfree_order' => $cashfreeOrder,
        ];
    }

    public function fetchOrder(string $cashfreeOrderId): array
    {
        $response = $this->client()->get($this->baseUrl() . '/orders/' . urlencode($cashfreeOrderId));

        if (! $response->successful()) {
            throw new RuntimeException('Unable to verify Cashfree order.');
        }

        return $response->json();
    }

    public function syncOrderStatus(Order $order, ?array $cashfreeOrder = null): Order
    {
        $cashfreeOrder ??= $this->fetchOrder($order->order_number);
        $status = strtoupper((string) ($cashfreeOrder['order_status'] ?? ''));

        $payment = $order->payments()->where('provider', 'cashfree')->latest()->first();

        if ($status === 'PAID') {
            $previousStatus = $order->status;
            $previousFulfillmentStatus = $order->fulfillment_status;
            $paymentPayload = $payment?->gateway_payload ?? [];
            $aiSensyAlreadySent = ! empty($paymentPayload['aisensy_order_paid_sent_at']);

            $order->forceFill([
                'status' => $order->status === 'pending' ? 'confirmed' : $order->status,
                'payment_status' => 'paid',
                'payment_method' => 'cashfree',
            ])->save();

            if ($payment) {
                $payment->forceFill([
                    'status' => 'success',
                    'gateway_payload' => array_merge($payment->gateway_payload ?? [], ['verified_order' => $cashfreeOrder]),
                    'paid_at' => now(),
                    'notes' => 'Cashfree payment successful',
                ])->save();
            }

            if ($payment && ! $aiSensyAlreadySent && $this->aiSensy->sendOrderPaid($order->refresh())) {
                $payment->forceFill([
                    'gateway_payload' => array_merge($payment->gateway_payload ?? [], [
                        'aisensy_order_paid_sent_at' => now()->toIso8601String(),
                    ]),
                ])->save();
            }

            if ($previousStatus !== $order->status) {
                $order->statusHistories()->create([
                    'from_status' => $previousStatus,
                    'to_status' => $order->status,
                    'from_fulfillment_status' => $previousFulfillmentStatus,
                    'to_fulfillment_status' => $order->fulfillment_status,
                    'updated_by' => $order->user_id,
                    'note' => 'Cashfree payment confirmed.',
                ]);
            }

            return $order->refresh();
        }

        if (in_array($status, ['EXPIRED', 'TERMINATED', 'TERMINATION_REQUESTED'], true)) {
            $order->forceFill(['payment_status' => 'failed'])->save();
            if ($payment) {
                $payment->forceFill([
                    'status' => 'failed',
                    'gateway_payload' => array_merge($payment->gateway_payload ?? [], ['verified_order' => $cashfreeOrder]),
                    'notes' => 'Cashfree payment not completed',
                ])->save();
            }
        }

        return $order->refresh();
    }

    public function webhookSignatureIsValid(string $rawBody, ?string $signature, ?string $timestamp): bool
    {
        if (! $signature || ! $timestamp) {
            return false;
        }

        $expected = base64_encode(hash_hmac('sha256', $timestamp . $rawBody, $this->secretKey(), true));

        return hash_equals($expected, $signature);
    }

    private function client(): PendingRequest
    {
        return Http::acceptJson()
            ->asJson()
            ->withOptions(['proxy' => ''])
            ->withHeaders([
                'x-client-id' => $this->appId(),
                'x-client-secret' => $this->secretKey(),
                'x-api-version' => (string) config('services.cashfree.api_version', '2025-01-01'),
            ])
            ->timeout(20);
    }

    private function baseUrl(): string
    {
        return config('services.cashfree.env') === 'production'
            ? 'https://api.cashfree.com/pg'
            : 'https://sandbox.cashfree.com/pg';
    }

    private function appId(): string
    {
        $appId = (string) config('services.cashfree.app_id');
        if ($appId === '') {
            throw new RuntimeException('Cashfree app id is not configured.');
        }

        return $appId;
    }

    private function secretKey(): string
    {
        $secretKey = (string) config('services.cashfree.secret_key');
        if ($secretKey === '') {
            throw new RuntimeException('Cashfree secret key is not configured.');
        }

        return $secretKey;
    }
}
