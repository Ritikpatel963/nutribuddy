<?php

namespace App\Http\Controllers\Frontend\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\Account\DeleteAddressRequest;
use App\Http\Requests\Frontend\Account\SetDefaultAddressRequest;
use App\Http\Requests\Frontend\StoreAddressRequest;
use App\Http\Requests\Frontend\UpdateAddressRequest;
use App\Models\CustomerAddress;
use App\Services\Access\CustomerAddressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserAddressController extends Controller
{
    public function index(Request $request)
    {
        $addresses = CustomerAddress::where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return view('frontend.account.profile', ['savedAddresses' => $addresses]);
    }

    public function create()
    {
        return view('frontend.account.address-create');
    }

    public function edit(Request $request, CustomerAddress $address)
    {
        abort_unless((int) $address->user_id === (int) $request->user()->id, 403);
        return view('frontend.account.address-edit', compact('address'));
    }

    public function update(UpdateAddressRequest $request, CustomerAddress $address): JsonResponse
    {
        abort_unless((int) $address->user_id === (int) $request->user()->id, 403);

        $validated = $request->validated();
        $address->update($validated);

        return response()->json(['message' => 'Address updated.']);
    }

    public function store(StoreAddressRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $validated['user_id'] = $request->user()->id;
        $validated['country'] = $validated['country'] ?? 'India';
        $validated['email'] = ($validated['email'] ?? null) ?: $request->user()->email;

        $address = CustomerAddress::create($validated);

        return response()->json(['data' => $address], 201);
    }

    public function destroy(DeleteAddressRequest $request, CustomerAddress $address): JsonResponse
    {
        $address->delete();

        return response()->json(['message' => 'Address deleted.']);
    }

    public function setDefault(SetDefaultAddressRequest $request, CustomerAddress $address, CustomerAddressService $addresses): JsonResponse
    {
        $addresses->setDefault((int) $request->user()->id, (int) $address->id);

        return response()->json(['message' => 'Default address updated.']);
    }
}
