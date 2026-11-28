@php
    $currentProduct = $product ?? null;
    $hasCustomSubscriptions = old('has_custom_subscriptions', $currentProduct && $currentProduct->subscriptionPlans->isNotEmpty());
    $submittedPlans = old('subscription_plans');
    if ($submittedPlans === null && $currentProduct && $currentProduct->subscriptionPlans->isNotEmpty()) {
        $submittedPlans = $currentProduct->subscriptionPlans->map(function ($plan) {
            return [
                'id' => $plan->id,
                'duration_months' => $plan->duration_months,
                'month_1_price' => $plan->month_1_price,
                'recurring_price' => $plan->recurring_price,
                'badge_text' => $plan->badge_text,
                'free_gift_variant_id' => $plan->free_gift_variant_id,
                'free_gift_label' => $plan->free_gift_label,
                'is_active' => $plan->is_active,
                'sort_order' => $plan->sort_order,
            ];
        })->toArray();
    }
    $submittedPlans = $submittedPlans ?: [];
    $giftVariants = $availableVariants ?? collect();
@endphp

<!-- SUBSCRIBE & SAVE PLANS CONFIGURATION -->
<div class="card border-0 radius-12 mb-24 shadow-sm">
    <div class="card-header bg-base border-bottom d-flex justify-content-between align-items-center py-16 px-24">
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-success-50 text-success-600 p-2 rounded-2 d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                <iconify-icon icon="solar:calendar-date-bold-duotone" class="fs-4"></iconify-icon>
            </span>
            <div>
                <h5 class="card-title mb-0">Subscribe & Save Plans</h5>
                <small class="text-secondary">Configure recurring subscription tiers, month 1 intro discounts, and bonus gifts.</small>
            </div>
        </div>
        <div class="form-check form-switch d-flex align-items-center gap-2 m-0">
            <input type="hidden" name="has_custom_subscriptions" value="0">
            <input class="form-check-input m-0 float-none" type="checkbox" name="has_custom_subscriptions" value="1" id="hasCustomSubToggle" {{ $hasCustomSubscriptions ? 'checked' : '' }}>
            <label class="form-check-label m-0 fw-bold cursor-pointer" for="hasCustomSubToggle">Custom Product Plans</label>
        </div>
    </div>

    <div class="card-body p-24">
        <!-- Storewide Defaults Banner (shown when custom plans toggle is OFF) -->
        <div id="storewidePlansNotice" class="alert alert-light border border-info-subtle radius-12 p-20 mb-0" style="{{ $hasCustomSubscriptions ? 'display:none;' : '' }}">
            <div class="d-flex align-items-start gap-3">
                <span class="text-info fs-3">
                    <iconify-icon icon="solar:info-circle-bold-duotone"></iconify-icon>
                </span>
                <div class="w-100">
                    <h6 class="fw-bold mb-1 text-dark">Storewide Default Plans Active for this Product</h6>
                    <p class="text-secondary small mb-3">Customers can subscribe on the storefront PDP using standard storewide tiers:</p>
                    <div class="row g-2">
                        <div class="col-md-4">
                            <div class="p-12 rounded bg-white border">
                                <span class="badge bg-light text-dark mb-1">2 Months Tier</span>
                                <div class="fw-bold fs-6">₹699 <small class="text-muted fw-normal">Month 1</small> / ₹749 <small class="text-muted fw-normal">Mo</small></div>
                                <small class="text-success fw-semibold">Save 42%</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-12 rounded bg-white border">
                                <span class="badge bg-light text-dark mb-1">3 Months Tier</span>
                                <div class="fw-bold fs-6">₹649 <small class="text-muted fw-normal">Month 1</small> / ₹699 <small class="text-muted fw-normal">Mo</small></div>
                                <small class="text-success fw-semibold">Save 46% + Free Perk</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-12 rounded bg-white border">
                                <span class="badge bg-light text-dark mb-1">4 Months Tier</span>
                                <div class="fw-bold fs-6">₹599 <small class="text-muted fw-normal">Month 1</small> / ₹649 <small class="text-muted fw-normal">Mo</small></div>
                                <small class="text-success fw-semibold">Save 50% + Free Perk</small>
                            </div>
                        </div>
                    </div>
                    <small class="text-muted d-block mt-3">To override these and set product-specific pricing, discounts, or bonus gifts, toggle <strong>"Custom Product Plans"</strong> above.</small>
                </div>
            </div>
        </div>

        <!-- Custom Plans Repeater (shown when toggle is ON) -->
        <div id="customPlansContainer" style="{{ $hasCustomSubscriptions ? '' : 'display:none;' }}">
            <div class="d-flex justify-content-between align-items-center mb-16">
                <div>
                    <h6 class="fw-bold mb-2 text-dark">Custom Product Subscription Tiers</h6>
                    <small class="text-muted">Define the duration tiers, Month 1 intro prices, recurring monthly billing amounts, and free gift perks.</small>
                </div>
                <button type="button" class="btn btn-success-600 btn-sm px-3 d-inline-flex align-items-center gap-1" id="addSubscriptionPlanBtn">
                    <iconify-icon icon="lucide:plus" style="font-size: 16px;"></iconify-icon> Add Plan Tier
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered bg-white radius-8 overflow-hidden align-middle mb-0" id="subscriptionPlansTable">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 110px;">Duration</th>
                            <th style="width: 140px;">Month 1 (₹)</th>
                            <th style="width: 140px;">Recurring (₹/Mo)</th>
                            <th style="width: 150px;">Badge Tag</th>
                            <th style="min-width: 280px;">Free Gift Perk (Cycle 1)</th>
                            <th style="width: 90px;" class="text-center">Active</th>
                            <th style="width: 70px;" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="subscriptionPlansWrapper">
                        <!-- Dynamic Rows Injected Here -->
                    </tbody>
                </table>
            </div>

            <div id="noSubscriptionPlansMsg" class="text-center py-4 text-muted bg-white rounded border border-dashed mt-2" style="display: none;">
                <iconify-icon icon="solar:calendar-date-linear" class="fs-1 opacity-25"></iconify-icon>
                <p class="mt-2 mb-0 small">No custom tiers configured. Click <strong>Add Plan Tier</strong> to create plans for this product.</p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const hasCustomSubToggle = document.getElementById('hasCustomSubToggle');
    const storewideNotice = document.getElementById('storewidePlansNotice');
    const customContainer = document.getElementById('customPlansContainer');
    const plansWrapper = document.getElementById('subscriptionPlansWrapper');
    const noPlansMsg = document.getElementById('noSubscriptionPlansMsg');
    const addPlanBtn = document.getElementById('addSubscriptionPlanBtn');

    // Available gift variants
    const giftVariants = [
        @foreach($giftVariants as $v)
        {
            id: {{ $v->id }},
            productName: "{{ addslashes($v->product?->name ?? 'Product') }}",
            variantName: "{{ addslashes($v->name) }}",
            sku: "{{ addslashes($v->sku ?? 'N/A') }}"
        },
        @endforeach
    ];

    let planIndex = 0;

    function syncNoPlansMsg() {
        const hasRows = plansWrapper && plansWrapper.children.length > 0;
        if (noPlansMsg) {
            noPlansMsg.style.display = hasRows ? 'none' : 'block';
        }
        const table = document.getElementById('subscriptionPlansTable');
        if (table) {
            table.style.display = hasRows ? 'table' : 'none';
        }
    }

    function addSubscriptionPlanRow(plan = {}) {
        const index = planIndex++;
        const tr = document.createElement('tr');
        tr.className = 'subscription-plan-row';

        const id = plan.id || '';
        const duration = plan.duration_months || 3;
        const month1 = plan.month_1_price !== undefined ? plan.month_1_price : '';
        const recurring = plan.recurring_price !== undefined ? plan.recurring_price : '';
        const badge = plan.badge_text || '';
        const giftVariantId = plan.free_gift_variant_id || '';
        const giftLabel = plan.free_gift_label || '';
        const isActive = plan.is_active !== undefined ? Boolean(plan.is_active) : true;

        let giftOptionsHtml = '<option value="">-- No Free Gift --</option>';
        giftVariants.forEach(function (v) {
            const isSelected = String(v.id) === String(giftVariantId) ? 'selected' : '';
            giftOptionsHtml += `<option value="${v.id}" ${isSelected}>${v.productName} — ${v.variantName} (SKU: ${v.sku})</option>`;
        });

        tr.innerHTML = `
            <td>
                <input type="hidden" name="subscription_plans[${index}][id]" value="${id}">
                <div class="input-group input-group-sm">
                    <input type="number" name="subscription_plans[${index}][duration_months]" value="${duration}" min="1" max="36" class="form-control text-center" required>
                    <span class="input-group-text">Mo</span>
                </div>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <span class="input-group-text">₹</span>
                    <input type="number" step="0.01" min="0" name="subscription_plans[${index}][month_1_price]" value="${month1}" placeholder="0.00" class="form-control" required>
                </div>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <span class="input-group-text">₹</span>
                    <input type="number" step="0.01" min="0" name="subscription_plans[${index}][recurring_price]" value="${recurring}" placeholder="0.00" class="form-control" required>
                </div>
            </td>
            <td>
                <input type="text" name="subscription_plans[${index}][badge_text]" value="${badge}" placeholder="e.g. 50% OFF" class="form-control form-control-sm">
            </td>
            <td>
                <div class="d-flex flex-column gap-1">
                    <select name="subscription_plans[${index}][free_gift_variant_id]" class="form-select form-select-sm">
                        ${giftOptionsHtml}
                    </select>
                    <input type="text" name="subscription_plans[${index}][free_gift_label]" value="${giftLabel}" placeholder="Gift Label (e.g. Free Kadha)" class="form-control form-control-sm">
                </div>
            </td>
            <td class="text-center">
                <input type="hidden" name="subscription_plans[${index}][is_active]" value="0">
                <input type="checkbox" name="subscription_plans[${index}][is_active]" value="1" class="form-check-input" ${isActive ? 'checked' : ''}>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-outline-danger btn-sm remove-plan-row-btn p-1 px-2" title="Remove Plan">
                    <iconify-icon icon="lucide:trash-2" style="font-size: 16px;"></iconify-icon>
                </button>
            </td>
        `;

        tr.querySelector('.remove-plan-row-btn').addEventListener('click', function () {
            tr.remove();
            syncNoPlansMsg();
        });

        plansWrapper.appendChild(tr);
        syncNoPlansMsg();
    }

    if (hasCustomSubToggle) {
        hasCustomSubToggle.addEventListener('change', function () {
            if (this.checked) {
                storewideNotice.style.display = 'none';
                customContainer.style.display = 'block';
                if (plansWrapper.children.length === 0) {
                    addSubscriptionPlanRow({ duration_months: 2, month_1_price: 699, recurring_price: 749, badge_text: '42% OFF' });
                    addSubscriptionPlanRow({ duration_months: 3, month_1_price: 649, recurring_price: 699, badge_text: '46% OFF' });
                    addSubscriptionPlanRow({ duration_months: 4, month_1_price: 599, recurring_price: 649, badge_text: '50% OFF' });
                }
            } else {
                storewideNotice.style.display = 'block';
                customContainer.style.display = 'none';
            }
        });
    }

    if (addPlanBtn) {
        addPlanBtn.addEventListener('click', function () {
            addSubscriptionPlanRow();
        });
    }

    // Populate pre-existing / old plans
    const initialPlans = @json($submittedPlans);
    if (initialPlans && initialPlans.length > 0) {
        initialPlans.forEach(function (plan) {
            addSubscriptionPlanRow(plan);
        });
    } else {
        syncNoPlansMsg();
    }
});
</script>
