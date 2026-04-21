<?php

namespace App\Http\Controllers;

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
        $weekStart = now()->startOfWeek();
        $customerScope = fn ($query) => $query->where(function ($userQuery) {
            $userQuery->whereNull('role')->orWhere('role', '!=', 'admin');
        });

        $totalProducts = Product::count();
        $productsThisWeek = Product::where('created_at', '>=', $weekStart)->count();

        $totalCustomers = User::where($customerScope)->count();
        $customersThisWeek = User::where($customerScope)->where('created_at', '>=', $weekStart)->count();

        $totalOrders = Order::count();
        $ordersThisWeek = Order::where('created_at', '>=', $weekStart)->count();

        $totalSales = (float) Order::where('payment_status', 'paid')->sum('grand_total');
        $salesThisWeek = (float) Order::where('payment_status', 'paid')->where('created_at', '>=', $weekStart)->sum('grand_total');

        $totalExpense = (float) OrderReturn::whereIn('status', ['approved', 'completed'])->sum('refund_amount');
        $expenseThisWeek = (float) OrderReturn::whereIn('status', ['approved', 'completed'])->where('created_at', '>=', $weekStart)->sum('refund_amount');

        $months = collect(range(11, 0))->map(fn ($offset) => now()->copy()->subMonths($offset));
        $revenueLabels = $months->map(fn (Carbon $month) => $month->format('M'))->values()->all();
        $revenueSeries = $months->map(function (Carbon $month) {
            return (float) Order::where('payment_status', 'paid')
                ->whereBetween('created_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
                ->sum('grand_total');
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
                'orders_this_week' => $ordersThisWeek,
                'total_sales' => $totalSales,
                'sales_this_week' => $salesThisWeek,
                'total_expense' => $totalExpense,
                'expense_this_week' => $expenseThisWeek,
            ],
            'revenueChart' => [
                'labels' => $revenueLabels,
                'revenue' => $revenueSeries,
                'expense' => $expenseSeries,
            ],
            'recentOrders' => $recentOrders,
        ];
    }
}
