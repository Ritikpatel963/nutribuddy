<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderReturn;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('dashboard/index3', $this->dashboardStats());
    }

    public function analytics(): View
    {
        return view('dashboard.analytics', $this->analyticsStats());
    }

    public function export(): BinaryFileResponse
    {
        [$startDate, $endDate, $period, $compare, $hasCustomRange] = $this->dashboardDateRange();
        $stats = $this->dashboardStats();
        $filename = 'nutribuddy-dashboard-' . $startDate->format('Y-m-d') . '-to-' . $endDate->format('Y-m-d') . '.xlsx';
        $path = storage_path('app/' . uniqid('dashboard-export-', true) . '.xlsx');
        $sheets = $this->dashboardExportSheets($stats, $startDate, $endDate, $period, $compare, $hasCustomRange);

        $this->writeXlsx($path, $sheets);

        return response()->download($path, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    private function dashboardExportSheets(array $stats, Carbon $startDate, Carbon $endDate, int $period, string $compare, bool $hasCustomRange): array
    {
        $rangeLabel = $startDate->toDateString() . ' to ' . $endDate->toDateString();
        $filterType = $hasCustomRange ? 'Custom date range' : $period . ' days';
        $compareLabel = $compare === 'last_year' ? 'Last year' : 'Previous period';
        $chart = $stats['revenueChart'];
        $deltas = $stats['deltas'];
        $data = $stats['stats'];

        $overview = [
            ['NutriBuddy Dashboard Export'],
            ['Date Range', $rangeLabel],
            ['Filter Type', $filterType],
            ['Compare', $compareLabel],
            ['Generated At', now()->format('Y-m-d H:i:s')],
            [],
            ['Dashboard Card', 'Main Value', 'Change / Detail'],
            ['Revenue', (float) $data['sales_in_period'], number_format((float) $deltas['sales'], 1) . '%'],
            ['Orders', (int) $data['orders_in_period'], number_format((float) $deltas['orders'], 1) . '%'],
            ['Average Order Value', (float) $data['average_order_value'], number_format((float) $deltas['average_order_value'], 1) . '%'],
            ['Active Subscriptions', (int) $data['active_subscriptions'], 'Module not connected'],
            ['Returns', (int) $data['return_count'], number_format((float) $data['return_rate'], 2) . '% return rate'],
            ['Refund Expense', (float) $data['expense_in_period'], 'Approved and completed returns'],
        ];

        $periodRows = collect($chart['labels'])->map(fn ($label, $index) => [
            $label,
            (float) ($chart['revenue'][$index] ?? 0),
            (int) ($chart['orders'][$index] ?? 0),
            (float) ($chart['aov'][$index] ?? 0),
            (float) ($chart['expense'][$index] ?? 0),
        ])->values()->all();

        $statusRows = Order::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw("COALESCE(NULLIF(status, ''), 'unknown') as status_label, COUNT(*) as orders, SUM(grand_total) as total")
            ->groupBy('status_label')
            ->orderByDesc('orders')
            ->get()
            ->map(fn ($row) => [ucfirst((string) $row->status_label), (int) $row->orders, (float) $row->total])
            ->values()
            ->all();

        $paymentRows = Order::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw("COALESCE(NULLIF(payment_status, ''), 'unknown') as payment_label, COUNT(*) as orders, SUM(grand_total) as total")
            ->groupBy('payment_label')
            ->orderByDesc('orders')
            ->get()
            ->map(fn ($row) => [ucfirst((string) $row->payment_label), (int) $row->orders, (float) $row->total])
            ->values()
            ->all();

        $orderDetailRows = [];
        Order::with('items')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at')
            ->chunk(200, function ($orders) use (&$orderDetailRows) {
                foreach ($orders as $order) {
                    $items = $order->items
                        ->map(fn ($item) => $item->product_name . ' x ' . $item->quantity)
                        ->implode('; ');

                    $orderDetailRows[] = [
                        $order->order_number,
                        optional($order->created_at)->format('Y-m-d H:i'),
                        $order->customer_name,
                        $order->customer_email,
                        $order->customer_phone,
                        $order->shipping_city,
                        $order->shipping_state,
                        $order->status,
                        $order->payment_status,
                        $order->payment_method,
                        $items,
                        (float) $order->subtotal,
                        (float) $order->discount_total,
                        (float) $order->shipping_total,
                        (float) ($order->gst_total ?: $order->tax_total),
                        (float) $order->grand_total,
                    ];
                }
            });

        return [
            'Overview' => $overview,
            'Revenue Card' => array_merge([
                ['Revenue Card'],
                ['Date Range', $rangeLabel],
                ['Total Revenue', (float) $data['sales_in_period']],
                ['Revenue Change', number_format((float) $deltas['sales'], 1) . '%'],
                ['Total Paid Sales All Time', (float) $data['total_sales']],
                [],
                ['Period', 'Revenue', 'Orders', 'Average Order Value', 'Refund Expense'],
            ], $periodRows),
            'Orders Card' => array_merge([
                ['Orders Card'],
                ['Date Range', $rangeLabel],
                ['Orders In Period', (int) $data['orders_in_period']],
                ['Orders Change', number_format((float) $deltas['orders'], 1) . '%'],
                ['Total Orders All Time', (int) $data['total_orders']],
                [],
                ['Status', 'Orders', 'Grand Total'],
            ], $statusRows, [
                [],
                ['Payment Status', 'Orders', 'Grand Total'],
            ], $paymentRows),
            'AOV Card' => array_merge([
                ['Average Order Value Card'],
                ['Date Range', $rangeLabel],
                ['Average Order Value', (float) $data['average_order_value']],
                ['AOV Change', number_format((float) $deltas['average_order_value'], 1) . '%'],
                [],
                ['Period', 'Revenue', 'Orders', 'Average Order Value', 'Refund Expense'],
            ], $periodRows),
            'Subscriptions Card' => [
                ['Active Subscriptions Card'],
                ['Date Range', $rangeLabel],
                ['Active Subscriptions', (int) $data['active_subscriptions']],
                ['Status', 'Module not connected'],
            ],
            'Snapshot Card' => [
                ["Today's Snapshot Card"],
                ['Metric', 'Value'],
                ['Orders Today', (int) $stats['snapshot']['orders_today']],
                ['Revenue Today', (float) $stats['snapshot']['revenue_today']],
                ['Pending Fulfillment', (int) $stats['snapshot']['pending_fulfillment']],
                ['In Transit', (int) $stats['snapshot']['in_transit']],
                ['Returns To Process', (int) $stats['snapshot']['returns_to_process']],
                ['Reviews Awaiting Reply', (int) $stats['snapshot']['reviews_awaiting_reply']],
            ],
            'Top Products Card' => array_merge([
                ['Top Products Card'],
                ['Date Range', $rangeLabel],
                ['Product', 'Quantity Sold', 'Revenue'],
            ], collect($stats['topSellingProducts'])->map(fn ($product) => [
                $product->product_name,
                (int) $product->total_qty,
                (float) $product->total_revenue,
            ])->values()->all()),
            'Sales By Zone Card' => array_merge([
                ['Sales By Zone Card'],
                ['Date Range', $rangeLabel],
                ['Zone', 'Revenue', 'Percent'],
            ], collect($stats['zoneSales'])->map(fn ($zone) => [
                $zone['zone'],
                (float) $zone['total'],
                (float) $zone['percent'],
            ])->values()->all()),
            'Recent Activity Card' => array_merge([
                ['Recent Activity Card'],
                ['Date', 'Customer', 'Activity', 'Amount'],
            ], collect($stats['recentOrders'])->take(8)->map(fn ($order) => [
                optional($order->created_at)->format('Y-m-d H:i'),
                $order->customer_name ?: ($order->user?->name ?? 'Customer'),
                'Placed an order',
                (float) $order->grand_total,
            ])->values()->all()),
            'Order Details' => array_merge([
                ['Order Details'],
                ['Date Range', $rangeLabel],
                [],
                ['Order No', 'Date', 'Customer', 'Email', 'Phone', 'City', 'State', 'Status', 'Payment Status', 'Payment Method', 'Items', 'Subtotal', 'Discount', 'Shipping', 'Tax/GST', 'Grand Total'],
            ], $orderDetailRows),
        ];
    }

    private function writeXlsx(string $path, array $sheets): void
    {
        $zip = new ZipArchive();
        $zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $zip->addFromString('[Content_Types].xml', $this->xlsxContentTypes(count($sheets)));
        $zip->addFromString('_rels/.rels', $this->xlsxRootRels());
        $zip->addFromString('xl/workbook.xml', $this->xlsxWorkbook(array_keys($sheets)));
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->xlsxWorkbookRels(count($sheets)));
        $zip->addFromString('xl/styles.xml', $this->xlsxStyles());

        $index = 1;
        foreach ($sheets as $rows) {
            $zip->addFromString("xl/worksheets/sheet{$index}.xml", $this->xlsxWorksheet($rows));
            $index++;
        }

        $zip->close();
    }

    private function xlsxWorksheet(array $rows): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<sheetViews><sheetView workbookViewId="0"/></sheetViews>'
            . '<sheetFormatPr defaultRowHeight="18"/>'
            . '<cols>';

        for ($column = 1; $column <= 16; $column++) {
            $width = $column === 1 ? 24 : 18;
            $xml .= '<col min="' . $column . '" max="' . $column . '" width="' . $width . '" customWidth="1"/>';
        }

        $xml .= '</cols><sheetData>';

        foreach ($rows as $rowIndex => $row) {
            $excelRow = $rowIndex + 1;
            $height = $rowIndex === 0 ? ' ht="24" customHeight="1"' : '';
            $xml .= '<row r="' . $excelRow . '"' . $height . '>';

            foreach (array_values($row) as $columnIndex => $value) {
                $cell = $this->xlsxColumnName($columnIndex + 1) . $excelRow;
                $style = $rowIndex === 0 ? ' s="1"' : ($this->xlsxLooksLikeHeader($row) ? ' s="2"' : '');

                if (is_int($value) || is_float($value)) {
                    $xml .= '<c r="' . $cell . '"' . $style . '><v>' . $value . '</v></c>';
                } else {
                    $xml .= '<c r="' . $cell . '" t="inlineStr"' . $style . '><is><t>' . $this->xml((string) $value) . '</t></is></c>';
                }
            }

            $xml .= '</row>';
        }

        return $xml . '</sheetData><pageMargins left="0.7" right="0.7" top="0.75" bottom="0.75" header="0.3" footer="0.3"/></worksheet>';
    }

    private function xlsxLooksLikeHeader(array $row): bool
    {
        return count($row) > 1 && collect($row)->every(fn ($cell) => is_string($cell) && $cell !== '');
    }

    private function xlsxColumnName(int $index): string
    {
        $name = '';
        while ($index > 0) {
            $index--;
            $name = chr(65 + ($index % 26)) . $name;
            $index = intdiv($index, 26);
        }

        return $name;
    }

    private function xlsxWorkbook(array $sheetNames): string
    {
        $sheets = '';
        foreach ($sheetNames as $index => $sheetName) {
            $sheetId = $index + 1;
            $sheets .= '<sheet name="' . $this->xml($sheetName) . '" sheetId="' . $sheetId . '" r:id="rId' . $sheetId . '"/>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets>' . $sheets . '</sheets></workbook>';
    }

    private function xlsxWorkbookRels(int $sheetCount): string
    {
        $rels = '';
        for ($index = 1; $index <= $sheetCount; $index++) {
            $rels .= '<Relationship Id="rId' . $index . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet' . $index . '.xml"/>';
        }

        $rels .= '<Relationship Id="rId' . ($sheetCount + 1) . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>';

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' . $rels . '</Relationships>';
    }

    private function xlsxContentTypes(int $sheetCount): string
    {
        $worksheets = '';
        for ($index = 1; $index <= $sheetCount; $index++) {
            $worksheets .= '<Override PartName="/xl/worksheets/sheet' . $index . '.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . $worksheets
            . '</Types>';
    }

    private function xlsxRootRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';
    }

    private function xlsxStyles(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<fonts count="3"><font><sz val="11"/><name val="Calibri"/></font><font><b/><sz val="14"/><name val="Calibri"/></font><font><b/><sz val="11"/><name val="Calibri"/></font></fonts>'
            . '<fills count="3"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill><fill><patternFill patternType="solid"><fgColor rgb="FFE8F3E5"/><bgColor indexed="64"/></patternFill></fill></fills>'
            . '<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="3"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/><xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0"/><xf numFmtId="0" fontId="2" fillId="2" borderId="0" xfId="0"/></cellXfs>'
            . '</styleSheet>';
    }

    private function xml(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }
    
    public function index2()
    {
        return view('dashboard/index2');
    }
    
    public function index3()
    {
        return view('dashboard/index3', $this->dashboardStats());
    }
    
    public function index4()
    {
        return view('dashboard/index4');
    }
    
    public function index5()
    {
        return view('dashboard/index5');
    }
    
    public function index6()
    {
        return view('dashboard/index6');
    }
    
    public function index7()
    {
        return view('dashboard/index7');
    }
    
    public function index8()
    {
        return view('dashboard/index8');
    }
    
    public function index9()
    {
        return view('dashboard/index9');
    }
    
    public function index10()
    {
        return view('dashboard/index10');
    }

    private function dashboardStats(): array
    {
        [$startDate, $endDate, $period, $compare, $hasCustomRange] = $this->dashboardDateRange();

        $previousStartDate = $compare === 'last_year'
            ? $startDate->copy()->subYear()
            : $startDate->copy()->subDays($period);
        $previousEndDate = $compare === 'last_year'
            ? $endDate->copy()->subYear()
            : $startDate->copy()->subSecond();

        $weekStart = now()->startOfWeek();
        $customerScope = fn ($query) => $query->where(function ($userQuery) {
            $userQuery->whereNull('role')->orWhere('role', '!=', 'admin');
        });

        // Basic Stats
        $totalProducts = Product::count();
        $productsThisWeek = Product::where('created_at', '>=', $weekStart)->count();

        $totalCustomers = User::where($customerScope)->count();
        $customersThisWeek = User::where($customerScope)->where('created_at', '>=', $weekStart)->count();

        $totalOrders = Order::count();
        $ordersInPeriod = Order::whereBetween('created_at', [$startDate, $endDate])->count();
        $ordersThisWeek = Order::where('created_at', '>=', $weekStart)->count();

        $totalSales = (float) Order::where('payment_status', 'paid')->sum('grand_total');
        $salesInPeriod = (float) Order::where('payment_status', 'paid')->whereBetween('created_at', [$startDate, $endDate])->sum('grand_total');
        $salesThisWeek = (float) Order::where('payment_status', 'paid')->where('created_at', '>=', $weekStart)->sum('grand_total');
        $previousSales = (float) Order::where('payment_status', 'paid')->whereBetween('created_at', [$previousStartDate, $previousEndDate])->sum('grand_total');
        $previousOrders = Order::whereBetween('created_at', [$previousStartDate, $previousEndDate])->count();
        $averageOrderValue = $ordersInPeriod > 0 ? $salesInPeriod / $ordersInPeriod : 0;
        $previousAverageOrderValue = $previousOrders > 0 ? $previousSales / $previousOrders : 0;

        $totalExpense = (float) OrderReturn::whereIn('status', ['approved', 'completed'])->sum('refund_amount');
        $expenseInPeriod = (float) OrderReturn::whereIn('status', ['approved', 'completed'])->whereBetween('created_at', [$startDate, $endDate])->sum('refund_amount');
        $expenseThisWeek = (float) OrderReturn::whereIn('status', ['approved', 'completed'])->where('created_at', '>=', $weekStart)->sum('refund_amount');

        // Top Selling Products
        $topSellingProducts = OrderItem::select('product_id', 'product_name', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(line_total) as total_revenue'))
            ->whereHas('order', function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])->where('payment_status', 'paid');
            })
            ->groupBy('product_id', 'product_name')
            ->orderBy('total_qty', 'desc')
            ->take(5)
            ->get();

        // Return Analysis
        $returnCount = OrderReturn::whereBetween('created_at', [$startDate, $endDate])->count();
        $returnRate = $ordersInPeriod > 0 ? ($returnCount / $ordersInPeriod) * 100 : 0;

        $bucketByMonth = $period > 120;
        $periodBuckets = $bucketByMonth
            ? collect()->range(0, $startDate->copy()->startOfMonth()->diffInMonths($endDate->copy()->startOfMonth()))
                ->map(fn ($offset) => $startDate->copy()->startOfMonth()->addMonths($offset))
            : collect()->range(0, $period - 1)
                ->map(fn ($offset) => $startDate->copy()->addDays($offset)->startOfDay());

        $revenueLabels = $periodBuckets->map(fn (Carbon $bucket) => $bucketByMonth ? $bucket->format('M Y') : $bucket->format('d M'))->values()->all();
        $revenueSeries = $periodBuckets->map(function (Carbon $bucket) use ($bucketByMonth, $startDate, $endDate) {
            $bucketStart = $bucketByMonth ? $bucket->copy()->startOfMonth() : $bucket->copy()->startOfDay();
            $bucketEnd = $bucketByMonth ? $bucket->copy()->endOfMonth() : $bucket->copy()->endOfDay();
            $bucketStart = $bucketStart->lt($startDate) ? $startDate->copy() : $bucketStart;
            $bucketEnd = $bucketEnd->gt($endDate) ? $endDate->copy() : $bucketEnd;

            return (float) Order::where('payment_status', 'paid')
                ->whereBetween('created_at', [$bucketStart, $bucketEnd])
                ->sum('grand_total');
        })->values()->all();
        
        $orderCountSeries = $periodBuckets->map(function (Carbon $bucket) use ($bucketByMonth, $startDate, $endDate) {
            $bucketStart = $bucketByMonth ? $bucket->copy()->startOfMonth() : $bucket->copy()->startOfDay();
            $bucketEnd = $bucketByMonth ? $bucket->copy()->endOfMonth() : $bucket->copy()->endOfDay();
            $bucketStart = $bucketStart->lt($startDate) ? $startDate->copy() : $bucketStart;
            $bucketEnd = $bucketEnd->gt($endDate) ? $endDate->copy() : $bucketEnd;

            return Order::whereBetween('created_at', [$bucketStart, $bucketEnd])->count();
        })->values()->all();

        $expenseSeries = $periodBuckets->map(function (Carbon $bucket) use ($bucketByMonth, $startDate, $endDate) {
            $bucketStart = $bucketByMonth ? $bucket->copy()->startOfMonth() : $bucket->copy()->startOfDay();
            $bucketEnd = $bucketByMonth ? $bucket->copy()->endOfMonth() : $bucket->copy()->endOfDay();
            $bucketStart = $bucketStart->lt($startDate) ? $startDate->copy() : $bucketStart;
            $bucketEnd = $bucketEnd->gt($endDate) ? $endDate->copy() : $bucketEnd;

            return (float) OrderReturn::whereIn('status', ['approved', 'completed'])
                ->whereBetween('created_at', [$bucketStart, $bucketEnd])
                ->sum('refund_amount');
        })->values()->all();

        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();
        $ordersToday = Order::whereBetween('created_at', [$todayStart, $todayEnd])->count();
        $revenueToday = (float) Order::where('payment_status', 'paid')->whereBetween('created_at', [$todayStart, $todayEnd])->sum('grand_total');
        $pendingFulfillment = Order::whereIn('status', ['pending', 'confirmed', 'processing', 'packed'])->count();
        $inTransit = Order::whereIn('status', ['shipped', 'out_for_delivery'])->count();
        $reviewsAwaitingReply = ProductReview::where('is_active', false)->count();
        $returnsToProcess = OrderReturn::where('status', 'pending')->count();

        $zoneSales = $this->zoneSales($startDate, $endDate);
        $recentOrders = Order::with(['items', 'user'])->latest()->take(8)->get();

        return [
            'stats' => [
                'total_products' => $totalProducts,
                'products_this_week' => $productsThisWeek,
                'total_customers' => $totalCustomers,
                'customers_this_week' => $customersThisWeek,
                'total_orders' => $totalOrders,
                'orders_in_period' => $ordersInPeriod,
                'orders_this_week' => $ordersThisWeek,
                'total_sales' => $totalSales,
                'sales_in_period' => $salesInPeriod,
                'sales_this_week' => $salesThisWeek,
                'average_order_value' => $averageOrderValue,
                'total_expense' => $totalExpense,
                'expense_in_period' => $expenseInPeriod,
                'expense_this_week' => $expenseThisWeek,
                'return_count' => $returnCount,
                'return_rate' => $returnRate,
                'active_subscriptions' => 0,
                'subscriptions_this_week' => 0,
            ],
            'deltas' => [
                'sales' => $this->percentChange($salesInPeriod, $previousSales),
                'orders' => $this->percentChange($ordersInPeriod, $previousOrders),
                'average_order_value' => $this->percentChange($averageOrderValue, $previousAverageOrderValue),
                'subscriptions' => 0,
            ],
            'snapshot' => [
                'orders_today' => $ordersToday,
                'revenue_today' => $revenueToday,
                'pending_fulfillment' => $pendingFulfillment,
                'in_transit' => $inTransit,
                'returns_to_process' => $returnsToProcess,
                'reviews_awaiting_reply' => $reviewsAwaitingReply,
            ],
            'revenueChart' => [
                'labels' => $revenueLabels,
                'revenue' => $revenueSeries,
                'expense' => $expenseSeries,
                'orders' => $orderCountSeries,
                'aov' => collect($revenueSeries)->map(function ($revenue, $index) use ($orderCountSeries) {
                    $orders = (int) ($orderCountSeries[$index] ?? 0);
                    return $orders > 0 ? round((float) $revenue / $orders, 2) : 0;
                })->values()->all(),
            ],
            'zoneSales' => $zoneSales,
            'topSellingProducts' => $topSellingProducts,
            'recentOrders' => $recentOrders,
            'filters' => [
                'period' => $period,
                'compare' => $compare,
                'is_custom_range' => $hasCustomRange,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
            ],
        ];
    }

    private function dashboardDateRange(): array
    {
        $request = request();
        $compare = $request->get('compare', 'previous');
        if (! in_array($compare, ['previous', 'last_year'], true)) {
            $compare = 'previous';
        }

        $hasCustomRange = $request->filled('start_date') && $request->filled('end_date');
        if ($hasCustomRange) {
            $startDate = Carbon::parse($request->get('start_date'))->startOfDay();
            $endDate = Carbon::parse($request->get('end_date'))->endOfDay();

            if ($startDate->gt($endDate)) {
                [$startDate, $endDate] = [$endDate->copy()->startOfDay(), $startDate->copy()->endOfDay()];
            }

            $period = $startDate->diffInDays($endDate) + 1;
        } else {
            $period = (int) $request->integer('period', 7);
            if (! in_array($period, [7, 30, 90, 365], true)) {
                $period = 7;
            }

            $endDate = now()->endOfDay();
            $startDate = now()->subDays($period - 1)->startOfDay();
        }

        return [$startDate, $endDate, $period, $compare, $hasCustomRange];
    }

    private function percentChange(float|int $current, float|int $previous): float
    {
        if ((float) $previous === 0.0) {
            return (float) $current > 0 ? 100.0 : 0.0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    private function analyticsStats(): array
    {
        $request = request();
        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->get('start_date'))->startOfDay()
            : now()->startOfMonth();
        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->get('end_date'))->endOfDay()
            : now()->endOfDay();

        if ($startDate->gt($endDate)) {
            [$startDate, $endDate] = [$endDate->copy()->startOfDay(), $startDate->copy()->endOfDay()];
        }

        $productId = $request->filled('product_id') ? (int) $request->get('product_id') : null;
        $segment = $request->get('segment', 'all');
        if (! in_array($segment, ['all', 'new', 'returning', 'subscribers'], true)) {
            $segment = 'all';
        }

        $ordersQuery = $this->analyticsOrdersQuery($startDate, $endDate, $productId, $segment);
        $allOrdersQuery = $this->analyticsOrdersQuery($startDate, $endDate, $productId, $segment, false);
        $orders = (clone $ordersQuery)->select('id', 'user_id', 'customer_name', 'payment_method', 'shipping_city', 'shipping_state', 'grand_total', 'created_at')->get();
        $allOrders = (clone $allOrdersQuery)->select('id', 'user_id', 'customer_name', 'customer_email', 'status', 'fulfillment_status', 'payment_status', 'payment_method', 'shipping_city', 'shipping_state', 'subtotal', 'discount_total', 'shipping_total', 'gst_total', 'grand_total', 'created_at')->get();

        $zoneGroups = $orders->groupBy(fn ($order) => $this->zoneForState($order->shipping_state));
        $zoneGrandTotal = (float) $orders->sum('grand_total');
        $zones = collect(['North', 'South', 'West', 'East'])->map(function ($zone) use ($zoneGroups, $zoneGrandTotal) {
            $rows = $zoneGroups->get($zone, collect());
            $total = (float) $rows->sum('grand_total');
            return [
                'zone' => $zone,
                'key' => strtolower($zone),
                'total' => $total,
                'percent' => $zoneGrandTotal > 0 ? round(($total / $zoneGrandTotal) * 100, 1) : 0,
                'orders' => $rows->count(),
                'states' => match ($zone) {
                    'North' => 'DL - UP - PB - HR',
                    'South' => 'KA - TN - TG - KL',
                    'West' => 'MH - GJ - RJ - GA',
                    default => 'WB - OD - AS - BR',
                },
            ];
        })->values();

        $stateRows = $orders->groupBy(fn ($order) => $order->shipping_state ?: 'Unknown')
            ->map(function ($rows, $state) {
                $revenue = (float) $rows->sum('grand_total');
                $count = $rows->count();
                return [
                    'state' => $state,
                    'zone' => $this->zoneForState($state),
                    'revenue' => $revenue,
                    'orders' => $count,
                    'aov' => $count > 0 ? round($revenue / $count, 2) : 0,
                ];
            })->sortByDesc('revenue')->values();

        $cityRows = $orders->groupBy(fn ($order) => $order->shipping_city ?: 'Unknown')
            ->map(function ($rows, $city) {
                return ['city' => $city, 'revenue' => (float) $rows->sum('grand_total'), 'orders' => $rows->count()];
            })->sortByDesc('revenue')->take(8)->values();

        $userIds = $orders->pluck('user_id')->filter()->unique()->values();
        $genderRows = User::whereIn('id', $userIds)
            ->selectRaw("COALESCE(NULLIF(gender, ''), 'Not set') as label, COUNT(*) as total")
            ->groupBy('label')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($row) => ['label' => ucfirst((string) $row->label), 'total' => (int) $row->total])
            ->values();

        $ageBuckets = collect([
            'Under 18' => 0,
            '18-24' => 0,
            '25-34' => 0,
            '35-44' => 0,
            '45+' => 0,
            'Not set' => 0,
        ]);
        User::whereIn('id', $userIds)->get(['dob'])->each(function ($user) use (&$ageBuckets) {
            if (! $user->dob) {
                $ageBuckets['Not set']++;
                return;
            }
            $age = Carbon::parse($user->dob)->age;
            $bucket = $age < 18 ? 'Under 18' : ($age <= 24 ? '18-24' : ($age <= 34 ? '25-34' : ($age <= 44 ? '35-44' : '45+')));
            $ageBuckets[$bucket]++;
        });

        $channelRows = $orders->groupBy(fn ($order) => strtoupper((string) ($order->payment_method ?: 'Unknown')))
            ->map(fn ($rows, $channel) => ['label' => $channel, 'total' => (float) $rows->sum('grand_total')])
            ->sortByDesc('total')
            ->values();

        $paidRevenue = (float) $orders->sum('grand_total');
        $allOrderCount = $allOrders->count();
        $paidOrderCount = $orders->count();
        $uniqueCustomerCount = $allOrders->pluck('user_id')->filter()->unique()->count();
        $guestOrderCount = $allOrders->whereNull('user_id')->count();
        $totalDiscount = (float) $allOrders->sum('discount_total');
        $totalShipping = (float) $allOrders->sum('shipping_total');
        $averageOrderValue = $paidOrderCount > 0 ? round($paidRevenue / $paidOrderCount, 2) : 0;

        $productRows = OrderItem::select('product_id', 'product_name', DB::raw('SUM(quantity) as quantity'), DB::raw('SUM(line_total) as revenue'), DB::raw('COUNT(DISTINCT order_id) as orders'))
            ->whereHas('order', function ($query) use ($startDate, $endDate, $productId, $segment) {
                $this->applyAnalyticsOrderFilters($query, $startDate, $endDate, $productId, $segment, true);
            })
            ->when($productId, fn ($query) => $query->where('product_id', $productId))
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('revenue')
            ->take(12)
            ->get()
            ->map(fn ($row) => [
                'product' => $row->product_name,
                'orders' => (int) $row->orders,
                'quantity' => (int) $row->quantity,
                'revenue' => (float) $row->revenue,
                'aov' => (int) $row->orders > 0 ? round((float) $row->revenue / (int) $row->orders, 2) : 0,
            ])
            ->values();

        $userRows = $allOrders->groupBy(fn ($order) => $order->user_id ?: 'guest:' . strtolower((string) ($order->customer_email ?: $order->customer_name ?: $order->id)))
            ->map(function ($rows) {
                $paidRows = $rows->where('payment_status', 'paid');
                $revenue = (float) $paidRows->sum('grand_total');
                return [
                    'customer' => $rows->first()->customer_name ?: 'Guest',
                    'email' => $rows->first()->customer_email ?: '-',
                    'orders' => $rows->count(),
                    'paid_orders' => $paidRows->count(),
                    'revenue' => $revenue,
                    'last_order' => $rows->max('created_at') ? Carbon::parse($rows->max('created_at'))->format('d M Y') : '-',
                ];
            })
            ->sortByDesc('revenue')
            ->take(12)
            ->values();

        $orderStatusRows = $allOrders->groupBy(fn ($order) => $order->status ?: 'unknown')
            ->map(fn ($rows, $status) => [
                'status' => ucfirst(str_replace('_', ' ', (string) $status)),
                'orders' => $rows->count(),
                'revenue' => (float) $rows->where('payment_status', 'paid')->sum('grand_total'),
            ])
            ->sortByDesc('orders')
            ->values();

        $paymentStatusRows = $allOrders->groupBy(fn ($order) => $order->payment_status ?: 'unknown')
            ->map(fn ($rows, $status) => [
                'status' => ucfirst(str_replace('_', ' ', (string) $status)),
                'orders' => $rows->count(),
                'amount' => (float) $rows->sum('grand_total'),
            ])
            ->sortByDesc('orders')
            ->values();

        $shipmentRows = $allOrders->groupBy(fn ($order) => $order->fulfillment_status ?: $order->status ?: 'unknown')
            ->map(fn ($rows, $status) => [
                'status' => ucfirst(str_replace('_', ' ', (string) $status)),
                'orders' => $rows->count(),
                'shipping' => (float) $rows->sum('shipping_total'),
                'revenue' => (float) $rows->where('payment_status', 'paid')->sum('grand_total'),
            ])
            ->sortByDesc('orders')
            ->values();

        $returnQuery = OrderReturn::with('order')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereHas('order', function ($query) use ($startDate, $endDate, $productId, $segment) {
                $this->applyAnalyticsOrderFilters($query, $startDate, $endDate, $productId, $segment, false);
            });

        $returns = $returnQuery->get();
        $returnRows = $returns->groupBy(fn ($return) => $return->status ?: 'unknown')
            ->map(fn ($rows, $status) => [
                'status' => ucfirst(str_replace('_', ' ', (string) $status)),
                'returns' => $rows->count(),
                'refund' => (float) $rows->sum('refund_amount'),
            ])
            ->sortByDesc('returns')
            ->values();

        $returnReasonRows = $returns->groupBy(fn ($return) => $return->reason ?: 'Not specified')
            ->map(fn ($rows, $reason) => [
                'reason' => $reason,
                'returns' => $rows->count(),
                'refund' => (float) $rows->sum('refund_amount'),
            ])
            ->sortByDesc('returns')
            ->take(8)
            ->values();

        return [
            'filters' => [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'product_id' => $productId,
                'segment' => $segment,
            ],
            'products' => Product::orderBy('name')->get(['id', 'name']),
            'zones' => $zones,
            'states' => $stateRows,
            'cities' => $cityRows,
            'gender' => $genderRows,
            'ages' => $ageBuckets->map(fn ($total, $label) => ['label' => $label, 'total' => $total])->values(),
            'channels' => $channelRows,
            'cohorts' => $this->cohortRetention($startDate, $endDate, $productId),
            'summary' => [
                'orders' => $allOrderCount,
                'paid_orders' => $paidOrderCount,
                'revenue' => $paidRevenue,
                'average_order_value' => $averageOrderValue,
                'customers' => $uniqueCustomerCount,
                'guest_orders' => $guestOrderCount,
                'discount' => $totalDiscount,
                'shipping' => $totalShipping,
                'returns' => $returns->count(),
                'refunds' => (float) $returns->sum('refund_amount'),
                'return_rate' => $allOrderCount > 0 ? round(($returns->count() / $allOrderCount) * 100, 2) : 0,
            ],
            'productRows' => $productRows,
            'userRows' => $userRows,
            'orderStatusRows' => $orderStatusRows,
            'paymentStatusRows' => $paymentStatusRows,
            'shipmentRows' => $shipmentRows,
            'returnRows' => $returnRows,
            'returnReasonRows' => $returnReasonRows,
        ];
    }

    private function analyticsOrdersQuery(Carbon $startDate, Carbon $endDate, ?int $productId, string $segment, bool $paidOnly = true): Builder
    {
        $query = Order::query();

        $this->applyAnalyticsOrderFilters($query, $startDate, $endDate, $productId, $segment, $paidOnly);

        return $query;
    }

    private function applyAnalyticsOrderFilters(Builder $query, Carbon $startDate, Carbon $endDate, ?int $productId, string $segment, bool $paidOnly = true): void
    {
        $query->whereBetween('created_at', [$startDate, $endDate]);

        if ($paidOnly) {
            $query->where('payment_status', 'paid');
        }

        if ($productId) {
            $query->whereHas('items', fn ($itemQuery) => $itemQuery->where('product_id', $productId));
        }

        if ($segment === 'new') {
            $query->whereDoesntHave('user.orders', fn ($orderQuery) => $orderQuery->where('created_at', '<', $startDate));
        } elseif ($segment === 'returning') {
            $query->whereHas('user.orders', fn ($orderQuery) => $orderQuery->where('created_at', '<', $startDate));
        } elseif ($segment === 'subscribers') {
            $query->whereRaw('1 = 0');
        }
    }

    private function zoneForState(?string $state): string
    {
        $state = strtolower(trim((string) $state));
        $north = ['delhi', 'haryana', 'punjab', 'uttar pradesh', 'rajasthan', 'uttarakhand', 'himachal pradesh', 'jammu', 'kashmir', 'chandigarh'];
        $south = ['karnataka', 'tamil nadu', 'kerala', 'andhra pradesh', 'telangana', 'puducherry'];
        $west = ['maharashtra', 'gujarat', 'goa', 'madhya pradesh', 'chhattisgarh'];

        if (in_array($state, $north, true)) return 'North';
        if (in_array($state, $south, true)) return 'South';
        if (in_array($state, $west, true)) return 'West';
        return 'East';
    }

    private function cohortRetention(Carbon $startDate, Carbon $endDate, ?int $productId): array
    {
        $baseQuery = Order::query()->where('payment_status', 'paid');
        if ($productId) {
            $baseQuery->whereHas('items', fn ($itemQuery) => $itemQuery->where('product_id', $productId));
        }

        return (clone $baseQuery)
            ->whereNotNull('user_id')
            ->whereBetween('created_at', [$startDate->copy()->subMonths(4), $endDate])
            ->select('user_id', DB::raw('MIN(created_at) as first_order_at'))
            ->groupBy('user_id')
            ->get()
            ->groupBy(fn ($row) => Carbon::parse($row->first_order_at)->format('M Y'))
            ->take(5)
            ->map(function ($rows, $cohort) use ($baseQuery) {
                $userIds = $rows->pluck('user_id');
                $start = Carbon::parse('01 ' . $cohort)->startOfMonth();

                $months = collect(range(0, 4))->map(function ($offset) use ($baseQuery, $userIds, $start) {
                    $monthStart = $start->copy()->addMonths($offset)->startOfMonth();
                    $monthEnd = $start->copy()->addMonths($offset)->endOfMonth();
                    $active = (clone $baseQuery)->whereIn('user_id', $userIds)->whereBetween('created_at', [$monthStart, $monthEnd])->distinct('user_id')->count('user_id');
                    return $userIds->count() > 0 ? round(($active / $userIds->count()) * 100) : null;
                })->all();

                return ['cohort' => $cohort, 'months' => $months];
            })->values()->all();
    }

    private function zoneSales(Carbon $startDate, Carbon $endDate): array
    {
        $north = ['delhi', 'haryana', 'punjab', 'uttar pradesh', 'rajasthan', 'uttarakhand', 'himachal pradesh', 'jammu', 'kashmir', 'chandigarh'];
        $south = ['karnataka', 'tamil nadu', 'kerala', 'andhra pradesh', 'telangana', 'puducherry'];
        $west = ['maharashtra', 'gujarat', 'goa', 'madhya pradesh', 'chhattisgarh'];

        $totals = ['North' => 0.0, 'South' => 0.0, 'West' => 0.0, 'East' => 0.0];

        Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('shipping_state', DB::raw('SUM(grand_total) as total'))
            ->groupBy('shipping_state')
            ->get()
            ->each(function ($row) use (&$totals, $north, $south, $west) {
                $state = strtolower((string) $row->shipping_state);
                $zone = 'East';
                if (in_array($state, $north, true)) {
                    $zone = 'North';
                } elseif (in_array($state, $south, true)) {
                    $zone = 'South';
                } elseif (in_array($state, $west, true)) {
                    $zone = 'West';
                }

                $totals[$zone] += (float) $row->total;
            });

        $grandTotal = array_sum($totals);

        return collect($totals)->map(fn ($value, $zone) => [
            'zone' => $zone,
            'total' => $value,
            'percent' => $grandTotal > 0 ? round(($value / $grandTotal) * 100, 1) : 0,
        ])->values()->all();
    }
}
