<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Notifications\AiSensyService;
use App\Services\Payments\RazorpayPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RazorpayPaymentController extends Controller
{
    public function __construct(
        private readonly RazorpayPaymentService $razorpay,
        private readonly AiSensyService $aiSensy
    ) {
    }

    public function verify(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
            'razorpay_order_id' => ['required', 'string'],
            'razorpay_payment_id' => ['required', 'string'],
            'razorpay_signature' => ['required', 'string'],
        ]);

        $order = Order::findOrFail($validated['order_id']);

        if (!$this->razorpay->verifySignature($validated)) {
            Log::warning('Razorpay signature verification failed.', [
                'order_id' => $order->id,
                'payload' => $validated,
            ]);
            return response()->json(['success' => false, 'message' => 'Payment signature verification failed.'], 400);
        }

        try {
            $previousStatus = $order->status;
            $previousFulfillmentStatus = $order->fulfillment_status;

            $order->forceFill([
                'status' => $order->status === 'pending' ? 'confirmed' : $order->status,
                'payment_status' => 'paid',
                'payment_method' => 'razorpay',
            ])->save();

            $payment = $order->payments()->where('provider', 'razorpay')->latest()->first();
            if ($payment) {
                $paymentPayload = $payment->gateway_payload ?? [];
                $aiSensyAlreadySent = !empty($paymentPayload['aisensy_order_paid_sent_at']);

                $payment->forceFill([
                    'status' => 'success',
                    'gateway_payment_id' => $validated['razorpay_payment_id'],
                    'gateway_payload' => array_merge($paymentPayload, ['verification_response' => $validated]),
                    'paid_at' => now(),
                    'notes' => 'Razorpay payment successful',
                ])->save();

                // Send AiSensy WhatsApp update
                if (!$aiSensyAlreadySent && $this->aiSensy->sendOrderPaid($order->refresh())) {
                    $payment->forceFill([
                        'gateway_payload' => array_merge($payment->gateway_payload ?? [], [
                            'aisensy_order_paid_sent_at' => now()->toIso8601String(),
                        ]),
                    ])->save();
                }
            }

            if ($previousStatus !== $order->status) {
                $order->statusHistories()->create([
                    'from_status' => $previousStatus,
                    'to_status' => $order->status,
                    'from_fulfillment_status' => $previousFulfillmentStatus,
                    'to_fulfillment_status' => $order->fulfillment_status,
                    'updated_by' => $order->user_id,
                    'note' => 'Razorpay payment confirmed.',
                ]);
            }
        } catch (\Throwable $exception) {
            Log::error('Error updating order status for Razorpay.', [
                'order_id' => $order->id,
                'message' => $exception->getMessage(),
            ]);
            return response()->json(['success' => false, 'message' => 'Failed to complete order processing.'], 500);
        }

        return response()->json(['success' => true]);
    }

    public function webhook(Request $request): JsonResponse
    {
        $rawBody = $request->getContent();
        $signature = $request->header('x-razorpay-signature');
        $webhookSecret = config('services.razorpay.webhook_secret');

        if ($signature && $webhookSecret) {
            $expected = hash_hmac('sha256', $rawBody, $webhookSecret);
            if (!hash_equals($expected, $signature)) {
                return response()->json(['ok' => false], 401);
            }
        }

        $payload = $request->json()->all();
        $event = $payload['event'] ?? '';

        if ($event === 'order.paid' || $event === 'payment.captured') {
            $razorpayOrderId = data_get($payload, 'payload.payment.entity.order_id') 
                ?: data_get($payload, 'payload.order.entity.id');
            $razorpayPaymentId = data_get($payload, 'payload.payment.entity.id');

            if ($razorpayOrderId) {
                $payment = \App\Models\Payment::where('provider', 'razorpay')
                    ->where('gateway_order_id', $razorpayOrderId)
                    ->first();

                if ($payment) {
                    $order = $payment->order;
                    if ($order && $order->payment_status !== 'paid') {
                        $previousStatus = $order->status;
                        $previousFulfillmentStatus = $order->fulfillment_status;

                        $order->forceFill([
                            'status' => $order->status === 'pending' ? 'confirmed' : $order->status,
                            'payment_status' => 'paid',
                            'payment_method' => 'razorpay',
                        ])->save();

                        $payment->forceFill([
                            'status' => 'success',
                            'gateway_payment_id' => $razorpayPaymentId ?: $payment->gateway_payment_id,
                            'paid_at' => now(),
                            'notes' => 'Razorpay webhook confirmation',
                        ])->save();

                        if ($previousStatus !== $order->status) {
                            $order->statusHistories()->create([
                                'from_status' => $previousStatus,
                                'to_status' => $order->status,
                                'from_fulfillment_status' => $previousFulfillmentStatus,
                                'to_fulfillment_status' => $order->fulfillment_status,
                                'updated_by' => $order->user_id,
                                'note' => 'Razorpay payment confirmed via webhook.',
                            ]);
                        }
                    }
                }
            }
        }

        return response()->json(['ok' => true]);
    }
}
