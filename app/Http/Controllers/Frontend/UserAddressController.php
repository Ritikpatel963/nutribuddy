<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CustomerAddress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserAddressController extends Controller
{
    public function index(Request $request)
    {
        $addresses = CustomerAddress::where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return view('pages.user-panel.personal-info', ['savedAddresses' => $addresses]);
    }

    public function create()
    {
        return view('pages.user-panel.address-create');
    }

    public function edit(Request $request, CustomerAddress $address)
    {
        abort_unless((int) $address->user_id === (int) $request->user()->id, 403);
        return view('pages.user-panel.address-edit', compact('address'));
    }

    public function update(Request $request, CustomerAddress $address): JsonResponse
    {
        abort_unless((int) $address->user_id === (int) $request->user()->id, 403);

        $validated = $request->validate([
            'label' => ['nullable', 'string', 'max:50'],
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:15'],
            'email' => ['nullable', 'email', 'max:255'],
            'address_line_1' => ['required', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'landmark' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'max:10'],
            'country' => ['nullable', 'string', 'max:100'],
        ]);

        $address->update($validated);

        return response()->json(['message' => 'Address updated.']);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'label' => ['nullable', 'string', 'max:50'],
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:15'],
            'email' => ['nullable', 'email', 'max:255'],
            'address_line_1' => ['required', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'landmark' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'max:10'],
            'country' => ['nullable', 'string', 'max:100'],
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['country'] = $validated['country'] ?? 'India';
        $validated['email'] = ($validated['email'] ?? null) ?: $request->user()->email;

        $address = CustomerAddress::create($validated);

        return response()->json(['data' => $address], 201);
    }

    public function destroy(Request $request, CustomerAddress $address): JsonResponse
    {
        abort_unless((int) $address->user_id === (int) $request->user()->id, 403);
        $address->delete();

        return response()->json(['message' => 'Address deleted.']);
    }

    public function setDefault(Request $request, CustomerAddress $address): JsonResponse
    {
        abort_unless((int) $address->user_id === (int) $request->user()->id, 403);

        // Reset all addresses for this user
        CustomerAddress::where('user_id', $request->user()->id)
            ->update(['is_default_shipping' => false, 'is_default_billing' => false]);

        // Set this one as default
        $address->update(['is_default_shipping' => true, 'is_default_billing' => true]);

        return response()->json(['message' => 'Default address updated.']);
    }
}
