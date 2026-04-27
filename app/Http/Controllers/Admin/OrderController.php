<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OrderStatusUpdatedMail;
use App\Models\Order;
use App\Support\OrderFlow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();

        $orders = Order::with(['user', 'coupon'])
            ->withCount('items')
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.ecommerce.orders.index', [
            'orders' => $orders,
            'statuses' => OrderFlow::ORDER_STATUSES,
            'paymentStatuses' => OrderFlow::PAYMENT_STATUSES,
            'fulfillmentStatuses' => OrderFlow::FULFILLMENT_STATUSES,
            'selectedStatus' => $status,
        ]);
    }

    public function show(Order $order): View
    {
        $order->load(['items.product', 'items.productVariant', 'payments', 'statusHistories.updatedBy']);

        return view('admin.ecommerce.orders.show', [
            'order' => $order,
            'statuses' => OrderFlow::ORDER_STATUSES,
            'paymentStatuses' => OrderFlow::PAYMENT_STATUSES,
            'fulfillmentStatuses' => OrderFlow::FULFILLMENT_STATUSES,
        ]);
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(OrderFlow::ORDER_STATUSES)],
            'fulfillment_status' => ['nullable', Rule::in(OrderFlow::FULFILLMENT_STATUSES)],
            'payment_status' => ['nullable', Rule::in(OrderFlow::PAYMENT_STATUSES)],
            'admin_note' => ['nullable', 'string'],
        ]);

        // Fallback to current values if not provided (e.g. from the quick-edit modal)
        $validated['fulfillment_status'] = $validated['fulfillment_status'] ?? $order->fulfillment_status;
        $validated['payment_status'] = $validated['payment_status'] ?? $order->payment_status;

        if (! OrderFlow::canMoveTo($order->status, $validated['status'])) {
            return back()->with('error', 'Invalid order status transition.');
        }

        $previousStatus = $order->status;
        $previousFulfillmentStatus = $order->fulfillment_status;

        $order->update($validated);
        if ($order->status === 'delivered' && $order->payment_method === 'cod' && $order->payment_status === 'pending') {
            $order->update(['payment_status' => 'paid']);
            $latestPayment = $order->payments()->latest()->first();
            $latestPayment?->update([
                'status' => 'paid',
                'paid_at' => now(),
                'notes' => trim(($latestPayment?->notes ?? '') . ' | Marked paid on delivery by admin'),
            ]);
        }

        $order->statusHistories()->create([
            'from_status' => $previousStatus,
            'to_status' => $validated['status'],
            'from_fulfillment_status' => $previousFulfillmentStatus,
            'to_fulfillment_status' => $validated['fulfillment_status'],
            'updated_by' => $request->user()?->id,
            'note' => $validated['admin_note'] ?? null,
        ]);

        if ($order->customer_email) {
            Mail::to($order->customer_email)->queue(new OrderStatusUpdatedMail($order));
        }

        return back()->with('success', 'Order status updated successfully.');
    }
}
