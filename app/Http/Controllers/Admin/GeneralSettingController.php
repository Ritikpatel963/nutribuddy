<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GeneralSettingController extends Controller
{
    public function index(): View
    {
        $settings = [
            'site_name' => Setting::get('site_name', 'NutriBuddy'),
            'meta_description' => Setting::get('meta_description', 'Your Health Partner - Premium Nutrition & Wellness Store'),
            'meta_keywords' => Setting::get('meta_keywords', 'nutrition, wellness, health, supplements'),
            'contact_email' => Setting::get('contact_email', 'admin@nutribuddy.com'),
            'contact_phone' => Setting::get('contact_phone', '+91 9876543210'),
            'address' => Setting::get('address', '123 Health St, Wellness City'),
            'gst_number' => Setting::get('gst_number', 'Not Specified'),
        ];

        return view('admin.settings.general', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'meta_keywords' => ['nullable', 'string'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'gst_number' => ['nullable', 'string', 'max:100'],
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value, 'general');
        }

        return back()->with('success', 'Settings updated successfully.');
    }

    public function paymentGateways(): View
    {
        $settings = [
            'razorpay_enabled' => Setting::get('razorpay_enabled', '0'),
            'razorpay_env' => Setting::get('razorpay_env', 'sandbox'),
            'cashfree_enabled' => Setting::get('cashfree_enabled', '0'),
            'cashfree_env' => Setting::get('cashfree_env', 'sandbox'),
            'cod_enabled' => Setting::get('cod_enabled', '0'),
        ];

        return view('admin.settings.payment_gateways', compact('settings'));
    }

    public function updatePaymentGateways(Request $request): RedirectResponse
    {
        if ($request->has('gateway') && $request->input('gateway') === 'razorpay') {
            $validated = $request->validate([
                'razorpay_env' => ['required', 'in:sandbox,production'],
            ]);
            Setting::set('razorpay_enabled', $request->has('razorpay_enabled') ? '1' : '0', 'payment');
            Setting::set('razorpay_env', $validated['razorpay_env'], 'payment');
        } elseif ($request->has('gateway') && $request->input('gateway') === 'cashfree') {
            $validated = $request->validate([
                'cashfree_env' => ['required', 'in:sandbox,production'],
            ]);
            Setting::set('cashfree_enabled', $request->has('cashfree_enabled') ? '1' : '0', 'payment');
            Setting::set('cashfree_env', $validated['cashfree_env'], 'payment');
        } elseif ($request->has('gateway') && $request->input('gateway') === 'cod') {
            Setting::set('cod_enabled', $request->has('cod_enabled') ? '1' : '0', 'payment');
        } else {
            // General bulk update
            Setting::set('razorpay_enabled', $request->has('razorpay_enabled') ? '1' : '0', 'payment');
            Setting::set('razorpay_env', $request->input('razorpay_env', 'sandbox'), 'payment');
            Setting::set('cashfree_enabled', $request->has('cashfree_enabled') ? '1' : '0', 'payment');
            Setting::set('cashfree_env', $request->input('cashfree_env', 'sandbox'), 'payment');
            Setting::set('cod_enabled', $request->has('cod_enabled') ? '1' : '0', 'payment');
        }

        return back()->with('success', 'Payment gateway settings updated successfully.');
    }
}
