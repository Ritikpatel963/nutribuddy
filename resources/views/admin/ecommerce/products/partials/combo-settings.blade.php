@php
    $currentProduct = $product ?? null;
    $isCombo = old('is_combo', $currentProduct ? (bool) $currentProduct->is_combo : false);
    $comboTagline = old('combo_tagline', $currentProduct ? $currentProduct->combo_tagline : '');
    $comboBadge = old('combo_badge', $currentProduct ? $currentProduct->combo_badge : '');
    $comboItems = old('combo_items');
    if ($comboItems === null && $currentProduct) {
        $comboItems = $currentProduct->comboItems->map(function ($item) {
            return [
                'child_variant_id' => $item->child_variant_id,
                'quantity' => $item->quantity,
            ];
        })->toArray();
    }
    $comboItems = $comboItems ?: [];
    $variantsForCombo = ($availableVariants ?? collect())->filter(function ($v) use ($currentProduct) {
        return ! $currentProduct || $v->product_id != $currentProduct->id;
    });
@endphp

<!-- COMBO PACK CONFIGURATION -->
<div class="card border-0 radius-12 mb-24 shadow-sm">
    <div class="card-header bg-base border-bottom d-flex justify-content-between align-items-center py-16 px-24">
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-primary-50 text-primary-600 p-2 rounded-2 d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                <iconify-icon icon="solar:box-minimalistic-bold-duotone" class="fs-4"></iconify-icon>
            </span>
            <div>
                <h5 class="card-title mb-0">Combo Pack Configuration (Virtual Bundle)</h5>
                <small class="text-secondary">Bundle multiple products together. Underlying inventories deduct automatically on purchase.</small>
            </div>
        </div>
        <div class="form-check form-switch d-flex align-items-center gap-2 m-0">
            <input type="hidden" name="is_combo" value="0">
            <input class="form-check-input m-0 float-none" type="checkbox" name="is_combo" value="1" id="isComboToggle" {{ $isCombo ? 'checked' : '' }}>
            <label class="form-check-label m-0 fw-bold cursor-pointer" for="isComboToggle">This is a Combo Pack</label>
        </div>
    </div>

    <div class="card-body p-24" id="comboDetailsContainer" style="{{ $isCombo ? '' : 'display: none;' }}">
        <div class="row g-4 mb-24">
            <div class="col-md-7">
                <label class="form-label fw-bold">Combo Tagline</label>
                <input type="text" name="combo_tagline" value="{{ $comboTagline }}" class="form-control" placeholder="e.g. 2-in-1 Daily Routine for Radiant Hair & Skin">
                <small class="text-muted">Displayed prominently under the title on the storefront card and PDP.</small>
            </div>
            <div class="col-md-5">
                <label class="form-label fw-bold">Combo Badge Text</label>
                <input type="text" name="combo_badge" value="{{ $comboBadge }}" class="form-control" placeholder="e.g. Save ₹700 or Best Value">
                <small class="text-muted">Eye-catching pill badge shown on the product card.</small>
            </div>
        </div>

        <div class="border rounded-12 p-20 bg-light-50">
            <div class="d-flex justify-content-between align-items-center mb-16">
                <div>
                    <h6 class="fw-bold mb-4 text-dark">Constituent Bundle Items</h6>
                    <small class="text-muted">Select the individual product variants and quantities bundled in this pack.</small>
                </div>
                <button type="button" class="btn btn-primary-600 btn-sm px-3 d-inline-flex align-items-center gap-1" id="addComboItemBtn">
                    <iconify-icon icon="lucide:plus" style="font-size: 16px;"></iconify-icon> Add Bundle Item
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered bg-white radius-8 overflow-hidden align-middle mb-0" id="comboItemsTable">
                    <thead class="bg-light">
                        <tr>
                            <th style="min-width: 320px;">Child Product / Variant</th>
                            <th style="width: 140px;">Quantity per Pack</th>
                            <th style="width: 80px;" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="comboItemsWrapper">
                        <!-- Dynamic Rows Injected Here -->
                    </tbody>
                </table>
            </div>

            <div id="noComboItemsMsg" class="text-center py-4 text-muted bg-white rounded border border-dashed mt-2" style="display: none;">
                <iconify-icon icon="solar:box-linear" class="fs-1 opacity-25"></iconify-icon>
                <p class="mt-2 mb-0 small">No constituent items added yet. Click <strong>Add Bundle Item</strong> to add products to this combo.</p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const isComboToggle = document.getElementById('isComboToggle');
    const comboContainer = document.getElementById('comboDetailsContainer');
    const comboItemsWrapper = document.getElementById('comboItemsWrapper');
    const noComboItemsMsg = document.getElementById('noComboItemsMsg');
    const addComboItemBtn = document.getElementById('addComboItemBtn');

    // Available variants options
    const availableVariants = [
        @foreach($variantsForCombo as $v)
        {
            id: {{ $v->id }},
            productName: "{{ addslashes($v->product?->name ?? 'Product') }}",
            variantName: "{{ addslashes($v->name) }}",
            sku: "{{ addslashes($v->sku ?? 'N/A') }}",
            price: "{{ $v->price }}"
        },
        @endforeach
    ];

    let comboItemIndex = 0;

    function syncNoComboItems() {
        const hasRows = comboItemsWrapper && comboItemsWrapper.children.length > 0;
        if (noComboItemsMsg) {
            noComboItemsMsg.style.display = hasRows ? 'none' : 'block';
        }
        const table = document.getElementById('comboItemsTable');
        if (table) {
            table.style.display = hasRows ? 'table' : 'none';
        }
    }

    function addComboItemRow(selectedVariantId = '', quantity = 1) {
        const index = comboItemIndex++;
        const tr = document.createElement('tr');
        tr.className = 'combo-item-row';

        let optionsHtml = '<option value="">-- Select Product Variant --</option>';
        availableVariants.forEach(function (v) {
            const isSelected = String(v.id) === String(selectedVariantId) ? 'selected' : '';
            optionsHtml += `<option value="${v.id}" ${isSelected}>${v.productName} — ${v.variantName} (SKU: ${v.sku}, ₹${v.price})</option>`;
        });

        tr.innerHTML = `
            <td>
                <select name="combo_items[${index}][child_variant_id]" class="form-select form-select-sm" required>
                    ${optionsHtml}
                </select>
            </td>
            <td>
                <input type="number" name="combo_items[${index}][quantity]" value="${quantity}" min="1" class="form-control form-control-sm text-center" required>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-outline-danger btn-sm remove-combo-item-btn p-1 px-2" title="Remove Item">
                    <iconify-icon icon="lucide:trash-2" style="font-size: 16px;"></iconify-icon>
                </button>
            </td>
        `;

        tr.querySelector('.remove-combo-item-btn').addEventListener('click', function () {
            tr.remove();
            syncNoComboItems();
        });

        comboItemsWrapper.appendChild(tr);
        syncNoComboItems();
    }

    if (isComboToggle) {
        isComboToggle.addEventListener('change', function () {
            if (this.checked) {
                comboContainer.style.display = 'block';
                if (comboItemsWrapper.children.length === 0) {
                    addComboItemRow();
                }
            } else {
                comboContainer.style.display = 'none';
            }
        });
    }

    if (addComboItemBtn) {
        addComboItemBtn.addEventListener('click', function () {
            addComboItemRow();
        });
    }

    // Populate pre-existing / old items
    const initialComboItems = @json($comboItems);
    if (initialComboItems && initialComboItems.length > 0) {
        initialComboItems.forEach(function (item) {
            addComboItemRow(item.child_variant_id, item.quantity || 1);
        });
    } else {
        syncNoComboItems();
    }
});
</script>
