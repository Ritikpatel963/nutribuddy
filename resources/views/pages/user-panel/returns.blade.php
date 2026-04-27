@extends('layouts.user-panel')
@section('title', 'My Returns — NutriBuddy Kids')
@section('panel-page-class', 'panel-returns')
@section('panel-content')
    <div class="inner-topbar">
        <button class="sidebar-toggle" onclick="toggleSidebar()">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <line x1="3" y1="6" x2="21" y2="6" />
                <line x1="3" y1="12" x2="21" y2="12" />
                <line x1="3" y1="18" x2="21" y2="18" />
            </svg>
        </button>
        <span class="it-title">Return History ↩️</span>
        <div style="width:36px"></div>
    </div>

    <div class="page">
        @if(session('success'))
            <div class="alert alert-success" style="background: #e8f5e9; color: #2e7d32; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger" style="background: #ffebee; color: #c62828; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                {{ session('error') }}
            </div>
        @endif

        <div class="orders-card fade-in d1">
            @forelse($returns as $return)
                <div class="order-item" style="border-bottom: 1px solid #eee; padding: 20px 0; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 5px;">
                            <span style="font-weight: bold; font-size: 1.1rem;">#{{ $return->return_number }}</span>
                            <span class="status-badge {{ $return->status === 'completed' ? 's-delivered' : ($return->status === 'rejected' ? 's-cancelled' : 's-pending') }}" style="font-size: 0.7rem; padding: 4px 10px;">
                                {{ strtoupper($return->status) }}
                            </span>
                        </div>
                        <p style="margin: 0; color: #666; font-size: 0.9rem;">
                            Requested for Order: <a href="{{ route('user.orders.detail-page', $return->order_id) }}" style="color: var(--primary-color); text-decoration: none; font-weight: 500;">#{{ $return->order->order_number }}</a>
                        </p>
                        <p style="margin: 5px 0 0; font-size: 0.85rem; color: #888;">
                            Date: {{ $return->created_at->format('d M Y') }}
                        </p>
                        <div style="margin-top: 10px; background: #f9f9f9; padding: 8px 12px; border-radius: 8px; font-size: 0.85rem;">
                            <strong>Reason:</strong> {{ $return->reason }}
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-weight: bold; color: var(--primary-color); font-size: 1.1rem; margin-bottom: 5px;">
                            ₹{{ number_format($return->refund_amount, 2) }}
                        </div>
                        <span style="font-size: 0.75rem; color: #999;">Refund Amount</span>
                    </div>
                </div>
            @empty
                <div style="text-align: center; padding: 60px 20px;">
                    <div style="font-size: 4rem; margin-bottom: 20px;">📦</div>
                    <h3>No return requests found</h3>
                    <p style="color: #666;">You haven't requested any returns yet.</p>
                    <a href="{{ route('order') }}" class="status-badge s-delivered" style="text-decoration: none; display: inline-block; margin-top: 15px; border: none; padding: 12px 25px;">View My Orders</a>
                </div>
            @endforelse

            <div style="margin-top: 20px;">
                {{ $returns->links() }}
            </div>
        </div>
    </div>
@endsection
