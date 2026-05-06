<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CoinTransaction;
use App\Models\Setting;
use Illuminate\Http\Request;

class LoyaltyController extends Controller
{
    public function settings()
    {
        $settings = [
            'loyalty_conversion_rate' => Setting::get('loyalty_conversion_rate', 10),
            'loyalty_max_redemption_percent' => Setting::get('loyalty_max_redemption_percent', 30),
            'loyalty_enabled' => Setting::get('loyalty_enabled', 1),
        ];

        return view('admin.ecommerce.loyalty.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'loyalty_conversion_rate' => 'required|integer|min:1',
            'loyalty_max_redemption_percent' => 'required|integer|min:0|max:100',
            'loyalty_enabled' => 'required|boolean',
        ]);

        Setting::set('loyalty_conversion_rate', $request->loyalty_conversion_rate);
        Setting::set('loyalty_max_redemption_percent', $request->loyalty_max_redemption_percent);
        Setting::set('loyalty_enabled', $request->loyalty_enabled);

        return back()->with('success', 'Loyalty settings updated successfully.');
    }

    public function transactions()
    {
        $transactions = CoinTransaction::with(['user', 'order'])->latest()->paginate(20);
        return view('admin.ecommerce.loyalty.transactions', compact('transactions'));
    }
}
