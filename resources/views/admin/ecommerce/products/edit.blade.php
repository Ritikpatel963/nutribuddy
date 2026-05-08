@extends('layout.layout')
@php
    $title = 'Edit Product';
    $subTitle = 'Ecommerce / Products / Edit';
@endphp

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <form action="{{ route('admin.ecommerce.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="product_type" value="{{ $product->product_type }}">

                <!-- 1. GENERAL INFORMATION -->
                <div class="card border-0 radius-12 mb-24">
                    <div class="card-header bg-base border-bottom py-16 px-24">
                        <h5 class="card-title mb-0">General Information</h5>
                    </div>
                    <div class="card-body p-24">
                        <div class="row g-4">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Product Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" value="{{ old('name', $product->name) }}" class="form-control" placeholder="e.g. GrowStrong Gummies" required>
                                @error('name')<span class="text-danger small">{{ $message }}</span>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">SKU Code</label>
                                <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" class="form-control" placeholder="Auto generated if empty">
                                @error('sku')<span class="text-danger small">{{ $message }}</span>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">HSN Code</label>
                                <input type="text" name="hsn_code" value="{{ old('hsn_code', $product->hsn_code) }}" class="form-control" placeholder="e.g. 2106">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-select" required>
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tax Rate</label>
                                <select name="tax_rate_id" class="form-select">
                                    <option value="">No Tax / Exempt</option>
                                    @foreach ($taxRates as $tax)
                                        <option value="{{ $tax->id }}" {{ old('tax_rate_id', $product->tax_rate_id) == $tax->id ? 'selected' : '' }}>{{ $tax->name }} ({{ $tax->rate }}%)</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. PRICING & STOCK -->
                <div class="card border-0 radius-12 mb-24">
                    <div class="card-header bg-base border-bottom py-16 px-24">
                        <h5 class="card-title mb-0">Pricing & Inventory</h5>
                    </div>
                    <div class="card-body p-24">
                        <div class="row g-4">
                            <div class="col-md-3">
                                <label class="form-label fw-bold">MRP / Compare (₹)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">₹</span>
                                    <input type="number" step="0.01" min="0" name="compare_at_price" value="{{ old('compare_at_price', $product->compare_at_price) }}" class="form-control border-start-0" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-success">Selling Price (₹) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">₹</span>
                                    <input type="number" step="0.01" min="0" name="base_price" value="{{ old('base_price', $product->base_price) }}" class="form-control border-start-0" placeholder="0.00" required>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-bold">Shipping (₹)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">₹</span>
                                    <input type="number" step="0.01" min="0" name="shipping_price" value="{{ old('shipping_price', $product->shipping_price ?? 0) }}" class="form-control border-start-0" placeholder="0.00">
                                </div>
                            </div>
                            
                            <div class="col-md-6" id="parentStockGroup">
                                <label class="form-label fw-bold">Current Stock Quantity <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><iconify-icon icon="solar:box-linear"></iconify-icon></span>
                                    <input type="number" name="stock_qty" id="productStockInput" value="{{ old('stock_qty', $product->inventory?->stock_qty ?? 0) }}" class="form-control" min="0">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-warning">NB Coins Reward 🪙</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">🪙</span>
                                    <input type="number" name="coins_reward" value="{{ old('coins_reward', $product->coins_reward ?? 0) }}" class="form-control border-start-0" placeholder="e.g. 50">
                                </div>
                                <small class="text-muted">Coins awarded to customer after purchase</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Dosage</label>
                                <input type="text" name="dosage" value="{{ old('dosage', $product->dosage) }}" class="form-control" placeholder="e.g. 1 Gummy daily">
                            </div>

                            <div class="col-md-6 d-flex align-items-end gap-24">
                                <input type="hidden" name="track_stock" value="0">
                                <div class="form-check form-switch d-flex align-items-center gap-2 p-0 mb-8">
                                    <input class="form-check-input m-0 float-none" type="checkbox" name="track_stock" value="1" id="track_stock" {{ old('track_stock', $product->inventory?->track_stock ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label m-0 fw-semibold" for="track_stock">Track Inventory</label>
                                </div>
                                <input type="hidden" name="is_active" value="0">
                                <div class="form-check form-switch d-flex align-items-center gap-2 p-0 mb-8">
                                    <input class="form-check-input m-0 float-none" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label m-0 fw-semibold" for="is_active">Active</label>
                                </div>
                                <input type="hidden" name="is_featured" value="0">
                                <div class="form-check form-switch d-flex align-items-center gap-2 p-0 mb-8">
                                    <input class="form-check-input m-0 float-none" type="checkbox" name="is_featured" value="1" id="is_featured" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                                    <label class="form-check-label m-0 fw-semibold" for="is_featured">Featured</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @include('admin.ecommerce.products._attribute_variations', ['attributes' => $attributes, 'product' => $product])

                <!-- 4. DESCRIPTIONS -->
                <div class="card border-0 radius-12 mb-24">
                    <div class="card-header bg-base border-bottom py-16 px-24">
                        <h5 class="card-title mb-0">Content & Descriptions</h5>
                    </div>
                    <div class="card-body p-24">
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label fw-bold">Full Detailed Description</label>
                                <textarea name="description" id="editor" class="form-control">{{ old('description', $product->description) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 5. MEDIA -->
                <div class="card border-0 radius-12 mb-24">
                    <div class="card-header bg-base border-bottom py-16 px-24">
                        <h5 class="card-title mb-0">Product Gallery</h5>
                    </div>
                    <div class="card-body p-24">
                        <div class="upload-area border-dashed radius-12 p-32 text-center cursor-pointer bg-white transition-base border-2"
                            onclick="document.getElementById('images').click()" id="dropZone">
                            <input type="file" name="images[]" id="images" class="d-none" multiple accept="image/*">
                            <div class="mb-12">
                                <iconify-icon icon="solar:camera-add-bold" class="text-primary-600 display-4"></iconify-icon>
                            </div>
                            <h6 class="mb-4 text-dark fw-bold">Click to upload or drag & drop</h6>
                            <p class="text-secondary small mb-0">Up to 5MB each (JPG, PNG, WebP)</p>
                        </div>
                        
                        <!-- Existing Images -->
                        <div id="existingImages" class="row g-3 mt-16">
                            @foreach($product->images as $img)
                                <div class="col-3 col-md-2 position-relative">
                                    <div class="radius-12 overflow-hidden border bg-white shadow-sm h-100">
                                        <img src="{{ asset('storage/' . $img->image_path) }}" class="w-100 h-100 object-fit-cover" style="aspect-ratio:1/1;">
                                    </div>
                                    <button type="button" class="btn btn-danger btn-xs position-absolute top-0 end-0 m-4 p-0 radius-circle d-flex align-items-center justify-content-center shadow-sm" style="width:22px;height:22px;" onclick="if(confirm('Delete image?')){document.getElementById('delete-image-{{ $img->id }}').submit()}">
                                        <iconify-icon icon="lucide:x" class="text-xs"></iconify-icon>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                        <div id="imagePreview" class="row g-3 mt-16"></div>
                    </div>
                </div>

                <!-- 6. PRODUCT FEATURES -->
                <div class="card border-0 radius-12 mb-24">
                    <div class="card-header bg-base border-bottom d-flex justify-content-between align-items-center py-16 px-24">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary-100 text-primary-600 p-2 rounded-2">
                                <iconify-icon icon="lucide:sparkles" class="fs-5"></iconify-icon>
                            </span>
                            <div>
                                <h5 class="card-title mb-0">Product Features</h5>
                                <small class="text-secondary">Key features shown on the product card and detail page</small>
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary-600 btn-sm px-3 d-inline-flex align-items-center gap-1" id="addTagBtn">
                            <iconify-icon icon="lucide:plus" style="font-size: 16px;"></iconify-icon> Add Feature
                        </button>
                    </div>
                    <div class="card-body p-24">
                        <div id="tagsWrapper">
                            <!-- Feature rows injected here -->
                        </div>
                        <div id="noTagsMessage" class="text-center py-5 text-muted">
                            <iconify-icon icon="lucide:list-checks" class="fs-1 opacity-25"></iconify-icon>
                            <p class="mt-2 mb-0 small">No features yet &mdash; click <strong>Add Feature</strong> to get started.</p>
                        </div>
                    </div>
                </div>

                <!-- 7. SEO -->
                <div class="card border-0 radius-12 mb-24">
                    <div class="card-header bg-base border-bottom py-16 px-24">
                        <h5 class="card-title mb-0">SEO Settings (Google Search)</h5>
                    </div>
                    <div class="card-body p-24">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Meta Title</label>
                                <input type="text" name="meta_title" value="{{ old('meta_title', $product->meta_title) }}" class="form-control" placeholder="Title for Search Engines">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Meta Keywords</label>
                                <input type="text" name="meta_keywords" value="{{ old('meta_keywords', $product->meta_keywords) }}" class="form-control" placeholder="Keyword1, Keyword2, ...">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Meta Description</label>
                                <textarea name="meta_description" class="form-control" rows="2" placeholder="Brief summary for Google results">{{ old('meta_description', $product->meta_description) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SAVE BUTTON -->
                <div class="card bg-white border-0 shadow-lg sticky-bottom py-20 px-32 radius-16 mb-40">
                    <div class="d-flex justify-content-end gap-16">
                        <a href="{{ route('admin.ecommerce.products.index') }}" class="btn btn-light px-32 fw-bold">Discard Changes</a>
                        <button type="submit" class="btn btn-primary-600 px-40 fw-bold d-flex align-items-center gap-2">
                            <iconify-icon icon="lucide:save" style="font-size:18px; line-height:1;"></iconify-icon>
                            <span>UPDATE PRODUCT</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @foreach ($product->images as $image)
        <form id="delete-image-{{ $image->id }}" action="{{ route('admin.ecommerce.products.images.destroy', $image) }}" method="POST" class="d-none">
            @csrf @method('DELETE')
        </form>
    @endforeach

    <!-- SCRIPTS -->
    <script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
    <script>
        ClassicEditor.create(document.querySelector('#editor')).catch(e => console.error(e));

        // ============================================================
        // TAGS MANAGEMENT
        // ============================================================
        let tagCount = 0;
        const tagsWrapper   = document.getElementById('tagsWrapper');
        const noTagsMessage = document.getElementById('noTagsMessage');
        const addTagBtn     = document.getElementById('addTagBtn');

        function syncNoTagsMessage() {
            noTagsMessage.style.display = tagsWrapper.children.length > 0 ? 'none' : 'block';
        }

        function addTagRow(iconPath = '', text = '') {
            const index = tagCount++;
            const row = document.createElement('div');
            row.className = 'tag-row d-flex align-items-center gap-3 px-3 py-2 border-bottom';
            row.innerHTML = `
                <div class="position-relative flex-shrink-0">
                    <div class="tag-icon-preview rounded-2 border bg-light d-flex align-items-center justify-content-center overflow-hidden" style="width:48px;height:48px;">
                        ${iconPath
                            ? `<img src="/storage/${iconPath}" class="w-100 h-100 object-fit-contain">`
                            : `<iconify-icon icon="lucide:image" class="fs-4 text-muted"></iconify-icon>`}
                    </div>
                    <label class="position-absolute bottom-0 end-0 mb-n1 me-n1 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center cursor-pointer" style="width:18px;height:18px;" title="Upload icon">
                        <iconify-icon icon="lucide:pencil" style="font-size:9px;"></iconify-icon>
                        <input type="file" name="tag_images[${index}]" class="d-none tag-image-input" accept="image/*">
                    </label>
                    <input type="hidden" name="tags[${index}][icon]" value="${iconPath}" class="tag-icon-hidden">
                </div>
                <div class="flex-grow-1">
                    <input type="text" name="tags[${index}][text]" value="${text}"
                        class="form-control form-control-sm"
                        placeholder="e.g. No Added Sugar" required>
                </div>
                <button type="button" class="btn btn-sm btn-ghost-danger remove-tag-row flex-shrink-0 px-2">
                    <iconify-icon icon="lucide:trash-2" class="fs-5"></iconify-icon>
                </button>
            `;
            tagsWrapper.appendChild(row);
            syncNoTagsMessage();

            row.querySelector('.tag-image-input').addEventListener('change', function () {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = e => {
                        row.querySelector('.tag-icon-preview').innerHTML =
                            `<img src="${e.target.result}" class="w-100 h-100 object-fit-contain">`;
                        row.querySelector('.tag-icon-hidden').value = '';
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });

            row.querySelector('.remove-tag-row').addEventListener('click', () => {
                row.remove();
                syncNoTagsMessage();
            });
        }

        @php
            $currentTags = $product->tags;
            if (is_string($currentTags)) {
                $currentTags = array_map(function($t) {
                    preg_match('/^([\x{1F300}-\x{1F9FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}])?\s*(.*)$/u', $t, $m);
                    return ['icon' => $m[1] ?? '', 'text' => $m[2] ?? $t];
                }, array_filter(array_map('trim', explode(',', $currentTags))));
            }
        @endphp

        @if(!empty($currentTags) && is_array($currentTags))
            @foreach($currentTags as $tag)
                addTagRow('{{ addslashes($tag['icon'] ?? '') }}', '{{ addslashes($tag['text'] ?? '') }}');
            @endforeach
        @endif

        addTagBtn.addEventListener('click', () => addTagRow());


        // Advanced variants JS removed

        // GALLERY PREVIEW WITH REMOVE OPTION
        const imageInput = document.getElementById('images');
        const previewContainer = document.getElementById('imagePreview');
        const dropZone = document.getElementById('dropZone');
        let selectedFiles = [];

        function renderPreviews() {
            previewContainer.innerHTML = '';
            selectedFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = e => {
                    const col = document.createElement('div');
                    col.className = 'col-4 col-md-3 col-lg-2';
                    col.innerHTML = `
                        <div class="position-relative group radius-12 overflow-hidden border shadow-sm h-100 bg-white">
                            <img src="${e.target.result}" class="w-100 h-100 object-fit-cover" style="aspect-ratio:1/1;">
                            <button type="button" class="btn btn-danger btn-xs position-absolute top-0 end-0 m-2 p-0 radius-circle d-flex align-items-center justify-content-center shadow-sm remove-img" 
                                style="width:24px;height:24px; z-index: 10;" data-index="${index}">
                                <iconify-icon icon="lucide:x" class="text-xs"></iconify-icon>
                            </button>
                            <div class="position-absolute bottom-0 start-0 w-100 p-1 bg-dark bg-opacity-50 text-white text-xxs text-truncate">
                                ${file.name}
                            </div>
                        </div>
                    `;
                    previewContainer.appendChild(col);

                    col.querySelector('.remove-img').addEventListener('click', function(e) {
                        e.stopPropagation();
                        removeFile(index);
                    });
                };
                reader.readAsDataURL(file);
            });
        }

        function removeFile(index) {
            selectedFiles.splice(index, 1);
            updateInputFiles();
            renderPreviews();
        }

        function updateInputFiles() {
            const dataTransfer = new DataTransfer();
            selectedFiles.forEach(file => dataTransfer.items.add(file));
            imageInput.files = dataTransfer.files;
        }

        imageInput.addEventListener('change', function() {
            const newFiles = Array.from(this.files);
            selectedFiles = [...selectedFiles, ...newFiles];
            updateInputFiles();
            renderPreviews();
        });

        // Drag and Drop
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
            const droppedFiles = Array.from(e.dataTransfer.files);
            selectedFiles = [...selectedFiles, ...droppedFiles];
            updateInputFiles();
            renderPreviews();
        }, false);
    </script>

    <style>
        .form-label { font-size: 0.8125rem; margin-bottom: 0.5rem; }
        .card { border-radius: 12px; }
        .bg-light-soft { background-color: #f8fafc; }
        .btn-soft-danger { background: #fee2e2; color: #dc2626; }
        .btn-soft-primary { background: #eef2ff; color: #4f46e5; }
        .upload-area:hover { background: #fff; border-color: var(--primary); }
        .radius-20 { border-radius: 20px; }
        .btn-xs { padding: 4px 8px; font-size: 11px; }
        .text-xxs { font-size: 10px; }
        .sticky-bottom { position: sticky; bottom: 20px; z-index: 1000; }
        .tag-item .card { transition: box-shadow 0.2s ease, transform 0.2s ease; }
        .tag-item .card:hover { box-shadow: 0 6px 20px rgba(0,0,0,.1) !important; transform: translateY(-2px); }
        .radius-12 { border-radius: 12px; }
        .radius-circle { border-radius: 50%; }
    </style>
@endsection
