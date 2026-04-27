<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class SideSectionController extends Controller
{
    public function index()
    {
        $settings = [
            'side_section_logo' => Setting::get('side_section_logo'),
            'side_section_contact_number' => Setting::get('side_section_contact_number'),
            'side_section_email' => Setting::get('side_section_email'),
            'side_section_address' => Setting::get('side_section_address'),
            'side_section_description' => Setting::get('side_section_description'),
            'side_section_social_links' => Setting::get('side_section_social_links', '[]')
        ];

        return view('admin.ecommerce.variants.side-section.side-section', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:2048',
            'contact_number' => 'required|string',
            'email' => 'required|email',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'social_platform' => 'nullable|array',
            'social_url' => 'nullable|array',
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('settings', 'public');
            Setting::set('side_section_logo', $path, 'side_section');
        } elseif (Setting::where('key', 'side_section_logo')->doesntExist()) {
            Setting::set('side_section_logo', null, 'side_section');
        }

        Setting::set('side_section_contact_number', $request->contact_number, 'side_section');
        Setting::set('side_section_email', $request->email, 'side_section');
        Setting::set('side_section_address', $request->address, 'side_section');
        Setting::set('side_section_description', $request->description, 'side_section');

        $socialLinks = [];
        if ($request->has('social_platform') && $request->has('social_url')) {
            foreach ($request->social_platform as $key => $platform) {
                if (!empty($request->social_url[$key])) {
                    $socialLinks[] = [
                        'platform' => $platform,
                        'url' => $request->social_url[$key]
                    ];
                }
            }
        }
        Setting::set('side_section_social_links', json_encode($socialLinks), 'side_section');

        return back()->with('success', 'Side section settings updated successfully.');
    }
}
