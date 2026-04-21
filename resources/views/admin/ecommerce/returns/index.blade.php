@extends('layout.layout')
@php
    $title = 'Order Returns';
    $subTitle = 'Ecommerce / Returns';
@endphp

@section('content')
    @include('admin.ecommerce._messages')

    <div class="card basic-data-table">
        <div class="card-header">
            <h5 class="card-title mb-0">Return Requests</h5>
        </div>
        <div class="card-body">
            <table class="table bordered-table mb-0" id="dataTable" data-page-length='10'>
                <thead>
                    <tr>
                        <th>Return No.</th>
                        <th>Order No.</th>
                        <th>Customer</th>
                        <th>Refund Amount</th>
                        <th>Status</th>
                        <th>Request Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($returns as $return)
                        <tr>
                            <td><span class="text-primary-600 fw-medium">#{{ $return->return_number }}</span></td>
                            <td><a href="{{ route('admin.ecommerce.orders.show', $return->order_id) }}" class="text-secondary-light fw-medium">#{{ $return->order->order_number }}</a></td>
                            <td>
                                <div class="flex-grow-1">
                                    <h6 class="text-md mb-0 fw-medium">{{ $return->order->user->name }}</h6>
                                    <span class="text-xs text-secondary-light">{{ $return->order->user->email }}</span>
                                </div>
                            </td>
                            <td>INR {{ number_format($return->refund_amount, 2) }}</td>
                            <td>
                                @if ($return->status == 'pending')
                                    <span class="badge bg-warning-focus text-warning-main px-16 py-4 radius-4 fw-medium text-sm">Pending</span>
                                @elseif($return->status == 'approved')
                                    <span class="badge bg-info-focus text-info-main px-16 py-4 radius-4 fw-medium text-sm">Approved</span>
                                @elseif($return->status == 'completed')
                                    <span class="badge bg-success-focus text-success-main px-16 py-4 radius-4 fw-medium text-sm">Completed</span>
                                @else
                                    <span class="badge bg-danger-focus text-danger-main px-16 py-4 radius-4 fw-medium text-sm">Rejected</span>
                                @endif
                            </td>
                            <td>{{ optional($return->created_at)->format('d M Y') ?? 'N/A' }}</td>
                            <td class="text-end">
                                <div class="d-flex align-items-center justify-content-end gap-2">
                                    <a href="{{ route('admin.ecommerce.order-returns.show', $return) }}" class="btn btn-sm btn-primary-600 d-inline-flex align-items-center gap-1">
                                        <iconify-icon icon="lucide:eye"></iconify-icon> View
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('dataTable')) {
                new DataTable('#dataTable');
            }
        });
    </script>
@endsection
