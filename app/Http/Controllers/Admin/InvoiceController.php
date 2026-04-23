<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $order->load(['user', 'items.product.images', 'coupon']);

        return view('admin.ecommerce.invoices.show', compact('order'));
    }

    public function invoiceAdd()
    {
        return view('invoice/invoiceAdd');
    }
    
    public function invoiceEdit()
    {
        return view('invoice/invoiceEdit');
    }
    
    public function invoiceList()
    {
        return view('invoice/invoiceList');
    }
    
    public function invoicePreview()
    {
        return view('invoice/invoicePreview');
    }
    
}
