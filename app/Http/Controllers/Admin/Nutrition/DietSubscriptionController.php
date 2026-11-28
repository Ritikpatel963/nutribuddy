<?php

namespace App\Http\Controllers\Admin\Nutrition;

use App\Http\Controllers\Controller;
use App\Models\DietSubscription;
use App\Models\SubscriptionPlan;
use App\Services\Nutrition\AdminDietSubscriptionTableService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DietSubscriptionController extends Controller
{
    use \App\Http\Controllers\Admin\ResourceWritesDisabled;
    use \App\Http\Controllers\Admin\ResourceIndexFallbacks;
    public function index()
    {
        $plans = SubscriptionPlan::query()->orderBy('name')->get(['id', 'name']);
        $statuses = DietSubscription::query()->select('status')->distinct()->orderBy('status')->pluck('status');
        $paymentStatuses = DietSubscription::query()->select('payment_status')->distinct()->orderBy('payment_status')->pluck('payment_status');

        return view('admin.nutrition.diet-subscriptions.index', compact('plans', 'statuses', 'paymentStatuses'));
    }

    public function data(Request $request, AdminDietSubscriptionTableService $table): JsonResponse
    {
        return response()->json($table->paginate($request));
    }

    public function show($id)
    {
        $subscription = DietSubscription::with(['user', 'subscriptionPlan', 'child'])->findOrFail($id);
        $assessments = $subscription->child
            ? $subscription->child->assessments()->with('answers.question')->latest()->paginate(5)
            : collect();

        return view('admin.nutrition.diet-subscriptions.show', compact('subscription', 'assessments'));
    }

    public function pause(DietSubscription $subscription, \App\Services\Payments\CashfreeSubscriptionService $cashfree)
    {
        $subId = $subscription->razorpay_order_id;
        if ($subId && str_starts_with($subId, 'sub_')) {
            try {
                $cashfree->manageSubscription($subId, 'PAUSE');
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Cashfree admin pause failed: ' . $e->getMessage());
                return back()->with('error', 'Cashfree pause failed: ' . $e->getMessage());
            }
        }

        $subscription->update(['status' => 'paused']);
        \App\Models\Subscription::where('cashfree_subscription_id', $subId)->update(['status' => 'PAUSED']);

        return back()->with('success', "Subscription #{$subscription->id} has been paused successfully.");
    }

    public function resume(DietSubscription $subscription, \App\Services\Payments\CashfreeSubscriptionService $cashfree)
    {
        $subId = $subscription->razorpay_order_id;
        if ($subId && str_starts_with($subId, 'sub_')) {
            $subModel = \App\Models\Subscription::where('cashfree_subscription_id', $subId)->first();
            if ($subModel && in_array(strtoupper(str_replace(' ', '_', (string) $subModel->status)), ['CUSTOMER_PAUSED', 'CUSTOMER_CANCELLED'], true)) {
                return back()->with('error', 'This subscription was paused/cancelled by the customer from their UPI app. Under NPCI/RBI rules, only the customer can resume it from their payment app.');
            }

            try {
                $cashfree->manageSubscription($subId, 'ACTIVATE');
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Cashfree admin resume failed: ' . $e->getMessage());
                $msg = $e->getMessage();
                if (stripos($msg, 'customer') !== false) {
                    $msg = 'This subscription was paused by the customer from their payment app. Only the customer can resume it.';
                }
                return back()->with('error', 'Cashfree resume failed: ' . $msg);
            }
        }

        $subscription->update(['status' => 'active']);
        \App\Models\Subscription::where('cashfree_subscription_id', $subId)->update(['status' => 'ACTIVE']);

        return back()->with('success', "Subscription #{$subscription->id} has been resumed successfully.");
    }

    public function cancel(DietSubscription $subscription, \App\Services\Payments\CashfreeSubscriptionService $cashfree)
    {
        $subId = $subscription->razorpay_order_id;
        if ($subId && str_starts_with($subId, 'sub_')) {
            try {
                $cashfree->manageSubscription($subId, 'CANCEL');
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Cashfree admin cancel failed: ' . $e->getMessage());
                return back()->with('error', 'Cashfree cancel failed: ' . $e->getMessage());
            }
        }

        $subscription->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);
        \App\Models\Subscription::where('cashfree_subscription_id', $subId)->update([
            'status' => 'CANCELLED',
            'cancelled_at' => now(),
        ]);

        return back()->with('success', "Subscription #{$subscription->id} has been cancelled successfully.");
    }

    public function syncStatus(DietSubscription $subscription, \App\Services\Payments\CashfreeSubscriptionService $cashfree)
    {
        $subId = $subscription->razorpay_order_id;
        if (! $subId || ! str_starts_with($subId, 'sub_')) {
            return back()->with('error', 'This subscription is not linked to a Cashfree subscription ID.');
        }

        try {
            $cfData = $cashfree->getSubscription($subId);
            $cfStatus = strtoupper(str_replace(' ', '_', $cfData['subscription_status'] ?? ''));

            $localStatus = match ($cfStatus) {
                'ACTIVE' => 'active',
                'PAUSED', 'CUSTOMER_PAUSED' => 'paused',
                'CANCELLED', 'CUSTOMER_CANCELLED' => 'cancelled',
                'COMPLETED', 'EXPIRED' => 'expired',
                default => strtolower($cfStatus),
            };

            $updateData = ['status' => $localStatus];
            if ($localStatus === 'cancelled' && ! $subscription->cancelled_at) {
                $updateData['cancelled_at'] = now();
            }
            if ($localStatus === 'active' && $subscription->payment_status !== 'paid') {
                $updateData['payment_status'] = 'paid';
            }
            $subscription->update($updateData);

            \App\Models\Subscription::where('cashfree_subscription_id', $subId)->update([
                'status' => $cfStatus,
                'cf_subscription_id' => $cfData['cf_subscription_id'] ?? null,
            ]);

            $statusText = match ($cfStatus) {
                'CUSTOMER_PAUSED' => 'CUSTOMER PAUSED (Paused by customer via UPI app)',
                'CUSTOMER_CANCELLED' => 'CUSTOMER CANCELLED (Cancelled by customer via UPI app)',
                default => $cfStatus,
            };

            return back()->with('success', "Subscription status synced from Cashfree: {$statusText}");
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Cashfree sync status failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to sync with Cashfree: ' . $e->getMessage());
        }
    }
}
