@php
    $route = Route::currentRouteName();
@endphp

<div class="profile-block">
    <div class="avatar">J<div class="online-dot"></div></div>
    <div class="profile-name">Jaydafsdf</div>
    <div class="profile-email">user@gmail.com</div>
</div>
<div style="overflow-y:auto;flex:1">
    <div class="nav-section">
        <span class="nav-label">Main</span>
        <a href="{{ route('userdashboard') }}" class="nav-item {{ request()->routeIs('userdashboard') ? 'active' : '' }}" onclick="setActive(this)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <rect x="3" y="3" width="7" height="7" rx="2" />
                <rect x="14" y="3" width="7" height="7" rx="2" />
                <rect x="3" y="14" width="7" height="7" rx="2" />
                <rect x="14" y="14" width="7" height="7" rx="2" />
            </svg>
            Overview
        </a>

          <a href="{{ route('meal-plan') }}" class="nav-item {{ request()->routeIs('meal-plan') ? 'active' : '' }}" onclick="setActive(this)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                <circle cx="12" cy="7" r="4" />
            </svg>
           Meal Plan
        </a>
        <a href="{{ route('health-scores') }}" class="nav-item {{ request()->routeIs('health-scores') ? 'active' : '' }}" onclick="setActive(this)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                <circle cx="12" cy="7" r="4" />
            </svg>
          Health Scores
        </a>
          <a href="{{ route('supplement') }}" class="nav-item {{ request()->routeIs('supplement') ? 'active' : '' }}" onclick="setActive(this)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                <circle cx="12" cy="7" r="4" />
            </svg>
         Supplement
        </a>
          <a href="{{ route('child-profile') }}" class="nav-item {{ request()->routeIs('child-profile') ? 'active' : '' }}" onclick="setActive(this)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                <circle cx="12" cy="7" r="4" />
            </svg>
         Child Profile
        </a>
        <a href="{{ route('growth-signal') }}" class="nav-item {{ request()->routeIs('growth-signal') ? 'active' : '' }}" onclick="setActive(this)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/>
                <polyline points="16 7 22 7 22 13"/>
            </svg>
            Growth Signals
        </a>
        <a href="{{ route('check-in') }}" class="nav-item {{ request()->routeIs('check-in') ? 'active' : '' }}" onclick="setActive(this)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <polyline points="9 11 11 13 15 9"/>
            </svg>
            Quarterly Check-in
        </a>
        <a href="{{ route('order') }}" class="nav-item {{ request()->routeIs('order') ? 'active' : '' }}" onclick="setActive(this)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z" />
                <line x1="3" y1="6" x2="21" y2="6" />
                <path d="M16 10a4 4 0 0 1-8 0" />
            </svg>
            Order <span class="nbadge">4</span>
        </a>
    </div>
    <div class="nav-section">
        <span class="nav-label">Account</span>
        <a href="{{ route('personal-info') }}" class="nav-item {{ request()->routeIs('personal-info') ? 'active' : '' }}" onclick="setActive(this)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                <circle cx="12" cy="7" r="4" />
            </svg>
            Personal Info
        </a>
        <a href="#" class="nav-item" onclick="setActive(this)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                <circle cx="12" cy="10" r="3" />
            </svg>
            Address
        </a>
        <a href="#" class="nav-item" onclick="setActive(this)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
            </svg>
            Wishlist
        </a>
        <a href="{{ route('change-password') }}" class="nav-item {{ request()->routeIs('change-password') ? 'active' : '' }}" onclick="setActive(this)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
            </svg>
            Change Password
        </a>
        <a href="{{ route('subscription') }}" class="nav-item {{ request()->routeIs('subscription') ? 'active' : '' }}" onclick="setActive(this)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
            </svg>
            Subscription
        </a>
        <a href="#" class="nav-item" onclick="setActive(this)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <rect x="1" y="4" width="22" height="16" rx="2" ry="2" />
                <line x1="1" y1="10" x2="23" y2="10" />
            </svg>
            My Coupons
        </a>
    </div>
</div>
<div class="sidebar-footer">
    <button class="logout-btn">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
            <polyline points="16 17 21 12 16 7" />
            <line x1="21" y1="12" x2="9" y2="12" />
        </svg>
        Logout
    </button>
</div>
