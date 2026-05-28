<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\CheckoutGuestSummaryRequest;
use App\Http\Requests\Frontend\CheckoutPlaceOrderRequest;
use App\Http\Requests\Frontend\CheckoutStoreRequest;
use App\Http\Requests\Frontend\CheckoutSummaryRequest;
use App\Models\Cart;
use App\Models\CustomerAddress;
use App\Services\Checkout\CheckoutSummaryService;
use App\Services\Checkout\OrderPlacementService;
use App\Services\Checkout\StateCityService;
use Illuminate\Http\JsonResponse;

class CheckoutController extends Controller
{
    public function index(StateCityService $stateCityService): \Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        $states = $stateCityService->checkoutStates();
        $stateCityMap = $stateCityService->checkoutCityMap();
        $allCities = $stateCityService->checkoutAllCities();
        $savedAddresses = collect();

        if (auth()->check()) {
            $cart = Cart::where('user_id', auth()->id())->withCount('items')->first();
            if (!$cart || $cart->items_count < 1) {
                return redirect()
                    ->route('cart.page')
                    ->with('warning', 'Please add at least one item to your cart before checkout.');
            }

            $savedAddresses = CustomerAddress::where('user_id', auth()->id())
                ->latest()
                ->get();
        }

        return view('pages.checkout', compact('savedAddresses', 'states', 'stateCityMap', 'allCities'));
    }

    public function getCities(string $stateCode, StateCityService $stateCityService): JsonResponse
    {
        $state = $stateCityService->getStateCityData()[$stateCode] ?? null;

        if (! $state) {
            return response()->json(['cities' => []], 404);
        }

        $cities = collect($state['cities'] ?? [])
            ->filter(fn ($city) => is_string($city) && trim($city) !== '')
            ->sort(SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->all();

        return response()->json(['cities' => $cities]);
    }

    public function store(CheckoutStoreRequest $request): \Illuminate\Http\RedirectResponse
    {
        $request->session()->put('checkout_details', $request->validated());

        return redirect()
            ->route('checkout.index')
            ->with('success', 'Checkout details saved successfully.');
    }

    public function summary(CheckoutSummaryRequest $request, CheckoutSummaryService $checkoutSummaryService): JsonResponse
    {
        return response()->json(
            $checkoutSummaryService->forUser($request->user(), $request->validated())
        );
    }

    public function guestSummary(CheckoutGuestSummaryRequest $request, CheckoutSummaryService $checkoutSummaryService): JsonResponse
    {
        return response()->json(
            $checkoutSummaryService->forGuest(
                $request->validated('items'),
                $request->validated('coupon_code')
            )
        );
    }

    public function placeOrder(CheckoutPlaceOrderRequest $request, OrderPlacementService $orderPlacementService): JsonResponse
    {
        return response()->json(
            $orderPlacementService->place($request->user(), $request->validated())
        );
    }
}
