<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\Admin\Dashboard\DashboardAnalyticsService;
use App\Services\Admin\Dashboard\DashboardOverviewService;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __construct(private readonly DashboardOverviewService $overview, private readonly DashboardAnalyticsService $analytics) {}

    public function index(): View
    {
        return view('dashboard/index3', $this->overview->stats());
    }

    public function analytics(): View
    {
        return view('dashboard.analytics', $this->analytics->build());
    }

}
