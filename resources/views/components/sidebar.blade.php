<aside class="sidebar">
    <button type="button" class="sidebar-close-btn">
        <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
    </button>
    <div>
        <a href="{{ route('index') }}" class="sidebar-logo">
            <img src="{{ asset('assets/images/logo.png') }}" alt="site logo" class="light-logo">
            <img src="{{ asset('assets/images/logo-light.png') }}" alt="site logo" class="dark-logo">
            <img src="{{ asset('assets/images/logo-icon.png') }}" alt="site logo" class="logo-icon">
        </a>
    </div>
    <div class="sidebar-menu-area">
        <ul class="sidebar-menu" id="sidebar-menu">
            {{-- <li class="dropdown">
                <a  href="javascript:void(0)">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="menu-icon"></iconify-icon>
                    <span>Dashboard</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('index') }}"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> eCommerce</a>
                    </li>
                    <li>
                    <a href="{{ route('index2') }}"><i class="ri-circle-fill circle-icon text-warning-main w-auto"></i> CRM</a>
                    </li>
                    <li>
                    <a href="{{ route('index3') }}"><i class="ri-circle-fill circle-icon text-info-main w-auto"></i> AI</a>
                    </li>
                    <li>
                    <a href="{{ route('index4') }}"><i class="ri-circle-fill circle-icon text-danger-main w-auto"></i> Cryptocurrency</a>
                    </li>
                    <li>
                    <a href="{{ route('index5') }}"><i class="ri-circle-fill circle-icon text-success-main w-auto"></i> Investment</a>
                    </li>
                    <li>
                    <a href="{{ route('index6') }}"><i class="ri-circle-fill circle-icon text-purple w-auto"></i> LMS</a>
                    </li>
                    <li>
                    <a href="{{ route('index7') }}"><i class="ri-circle-fill circle-icon text-info-main w-auto"></i> NFT & Gaming</a>
                    </li>
                    <li>
                    <a href="{{ route('index8') }}"><i class="ri-circle-fill circle-icon text-danger-main w-auto"></i> Medical</a>
                    </li>
                    <li>
                    <a href="{{ route('index9') }}"><i class="ri-circle-fill circle-icon text-purple w-auto"></i> Analytics</a>
                    </li>
                    <li>
                    <a href="{{ route('index10') }}"><i class="ri-circle-fill circle-icon text-warning-main w-auto"></i> POS & Inventory </a>
                    </li>
                </ul>
            </li> --}}
            <li>
                <a href="{{ route('index') }}">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="menu-icon"></iconify-icon>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="solar:box-outline" class="menu-icon"></iconify-icon>
                    <span>Products Management</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('admin.ecommerce.categories.index') }}"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Categories</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.ecommerce.products.index') }}"><i class="ri-circle-fill circle-icon text-warning-main w-auto"></i> Products</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.ecommerce.ingredient-categories.index') }}"><i class="ri-circle-fill circle-icon text-success-main w-auto"></i> Ingredient Categories</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.ecommerce.ingredients.index') }}"><i class="ri-circle-fill circle-icon text-danger-main w-auto"></i> Ingredients</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.ecommerce.variants.index') }}"><i class="ri-circle-fill circle-icon text-lilac-600 w-auto"></i> Variants</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.ecommerce.tax-rates.index') }}"><i class="ri-circle-fill circle-icon text-info-main w-auto"></i> Tax Rates</a>
                    </li>
                </ul>
            </li>

            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="solar:cart-large-minimalistic-outline" class="menu-icon"></iconify-icon>
                    <span>Orders & Sales</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('admin.ecommerce.orders.index') }}"><i class="ri-circle-fill circle-icon text-success-main w-auto"></i> Orders</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.ecommerce.order-returns.index') }}"><i class="ri-circle-fill circle-icon text-danger-main w-auto"></i> Order Returns</a>
                    </li>
                </ul>
            </li>

            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="solar:users-group-two-rounded-outline" class="menu-icon"></iconify-icon>
                    <span>Customer Relations</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('admin.ecommerce.customers.index') }}"><i class="ri-circle-fill circle-icon text-info-600 w-auto"></i> Customers</a>
                    </li>
                </ul>
            </li>

            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="solar:tag-outline" class="menu-icon"></iconify-icon>
                    <span>Promotions</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('admin.ecommerce.coupons.index') }}"><i class="ri-circle-fill circle-icon text-danger-main w-auto"></i> Coupons</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.ecommerce.newsletter.index') }}"><i class="ri-circle-fill circle-icon text-cyan w-auto"></i> Newsletter</a>
                    </li>
                </ul>
            </li>

            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="solar:star-outline" class="menu-icon"></iconify-icon>
                    <span>Moderation</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('admin.ecommerce.reviews.index') }}"><i class="ri-circle-fill circle-icon text-warning-main w-auto"></i> Reviews & Ratings</a>
                    </li>
                </ul>
            </li>

            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="solar:tuning-square-2-outline" class="menu-icon"></iconify-icon>
                    <span>Administration</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('admin.ecommerce.notifications.index') }}"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Notifications</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.ecommerce.support-tickets.index') }}"><i class="ri-circle-fill circle-icon text-pink w-auto"></i> Support Tickets</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.ecommerce.contact-leads.index') }}"><i class="ri-circle-fill circle-icon text-orange w-auto"></i> Contact Leads</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.ecommerce.settings.general') }}"><i class="ri-circle-fill circle-icon text-info-main w-auto"></i> Site Settings</a>
                    </li>
                </ul>
            </li>

            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="solar:document-text-outline" class="menu-icon"></iconify-icon>
                    <span>Content Management</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('admin.ecommerce.blog-categories.index') }}"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Blog Categories</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.ecommerce.blog-posts.index') }}"><i class="ri-circle-fill circle-icon text-warning-main w-auto"></i> Blog Posts</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="javascript:void(0)" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <iconify-icon icon="lucide:power" class="menu-icon"></iconify-icon>
                    <span>Log Out</span>
                </a>
            </li>
        </ul>
    </div>
</aside>
