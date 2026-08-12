@extends('layout.layout')
@php
    $title = 'Problem Solution';
    $subTitle = 'Ecommerce / Products / Problem Solution';
@endphp

@section('content')
    @include('admin.ecommerce._messages')

    <div class="card basic-data-table border-0 radius-12 mb-24">
        <div class="card-header bg-base border-bottom py-16 px-24 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h5 class="card-title mb-0">Problem Solution Sections</h5>
                <p class="text-secondary-light mb-0 mt-1">Manage the dynamic answer section for each product detail page.</p>
            </div>

            <form action="{{ route('admin.ecommerce.products.problem-solution.index') }}" method="GET" class="d-flex align-items-center gap-2">
                <select name="category_id" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
                <a href="{{ route('admin.ecommerce.products.index') }}" class="btn btn-sm btn-outline-secondary radius-8">Products</a>
            </form>
        </div>

        <div class="card-body p-24">
            <div class="table-responsive">
                <table class="table bordered-table mb-0" id="problemSolutionTable" data-page-length="10">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Product</th>
                            <th>Section Status</th>
                            <th>Cards</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            @php
                                $primaryImage = $product->images->where('is_primary', true)->first() ?? $product->images->first();
                                $leftCount = count($product->ps_left_cards ?: []);
                                $rightCount = count($product->ps_right_cards ?: []);
                                $hasCustomSection = $product->ps_brand_title
                                    || $product->ps_product_title
                                    || !empty($product->ps_left_cards)
                                    || !empty($product->ps_right_cards)
                                    || $product->ps_center_image
                                    || $product->ps_shelf_left_image
                                    || $product->ps_shelf_right_image;
                            @endphp
                            <tr>
                                <td>
                                    <img src="{{ $primaryImage ? asset('storage/' . $primaryImage->image_path) : asset('assets/images/logo-icon.png') }}"
                                        alt="" class="w-48-px h-48-px radius-8 border flex-shrink-0 object-fit-cover">
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="text-md fw-semibold text-dark mb-0">{{ $product->name }}</span>
                                        <small class="text-secondary-light">{{ $product->category?->name ?: 'Uncategorized' }}</small>
                                    </div>
                                </td>
                                <td>
                                    @if ($hasCustomSection)
                                        <span class="badge bg-success-100 text-success-600">Custom</span>
                                    @else
                                        <span class="badge bg-warning-100 text-warning-600">Using Default</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-primary-100 text-primary-600">{{ $leftCount }} Left</span>
                                    <span class="badge bg-info-100 text-info-600">{{ $rightCount }} Right</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.ecommerce.products.problem-solution.edit', $product) }}"
                                        class="btn btn-sm btn-outline-success-600 radius-8 d-inline-flex align-items-center gap-1">
                                        <iconify-icon icon="lucide:edit-3"></iconify-icon> Edit Section
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-secondary-light py-24">No products found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('problemSolutionTable') && window.DataTable) {
                new DataTable('#problemSolutionTable');
            }
        });
    </script>
@endsection
