@extends('layouts.main')
@section('title', 'Checkout — NutriBuddy Kids')

@section('content')
<script src="https://sdk.cashfree.com/js/v3/cashfree.js"></script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    window.NB_CHECKOUT_CONFIG = {
        enabled: true,
        isGuest: @json(auth()->guest()),
        isLoggedIn: @json((bool) auth()->check()),
        selectedAddressId: @json((string)(auth()->check() && isset($savedAddresses) && $savedAddresses->first() ? $savedAddresses->first()->id : '')),
        email: @json(auth()->user()->email ?? ''),
        phone: @json(auth()->user()->phone ?? ''),
        cartPageUrl: @json(route('cart.page')),
        citiesUrlTemplate: @json(route('checkout.cities', ['stateCode' => '__STATE__'])),
        states: @json($states),
        citiesByState: @json($stateCityMap),
        allCities: @json($allCities),
        api: {
            cartUrl: '/user/cart',
            cartUpdateTemplate: @json(route('user.cart.items.update', ['itemId' => '__ITEM__'])),
            addressesUrl: '/user/addresses',
            checkoutSummaryUrl: '/user/checkout/summary',
            placeOrderUrl: '/user/checkout/place-order',
            csrfTokenUrl: @json(route('csrf.token')),
            sendOtpUrl: @json(route('frontend.sendOtp')),
            verifyOtpUrl: @json(route('frontend.verifyOtp'))
        },
        cashfreeMode: @json(config('services.cashfree.env') === 'production' ? 'production' : 'sandbox')
    };
