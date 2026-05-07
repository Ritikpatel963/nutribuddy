@extends('layout.layout')
@php
    $title = 'Nutribuddy Admin Analytics';
    $subTitle = 'Performance Dashboard';

    $chartPayload = [
        'labels' => $revenueChart['labels'] ?? [],
        'revenue' => $revenueChart['revenue'] ?? [],
        'expense' => $revenueChart['expense'] ?? [],
        'orders' => $revenueChart['orders'] ?? [],
    ];

    $script = '<script>window.dashboardAnalyticsData = ' . json_encode($chartPayload) . ';</script>';
    $script .= '<script>
document.addEventListener("DOMContentLoaded", function () {
  var data = window.dashboardAnalyticsData || {labels: [], revenue: [], expense: [], orders: []};
  
  // Revenue & Expense Chart
  var revEl = document.querySelector("#revenueStatusChart");
  if (revEl && typeof ApexCharts !== "undefined") {
    var options = {
        series: [
            { name: "Revenue", data: data.revenue },
            { name: "Expense", data: data.expense }
        ],
        colors: ["#487FFF", "#FF4D8F"],
        chart: { type: "area", height: 500, toolbar: { show: false }, zoom: { enabled: false } },
        dataLabels: { enabled: false },
        stroke: { curve: "smooth", width: 3 },
        fill: { type: "gradient", gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.1, stops: [0, 90, 100] } },
        grid: { borderColor: "#f1f1f1", strokeDashArray: 3 },
        xaxis: { categories: data.labels },
        yaxis: { labels: { formatter: function (v) { return "₹" + Number(v).toLocaleString(); } } },
        tooltip: { y: { formatter: function (v) { return "₹" + Number(v).toLocaleString(); } } }
    };
    new ApexCharts(revEl, options).render();
  }

  // Order Volume Chart
  var orderEl = document.querySelector("#orderVolumeChart");
  if (orderEl && typeof ApexCharts !== "undefined") {
    var orderOptions = {
        series: [{ name: "Orders", data: data.orders }],
        colors: ["#00BF8F"],
        chart: { type: "bar", height: 200, toolbar: { show: false } },
        plotOptions: { bar: { borderRadius: 6, columnWidth: "40%" } },
        dataLabels: { enabled: false },
        xaxis: { categories: data.labels },
        grid: { borderColor: "#f1f1f1", strokeDashArray: 3 }
    };
    new ApexCharts(orderEl, orderOptions).render();
  }
});
</script>';
@endphp

