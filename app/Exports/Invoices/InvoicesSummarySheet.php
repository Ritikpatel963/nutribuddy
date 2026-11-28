<?php

namespace App\Exports\Invoices;

use App\Models\Setting;
use App\Services\PricingService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InvoicesSummarySheet implements FromArray, ShouldAutoSize, WithStyles, WithTitle
{
    public function __construct(
        private readonly Collection $orders,
        private readonly array $filters = []
    ) {}

    public function array(): array
    {
        $sellerState = Setting::get('store_state', 'Gujarat');

        $totalInvoices = $this->orders->count();
        $totalInvoiceValue = (float) $this->orders->sum('grand_total');
        $totalTax = (float) $this->orders->sum('tax_total');
        $totalSubtotal = (float) $this->orders->sum('subtotal');

        $totalIgst = 0.0;
        $totalCgst = 0.0;
        $totalSgst = 0.0;
        $totalTaxable = 0.0;

        foreach ($this->orders as $order) {
            $customerState = $order->shipping_state ?: ($order->billing_state ?: 'Gujarat');

            if ((float) $order->igst_total > 0) {
                $isInter = true;
            } elseif ((float) $order->cgst_total > 0 || (float) $order->sgst_total > 0) {
                $isInter = false;
            } else {
                $isInter = ! PricingService::isSameState($customerState, $sellerState);
            }

            if ($order->items->isEmpty()) {
                $tax = (float) $order->tax_total;
                $taxable = max(0.0, (float) $order->grand_total - $tax);
                $totalTaxable += $taxable;

                if ($isInter) {
                    $totalIgst += $tax;
                } else {
                    $totalCgst += round($tax / 2, 2);
                    $totalSgst += round($tax / 2, 2);
                }
            } else {
                foreach ($order->items as $item) {
                    $unitPrice = (float) $item->unit_price;
                    $qty = (int) $item->quantity;
                    $itemTax = (float) $item->tax_amount;
                    $lineTotal = (float) $item->line_total;

                    $taxable = ($unitPrice > 0 && $qty > 0) ? ($unitPrice * $qty) : max(0.0, $lineTotal - $itemTax);
                    $totalTaxable += $taxable;

                    if ($isInter) {
                        $totalIgst += $itemTax;
                    } else {
                        $totalCgst += round($itemTax / 2, 2);
                        $totalSgst += round($itemTax / 2, 2);
                    }
                }
            }
        }

        $paymentCounts = $this->orders->groupBy(fn ($o) => strtolower((string) ($o->payment_status ?: 'pending')))->map(fn ($group) => [
            'count' => $group->count(),
            'total' => (float) $group->sum('grand_total'),
        ]);

        $stateCounts = $this->orders->groupBy(fn ($o) => (string) ($o->shipping_state ?: ($o->billing_state ?: 'Unspecified')))->map(fn ($group) => [
            'count' => $group->count(),
            'total' => (float) $group->sum('grand_total'),
            'tax' => (float) $group->sum('tax_total'),
        ]);

        $dateRangeStr = (! empty($this->filters['from_date']) ? $this->filters['from_date'] : 'Start')
            .' to '
            .(! empty($this->filters['to_date']) ? $this->filters['to_date'] : 'Now');

        $rows = [
            ['NutriBuddy Invoices & Tax Summary Report'],
            ['Generated At', now()->format('d M Y, h:i A')],
            ['Filter - Date Range', $dateRangeStr],
            ['Filter - Payment Status', ! empty($this->filters['payment_status']) ? ucfirst($this->filters['payment_status']) : 'All Payments'],
            ['Filter - Search', ! empty($this->filters['search']) ? $this->filters['search'] : 'All'],
            [''],
            ['KEY FINANCIAL & GST METRICS', 'VALUE (INR)'],
            ['Total Invoices', $totalInvoices],
            ['Total Invoice Value (Gross Amount)', number_format($totalInvoiceValue, 2)],
            ['Total Taxable Value', number_format($totalTaxable, 2)],
            ['Total Tax / GST Collected', number_format($totalTax, 2)],
            ['Total Integrated Tax (IGST)', number_format($totalIgst, 2)],
            ['Total Central Tax (CGST)', number_format($totalCgst, 2)],
            ['Total State Tax (SGST)', number_format($totalSgst, 2)],
            [''],
            ['PAYMENT STATUS BREAKDOWN', 'INVOICES COUNT', 'TOTAL VALUE (INR)'],
        ];

        foreach ($paymentCounts as $status => $data) {
            $rows[] = [
                ucfirst($status),
                $data['count'],
                number_format($data['total'], 2),
            ];
        }

        $rows[] = [''];
        $rows[] = ['PLACE OF SUPPLY (STATE-WISE) BREAKDOWN', 'INVOICES COUNT', 'TAX AMOUNT (INR)', 'TOTAL VALUE (INR)'];

        foreach ($stateCounts as $state => $data) {
            $rows[] = [
                $state,
                $data['count'],
                number_format($data['tax'], 2),
                number_format($data['total'], 2),
            ];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            7 => ['font' => ['bold' => true]],
            16 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Invoices Summary';
    }
}
