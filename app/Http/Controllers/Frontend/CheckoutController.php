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
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;

class CheckoutController extends Controller
{
    private const STATE_CITY_JSON = 'app/india_states_cities.json';
    private const STATE_ALIASES = [
        'Dadra and Nagar Haveli' => 'Dadra and Nagar Haveli and Daman and Diu',
        'Daman and Diu' => 'Dadra and Nagar Haveli and Daman and Diu',
    ];

    private const STATE_CODE_NAME_OVERRIDES = [
        '35' => 'Madhya Pradesh',
        '37' => 'Chhattisgarh',
        '39' => 'Uttarakhand',
    ];

    private const REQUIRED_STATES = [
        'Andaman and Nicobar Islands',
        'Andhra Pradesh',
        'Arunachal Pradesh',
        'Assam',
        'Bihar',
        'Chandigarh',
        'Chhattisgarh',
        'Dadra and Nagar Haveli and Daman and Diu',
        'Delhi',
        'Goa',
        'Gujarat',
        'Haryana',
        'Himachal Pradesh',
        'Jammu and Kashmir',
        'Jharkhand',
        'Karnataka',
        'Kerala',
        'Ladakh',
        'Lakshadweep',
        'Madhya Pradesh',
        'Maharashtra',
        'Manipur',
        'Meghalaya',
        'Mizoram',
        'Nagaland',
        'Odisha',
        'Puducherry',
        'Punjab',
        'Rajasthan',
        'Sikkim',
        'Tamil Nadu',
        'Telangana',
        'Tripura',
        'Uttar Pradesh',
        'Uttarakhand',
        'West Bengal',
    ];

    public function index(): \Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        $stateCityData = $this->stateCityData();
        $states = $this->checkoutStates($stateCityData);
        $stateCityMap = $this->checkoutCityMap($stateCityData);
        $allCities = $this->checkoutAllCities($stateCityData);
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

    public function page(Request $request): \Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        return $this->index();
    }

    public function getCities(string $stateCode): JsonResponse
    {
        $state = $this->stateCityData()[$stateCode] ?? null;

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

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $data = $this->stateCityData();
        $stateCodes = array_keys($data);
        $stateCode = $request->input('state');
        $cities = $data[$stateCode]['cities'] ?? [];

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:500'],
            'state' => ['required', 'string', Rule::in($stateCodes)],
            'city' => ['required', 'string', Rule::in($cities)],
            'pincode' => ['required', 'digits:6'],
        ]);

        $request->session()->put('checkout_details', $validated);

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

    private function checkoutStates(?array $stateCityData = null): array
    {
        return collect($stateCityData ?? $this->stateCityData())
            ->map(fn (array $state, string $code) => [
                'code' => $code,
                'name' => $state['name'] ?: $code,
            ])
            ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->all();
    }

    private function checkoutCityMap(?array $stateCityData = null): array
    {
        return collect($stateCityData ?? $this->stateCityData())
            ->mapWithKeys(fn (array $state, string $code) => [
                $code => collect($state['cities'] ?? [])
                    ->filter(fn ($city) => is_string($city) && trim($city) !== '')
                    ->sort(SORT_NATURAL | SORT_FLAG_CASE)
                    ->values()
                    ->all(),
            ])
            ->all();
    }

    private function checkoutAllCities(?array $stateCityData = null): array
    {
        return collect($stateCityData ?? $this->stateCityData())
            ->flatMap(fn (array $state) => $state['cities'] ?? [])
            ->filter(fn ($city) => is_string($city) && trim($city) !== '')
            ->unique(fn ($city) => strtolower($city))
            ->sort(SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->all();
    }

    private function stateCityData(): array
    {
        $path = storage_path(self::STATE_CITY_JSON);

        if (! File::exists($path)) {
            return [];
        }

        $data = json_decode(File::get($path), true);

        if (! is_array($data)) {
            return [];
        }

        $states = [];
        foreach ($data as $code => $state) {
            if (! is_array($state)) {
                continue;
            }

            $normalizedCode = is_numeric($code)
                ? str_pad((string) $code, 2, '0', STR_PAD_LEFT)
                : trim((string) $code);

            if ($normalizedCode === '') {
                continue;
            }

            $states[$normalizedCode] = [
                'name' => self::STATE_CODE_NAME_OVERRIDES[$normalizedCode] ?? (string) ($state['name'] ?? ''),
                'cities' => is_array($state['cities'] ?? null) ? $state['cities'] : [],
            ];
        }

        $merged = [];
        foreach ($states as $code => $state) {
            $name = trim($state['name']) !== '' ? trim($state['name']) : $code;
            $name = self::STATE_ALIASES[$name] ?? $name;
            $existingCode = collect($merged)
                ->search(fn ($existing) => strcasecmp($existing['name'], $name) === 0);

            if ($existingCode === false) {
                $merged[$code] = [
                    'name' => $name,
                    'cities' => $state['cities'],
                ];
                continue;
            }

            $merged[$existingCode]['cities'] = collect($merged[$existingCode]['cities'])
                ->merge($state['cities'])
                ->filter(fn ($city) => is_string($city) && trim($city) !== '')
                ->unique(fn ($city) => strtolower($city))
                ->values()
                ->all();
        }

        foreach (self::REQUIRED_STATES as $name) {
            $exists = collect($merged)
                ->contains(fn ($state) => strcasecmp($state['name'], $name) === 0);

            if (! $exists) {
                $merged['manual-' . strtolower(str_replace([' ', 'and'], ['-', 'and'], $name))] = [
                    'name' => $name,
                    'cities' => [],
                ];
            }
        }

        return $merged;
    }
}