@section('content')
    <style>
        .stats-card { transition: all 0.3s ease; border: 1px solid #f0f0f0 !important; }
        .stats-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.05) !important; border-color: var(--primary-600) !important; }
        .filter-form { background: #fff; padding: 20px; border-radius: 12px; margin-bottom: 30px; border: 1px solid #eee; display: flex; align-items: flex-end; gap: 20px; flex-wrap: wrap; }
    </style>

    <!-- Filters Section -->
    <form action="" method="GET" class="filter-form">
        <div class="form-group mb-0">
            <label class="form-label fw-bold text-secondary-light text-sm">Start Date</label>
            <input type="date" name="start_date" class="form-control" value="{{ $filters['start_date'] }}">
        </div>
        <div class="form-group mb-0">
            <label class="form-label fw-bold text-secondary-light text-sm">End Date</label>
            <input type="date" name="end_date" class="form-control" value="{{ $filters['end_date'] }}">
        </div>
        <button type="submit" class="btn btn-primary-600 px-30 radius-8">Apply Filter</button>
        <a href="{{ request()->url() }}" class="btn btn-outline-secondary px-20 radius-8">Reset</a>
    </form>

    <div class="row gy-4">
        <!-- Main Chart Section -->
        <div class="col-xxl-8 col-lg-7">
            <div class="card radius-8 border-0 shadow-sm">
                <div class="card-body p-24">
                    <div class="d-flex align-items-center justify-content-between mb-24">
                        <h6 class="mb-0 fw-bold">Financial Performance (Monthly)</h6>
                        <div class="d-flex gap-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="w-12-px h-12-px radius-2 bg-primary-600"></span>
                                <span class="text-xs fw-bold">Revenue</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="w-12-px h-12-px radius-2 bg-pink"></span>
                                <span class="text-xs fw-bold">Refunds</span>
                            </div>
                        </div>
                    </div>
                    <div id="revenueStatusChart"></div>
                </div>
            </div>
        </div>

        <!-- Quick Stats Section -->
        <div class="col-xxl-4 col-lg-5">
            <div class="row gy-4 h-100">
                <div class="col-6">
                    <div class="card h-100 stats-card radius-8">
                        <div class="card-body p-20">
                            <div class="d-flex align-items-center justify-content-between mb-12">
                                <span class="w-40-px h-40-px bg-primary-light text-primary-600 radius-8 d-flex align-items-center justify-content-center">
                                    <iconify-icon icon="solar:cart-large-bold" class="h5 mb-0"></iconify-icon>
                                </span>
                            </div>
                            <h6 class="mb-4 fw-bold">{{ number_format($stats['orders_in_period']) }}</h6>
                            <p class="text-xs text-secondary-light fw-bold mb-0">Orders in Period</p>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card h-100 stats-card radius-8">
                        <div class="card-body p-20">
                            <div class="d-flex align-items-center justify-content-between mb-12">
                                <span class="w-40-px h-40-px bg-success-light text-success-600 radius-8 d-flex align-items-center justify-content-center">
                                    <iconify-icon icon="solar:wad-of-money-bold" class="h5 mb-0"></iconify-icon>
                                </span>
                            </div>
                            <h6 class="mb-4 fw-bold">₹{{ number_format($stats['sales_in_period'], 2) }}</h6>
                            <p class="text-xs text-secondary-light fw-bold mb-0">Revenue in Period</p>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card h-100 stats-card radius-8">
                        <div class="card-body p-20">
                            <div class="d-flex align-items-center justify-content-between mb-12">
                                <span class="w-40-px h-40-px bg-danger-light text-danger-600 radius-8 d-flex align-items-center justify-content-center">
                                    <iconify-icon icon="solar:undo-left-bold" class="h5 mb-0"></iconify-icon>
                                </span>
                            </div>
                            <h6 class="mb-4 fw-bold">{{ number_format($stats['return_count']) }}</h6>
                            <p class="text-xs text-secondary-light fw-bold mb-0">Returns ({{ number_format($stats['return_rate'], 1) }}%)</p>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card h-100 stats-card radius-8">
                        <div class="card-body p-20">
                            <div class="d-flex align-items-center justify-content-between mb-12">
                                <span class="w-40-px h-40-px bg-warning-light text-warning-600 radius-8 d-flex align-items-center justify-content-center">
                                    <iconify-icon icon="solar:users-group-rounded-bold" class="h5 mb-0"></iconify-icon>
                                </span>
                            </div>
                            <h6 class="mb-4 fw-bold">{{ number_format($stats['total_customers']) }}</h6>
                            <p class="text-xs text-secondary-light fw-bold mb-0">Total Customers</p>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card stats-card radius-8">
                        <div class="card-body p-20">
                            <h6 class="text-sm fw-bold mb-12">Order Volume (Monthly)</h6>
                            <div id="orderVolumeChart"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Selling Products Section -->
        <div class="col-xxl-6 col-lg-12">
            <div class="card h-100 radius-8 border-0 shadow-sm">
                <div class="card-body p-24">
                    <div class="d-flex align-items-center justify-content-between mb-20">
                        <h6 class="mb-0 fw-bold">Top Selling Products</h6>
                        <span class="text-xs text-secondary-light fw-semibold">Last 30 Days</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table bordered-table mb-0">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Units Sold</th>
                                    <th>Revenue Generated</th>
                                    <th>Trend</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topSellingProducts as $product)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="text-md fw-bold text-secondary-light">{{ $product->product_name }}</span>
                                        </div>
                                    </td>
                                    <td><span class="fw-bold">{{ $product->total_qty }}</span> units</td>
                                    <td><span class="text-success-600 fw-bold">₹{{ number_format($product->total_revenue, 2) }}</span></td>
                                    <td><span class="bg-success-focus text-success-main px-2 py-1 rounded text-xs fw-bold">Hot 🔥</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders Section -->
        <div class="col-xxl-6 col-lg-12">
            <div class="card h-100 radius-8 border-0 shadow-sm">
                <div class="card-body p-24">
                    <div class="d-flex align-items-center justify-content-between mb-20">
                        <h6 class="mb-0 fw-bold">Recent Orders</h6>
                        <a href="{{ route('admin.ecommerce.orders.index') }}" class="btn btn-sm btn-primary-light radius-8 px-12">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table bordered-table mb-0">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentOrders as $order)
                                    @php
                                        $status = strtolower((string) $order->status);
                                        $statusClass = match($status) {
                                            'delivered', 'completed' => 'bg-success-focus text-success-main',
                                            'processing', 'shipped' => 'bg-info-focus text-info-main',
                                            'pending' => 'bg-warning-focus text-warning-main',
                                            'cancelled', 'failed' => 'bg-danger-focus text-danger-main',
                                            default => 'bg-secondary-100 text-secondary-600',
                                        };
                                    @endphp
                                    <tr>
                                        <td><a href="{{ route('admin.ecommerce.orders.show', $order) }}" class="fw-bold text-primary-600 hover-text-primary-700">#{{ $order->order_number }}</a></td>
                                        <td>{{ $order->customer_name ?: ($order->user?->name ?? 'Guest') }}</td>
                                        <td>₹{{ number_format((float) $order->grand_total, 2) }}</td>
                                        <td><span class="{{ $statusClass }} px-12 py-2 rounded-pill fw-bold text-xs">{{ ucfirst($status) }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
