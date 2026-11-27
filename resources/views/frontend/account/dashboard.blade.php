@extends('layouts.user-panel')
@section('title', 'Dashboard — NutriBuddy Kids')
@section('panel-page-class', 'panel-userdashboard')

@section('panel-content')
  <div class="ud-main">
    {{-- ══ MOBILE TOPBAR ══ --}}
    <div class="page">

      <!-- WELCOME BANNER -->
      <div class="welcome-banner d1">
        <div class="welcome-text" style="position:relative;z-index:1">
          <h2>Welcome back, <span>{{ Auth::user()->name }}!</span> </h2>
          <p id="udWelcomeLine">Here's a quick overview of your account activity.<br>You have <strong style="color:var(--ye)">0 pending
              orders</strong> awaiting delivery.</p>
        </div>
        <div class="welcome-right">
          <div class="banner-stat">
            <div class="bs-num" id="udTotalSpent">₹0</div>
            <div class="bs-lbl">Total Spent</div>
          </div>
          <div class="banner-stat">
            <div class="bs-num" id="udOrdersCount">0</div>
            <div class="bs-lbl">Orders</div>
          </div>
          <div class="banner-emoji"></div>
        </div>
      </div>

      <!-- STAT CARDS -->
      <div class="stats-grid">
        <div class="stat-card d2">
          <div class="sc-icon" style="background:var(--mnl)">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--mn)" stroke-width="2.2">
              <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z" />
              <line x1="3" y1="6" x2="21" y2="6" />
              <path d="M16 10a4 4 0 0 1-8 0" />
            </svg>
          </div>
          <div class="sc-info">
            <div class="num" id="udStatTotal">0</div>
            <div class="lbl">Total Orders</div>
          </div>
        </div>
        <div class="stat-card d3">
          <div class="sc-icon" style="background:var(--skl)">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--sk)" stroke-width="2.2">
              <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
              <polyline points="22 4 12 14.01 9 11.01" />
            </svg>
          </div>
          <div class="sc-info">
            <div class="num" id="udStatCompleted">0</div>
            <div class="lbl">Completed</div>
          </div>
        </div>
        <div class="stat-card d4">
          <div class="sc-icon" style="background:var(--yel)">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2.2">
              <circle cx="12" cy="12" r="10" />
              <polyline points="12 6 12 12 16 14" />
            </svg>
          </div>
          <div class="sc-info">
            <div class="num" id="udStatPending">0</div>
            <div class="lbl">Pending</div>
          </div>
        </div>
        <div class="stat-card d2">
          <div class="sc-icon" style="background:#ffe4e6">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#e11d48" stroke-width="2.2">
              <circle cx="12" cy="12" r="10" />
              <line x1="15" y1="9" x2="9" y2="15" />
              <line x1="9" y1="9" x2="15" y2="15" />
            </svg>
          </div>
          <div class="sc-info">
            <div class="num" id="udStatCancelled">0</div>
            <div class="lbl">Cancelled</div>
          </div>
        </div>
        <div class="stat-card d3" style="display:none;">
          <div class="sc-icon" style="background:var(--pkl)">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--pk)" stroke-width="2.2">
              <path
                d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
            </svg>
          </div>
          <div class="sc-info">
            <div class="num">0</div>
            <div class="lbl">Wishlist</div>
          </div>
        </div>
        <div class="stat-card d4">
          <div class="sc-icon" style="background:var(--pul)">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--pu)" stroke-width="2.2">
              <polygon
                points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
            </svg>
          </div>
          <div class="sc-info">
            <div class="num">{{ $reviewCount }}</div>
            <div class="lbl">Reviews</div>
          </div>
        </div>
      </div>

      <!-- QUICK ACTIONS -->
      <div class="qa-grid">
        <a href="{{ route('product') }}" class="qa-btn d3">
          <div class="qi" style="background:var(--pkl);color:var(--pk)">
            <svg class="qa-icon" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
              <circle cx="9" cy="21" r="1" />
              <circle cx="20" cy="21" r="1" />
              <path d="M1 1h4l2.7 13.4a2 2 0 0 0 2 1.6h9.8a2 2 0 0 0 2-1.6L23 6H6" />
            </svg>
          </div>
          <span>Shop Now</span>
        </a>
        <a href="{{ route('order') }}" class="qa-btn d4">
          <div class="qi" style="background:var(--mnl);color:var(--mn)">
            <svg class="qa-icon" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
              <path d="m12 3 9 5-9 5-9-5 9-5Z" />
              <path d="M3 8v9l9 5 9-5V8M12 13v9M7.5 5.5l9 5" />
            </svg>
          </div>
          <span>Track Order</span>
        </a>
        <a href="{{ route('my-coupons') }}" class="qa-btn d5">
          <div class="qi" style="background:var(--pul);color:var(--pu)">
            <svg class="qa-icon" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
              <path d="M3 5h18v4a3 3 0 0 0 0 6v4H3v-4a3 3 0 0 0 0-6V5Z" />
              <path d="M13 5v2m0 4v2m0 4v2" />
            </svg>
          </div>
          <span>My Coupons</span>
        </a>
        <a href="{{ route('meal-plan') }}" class="qa-btn d6">
          <div class="qi" style="background:var(--yel);color:#ca8a04">
            <svg class="qa-icon" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
              <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
              <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z" />
            </svg>
          </div>
          <span>Diet Chart</span>
        </a>
      </div>

      <!-- BOTTOM GRID -->
      <div class="bottom-grid">

        <!-- RECENT ORDERS -->
        <div class="box">
          <div class="box-head">
            <h3>📋 Recent Orders</h3>
            <a href="{{ route('order') }}" class="view-all">View All</a>
          </div>
          <div style="overflow-x:auto">
            <table class="orders-table">
              <thead>
                <tr>
                  <th>Order #</th>
                  <th>Date</th>
                  <th>Amount</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody id="udRecentOrdersBody"></tbody>
            </table>
          </div>
        </div>

        <!-- RIGHT COLUMN -->
        <div style="display:flex;flex-direction:column;gap:16px">
          <!-- Assessment Widget -->
          @php $latestAttempt = auth()->user()->assessmentAttempts()->latest()->first(); @endphp
          <div class="progress-card" onclick="window.location.href='{{ route('user.assessment.index') }}'" style="cursor: pointer; background: linear-gradient(135deg, var(--pkl) 0%, var(--pul) 100%);">
            <h4 style="color: var(--pu);">🩺 Health Score</h4>
            @if($latestAttempt)
                <div style="font-family: 'Fredoka One', cursive; font-size: 1.8rem; color: var(--pu); margin: 8px 0;">
                    {{ number_format($latestAttempt->percentage, 0) }}%
                </div>
                <p style="font-size: 0.8rem; color: var(--text-light);">{{ $latestAttempt->result_level }} (Latest)</p>
                <div class="progress-bar" style="height: 6px; margin-top: 10px;">
                  <div class="progress-fill" style="width:{{ $latestAttempt->percentage }}%; background: var(--pu);"></div>
                </div>
            @else
                <div style="font-family: 'Fredoka One', cursive; font-size: 1.2rem; color: var(--pu); margin: 8px 0;">
                    Not Taken Yet
                </div>
                <p style="font-size: 0.8rem; color: var(--text-light);">Take your first assessment now!</p>
                <a href="{{ route('user.assessment.index') }}" class="assessment-cta">Take Assessment <span aria-hidden="true">&rarr;</span></a>
            @endif
          </div>
          <!-- Loyalty -->
          <div class="progress-card" onclick="window.location.href='{{ route('wallet') }}'" style="cursor: pointer;">
            <h4>NB Coins Wallet 🪙</h4>
            <div style="font-family: 'Fredoka One', cursive; font-size: 1.8rem; color: var(--or); margin: 8px 0;">
                {{ auth()->user()->coins_balance }}
            </div>
            <p style="font-size: 0.8rem; color: var(--text-light);">Available for your next discount!</p>
            <div class="progress-bar" style="height: 6px; margin-top: 10px;">
              <div class="progress-fill" style="width:{{ min(100, (auth()->user()->coins_balance / 1000) * 100) }}%; background: var(--or);"></div>
            </div>
            <div class="progress-labels"><span>0</span><span>Next goal: 1000</span></div>
          </div>
          <!-- Reviews -->
          <div class="box" style="flex:1">
            <div class="box-head">
              <h3>Recent Reviews</h3>
              <a href="{{ route('user.reviews.index') }}" class="view-all">See All</a>
            </div>
            <div class="reviews-list">
              @forelse($recentReviews as $review)
                <div class="review-item">
                  <div class="review-avatar" style="background:{{ $loop->odd ? 'var(--mnl)' : 'var(--skl)' }}">
                    @if($review->product?->primaryImage)
                      <img src="{{ asset('storage/' . $review->product->primaryImage->image_path) }}" alt="" loading="lazy" style="width:100%;height:100%;object-fit:cover;border-radius:inherit">
                    @else
                      {{ strtoupper(substr($review->product?->name ?? 'P', 0, 1)) }}
                    @endif
                  </div>
                  <div>
                    <div class="rp">{{ $review->product?->name ?? 'Product unavailable' }}</div>
                    <div class="stars" role="img" aria-label="{{ $review->rating }} out of 5 stars">@for($star = 1; $star <= 5; $star++)@if($star <= $review->rating)&#9733;@else&#9734;@endif @endfor</div>
                    @if($review->comment)
                      <div class="rtxt">{{ $review->comment }}</div>
                    @endif
                  </div>
                </div>
              @empty
                <div style="padding:16px 20px;text-align:center;font-size:.82rem;color:var(--muted)">
                  You have not reviewed a product from your orders yet.
                  <a href="{{ route('user.reviews.index') }}" style="color:var(--pk);font-weight:700">Review an ordered product</a>
                </div>
              @endforelse
            </div>
          </div>
        </div>

      </div><!-- /bottom-grid -->

    </div><!-- /page -->
  </div><!-- /ud-main -->

  @push('scripts')
    <script>
      </script>
  @endpush
