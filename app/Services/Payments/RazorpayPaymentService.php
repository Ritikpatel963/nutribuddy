<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class RazorpayPaymentService
{
    public function createOrder(Order $order): array
    {
        $payload = [
            'amount' => round((float) $order->grand_total * 100), // Razorpay expects amount in paise (1 INR = 100 paise)
            'currency' => 'INR',
            'receipt' => $order->order_number,
            'notes' => [
                'local_order_id' => (string) $order->id,
                'checkout_token' => (string) $order->checkout_token,
            ],
        ];

        $response = Http::withBasicAuth($this->keyId(), $this->keySecret())
            ->post('https://api.razorpay.com/v1/orders', $payload);

        if (!$response->successful()) {
            Log::error('Razorpay order creation failed.', [
                'order_id' => $order->id,
                'status' => $response->status(),
                'body' => $response->json() ?: $response->body(),
            ]);

            throw new RuntimeException('Unable to start Razorpay payment. Please try again.');
        }

        $razorpayOrder = $response->json();

        $order->payments()->updateOrCreate(
            ['provider' => 'razorpay', 'transaction_type' => 'capture'],
            [
                'status' => 'initiated',
                'currency' => 'INR',
                'amount' => $order->grand_total,
                'gateway_order_id' => $razorpayOrder['id'],
                'gateway_payload' => $razorpayOrder,
                'notes' => 'Razorpay payment initiated',
            ]
        );

        return $razorpayOrder;
    }

    public function verifySignature(array $data): bool
    {
        $razorpayOrderId = $data['razorpay_order_id'] ?? '';
        $razorpayPaymentId = $data['razorpay_payment_id'] ?? '';
        $razorpaySignature = $data['razorpay_signature'] ?? '';

        if (!$razorpayOrderId || !$razorpayPaymentId || !$razorpaySignature) {
            return false;
        }

        $expected = hash_hmac('sha256', $razorpayOrderId . '|' . $razorpayPaymentId, $this->keySecret());

        return hash_equals($expected, $razorpaySignature);
    }

    private function keyId(): string
    {
        $keyId = (string) config('services.razorpay.key_id');
        if ($keyId === '') {
            throw new RuntimeException('Razorpay Key ID is not configured.');
        }

        return $keyId;
    }

    private function keySecret(): string
    {
        $keySecret = (string) config('services.razorpay.key_secret');
        if ($keySecret === '') {
            throw new RuntimeException('Razorpay Key Secret is not configured.');
        }

        return $keySecret;
    }
}
