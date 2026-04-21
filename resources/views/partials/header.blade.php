<nav id="mainNav">
    <a href="{{ route('home') }}" class="logo-new">
        <img src="{{ asset('img/logo.png') }}" alt="NutriBuddy"
            onerror="this.style.display='none';this.nextElementSibling.style.display='inline'">
        <span style="display:none;font-family:'Fredoka One',cursive;font-size:1.5rem;color:var(--pk)">
            NutriBuddy<sup
                style="font-size:.55rem;background:var(--ye);color:var(--dk);padding:2px 7px;border-radius:20px;margin-left:4px;font-family:'Nunito',sans-serif;font-weight:900">KIDS</sup>
        </span>
    </a>

    <ul class="nav-links">
        <li><a href="{{ route('home') }}">Home</a></li>
        <li><a href="{{ route('about') }}">About Us</a></li>
        <li><a href="{{ route('product') }}">Products</a></li>
        <li><a href="{{ route('diet_chart') }}">Personalized Diet Chart</a></li>
        <li><a href="#">Testimonials</a></li>
    </ul>

    <div class="nav-actions">
        <button id="cartIconBtn" title="View Cart">
            <img src="{{ asset('img/shopping-cart.png') }}" alt="Cart"
                onerror="this.style.display='none';this.parentElement.insertAdjacentHTML('afterbegin','<span style=\'font-size:1.15rem\'>🛒</span>')">
            <span class="cart-count" id="cartCount">3</span>
        </button>

        <a href="{{ route('contact') }}" class="nav-cta">Contact Us</a>

        <button class="hamburger" id="hamburgerBtn" aria-expanded="false">
            <img src="{{ asset('img/menu.png') }}" alt="Menu"
                onerror="this.style.display='none';this.parentElement.innerHTML='<span></span><span></span><span></span>'">
        </button>
    </div>
</nav>

<div class="menu-overlay" id="menuOverlay"></div>

<style>
    @if(isset($isUserPanel) && $isUserPanel)
    .mobile-menu.open {
        max-height: unset !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        height: calc(100vh - 68px) !important;
    }
    .mobile-menu ul {
        padding-bottom: 40px !important;
    }
    @endif
</style>

<div class="mobile-menu" id="mobileMenu" aria-hidden="true">
    <ul>
        @if(isset($isUserPanel) && $isUserPanel)
            <li class="mob-cat-title"
                style="font-family:'Fredoka One',cursive;font-size:.7rem;color:var(--pk);padding:10px 6%;letter-spacing:1px;text-transform:uppercase;">
                Main</li>
            <li><a href="{{ route('userdashboard') }}">Overview</a></li>
            <li><a href="{{ route('meal-plan') }}">Meal Plan</a></li>
            <li><a href="{{ route('health-scores') }}">Health Scores</a></li>
            <li><a href="{{ route('supplement') }}">Supplement</a></li>
            <li><a href="{{ route('child-profile') }}">Child Profile</a></li>
            <li><a href="{{ route('growth-signal') }}">Growth Signals</a></li>
            <li><a href="{{ route('check-in') }}">Quarterly Check-in</a></li>
            <li><a href="{{ route('order') }}">Order</a></li>

            <li class="mob-cat-title"
                style="font-family:'Fredoka One',cursive;font-size:.7rem;color:var(--pk);padding:10px 6%;letter-spacing:1px;text-transform:uppercase;">
                Account</li>
            <li><a href="{{ route('personal-info') }}">Personal Info</a></li>
            <li><a href="#">Address</a></li>
            <li><a href="#">Wishlist</a></li>
            <li><a href="{{ route('change-password') }}">Change Password</a></li>
            <li><a href="{{ route('subscription') }}">Subscription</a></li>
            <li><a href="#">My Coupons</a></li>

            <li>
                <hr style="border:0; border-top:1px solid rgba(0,0,0,0.1); margin: 8px 6%;">
            </li>
            <li class="mob-cat-title"
                style="font-family:'Fredoka One',cursive;font-size:.7rem;color:var(--pk);padding:10px 6%;letter-spacing:1px;text-transform:uppercase;">
                Global Nav</li>
        @endif
        <li><a href="{{ route('home') }}"><span class="link-emoji"></span> Home</a></li>
        <li><a href="{{ route('about') }}"><span class="link-emoji"></span> About Us</a></li>
        <li><a href="{{ route('product') }}"><span class="link-emoji"></span> Products</a></li>
        <li><a href="{{ route('diet_chart') }}"><span class="link-emoji"></span> Personalized Diet Chart</a></li>
        <li><a href="#"><span class="link-emoji"></span> Testimonials</a></li>
    </ul>
    <div class="mobile-cta-wrap">
        <a href="{{ route('contact') }}">Contact Us</a>
    </div>
</div>

<div id="cart-popup">
    <div class="popup-inner">
        <button class="close-cart" id="closeCart">✕</button>
        <div class="cart-inner">
            <h4 class="title-text"><span>3</span> Cart Items</h4>

            <div class="single-cart-box">
                <div class="image-box"><img src="{{ asset('img/product2.png') }}" alt=""></div>
                <div>
                    <h5>GrowStrong Gummies — 30 Day Supply</h5>
                    <h4>₹599 <span style="font-size:.75rem;color:#aaa;font-family:'DM Sans',sans-serif">× 1</span></h4>
                </div>
                <button>✕</button>
            </div>

            <div class="single-cart-box">
                <div class="image-box"><img src="{{ asset('img/product2.png') }}" alt=""></div>
                <div>
                    <h5>BrainBoost Chews — 30 Day Supply</h5>
                    <h4>₹649 <span style="font-size:.75rem;color:#aaa;font-family:'DM Sans',sans-serif">× 1</span></h4>
                </div>
                <button>✕</button>
            </div>

            <div class="single-cart-box">
                <div class="image-box"><img src="{{ asset('img/product2.png') }}" alt=""></div>
                <div>
                    <h5>DreamCalm Drops — 30 Day Supply</h5>
                    <h4>₹549 <span style="font-size:.75rem;color:#aaa;font-family:'DM Sans',sans-serif">× 1</span></h4>
                </div>
                <button>✕</button>
            </div>

            <div class="text-box">
                <h5>Subtotal</h5>
                <span>₹1,797</span>
            </div>

            <div class="btn-box">
                <a href="#">View Cart</a>
                <a href="#">Checkout →</a>
            </div>
        </div>
    </div>
</div>