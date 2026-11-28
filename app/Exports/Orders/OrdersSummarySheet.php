<?php

namespace App\Exports\Orders;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrdersSummarySheet implements FromArray, ShouldAutoSize, WithStyles, WithTitle
{
    public function __construct(
        private readonly Collection $orders,
        private readonly array $filters = []
    ) {}

    public function array(): array
    {
        $totalOrders = $this->orders->count();
        $totalGross = (float) $this->orders->sum('grand_total');
        $totalSubtotal = (float) $this->orders->sum('subtotal');
        $totalTax = (float) $this->orders->sum('tax_total');
        $totalShipping = (float) $this->orders->sum('shipping_total');
        $totalDiscount = (float) $this->orders->sum('discount_total');
        $totalCoinsRedeemed = (int) $this->orders->sum('coins_redeemed');
        $totalCoinDiscount = (float) $this->orders->sum('coin_discount');

        $statusCounts = $this->orders->groupBy('status')->map(fn ($group) => [
            'count' => $group->count(),
            'total' => (float) $group->sum('grand_total'),
        ]);

        $paymentCounts = $this->orders->groupBy(fn ($o) => strtolower((string) ($o->payment_method ?: 'other')))->map(fn ($group) => [
            'count' => $group->count(),
            'total' => (float) $group->sum('grand_total'),
        ]);

        $rows = [
            ['NutriBuddy Orders Summary Report'],
            ['Generated At', now()->format('d M Y, h:i A')],
            ['Filter - Status', ! empty($this->filters['status']) ? ucfirst($this->filters['status']) : 'All Statuses'],
            ['Filter - Date Range', (! empty($this->filters['from_date']) ? $this->filters['from_date'] : 'Start').' to '.(! empty($this->filters['to_date']) ? $this->filters['to_date'] : 'Now')],
            [''],
            ['KEY METRICS', 'VALUE'],
            ['Total Orders', $totalOrders],
            ['Total Sales / Gross Revenue (INR)', number_format($totalGross, 2)],
            ['Subtotal (INR)', number_format($totalSubtotal, 2)],
            ['Total Tax / GST (INR)', number_format($totalTax, 2)],
            ['Total Shipping Charges (INR)', number_format($totalShipping, 2)],
            ['Total Discounts (INR)', number_format($totalDiscount, 2)],
            ['Total NB Coins Redeemed', number_format($totalCoinsRedeemed).' coins (INR '.number_format($totalCoinDiscount, 2).')'],
            [''],
            ['ORDER STATUS BREAKDOWN', 'COUNT', 'TOTAL VALUE (INR)'],
        ];

        foreach ($statusCounts as $st => $data) {
            $rows[] = [
                ucfirst($st),
                $data['count'],
                number_format($data['total'], 2),
            ];
        }

        $rows[] = [''];
        $rows[] = ['PAYMENT METHOD BREAKDOWN', 'COUNT', 'TOTAL VALUE (INR)'];

        foreach ($paymentCounts as $method => $data) {
            $rows[] = [
                strtoupper($method),
                $data['count'],
                number_format($data['total'], 2),
            ];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            6 => ['font' => ['bold' => true]],
            15 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Orders Summary';
    }
}
