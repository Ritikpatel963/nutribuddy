@extends('layout.layout')
@php
    $title = 'Nutribuddy Admin';
    $subTitle = 'Admin Panel';

    $chartPayload = [
        'labels' => $revenueChart['labels'] ?? [],
        'revenue' => $revenueChart['revenue'] ?? [],
        'expense' => $revenueChart['expense'] ?? [],
    ];

    $script = '<script>window.dashboardRevenueChartData = ' . json_encode($chartPayload) . ';</script>';
    $script .= '<script>\n'
        . 'document.addEventListener("DOMContentLoaded", function () {\n'
        . '  var el = document.querySelector("#paymentStatusChart");\n'
        . '  if (!el || typeof ApexCharts === "undefined") return;\n'
        . '  var data = window.dashboardRevenueChartData || {labels: [], revenue: [], expense: []};\n'
        . '  var options = {\n'
        . '    series: [\n'
        . '      { name: "Revenue", data: data.revenue },\n'
        . '      { name: "Expense", data: data.expense }\n'
        . '    ],\n'
        . '    colors: ["#487FFF", "#FF9F29"],\n'
        . '    chart: { type: "bar", height: 250, toolbar: { show: false } },\n'
        . '    grid: { show: true, borderColor: "#D1D5DB", strokeDashArray: 4, position: "back" },\n'
        . '    plotOptions: { bar: { borderRadius: 4, columnWidth: 10 } },\n'
        . '    dataLabels: { enabled: false },\n'
        . '    stroke: { show: true, width: 2, colors: ["transparent"] },\n'
        . '    xaxis: { categories: data.labels },\n'
        . '    yaxis: { labels: { formatter: function (value) { return "INR " + Number(value).toLocaleString(); } } },\n'
        . '    tooltip: { y: { formatter: function (value) { return "INR " + Number(value).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}); } } }\n'
        . '  };\n'
        . '  new ApexCharts(el, options).render();\n'
        . '});\n'
        . '</script>';
@endphp

