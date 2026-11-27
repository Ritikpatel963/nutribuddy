<?php

namespace App\Http\Controllers\Frontend\Account;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class UserDashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $recentReviews = $user->reviews()
            ->with('product.primaryImage')
            ->latest()
            ->limit(2)
            ->get();
        $reviewCount = $user->reviews()->count();

        return view('frontend.account.dashboard', compact('recentReviews', 'reviewCount'));
    }
}
