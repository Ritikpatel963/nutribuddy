@extends('layouts.user-panel')
@section('title', 'Return Policy — NutriBuddy Kids')
@section('panel-page-class', 'panel-user-return')

@section('panel-content')

    <div class="inner-topbar">
        <button class="sidebar-toggle" onclick="toggleSidebar()">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <line x1="3" y1="6" x2="21" y2="6" />
                <line x1="3" y1="12" x2="21" y2="12" />
                <line x1="3" y1="18" x2="21" y2="18" />
            </svg>
        </button>
        <span class="it-title">Return Policy 📦</span>
        <div style="width:36px"></div>
    </div>

    <!-- MAIN -->
    <div class="main">


      <div class="page">

        <!-- WELCOME BANNER -->
        <div class="welcome-banner d1">
          <div class="welcome-text" style="position:relative;z-index:1">
            <h2>Welcome back, <span>Jaydafsdf!</span> 👋</h2>
            <p>Check your return policy and recent support updates here.</p>
          </div>
          <div class="welcome-right">
            <div class="banner-stat">
              <div class="bs-num">7</div>
              <div class="bs-lbl">Days Return</div>
            </div>
            <div class="banner-stat">
              <div class="bs-num">24/7</div>
              <div class="bs-lbl">Support</div>
            </div>
            <div class="banner-emoji">📦</div>
          </div>
        </div>

        <!-- HERO -->
        <div class="policy-hero fade-in d1">
          <div class="hero-text">
            <div class="badge">📦 Return & Refund Policy</div>
            <h1>7-Day <span>Return</span> Policy</h1>
            <p>Not satisfied? Return your product within <strong style="color:var(--ye)">7 days</strong> of delivery —
              no hassle, no questions asked.</p>
            <div class="timer-pills">
              <span class="tpill">📅 7-Day Window</span>
              <span class="tpill">💳 5–7 Day Refund</span>
              <span class="tpill">✅ Easy Process</span>
            </div>
          </div>
          <div class="hero-emoji">📦</div>
        </div>

        <!-- CONTENT GRID -->
        <div class="content-grid">

          <!-- LEFT -->
          <div>
            <!-- Eligible -->
            <div class="box fade-in d2">
              <div class="box-head">
                <div class="sec-label">Eligibility</div>
                <h2>Return Conditions</h2>
                <p>You can raise a return request within <strong>7 days</strong> of receiving your order. The following
                  conditions must be met.</p>
              </div>
              <div class="policy-list">
                <div class="policy-item">
                  <div class="pi-check" style="background:var(--mnl)">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="var(--mn)" stroke-width="3">
                      <polyline points="20 6 9 17 4 12" />
                    </svg>
                  </div>
                  <div class="pi-body">
                    <h4>Unused & Sealed</h4>
                    <p>Product must be unused, unopened, and in its original condition.</p>
                  </div>
                </div>
                <div class="policy-item">
                  <div class="pi-check" style="background:var(--mnl)">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="var(--mn)" stroke-width="3">
                      <polyline points="20 6 9 17 4 12" />
                    </svg>
                  </div>
                  <div class="pi-body">
                    <h4>Original Packaging & Invoice</h4>
                    <p>Return request requires the original box, packaging, and purchase invoice.</p>
                  </div>
                </div>
                <div class="policy-item">
                  <div class="pi-check" style="background:var(--mnl)">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="var(--mn)" stroke-width="3">
                      <polyline points="20 6 9 17 4 12" />
                    </svg>
                  </div>
                  <div class="pi-body">
                    <h4>Refund in 5–7 Working Days</h4>
                    <p>Once approved, refund is credited back to your original payment method.</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Not Eligible -->
            <div class="box fade-in d3">
              <div class="ne-head">
                <div
                  style="width:30px;height:30px;border-radius:9px;background:#ffe4e6;display:flex;align-items:center;justify-content:center;font-size:.95rem">
                  ❌</div>
                <h3>Not Eligible for Return</h3>
              </div>
              <div class="ne-item">
                <div class="ne-cross">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#e11d48" stroke-width="3">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                  </svg>
                </div>
                <div class="ne-body">
                  <h4>Opened or Used Products</h4>
                  <p>Products that have been opened, consumed, or tampered with cannot be returned.</p>
                </div>
              </div>
              <div class="ne-item">
                <div class="ne-cross">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#e11d48" stroke-width="3">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                  </svg>
                </div>
                <div class="ne-body">
                  <h4>Requests After 7 Days</h4>
                  <p>Return requests raised after 7 days of delivery will not be accepted.</p>
                </div>
              </div>
              <div class="ne-item">
                <div class="ne-cross">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#e11d48" stroke-width="3">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                  </svg>
                </div>
                <div class="ne-body">
                  <h4>Missing Packaging or Invoice</h4>
                  <p>Returns without the original box or invoice cannot be processed.</p>
                </div>
              </div>
            </div>
          </div>

          <!-- RIGHT -->
          <div class="right-col fade-in d4">

            <!-- Steps -->
            <div class="side-card">
              <div class="sc-head">
                <div
                  style="width:28px;height:28px;border-radius:9px;background:var(--pkl);display:flex;align-items:center;justify-content:center;font-size:.9rem">
                  📋</div>
                <h3>How to Return?</h3>
              </div>
              <div class="sc-body">
                <div class="step-list">
                  <div class="step-item">
                    <div class="step-num">1</div>
                    <div class="step-txt">
                      <h4>Go to My Orders</h4>
                      <p>Find the order you'd like to return.</p>
                    </div>
                  </div>
                  <div class="step-item">
                    <div class="step-num">2</div>
                    <div class="step-txt">
                      <h4>Submit Return Request</h4>
                      <p>Click "View" and raise a return with your reason.</p>
                    </div>
                  </div>
                  <div class="step-item">
                    <div class="step-num">3</div>
                    <div class="step-txt">
                      <h4>Pack the Product</h4>
                      <p>Repack carefully in the original packaging.</p>
                    </div>
                  </div>
                  <div class="step-item">
                    <div class="step-num">4</div>
                    <div class="step-txt">
                      <h4>Pickup & Refund</h4>
                      <p>We'll schedule pickup and process your refund.</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Timeline -->
            <div class="side-card">
              <div class="sc-head">
                <div
                  style="width:28px;height:28px;border-radius:9px;background:var(--mnl);display:flex;align-items:center;justify-content:center;font-size:.9rem">
                  ⏱️</div>
                <h3>Refund Timeline</h3>
              </div>
              <div class="sc-body">
                <div class="timeline">
                  <div class="tl-item">
                    <div class="tl-left">
                      <div class="tl-dot" style="background:var(--pkl)">📨</div>
                      <div class="tl-line"></div>
                    </div>
                    <div class="tl-content">
                      <h4>Request Submitted</h4>
                      <p>Return request raised successfully</p><span class="day-badge">Day 0</span>
                    </div>
                  </div>
                  <div class="tl-item">
                    <div class="tl-left">
                      <div class="tl-dot" style="background:var(--yel)">🔍</div>
                      <div class="tl-line"></div>
                    </div>
                    <div class="tl-content">
                      <h4>Review & Approval</h4>
                      <p>Our team reviews the request</p><span class="day-badge"
                        style="background:var(--yel);color:#92400e">Day 1–2</span>
                    </div>
                  </div>
                  <div class="tl-item">
                    <div class="tl-left">
                      <div class="tl-dot" style="background:var(--skl)">🚚</div>
                      <div class="tl-line"></div>
                    </div>
                    <div class="tl-content">
                      <h4>Product Pickup</h4>
                      <p>Courier pickup scheduled</p><span class="day-badge"
                        style="background:var(--skl);color:#0369a1">Day 2–3</span>
                    </div>
                  </div>
                  <div class="tl-item">
                    <div class="tl-left">
                      <div class="tl-dot" style="background:var(--mnl)">💳</div>
                    </div>
                    <div class="tl-content">
                      <h4>Refund Processed</h4>
                      <p>Credited to your account</p><span class="day-badge"
                        style="background:var(--mnl);color:#065f46">Day 5–7</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Contact -->
            <div class="contact-card fade-in d5">
              <span class="ci">🤝</span>
              <h3>Need Help?</h3>
              <p>Our support team is available 24/7 to assist you with any return or refund queries.</p>
              <a href="mailto:support@nutribuddy.in" class="contact-btn">💬 Contact Support</a>
            </div>

          </div>
        </div>

      </div>
    </div>

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
    }
  </script>

    @endpush
@endsection