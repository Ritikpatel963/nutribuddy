@extends('layout.layout')
@php
    $title = 'Loyalty Settings';
    $subTitle = 'Manage NB Coins earning and redemption rules';
@endphp

@section('content')
<style>
    .coin-input-group {
        display: flex;
        align-items: stretch;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
        background: #fff;
        transition: border-color 0.2s;
    }
    .coin-input-group:focus-within {
        border-color: #be185d;
        box-shadow: 0 0 0 3px rgba(190,24,93,0.08);
    }
    .coin-input-group .coin-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 14px;
        background: #fdf2f8;
        color: #be185d;
        font-size: 18px;
        border-right: 1.5px solid #e2e8f0;
        flex-shrink: 0;
    }
    .coin-input-group .coin-icon.warning {
        background: #fffbeb;
        color: #d97706;
        border-right: 1.5px solid #e2e8f0;
    }
    .coin-input-group input {
        border: none !important;
        box-shadow: none !important;
        outline: none !important;
        border-radius: 0 !important;
        flex: 1;
        padding: 10px 14px;
        font-size: 0.95rem;
        background: transparent;
    }
    .coin-input-group .coin-suffix {
        display: flex;
        align-items: center;
        padding: 0 14px;
        background: #f8fafc;
        color: #64748b;
        font-size: 0.82rem;
        font-weight: 600;
        border-left: 1.5px solid #e2e8f0;
        white-space: nowrap;
        flex-shrink: 0;
    }
</style>

<div class="row g-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">NB Coins Configuration</h5>
            </div>
            <div class="card-body">
                @include('admin.ecommerce._messages')

                <form action="{{ route('admin.ecommerce.loyalty.settings.update') }}" method="POST">
                    @csrf
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Conversion Rate (Coins to INR)</label>
                            <div class="coin-input-group">
                                <span class="coin-icon">
                                    <iconify-icon icon="solar:money-bag-outline"></iconify-icon>
                                </span>
                                <input type="number" name="loyalty_conversion_rate"
                                    value="{{ old('loyalty_conversion_rate', $settings['loyalty_conversion_rate']) }}"
                                    class="form-control" placeholder="e.g. 10">
                                <span class="coin-suffix">Coins = ₹1</span>
                            </div>
                            <p class="text-xs text-secondary-light mt-2">Example: If set to 10, then 100 coins = ₹10 discount.</p>
                            @error('loyalty_conversion_rate')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Max Redemption Limit (%)</label>
                            <div class="coin-input-group">
                                <span class="coin-icon warning">
                                    <iconify-icon icon="solar:bag-check-outline"></iconify-icon>
                                </span>
                                <input type="number" name="loyalty_max_redemption_percent"
                                    value="{{ old('loyalty_max_redemption_percent', $settings['loyalty_max_redemption_percent']) }}"
                                    class="form-control" placeholder="e.g. 30" min="0" max="100">
                                <span class="coin-suffix">% of Order Total</span>
                            </div>
                            <p class="text-xs text-secondary-light mt-2">Maximum percentage of order value that can be paid using coins.</p>
                            @error('loyalty_max_redemption_percent')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">System Status</label>
                            <div class="d-flex align-items-center gap-3 mt-1">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="loyalty_enabled" value="0">
                                    <input class="form-check-input" type="checkbox" name="loyalty_enabled" value="1" id="loyalty_enabled" {{ $settings['loyalty_enabled'] ? 'checked' : '' }}>
                                    <label class="form-check-label" for="loyalty_enabled">Enable NB Coins System</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-primary-600 px-32">
                                <iconify-icon icon="solar:check-read-outline" class="me-2"></iconify-icon> Update Loyalty Rules
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