</script>

    @if ($errors->any())
        <div class="checkout-validation-errors">
            <strong>Please fix the following:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- HERO -->
    <section class="product-listing-hero reveal">
        <div class="product-listing-hero-inner">
            <div class="product-listing-breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span>/</span>
                <a href="{{ route('cart.page') }}">Cart</a>
                <span>/</span>
                <span>Checkout</span>
            </div>
            <span class="product-listing-hero-badge">Secure Checkout</span>
            <h1 class="product-listing-hero-title">Complete Your Order</h1>
            <p class="product-listing-hero-sub">Review your delivery details, confirm payment, and place your NutriBuddy order securely.</p>
        </div>
    </section>
    <!-- Menu Overlay -->
    <div class="menu-overlay" id="menuOverlay"></div>

    <!-- Mobile Dropdown Menu -->
    <div class="mobile-menu" id="mobileMenu" aria-hidden="true">
        <ul>
            <li><a href="#"><span class="link-emoji"></span> Home</a></li>
            <li><a href="#"><span class="link-emoji"></span> About Us</a></li>
            <li><a href="#"><span class="link-emoji"></span> Products</a></li>
            <li><a href="#"><span class="link-emoji"></span> Personalized Diet Chart</a></li>
            <li><a href="#"><span class="link-emoji"></span> Testimonials</a></li>
        </ul>
        <div class="mobile-cta-wrap">
            <a href="#">Contact Us</a>
        </div>
    </div>

    <!-- ══ TOPBAR ══ -->
    <header class="checkout-topbar">

        <div class="topbar-steps">
            <div class="ts-arrow">›</div>
            <div class="ts active" id="step-addr">
                <div class="ts-num">1</div> Delivery Address
            </div>
            <div class="ts-arrow">›</div>
            <div class="ts" id="step-pay">
                <div class="ts-num">2</div> Payment
            </div>
        </div>
    </header>
    <div class="progress-strip">
        <div class="progress-fill" id="progressFill"></div>
    </div>

    <!-- ══ MAIN ══ -->
    <main class="checkout-main">

        <!-- ══ LEFT ══ -->
        <div class="left-panel">

            <!-- STEP 1: ADDRESS -->
            <div class="co-card active-card" id="addressCard">
                <div class="card-head">
                    <div class="card-head-left">
                        <div class="step-badge" id="addrBadge">1</div>
                        <h3>Delivery Address</h3>
                    </div>
                    @auth
                    <span style="font-size:.78rem;color:var(--text-light)" id="savedAddrCount">
                        {{ $savedAddresses->count() }} saved {{ \Illuminate\Support\Str::plural('address', $savedAddresses->count()) }}
                    </span>
                    @endauth
                </div>
                <div class="card-body">

                    @auth
                    @if($savedAddresses->count() > 0)
                    {{-- ── TABS ── --}}
                    <div class="addr-tabs">
                        <button class="addr-tab active" id="tabSaved" onclick="switchAddrTab('saved')">📍 Saved Addresses</button>
                        <button class="addr-tab" id="tabNew" onclick="switchAddrTab('new')">➕ Add New</button>
                    </div>

                    {{-- ── SAVED ADDRESS LIST ── --}}
                    <div id="savedAddrPanel">
                        <div class="saved-addresses" id="savedAddressList">
                            @foreach($savedAddresses as $addr)
                            <div class="addr-item {{ $loop->first ? 'selected' : '' }}"
                                 data-address-id="{{ $addr->id }}"
                                 onclick="selectSavedAddress(this, '{{ $addr->id }}')">
                                <div class="addr-radio"></div>
                                <div class="addr-info" style="flex:1">
                                    <div class="addr-name">
                                        {{ $addr->full_name }}
                                        <span class="addr-type-tag">{{ $addr->label ?? 'Home' }}</span>
                                    </div>
                                    <div class="addr-line">
                                        {{ $addr->address_line_1 }}{{ $addr->address_line_2 ? ', '.$addr->address_line_2 : '' }}{{ $addr->landmark ? ', Near '.$addr->landmark : '' }}
                                    </div>
                                    <div class="addr-line">
                                        {{ $addr->city }}, {{ $addr->state }} — {{ $addr->postal_code }}
                                    </div>
                                    <div class="addr-phone">📱 {{ $addr->phone }}</div>
                                </div>
                                <button class="addr-del-btn" title="Delete"
                                    onclick="deleteAddress(event, {{ $addr->id }}, this)">🗑</button>
                            </div>
                            @endforeach
                        </div>
                        <button class="add-addr-btn" onclick="switchAddrTab('new')" style="margin-top:8px">
                            <span>➕</span> Add a New Address
                        </button>
                    </div>
                    @endif
                    @endauth

                    {{-- ── NEW ADDRESS FORM ── --}}
                    <div id="newAddrPanel" @auth @if($savedAddresses->count() > 0) style="display:none" @endif @endauth>
                        <div id="addressFormPanel">
                            <div class="new-addr-form show">
                                <div class="form-grid">
                                    <div class="form-group"><label>First Name *</label><input type="text" id="firstName" placeholder="e.g. Priya"></div>
                                    <div class="form-group"><label>Last Name *</label><input type="text" id="lastName" placeholder="e.g. Sharma"></div>
                                </div>
                                <div class="form-grid single" style="margin-top:14px">
                                    <div class="form-group"><label>Mobile Number *</label><input type="tel" id="addressPhone" placeholder="+91 XXXXX XXXXX"></div>
                                </div>
                                <div class="form-grid single" style="margin-top:14px">
                                    <div class="form-group"><label>Flat / House / Apartment *</label><input type="text" id="addressLine1" placeholder="e.g. 42, Sunshine Residency"></div>
                                </div>
                                <div class="form-grid single" style="margin-top:14px">
                                    <div class="form-group"><label>Street / Area / Colony *</label><input type="text" id="addressLine2" placeholder="e.g. HSR Layout, Sector 3"></div>
                                </div>
                                <div class="form-grid" style="margin-top:14px">
                                    <div class="form-group">
                                        <label>State *</label>
                                        <div class="checkout-combobox">
                                            <input type="text" id="stateField" placeholder="Type or select state" autocomplete="off" data-old-state="{{ old('state') }}">
                                            <input type="hidden" id="stateCodeField">
                                            <div class="checkout-combobox-menu" id="stateDropdown" hidden></div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>City *</label>
                                        <div class="checkout-combobox checkout-city-select-wrap">
                                            <input type="text" id="cityField" placeholder="Select state first" autocomplete="off" data-old-city="{{ old('city') }}" disabled>
                                            <div class="checkout-combobox-menu" id="cityDropdown" hidden></div>
                                            <span class="checkout-city-spinner" id="citySpinner" aria-hidden="true"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-grid" style="margin-top:14px">
                                    <div class="form-group"><label>Pincode *</label><input type="text" maxlength="6" placeholder="6-digit pincode" id="newPincode" oninput="autoFillCity()"></div>
                                    <div class="form-group">
                                        <label>Address Type</label>
                                        <div class="addr-type-row">
                                            <button class="addr-type-btn active" data-type="Home" onclick="toggleAddrType(this)">🏠 Home</button>
                                            <button class="addr-type-btn" data-type="Work" onclick="toggleAddrType(this)">💼 Work</button>
                                            <button class="addr-type-btn" data-type="Other" onclick="toggleAddrType(this)">📍 Other</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button class="continue-btn" id="addressContinueBtn" onclick="saveAndGoToPayment()">
                        Continue to Payment
                    </button>
                </div>
            </div>

            <!-- STEP 2: PAYMENT -->
            <div class="co-card" id="paymentCard" style="opacity:.5;pointer-events:none">
                <div class="card-head">
                    <div class="card-head-left">
                        <div class="step-badge" id="payBadge">2</div>
                        <h3>Payment Method</h3>
                    </div>
                    <span style="font-size:.78rem;color:var(--text-light)">Choose how to pay</span>
                </div>
                <div class="card-body">
                    @php
                        $cashfreeEnabled = \App\Models\Setting::get('cashfree_enabled', '0') === '1';
                        $razorpayEnabled = \App\Models\Setting::get('razorpay_enabled', '0') === '1';
                        $codEnabled      = \App\Models\Setting::get('cod_enabled', '0') === '1';

                        $defaultMethod = 'cashfree';
                        if (!$cashfreeEnabled) {
                            if ($razorpayEnabled) {
                                $defaultMethod = 'razorpay';
                            } elseif ($codEnabled) {
                                $defaultMethod = 'cod';
                            }
                        }
                    @endphp
                    
                    <div class="payment-methods">
                        @if ($cashfreeEnabled)
                            <div class="pay-method {{ $defaultMethod === 'cashfree' ? 'selected' : '' }}" id="payMethodCashfree" data-method="cashfree"
                                onclick="selectPayMethod(this,'cashfree')">
                                <div class="pay-head">
                                    <div class="pay-radio"></div>
                                    <div class="pay-icon">💵</div>
                                    <div>
                                        <div class="pay-name">Cashfree Online Payment</div>
                                        <div class="pay-sub">Pay securely with UPI, cards, wallets or netbanking</div>
                                    </div>
                                    <div class="pay-tags"></div>
                                </div>
                            </div>
                        @endif

                        @if ($razorpayEnabled)
                            <div class="pay-method {{ $defaultMethod === 'razorpay' ? 'selected' : '' }}" id="payMethodRazorpay" data-method="razorpay"
                                onclick="selectPayMethod(this,'razorpay')">
                                <div class="pay-head">
                                    <div class="pay-radio"></div>
                                    <div class="pay-icon">💳</div>
                                    <div>
                                        <div class="pay-name">Razorpay Secure</div>
                                        <div class="pay-sub">Pay with UPI, Cards, Netbanking or Wallets</div>
                                    </div>
                                    <div class="pay-tags"></div>
                                </div>
                            </div>
                        @endif

                        @if ($codEnabled)
                            <div class="pay-method {{ $defaultMethod === 'cod' ? 'selected' : '' }}" id="payMethodCod" data-method="cod"
                                onclick="selectPayMethod(this,'cod')">
                                <div class="pay-head">
                                    <div class="pay-radio"></div>
                                    <div class="pay-icon">🤝</div>
                                    <div>
                                        <div class="pay-name">Cash on Delivery (COD)</div>
                                        <div class="pay-sub">Pay in cash upon delivery</div>
                                    </div>
                                    <div class="pay-tags"></div>
                                </div>
                            </div>
                        @endif

                        @if (!$cashfreeEnabled && !$razorpayEnabled && !$codEnabled)
                            <div class="text-danger p-3 text-center w-100" style="font-weight: 800; font-family: 'Nunito', sans-serif;">
                                ⚠️ No payment methods are currently active.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div><!-- /left-panel -->

        <!-- ══ RIGHT: ORDER SUMMARY ══ -->
        <div class="order-summary">
            <div class="os-head">
                <h3>Order Summary</h3>
                <div class="item-count">3 Items</div>
            </div>
            <div class="os-body">
                <div class="cart-items" id="checkoutCartItems"></div>

                <!-- Coupon -->
                <div class="coupon-row" id="couponRow">
                    <div class="coupon-label">Coupon / Promo Code</div>
                    <div class="coupon-input-row">
                        <input class="coupon-input" type="text" placeholder="Enter coupon code" id="couponInput">
                        <button class="coupon-apply-btn" onclick="applyCoupon()">Apply</button>
                    </div>
                    <div class="coupon-applied-msg" id="couponMsg"></div>
                </div>

                <!-- Loyalty Coins -->
                @auth
                @php
                    $adminMaxRedeemableCoins = (int) \App\Models\Setting::get('loyalty_max_redeemable_coins', 0);
                    $checkoutCoinSliderMax = $adminMaxRedeemableCoins > 0
                        ? min((int) auth()->user()->coins_balance, $adminMaxRedeemableCoins)
                        : (int) auth()->user()->coins_balance;
                    $canRedeemCoins = $checkoutCoinSliderMax > 0;
                @endphp
                <div class="loyalty-row" id="coinRedeemRow" style="margin-top: 20px; border-top: 2px dashed var(--border); padding-top: 20px;">
                    <div class="d-flex justify-content-between align-items-center mb-10" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <div class="coupon-label" style="font-family: 'Nunito', sans-serif; font-weight: 800; font-size: 0.82rem; color: var(--dk);">Redeem NB Coins 🪙</div>
                        <div class="lb-pts" id="userCoinBalance" style="font-family: 'Fredoka One', cursive; color: var(--or); font-size: 0.85rem;">{{ auth()->user()->coins_balance }} Available</div>
                    </div>
                    <div class="coin-redeem-box is-disabled" id="coinRedeemBox">
                        <label class="coin-redeem-toggle {{ $canRedeemCoins ? '' : 'is-disabled' }}" for="coinRedeemToggle">
                            <input type="checkbox" id="coinRedeemToggle" {{ $canRedeemCoins ? '' : 'disabled' }}>
                            <span>{{ $canRedeemCoins ? 'Use NB Coins on this order' : 'No NB Coins available to redeem' }}</span>
                        </label>
                        <input type="range" class="coin-slider" id="coinSlider" min="0" max="{{ $checkoutCoinSliderMax }}" value="0" step="1" data-admin-max="{{ $adminMaxRedeemableCoins }}" disabled>
                        <div style="display: flex; justify-content: space-between; margin-top: 8px;">
                            <span style="font-size: 0.7rem; color: #aaa;">0</span>
                            <span id="coinsToRedeemValue" style="font-family: 'Fredoka One', cursive; color: var(--or); font-size: 0.95rem;">Redeeming: 0 Coins</span>
                            <span id="coinSliderMaxValue" style="font-size: 0.7rem; color: #aaa;">{{ $checkoutCoinSliderMax }}</span>
                        </div>
                        @if($adminMaxRedeemableCoins > 0)
                            <div style="font-size: 0.7rem; color: #777; text-align: center; margin-top: 6px; font-weight: 700;">
                                Max {{ $adminMaxRedeemableCoins }} coins per order
                            </div>
                        @endif
                        <div id="coinDiscountText" style="font-size: 0.72rem; color: #777; text-align: center; margin-top: 8px; font-weight: 600;">
                            Value: ₹0.00 off
                        </div>
                    </div>
                </div>
                @endauth

                <!-- Price Breakdown -->
                <div class="price-breakdown" id="priceBreakdown">
                    <div class="pb-row">
                        <span class="pb-label" id="pbMrpLabel">Price (0 items)</span>
                        <span class="pb-val" id="pbMrpValue">₹0</span>
                    </div>
                    <div class="pb-row">
                        <span class="pb-label">Product Discount</span>
                        <span class="pb-val green" id="pbProductDiscount">− ₹0</span>
                    </div>
                    <div class="pb-row">
                        <span class="pb-label">Delivery Charges</span>
                        <span class="pb-val green" id="pbDelivery">FREE 🎉</span>
                    </div>
                    <div class="pb-row">
                        <span class="pb-label">GST (Applied on top)</span>
                        <span class="pb-val" id="pbGst">₹0</span>
                    </div>
                    <div class="pb-row" id="couponRow2" style="display:none">
                        <span class="pb-label">Coupon</span>
                        <span class="pb-val green" id="couponDiscount">− ₹0</span>
                    </div>
                    <div class="pb-row" id="coinDiscountRow" style="display:none">
                        <span class="pb-label">NB Coins Discount</span>
                        <span class="pb-val green" id="coinDiscountVal">− ₹0</span>
                    </div>
                    <div class="pb-divider"></div>
                    <div class="pb-total-row">
                        <div>
                            <div class="pb-total-label">Total Amount</div>
                            <div class="tax-note">Incl. all taxes</div>
                        </div>
                        <div>
                            <div class="pb-total-val" id="totalDisplay">₹0</div>
                        </div>
                    </div>
                </div>
                <!-- Place Order (shown after payment section is active) -->
                <div id="placeOrderWrap" style="display:none;margin-top:16px">
                    <button class="place-order-btn" onclick="openOtpModal()">
                        🔒 Place Order Securely — ₹0
                    </button>
                    <div class="trust-row">
                        <div class="tr-badge">🔒 SSL Secure</div>
                        <div class="tr-badge">✅ FSSAI Certified</div>
                        <div class="tr-badge">🔄 Easy Returns</div>
                    </div>
                </div>

            </div>
        </div>

    </main>

    <!-- ══════════════════════════════
                     PHONE / OTP MODAL
                ══════════════════════════════ -->
    <div class="checkout-modal-overlay" id="otpModal">
        <div class="otp-modal">

            <!-- Header -->
            <div class="om-header">
                <button class="om-close" onclick="closeOtpModal()">✕</button>
                <div class="om-lock-icon">👋</div>
                <h2>Login / Sign Up</h2>
                <p>Enter your mobile number to continue with your order</p>
            </div>

            <!-- Body -->
            <div class="om-body">

                <!-- STEP A: Phone Number -->
                <div class="om-step active" id="stepPhone">
                    <div class="om-label">📱 Mobile Number</div>
                    <div class="phone-input-wrap">
                        <div class="phone-prefix">🇮🇳 +91</div>
                        <input type="tel" id="phoneInput" maxlength="10" inputmode="numeric" pattern="[0-9]*" autocomplete="tel"
                            placeholder="Enter 10-digit number" oninput="this.value=this.value.replace(/\D/g,'').slice(0,10)">
                    </div>
                    <div id="phoneError"
                        style="font-size:.78rem;color:var(--or);margin-top:8px;font-family:'Nunito',sans-serif;font-weight:700;display:none">
                    </div>

                    <!-- Pre-filled hint -->
                    <div style="display:flex;align-items:center;gap:6px;margin-top:12px;padding:10px 14px;background:var(--pkl);border-radius:10px;cursor:pointer"
                        onclick="useSavedPhone()">
                        <span style="font-size:1rem">👩</span>
                        <div style="flex:1">
                            <div style="font-family:'Nunito',sans-serif;font-weight:800;font-size:.78rem;color:var(--pkd)">
                                Use saved number</div>
                            <div style="font-size:.74rem;color:var(--pkd);opacity:.8">
                                {{ auth()->user()?->phone ? '+91 ' . auth()->user()->phone : 'Use your login mobile number' }}
                            </div>
                        </div>
                        <span style="font-size:.75rem;color:var(--pkd);font-weight:700">Tap →</span>
                    </div>

                    <button class="send-otp-btn" id="sendOtpBtn" onclick="sendOtp()">
                        Send OTP →
                    </button>

                    <div style="text-align:center;margin-top:12px;font-size:.76rem;color:var(--text-light)">
                        OTP will be sent via SMS to your mobile number
                    </div>
                </div>

                <!-- STEP B: OTP Verification -->
                <div class="om-step" id="stepOtp">
                    <div class="otp-sent-info">
                        <span style="font-size:1.1rem">✅</span>
                        <div>OTP sent to <strong id="sentToNum">+91 98765 43210</strong>. Valid for <strong>10
                                minutes</strong>.</div>
                    </div>

                    <div class="om-label" style="justify-content:center">Enter 6-digit OTP</div>

                    <div class="otp-boxes">
                        <input class="otp-box" type="tel" maxlength="1" inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code" id="otp1" oninput="otpInput(this,null,'otp2')"
                            onkeydown="otpKeydown(event,this,null,'otp2')">
                        <input class="otp-box" type="tel" maxlength="1" inputmode="numeric" pattern="[0-9]*" id="otp2" oninput="otpInput(this,'otp1','otp3')"
                            onkeydown="otpKeydown(event,this,'otp1','otp3')">
                        <input class="otp-box" type="tel" maxlength="1" inputmode="numeric" pattern="[0-9]*" id="otp3" oninput="otpInput(this,'otp2','otp4')"
                            onkeydown="otpKeydown(event,this,'otp2','otp4')">
                        <input class="otp-box" type="tel" maxlength="1" inputmode="numeric" pattern="[0-9]*" id="otp4" oninput="otpInput(this,'otp3','otp5')"
                            onkeydown="otpKeydown(event,this,'otp3','otp5')">
                        <input class="otp-box" type="tel" maxlength="1" inputmode="numeric" pattern="[0-9]*" id="otp5" oninput="otpInput(this,'otp4','otp6')"
                            onkeydown="otpKeydown(event,this,'otp4','otp6')">
                        <input class="otp-box" type="tel" maxlength="1" inputmode="numeric" pattern="[0-9]*" id="otp6" oninput="otpInput(this,'otp5',null)"
                            onkeydown="otpKeydown(event,this,'otp5',null)">
                    </div>

                    <div class="otp-error" id="otpError">❌ Incorrect OTP. Please try again.</div>

                    <div class="otp-timer" id="otpTimerText">
                        Resend OTP in <strong id="timerCount">30s</strong>
                    </div>

                    <button class="verify-otp-btn" id="verifyOtpBtn" onclick="verifyOtp()">
                        ✅ Verify & Place Order
                    </button>

                    <div class="change-phone-link">
                        Wrong number? <span onclick="backToPhone()">Change mobile number</span>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- ══ SUCCESS OVERLAY ══ -->
    <div class="success-overlay" id="successOverlay">
        <div class="success-box">
            <span class="success-icon">🎉</span>
            <h2>Order Placed!</h2>
            <p>Yay! Your NutriBuddy order has been placed successfully. Your little superheroes will receive their gummies soon!</p>
            <div class="order-id-box" id="successOrderNumber">Order ID: —</div>
            <p style="font-size:.82rem;color:var(--text-light);margin-bottom:20px">Estimated delivery: <strong>2–5 business days</strong><br>You'll receive an SMS confirmation shortly.</p>
            <div class="success-btns">
                <a class="btn-details" id="orderDetailBtn" href="#">📋 View Order Details</a>
                <a class="btn-home" href="{{ route('home') }}">🏠 Back to Home</a>
            </div>
            <div class="redirect-countdown" id="redirectCountdownWrap">
                Auto-redirecting to your order in <strong id="redirectCountdown">5</strong>s…
            </div>
        </div>
    </div>

    <!-- footer -->
    <!-- ══════════════════════════════════════════
                       NEWSLETTER
                  ══════════════════════════════════════════ -->
    <!-- ══════════════════════════════════════════
                       FOOTER
                  ══════════════════════════════════════════ -->@endsection
