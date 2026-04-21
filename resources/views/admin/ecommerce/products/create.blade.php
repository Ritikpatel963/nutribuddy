@extends('layout.layout')
@php
    $title = 'Create Product';
    $subTitle = 'Ecommerce / Products / Create';
@endphp

@section('content')
    <div class="row g-4">
        <div class="col-lg-12">
            @include('admin.ecommerce._messages')
            
            <form method="POST" action="{{ route('admin.ecommerce.products.store') }}" enctype="multipart/form-data">
                @csrf
                
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Product Information</h5>
                        <a href="{{ route('admin.ecommerce.products.index') }}" class="btn btn-sm btn-secondary">Back to List</a>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Product Name</label>
                                <div class="icon-field">
                                    <span class="icon">
                                        <iconify-icon icon="f7:bag"></iconify-icon>
                                    </span>
                                    <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="Enter product name" required>
                                </div>
                                @error('name')<span class="text-danger small">{{ $message }}</span>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">SKU</label>
                                <div class="icon-field">
                                    <span class="icon">
                                        <iconify-icon icon="ant-design:barcode-outlined"></iconify-icon>
                                    </span>
                                    <input type="text" name="sku" value="{{ old('sku') }}" class="form-control" placeholder="SKU Code" required>
                                </div>
                                @error('sku')<span class="text-danger small">{{ $message }}</span>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Product Type</label>
                                <select name="product_type" class="form-select" required>
                                    <option value="simple" {{ old('product_type') == 'simple' ? 'selected' : '' }}>Simple</option>
                                    <option value="variable" {{ old('product_type') == 'variable' ? 'selected' : '' }}>Variable</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Category</label>
                                <select name="category_id" class="form-select">
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">HSN Code</label>
                                <div class="icon-field">
                                    <span class="icon">
                                        <iconify-icon icon="lucide:shield-check"></iconify-icon>
                                    </span>
                                    <input type="text" name="hsn_code" value="{{ old('hsn_code') }}" class="form-control" placeholder="HSN Code">
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label">Base Price (INR)</label>
                                <div class="icon-field">
                                    <span class="icon">
                                        <iconify-icon icon="lucide:indian-rupee"></iconify-icon>
                                    </span>
                                    <input type="number" step="0.01" min="0" name="base_price" value="{{ old('base_price') }}" class="form-control" placeholder="0.00" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Compare At Price</label>
                                <div class="icon-field">
                                    <span class="icon">
                                        <iconify-icon icon="lucide:indian-rupee"></iconify-icon>
                                    </span>
                                    <input type="number" step="0.01" min="0" name="compare_at_price" value="{{ old('compare_at_price') }}" class="form-control" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Shipping Price</label>
                                <div class="icon-field">
                                    <span class="icon">
                                        <iconify-icon icon="lucide:truck"></iconify-icon>
                                    </span>
                                    <input type="number" step="0.01" min="0" name="shipping_price" value="{{ old('shipping_price') }}" class="form-control" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tax Rate</label>
                                <select name="tax_rate_id" class="form-select">
                                    <option value="">No Tax</option>
                                    @foreach ($taxRates as $taxRate)
                                        <option value="{{ $taxRate->id }}" {{ old('tax_rate_id') == $taxRate->id ? 'selected' : '' }}>{{ $taxRate->name }} ({{ $taxRate->rate }}%)</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Short Description</label>
                                <textarea name="short_description" class="form-control" rows="2">{{ old('short_description') }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Full Description</label>
                                <textarea name="description" id="editor" class="form-control" rows="5">{{ old('description') }}</textarea>
                            </div>

                            <div class="col-md-4">
                                <div class="form-check form-switch pt-8">
                                    <input type="hidden" name="is_active" value="0">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label">Active Listing</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch pt-8">
                                    <input type="hidden" name="is_featured" value="0">
                                    <input class="form-check-input" type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                                    <label class="form-check-label">Featured Product</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch pt-8">
                                    <input type="hidden" name="is_variant_enabled" value="0">
                                    <input class="form-check-input" type="checkbox" name="is_variant_enabled" value="1" {{ old('is_variant_enabled') ? 'checked' : '' }}>
                                    <label class="form-check-label">Enable Variants</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Product Gallery</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Upload Images</label>
                                <div class="upload-area border-dashed radius-8 p-32 text-center" id="uploadArea">
                                    <input type="file" name="images[]" id="images" class="d-none" multiple accept="image/*">
                                    <label for="images" class="cursor-pointer">
                                        <iconify-icon icon="solar:upload-minimalistic-bold" class="text-primary-600 text-4xl mb-8"></iconify-icon>
                                        <p class="mb-0 text-secondary-light">Click to upload or drag and drop</p>
                                        <p class="text-xs text-secondary-light">JPEG, PNG, JPG or WEBP (max. 5MB per image)</p>
                                    </label>
                                </div>
                                <div id="imagePreview" class="row g-3 mt-16"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Initial Stock</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Opening Stock</label>
                                <div class="icon-field">
                                    <span class="icon">
                                        <iconify-icon icon="solar:box-linear"></iconify-icon>
                                    </span>
                                    <input type="number" name="stock_qty" value="{{ old('stock_qty', 0) }}" class="form-control" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Low Stock Threshold</label>
                                <div class="icon-field">
                                    <span class="icon">
                                        <iconify-icon icon="solar:bell-bing-linear"></iconify-icon>
                                    </span>
                                    <input type="number" name="low_stock_threshold" value="{{ old('low_stock_threshold', 5) }}" class="form-control" min="0">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check form-switch pt-28">
                                    <input type="hidden" name="track_stock" value="0">
                                    <input class="form-check-input" type="checkbox" name="track_stock" value="1" {{ old('track_stock', true) ? 'checked' : '' }}>
                                    <label class="form-check-label">Track Inventory</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check form-switch pt-28">
                                    <input type="hidden" name="is_in_stock" value="0">
                                    <input class="form-check-input" type="checkbox" name="is_in_stock" value="1" {{ old('is_in_stock', true) ? 'checked' : '' }}>
                                    <label class="form-check-label">Is In Stock</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">SEO Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">Meta Title</label>
                                <div class="icon-field">
                                    <span class="icon">
                                        <iconify-icon icon="lucide:type"></iconify-icon>
                                    </span>
                                    <input type="text" name="meta_title" value="{{ old('meta_title') }}" class="form-control" placeholder="SEO optimized title">
                                </div>
                                <p class="text-xs text-secondary-light mt-8">Recommended: 50-60 characters.</p>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Meta Description</label>
                                <textarea name="meta_description" class="form-control" rows="3" placeholder="Brief summary for search results">{{ old('meta_description') }}</textarea>
                                <p class="text-xs text-secondary-light mt-8">Recommended: 150-160 characters.</p>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Meta Keywords</label>
                                <div class="icon-field">
                                    <span class="icon">
                                        <iconify-icon icon="lucide:hash"></iconify-icon>
                                    </span>
                                    <input type="text" name="meta_keywords" value="{{ old('meta_keywords') }}" class="form-control" placeholder="keyword1, keyword2, keyword3">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary-600 px-32">Create Product</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create( document.querySelector( '#editor' ) )
        .catch( error => {
            console.error( error );
        } );

    // Image Preview logic
    document.getElementById('images').addEventListener('change', function(e) {
        const preview = document.getElementById('imagePreview');
        preview.innerHTML = '';
        
        if (this.files) {
            [...this.files].forEach(file => {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const col = document.createElement('div');
                    col.className = 'col-md-2 col-sm-4 col-6';
                    col.innerHTML = `
                        <div class="position-relative radius-8 overflow-hidden border">
                            <img src="${event.target.result}" class="w-100 h-100 object-fit-cover" style="aspect-ratio: 1/1;">
                        </div>
                    `;
                    preview.appendChild(col);
                }
                reader.readAsDataURL(file);
            });
        }
    });
</script>
@endsection
