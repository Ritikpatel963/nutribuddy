<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\OrderStatusUpdatedMail;
use App\Models\Order;
use App\Support\OrderFlow;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class UserOrderController extends Controller
{
    public function invoicesIndex(Request $request): View
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->where('status', '!=', 'cancelled')
            ->latest()
            ->paginate(15);

        return view('pages.user-panel.invoices', compact('orders'));
    }

    public function detailPage(Request $request, Order $order): View
    {
        abort_unless((int) $order->user_id === (int) $request->user()->id || $request->user()->role === 'admin', 403);
        $order->load([
            'items.product',
            'items.productVariant',
            'items.returnItems.returnRequest',
            'payments',
            'statusHistories.updatedBy',
            'returns',
        ]);

        return view('pages.user-panel.order-details', [
            'order' => $order,
        ]);
    }

    public function invoicePage(Request $request, Order $order): View
    {
        abort_unless((int) $order->user_id === (int) $request->user()->id || $request->user()->role === 'admin', 403);
        $order->load(['items.product', 'items.productVariant', 'payments']);

        return view('pages.user-panel.order-invoice', [
            'order' => $order,
            'invoiceNumber' => 'INV-' . $order->order_number,
        ]);
    }

    public function invoiceDownload(Request $request, Order $order)
    {
        abort_unless((int) $order->user_id === (int) $request->user()->id || $request->user()->role === 'admin', 403);
        $order->load(['items.product', 'items.productVariant', 'payments']);

        $invoiceNumber = 'INV-' . $order->order_number;

        $pdf = Pdf::loadView('invoices.printable', [
            'order' => $order,
            'invoiceNumber' => $invoiceNumber,
        ])->setPaper('a4');

        return $pdf->download($invoiceNumber . '.pdf');
    }

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d'],
        ]);

        if (
            ($validated['date_from'] ?? null)
            && ($validated['date_to'] ?? null)
            && $validated['date_from'] > $validated['date_to']
        ) {
            return response()->json([
                'message' => 'The end date must be after or equal to the start date.',
            ], 422);
        }

        $orders = Order::with(['items.returnItems.returnRequest'])
            ->withCount('items')
            ->where('user_id', $request->user()->id)
            ->when($validated['date_from'] ?? null, function ($query, string $dateFrom) {
                $query->where(function ($dateQuery) use ($dateFrom) {
                    $dateQuery
                        ->whereDate('placed_at', '>=', $dateFrom)
                        ->orWhere(function ($fallbackQuery) use ($dateFrom) {
                            $fallbackQuery
                                ->whereNull('placed_at')
                                ->whereDate('created_at', '>=', $dateFrom);
                        });
                });
            })
            ->when($validated['date_to'] ?? null, function ($query, string $dateTo) {
                $query->where(function ($dateQuery) use ($dateTo) {
                    $dateQuery
                        ->whereDate('placed_at', '<=', $dateTo)
                        ->orWhere(function ($fallbackQuery) use ($dateTo) {
                            $fallbackQuery
                                ->whereNull('placed_at')
                                ->whereDate('created_at', '<=', $dateTo);
                        });
                });
            })
            ->latest()
            ->paginate(10);

        $orders->getCollection()->transform(function (Order $order) {
            $order->items->each(function ($item) {
                $alreadyRequested = $item->returnItems
                    ->filter(fn ($returnItem) => in_array($returnItem->returnRequest?->status, ['pending', 'approved', 'completed'], true))
                    ->sum('quantity');

                $item->setAttribute('returnable_quantity', max(0, (int) $item->quantity - (int) $alreadyRequested));
                $item->setAttribute('returned_quantity', (int) $alreadyRequested);
            });

            return $order;
        });

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
                'coins_redeemed' => $order->coins_redeemed,
                'coin_discount' => $order->coin_discount,
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
