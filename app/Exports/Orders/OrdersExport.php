<?php

namespace App\Exports\Orders;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class OrdersExport implements WithMultipleSheets
{
    public function __construct(
        private readonly Collection $orders,
        private readonly array $filters = []
    ) {}

    public function sheets(): array
    {
        return [
            new OrdersSummarySheet($this->orders, $this->filters),
            new OrdersDataSheet($this->orders),
        ];
    }
}
