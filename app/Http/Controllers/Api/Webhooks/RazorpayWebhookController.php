<?php

namespace App\Http\Controllers\Api\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\Payments\RazorpayPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RazorpayWebhookController extends Controller
{
    public function handle(Request $request, RazorpayPaymentService $razorpay): JsonResponse
    {
        $rawBody = $request->getContent();
        $signature = $request->header('x-razorpay-signature');
        $webhookSecret = config('services.razorpay.webhook_secret');

        if (! $signature || ! $webhookSecret) {
            return response()->json(['ok' => false, 'message' => 'Missing signature or webhook secret'], 401);
        }

        $expected = hash_hmac('sha256', $rawBody, $webhookSecret);
        if (! hash_equals($expected, $signature)) {
            return response()->json(['ok' => false, 'message' => 'Invalid signature'], 401);
        }

        $payload = $request->json()->all();
        if (in_array($payload['event'] ?? '', ['order.paid', 'payment.captured'], true)) {
            $razorpayOrderId = data_get($payload, 'payload.payment.entity.order_id')
                ?: data_get($payload, 'payload.order.entity.id');
            $razorpayPaymentId = data_get($payload, 'payload.payment.entity.id');
            $payment = $razorpayOrderId
                ? Payment::where('provider', 'razorpay')->where('gateway_order_id', $razorpayOrderId)->first()
                : null;
            $order = $payment?->order;

            if ($payment && $order) {
                $razorpay->confirmWebhookPayment($payment, $razorpayPaymentId);
            }
        }

        return response()->json(['ok' => true]);
    }
}
