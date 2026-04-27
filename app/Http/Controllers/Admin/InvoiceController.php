<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the invoices.
     */
    public function index(Request $request)
    {
        $orders = Order::where('status', '!=', 'cancelled')
            ->when($request->search, function($q, $search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15);

        return view('admin.ecommerce.invoices.index', compact('orders'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $invoice)
    {
        $invoice->load(['user', 'items.product.images', 'items.productVariant', 'coupon', 'payments']);

        return view('admin.ecommerce.invoices.show', ['order' => $invoice]);
    }
}
