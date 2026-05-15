<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Coupon;

class UserCouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('user_id')
                    ->orWhere('user_id', auth()->id());
            })
            ->latest()
            ->get();

        return view('pages.user-panel.my-coupons', compact('coupons'));
    }
}
