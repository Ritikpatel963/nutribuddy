<?php

namespace App\Services\Admin\Dashboard;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderReturn;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class DashboardAnalyticsService
{
    public function build(): array
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
                $ageBuckets->put('Not set', $ageBuckets->get('Not set', 0) + 1);
                return;
            }
            $age = Carbon::parse($user->dob)->age;
            $bucket = $age < 18 ? 'Under 18' : ($age <= 24 ? '18-24' : ($age <= 34 ? '25-34' : ($age <= 44 ? '35-44' : '45+')));
            $ageBuckets->put($bucket, $ageBuckets->get($bucket, 0) + 1);
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

}
