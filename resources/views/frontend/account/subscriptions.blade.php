@extends('layouts.user-panel')
@section('title', 'Subscription — NutriBuddy Kids')
@section('panel-page-class', 'panel-subscription')
@section('panel-content')
        <div class="page">

            {{-- Rule 5: pending questionnaire banners --}}
            @if (!empty($pendingQuestionnaires) && $pendingQuestionnaires->isNotEmpty())
                @foreach ($pendingQuestionnaires as $pq)
                <div class="season-alert" style="background:var(--pul,#fef3c7);border-left:4px solid var(--pk,#f59e0b);margin-bottom:1rem;">
                    <div class="alert-icon">📋</div>
                    <div>
                        <div class="alert-title">Complete your questionnaire — {{ $pq['subscription']->subscriptionPlan?->name ?? 'Paid' }} Plan</div>
                        <div class="alert-body">
                            Your payment for {{ $pq['subscription']->child?->name }} was successful!
                            Answer a few more questions to unlock the personalised diet chart.
                            <a href="{{ $pq['url'] }}" class="fw-bold ms-2">Continue →</a>
                        </div>
                    </div>
                </div>
                @endforeach
            @endif


            @php($activeSub = $subscriptions->first(fn($s) => in_array($s->status, ['active', 'paused'])))
            <!-- CURRENT PLAN BANNER -->
            <div class="current-plan">
                @if ($activeSub)
                    <div class="cp-left">
                        <div class="cp-badge" style="{{ $activeSub->status === 'paused' ? 'background:#fef3c7;color:#b45309;' : '' }}">
                            {{ $activeSub->status === 'paused' ? '⏸️ Paused Plan' : '⚡ Active Plan' }}
                        </div>
                        <div class="cp-name">{{ $activeSub->subscriptionPlan?->name ?? 'Diet Plan' }}</div>
                        <div class="cp-desc">
                            {{ $activeSub->child?->name ? "Unlocking personalized nutrition for " . $activeSub->child->name : "Personalized nutrition plan" }}
                        </div>
                    </div>
                    <div class="cp-right">
                        <div class="cp-price">₹{{ number_format($activeSub->amount_paid, 0) }}</div>
                        <div class="cp-period">/cycle</div>
                        <div class="cp-renew">
                            @if ($activeSub->status === 'paused')
                                ⏸️ AutoPay Paused (Valid till {{ $activeSub->expires_at ? $activeSub->expires_at->format('M d, Y') : 'N/A' }})
                            @else
                                🔄 Renews on {{ $activeSub->expires_at ? $activeSub->expires_at->format('M d, Y') : 'N/A' }}
                            @endif
                        </div>
                        <div class="d-flex gap-2 mt-2">
                            @if ($activeSub->status === 'active')
                                <button class="cp-manage-btn" style="background:#f59e0b;" onclick="handleSubAction('pause', {{ $activeSub->id }})">
                                    ⏸️ Pause Plan
                                </button>
                            @elseif ($activeSub->status === 'paused')
                                <button class="cp-manage-btn" style="background:#10b981;" onclick="handleSubAction('resume', {{ $activeSub->id }})">
                                    ▶️ Resume Plan
                                </button>
                            @endif
                            <button class="cp-manage-btn" style="background:#ef4444;" onclick="handleSubAction('cancel', {{ $activeSub->id }})">
                                ✖ Cancel
                            </button>
                        </div>
                    </div>
                @else
                    <div class="cp-left">
                        <div class="cp-badge" style="background:#e5e7eb;color:#374151">No Active Plan</div>
                        <div class="cp-name">No Active Subscription</div>
                        <div class="cp-desc">Choose a plan below to start personalized nutrition for your child.</div>
                    </div>
                    <div class="cp-right">
                        <div class="cp-price">₹0</div>
                        <div class="cp-period">/month</div>
                        <div class="cp-renew">Inactive</div>
                    </div>
                @endif
            </div>

            <!-- BILLING TOGGLE -->
            <div class="billing-toggle">
                <span class="toggle-label active" id="lbl-monthly">Monthly</span>
                <button class="toggle-pill" id="toggleBtn" onclick="switchBilling()">
                    <div class="knob"></div>
                </button>
                <span class="toggle-label" id="lbl-yearly">Yearly</span>
                <span class="save-badge">Save 30%</span>
            </div>

            <!-- PLANS GRID -->
            <div class="plans-grid">

                <!-- Starter -->
                <div class="plan-card">
                    <div class="plan-icon" style="background:var(--skl)">🌱</div>
                    <div class="plan-name">Starter</div>
                    <div class="plan-tagline">Perfect for trying it out</div>
                    <div class="plan-price">
                        <span class="currency">₹</span>
                        <span class="amount" id="p1">0</span>
                        <span class="period">/mo</span>
                    </div>
                    <div class="plan-yearly-note" id="n1"></div>
                    <div class="plan-divider"></div>
                    <div class="feature-list">
                        <div class="feature-item">
                            <div class="fi-dot" style="background:var(--mnl);color:#059669">✓</div> 1 Child profile
                        </div>
                        <div class="feature-item">
                            <div class="fi-dot" style="background:var(--mnl);color:#059669">✓</div> Basic meal plans
                        </div>
                        <div class="feature-item">
                            <div class="fi-dot" style="background:var(--mnl);color:#059669">✓</div> Nutrition tracker
                        </div>
                        <div class="feature-item off">
                            <div class="fi-dot">—</div> Expert consultations
                        </div>
                        <div class="feature-item off">
                            <div class="fi-dot">—</div> Priority delivery
                        </div>
                        <div class="feature-item off">
                            <div class="fi-dot">—</div> Custom diet plans
                        </div>
                    </div>
                    <button class="plan-btn outline" onclick="showToast('Downgrading to Starter...')">Get Started</button>
                </div>

                <!-- Growth (Current) -->
                <div class="plan-card popular current-active">
                    <div class="current-tag">✓ Current</div>
                    <div class="plan-icon" style="background:var(--pkl)">🚀</div>
                    <div class="plan-name">Growth</div>
                    <div class="plan-tagline">Most popular for families</div>
                    <div class="plan-price">
                        <span class="currency">₹</span>
                        <span class="amount" id="p2">299</span>
                        <span class="period">/mo</span>
                    </div>
                    <div class="plan-yearly-note" id="n2"></div>
                    <div class="plan-divider"></div>
                    <div class="feature-list">
                        <div class="feature-item">
                            <div class="fi-dot" style="background:var(--mnl);color:#059669">✓</div> 3 Child profiles
                        </div>
                        <div class="feature-item">
                            <div class="fi-dot" style="background:var(--mnl);color:#059669">✓</div> Advanced meal plans
                        </div>
                        <div class="feature-item">
                            <div class="fi-dot" style="background:var(--mnl);color:#059669">✓</div> Nutrition tracker
                        </div>
                        <div class="feature-item">
                            <div class="fi-dot" style="background:var(--mnl);color:#059669">✓</div> 2 Expert
                            consultations/mo
                        </div>
                        <div class="feature-item">
                            <div class="fi-dot" style="background:var(--mnl);color:#059669">✓</div> Priority delivery
                        </div>
                        <div class="feature-item off">
                            <div class="fi-dot">—</div> Custom diet plans
                        </div>
                    </div>
                    <button class="plan-btn ghost">✓ Active Plan</button>
                </div>

                <!-- Premium -->
                <div class="plan-card popular">
                    <div class="popular-tag">⭐ Best Value</div>
                    <div class="plan-icon" style="background:var(--pul)">👑</div>
                    <div class="plan-name">Premium</div>
                    <div class="plan-tagline">Everything for power families</div>
                    <div class="plan-price">
                        <span class="currency">₹</span>
                        <span class="amount" id="p3">599</span>
                        <span class="period">/mo</span>
                    </div>
                    <div class="plan-yearly-note" id="n3"></div>
                    <div class="plan-divider"></div>
                    <div class="feature-list">
                        <div class="feature-item">
                            <div class="fi-dot" style="background:var(--mnl);color:#059669">✓</div> Unlimited Child
                            profiles
                        </div>
                        <div class="feature-item">
                            <div class="fi-dot" style="background:var(--mnl);color:#059669">✓</div> Custom meal plans
                        </div>
                        <div class="feature-item">
                            <div class="fi-dot" style="background:var(--mnl);color:#059669">✓</div> Advanced tracker +
                            insights
                        </div>
                        <div class="feature-item">
                            <div class="fi-dot" style="background:var(--mnl);color:#059669">✓</div> Unlimited
                            consultations
                        </div>
                        <div class="feature-item">
                            <div class="fi-dot" style="background:var(--mnl);color:#059669">✓</div> Priority delivery
                        </div>
                        <div class="feature-item">
                            <div class="fi-dot" style="background:var(--mnl);color:#059669">✓</div> Custom diet plans
                        </div>
                    </div>
                    <button class="plan-btn solid" onclick="showToast('Upgrading to Premium! 🎉')">Upgrade Now</button>
                </div>

            </div>

            <!-- BENEFITS ROW -->
            <div class="benefits-row">
                <div class="benefit-card">
                    <div class="benefit-icon">🚚</div>
                    <div class="benefit-title">Free Delivery</div>
                    <div class="benefit-desc">On all orders above ₹499</div>
                </div>
                <div class="benefit-card">
                    <div class="benefit-icon">👩‍⚕️</div>
                    <div class="benefit-title">Expert Support</div>
                    <div class="benefit-desc">Nutritionist consultations included</div>
                </div>
                <div class="benefit-card">
                    <div class="benefit-icon">🔄</div>
                    <div class="benefit-title">Easy Cancel</div>
                    <div class="benefit-desc">No lock-in, cancel anytime</div>
                </div>
                <div class="benefit-card">
                    <div class="benefit-icon">🛡️</div>
                    <div class="benefit-title">Secure Billing</div>
                    <div class="benefit-desc">100% safe & encrypted payments</div>
                </div>
            </div>

            <!-- BILLING HISTORY -->
            <div class="history-section">
                <div class="history-head">
                    <div class="h-icon">🧾</div>
                    <h3>Billing History</h3>
                </div>
                <div style="overflow-x:auto">
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Plan</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="font-weight:700">#INV-0004</td>
                                <td>Growth Plan</td>
                                <td style="color:var(--muted)">Apr 3, 2026</td>
                                <td style="font-weight:700">₹299</td>
                                <td><span class="status-chip s-paid">✓ Paid</span></td>
                                <td><button class="inv-btn"
                                        onclick="showToast('Downloading invoice...')">Download</button></td>
                            </tr>
                            <tr>
                                <td style="font-weight:700">#INV-0003</td>
                                <td>Growth Plan</td>
                                <td style="color:var(--muted)">Mar 3, 2026</td>
                                <td style="font-weight:700">₹299</td>
                                <td><span class="status-chip s-paid">✓ Paid</span></td>
                                <td><button class="inv-btn"
                                        onclick="showToast('Downloading invoice...')">Download</button></td>
                            </tr>
                            <tr>
                                <td style="font-weight:700">#INV-0002</td>
                                <td>Starter Plan</td>
                                <td style="color:var(--muted)">Feb 3, 2026</td>
                                <td style="font-weight:700">₹0</td>
                                <td><span class="status-chip s-paid">✓ Free</span></td>
                                <td><button class="inv-btn"
                                        onclick="showToast('Downloading invoice...')">Download</button></td>
                            </tr>
                            <tr>
                                <td style="font-weight:700">#INV-0001</td>
                                <td>Starter Plan</td>
                                <td style="color:var(--muted)">Jan 3, 2026</td>
                                <td style="font-weight:700">₹0</td>
                                <td><span class="status-chip s-paid">✓ Free</span></td>
                                <td><button class="inv-btn"
                                        onclick="showToast('Downloading invoice...')">Download</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($activeSub)
            <!-- CANCEL ZONE -->
            <div class="cancel-zone">
                <div class="cancel-head">
                    <div class="c-icon">⚠️</div>
                    <h3>Cancel Subscription</h3>
                </div>
                <div class="cancel-body">
                    <div class="cancel-info">
                        <p>Cancelling will stop AutoPay recurring charges immediately. You will retain full access until <strong>{{ $activeSub->expires_at ? $activeSub->expires_at->format('M d, Y') : 'the end of your billing cycle' }}</strong>.</p>
                    </div>
                    <button class="cancel-btn" onclick="handleSubAction('cancel', {{ $activeSub->id }})">Cancel Subscription</button>
                </div>
            </div>
            @endif

        </div><!-- /page -->
    </div><!-- /main -->

    @push('scripts')
        <script>
            /* Billing toggle */
            let yearly = false;
            const prices = {
                monthly: [0, 299, 599],
                yearly: [0, 209, 419]
            };
            const ids = ['p1', 'p2', 'p3'];
            const notes = ['n1', 'n2', 'n3'];
            const yearlyTotal = [0, 2508, 5028];

            function switchBilling() {
                yearly = !yearly;
                const btn = document.getElementById('toggleBtn');
                btn.classList.toggle('yearly', yearly);
                document.getElementById('lbl-monthly').classList.toggle('active', !yearly);
                document.getElementById('lbl-yearly').classList.toggle('active', yearly);
                ids.forEach((id, i) => {
                    document.getElementById(id).textContent = yearly ? prices.yearly[i] : prices.monthly[i];
                    document.getElementById(notes[i]).textContent = yearly && prices.yearly[i] > 0 ?
                        `₹${yearlyTotal[i]}/year — save ₹${(prices.monthly[i]-prices.yearly[i])*12}` : '';
                });
            }

            /* Subscriptions Actions (Pause, Resume, Cancel) */
            async function handleSubAction(action, subId) {
                const actionLabels = {
                    pause: 'pause your subscription? Your AutoPay recurring charge will be paused.',
                    resume: 'resume your subscription? AutoPay recurring charges will be reactivated.',
                    cancel: 'cancel your subscription? AutoPay recurring mandate will be cancelled permanently.'
                };

                if (!confirm('Are you sure you want to ' + (actionLabels[action] || action + ' this subscription?'))) {
                    return;
                }

                try {
                    const response = await fetch('/api/subscriptions/' + action, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ subscription_id: subId })
                    });
                    const data = await response.json();
                    if (data.success) {
                        if (typeof nbToast === 'function') {
                            nbToast(data.message, 'success');
                        } else {
                            showToast(data.message);
                        }
                        setTimeout(() => window.location.reload(), 1200);
                    } else {
                        if (typeof nbToast === 'function') {
                            nbToast(data.message || 'Operation failed', 'error');
                        } else {
                            showToast(data.message || 'Operation failed');
                        }
                    }
                } catch (err) {
                    console.error(err);
                    if (typeof nbToast === 'function') {
                        nbToast('An unexpected error occurred.', 'error');
                    } else {
                        showToast('An unexpected error occurred.');
                    }
                }
            }

            /* Toast */
            function showToast(msg) {
                const t = document.getElementById('toast');
                if (t) {
                    document.getElementById('toastMsg').textContent = msg;
                    t.classList.add('show');
                    setTimeout(() => t.classList.remove('show'), 3000);
                } else if (typeof nbToast === 'function') {
                    nbToast(msg, 'info');
                } else {
                    alert(msg);
                }
            }
        </script>
    @endpush
@endsection
