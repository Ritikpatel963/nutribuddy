@extends('layout.layout')

@php
    $title = 'Diet Subscriptions';
    $subTitle = 'Diet Services Management / Subscriptions';
    $script = '<script>
        $(".remove-item-btn").on("click", function() {
            $(this).closest("tr").addClass("d-none");
        });
    </script>';
@endphp

@section('content')

<div class="card basic-data-table">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0">Diet Subscriptions</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table bordered-table mb-0" id="dataTable" data-page-length="10">
                <thead>
                    <tr>
                        <th scope="col">User</th>
                        <th scope="col">Plan</th>
                        <th scope="col">Amount Paid</th>
                        <th scope="col">Status</th>
                        <th scope="col">Start Date</th>
                        <th scope="col">End Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subscriptions as $sub)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="text-md mb-0 fw-normal">{{ $sub->user->name ?? 'Guest' }}</h6>
                                    <span class="text-sm text-secondary-light fw-normal">{{ $sub->user->email ?? '' }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="fw-semibold text-primary-light">{{ $sub->model ?? ($sub->subscriptionPlan->name ?? 'Unknown') }}</span>
                        </td>
                        <td>₹{{ number_format($sub->amount_paid, 2) }}</td>
                        <td>
                            @if($sub->status == 'active')
                                <span class="bg-success-focus text-success-main px-24 py-4 rounded-pill fw-medium text-sm">Active</span>
                            @elseif($sub->status == 'expired')
                                <span class="bg-danger-focus text-danger-main px-24 py-4 rounded-pill fw-medium text-sm">Expired</span>
                            @elseif($sub->status == 'pending')
                                <span class="bg-warning-focus text-warning-main px-24 py-4 rounded-pill fw-medium text-sm">Pending</span>
                            @else
                                <span class="bg-secondary-focus text-secondary-main px-24 py-4 rounded-pill fw-medium text-sm">{{ ucfirst($sub->status) }}</span>
                            @endif
                        </td>
                        <td>{{ $sub->starts_at ? \Carbon\Carbon::parse($sub->starts_at)->format('d M Y') : 'N/A' }}</td>
                        <td>{{ $sub->expires_at ? \Carbon\Carbon::parse($sub->expires_at)->format('d M Y') : 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-end mt-4">
            {{ $subscriptions->links() }}
        </div>
    </div>
</div>

@endsection
