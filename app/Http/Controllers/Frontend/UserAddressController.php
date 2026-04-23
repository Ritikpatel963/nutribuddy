<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CustomerAddress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserAddressController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $addresses = CustomerAddress::where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json(['data' => $addresses]);
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
        $validated['email'] = $validated['email'] ?: $request->user()->email;

        $address = CustomerAddress::create($validated);

        return response()->json(['data' => $address], 201);
    }

    public function destroy(Request $request, CustomerAddress $address): JsonResponse
    {
        abort_unless((int) $address->user_id === (int) $request->user()->id, 403);
        $address->delete();

        return response()->json(['message' => 'Address deleted.']);
    }
}
