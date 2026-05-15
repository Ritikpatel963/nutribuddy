<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\CheckoutPlaceOrderRequest;
use App\Http\Requests\Frontend\CheckoutSummaryRequest;
use App\Models\Cart;
use App\Models\CustomerAddress;
use App\Services\Checkout\CheckoutSummaryService;
use App\Services\Checkout\OrderPlacementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function page(Request $request): \Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
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
        return view('pages.checkout', compact('savedAddresses'));
    }

    public function summary(CheckoutSummaryRequest $request, CheckoutSummaryService $checkoutSummaryService): JsonResponse
    {
        return response()->json(
            $checkoutSummaryService->forUser($request->user(), $request->validated())
        );
    }

    public function guestSummary(Request $request, CheckoutSummaryService $checkoutSummaryService): JsonResponse
    {
        return response()->json(
            $checkoutSummaryService->forGuest(
                $request->input('items', []),
                $request->input('coupon_code')
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
