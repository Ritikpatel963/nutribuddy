@extends('layouts.admin')
@php
    $title = 'Create New Combo Pack';
    $subTitle = 'Ecommerce / Combo Packs / Create';
@endphp

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <form action="{{ route('admin.ecommerce.combos.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
                @csrf
                <input type="hidden" name="is_combo" value="1">
                <input type="hidden" name="product_type" value="simple">

                @include('admin.ecommerce.products.partials.accordion-cards-manager')

                <!-- 1. GENERAL INFORMATION -->
                <div class="card border-0 radius-12 mb-24">
                    <div class="card-header bg-base border-bottom py-16 px-24 d-flex align-items-center gap-2">
                        <span class="badge bg-primary-50 text-primary-600 p-2 rounded-2">
                            <iconify-icon icon="solar:box-minimalistic-bold-duotone" class="fs-5"></iconify-icon>
                        </span>
                        <div>
                            <h5 class="card-title mb-0">Combo Pack General Information</h5>
                            <small class="text-secondary">Basic details for the combo package</small>
                        </div>
                    </div>
                    <div class="card-body p-24">
                        <div class="row g-4">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Combo Pack Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" value="{{ old('name') }}" class="form-control"
                                    placeholder="e.g. Complete Hair & Skin Wellness Duo" required>
                                @error('name')<span class="text-danger small">{{ $message }}</span>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">SKU Code</label>
                                <input type="text" name="sku" value="{{ old('sku') }}" class="form-control"
                                    placeholder="Auto generated if empty (e.g. COMBO-HAIR-SKIN)">
                                @error('sku')<span class="text-danger small">{{ $message }}</span>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">HSN Code</label>
                                <input type="text" name="hsn_code" value="{{ old('hsn_code') }}" class="form-control"
                                    placeholder="e.g. 2106">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-select" required>
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tax Rate</label>
                                <select name="tax_rate_id" class="form-select">
                                    <option value="">No Tax / Exempt</option>
                                    @foreach ($taxRates as $tax)
                                        <option value="{{ $tax->id }}" {{ old('tax_rate_id') == $tax->id ? 'selected' : '' }}>
                                            {{ $tax->name }} ({{ $tax->rate }}%)
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. CONSTITUENT BUNDLE ITEMS & BRANDING -->
                <div class="card border-0 radius-12 mb-24 shadow-sm">
                    <div class="card-header bg-base border-bottom py-16 px-24 d-flex align-items-center gap-2">
                        <span class="badge bg-success-50 text-success-600 p-2 rounded-2">
                            <iconify-icon icon="lucide:layers" class="fs-5"></iconify-icon>
                        </span>
                        <div>
                            <h5 class="card-title mb-0">Constituent Bundle Items</h5>
                            <small class="text-secondary">Select the individual products and quantities bundled into this combo package</small>
                        </div>
                    </div>
                    <div class="card-body p-24">
                        <div class="row g-4 mb-24">
                            <div class="col-md-7">
                                <label class="form-label fw-bold">Combo Tagline</label>
                                <input type="text" name="combo_tagline" value="{{ old('combo_tagline') }}" class="form-control"
                                    placeholder="e.g. 2-in-1 Daily Routine for Radiant Hair & Skin">
                                <small class="text-muted">Displayed prominently under the combo title on the storefront card and PDP.</small>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label fw-bold">Combo Badge Text</label>
                                <input type="text" name="combo_badge" value="{{ old('combo_badge') }}" class="form-control"
                                    placeholder="e.g. Save ₹700 or Best Value">
                                <small class="text-muted">Eye-catching pill badge shown on the product card.</small>
                            </div>
                        </div>

                        <div class="border rounded-12 p-20 bg-light-50">
                            <div class="d-flex justify-content-between align-items-center mb-16">
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark">Bundled Products List <span class="text-danger">*</span></h6>
                                    <small class="text-muted">Stock will be automatically deducted from each of these child items upon order placement.</small>
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
                                <p class="mt-2 mb-0 small">No constituent items added yet. Click <strong>Add Bundle Item</strong> to select products.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. PRICING & INVENTORY -->
                <div class="card border-0 radius-12 mb-24">
                    <div class="card-header bg-base border-bottom py-16 px-24">
                        <h5 class="card-title mb-0">Pricing & Inventory</h5>
                    </div>
                    <div class="card-body p-24">
                        <div class="row g-4">
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Combo MRP / Compare (₹)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">₹</span>
                                    <input type="number" step="0.01" min="0" name="compare_at_price"
                                        value="{{ old('compare_at_price') }}" class="form-control border-start-0"
                                        placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-success">Combo Selling Price (₹) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">₹</span>
                                    <input type="number" step="0.01" min="0" name="base_price"
                                        value="{{ old('base_price') }}" class="form-control border-start-0"
                                        placeholder="0.00" required>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-bold">Shipping (₹)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">₹</span>
                                    <input type="number" step="0.01" min="0" name="shipping_price"
                                        value="{{ old('shipping_price', 0) }}" class="form-control border-start-0"
                                        placeholder="0.00">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-bold">Initial Virtual Stock Qty</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><iconify-icon icon="solar:box-linear"></iconify-icon></span>
                                    <input type="number" name="stock_qty" value="{{ old('stock_qty', 100) }}" class="form-control" min="0">
                                </div>
                            </div>

                            <div class="col-md-12 d-flex align-items-center gap-24 pt-2">
                                <input type="hidden" name="track_stock" value="0">
                                <div class="form-check form-switch d-flex align-items-center gap-2 p-0 mb-8">
                                    <input class="form-check-input m-0 float-none" type="checkbox" name="track_stock" value="1"
                                        id="track_stock" checked>
                                    <label class="form-check-label m-0 fw-semibold cursor-pointer" for="track_stock">Track Bundle Virtual Stock</label>
                                </div>
                                <input type="hidden" name="is_active" value="0">
                                <div class="form-check form-switch d-flex align-items-center gap-2 p-0 mb-8">
                                    <input class="form-check-input m-0 float-none" type="checkbox" name="is_active" value="1"
                                        id="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label m-0 fw-semibold cursor-pointer" for="is_active">Publish Now</label>
                                </div>
                                <input type="hidden" name="is_featured" value="0">
                                <div class="form-check form-switch d-flex align-items-center gap-2 p-0 mb-8">
                                    <input class="form-check-input m-0 float-none" type="checkbox" name="is_featured" value="1"
                                        id="is_featured" {{ old('is_featured') ? 'checked' : '' }}>
                                    <label class="form-check-label m-0 fw-semibold cursor-pointer" for="is_featured">Featured on Homepage</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. SUBSCRIBE & SAVE PLANS (DEDICATED FOR THIS COMBO) -->
                @include('admin.ecommerce.products.partials.subscription-plans-settings', ['availableVariants' => $availableVariants])

                <!-- 5. CARD MEDIA -->
                <div class="card border-0 radius-12 mb-24">
                    <div class="card-header bg-base border-bottom py-16 px-24">
                        <h5 class="card-title mb-0">Combo Card Images</h5>
                        <small class="text-secondary">Shown on the Combo Packs shop catalog and cards</small>
                    </div>
                    <div class="card-body p-24">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Default Card Image</label>
                                <input type="file" name="card_image" class="form-control" accept="image/*">
                                <small class="text-muted">Primary combo bundle packaging image.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Hover Card Image</label>
                                <input type="file" name="card_hover_image" class="form-control" accept="image/*">
                                <small class="text-muted">Appears on card hover.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 6. PRODUCT GALLERY -->
                <div class="card border-0 radius-12 mb-24">
                    <div class="card-header bg-base border-bottom py-16 px-24">
                        <h5 class="card-title mb-0">Combo Gallery Images</h5>
                        <small class="text-secondary">Shown in the gallery on the combo detail page</small>
                    </div>
                    <div class="card-body p-24">
                        <div class="upload-area border-dashed radius-12 p-32 text-center cursor-pointer bg-white transition-base border-2"
                            onclick="document.getElementById('images').click()" id="dropZone">
                            <input type="file" name="images[]" id="images" class="d-none" multiple accept="image/*">
                            <div class="mb-12">
                                <iconify-icon icon="solar:camera-add-bold" class="text-primary-600 display-4"></iconify-icon>
                            </div>
                            <h6 class="mb-4 text-dark fw-bold">Click to upload combo gallery images</h6>
                            <p class="text-secondary small mb-0">Up to 5MB each (JPG, PNG, WebP)</p>
                        </div>
                        <div id="imagePreview" class="row g-3 mt-16"></div>
                    </div>
                </div>

                <!-- 7. ACCORDION DESCRIPTIONS (WITH CKEDITOR) -->
                @include('admin.ecommerce.products.partials.accordion-descriptions')

                <!-- 8. REAL RESULTS & CLEAN INGREDIENTS -->
                @include('admin.ecommerce.products.partials.transform-section-fields')
                @include('admin.ecommerce.products.partials.features-section-fields')
                @include('admin.ecommerce.products.partials.formula-section-fields')
                @include('admin.ecommerce.products.partials.banner-section-fields')
                @include('admin.ecommerce.products.partials.faq-section-fields')

                <!-- 9. SEO -->
                <div class="card border-0 radius-12 mb-24">
                    <div class="card-header bg-base border-bottom py-16 px-24">
                        <h5 class="card-title mb-0">SEO Settings (Google Search)</h5>
                    </div>
                    <div class="card-body p-24">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Meta Title</label>
                                <input type="text" name="meta_title" value="{{ old('meta_title') }}" class="form-control"
                                    placeholder="Title for Search Engines">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Meta Keywords</label>
                                <input type="text" name="meta_keywords" value="{{ old('meta_keywords') }}"
                                    class="form-control" placeholder="Keyword1, Keyword2, ...">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Meta Description</label>
                                <textarea name="meta_description" class="form-control" rows="2"
                                    placeholder="Brief summary for Google results">{{ old('meta_description') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SAVE BUTTON -->
                <div class="card bg-white border-0 shadow-lg sticky-bottom py-20 px-32 radius-16 mb-40">
                    <div class="d-flex justify-content-end gap-16">
                        <a href="{{ route('admin.ecommerce.combos.index') }}" class="btn btn-light px-32 fw-bold">Cancel</a>
                        <button type="submit" class="btn btn-primary-600 px-40 fw-bold d-flex align-items-center gap-2">
                            <iconify-icon icon="lucide:check-circle" style="font-size:18px; line-height:1;"></iconify-icon>
                            <span>CREATE COMBO PACK</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- COMBO SCRIPT -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const comboItemsWrapper = document.getElementById('comboItemsWrapper');
        const noComboItemsMsg = document.getElementById('noComboItemsMsg');
        const addComboItemBtn = document.getElementById('addComboItemBtn');

        const availableVariants = [
            @foreach($availableVariants as $v)
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

            const selectEl = tr.querySelector('select');
            selectEl.addEventListener('change', function () {
                const val = this.value;
                if (!val) return;
                const allSelects = comboItemsWrapper.querySelectorAll('select[name^="combo_items"]');
                let duplicateCount = 0;
                allSelects.forEach(function (s) {
                    if (s !== selectEl && s.value === val) {
                        duplicateCount++;
                    }
                });
                if (duplicateCount > 0) {
                    if (window.nbAdminToast) {
                        window.nbAdminToast('This variant is already chosen in another row. Its quantity will be automatically combined on save.', 'warning', 'Duplicate Variant Added');
                    }
                }
            });

            tr.querySelector('.remove-combo-item-btn').addEventListener('click', function () {
                tr.remove();
                syncNoComboItems();
            });

            comboItemsWrapper.appendChild(tr);
            syncNoComboItems();
        }

        if (addComboItemBtn) {
            addComboItemBtn.addEventListener('click', function () {
                addComboItemRow();
            });
        }

        const initialComboItems = @json(old('combo_items', []));
        if (initialComboItems && initialComboItems.length > 0) {
            initialComboItems.forEach(function (item) {
                addComboItemRow(item.child_variant_id, item.quantity || 1);
            });
        } else {
            addComboItemRow();
        }

        // Gallery Images Multi-Upload (Cumulative)
        const dropZone = document.getElementById('dropZone');
        const imagesInput = document.getElementById('images');
        const imagePreview = document.getElementById('imagePreview');
        let selectedGalleryFiles = [];

        function renderGalleryPreviews() {
            imagePreview.innerHTML = '';
            selectedGalleryFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = e => {
                    const col = document.createElement('div');
                    col.className = 'col-6 col-sm-4 col-md-3 col-lg-2 position-relative';
                    col.innerHTML = `
                        <div class="position-relative border radius-8 overflow-hidden bg-light shadow-xs" style="aspect-ratio:1/1;">
                            <img src="${e.target.result}" class="w-100 h-100 object-fit-cover">
                            <button type="button" class="btn btn-danger btn-xs position-absolute top-0 end-0 m-1 p-0 rounded-circle d-flex align-items-center justify-content-center shadow remove-gallery-file" 
                                style="width:22px;height:22px; z-index: 10;" title="Remove this image">
                                <i class="fas fa-times font-xs"></i>
                            </button>
                            <div class="position-absolute bottom-0 start-0 w-100 p-1 bg-dark bg-opacity-50 text-white text-truncate font-xxs">
                                ${file.name}
                            </div>
                        </div>
                    `;
                    imagePreview.appendChild(col);

                    col.querySelector('.remove-gallery-file').addEventListener('click', function(e) {
                        e.stopPropagation();
                        selectedGalleryFiles.splice(index, 1);
                        syncInputFiles();
                        renderGalleryPreviews();
                    });
                };
                reader.readAsDataURL(file);
            });
        }

        function syncInputFiles() {
            const dt = new DataTransfer();
            selectedGalleryFiles.forEach(file => dt.items.add(file));
            imagesInput.files = dt.files;
        }

        if (imagesInput) {
            imagesInput.addEventListener('change', function () {
                const newFiles = Array.from(this.files);
                selectedGalleryFiles = [...selectedGalleryFiles, ...newFiles];
                syncInputFiles();
                renderGalleryPreviews();
            });
        }

        if (dropZone) {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, e => {
                    e.preventDefault();
                    e.stopPropagation();
                }, false);
            });

            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, () => dropZone.classList.add('border-primary'), false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, () => dropZone.classList.remove('border-primary'), false);
            });

            dropZone.addEventListener('drop', e => {
                const droppedFiles = Array.from(e.dataTransfer.files).filter(f => f.type.startsWith('image/'));
                selectedGalleryFiles = [...selectedGalleryFiles, ...droppedFiles];
                syncInputFiles();
                renderGalleryPreviews();
            }, false);
        }
    });
    </script>
@endsection
