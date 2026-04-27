@extends('layout.layout')
@php
    $title = 'Billing & Invoices';
    $subTitle = 'Ecommerce / Invoices';
@endphp

@section('content')
    <div class="card basic-data-table">
        <div class="card-header d-flex align-items-center justify-content-between gap-3">
            <h5 class="card-title mb-0">Invoice List</h5>
            <form action="{{ route('admin.ecommerce.invoices.index') }}" method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control form-control-sm w-auto" placeholder="Order #, Name..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-sm btn-primary">Search</button>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table bordered-table mb-0">
                    <thead>
                        <tr>
                            <th>Invoice ID</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Order ID</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.ecommerce.invoices.show', $order) }}" class="text-primary-600 fw-bold">#INV-{{ $order->order_number }}</a>
                                </td>
                                <td>{{ optional($order->placed_at)->format('d M Y') ?? $order->created_at->format('d M Y') }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <h6 class="text-md mb-0 fw-medium">{{ $order->customer_name }}</h6>
                                            <span class="text-sm text-secondary-light">{{ $order->customer_email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>#{{ $order->order_number }}</td>
                                <td>₹{{ number_format($order->grand_total, 2) }}</td>
                                <td>
                                    <span class="badge {{ $order->payment_status === 'paid' ? 'bg-success-100 text-success-600' : 'bg-warning-100 text-warning-600' }}">
                                        {{ strtoupper($order->payment_status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ route('admin.ecommerce.invoices.show', $order) }}" class="w-32-px h-32-px bg-primary-light text-primary-600 rounded-circle d-flex align-items-center justify-content-center" title="View Details">
                                            <iconify-icon icon="lucide:eye"></iconify-icon>
                                        </a>
                                        <a href="{{ route('user.orders.invoice-download', $order) }}?print=1" target="_blank" class="w-32-px h-32-px bg-success-light text-success-600 rounded-circle d-flex align-items-center justify-content-center" title="Print Invoice">
                                            <iconify-icon icon="lucide:printer"></iconify-icon>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">No invoices found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-24">
                <p class="text-secondary-light">Showing {{ $orders->firstItem() ?? 0 }} to {{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }} entries</p>
                {{ $orders->links() }}
            </div>
        </div>
    </div>
@endsection
