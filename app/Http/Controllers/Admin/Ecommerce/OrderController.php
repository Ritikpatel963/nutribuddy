<?php

namespace App\Http\Controllers\Admin\Ecommerce;

use App\Exports\Orders\OrdersExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Ecommerce\UpdateOrderStatusRequest;
use App\Mail\OrderStatusUpdatedMail;
use App\Models\Order;
use App\Services\Ecommerce\OrderWorkflowService;
use App\Services\Notifications\OrderNotificationService;
use App\Support\OrderFlow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $orders = Order::with(['user', 'coupon', 'items.product', 'items.productVariant', 'payments'])
            ->withCount('items')
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($fromDate, fn ($query) => $query->whereDate('created_at', '>=', $fromDate))
            ->when($toDate, fn ($query) => $query->whereDate('created_at', '<=', $toDate))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.ecommerce.orders.index', [
            'orders' => $orders,
            'statuses' => OrderFlow::ORDER_STATUSES,
            'paymentStatuses' => OrderFlow::PAYMENT_STATUSES,
            'fulfillmentStatuses' => OrderFlow::FULFILLMENT_STATUSES,
            'selectedStatus' => $status,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
        ]);
    }

    public function export(Request $request): BinaryFileResponse
    {
        $status = $request->string('status')->toString();
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $orders = Order::with(['user', 'coupon', 'items.product', 'items.productVariant', 'payments'])
            ->withCount('items')
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($fromDate, fn ($query) => $query->whereDate('created_at', '>=', $fromDate))
            ->when($toDate, fn ($query) => $query->whereDate('created_at', '<=', $toDate))
            ->latest()
            ->get();

        $filename = 'orders-export-'.now()->format('Y-m-d-His').'.xlsx';

        return Excel::download(new OrdersExport($orders, [
            'status' => $status,
            'from_date' => $fromDate,
            'to_date' => $toDate,
        ]), $filename);
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

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order, OrderWorkflowService $workflow): RedirectResponse
    {
        $validated = $request->validated();
        if (! $workflow->updateStatusByAdmin($order, $validated, (int) $request->user()->id)) {
            return back()->with('error', 'Invalid order status transition.');
        }

        if ($order->customer_email && ! app(OrderNotificationService::class)->handlesEmailForStatus($order->status)) {
            Mail::to($order->customer_email)->queue(new OrderStatusUpdatedMail($order));
        }

        return back()->with('success', 'Order status updated successfully.');
    }
}