@section('content')
    <div class="row gy-4">
        <div class="col-xxl-12">
            <div class="card radius-8 border-0">
                <div class="row">
                    <div class="col-xxl-6 pe-xxl-0">
                        <div class="card-body p-24">
                            <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                                <h6 class="mb-2 fw-bold text-lg">Revenue Report</h6>
                            </div>
                            <ul class="d-flex flex-wrap align-items-center mt-3 gap-3">
                                <li class="d-flex align-items-center gap-2">
                                    <span class="w-12-px h-12-px radius-2 bg-primary-600"></span>
                                    <span class="text-secondary-light text-sm fw-semibold">Earning:
                                        <span class="text-primary-light fw-bold">INR {{ number_format((float) ($stats['total_sales'] ?? 0), 2) }}</span>
                                    </span>
                                </li>
                                <li class="d-flex align-items-center gap-2">
                                    <span class="w-12-px h-12-px radius-2 bg-yellow"></span>
                                    <span class="text-secondary-light text-sm fw-semibold">Expense:
                                        <span class="text-primary-light fw-bold">INR {{ number_format((float) ($stats['total_expense'] ?? 0), 2) }}</span>
                                    </span>
                                </li>
                            </ul>
                            <div class="mt-40">
                                <div id="paymentStatusChart" class="margin-16-minus"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-6">
                        <div class="row h-100 g-0">
                            <div class="col-6 p-0 m-0">
                                <div class="card-body p-24 h-100 d-flex flex-column justify-content-center border border-top-0">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                                        <div>
                                            <span class="mb-12 w-44-px h-44-px text-primary-600 bg-primary-light border border-primary-light-white flex-shrink-0 d-flex justify-content-center align-items-center radius-8 h6 mb-12">
                                                <iconify-icon icon="fa-solid:box-open" class="icon"></iconify-icon>
                                            </span>
                                            <span class="mb-1 fw-medium text-secondary-light text-md">Total Products</span>
                                            <h6 class="fw-semibold text-primary-light mb-1">{{ number_format($stats['total_products'] ?? 0) }}</h6>
                                        </div>
                                    </div>
                                    <p class="text-sm mb-0">Added this week: <span class="bg-success-focus px-1 rounded-2 fw-medium text-success-main text-sm">+{{ number_format($stats['products_this_week'] ?? 0) }}</span></p>
                                </div>
                            </div>
                            <div class="col-6 p-0 m-0">
                                <div class="card-body p-24 h-100 d-flex flex-column justify-content-center border border-top-0 border-start-0 border-end-0">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                                        <div>
                                            <span class="mb-12 w-44-px h-44-px text-yellow bg-yellow-light border border-yellow-light-white flex-shrink-0 d-flex justify-content-center align-items-center radius-8 h6 mb-12">
                                                <iconify-icon icon="flowbite:users-group-solid" class="icon"></iconify-icon>
                                            </span>
                                            <span class="mb-1 fw-medium text-secondary-light text-md">Total Customer</span>
                                            <h6 class="fw-semibold text-primary-light mb-1">{{ number_format($stats['total_customers'] ?? 0) }}</h6>
                                        </div>
                                    </div>
                                    <p class="text-sm mb-0">Added this week: <span class="bg-success-focus px-1 rounded-2 fw-medium text-success-main text-sm">+{{ number_format($stats['customers_this_week'] ?? 0) }}</span></p>
                                </div>
                            </div>
                            <div class="col-6 p-0 m-0">
                                <div class="card-body p-24 h-100 d-flex flex-column justify-content-center border border-top-0 border-bottom-0">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                                        <div>
                                            <span class="mb-12 w-44-px h-44-px text-lilac bg-lilac-light border border-lilac-light-white flex-shrink-0 d-flex justify-content-center align-items-center radius-8 h6 mb-12">
                                                <iconify-icon icon="majesticons:shopping-cart" class="icon"></iconify-icon>
                                            </span>
                                            <span class="mb-1 fw-medium text-secondary-light text-md">Total Orders</span>
                                            <h6 class="fw-semibold text-primary-light mb-1">{{ number_format($stats['total_orders'] ?? 0) }}</h6>
                                        </div>
                                    </div>
                                    <p class="text-sm mb-0">Added this week: <span class="bg-success-focus px-1 rounded-2 fw-medium text-success-main text-sm">+{{ number_format($stats['orders_this_week'] ?? 0) }}</span></p>
                                </div>
                            </div>
                            <div class="col-6 p-0 m-0">
                                <div class="card-body p-24 h-100 d-flex flex-column justify-content-center border border-top-0 border-start-0 border-end-0 border-bottom-0">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                                        <div>
                                            <span class="mb-12 w-44-px h-44-px text-pink bg-pink-light border border-pink-light-white flex-shrink-0 d-flex justify-content-center align-items-center radius-8 h6 mb-12">
                                                <iconify-icon icon="ri:discount-percent-fill" class="icon"></iconify-icon>
                                            </span>
                                            <span class="mb-1 fw-medium text-secondary-light text-md">Total Sales</span>
                                            <h6 class="fw-semibold text-primary-light mb-1">INR {{ number_format((float) ($stats['total_sales'] ?? 0), 2) }}</h6>
                                        </div>
                                    </div>
                                    <p class="text-sm mb-0">This week sales: <span class="bg-success-focus px-1 rounded-2 fw-medium text-success-main text-sm">INR {{ number_format((float) ($stats['sales_this_week'] ?? 0), 2) }}</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-12 col-lg-12">
            <div class="card h-100">
                <div class="card-body p-24">
                    <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between mb-20">
                        <h6 class="mb-2 fw-bold text-lg mb-0">Recent Orders</h6>
                        <a href="{{ route('admin.ecommerce.orders.index') }}" class="text-primary-600 hover-text-primary d-flex align-items-center gap-1">
                            View All
                            <iconify-icon icon="solar:alt-arrow-right-linear" class="icon"></iconify-icon>
                        </a>
                    </div>
                    <div class="table-responsive scroll-sm">
                        <table class="table bordered-table mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">Users</th>
                                    <th scope="col">Invoice</th>
                                    <th scope="col">Items</th>
                                    <th scope="col">Qty</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col" class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentOrders as $order)
                                    @php
                                        $firstItem = $order->items->first();
                                        $itemCount = $order->items->count();
                                        $itemLabel = $firstItem?->product_name ?? 'N/A';
                                        if ($itemCount > 1) {
                                            $itemLabel .= ' +' . ($itemCount - 1) . ' more';
                                        }
                                        $totalQty = $order->items->sum('quantity');
                                        $status = strtolower((string) $order->status);
                                        $statusClass = match($status) {
                                            'delivered', 'completed' => 'bg-success-focus text-success-main',
                                            'processing', 'shipped' => 'bg-info-focus text-info-main',
                                            'pending' => 'bg-warning-focus text-warning-main',
                                            'cancelled', 'canceled', 'failed' => 'bg-danger-focus text-danger-main',
                                            default => 'bg-secondary-100 text-secondary-600',
                                        };
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ asset('assets/images/users/user1.png') }}" alt=""
                                                    class="flex-shrink-0 me-12 radius-8">
                                                <span class="text-lg text-secondary-light fw-semibold flex-grow-1">{{ $order->customer_name ?: ($order->user?->name ?? 'Guest') }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $order->order_number }}</td>
                                        <td>{{ $itemLabel }}</td>
                                        <td>{{ $totalQty }}</td>
                                        <td>INR {{ number_format((float) $order->grand_total, 2) }}</td>
                                        <td class="text-center">
                                            <span class="{{ $statusClass }} px-24 py-4 rounded-pill fw-medium text-sm">{{ ucfirst($status) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-secondary-light py-4">No orders found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
