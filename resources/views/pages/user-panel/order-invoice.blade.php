@extends('layouts.user-panel')
@section('title', 'Invoice Details — NutriBuddy Kids')
@section('panel-page-class', 'panel-order')

@push('styles')
    <style>
        /* ═══════════════════════════════════════════════════════
           ADMIN-STYLE INVOICE FOR USER PANEL
           ═══════════════════════════════════════════════════════ */

        .inv-wrap * {
            box-sizing: border-box !important;
        }

        .inv-wrap {
            font-family: 'Inter', 'Segoe UI', sans-serif !important;
            max-width: 100% !important;
            margin: 0 auto !important;
            padding: 0 0 40px !important;
        }

        /* ── Card ── */
        .inv-wrap .inv-card {
            background: #fff !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 24px !important;
            overflow: hidden !important;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.04) !important;
            position: relative !important;
        }

        .inv-wrap .inv-card-topbar {
            height: 8px !important;
            background: linear-gradient(90deg, var(--pk), var(--pu)) !important;
            padding: 0 !important;
            border: none !important;
        }

        .inv-wrap .inv-body {
            padding: 40px !important;
        }

        /* ── Header ── */
        .inv-wrap .inv-header {
            width: 100% !important;
            margin-bottom: 30px !important;
            padding-bottom: 24px !important;
            border-bottom: 2px solid #f3f4f6 !important;
            display: table !important;
            table-layout: fixed !important;
        }

        .inv-wrap .inv-company-left {
            display: table-cell !important;
            vertical-align: top !important;
            width: 60% !important;
        }

        .inv-wrap .inv-title-block {
            display: table-cell !important;
            vertical-align: top !important;
            text-align: right !important;
            width: 40% !important;
        }

        .inv-wrap .inv-company-logo img {
            height: 50px !important;
            margin-bottom: 12px !important;
        }

        .inv-wrap .inv-company-name {
            font-family: 'Fredoka One', cursive !important;
            font-size: 1.2rem !important;
            color: var(--dk) !important;
            margin-bottom: 6px !important;
        }

        .inv-wrap .inv-company-meta {
            font-size: 0.85rem !important;
            color: var(--muted) !important;
            line-height: 1.6 !important;
        }

        .inv-wrap .inv-legal-tag {
            display: inline-block !important;
            background: var(--pul) !important;
            color: var(--pud) !important;
            padding: 3px 10px !important;
            border-radius: 6px !important;
            font-size: 0.72rem !important;
            font-weight: 700 !important;
            margin-right: 6px !important;
            margin-top: 10px !important;
        }

        .inv-wrap .inv-word {
            font-family: 'Fredoka One', cursive !important;
            font-size: 2.2rem !important;
            color: var(--dk) !important;
            line-height: 1 !important;
            margin-bottom: 8px !important;
        }

        .inv-wrap .inv-num {
            font-size: 0.95rem !important;
            color: var(--muted) !important;
            margin-bottom: 12px !important;
        }

        .inv-wrap .inv-status-pill {
            display: inline-block !important;
            padding: 5px 14px !important;
            border-radius: 8px !important;
            font-size: 0.8rem !important;
            font-weight: 700 !important;
        }

        /* ── Info Grid ── */
        .inv-wrap .inv-info-grid {
            width: 100% !important;
            background: #f9f5ff !important;
            border-radius: 20px !important;
            margin-bottom: 30px !important;
            display: table !important;
        }

        .inv-wrap .inv-info-grid td {
            width: 33.33% !important;
            padding: 24px !important;
            vertical-align: top !important;
            border-right: 1px solid rgba(0,0,0,0.05) !important;
        }

        .inv-wrap .inv-info-grid td:last-child {
            border-right: none !important;
        }

        .inv-wrap .col-label {
            font-family: 'Fredoka One', cursive !important;
            font-size: 0.8rem !important;
            color: var(--pk) !important;
            text-transform: uppercase !important;
            letter-spacing: 1.5px !important;
            display: block !important;
            margin-bottom: 12px !important;
        }

        .inv-wrap .inv-info-grid p {
            font-size: 0.95rem !important;
            line-height: 1.6 !important;
            color: var(--dk) !important;
            margin-bottom: 4px !important;
        }

        /* ── Table ── */
        .inv-wrap .inv-table-wrap {
            border: 1px solid #e5e7eb !important;
            border-radius: 16px !important;
            overflow: hidden !important;
            margin-bottom: 30px !important;
        }

        .inv-wrap .inv-table {
            width: 100% !important;
            border-collapse: collapse !important;
        }

        .inv-wrap .inv-table th {
            background: #f9fafb !important;
            padding: 14px !important;
            text-align: left !important;
            font-size: 0.75rem !important;
            font-weight: 700 !important;
            color: var(--muted) !important;
            text-transform: uppercase !important;
            border-bottom: 1px solid #e5e7eb !important;
        }

        .inv-wrap .inv-table td {
            padding: 16px 14px !important;
            border-bottom: 1px solid #f3f4f6 !important;
            vertical-align: top !important;
        }

        .inv-wrap .inv-prod-name {
            font-weight: 800 !important;
            font-size: 1.05rem !important;
            color: var(--dk) !important;
            margin-bottom: 4px !important;
        }

        .inv-wrap .inv-spec-pill {
            display: inline-block !important;
            background: var(--pul) !important;
            color: var(--pud) !important;
            padding: 2px 8px !important;
            border-radius: 6px !important;
            font-size: 0.7rem !important;
            font-weight: 700 !important;
            margin-right: 5px !important;
            margin-top: 5px !important;
        }

        .inv-wrap .inv-amount {
            font-weight: 800 !important;
        }

        /* ── Totals ── */
        .inv-wrap .inv-totals-box {
            display: inline-block !important;
            width: 320px !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 16px !important;
            overflow: hidden !important;
        }

        .inv-wrap .inv-total-line {
            display: flex !important;
            justify-content: space-between !important;
            padding: 12px 20px !important;
            font-size: 0.95rem !important;
            border-bottom: 1px solid #f3f4f6 !important;
        }

        .inv-wrap .inv-total-line:last-child {
            border-bottom: none !important;
        }

        .inv-wrap .inv-total-line.grand-tot {
            background: linear-gradient(135deg, #fdf2f8, #f5f3ff) !important;
            padding: 18px 20px !important;
            font-family: 'Fredoka One', cursive !important;
            font-size: 1.4rem !important;
            color: var(--pu) !important;
        }

        @media (max-width: 768px) {
            .inv-wrap .inv-header, .inv-wrap .inv-info-grid {
                display: block !important;
            }
            .inv-wrap .inv-company-left, .inv-wrap .inv-title-block, .inv-wrap .inv-info-grid td {
                display: block !important;
                width: 100% !important;
                text-align: left !important;
                padding: 20px !important;
            }
            .inv-wrap .inv-info-grid td {
                border-right: none !important;
                border-bottom: 1px solid rgba(0,0,0,0.05) !important;
            }
            .inv-wrap .inv-totals-box {
                width: 100% !important;
            }
        }
    </style>
@endpush

@section('panel-content')
    <div class="inner-topbar">
        <button class="sidebar-toggle" onclick="toggleSidebar()">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <line x1="3" y1="6" x2="21" y2="6" />
                <line x1="3" y1="12" x2="21" y2="12" />
                <line x1="3" y1="18" x2="21" y2="18" />
            </svg>
        </button>
        <span class="it-title">Invoice Details 🧾</span>
        <div style="width:36px"></div>
    </div>

    <div class="page">
        <div class="inv-wrap fade-in">
            <div class="inv-card">
                <div class="inv-card-topbar"></div>

                <div class="inv-body">
                    {{-- HEADER --}}
                    <div class="inv-header">
                        <div class="inv-company-left">
                            <div class="inv-company-logo">
                                <img src="{{ asset('assets/images/logo.png') }}" alt="NutriBuddy">
                            </div>
                            <div class="inv-company-name">{{ config('company.name') }}</div>
                            <div class="inv-company-meta">
                                <span>{{ config('company.address') }}, {{ config('company.city') }}</span><br>
                                <span>📞 {{ config('company.phone') }} &nbsp;·&nbsp; ✉️ {{ config('company.email') }}</span>
                            </div>
                            <div class="inv-legal-tags">
                                <span class="inv-legal-tag">GSTIN: {{ config('company.gst') }}</span>
                                <span class="inv-legal-tag">PAN: {{ config('company.pan') }}</span>
                                <span class="inv-legal-tag">CIN: {{ config('company.cin') }}</span>
                            </div>
                        </div>

                        <div class="inv-title-block">
                            <div class="inv-word">INVOICE</div>
                            <div class="inv-num">#INV-{{ $order->order_number }}</div>
                            <div class="inv-date">
                                Issued: {{ optional($order->placed_at)->format('d M Y') ?? $order->created_at->format('d M Y') }}
                            </div>
                            <span class="inv-status-pill" style="background: {{ $order->payment_status === 'paid' ? '#d1fae5' : '#fef3c7' }}; color: {{ $order->payment_status === 'paid' ? '#065f46' : '#92400e' }}">
                                {{ strtoupper($order->payment_status) }}
                            </span>
                        </div>
                    </div>

                    {{-- INFO GRID --}}
                    <table class="inv-info-grid">
                        <tr>
                            <td>
                                <span class="col-label">Billed To</span>
                                <p><strong>{{ $order->customer_name }}</strong></p>
                                <p style="color: var(--muted); font-size: 0.85rem;">{{ $order->customer_email }}</p>
                                <p style="color: var(--muted); font-size: 0.85rem;">{{ $order->customer_phone }}</p>
                            </td>
                            <td>
                                <span class="col-label">Shipped To</span>
                                <p><strong>{{ $order->shipping_name }}</strong></p>
                                <p style="color: var(--muted); font-size: 0.85rem;">{{ $order->shipping_address_line_1 }}</p>
                                @if($order->shipping_address_line_2)
                                    <p style="color: var(--muted); font-size: 0.85rem;">{{ $order->shipping_address_line_2 }}</p>
                                @endif
                                <p style="color: var(--muted); font-size: 0.85rem;">{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}</p>
                            </td>
                            <td>
                                <span class="col-label">Payment</span>
                                <p>Method: <strong>{{ strtoupper($order->payment_method) }}</strong></p>
                                <p>Order ID: <strong>#{{ $order->order_number }}</strong></p>
                                <p>Status: <strong style="color: {{ $order->payment_status === 'paid' ? '#10b981' : '#f59e0b' }}">{{ strtoupper($order->payment_status) }}</strong></p>
                            </td>
                        </tr>
                    </table>

                    {{-- ITEMS TABLE --}}
                    <div class="inv-table-wrap">
                        <table class="inv-table">
                            <thead>
                                <tr>
                                    <th style="width: 45%;">Product Description</th>
                                    <th style="text-align: center; width: 10%;">Qty</th>
                                    <th style="text-align: right; width: 15%;">Price</th>
                                    <th style="text-align: right; width: 15%;">GST</th>
                                    <th style="text-align: right; width: 15%;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                @php
                                    $vName = $item->item_snapshot['variant_name'] ?? ($item->productVariant?->name ?? null);
                                    $product = $item->product;
                                    
                                    // Rule: Always show GST on the original unit price
                                    $taxRate = $item->tax_percent ?? ($product?->taxRate?->rate ?? 0);
                                    $gstAmt = ($item->unit_price * $item->quantity * $taxRate) / 100;
                                    
                                    // Total for this line including full GST
                                    $lineTotalWithFullTax = ($item->unit_price * $item->quantity) + $gstAmt;

                                    $specs = [];
                                    if ($product) {
                                        if ($product->flavor || $product->flavour) $specs[] = 'Flavor: ' . ($product->flavor ?? $product->flavour);
                                        if ($product->pack_size) $specs[] = 'Pack: ' . $product->pack_size;
                                        if ($product->age_group) $specs[] = 'Age: ' . $product->age_group;
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <div class="inv-prod-name">{{ $item->product_name }}</div>
                                        @if($vName) <div style="font-size: 0.8rem; color: var(--muted);">{{ $vName }}</div> @endif
                                        @foreach($specs as $spec)
                                            <span class="inv-spec-pill">{{ $spec }}</span>
                                        @endforeach
                                    </td>
                                    <td style="text-align: center; font-weight: 700;">{{ $item->quantity }}</td>
                                    <td style="text-align: right;">₹{{ number_format($item->unit_price, 2) }}</td>
                                    <td style="text-align: right;">
                                        <div style="font-weight: 600;">{{ number_format($taxRate, 2) }}%</div>
                                        <div style="font-size: 0.75rem; color: var(--muted);">₹{{ number_format($gstAmt, 2) }}</div>
                                    </td>
                                    <td style="text-align: right;" class="inv-amount">₹{{ number_format($lineTotalWithFullTax, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- TOTALS --}}
                <div style="text-align: right;">
                    <div class="inv-totals-box">
                        @php
                            // 1. Calculate the REAL original GST for the whole order
                            $totalOriginalGst = $order->items->sum(function($i) {
                                $rate = $i->tax_percent ?? 0;
                                return ($i->unit_price * $i->quantity * $rate) / 100;
                            });

                            $displayCouponDiscount = (float) $order->discount_total;
                            $displayCoinDiscount = (float) $order->coin_discount;
                        @endphp
                        
                        <div class="inv-total-line">
                            <span>Subtotal</span>
                            <strong>₹{{ number_format((float) $order->subtotal, 2) }}</strong>
                        </div>

                        @if($displayCouponDiscount > 0.01)
                            <div class="inv-total-line" style="color: #ef4444; background: #fff5f5;">
                                <span>Coupon Discount</span>
                                <strong>- ₹{{ number_format($displayCouponDiscount, 2) }}</strong>
                            </div>
                        @endif

                        @if($displayCoinDiscount > 0.01)
                            <div class="inv-total-line" style="color: #f97316; background: #fffaf0;">
                                <span>NB Coins Discount{{ (int) $order->coins_redeemed > 0 ? ' (' . number_format((int) $order->coins_redeemed) . ' coins)' : '' }}</span>
                                <strong>- ₹{{ number_format($displayCoinDiscount, 2) }}</strong>
                            </div>
                        @endif

                        <div class="inv-total-line">
                            <span>GST / Tax</span>
                            <strong>₹{{ number_format($totalOriginalGst, 2) }}</strong>
                        </div>

                        <div class="inv-total-line">
                            <span>Shipping</span>
                            <strong>₹{{ number_format($order->shipping_total, 2) }}</strong>
                        </div>

                        <div class="inv-total-line grand-tot">
                            <span>Grand Total</span>
                            <strong>₹{{ number_format(round($order->grand_total)) }}</strong>
                        </div>
                    </div>
                </div>

                    {{-- FOOTER --}}
                    <div style="text-align: center; margin-top: 40px; padding-top: 30px; border-top: 1px solid #eee; font-size: 0.8rem; color: var(--muted); line-height: 1.8;">
                        <div style="font-weight: 800; color: var(--dk); font-size: 0.9rem; margin-bottom: 5px;">{{ config('company.name') }}</div>
                        <div>{{ config('company.email') }} &nbsp;·&nbsp; {{ config('company.phone') }} &nbsp;·&nbsp; {{ config('company.website') }}</div>
                        <div style="margin-top: 10px;">
                            GSTIN: {{ config('company.gst') }} &nbsp;·&nbsp; PAN: {{ config('company.pan') }} &nbsp;·&nbsp; CIN: {{ config('company.cin') }}
                        </div>
                        <div style="margin-top: 10px; font-size: 0.7rem;">This is a computer-generated invoice and does not require a physical signature.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <a href="{{ route('user.orders.invoice-download', $order) }}" 
       style="position: fixed; bottom: 30px; right: 30px; background: linear-gradient(135deg, var(--pk), var(--pu)); color: #fff; padding: 16px 32px; border-radius: 50px; font-family: 'Fredoka One', cursive; text-decoration: none; box-shadow: 0 10px 30px rgba(124, 58, 237, 0.3); display: flex; align-items: center; gap: 10px; z-index: 100;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
            <polyline points="7 10 12 15 17 10"></polyline>
            <line x1="12" y1="15" x2="12" y2="3"></line>
        </svg>
        Download PDF
    </a>

    @push('scripts')
        <script>
            function toggleSidebar() {
                document.getElementById('sidebar').classList.toggle('open');
                document.getElementById('overlay').classList.toggle('show');
            }
        </script>
    @endpush
@endsection
