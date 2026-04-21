<?php

namespace App\Http\Controllers\Admin;

use App\Models\OrderReturn;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,completed',
            'refund_amount' => 'required|numeric|min:0',
            'admin_note' => 'nullable|string',
        ]);

        $orderReturn->update([
            'status' => $request->status,
            'refund_amount' => $request->refund_amount,
            'admin_note' => $request->admin_note,
        ]);

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