@endsection

@push('scripts')
<script>
  (function () {
    const ordersUrl = @json(route('user.orders.index'));

    function money(value) {
      return `₹${Number(value || 0).toLocaleString('en-IN', { maximumFractionDigits: 0 })}`;
    }

    function dateText(value) {
      const dt = new Date(value);
      return Number.isNaN(dt.getTime()) ? '-' : dt.toLocaleDateString('en-IN', { day:'2-digit', month:'short', year:'numeric' });
    }

    function badgeClass(status) {
      if (status === 'delivered') return 's-delivered';
      if (status === 'cancelled') return 's-cancelled';
      return 's-pending';
    }

    async function loadDashboardOrders() {
      const res = await fetch(ordersUrl, { headers: { 'Accept': 'application/json' } });
      if (res.status === 401 || res.status === 419) {
        window.location.href = '/login';
        return;
      }
      if (!res.ok) return;

      const payload = await res.json();
      const orders = payload.data || [];

      const total = orders.length;
      const completed = orders.filter(o => o.status === 'delivered').length;
      const cancelled = orders.filter(o => o.status === 'cancelled').length;
      const pending = orders.filter(o => ['pending','confirmed','processing','packed','shipped'].includes(o.status)).length;
      const totalSpent = orders.reduce((sum, o) => sum + Number(o.grand_total || 0), 0);

      document.getElementById('udOrdersCount').textContent = total;
      document.getElementById('udStatTotal').textContent = total;
      document.getElementById('udStatCompleted').textContent = completed;
      document.getElementById('udStatPending').textContent = pending;
      document.getElementById('udStatCancelled').textContent = cancelled;
      document.getElementById('udTotalSpent').textContent = money(totalSpent);
      document.getElementById('udWelcomeLine').innerHTML =
        `Here's a quick overview of your account activity.<br>You have <strong style="color:var(--ye)">${pending} pending orders</strong> awaiting delivery.`;

      const body = document.getElementById('udRecentOrdersBody');
      body.innerHTML = '';
      orders.slice(0, 4).forEach(o => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td><span class="order-id">${o.order_number}</span></td>
          <td>${dateText(o.placed_at || o.created_at)}</td>
          <td><strong>${money(o.grand_total)}</strong></td>
          <td><span class="status-badge ${badgeClass(o.status)}">${String(o.status || '').toUpperCase()}</span></td>
        `;
        body.appendChild(tr);
      });

      if (!orders.length) {
        body.innerHTML = `<tr><td colspan="4" style="padding:14px 10px;color:var(--muted)">No orders yet.</td></tr>`;
      }
    }

    document.addEventListener('DOMContentLoaded', loadDashboardOrders);
  })();
</script>
@endpush
