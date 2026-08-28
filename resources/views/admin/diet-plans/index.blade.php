@extends('layout.layout')
@php
    $title = 'Diet Plans';
    $subTitle = 'Diet Services Management';
@endphp

@section('content')
    @include('admin.ecommerce._messages')

    <div class="card basic-data-table border-0 radius-12 mb-24">
        <div class="card-header bg-base border-bottom py-16 px-24">
            <h5 class="card-title mb-0">Diet Plans List</h5>
        </div>
        <div class="card-body p-24">
            <div class="table-responsive">
                <table class="table bordered-table mb-0" id="dataTable" data-page-length='10'>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Base Price</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($plans as $plan)
                            <tr>
                                <td><span class="text-sm text-secondary-light">{{ $plan->id }}</span></td>
                                <td><span class="text-md fw-semibold text-dark">{{ $plan->name }}</span></td>
                                <td><span class="text-md text-dark">₹{{ number_format($plan->price, 2) }}</span></td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        @if($plan->is_active)
                                            <span class="badge bg-success-100 text-success-600 w-fit">Active</span>
                                        @else
                                            <span class="badge bg-danger-100 text-danger-600 w-fit">Inactive</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex align-items-center justify-content-end gap-2">
                                        <a href="{{ route('admin.ecommerce.diet-plans.edit', $plan) }}"
                                            class="btn btn-sm btn-outline-success-600 radius-8 d-inline-flex align-items-center gap-1">
                                            <iconify-icon icon="lucide:edit"></iconify-icon> Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
