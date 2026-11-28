@extends('layouts.admin')
@php
    $title = 'Combo Packs';
    $subTitle = 'Ecommerce / Combo Packs';
@endphp

@section('content')
    <div class="card basic-data-table border-0 radius-12 mb-24">
        <div class="card-header bg-base border-bottom py-16 px-24 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary-50 text-primary-600 p-2 rounded-2 d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                        <iconify-icon icon="solar:box-minimalistic-bold-duotone" class="fs-4"></iconify-icon>
                    </span>
                    <div>
                        <h5 class="card-title mb-0">Combo Packs Management</h5>
                        <small class="text-secondary">Virtual bundle products containing multiple constituent items</small>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('admin.ecommerce.products.index') }}" class="btn btn-sm btn-outline-secondary">
                    <iconify-icon icon="solar:box-outline"></iconify-icon> Standard Products
                </a>
                <a href="{{ route('admin.ecommerce.combos.create') }}" class="btn btn-sm btn-primary-600 d-inline-flex align-items-center gap-1">
                    <i class="ri-add-line"></i> Create New Combo Pack
                </a>
            </div>
        </div>
        <div class="card-body p-24">
            @if($combos->isEmpty())
                <div class="text-center py-5 text-muted">
                    <iconify-icon icon="solar:box-minimalistic-linear" class="fs-1 opacity-25"></iconify-icon>
                    <p class="mt-2 mb-3">No combo packs created yet.</p>
                    <a href="{{ route('admin.ecommerce.combos.create') }}" class="btn btn-sm btn-primary-600">
                        Create Your First Combo Pack
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table bordered-table mb-0" id="dataTable" data-page-length='10'>
                        <thead>
                            <tr>
                                <th style="width: 70px;">Image</th>
                                <th style="min-width: 260px;">Combo Details</th>
                                <th style="min-width: 220px;">Bundled Child Items</th>
                                <th>Pricing</th>
                                <th>Subscriptions</th>
                                <th>Status</th>
                                <th class="text-end" style="width: 120px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($combos as $combo)
                                @php
                                    $primaryImage = $combo->images->where('is_primary', true)->first() ?? $combo->images->first();
                                    $imageSrc = $combo->card_image_path
                                        ? asset('storage/' . $combo->card_image_path)
                                        : ($primaryImage ? asset('storage/' . $primaryImage->image_path) : asset('assets/images/logo-icon.png'));
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $imageSrc }}" 
                                                alt="{{ $combo->name }}" class="w-56-px h-56-px radius-8 border flex-shrink-0 object-fit-cover shadow-xs">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <a href="{{ route('admin.ecommerce.combos.edit', $combo) }}" class="fw-bold text-dark text-hover-primary">
                                                    {{ $combo->name }}
                                                </a>
                                                @if($combo->combo_badge)
                                                    <span class="badge bg-success-50 text-success-700 border border-success-200 py-1 px-2 font-xs">
                                                        {{ $combo->combo_badge }}
                                                    </span>
                                                @endif
                                            </div>
                                            @if($combo->combo_tagline)
                                                <small class="text-secondary text-truncate" style="max-width: 320px;">{{ $combo->combo_tagline }}</small>
                                            @endif
                                            <small class="text-muted mt-1">SKU: <span class="fw-semibold text-dark">{{ $combo->sku ?? 'N/A' }}</span></small>
                                        </div>
                                    </td>
                                    <td>
                                        @if($combo->comboItems->isNotEmpty())
                                            <div class="d-flex flex-column gap-1">
                                                @foreach($combo->comboItems as $item)
                                                    <div class="d-flex align-items-center gap-1 font-xs text-secondary">
                                                        <span class="badge bg-light text-dark border px-1">{{ $item->quantity }}x</span>
                                                        <span>{{ $item->childVariant?->product?->name ?? 'Product' }} &mdash; <span class="fw-semibold">{{ $item->childVariant?->name ?? 'Default' }}</span></span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="badge bg-warning-50 text-warning-700">No items mapped</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold text-success font-md">₹{{ number_format($combo->base_price, 2) }}</span>
                                            @if($combo->compare_at_price > $combo->base_price)
                                                <span class="text-muted text-decoration-line-through font-xs">₹{{ number_format($combo->compare_at_price, 2) }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($combo->subscriptionPlans->isNotEmpty())
                                            <span class="badge bg-info-50 text-info-700 border border-info-200">
                                                {{ $combo->subscriptionPlans->count() }} Custom Tiers
                                            </span>
                                        @else
                                            <span class="badge bg-light text-secondary border">Store Defaults</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($combo->is_active)
                                            <span class="badge bg-success-100 text-success-600 px-3 py-1 radius-4">Published</span>
                                        @else
                                            <span class="badge bg-neutral-200 text-neutral-600 px-3 py-1 radius-4">Draft</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="d-inline-flex gap-2">
                                            <a href="{{ route('product.show', $combo->slug) }}" target="_blank"
                                                class="w-32-px h-32-px bg-light-soft text-secondary rounded-circle d-inline-flex align-items-center justify-content-center"
                                                title="View Storefront PDP">
                                                <i class="ri-eye-line"></i>
                                            </a>
                                            <a href="{{ route('admin.ecommerce.combos.edit', $combo) }}"
                                                class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center"
                                                title="Edit Combo">
                                                <i class="ri-edit-line"></i>
                                            </a>
                                            <form action="{{ route('admin.ecommerce.combos.destroy', $combo) }}" method="POST"
                                                class="d-inline" onsubmit="return confirm('Are you sure you want to delete this combo pack?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center border-0"
                                                    title="Delete Combo">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
