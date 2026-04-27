<?php

namespace App\Http\Controllers\Admin;

use App\Mail\ReturnStatusUpdatedMail;
use App\Models\OrderReturn;
use App\Http\Controllers\Controller;
use App\Support\OrderFlow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OrderReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $returns = OrderReturn::with('order.user')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('admin.ecommerce.returns.index', compact('returns'));
    }

    /**
     * Display the specified resource.
     */
    public function show(OrderReturn $orderReturn)
    {
        $orderReturn->load('order.items.product', 'order.user');
        return view('admin.ecommerce.returns.show', compact('orderReturn'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OrderReturn $orderReturn)
    {
        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', OrderFlow::RETURN_STATUSES),
            'refund_amount' => 'required|numeric|min:0',
            'admin_note' => 'nullable|string',
        ]);

        $orderReturn->update($validated);

        $order = $orderReturn->order()->with('payments')->first();
        if ($order) {
            if ($orderReturn->status === 'approved') {
                $order->update([
                    'admin_note' => $validated['admin_note'] ?? $order->admin_note,
                ]);
            }

            if ($orderReturn->status === 'completed') {
                $order->update([
                    'status' => 'returned',
                    'payment_status' => $order->payment_method === 'cod' ? 'refunded' : $order->payment_status,
                ]);

                $latestPayment = $order->payments()->latest()->first();
                if ($latestPayment && $order->payment_method === 'cod') {
                    $latestPayment->update([
                        'status' => 'refunded',
                        'notes' => trim(($latestPayment->notes ?? '') . ' | Return completed and refund processed'),
                    ]);
                }

                $order->statusHistories()->create([
                    'from_status' => 'delivered',
                    'to_status' => 'returned',
                    'from_fulfillment_status' => $order->fulfillment_status,
                    'to_fulfillment_status' => $order->fulfillment_status,
                    'updated_by' => $request->user()?->id,
                    'note' => 'Order marked returned after return request completion.',
                ]);
            }
        }

        $customerEmail = $orderReturn->order?->customer_email;
        if ($customerEmail) {
            Mail::to($customerEmail)->queue(new ReturnStatusUpdatedMail($orderReturn->fresh('order')));
        }

        return redirect()->route('admin.ecommerce.order-returns.index')->with('success', 'Order return status updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OrderReturn $orderReturn)
    {
        $orderReturn->delete();
        return redirect()->route('admin.ecommerce.order-returns.index')->with('success', 'Order return record deleted.');
    }
}
