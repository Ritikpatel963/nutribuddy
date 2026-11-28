<?php

namespace App\Http\Controllers\Admin\Billing;

use App\Exports\Invoices\InvoicesSummaryExport;
use App\Http\Controllers\Admin\ResourceIndexFallbacks;
use App\Http\Controllers\Admin\ResourceWritesDisabled;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class InvoiceController extends Controller
{
    use ResourceIndexFallbacks;
    use ResourceWritesDisabled;

    /**
     * Display a listing of the invoices.
     */
    public function index(Request $request)
    {
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $orders = Order::where('status', '!=', 'cancelled')
            ->when($request->search, function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('order_number', 'like', "%{$search}%")
                        ->orWhere('invoice_number', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('customer_email', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('payment_status'), fn ($q) => $q->where('payment_status', $request->payment_status))
            ->when($request->filled('product_id'), fn ($q) => $q->whereHas('items', fn ($items) => $items->where('product_id', $request->product_id)))
            ->when($fromDate, function ($q, $fromDate) {
                $q->where(function ($sub) use ($fromDate) {
                    $sub->whereDate('placed_at', '>=', $fromDate)
                        ->orWhere(function ($nested) use ($fromDate) {
                            $nested->whereNull('placed_at')->whereDate('created_at', '>=', $fromDate);
                        });
                });
            })
            ->when($toDate, function ($q, $toDate) {
                $q->where(function ($sub) use ($toDate) {
                    $sub->whereDate('placed_at', '<=', $toDate)
                        ->orWhere(function ($nested) use ($toDate) {
                            $nested->whereNull('placed_at')->whereDate('created_at', '<=', $toDate);
                        });
                });
            })
            ->latest('placed_at')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.billing.invoices.index', [
            'orders' => $orders,
            'products' => Product::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'fromDate' => $fromDate,
            'toDate' => $toDate,
        ]);
    }

    /**
     * Export invoice summary in Excel.
     */
    public function export(Request $request): BinaryFileResponse
    {
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $orders = Order::where('status', '!=', 'cancelled')
            ->with(['user', 'items.product', 'items.productVariant', 'payments'])
            ->when($request->search, function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('order_number', 'like', "%{$search}%")
                        ->orWhere('invoice_number', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('customer_email', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('payment_status'), fn ($q) => $q->where('payment_status', $request->payment_status))
            ->when($request->filled('product_id'), fn ($q) => $q->whereHas('items', fn ($items) => $items->where('product_id', $request->product_id)))
            ->when($fromDate, function ($q, $fromDate) {
                $q->where(function ($sub) use ($fromDate) {
                    $sub->whereDate('placed_at', '>=', $fromDate)
                        ->orWhere(function ($nested) use ($fromDate) {
                            $nested->whereNull('placed_at')->whereDate('created_at', '>=', $fromDate);
                        });
                });
            })
            ->when($toDate, function ($q, $toDate) {
                $q->where(function ($sub) use ($toDate) {
                    $sub->whereDate('placed_at', '<=', $toDate)
                        ->orWhere(function ($nested) use ($toDate) {
                            $nested->whereNull('placed_at')->whereDate('created_at', '<=', $toDate);
                        });
                });
            })
            ->latest('placed_at')
            ->latest('id')
            ->get();

        $filename = 'invoice-summary-'.now()->format('Y-m-d-His').'.xlsx';

        return Excel::download(new InvoicesSummaryExport($orders, [
            'search' => $request->input('search'),
            'payment_status' => $request->input('payment_status'),
            'from_date' => $fromDate,
            'to_date' => $toDate,
        ]), $filename);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $invoice)
    {
        $invoice->load(['user', 'items.product.images', 'items.productVariant', 'coupon', 'payments']);

        return view('admin.billing.invoices.show', ['order' => $invoice]);
    }
}
