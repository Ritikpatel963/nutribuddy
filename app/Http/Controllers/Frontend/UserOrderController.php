<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\OrderStatusUpdatedMail;
use App\Models\Order;
use App\Support\OrderFlow;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class UserOrderController extends Controller
{
    public function detailPage(Request $request, Order $order): View
    {
        abort_unless((int) $order->user_id === (int) $request->user()->id, 403);
        $order->load(['items.product', 'items.productVariant', 'payments', 'statusHistories.updatedBy', 'returns']);

        return view('pages.user-panel.order-details', [
            'order' => $order,
        ]);
    }

    public function invoicePage(Request $request, Order $order): View
    {
        abort_unless((int) $order->user_id === (int) $request->user()->id, 403);
        $order->load(['items', 'payments']);

        return view('pages.user-panel.order-invoice', [
            'order' => $order,
            'invoiceNumber' => 'INV-' . $order->order_number,
        ]);
    }

    public function invoiceDownload(Request $request, Order $order)
    {
        abort_unless((int) $order->user_id === (int) $request->user()->id, 403);
        $order->load(['items', 'payments']);

        $invoiceNumber = 'INV-' . $order->order_number;
        $content = view('invoices.printable', [
            'order' => $order,
            'invoiceNumber' => $invoiceNumber,
        ])->render();

        return response($content)
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $invoiceNumber . '.html"');
    }

    public function index(Request $request): JsonResponse
    {
        $orders = Order::withCount('items')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return response()->json($orders);
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        abort_unless((int) $order->user_id === (int) $request->user()->id, 403);

        $order->load([
            'items.product',
            'items.productVariant',
            'payments',
            'statusHistories.updatedBy',
            'returns',
        ]);

        return response()->json($order);
    }

    public function invoice(Request $request, Order $order): JsonResponse
    {
        abort_unless((int) $order->user_id === (int) $request->user()->id, 403);

        $order->load(['items', 'payments']);

        return response()->json([
            'invoice_number' => 'INV-' . $order->order_number,
            'order' => $order,
            'billing' => [
                'name' => $order->customer_name,
                'email' => $order->customer_email,
                'phone' => $order->customer_phone,
            ],
            'shipping' => [
                'name' => $order->shipping_name,
                'phone' => $order->shipping_phone,
                'line_1' => $order->shipping_address_line_1,
                'line_2' => $order->shipping_address_line_2,
                'landmark' => $order->shipping_landmark,
                'city' => $order->shipping_city,
                'state' => $order->shipping_state,
                'postal_code' => $order->shipping_postal_code,
                'country' => $order->shipping_country,
            ],
            'totals' => [
                'subtotal' => $order->subtotal,
                'discount_total' => $order->discount_total,
                'tax_total' => $order->tax_total,
                'gst_total' => $order->gst_total,
                'cgst_total' => $order->cgst_total,
                'sgst_total' => $order->sgst_total,
                'igst_total' => $order->igst_total,
                'shipping_total' => $order->shipping_total,
                'grand_total' => $order->grand_total,
            ],
        ]);
    }

    public function cancel(Request $request, Order $order): JsonResponse
    {
        abort_unless((int) $order->user_id === (int) $request->user()->id, 403);

        if (! OrderFlow::canMoveTo($order->status, 'cancelled')) {
            return response()->json([
                'message' => 'This order cannot be cancelled at the current stage.',
            ], 422);
        }

        $previousStatus = $order->status;
        $previousFulfillmentStatus = $order->fulfillment_status;

        $order->update([
            'status' => 'cancelled',
            'fulfillment_status' => $previousFulfillmentStatus === 'fulfilled' ? 'fulfilled' : 'unfulfilled',
            'admin_note' => 'Cancelled by customer',
        ]);

        $order->statusHistories()->create([
            'from_status' => $previousStatus,
            'to_status' => 'cancelled',
            'from_fulfillment_status' => $previousFulfillmentStatus,
            'to_fulfillment_status' => $order->fulfillment_status,
            'updated_by' => $request->user()->id,
            'note' => 'Cancelled by customer from user panel.',
        ]);

        if ($order->customer_email) {
            Mail::to($order->customer_email)->queue(new OrderStatusUpdatedMail($order));
        }

        return response()->json([
            'message' => 'Order cancelled successfully.',
            'order' => $order->fresh(),
        ]);
    }
}
