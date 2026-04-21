<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends Controller
{
    private const ORDER_STATUSES = [
        'pending',
        'confirmed',
        'processing',
        'packed',
        'shipped',
        'delivered',
        'cancelled',
        'returned',
    ];

    private const PAYMENT_STATUSES = [
        'pending',
        'paid',
        'failed',
        'refunded',
        'partially_refunded',
    ];

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
            'statuses' => self::ORDER_STATUSES,
            'paymentStatuses' => self::PAYMENT_STATUSES,
            'selectedStatus' => $status,
        ]);
    }

    public function show(Order $order): View
    {
        $order->load(['items.product', 'items.productVariant', 'payments']);

        return view('admin.ecommerce.orders.show', [
            'order' => $order,
            'statuses' => self::ORDER_STATUSES,
            'paymentStatuses' => self::PAYMENT_STATUSES,
        ]);
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(self::ORDER_STATUSES)],
            'payment_status' => ['required', Rule::in(self::PAYMENT_STATUSES)],
            'admin_note' => ['nullable', 'string'],
        ]);

        $order->update($validated);

        return back()->with('success', 'Order status updated successfully.');
    }
}
