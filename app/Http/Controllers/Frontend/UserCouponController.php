<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CouponUsage;

class UserCouponController extends Controller
{
    public function index()
    {
        $couponUsages = CouponUsage::with(['coupon', 'order'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('pages.user-panel.my-coupons', compact('couponUsages'));
    }
}
