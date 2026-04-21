@extends('layouts.user-panel')
@section('title', 'My Orders — NutriBuddy Kids')
@section('panel-page-class', 'panel-order')
@section('panel-content')
    <div class="inner-topbar">
        <button class="sidebar-toggle" onclick="toggleSidebar()">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <line x1="3" y1="6" x2="21" y2="6" />
                <line x1="3" y1="12" x2="21" y2="12" />
                <line x1="3" y1="18" x2="21" y2="18" />
            </svg>
        </button>
        <span class="it-title">My Orders 📋</span>
        <div style="width:36px"></div>
    </div>
    <!-- Wrapper removed to match ud-layout -->
        <!-- Cancel Confirm Modal -->
        <div class="modal-backdrop" id="cancelModal">
            <div class="modal">
                <h3>Cancel Order? 🤔</h3>
                <p>Are you sure you want to cancel order <strong id="cancelOrderId"></strong>? This action cannot be undone.
                </p>
                <div class="modal-btns">
                    <button class="m-btn-cancel" onclick="closeModal()">Keep Order</button>
                    <button class="m-btn-confirm" onclick="confirmCancel()">Yes, Cancel</button>
                </div>
            </div>
        </div>
        <!-- ════════════ MAIN ════════════ -->
            <div class="page">
               <!-- SUMMARY STRIP -->
                <div class="summary-strip fade-in d1">
                    <div class="sum-card">
                        <div class="sum-icon" style="background:var(--mnl)">📦</div>
                        <div>
                            <div class="s-num">4</div>
                            <div class="s-lbl">Total Orders</div>
                        </div>
                    </div>
                    <div class="sum-card">
                        <div class="sum-icon" style="background:var(--yel)">⏳</div>
                        <div>
                            <div class="s-num">2</div>
                            <div class="s-lbl">Pending</div>
                        </div>
                    </div>
                    <div class="sum-card">
                        <div class="sum-icon" style="background:var(--mnl)">✅</div>
                        <div>
                            <div class="s-num">1</div>
                            <div class="s-lbl">Delivered</div>
                        </div>
                    </div>
                    <div class="sum-card">
                        <div class="sum-icon" style="background:#ffe4e6">❌</div>
                        <div>
                            <div class="s-num">1</div>
                            <div class="s-lbl">Cancelled</div>
                        </div>
                    </div>
                </div>

                <!-- PAGE HEADER -->
                <div class="page-header fade-in d2">
                    <div class="page-header-left">
                        <h1>My Orders 📋</h1>
                        <p>Track and manage all your NutriBuddy purchases</p>
                    </div>
                    <div class="page-header-right">
                        <div class="search-bar">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5" color="var(--muted)">
                                <circle cx="11" cy="11" r="8" />
                                <line x1="21" y1="21" x2="16.65" y2="16.65" />
                            </svg>
                            <input type="text" placeholder="Search by order ID..." id="searchInput"
                                oninput="filterOrders()">
                        </div>
                        <div class="filter-tabs">
                            <button class="ftab active" onclick="filterTab(this,'all')">All (4)</button>
                            <button class="ftab" onclick="filterTab(this,'pending')">Pending (2)</button>
                            <button class="ftab" onclick="filterTab(this,'delivered')">Delivered (1)</button>
                            <button class="ftab" onclick="filterTab(this,'cancelled')">Cancelled (1)</button>
                        </div>
                    </div>
                </div>

                <!-- WELCOME BANNER -->
                <div class="welcome-banner d1">
                    <div class="welcome-text" style="position:relative;z-index:1">
                        <h2>Welcome back, <span>Jaydafsdf!</span> 👋</h2>
                        <p>Review your recent orders and track packages in one place.</p>
                    </div>
                    <div class="welcome-right">
                        <div class="banner-stat">
                            <div class="bs-num">4</div>
                            <div class="bs-lbl">Orders</div>
                        </div>
                        <div class="banner-stat">
                            <div class="bs-num">2</div>
                            <div class="bs-lbl">Pending</div>
                        </div>
                        <div class="banner-emoji">📦</div>
                    </div>
                </div>

                <!-- ORDERS TABLE (desktop) -->
                <div class="orders-card fade-in d3">
                    <div style="overflow-x:auto">
                        <table class="orders-table" id="ordersTable">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="ordersBody">
                                <tr data-status="pending">
                                    <td>
                                        <span class="order-id">ORD-1774942041747</span>
                                        <span class="order-product"> Immunity Gummies × 2</span>
                                    </td>
                                    <td>3/31/2026</td>
                                    <td class="amount-cell"><strong>₹2,828.46</strong><small>Prepaid</small></td>
                                    <td><span class="status-badge s-pending">PENDING</span></td>
                                    <td>
                                        <div class="actions-cell">
                                            <a href="#" class="act-btn act-view">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2.5">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                                    <circle cx="12" cy="12" r="3" />
                                                </svg>
                                                View
                                            </a>
                                            <button class="act-btn act-cancel"
                                                onclick="openCancelModal('ORD-1774942041747')">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2.5">
                                                    <line x1="18" y1="6" x2="6" y2="18" />
                                                    <line x1="6" y1="6" x2="18" y2="18" />
                                                </svg>
                                                Cancel
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr data-status="pending">
                                    <td>
                                        <span class="order-id">ORD-1774668404749</span>
                                        <span class="order-product">🧠 Brain Boost Chews × 1</span>
                                    </td>
                                    <td>3/28/2026</td>
                                    <td class="amount-cell"><strong>₹588.00</strong><small>Prepaid</small></td>
                                    <td><span class="status-badge s-pending">PENDING</span></td>
                                    <td>
                                        <div class="actions-cell">
                                            <a href="#" class="act-btn act-view">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2.5">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                                    <circle cx="12" cy="12" r="3" />
                                                </svg>
                                                View
                                            </a>
                                            <button class="act-btn act-cancel"
                                                onclick="openCancelModal('ORD-1774668404749')">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2.5">
                                                    <line x1="18" y1="6" x2="6" y2="18" />
                                                    <line x1="6" y1="6" x2="18" y2="18" />
                                                </svg>
                                                Cancel
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr data-status="delivered">
                                    <td>
                                        <span class="order-id">ORD-1774331569477</span>
                                        <span class="order-product">💊 Multi Vitamin × 1</span>
                                    </td>
                                    <td>3/24/2026</td>
                                    <td class="amount-cell"><strong>₹472.00</strong><small>COD</small></td>
                                    <td><span class="status-badge s-delivered">DELIVERED</span></td>
                                    <td>
                                        <div class="actions-cell">
                                            <a href="#" class="act-btn act-view">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2.5">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                                    <circle cx="12" cy="12" r="3" />
                                                </svg>
                                                View
                                            </a>
                                            <a href="#" class="act-btn act-review">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2.5">
                                                    <polygon
                                                        points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                                </svg>
                                                Review
                                            </a>
                                            <a href="#" class="act-btn act-reorder">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2.5">
                                                    <polyline points="23 4 23 10 17 10" />
                                                    <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10" />
                                                </svg>
                                                Reorder
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <tr data-status="cancelled">
                                    <td>
                                        <span class="order-id">ORD-1774331118451</span>
                                        <span class="order-product"> Ashwagandha Pack × 1</span>
                                    </td>
                                    <td>3/24/2026</td>
                                    <td class="amount-cell"><strong>₹224.20</strong><small>Prepaid · Refunded</small></td>
                                    <td><span class="status-badge s-cancelled">CANCELLED</span></td>
                                    <td>
                                        <div class="actions-cell">
                                            <a href="#" class="act-btn act-view">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2.5">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                                    <circle cx="12" cy="12" r="3" />
                                                </svg>
                                                View
                                            </a>
                                            <a href="#" class="act-btn act-reorder">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2.5">
                                                    <polyline points="23 4 23 10 17 10" />
                                                    <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10" />
                                                </svg>
                                                Reorder
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- MOBILE CARDS -->
                    <div class="mobile-orders" id="mobileOrders">
                        <!-- card 1 -->
                        <div class="m-order-card" data-status="pending">
                            <div class="m-card-top">
                                <div>
                                    <div class="m-id">ORD-1774942041747</div>
                                    <div class="m-date"> Immunity Gummies × 2 · 31 Mar 2026</div>
                                </div>
                                <span class="status-badge s-pending">PENDING</span>
                            </div>
                            <div class="m-card-mid">
                                <div class="m-amount">₹2,828.46</div>
                                <small style="color:var(--muted);font-size:.73rem">Prepaid</small>
                            </div>
                            <div class="m-card-actions">
                                <a href="#" class="act-btn act-view">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2.5">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>View
                                </a>
                                <button class="act-btn act-cancel" onclick="openCancelModal('ORD-1774942041747')">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2.5">
                                        <line x1="18" y1="6" x2="6" y2="18" />
                                        <line x1="6" y1="6" x2="18" y2="18" />
                                    </svg>Cancel
                                </button>
                            </div>
                        </div>
                        <!-- card 2 -->
                        <div class="m-order-card" data-status="pending">
                            <div class="m-card-top">
                                <div>
                                    <div class="m-id">ORD-1774668404749</div>
                                    <div class="m-date">🧠 Brain Boost Chews × 1 · 28 Mar 2026</div>
                                </div>
                                <span class="status-badge s-pending">PENDING</span>
                            </div>
                            <div class="m-card-mid">
                                <div class="m-amount">₹588.00</div>
                                <small style="color:var(--muted);font-size:.73rem">Prepaid</small>
                            </div>
                            <div class="m-card-actions">
                                <a href="#" class="act-btn act-view">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2.5">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>View
                                </a>
                                <button class="act-btn act-cancel" onclick="openCancelModal('ORD-1774668404749')">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2.5">
                                        <line x1="18" y1="6" x2="6" y2="18" />
                                        <line x1="6" y1="6" x2="18" y2="18" />
                                    </svg>Cancel
                                </button>
                            </div>
                        </div>
                        <!-- card 3 -->
                        <div class="m-order-card" data-status="delivered">
                            <div class="m-card-top">
                                <div>
                                    <div class="m-id">ORD-1774331569477</div>
                                    <div class="m-date">💊 Multi Vitamin × 1 · 24 Mar 2026</div>
                                </div>
                                <span class="status-badge s-delivered">DELIVERED</span>
                            </div>
                            <div class="m-card-mid">
                                <div class="m-amount">₹472.00</div>
                                <small style="color:var(--muted);font-size:.73rem">COD</small>
                            </div>
                            <div class="m-card-actions">
                                <a href="#" class="act-btn act-view"><svg width="13" height="13"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>View</a>
                                <a href="#" class="act-btn act-review"><svg width="13" height="13"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                        <polygon
                                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                    </svg>Review</a>
                                <a href="#" class="act-btn act-reorder"><svg width="13" height="13"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                        <polyline points="23 4 23 10 17 10" />
                                        <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10" />
                                    </svg>Reorder</a>
                            </div>
                        </div>
                        <!-- card 4 -->
                        <div class="m-order-card" data-status="cancelled">
                            <div class="m-card-top">
                                <div>
                                    <div class="m-id">ORD-1774331118451</div>
                                    <div class="m-date"> Ashwagandha Pack × 1 · 24 Mar 2026</div>
                                </div>
                                <span class="status-badge s-cancelled">CANCELLED</span>
                            </div>
                            <div class="m-card-mid">
                                <div class="m-amount">₹224.20</div>
                                <small style="color:var(--muted);font-size:.73rem">Prepaid · Refunded</small>
                            </div>
                            <div class="m-card-actions">
                                <a href="#" class="act-btn act-view"><svg width="13" height="13"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>View</a>
                                <a href="#" class="act-btn act-reorder"><svg width="13" height="13"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                        <polyline points="23 4 23 10 17 10" />
                                        <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10" />
                                    </svg>Reorder</a>
                            </div>
                        </div>
                    </div>

                    <!-- empty state (hidden by default) -->
                    <div class="empty-state" id="emptyState" style="display:none">
                        <span class="empty-icon">📭</span>
                        <h3>No orders found</h3>
                        <p>We couldn't find any orders matching your filter.</p>
                        <a href="#" class="shop-btn">🛒 Shop Now</a>
                    </div>

                    <!-- pagination -->
                    <div class="pagination">
                        <span class="pag-info">Showing 4 of 4 orders</span>
                        <div class="pag-btns">
                            <button class="pag-btn">‹</button>
                            <button class="pag-btn active">1</button>
                            <button class="pag-btn">›</button>
                        </div>
                    </div>
                </div>

            </div><!-- /page -->
    @push('scripts')
        <script>
            // sidebar
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

            // filter tabs
            function filterTab(btn, status) {
                document.querySelectorAll('.ftab').forEach(t => t.classList.remove('active'));
                btn.classList.add('active');
                // table rows
                document.querySelectorAll('#ordersBody tr').forEach(tr => {
                    tr.style.display = (status === 'all' || tr.dataset.status === status) ? '' : 'none';
                });
                // mobile cards
                document.querySelectorAll('#mobileOrders .m-order-card').forEach(c => {
                    c.style.display = (status === 'all' || c.dataset.status === status) ? '' : 'none';
                });
                checkEmpty();
            }

            // search
            function filterOrders() {
                const q = document.getElementById('searchInput').value.toLowerCase();
                document.querySelectorAll('#ordersBody tr').forEach(tr => {
                    const id = tr.querySelector('.order-id')?.textContent.toLowerCase() || '';
                    tr.style.display = id.includes(q) ? '' : 'none';
                });
                document.querySelectorAll('#mobileOrders .m-order-card').forEach(c => {
                    const id = c.querySelector('.m-id')?.textContent.toLowerCase() || '';
                    c.style.display = id.includes(q) ? '' : 'none';
                });
                checkEmpty();
            }

            function checkEmpty() {
                const visibleRows = [...document.querySelectorAll('#ordersBody tr')].filter(tr => tr.style.display !== 'none');
                const visibleCards = [...document.querySelectorAll('#mobileOrders .m-order-card')].filter(c => c.style
                    .display !== 'none');
                const empty = visibleRows.length === 0 && visibleCards.length === 0;
                document.getElementById('emptyState').style.display = empty ? 'block' : 'none';
            }

            // cancel modal
            let pendingCancel = null;

            function openCancelModal(orderId) {
                pendingCancel = orderId;
                document.getElementById('cancelOrderId').textContent = orderId;
                document.getElementById('cancelModal').classList.add('show');
            }

            function closeModal() {
                pendingCancel = null;
                document.getElementById('cancelModal').classList.remove('show');
            }

            function confirmCancel() {
                if (pendingCancel) {
                    // find the row and update
                    document.querySelectorAll('#ordersBody tr').forEach(tr => {
                        if (tr.querySelector('.order-id')?.textContent === pendingCancel) {
                            tr.querySelector('.status-badge').className = 'status-badge s-cancelled';
                            tr.querySelector('.status-badge').textContent = 'CANCELLED';
                            tr.dataset.status = 'cancelled';
                            const actions = tr.querySelector('.actions-cell');
                            actions.innerHTML = `
          <a href="#" class="act-btn act-view"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>View</a>
          <a href="#" class="act-btn act-reorder"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>Reorder</a>
        `;
                        }
                    });
                }
                closeModal();
            }
            // close modal on backdrop click
            document.getElementById('cancelModal').addEventListener('click', function(e) {
                if (e.target === this) closeModal()
            });
        </script>
    @endpush
@endsection
