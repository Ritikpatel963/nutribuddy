@extends('layout.layout')
@php
    $title = 'Add New Product';
    $subTitle = 'Ecommerce / Products / Create';
@endphp

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <form action="{{ route('admin.ecommerce.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="product_type" value="simple">

                <!-- 1. GENERAL INFORMATION -->
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-header bg-white py-16">
                        <h6 class="card-title mb-0 text-primary">General Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Product Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" value="{{ old('name') }}" class="form-control"
                                    placeholder="e.g. GrowStrong Gummies" required>
                                @error('name')<span class="text-danger small">{{ $message }}</span>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">SKU Code <span class="text-danger">*</span></label>
                                <input type="text" name="sku" value="{{ old('sku') }}" class="form-control"
                                    placeholder="Unique identifier" required>
                                @error('sku')<span class="text-danger small">{{ $message }}</span>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">HSN Code</label>
                                <input type="text" name="hsn_code" value="{{ old('hsn_code') }}" class="form-control"
                                    placeholder="e.g. 2106">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Brand Name</label>
                                <input type="text" name="brand" value="{{ old('brand', 'NutriBuddy') }}"
                                    class="form-control" placeholder="e.g. NutriBuddy">
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
                                            {{ $tax->name }} ({{ $tax->rate }}%)</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. PRICING & STOCK -->
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-header bg-white py-16">
                        <h6 class="card-title mb-0 text-primary">Pricing & Inventory</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-success">Selling Price (₹) <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">₹</span>
                                    <input type="number" step="0.01" min="0" name="base_price"
                                        value="{{ old('base_price') }}" class="form-control border-start-0"
                                        placeholder="0.00" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">MRP / Compare (₹)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">₹</span>
                                    <input type="number" step="0.01" min="0" name="compare_at_price"
                                        value="{{ old('compare_at_price') }}" class="form-control border-start-0"
                                        placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Cost Price (₹)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">₹</span>
                                    <input type="number" step="0.01" min="0" name="cost_price"
                                        value="{{ old('cost_price') }}" class="form-control border-start-0"
                                        placeholder="0.00">
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

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Stock Quantity <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><iconify-icon
                                            icon="solar:box-linear"></iconify-icon></span>
                                    <input type="number" name="stock_qty" id="productStockInput"
                                        value="{{ old('stock_qty', 0) }}" class="form-control" min="0" required>
                                </div>
                            </div>

                            <div class="col-md-6 d-flex align-items-end gap-24">
                                <div class="form-check form-switch mb-8">
                                    <input type="hidden" name="track_stock" value="0">
                                    <input class="form-check-input" type="checkbox" name="track_stock" value="1"
                                        id="track_stock" checked>
                                    <label class="form-check-label fw-semibold" for="track_stock">Track Inventory</label>
                                </div>
                                <div class="form-check form-switch mb-8">
                                    <input type="hidden" name="is_active" value="0">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                        id="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="is_active">Publish Now</label>
                                </div>
                                <div class="form-check form-switch mb-8">
                                    <input type="hidden" name="is_featured" value="0">
                                    <input class="form-check-input" type="checkbox" name="is_featured" value="1"
                                        id="is_featured" {{ old('is_featured') ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="is_featured">Featured</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. PRODUCT ATTRIBUTES (FLAVOR, PACK SIZE, AGE) -->
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-header bg-white py-16">
                        <h6 class="card-title mb-0 text-primary">Product Attributes</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Flavor (e.g. Mango, Berry)</label>
                                <input type="text" name="flavor" value="{{ old('flavor') }}" class="form-control" placeholder="Product flavor">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Pack Size (e.g. 30 Gummies, 100ml)</label>
                                <input type="text" name="pack_size" value="{{ old('pack_size') }}" class="form-control" placeholder="Quantity and unit">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Age Group (e.g. 2-7 Yrs, Adult)</label>
                                <input type="text" name="age_group" value="{{ old('age_group') }}" class="form-control" placeholder="Target age range">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. DESCRIPTIONS -->
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-header bg-white py-16">
                        <h6 class="card-title mb-0 text-primary">Content & Descriptions</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label fw-bold">Short Description (Summary)</label>
                                <textarea name="short_description" class="form-control" rows="2"
                                    placeholder="Brief intro for product list page">{{ old('short_description') }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Full Detailed Description</label>
                                <textarea name="description" id="editor"
                                    class="form-control">{{ old('description') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 5. MEDIA -->
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-header bg-white py-16">
                        <h6 class="card-title mb-0 text-primary">Product Gallery</h6>
                    </div>
                    <div class="card-body">
                        <div class="upload-area border-dashed radius-12 p-32 text-center cursor-pointer bg-light-soft transition-base"
                            onclick="document.getElementById('images').click()">
                            <input type="file" name="images[]" id="images" class="d-none" multiple accept="image/*">
                            <div class="mb-12">
                                <iconify-icon icon="solar:upload-minimalistic-bold"
                                    class="text-primary fs-1"></iconify-icon>
                            </div>
                            <p class="mb-4 text-dark fw-bold">Drop files here or click to upload</p>
                            <p class="text-secondary small mb-0">JPEG, PNG, WebP (Max 5MB per image)</p>
                        </div>
                        <div id="imagePreview" class="row g-3 mt-16"></div>
                    </div>
                </div>

                <!-- 6. HIGHLIGHTS (TAGS) -->
                <div class="card mb-4 shadow-sm border-0 overflow-hidden">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-16">
                        <h6 class="card-title mb-0 text-primary">Key Highlights (Tags)</h6>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addTagBtn">
                            <iconify-icon icon="lucide:plus" class="me-1"></iconify-icon> Add Tag
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light small fw-bold">
                                    <tr>
                                        <th class="ps-24" style="width: 180px;">Icon / Image</th>
                                        <th>Label</th>
                                        <th class="text-center" style="width: 80px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="tagsTableBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- 7. SEO -->
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-header bg-white py-16">
                        <h6 class="card-title mb-0 text-primary">SEO Settings (Google Search)</h6>
                    </div>
                    <div class="card-body">
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
                        <a href="{{ route('admin.ecommerce.products.index') }}"
                            class="btn btn-light px-32 fw-bold">Cancel</a>
                        <button type="submit" class="btn btn-primary-600 px-40 fw-bold">
                            <iconify-icon icon="lucide:check-circle" class="me-8"></iconify-icon> CREATE PRODUCT
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- SCRIPTS -->
    <script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
    <script>
        ClassicEditor.create(document.querySelector('#editor')).catch(e => console.error(e));

        // ============================================================
        // TAGS MANAGEMENT
        // ============================================================
        let tagCount = 0;
        const tagsTableBody = document.getElementById('tagsTableBody');
        const addTagBtn = document.getElementById('addTagBtn');

        function addTagRow(iconPath = '', text = '', isEmoji = true) {
            const index = tagCount++;
            const tr = document.createElement('tr');
            tr.className = 'tag-row animate__animated animate__fadeIn';
            tr.innerHTML = `
                    <td class="ps-24 py-12">
                        <div class="d-flex align-items-center gap-12">
                            <div class="tag-icon-preview border radius-circle d-flex align-items-center justify-content-center bg-white shadow-sm" style="width:40px;height:40px; min-width:40px; overflow:hidden">
                                ${iconPath ? `<img src="${isEmoji ? '' : '/storage/' + iconPath}" class="w-100 h-100 object-fit-cover" style="display: ${isEmoji ? 'none' : 'block'};">` : '<iconify-icon icon="lucide:image" class="text-secondary-light"></iconify-icon>'}
                                <span class="emoji-display fs-5" style="display: ${isEmoji ? 'block' : 'none'}">${isEmoji ? iconPath : ''}</span>
                            </div>
                            <div class="flex-grow-1">
                                <input type="file" name="tag_images[${index}]" class="form-control form-control-xs tag-image-input" accept="image/*" style="font-size: 10px;">
                                <input type="hidden" name="tags[${index}][icon]" value="${iconPath}" class="tag-icon-hidden">
                            </div>
                        </div>
                    </td>
                    <td>
                        <input type="text" name="tags[${index}][text]" value="${text}" class="form-control" placeholder="e.g. Boosts Immunity" required>
                    </td>
                    <td class="text-center pe-24">
                        <button type="button" class="btn btn-sm btn-icon btn-soft-danger remove-tag-row border-0 radius-circle">
                            <iconify-icon icon="lucide:trash-2"></iconify-icon>
                        </button>
                    </td>
                `;
            tagsTableBody.appendChild(tr);

            const fileInput = tr.querySelector('.tag-image-input');
            const hiddenInput = tr.querySelector('.tag-icon-hidden');
            const previewDiv = tr.querySelector('.tag-icon-preview');

            fileInput.addEventListener('change', function () {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = e => {
                        previewDiv.innerHTML = `<img src="${e.target.result}" class="w-100 h-100 object-fit-cover">`;
                        hiddenInput.value = '';
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });

            tr.querySelector('.remove-tag-row').addEventListener('click', () => tr.remove());
        }

        addTagBtn.addEventListener('click', () => addTagRow());

        // Advanced variants JS removed

        // GALLERY PREVIEW
        document.getElementById('images').addEventListener('change', function () {
            const preview = document.getElementById('imagePreview');
            preview.innerHTML = '';
            [...this.files].forEach(file => {
                const reader = new FileReader();
                reader.onload = e => {
                    const col = document.createElement('div');
                    col.className = 'col-3 col-md-2';
                    col.innerHTML = `<div class="radius-12 overflow-hidden border bg-white shadow-sm"><img src="${e.target.result}" class="w-100 h-100 object-fit-cover" style="aspect-ratio:1/1;"></div>`;
                    preview.appendChild(col);
                };
                reader.readAsDataURL(file);
            });
        });
    </script>

    <style>
        .form-label {
            font-size: 0.8125rem;
            margin-bottom: 0.5rem;
        }

        .card {
            border-radius: 12px;
        }

        .bg-light-soft {
            background-color: #f8fafc;
        }

        .btn-soft-danger {
            background: #fee2e2;
            color: #dc2626;
        }

        .btn-soft-primary {
            background: #eef2ff;
            color: #4f46e5;
        }

        .upload-area:hover {
            background: #fff;
            border-color: var(--primary);
        }

        .radius-20 {
            border-radius: 20px;
        }

        .btn-xs {
            padding: 4px 8px;
            font-size: 11px;
        }

        .text-xxs {
            font-size: 10px;
        }

        .sticky-bottom {
            position: sticky;
            bottom: 20px;
            z-index: 1000;
        }
    </style>
@endsection