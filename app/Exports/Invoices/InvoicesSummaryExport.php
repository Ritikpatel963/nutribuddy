<?php

namespace App\Exports\Invoices;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class InvoicesSummaryExport implements WithMultipleSheets
{
    public function __construct(
        private readonly Collection $orders,
        private readonly array $filters = []
    ) {}

    public function sheets(): array
    {
        return [
            new InvoicesSummarySheet($this->orders, $this->filters),
            new InvoicesRegisterSheet($this->orders),
        ];
    }
}
