<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Order;
use App\Models\OrderReturn;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('dashboard/index3', $this->dashboardStats());
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
        $request = request();
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date'))->startOfDay() : now()->subDays(30)->startOfDay();
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date'))->endOfDay() : now()->endOfDay();

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

        $totalExpense = (float) OrderReturn::whereIn('status', ['approved', 'completed'])->sum('refund_amount');
        $expenseInPeriod = (float) OrderReturn::whereIn('status', ['approved', 'completed'])->whereBetween('created_at', [$startDate, $endDate])->sum('refund_amount');
        $expenseThisWeek = (float) OrderReturn::whereIn('status', ['approved', 'completed'])->where('created_at', '>=', $weekStart)->sum('refund_amount');

        // Top Selling Products
        $topSellingProducts = \App\Models\OrderItem::select('product_id', 'product_name', \Illuminate\Support\Facades\DB::raw('SUM(quantity) as total_qty'), \Illuminate\Support\Facades\DB::raw('SUM(line_total) as total_revenue'))
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

        // Charts data (Month-wise)
        $months = collect(range(11, 0))->map(fn ($offset) => now()->copy()->subMonths($offset));
        $revenueLabels = $months->map(fn (Carbon $month) => $month->format('M'))->values()->all();
        $revenueSeries = $months->map(function (Carbon $month) {
            return (float) Order::where('payment_status', 'paid')
                ->whereBetween('created_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
                ->sum('grand_total');
        })->values()->all();
        
        $orderCountSeries = $months->map(function (Carbon $month) {
            return Order::whereBetween('created_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])->count();
        })->values()->all();

        $expenseSeries = $months->map(function (Carbon $month) {
            return (float) OrderReturn::whereIn('status', ['approved', 'completed'])
                ->whereBetween('created_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
                ->sum('refund_amount');
        })->values()->all();

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
                'total_expense' => $totalExpense,
                'expense_in_period' => $expenseInPeriod,
                'expense_this_week' => $expenseThisWeek,
                'return_count' => $returnCount,
                'return_rate' => $returnRate,
            ],
            'revenueChart' => [
                'labels' => $revenueLabels,
                'revenue' => $revenueSeries,
                'expense' => $expenseSeries,
                'orders' => $orderCountSeries,
            ],
            'topSellingProducts' => $topSellingProducts,
            'recentOrders' => $recentOrders,
            'filters' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
        ];
    }
}
