@extends('layouts.user-panel')
@section('title', 'NB Coins Wallet — NutriBuddy Kids')
@section('panel-page-class', 'panel-userdashboard panel-wallet')

@push('styles')
<style>
    .panel-wallet .page {
        animation: fadeUp 0.5s ease-out forwards;
    }

    .panel-wallet .welcome-banner {
        border-radius: 24px;
        padding: 40px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(255, 107, 0, 0.2);
    }

    .panel-wallet .welcome-banner::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.2) 0%, transparent 70%);
        border-radius: 50%;
    }

    .panel-wallet .stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-top: -40px !important;
        position: relative;
        z-index: 10;
        padding: 0 20px;
    }

    .panel-wallet .stat-card {
        background: #fff;
        border: 2px solid var(--border);
        border-radius: 20px;
        padding: 25px;
        display: flex;
        align-items: center;
        gap: 18px;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .panel-wallet .stat-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
        border-color: var(--or);
    }

    .panel-wallet .sc-icon {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
    }

    .panel-wallet .sc-info .num {
        font-family: 'Fredoka One', cursive;
        font-size: 2.2rem;
        color: var(--dk);
        line-height: 1;
    }

    .panel-wallet .sc-info .lbl {
        font-size: 0.85rem;
        color: var(--muted);
        font-weight: 700;
        margin-top: 4px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .panel-wallet .box {
        background: #fff;
        border: 2px solid var(--border);
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
    }

    .panel-wallet .box-head {
        padding: 22px 28px;
        border-bottom: 2px solid var(--border);
        background: #fafbfc;
    }

    .panel-wallet .box-head h3 {
        font-family: 'Fredoka One', cursive;
        font-size: 1.15rem;
        color: var(--dk);
        margin: 0;
    }

    .panel-wallet .orders-table {
        width: 100%;
        border-collapse: collapse;
    }

    .panel-wallet .orders-table th {
        padding: 16px 28px;
        text-align: left;
        background: #f8f9fa;
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        color: var(--muted);
        font-weight: 800;
        border-bottom: 2px solid var(--border);
    }

    .panel-wallet .orders-table td {
        padding: 20px 28px;
        border-bottom: 1.5px solid var(--border);
        font-size: 0.88rem;
    }

    .panel-wallet .status-badge {
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 0.72rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .panel-wallet .s-delivered {
        background: #e6fcf5;
        color: #087f5b;
        border: 1px solid #c3fae8;
    }

    .panel-wallet .s-cancelled {
        background: #fff5f5;
        color: #c92a2a;
        border: 1px solid #ffe3e3;
    }

    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 850px) {
        .panel-wallet .stats-grid {
            grid-template-columns: 1fr;
            margin-top: -30px !important;
        }

        .panel-wallet .welcome-banner {
            padding: 30px 20px;
            text-align: center;
            flex-direction: column;
            gap: 20px;
        }
    }

    .panel-wallet .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
        padding: 35px;
    }

    .panel-wallet .info-item h4 {
        font-family: 'Fredoka One', cursive;
        font-size: 1.2rem;
        color: var(--dk);
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .panel-wallet .info-item p {
        font-size: 0.9rem;
        color: var(--muted);
        line-height: 1.7;
        margin: 0;
    }
</style>
@endpush

@section('panel-content')
<style>
    /* Immediate styles to ensure page looks correct even if stack fails */
    .panel-wallet .page {
        background: #fff;
        min-height: 100vh;
    }
</style>
    <div class="ud-main">

        <div class="page">
            <!-- WALLET HEADER -->
            <div class="welcome-banner" style="background: linear-gradient(135deg, #ff9100, #ff6b00); min-height: 180px;">
                <div class="welcome-text">
                    <h2>Total NB Coins</h2>
                    <div style="font-family: 'Fredoka One', cursive; font-size: 3.5rem; color: #fff; margin-top: 10px;">
                        {{ $user->coins_balance }}
                    </div>
                    <p style="color: rgba(255,255,255,0.9); font-weight: 600;">10 Coins = ₹1 Discount</p>
                </div>
                <div class="welcome-right">
                    <div class="banner-stat">
                        <div class="bs-num">₹{{ number_format($user->coins_balance / 10, 2) }}</div>
                        <div class="bs-lbl">Equivalent Value</div>
                    </div>
                </div>
            </div>

            <!-- QUICK INFO -->
            <div class="stats-grid">
                <div class="stat-card" style="background: #fff; border: 2px solid var(--border);">
                    <div class="sc-icon" style="background: var(--mnl)">🪙</div>
                    <div class="sc-info">
                        <div class="num">{{ $transactions->where('type', 'earned')->sum('amount') }}</div>
                        <div class="lbl">Total Earned</div>
                    </div>
                </div>
                <div class="stat-card" style="background: #fff; border: 2px solid var(--border);">
                    <div class="sc-icon" style="background: #ffe4e6">💸</div>
                    <div class="sc-info">
                        <div class="num">{{ $transactions->where('type', 'spent')->sum('amount') }}</div>
                        <div class="lbl">Total Spent</div>
                    </div>
                </div>
            </div>

            <!-- TRANSACTION HISTORY -->
            <div class="box" style="margin-top: 24px;">
                <div class="box-head">
                    <h3>📜 Transaction History</h3>
                </div>
                <div style="overflow-x:auto">
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Type</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $trx)
                                <tr>
                                    <td>{{ $trx->created_at->format('d M, Y') }}</td>
                                    <td style="font-size: 0.85rem; color: var(--text-light);">
                                        {{ $trx->description }}
                                        @if($trx->order)
                                            <br><small>Order: #{{ $trx->order->order_number }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="status-badge {{ $trx->type === 'earned' ? 's-delivered' : 's-cancelled' }}"
                                            style="font-size: 0.7rem;">
                                            {{ strtoupper($trx->type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong style="color: {{ $trx->type === 'earned' ? '#00a870' : '#e11d48' }};">
                                            {{ $trx->type === 'earned' ? '+' : '-' }} {{ $trx->amount }}
                                        </strong>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="text-align: center; padding: 40px; color: var(--text-light);">
                                        <div style="font-size: 2rem; margin-bottom: 10px;">🍃</div>
                                        No transactions yet. Start shopping to earn NB Coins!
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div style="padding: 20px;">
                    {{ $transactions->links() }}
                </div>
            </div>

            <!-- HOW IT WORKS -->
            <div class="box" style="margin-top: 30px; background: #fcfdfe;">
                <div class="box-head">
                    <h3>💡 How NB Coins Work?</h3>
                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <h4>🛍️ Earn</h4>
                        <p>Earn coins on every purchase! Look for the "NB Coins" badge on product pages to see how many
                            you'll get automatically after your order is confirmed.</p>
                    </div>
                    <div class="info-item">
                        <h4>✨ Redeem</h4>
                        <p>Use your coins at checkout to get instant discounts. <strong>10 coins = ₹1</strong> off your
                            total amount. Just move the slider at checkout!</p>
                    </div>
                    <div class="info-item">
                        <h4>🛡️ Limits</h4>
                        <p>You can redeem coins for up to <strong>30%</strong> of your total order value. The more you shop,
                            the more you save on NutriBuddy!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection