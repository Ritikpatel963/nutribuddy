@extends('layouts.user-panel')
@section('title', 'Dashboard — NutriBuddy Kids')
@section('panel-page-class', 'panel-userdashboard')

@section('panel-content')
  <div class="ud-main">

    <!-- mobile topbar with sidebar toggle -->
    <div class="inner-topbar">
      <button class="sidebar-toggle" onclick="toggleSidebar()">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
          <line x1="3" y1="6" x2="21" y2="6" />
          <line x1="3" y1="12" x2="21" y2="12" />
          <line x1="3" y1="18" x2="21" y2="18" />
        </svg>
      </button>
      <span class="it-title">Overview </span>
      <div style="width:36px"></div><!-- spacer -->
    </div>

    <div class="page">

      <!-- WELCOME BANNER -->
      <div class="welcome-banner d1">
        <div class="welcome-text" style="position:relative;z-index:1">
          <h2>Welcome back, <span>{{ Auth::user()->name }}!</span> 👋</h2>
          <p>Here's a quick overview of your account activity.<br>You have <strong style="color:var(--ye)">2 pending
              orders</strong> awaiting delivery.</p>
        </div>
        <div class="welcome-right">
          <div class="banner-stat">
            <div class="bs-num">₹4,112</div>
            <div class="bs-lbl">Total Spent</div>
          </div>
          <div class="banner-stat">
            <div class="bs-num">4</div>
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
            <div class="num">4</div>
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
            <div class="num">1</div>
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
            <div class="num">2</div>
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
            <div class="num">1</div>
            <div class="lbl">Cancelled</div>
          </div>
        </div>
        <div class="stat-card d3">
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
            <div class="num">0</div>
            <div class="lbl">Reviews</div>
          </div>
        </div>
      </div>

      <!-- QUICK ACTIONS -->
      <div class="qa-grid">
        <a href="#" class="qa-btn d3">
          <div class="qi" style="background:var(--pkl)">🛒</div>
          <span>Shop Now</span>
        </a>
        <a href="#" class="qa-btn d4">
          <div class="qi" style="background:var(--mnl)">📦</div>
          <span>Track Order</span>
        </a>
        <a href="#" class="qa-btn d5">
          <div class="qi" style="background:var(--pul)">🎟️</div>
          <span>My Coupons</span>
        </a>
        <a href="#" class="qa-btn d6">
          <div class="qi" style="background:var(--yel)"></div>
          <span>Diet Chart</span>
        </a>
      </div>

      <!-- BOTTOM GRID -->
      <div class="bottom-grid">

        <!-- RECENT ORDERS -->
        <div class="box">
          <div class="box-head">
            <h3>📋 Recent Orders</h3>
            <a href="#" class="view-all">View All</a>
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
              <tbody>
                <tr>
                  <td><span class="order-id">ORD-1774942041747</span></td>
                  <td>31 Mar 2026</td>
                  <td><strong>₹2,828</strong></td>
                  <td><span class="status-badge s-pending">PENDING</span></td>
                </tr>
                <tr>
                  <td><span class="order-id">ORD-1774668404749</span></td>
                  <td>28 Mar 2026</td>
                  <td><strong>₹588</strong></td>
                  <td><span class="status-badge s-pending">PENDING</span></td>
                </tr>
                <tr>
                  <td><span class="order-id">ORD-1774331569477</span></td>
                  <td>24 Mar 2026</td>
                  <td><strong>₹472</strong></td>
                  <td><span class="status-badge s-delivered">DELIVERED</span></td>
                </tr>
                <tr>
                  <td><span class="order-id">ORD-1774331118451</span></td>
                  <td>24 Mar 2026</td>
                  <td><strong>₹224</strong></td>
                  <td><span class="status-badge s-cancelled">CANCELLED</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- RIGHT COLUMN -->
        <div style="display:flex;flex-direction:column;gap:16px">
          <!-- Loyalty -->
          <div class="progress-card">
            <h4>NutriBuddy Loyalty</h4>
            <p>You're 1 order away from Gold status!</p>
            <div class="progress-bar">
              <div class="progress-fill" style="width:75%"></div>
            </div>
            <div class="progress-labels"><span>Silver</span><span>75% to Gold</span></div>
          </div>
          <!-- Reviews -->
          <div class="box" style="flex:1">
            <div class="box-head">
              <h3>Recent Reviews</h3>
              <a href="#" class="view-all">See All</a>
            </div>
            <div class="reviews-list">
              <div class="review-item">
                <div class="review-avatar" style="background:var(--mnl)"></div>
                <div>
                  <div class="rp">Immunity Gummies</div>
                  <div class="stars">★★★★★</div>
                  <div class="rtxt">My son loves them! Noticeable difference in 3 weeks.</div>
                </div>
              </div>
              <div class="review-item">
                <div class="review-avatar" style="background:var(--skl)">🧠</div>
                <div>
                  <div class="rp">Brain Boost Chews</div>
                  <div class="stars">★★★★☆</div>
                  <div class="rtxt">Great taste, my daughter asks for it daily!</div>
                </div>
              </div>
              <div style="padding:14px 20px;text-align:center;font-size:.78rem;color:var(--muted)">
                No more reviews yet — <a href="#" style="color:var(--pk);font-weight:700">write one!</a>
              </div>
            </div>
          </div>
        </div>

      </div><!-- /bottom-grid -->

      <!-- add new diet section -->
      <!-- Header -->
      <div class="header">
        <div>
          <h1 class="greeting">Good morning, Priya </h1>
          <p class="sub-info">
            Thursday
            <span class="sep">·</span>
            🌤️ Greeshma season
            <span class="sep">·</span>
            Summer cooling plan active
          </p>
        </div>
        <div>
          <a href="/check-in">
          <button class="btn-quarterly">Quarterly check-in</button>
        </a></div>
      </div>

      <!-- Season Banner -->
      <div class="season-banner">
        <div class="season-icon">☀️</div>
        <div>
          <div class="season-label">Greeshma — Summer Season Plan (May–June)</div>
          <div class="season-tags">
            <span class="season-tag">🥥 Cooling foods</span>
            <span class="season-tag">🥤 Coconut water 2× daily</span>
            <span class="season-tag">🍚 Curd rice</span>
            <span class="season-tag">🥗 Light meals</span>
            <span class="season-tag">🌙 Early dinner</span>
          </div>
        </div>
      </div>

      <!-- Grid -->
      <div class="grid">

        <!-- Left: Today's Meals -->
        <div class="card">
          <div class="card-header">
            <span class="card-title">Today's meals</span>
            <a href="#" class="card-link">Full week →</a>
          </div>
          <ul class="meal-list" style="list-style:none;">
            <li class="meal-item">
              <div class="time-chip">7 AM</div>
              <div class="meal-icon">🥛</div>
              <div class="meal-info">
                <div class="meal-name">Turmeric milk</div>
                <div class="meal-meta">Calcium · Vit D · Curcumin</div>
              </div>
            </li>
            <li class="meal-item">
              <div class="time-chip">8 AM</div>
              <div class="meal-icon">🫐</div>
              <div class="meal-info">
                <div class="meal-name">Ragi porridge + banana</div>
                <div class="meal-meta">Calcium 344 mg · Iron 3.9 mg</div>
              </div>
            </li>
            <li class="meal-item">
              <div class="time-chip">10:30</div>
              <div class="meal-icon">🍃</div>
              <div class="meal-info">
                <div class="meal-name">Guava + peanut pack</div>
                <div class="meal-meta">Vit C 228 mg — triples iron</div>
              </div>
            </li>
            <li class="meal-item">
              <div class="time-chip">1 PM</div>
              <div class="meal-icon">🍛</div>
              <div class="meal-info">
                <div class="meal-name">Palak dal + chapati + curd</div>
                <div class="meal-meta">Lemon off-heat for 3× iron</div>
              </div>
            </li>
          </ul>
          <div class="see-all-wrap">
            <a href="{{ route('meal-plan') }}">
              <button class="btn-see-all" style="cursor: pointer;">See all 6 meals + full week →</button>
            </a>
          </div>
        </div>

        <!-- Right: Health Score -->
        <div class="card">
          <div class="card-header">
            <span class="card-title">Arjun's Health Score</span>
            <a href="#" class="card-link">Details →</a>
          </div>
          <div class="score-body">
            <div class="score-top">
              <!-- Ring -->
              <div class="score-ring-wrap">
                <svg viewBox="0 0 100 100" width="100" height="100">
                  <defs>
                    <linearGradient id="ringGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                      <stop offset="0%" stop-color="var(--pu)" />
                      <stop offset="100%" stop-color="var(--pk)" />
                    </linearGradient>
                  </defs>
                  <circle class="ring-bg" cx="50" cy="50" r="40" />
                  <circle class="ring-fill" cx="50" cy="50" r="40" />
                </svg>
                <div class="score-number">
                  <span class="score-val">75</span>
                  <span class="score-denom">/100</span>
                </div>
              </div>

              <!-- Labels -->
              <div class="score-info">
                <div class="score-progress-label">
                  <span class="up-arrow">↑</span>
                  Great progress! <strong>+21 pts</strong> in 90 days
                </div>
                <div class="bar-group">
                  <!-- Immunity -->
                  <div class="bar-row">
                    <div class="bar-label-row">
                      <span class="bar-name">Immunity</span>
                      <span class="bar-pct immunity">87%</span>
                    </div>
                    <div class="bar-track">
                      <div class="bar-fill immunity" style="width:87%"></div>
                    </div>
                  </div>
                  <!-- Growth -->
                  <div class="bar-row">
                    <div class="bar-label-row">
                      <span class="bar-name">Growth</span>
                      <span class="bar-pct growth">79%</span>
                    </div>
                    <div class="bar-track">
                      <div class="bar-fill growth" style="width:79%"></div>
                    </div>
                  </div>
                  <!-- Brain -->
                  <div class="bar-row">
                    <div class="bar-label-row">
                      <span class="bar-name">Brain</span>
                      <span class="bar-pct brain">72%</span>
                    </div>
                    <div class="bar-track">
                      <div class="bar-fill brain" style="width:72%"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Check-in notice -->
            <div class="checkin-notice">
              <div class="notice-icon">📋</div>
              <div>
                <div class="notice-title">Check-in due</div>
                <div class="notice-desc">Update Arjun's weight &amp; height to refresh scores.</div>
              </div>
            </div>
          </div>
        </div>

      </div><!-- /grid -->

    </div><!-- /page -->
  </div><!-- /ud-main -->

  @push('scripts')
    <script>
      function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('open');
        document.getElementById('overlay').classList.toggle('show');
      }

      function closeSidebar() {
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('overlay').classList.remove('show');
      }

      function setActive(el) {
        document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
        el.classList.add('active');
        // close sidebar on mobile after nav click
        if (window.innerWidth <= 900) closeSidebar();
      }
    </script>
  @endpush
@endsection