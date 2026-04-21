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
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value, 'general');
        }

        return back()->with('success', 'Settings updated successfully.');
    }
}
