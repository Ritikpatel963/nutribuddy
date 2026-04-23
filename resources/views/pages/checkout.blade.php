@extends('layouts.main')
@section('title', 'Checkout — NutriBuddy Kids')

@push('styles')
    <style>
        /* ── TOPBAR ── */
        .checkout-topbar {
            /* background:var(--wh);border-bottom:2px solid var(--pkl); */
            padding: 0 5%;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: sticky;
            top: 0;
            z-index: 100;
            /* box-shadow:0 2px 16px rgba(0,0,0,.06); */
        }

        .topbar-secure {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: .82rem;
            color: var(--text-light);
            font-weight: 600
        }

        .topbar-secure .lock {
            font-size: 1rem
        }

        .topbar-steps {
            display: flex;
            align-items: center;
            gap: 0
        }

        .ts {
            display: flex;
            align-items: center;
            gap: 8px;
            font-family: 'Nunito', sans-serif;
            font-size: .78rem;
            font-weight: 800;
            color: #bbb;
            padding: 0 14px;
            position: relative
        }

        .ts.active {
            color: var(--pk)
        }

        .ts.done {
            color: var(--mn)
        }

        .ts-num {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            border: 2px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .7rem;
            transition: all .3s
        }

        .ts.active .ts-num {
            border-color: var(--pk);
            background: var(--pk);
            color: #fff
        }

        .ts.done .ts-num {
            border-color: var(--mn);
            background: var(--mn);
            color: #fff
        }

        .ts-arrow {
            color: #ddd;
            font-size: .9rem;
            padding: 0 4px
        }

        /* ── PROGRESS BAR ── */
        .progress-strip {
            height: 4px;
            position: relative
        }


        /* ── MAIN LAYOUT ── */
        .checkout-main {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 28px;
            padding: 32px 5% 80px;
            align-items: start;
        }

        /* ── LEFT PANEL ── */
        .left-panel {
            display: flex;
            flex-direction: column;
            gap: 22px
        }

        /* Cards */
        .co-card {
            background: var(--wh);
            border-radius: 20px;
            border: 2px solid var(--border);
            overflow: hidden;
            transition: border-color .3s;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .04);
        }

        .co-card.active-card {
            border-color: var(--pk);
            box-shadow: 0 4px 24px rgba(255, 77, 143, .12)
        }

        .card-head {
            padding: 18px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            border-bottom: 2px solid var(--border);
            background: linear-gradient(135deg, rgba(255, 77, 143, .03), rgba(124, 58, 237, .02));
        }

        .active-card .card-head {
            border-color: var(--pkl)
        }

        .card-head-left {
            display: flex;
            align-items: center;
            gap: 12px
        }

        .step-badge {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--pk), var(--pkd));
            color: #fff;
            font-family: 'Fredoka One', cursive;
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .step-badge.done-badge {
            background: linear-gradient(135deg, var(--mn), #00a870)
        }

        .card-head h3 {
            font-family: 'Nunito', sans-serif;
            font-size: 1.1rem;
            color: var(--dk)
        }

        .card-head .edit-link {
            font-size: .8rem;
            color: var(--pk);
            font-weight: 700;
            cursor: pointer;
            border: none;
            background: none;
            transition: color .2s
        }

        .card-head .edit-link:hover {
            color: var(--pkd);
            text-decoration: underline
        }

        .card-body {
            padding: 24px
        }

        /* ── SECTION: LOGIN ── */
        .login-done {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 24px
        }

        .login-done .user-ava {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: var(--pkl);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0
        }

        .login-done .user-name {
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: .95rem;
            color: var(--dk)
        }

        .login-done .user-phone {
            font-size: .8rem;
            color: var(--text-light)
        }

        /* ── SECTION: ADDRESS ── */
        .addr-tabs {
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
            flex-wrap: wrap
        }

        .addr-tab {
            border: 2px solid var(--border);
            border-radius: 50px;
            padding: 9px 18px;
            font-family: 'Nunito', sans-serif;
            font-weight: 800;
            font-size: .82rem;
            color: var(--text-light);
            cursor: pointer;
            transition: all .25s;
            background: white
        }

        .addr-tab.active {
            border-color: var(--pk);
            background: var(--pkl);
            color: var(--pkd)
        }

        .addr-tab:hover {
            border-color: var(--pkl)
        }

        .saved-addresses {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 20px
        }

        .addr-item {
            border: 2.5px solid var(--border);
            border-radius: 16px;
            padding: 16px 18px;
            display: flex;
            align-items: flex-start;
            gap: 14px;
            cursor: pointer;
            transition: all .3s;
            position: relative;
            background: white;
        }

        .addr-item:hover {
            border-color: var(--pkl);
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(255, 77, 143, .08)
        }

        .addr-item.selected {
            border-color: var(--pk);
            background: linear-gradient(135deg, rgba(255, 77, 143, .04), rgba(255, 77, 143, .01))
        }

        .addr-radio {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2.5px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 2px;
            transition: all .25s
        }

        .addr-item.selected .addr-radio {
            border-color: var(--pk);
            background: var(--pk)
        }

        .addr-item.selected .addr-radio::after {
            content: '';
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: white
        }

        .addr-info .addr-name {
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: .92rem;
            color: var(--dk);
            margin-bottom: 3px;
            display: flex;
            align-items: center;
            gap: 8px
        }

        .addr-type-tag {
            background: var(--pkl);
            color: var(--pk);
            font-size: .65rem;
            font-weight: 900;
            padding: 2px 9px;
            border-radius: 20px;
            font-family: 'Nunito', sans-serif
        }

        .addr-info .addr-line {
            font-size: .84rem;
            color: var(--text-light);
            line-height: 1.55
        }

        .addr-info .addr-phone {
            font-size: .8rem;
            color: var(--text-light);
            margin-top: 3px
        }

        .addr-del-btn {
            position: absolute;
            top: 12px;
            right: 14px;
            background: none;
            border: none;
            color: #ccc;
            cursor: pointer;
            font-size: .9rem;
            transition: color .2s
        }

        .addr-del-btn:hover {
            color: var(--or)
        }

        .add-addr-btn {
            border: 2.5px dashed var(--pkl);
            border-radius: 16px;
            padding: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
            color: var(--pk);
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: .9rem;
            background: none;
            width: 100%;
            transition: all .25s;
        }

        .add-addr-btn:hover {
            background: var(--pkl);
            border-style: solid
        }

        .new-addr-form {
            display: none;
            margin-top: 16px
        }

        .new-addr-form.show {
            display: block;
            animation: slideDown .3s ease
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px
        }

        .form-grid.single {
            grid-template-columns: 1fr
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px
        }

        .form-group label {
            font-family: 'Nunito', sans-serif;
            font-weight: 800;
            font-size: .78rem;
            color: var(--dk);
            letter-spacing: .3px
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            border: 2px solid var(--border);
            border-radius: 12px;
            padding: 12px 14px;
            font-family: 'DM Sans', sans-serif;
            font-size: .9rem;
            color: var(--dk);
            background: white;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: var(--pk);
            box-shadow: 0 0 0 3px rgba(255, 77, 143, .1)
        }

        .form-group input::placeholder {
            color: #bbb
        }

        .form-group select {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23999' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center
        }

        .addr-type-row {
            display: flex;
            gap: 10px;
            margin-top: 4px
        }

        .addr-type-btn {
            border: 2px solid var(--border);
            border-radius: 50px;
            padding: 8px 16px;
            font-family: 'Nunito', sans-serif;
            font-weight: 800;
            font-size: .78rem;
            cursor: pointer;
            background: white;
            color: var(--text-light);
            transition: all .25s;
            display: flex;
            align-items: center;
            gap: 6px
        }

        .addr-type-btn.active {
            border-color: var(--pk);
            background: var(--pkl);
            color: var(--pkd)
        }

        /* ── SECTION: PAYMENT ── */
        .payment-methods {
            display: flex;
            flex-direction: column;
            gap: 12px
        }

        .pay-method {
            border: 2.5px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            transition: all .3s;
            background: white;
        }

        .pay-method.selected {
            border-color: var(--pk);
            box-shadow: 0 4px 16px rgba(255, 77, 143, .1)
        }

        .pay-head {
            padding: 16px 18px;
            display: flex;
            align-items: center;
            gap: 14px;
            cursor: pointer;
            transition: background .2s;
        }

        .pay-head:hover {
            background: rgba(255, 77, 143, .02)
        }

        .pay-radio {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2.5px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all .25s
        }

        .pay-method.selected .pay-radio {
            border-color: var(--pk);
            background: var(--pk)
        }

        .pay-method.selected .pay-radio::after {
            content: '';
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: white
        }

        .pay-icon {
            font-size: 1.5rem;
            width: 38px;
            text-align: center
        }

        .pay-name {
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: .92rem;
            color: var(--dk)
        }

        .pay-sub {
            font-size: .76rem;
            color: var(--text-light)
        }

        .pay-tags {
            margin-left: auto;
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            justify-content: flex-end
        }

        .pay-tag {
            background: var(--mnl);
            color: #00a870;
            font-size: .65rem;
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            padding: 3px 9px;
            border-radius: 20px
        }

        .pay-body {
            display: none;
            padding: 0 18px 18px;
            border-top: 2px solid var(--border);
            padding-top: 16px
        }

        .pay-method.selected .pay-body {
            display: block
        }

        .upi-input-row {
            display: flex;
            gap: 10px
        }

        .upi-input-row input {
            flex: 1;
            border: 2px solid var(--border);
            border-radius: 12px;
            padding: 12px 14px;
            font-family: 'DM Sans', sans-serif;
            font-size: .9rem;
            outline: none;
            transition: border-color .2s
        }

        .upi-input-row input:focus {
            border-color: var(--pk)
        }

        .upi-verify-btn {
            background: var(--pk);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 12px 20px;
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: .85rem;
            cursor: pointer;
            white-space: nowrap;
            transition: all .2s
        }

        .upi-verify-btn:hover {
            background: var(--pkd)
        }

        .upi-apps {
            display: flex;
            gap: 12px;
            margin-top: 14px;
            flex-wrap: wrap
        }

        .upi-app {
            border: 2px solid var(--border);
            border-radius: 12px;
            padding: 10px 14px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
            cursor: pointer;
            transition: all .25s;
            background: white;
            font-size: .7rem;
            font-family: 'Nunito', sans-serif;
            font-weight: 800;
            color: var(--text-light);
            min-width: 64px
        }

        .upi-app:hover {
            border-color: var(--pk);
            transform: translateY(-2px)
        }

        .upi-app.active {
            border-color: var(--pk);
            background: var(--pkl);
            color: var(--pkd)
        }

        .upi-app img {
            width: 28px;
        }

        .upi-app span {
            font-size: 1.6rem
        }

        .card-row {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px
        }

        .emi-options {
            display: flex;
            flex-direction: column;
            gap: 8px
        }

        .emi-item {
            border: 2px solid var(--border);
            border-radius: 12px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            transition: all .25s
        }

        .emi-item:hover {
            border-color: var(--pkl)
        }

        .emi-item.active {
            border-color: var(--pk);
            background: var(--pkl)
        }

        .emi-item .emi-months {
            font-family: 'Fredoka One', cursive;
            font-size: 1rem;
            color: var(--dk)
        }

        .emi-item .emi-amount {
            font-size: .82rem;
            color: var(--text-light)
        }

        .emi-item .emi-interest {
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: .75rem;
            color: var(--mn)
        }

        .wallets {
            display: flex;
            gap: 10px;
            flex-wrap: wrap
        }

        .wallet-item {
            border: 2px solid var(--border);
            border-radius: 12px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: all .25s;
            font-family: 'Nunito', sans-serif;
            font-weight: 800;
            font-size: .82rem
        }

        .wallet-item:hover {
            border-color: var(--pkl)
        }

        .wallet-item.active {
            border-color: var(--pk);
            background: var(--pkl);
            color: var(--pkd)
        }

        .wallet-item span {
            font-size: 1.3rem
        }

        .netbank-select {
            width: 100%;
            margin-top: 4px
        }

        .cod-note {
            background: var(--yel);
            border: 2px solid var(--ye);
            border-radius: 12px;
            padding: 14px;
            font-size: .84rem;
            color: #907000;
            line-height: 1.6;
            display: flex;
            gap: 10px;
            align-items: flex-start
        }

        /* ── OFFER STRIP ── */
        .offer-strip {
            background: linear-gradient(135deg, rgba(255, 77, 143, .08), rgba(124, 58, 237, .06));
            border: 2px solid var(--pkl);
            border-radius: 14px;
            padding: 14px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-family: 'Nunito', sans-serif;
            font-weight: 800;
            font-size: .84rem;
            color: var(--pkd);
        }

        .offer-strip span {
            font-size: 1.1rem
        }

        /* ── RIGHT: ORDER SUMMARY ── */
        .order-summary {
            background: var(--wh);
            border-radius: 20px;
            border: 2px solid var(--border);
            box-shadow: 0 4px 20px rgba(0, 0, 0, .05);
            position: sticky;
            top: 84px;
            overflow: hidden;
        }

        .os-head {
            padding: 18px 22px;
            background: linear-gradient(135deg, var(--dk2), #2d0060);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .os-head h3 {
            font-family: 'Nunito', sans-serif;
            color: white;
            font-size: 1.1rem
        }

        .os-head .item-count {
            background: var(--pk);
            color: white;
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: .72rem;
            padding: 3px 10px;
            border-radius: 20px
        }

        .os-body {
            padding: 20px 22px
        }

        /* Cart Items */
        .cart-items {
            display: flex;
            flex-direction: column;
            gap: 14px;
            margin-bottom: 18px
        }

        .ci {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding-bottom: 14px;
            border-bottom: 1px solid var(--border)
        }

        .ci:last-child {
            border-bottom: none;
            padding-bottom: 0
        }

        .ci-img img {
            width: 30px !important;
        }

        .user-ava img {
            width: 25px;
        }

        .ci-img {
            width: 64px;
            height: 64px;
            border-radius: 14px;
            background: linear-gradient(135deg, #FFF0FA, #F0E8FF);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            flex-shrink: 0;
            border: 2px solid var(--pkl)
        }

        .ci-info {
            flex: 1
        }

        .ci-name {
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: .88rem;
            color: var(--dk);
            margin-bottom: 3px;
            line-height: 1.4
        }

        .ci-variant {
            font-size: .75rem;
            color: var(--text-light);
            margin-bottom: 6px
        }

        .ci-qty-row {
            display: flex;
            align-items: center;
            gap: 8px
        }

        .ci-qty-row.is-updating {
            opacity: .65;
            pointer-events: none
        }

        .qty-btn {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            border: 1.5px solid var(--border);
            background: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            font-weight: 900;
            transition: all .2s;
            color: var(--dk);
            touch-action: manipulation
        }

        .qty-btn:hover {
            border-color: var(--pk);
            background: var(--pkl);
            color: var(--pk)
        }

        .qty-btn:disabled {
            opacity: .55;
            cursor: wait
        }

        .qty-val {
            width: 58px;
            height: 34px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            background: white;
            text-align: center;
            font-size: .95rem;
            font-weight: 800;
            color: var(--dk);
            outline: none
        }

        .ci-price {
            font-family: 'Fredoka One', cursive;
            font-size: 1rem;
            color: var(--pk);
            text-align: right;
            flex-shrink: 0
        }

        .ci-old {
            font-size: .72rem;
            color: #bbb;
            text-decoration: line-through;
            font-family: 'DM Sans', sans-serif
        }

        /* Coupon */
        .coupon-row {
            border: 2px dashed var(--pkl);
            border-radius: 14px;
            padding: 14px 16px;
            margin-bottom: 16px;
        }

        .coupon-row.applied {
            border-style: solid;
            border-color: var(--mn);
            background: var(--mnl)
        }

        .coupon-label {
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: .8rem;
            color: var(--dk);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px
        }

        .coupon-input-row {
            display: flex;
            gap: 8px
        }

        .coupon-input {
            flex: 1;
            border: 2px solid var(--border);
            border-radius: 10px;
            padding: 10px 12px;
            font-family: 'DM Sans', sans-serif;
            font-size: .88rem;
            outline: none;
            transition: border-color .2s;
            background: white
        }

        .coupon-input:focus {
            border-color: var(--pk)
        }

        .coupon-apply-btn {
            background: linear-gradient(135deg, var(--pk), var(--pkd));
            color: white;
            border: none;
            border-radius: 10px;
            padding: 10px 16px;
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: .82rem;
            cursor: pointer;
            white-space: nowrap;
            transition: all .2s
        }

        .coupon-apply-btn:hover {
            transform: scale(1.04)
        }

        .coupon-applied-msg {
            font-size: .78rem;
            color: #00a870;
            font-weight: 700;
            margin-top: 6px;
            display: none
        }

        .coupon-applied-msg.show {
            display: flex;
            align-items: center;
            gap: 4px
        }

        /* Price Breakdown */
        .price-breakdown {
            display: flex;
            flex-direction: column;
            gap: 9px;
            margin-bottom: 16px
        }

        .pb-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: .86rem
        }

        .pb-label {
            color: var(--text-light)
        }

        .pb-val {
            font-weight: 700;
            color: var(--dk)
        }

        .pb-val.green {
            color: var(--mn)
        }

        .pb-val.red {
            color: var(--or)
        }

        .pb-divider {
            height: 1px;
            background: var(--border);
            margin: 4px 0
        }

        .pb-total-row {
            display: flex;
            align-items: center;
            justify-content: space-between
        }

        .pb-total-label {
            font-family: 'Fredoka One', cursive;
            font-size: 1.05rem;
            color: var(--dk)
        }

        .pb-total-val {
            font-family: 'Fredoka One', cursive;
            font-size: 1.4rem;
            color: var(--pk)
        }

        .tax-note {
            font-size: .72rem;
            color: var(--text-light);
            margin-top: 2px
        }

        /* Savings Banner */
        .savings-banner {
            background: linear-gradient(135deg, var(--mnl), rgba(0, 214, 143, .12));
            border: 2px solid var(--mn);
            border-radius: 12px;
            padding: 10px 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: .82rem;
            color: #00a870;
            margin-bottom: 16px;
        }

        /* Place Order */
        .place-order-btn {
            width: 100%;
            background: linear-gradient(135deg, var(--pk), var(--pkd));
            border: none;
            border-radius: 16px;
            padding: 18px;
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: 1.05rem;
            color: white;
            cursor: pointer;
            transition: all .3s;
            box-shadow: 0 8px 28px rgba(255, 77, 143, .4);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            position: relative;
            overflow: hidden;
        }

        .place-order-btn::before {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, .15);
            transform: translateX(-100%);
            transition: transform .3s
        }

        .place-order-btn:hover::before {
            transform: translateX(0)
        }

        .place-order-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 38px rgba(255, 77, 143, .55)
        }

        .place-order-btn:active {
            transform: translateY(0)
        }

        /* Trust Badges */
        .trust-row {
            display: flex;
            justify-content: center;
            gap: 16px;
            margin-top: 14px;
            flex-wrap: wrap
        }

        .tr-badge {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: .72rem;
            color: var(--text-light);
            font-family: 'Nunito', sans-serif;
            font-weight: 700
        }

        /* Loyalty Points */
        .loyalty-box {
            background: linear-gradient(135deg, var(--yel), rgba(255, 214, 0, .12));
            border: 2px solid var(--ye);
            border-radius: 14px;
            padding: 12px 16px;
            margin-top: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .loyalty-box .lb-icon {
            font-size: 1.4rem
        }

        .loyalty-box .lb-text {
            font-family: 'Nunito', sans-serif;
            font-weight: 800;
            font-size: .82rem;
            color: #907000
        }

        .loyalty-box .lb-pts {
            font-family: 'Fredoka One', cursive;
            font-size: 1rem;
            color: var(--or)
        }

        /* Payment Method Continue Button */
        .continue-btn {
            width: 100%;
            background: linear-gradient(135deg, var(--pk), var(--pkd));
            border: none;
            border-radius: 14px;
            padding: 16px;
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: .95rem;
            color: white;
            cursor: pointer;
            margin-top: 18px;
            transition: all .3s;
            box-shadow: 0 6px 22px rgba(255, 77, 143, .35);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .continue-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(255, 77, 143, .5)
        }

        /* ══════════════════════════════════════════
                       PHONE / OTP MODAL
                    ══════════════════════════════════════════ */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 9000;
            background: rgba(13, 0, 32, .7);
            backdrop-filter: blur(10px);
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.show {
            display: flex;
            animation: fadeIn .25s ease
        }

        .otp-modal {
            background: white;
            border-radius: 28px;
            width: 90%;
            max-width: 420px;
            overflow: hidden;
            box-shadow: 0 32px 80px rgba(0, 0, 0, .35);
            animation: popUp .4s cubic-bezier(.34, 1.56, .64, 1);
            border: 2.5px solid var(--pkl);
        }

        /* Modal Header */
        .om-header {
            background: linear-gradient(135deg, var(--pk), var(--pkd));
            padding: 28px 32px 24px;
            text-align: center;
            position: relative;
        }

        .om-header .om-close {
            position: absolute;
            top: 14px;
            right: 16px;
            background: rgba(255, 255, 255, .2);
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            color: white;
            font-size: 1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background .2s;
        }

        .om-header .om-close:hover {
            background: rgba(255, 255, 255, .35)
        }

        .om-lock-icon {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin: 0 auto 12px;
            border: 2.5px solid rgba(255, 255, 255, .4);
        }

        .om-header h2 {
            font-family: 'Fredoka One', cursive;
            font-size: 1.5rem;
            color: white;
            margin-bottom: 4px;
        }

        .om-header p {
            font-size: .82rem;
            color: rgba(255, 255, 255, .85);
            line-height: 1.5
        }

        /* Modal Body */
        .om-body {
            padding: 28px 32px 32px
        }

        /* Steps inside modal */
        .om-step {
            display: none
        }

        .om-step.active {
            display: block;
            animation: slideDown .3s ease
        }

        /* Phone step */
        .om-label {
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: .82rem;
            color: var(--dk);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .phone-input-wrap {
            display: flex;
            border: 2.5px solid var(--border);
            border-radius: 14px;
            overflow: hidden;
            transition: border-color .2s;
        }

        .phone-input-wrap:focus-within {
            border-color: var(--pk);
            box-shadow: 0 0 0 3px rgba(255, 77, 143, .1)
        }

        .phone-prefix {
            background: var(--grey);
            padding: 0 14px;
            display: flex;
            align-items: center;
            font-family: 'Nunito', sans-serif;
            font-weight: 800;
            font-size: .9rem;
            color: var(--dk);
            border-right: 2px solid var(--border);
            white-space: nowrap;
        }

        .phone-input-wrap input {
            flex: 1;
            border: none;
            padding: 14px 14px;
            font-family: 'DM Sans', sans-serif;
            font-size: 1rem;
            color: var(--dk);
            outline: none;
            background: white;
            letter-spacing: 1px;
        }

        .phone-input-wrap input::placeholder {
            color: #bbb;
            letter-spacing: 0
        }

        /* Send OTP btn */
        .send-otp-btn {
            width: 100%;
            margin-top: 16px;
            background: linear-gradient(135deg, var(--pk), var(--pkd));
            border: none;
            border-radius: 14px;
            padding: 16px;
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: 1rem;
            color: white;
            cursor: pointer;
            transition: all .3s;
            box-shadow: 0 6px 20px rgba(255, 77, 143, .35);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .send-otp-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 28px rgba(255, 77, 143, .5)
        }

        .send-otp-btn:disabled {
            opacity: .6;
            transform: none;
            cursor: not-allowed
        }

        /* OTP step */
        .otp-sent-info {
            background: var(--mnl);
            border: 2px solid var(--mn);
            border-radius: 12px;
            padding: 12px 16px;
            font-size: .82rem;
            color: #007a55;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .otp-boxes {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin: 20px 0 8px
        }

        .otp-box {
            width: 46px;
            height: 60px;
            border: 2.5px solid var(--border);
            border-radius: 14px;
            text-align: center;
            font-family: 'Fredoka One', cursive;
            font-size: 1.6rem;
            color: var(--dk);
            outline: none;
            background: white;
            transition: all .25s;
            caret-color: var(--pk);
        }

        .otp-box:focus {
            border-color: var(--pk);
            box-shadow: 0 0 0 3px rgba(255, 77, 143, .12);
            transform: scale(1.05)
        }

        .otp-box.filled {
            border-color: var(--pk);
            background: var(--pkl)
        }

        .otp-timer {
            text-align: center;
            font-size: .8rem;
            color: var(--text-light);
            margin-bottom: 16px
        }

        .otp-timer strong {
            color: var(--pk);
            font-family: 'Nunito', sans-serif;
            font-weight: 900
        }

        .resend-link {
            color: var(--pk);
            cursor: pointer;
            font-weight: 700;
            text-decoration: underline
        }

        .resend-link:hover {
            color: var(--pkd)
        }

        .resend-link.disabled {
            color: #bbb;
            cursor: not-allowed;
            text-decoration: none;
            pointer-events: none
        }

        .verify-otp-btn {
            width: 100%;
            background: linear-gradient(135deg, var(--pk), var(--pkd));
            border: none;
            border-radius: 14px;
            padding: 16px;
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: 1rem;
            color: white;
            cursor: pointer;
            transition: all .3s;
            box-shadow: 0 6px 20px rgba(255, 77, 143, .35);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .verify-otp-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 28px rgba(255, 77, 143, .5)
        }

        .verify-otp-btn:disabled {
            opacity: .6;
            transform: none;
            cursor: not-allowed
        }

        .otp-error {
            text-align: center;
            font-size: .82rem;
            color: var(--or);
            font-family: 'Nunito', sans-serif;
            font-weight: 700;
            margin-top: -4px;
            margin-bottom: 8px;
            display: none;
        }

        .otp-error.show {
            display: block;
            animation: slideDown .2s ease
        }

        .change-phone-link {
            text-align: center;
            margin-top: 12px;
            font-size: .8rem;
            color: var(--text-light);
        }

        .change-phone-link span {
            color: var(--pk);
            cursor: pointer;
            font-weight: 700;
            text-decoration: underline
        }

        .change-phone-link span:hover {
            color: var(--pkd)
        }

        /* ── SUCCESS OVERLAY ── */
        .success-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: rgba(13, 0, 32, .85);
            backdrop-filter: blur(8px);
            align-items: center;
            justify-content: center;
        }

        .success-overlay.show {
            display: flex;
            animation: fadeIn .3s ease
        }

        .success-box {
            background: white;
            border-radius: 32px;
            padding: 48px 40px;
            text-align: center;
            max-width: 440px;
            width: 90%;
            animation: popUp .5s cubic-bezier(.34, 1.56, .64, 1);
            border: 3px solid var(--pkl);
        }

        .success-icon {
            font-size: 4.5rem;
            margin-bottom: 16px;
            display: block;
            animation: bounce 1s ease infinite alternate
        }

        .success-box h2 {
            font-family: 'Fredoka One', cursive;
            font-size: 2rem;
            color: var(--dk);
            margin-bottom: 8px
        }

        .success-box p {
            color: var(--text-light);
            font-size: .92rem;
            line-height: 1.7;
            margin-bottom: 24px
        }

        .order-id-box {
            background: var(--pkl);
            border-radius: 12px;
            padding: 12px 20px;
            font-family: 'Fredoka One', cursive;
            font-size: 1.1rem;
            color: var(--pkd);
            margin-bottom: 24px
        }

        .success-btns {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap
        }

        .success-btns a {
            padding: 13px 24px;
            border-radius: 50px;
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: .9rem;
            text-decoration: none;
            transition: all .25s;
            cursor: pointer;
            border: none
        }

        .btn-track {
            background: linear-gradient(135deg, var(--pk), var(--pkd));
            color: white;
            box-shadow: 0 6px 18px rgba(255, 77, 143, .35)
        }

        .btn-home {
            background: var(--pkl);
            color: var(--pkd)
        }

        .btn-track:hover,
        .btn-home:hover {
            transform: translateY(-2px)
        }

        /* ── RESPONSIVE ── */
        @media(max-width:1024px) {
            .checkout-main {
                grid-template-columns: 1fr;
                max-width: 680px
            }

            .order-summary {
                position: static
            }

            .topbar-steps {
                display: none
            }
        }

        @media(max-width:640px) {
            .checkout-main {
                padding: 16px 4% 60px;
                gap: 18px
            }

            .checkout-topbar {
                padding: 0 4%;
                height: 58px
            }

            .card-body {
                padding: 16px
            }

            .os-body {
                padding: 16px 16px
            }

            .form-grid {
                grid-template-columns: 1fr
            }

            .upi-apps {
                gap: 8px
            }

            .upi-app {
                min-width: 56px;
                padding: 8px 10px
            }

            .ci-img {
                width: 52px;
                height: 52px;
                font-size: 1.6rem
            }

            .qty-btn {
                width: 38px;
                height: 38px;
                font-size: 1.05rem
            }

            .qty-val {
                flex: 1;
                min-width: 0;
                width: auto;
                height: 38px
            }

            .wallets {
                gap: 8px
            }

            .pay-tags {
                display: none
            }

            .success-box {
                padding: 32px 24px
            }

            .om-body {
                padding: 20px 20px 24px
            }

            .om-header {
                padding: 22px 24px 20px
            }

            .otp-boxes {
                gap: 8px
            }

            .otp-box {
                width: 40px;
                height: 54px;
                font-size: 1.4rem
            }
        }

        /* ── KEYFRAMES ── */
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0
            }

            to {
                opacity: 1
            }
        }

        @keyframes popUp {
            from {
                opacity: 0;
                transform: scale(.7)
            }

            to {
                opacity: 1;
                transform: scale(1)
            }
        }

        @keyframes bounce {
            from {
                transform: scale(1)
            }

            to {
                transform: scale(1.15)
            }
        }
    </style>
@endpush

@section('content')
    <!-- HERO -->
    <div class="page-hero">
        <span class="hero-eyebrow">Legal · NutriBuddy Kids</span>
        <h1 class="hero-title">Check<span>Out</span></h1>
        <p class="hero-subtitle">Please read these terms carefully before using our website or purchasing our products. They
            govern your relationship with NutriBuddy.</p>
        <div class="hero-meta">
            <div class="meta-pill">📅 Last Updated: June 1, 2025</div>
            <div class="meta-pill">🏢 NutriBuddy Kids Pvt. Ltd.</div>
            <div class="meta-pill">🇮🇳 Governed by Indian Law</div>
        </div>
    </div>
    <!-- Menu Overlay -->
    <div class="menu-overlay" id="menuOverlay"></div>

    <!-- Mobile Dropdown Menu -->
    <div class="mobile-menu" id="mobileMenu" aria-hidden="true">
        <ul>
            <li><a href="#"><span class="link-emoji"></span> Home</a></li>
            <li><a href="#"><span class="link-emoji"></span> About Us</a></li>
            <li><a href="#"><span class="link-emoji"></span> Products</a></li>
            <li><a href="#"><span class="link-emoji"></span> Personalized Diet Chart</a></li>
            <li><a href="#"><span class="link-emoji"></span> Testimonials</a></li>
        </ul>
        <div class="mobile-cta-wrap">
            <a href="#">Contact Us</a>
        </div>
    </div>

    <!-- ══ TOPBAR ══ -->
    <header class="checkout-topbar">

        <div class="topbar-steps">
            <div class="ts done" id="step-login">
                <div class="ts-num">✓</div> Login
            </div>
            <div class="ts-arrow">›</div>
            <div class="ts active" id="step-addr">
                <div class="ts-num">2</div> Address
            </div>
            <div class="ts-arrow">›</div>
            <div class="ts" id="step-pay">
                <div class="ts-num">3</div> Payment
            </div>
        </div>
    </header>
    <div class="progress-strip">
        <div class="progress-fill" id="progressFill"></div>
    </div>

    <!-- ══ MAIN ══ -->
    <main class="checkout-main">

        <!-- ══ LEFT ══ -->
        <div class="left-panel">

            <!-- STEP 1: LOGIN — DONE -->
            <div class="co-card" id="loginCard">
                <div class="card-head">
                    <div class="card-head-left">
                        <div class="step-badge done-badge">✓</div>
                        <h3>Login / Account</h3>
                    </div>
                    <button class="edit-link" onclick="editSection('login')">Change</button>
                </div>
                <div class="login-done">
                    <div class="user-ava"><img src="img/people.png" alt=""></div>
                    <div>
                        <div class="user-name">{{ auth()->user()->name ?? 'User' }}</div>
                        <div class="user-phone">+91 {{ auth()->user()->phone ?? '—' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- STEP 2: ADDRESS -->
            <div class="co-card active-card" id="addressCard">
                <div class="card-head">
                    <div class="card-head-left">
                        <div class="step-badge" id="addrBadge">2</div>
                        <h3>Delivery Address</h3>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px">
                        <span style="font-size:.78rem;color:var(--text-light)">2 saved addresses</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="addr-tabs">
                        <div class="addr-tab active" onclick="setAddrTab(this)">🏠 Saved Addresses</div>
                        <div class="addr-tab" onclick="setAddrTab(this)">➕ Add New</div>
                    </div>
                    <div class="saved-addresses" id="savedAddrPanel"></div>
                    <div id="newAddrPanel" style="display:none">
                        <div class="new-addr-form show">
                            <div class="form-grid">
                                <div class="form-group"><label>First Name *</label><input type="text"
                                        placeholder="e.g. Priya"></div>
                                <div class="form-group"><label>Last Name *</label><input type="text"
                                        placeholder="e.g. Sharma"></div>
                            </div>
                            <div class="form-grid single" style="margin-top:14px">
                                <div class="form-group"><label>Mobile Number *</label><input type="tel"
                                        placeholder="+91 XXXXX XXXXX"></div>
                            </div>
                            <div class="form-grid single" style="margin-top:14px">
                                <div class="form-group"><label>Flat / House / Apartment *</label><input type="text"
                                        placeholder="e.g. 42, Sunshine Residency"></div>
                            </div>
                            <div class="form-grid single" style="margin-top:14px">
                                <div class="form-group"><label>Street / Area / Colony *</label><input type="text"
                                        placeholder="e.g. HSR Layout, Sector 3"></div>
                            </div>
                            <div class="form-grid" style="margin-top:14px">
                                <div class="form-group"><label>Pincode *</label><input type="text" maxlength="6"
                                        placeholder="6-digit pincode" id="newPincode" oninput="autoFillCity()"></div>
                                <div class="form-group"><label>City *</label><input type="text" placeholder="City"
                                        id="cityField"></div>
                            </div>
                            <div class="form-grid" style="margin-top:14px">
                                <div class="form-group">
                                    <label>State *</label>
                                    <select id="stateField">
                                        <option value="">Select State</option>
                                        <option>Andhra Pradesh</option>
                                        <option>Assam</option>
                                        <option>Bihar</option>
                                        <option>Delhi</option>
                                        <option>Goa</option>
                                        <option>Gujarat</option>
                                        <option>Haryana</option>
                                        <option>Himachal Pradesh</option>
                                        <option>Jharkhand</option>
                                        <option selected>Karnataka</option>
                                        <option>Kerala</option>
                                        <option>Madhya Pradesh</option>
                                        <option>Maharashtra</option>
                                        <option>Odisha</option>
                                        <option>Punjab</option>
                                        <option>Rajasthan</option>
                                        <option>Tamil Nadu</option>
                                        <option>Telangana</option>
                                        <option>Uttar Pradesh</option>
                                        <option>Uttarakhand</option>
                                        <option>West Bengal</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Address Type</label>
                                    <div class="addr-type-row">
                                        <button class="addr-type-btn active" onclick="toggleAddrType(this)">
                                            Home</button>
                                        <button class="addr-type-btn" onclick="toggleAddrType(this)"> Work</button>
                                        <button class="addr-type-btn" onclick="toggleAddrType(this)"> Other</button>
                                    </div>
                                </div>
                            </div>
                            <div style="margin-top:14px;">
                                <button type="button" class="continue-btn" onclick="saveNewAddress()"
                                    style="width:auto;padding:10px 18px;">
                                    Save Address
                                </button>
                            </div>
                        </div>
                    </div>
                    <button class="continue-btn" id="addressContinueBtn" onclick="goToPayment()">
                        Continue to Payment 
                    </button>
                </div>
            </div>

            <!-- STEP 3: PAYMENT -->
            <div class="co-card" id="paymentCard" style="opacity:.5;pointer-events:none">
                <div class="card-head">
                    <div class="card-head-left">
                        <div class="step-badge" id="payBadge">3</div>
                        <h3>Payment Method</h3>
                    </div>
                    <span style="font-size:.78rem;color:var(--text-light)">Choose how to pay</span>
                </div>
                <div class="card-body">
                    <div class="payment-methods">
                        <!-- UPI -->
                        <!-- COD -->
                        <div class="pay-method selected" id="payMethodCod" data-method="cod"
                            onclick="selectPayMethod(this,'cod')">
                            <div class="pay-head">
                                <div class="pay-radio"></div>
                                <div class="pay-icon">💵</div>
                                <div>
                                    <div class="pay-name">Cash on Delivery</div>
                                    <div class="pay-sub">Pay when your order arrives</div>
                                </div>
                                <div class="pay-tags"></div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div><!-- /left-panel -->

        <!-- ══ RIGHT: ORDER SUMMARY ══ -->
        <div class="order-summary">
            <div class="os-head">
                <h3>Order Summary</h3>
                <div class="item-count">3 Items</div>
            </div>
            <div class="os-body">
                <div class="cart-items" id="checkoutCartItems"></div>

                <!-- Coupon -->
                <div class="coupon-row" id="couponRow">
                    <div class="coupon-label">Coupon / Promo Code</div>
                    <div class="coupon-input-row">
                        <input class="coupon-input" type="text" placeholder="Enter coupon code" id="couponInput">
                        <button class="coupon-apply-btn" onclick="applyCoupon()">Apply</button>
                    </div>
                    <div class="coupon-applied-msg" id="couponMsg"></div>
                </div>

                <!-- Price Breakdown -->
                <div class="price-breakdown" id="priceBreakdown">
                    <div class="pb-row">
                        <span class="pb-label" id="pbMrpLabel">MRP (0 items)</span>
                        <span class="pb-val" id="pbMrpValue" style="text-decoration:line-through;color:#bbb">₹0</span>
                    </div>
                    <div class="pb-row">
                        <span class="pb-label">Product Discount</span>
                        <span class="pb-val green" id="pbProductDiscount">− ₹0</span>
                    </div>
                    <div class="pb-row">
                        <span class="pb-label">Delivery Charges</span>
                        <span class="pb-val green" id="pbDelivery">FREE 🎉</span>
                    </div>
                    <div class="pb-row">
                        <span class="pb-label">Included GST</span>
                        <span class="pb-val" id="pbGst">Included in price</span>
                    </div>
                    <div class="pb-row" id="couponRow2" style="display:none">
                        <span class="pb-label">Coupon</span>
                        <span class="pb-val green" id="couponDiscount">− ₹180</span>
                    </div>
                    <div class="pb-divider"></div>
                    <div class="pb-total-row">
                        <div>
                            <div class="pb-total-label">Total Amount</div>
                            <div class="tax-note">Incl. all taxes</div>
                        </div>
                        <div>
                            <div class="pb-total-val" id="totalDisplay">₹1,800</div>
                        </div>
                    </div>
                </div>
                <!-- Place Order (shown after payment section is active) -->
                <div id="placeOrderWrap" style="display:none;margin-top:16px">
                    <button class="place-order-btn" onclick="openOtpModal()">
                        🔒 Place Order Securely — ₹1,800
                    </button>
                    <div class="trust-row">
                        <div class="tr-badge">🔒 SSL Secure</div>
                        <div class="tr-badge">✅ FSSAI Certified</div>
                        <div class="tr-badge">🔄 Easy Returns</div>
                    </div>
                </div>

            </div>
        </div>

    </main>

    <!-- ══════════════════════════════
                 PHONE / OTP MODAL
            ══════════════════════════════ -->
    <div class="modal-overlay" id="otpModal">
        <div class="otp-modal">

            <!-- Header -->
            <div class="om-header">
                <button class="om-close" onclick="closeOtpModal()">✕</button>
                <div class="om-lock-icon">🔒</div>
                <h2>Verify & Pay</h2>
                <p>Confirm your mobile number to securely place your order</p>
            </div>

            <!-- Body -->
            <div class="om-body">

                <!-- STEP A: Phone Number -->
                <div class="om-step active" id="stepPhone">
                    <div class="om-label">📱 Mobile Number</div>
                    <div class="phone-input-wrap">
                        <div class="phone-prefix">🇮🇳 +91</div>
                        <input type="tel" id="phoneInput" maxlength="10" placeholder="Enter 10-digit number"
                            oninput="this.value=this.value.replace(/\D/g,'')">
                    </div>
                    <div id="phoneError"
                        style="font-size:.78rem;color:var(--or);margin-top:8px;font-family:'Nunito',sans-serif;font-weight:700;display:none">
                    </div>

                    <!-- Pre-filled hint -->
                    <div style="display:flex;align-items:center;gap:6px;margin-top:12px;padding:10px 14px;background:var(--pkl);border-radius:10px;cursor:pointer"
                        onclick="useSavedPhone()">
                        <span style="font-size:1rem">👩</span>
                        <div style="flex:1">
                            <div style="font-family:'Nunito',sans-serif;font-weight:800;font-size:.78rem;color:var(--pkd)">
                                Use saved number</div>
                            <div style="font-size:.74rem;color:var(--pkd);opacity:.8">
                                {{ auth()->user()?->phone ? '+91 ' . auth()->user()->phone : 'Use your login mobile number' }}
                            </div>
                        </div>
                        <span style="font-size:.75rem;color:var(--pkd);font-weight:700">Tap →</span>
                    </div>

                    <button class="send-otp-btn" id="sendOtpBtn" onclick="sendOtp()">
                        Send OTP →
                    </button>

                    <div style="text-align:center;margin-top:12px;font-size:.76rem;color:var(--text-light)">
                        OTP will be sent via SMS to your mobile number
                    </div>
                </div>

                <!-- STEP B: OTP Verification -->
                <div class="om-step" id="stepOtp">
                    <div class="otp-sent-info">
                        <span style="font-size:1.1rem">✅</span>
                        <div>OTP sent to <strong id="sentToNum">+91 98765 43210</strong>. Valid for <strong>10
                                minutes</strong>.</div>
                    </div>

                    <div class="om-label" style="justify-content:center">Enter 6-digit OTP</div>

                    <div class="otp-boxes">
                        <input class="otp-box" type="tel" maxlength="1" id="otp1"
                            oninput="otpInput(this,null,'otp2')" onkeydown="otpKeydown(event,this,null,'otp2')">
                        <input class="otp-box" type="tel" maxlength="1" id="otp2"
                            oninput="otpInput(this,'otp1','otp3')" onkeydown="otpKeydown(event,this,'otp1','otp3')">
                        <input class="otp-box" type="tel" maxlength="1" id="otp3"
                            oninput="otpInput(this,'otp2','otp4')" onkeydown="otpKeydown(event,this,'otp2','otp4')">
                        <input class="otp-box" type="tel" maxlength="1" id="otp4"
                            oninput="otpInput(this,'otp3','otp5')" onkeydown="otpKeydown(event,this,'otp3','otp5')">
                        <input class="otp-box" type="tel" maxlength="1" id="otp5"
                            oninput="otpInput(this,'otp4','otp6')" onkeydown="otpKeydown(event,this,'otp4','otp6')">
                        <input class="otp-box" type="tel" maxlength="1" id="otp6"
                            oninput="otpInput(this,'otp5',null)" onkeydown="otpKeydown(event,this,'otp5',null)">
                    </div>

                    <div class="otp-error" id="otpError">❌ Incorrect OTP. Please try again.</div>

                    <div class="otp-timer" id="otpTimerText">
                        Resend OTP in <strong id="timerCount">30s</strong>
                    </div>

                    <button class="verify-otp-btn" id="verifyOtpBtn" onclick="verifyOtp()">
                        ✅ Verify & Place Order
                    </button>

                    <div class="change-phone-link">
                        Wrong number? <span onclick="backToPhone()">Change mobile number</span>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- ══ SUCCESS OVERLAY ══ -->
    <div class="success-overlay" id="successOverlay">
        <div class="success-box">
            <span class="success-icon">🎉</span>
            <h2>Order Placed!</h2>
            <p>Yay! Your NutriBuddy order has been placed successfully. Your little superheroes will receive their gummies
                soon! </p>
            <div class="order-id-box">Order ID: NB-2025-48291</div>
            <p style="font-size:.82rem;color:var(--text-light);margin-bottom:20px">Estimated delivery: <strong>Tomorrow, by
                    5 PM</strong><br>Confirmation SMS sent to +91 98765 43210</p>
            <div class="success-btns">
                <a class="btn-track" href="#">Track Order</a>
                <a class="btn-home" href="{{ route('home') }}">Back to Home</a>
            </div>
        </div>
    </div>

    <!-- footer -->
    <!-- ══════════════════════════════════════════
                   NEWSLETTER
              ══════════════════════════════════════════ -->
    <div class="newsletter reveal">
        <span class="sec-eye">Stay in the Loop</span>
        <h2 class="sec-title">Wellness Tips for Your Little Ones</h2>
        <p class="nl-sub">Join 25,000+ parents getting Ayurvedic parenting tips, exclusive discounts & early product access
            every week.</p>
        <div class="nl-form">
            <input class="nl-input" type="email" placeholder="Enter your email address">
            <button class="hbtn hbtn-main" style="padding:13px 28px;font-size:.9rem">Subscribe</button>
        </div>
    </div>

    <!-- ══════════════════════════════════════════
                   FOOTER
              ══════════════════════════════════════════ -->
    <footer class="kiddex-footer">

        <!-- Animated background blobs -->
        <div class="footer-anim">
            <div class="fa-dot"
                style="width:300px;height:300px;background:var(--pk);top:-80px;left:-80px;--dur:8s;--del:0s"></div>
            <div class="fa-dot"
                style="width:200px;height:200px;background:var(--pu);bottom:-50px;right:10%;--dur:6s;--del:2s"></div>
            <div class="fa-dot" style="width:150px;height:150px;background:var(--ye);top:40%;left:40%;--dur:10s;--del:1s">
            </div>
        </div>

        <!-- Widget Grid -->
        <div class="footer-widget-area">

            <!-- Brand Column -->
            <div class="fw-brand">
                <a href="#" class="footer-logo-text">
                    <img src="img/logo.png" alt="NutriBuddy"
                        onerror="this.style.display='none';this.nextElementSibling.style.display='inline'">
                    <span
                        style="display:none;font-family:'Fredoka One',cursive;font-size:1.6rem;color:var(--pk)">NutriBuddy</span>
                </a>
                <ul class="footer-contact-list">
                    <li>
                        <span class="fci"><img src="img/location.png" alt=""
                                onerror="this.outerHTML='📍'"></span>
                        42, Wellness Tower, Bengaluru – 560001, Karnataka, India
                    </li>
                    <li>
                        <span class="fci"><img src="img/phone.png" alt=""
                                onerror="this.outerHTML='📞'"></span>
                        <a href="tel:18001234567">1800-123-4567</a> (Toll-Free)
                    </li>
                    <li>
                        <span class="fci"><img src="img/email.png" alt=""
                                onerror="this.outerHTML='✉️'"></span>
                        <a href="mailto:hello@nutribuddy.in">hello@nutribuddy.in</a>
                    </li>
                </ul>
                <div class="footer-socials">
                    <a href="#" title="Instagram"><img src="img/instagram.png" alt="Instagram"
                            onerror="this.outerHTML='<span style=\'font-size:1.5rem\'>📷</span>'"></a>
                    <a href="#" title="Facebook"><img src="img/facebook.png" alt="Facebook"
                            onerror="this.outerHTML='<span style=\'font-size:1.5rem\'>📘</span>'"></a>
                    <a href="#" title="WhatsApp"><img src="img/whatsapp.png" alt="WhatsApp"
                            onerror="this.outerHTML='<span style=\'font-size:1.5rem\'>💬</span>'"></a>
                    <a href="#" title="Twitter"><img src="img/twitter.png" alt="Twitter"
                            onerror="this.outerHTML='<span style=\'font-size:1.5rem\'>🐦</span>'"></a>
                </div>
            </div>

            <!-- Products Column -->
            <div class="fw-links">
                <h4>Products</h4>
                <ul>
                    <li><a href="#">GrowStrong Gummies</a></li>
                    <li><a href="#">BrainBoost Chews</a></li>
                    <li><a href="#">DreamCalm Drops</a></li>
                    <li><a href="#">Subscription Packs</a></li>
                    <li><a href="#">Shop All</a></li>
                </ul>
            </div>

            <!-- Company Column -->
            <div class="fw-links">
                <h4>Company</h4>
                <ul>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Our Ingredients</a></li>
                    <li><a href="#">Blog &amp; Tips</a></li>
                    <li><a href="#">Pediatrician Network</a></li>
                    <li><a href="#">Contact Us</a></li>
                </ul>
            </div>

            <!-- Support Column -->
            <div class="fw-links">
                <h4>Support</h4>
                <ul>
                    <li><a href="#">FAQs</a></li>
                    <li><a href="#">Track My Order</a></li>
                    <li><a href="#">Returns &amp; Refunds</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms of Service</a></li>
                </ul>
            </div>

            @push('scripts')
                <script>
                    /* ══ STEP NAVIGATION ══ */
                    function goToPayment() {
                        const addrCard = document.getElementById('addressCard');
                        const payCard = document.getElementById('paymentCard');
                        const addrBadge = document.getElementById('addrBadge');
                        const step2 = document.getElementById('step-addr');
                        const step3 = document.getElementById('step-pay');

                        addrCard.classList.remove('active-card');
                        addrBadge.textContent = '✓';
                        addrBadge.classList.add('done-badge');
                        step2.classList.remove('active');
                        step2.classList.add('done');
                        step2.querySelector('.ts-num').textContent = '✓';

                        payCard.style.opacity = '1';
                        payCard.style.pointerEvents = 'all';
                        payCard.classList.add('active-card');
                        step3.classList.add('active');

                        document.getElementById('progressFill').style.width = '66%';
                        document.getElementById('placeOrderWrap').style.display = 'block';

                        payCard.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }

                    function editSection(sec) {
                        if (sec === 'login') alert('Redirecting to login page…');
                    }

                    /* ══ ADDRESS ══ */
                    function setAddrTab(el) {
                        document.querySelectorAll('.addr-tab').forEach(t => t.classList.remove('active'));
                        el.classList.add('active');
                        const isSaved = el.textContent.includes('Saved');
                        document.getElementById('savedAddrPanel').style.display = isSaved ? 'flex' : 'none';
                        document.getElementById('newAddrPanel').style.display = isSaved ? 'none' : 'block';
                    }

                    function selectAddr(el) {
                        document.querySelectorAll('.addr-item').forEach(a => a.classList.remove('selected'));
                        el.classList.add('selected');
                        window.__selectedAddressId = el.getAttribute('data-address-id') || '';
                    }

                    function getNewAddressPayload() {
                        const firstName = document.querySelector('#newAddrPanel input[placeholder="e.g. Priya"]')?.value?.trim() ||
                            '';
                        const lastName = document.querySelector('#newAddrPanel input[placeholder="e.g. Sharma"]')?.value?.trim() ||
                            '';
                        const phoneRaw = document.querySelector('#newAddrPanel input[placeholder="+91 XXXXX XXXXX"]')?.value
                            ?.trim() || '';
                        const line1 = document.querySelector('#newAddrPanel input[placeholder="e.g. 42, Sunshine Residency"]')
                            ?.value?.trim() || '';
                        const line2 = document.querySelector('#newAddrPanel input[placeholder="e.g. HSR Layout, Sector 3"]')?.value
                            ?.trim() || '';
                        const postalCode = document.getElementById('newPincode')?.value?.trim() || '';
                        const city = document.getElementById('cityField')?.value?.trim() || '';
                        const state = document.getElementById('stateField')?.value?.trim() || '';
                        const activeTypeBtn = document.querySelector('.addr-type-btn.active');
                        const label = activeTypeBtn ? activeTypeBtn.textContent.replace(/[^\w\s]/g, '').trim() : 'Home';
                        const phone = phoneRaw.replace(/\D/g, '').slice(-10);

                        return {
                            label: label || 'Home',
                            full_name: `${firstName} ${lastName}`.trim(),
                            phone,
                            email: '{{ auth()->user()->email ?? '' }}',
                            address_line_1: line1,
                            address_line_2: line2,
                            city,
                            state,
                            postal_code: postalCode,
                            country: 'India'
                        };
                    }

                    function hasRequiredNewAddressFields(payload = {}) {
                        return !!(payload.full_name && payload.phone && payload.address_line_1 && payload.postal_code && payload.city &&
                            payload.state);
                    }

                    async function persistNewAddress(payload, options = {}) {
                        const res = await fetch(api.addressesUrl, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                ...(api.csrf ? {
                                    'X-CSRF-TOKEN': api.csrf
                                } : {})
                            },
                            body: JSON.stringify(payload)
                        });

                        const responsePayload = await res.json().catch(() => ({}));
                        if (!res.ok) {
                            if (!options.silent) {
                                alert(responsePayload.message || 'Unable to save address.');
                            }
                            return null;
                        }

                        return responsePayload.data || null;
                    }

                    function getFirstSavedAddressId() {
                        return document.querySelector('#savedAddrPanel .addr-item')?.getAttribute('data-address-id') || '';
                    }

                    async function ensureCheckoutAddressReady() {
                        if (window.__selectedAddressId) {
                            return true;
                        }

                        const pendingAddress = getNewAddressPayload();
                        if (hasRequiredNewAddressFields(pendingAddress)) {
                            const createdAddress = await persistNewAddress(pendingAddress, {
                                silent: true
                            });
                            if (createdAddress?.id) {
                                window.__selectedAddressId = String(createdAddress.id);
                            }
                        }

                        await loadAddresses();

                        if (!window.__selectedAddressId) {
                            const firstAddressId = getFirstSavedAddressId();
                            if (firstAddressId) {
                                window.__selectedAddressId = String(firstAddressId);
                                const firstAddressEl = document.querySelector(
                                    `#savedAddrPanel .addr-item[data-address-id="${firstAddressId}"]`
                                );
                                if (firstAddressEl) {
                                    selectAddr(firstAddressEl);
                                }
                            }
                        }

                        return !!window.__selectedAddressId;
                    }

                    function toggleAddrType(el) {
                        document.querySelectorAll('.addr-type-btn').forEach(b => b.classList.remove('active'));
                        el.classList.add('active');
                    }

                    function autoFillCity() {
                        const pin = document.getElementById('newPincode').value;
                        if (pin.length === 6) {
                            const cities = {
                                '560102': 'Bengaluru',
                                '400001': 'Mumbai',
                                '110001': 'Delhi',
                                '600001': 'Chennai',
                                '500001': 'Hyderabad'
                            };
                            document.getElementById('cityField').value = cities[pin] || 'Auto-detected';
                        }
                    }

                    /* ══ PAYMENT ══ */
                    const api = {
                        cartUrl: '/user/cart',
                        cartUpdateTemplate: @json(route('user.cart.items.update', ['itemId' => '__ITEM__'])),
                        addressesUrl: '/user/addresses',
                        checkoutSummaryUrl: '/user/checkout/summary',
                        placeOrderUrl: '/user/checkout/place-order',
                        sendOtpUrl: @json(route('frontend.sendOtp')),
                        verifyOtpUrl: @json(route('frontend.verifyOtp')),
                        csrf: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    };

                    window.__selectedAddressId = '';
                    window.__checkoutToken = '';
                    window.__couponCode = '';
                    let isLoggedIn = @json((bool) auth()->check());
                    let isApplyingCoupon = false;
                    let isVerifyingOtp = false;
                    let isPlacingOrder = false;
                    const pendingCartKey = 'nb_pending_cart';
                    let currentTotal = 0;

                    function updateCsrfToken(token) {
                        if (!token) {
                            return;
                        }

                        api.csrf = token;
                        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                        if (csrfMeta) {
                            csrfMeta.setAttribute('content', token);
                        }
                    }

                    function getPendingCartItems() {
                        try {
                            const raw = localStorage.getItem(pendingCartKey);
                            const parsed = raw ? JSON.parse(raw) : [];
                            return Array.isArray(parsed) ? parsed : [];
                        } catch (_) {
                            return [];
                        }
                    }

                    function savePendingCartItems(items) {
                        localStorage.setItem(pendingCartKey, JSON.stringify(items || []));
                    }

                    function normalizeCheckoutQuantity(quantity) {
                        return Math.max(1, Math.min(10, Number(quantity || 1)));
                    }

                    function updatePendingCartItemQuantity(productId, productVariantId = null, quantity = 1) {
                        const targetKey = `${Number(productId || 0)}::${Number(productVariantId || 0)}`;
                        const nextItems = getPendingCartItems().map(it => {
                            const itemKey = `${Number(it.product_id || 0)}::${Number(it.product_variant_id || 0)}`;
                            if (itemKey !== targetKey) return it;

                            return {
                                ...it,
                                quantity: normalizeCheckoutQuantity(quantity)
                            };
                        });

                        savePendingCartItems(nextItems);
                    }

                    function createCheckoutQtyControls(quantity) {
                        const qty = normalizeCheckoutQuantity(quantity);
                        return `
                            <button type="button" class="qty-btn" data-qty-delta="-1" aria-label="Decrease quantity">−</button>
                            <input type="number" min="1" max="10" class="qty-val" value="${qty}" aria-label="Quantity">
                            <button type="button" class="qty-btn" data-qty-delta="1" aria-label="Increase quantity">+</button>
                        `;
                    }

                    function bindCheckoutQtyControls(row, quantity, onChange) {
                        const qtyRow = row.querySelector('.ci-qty-row');
                        if (!qtyRow) return;
                        const input = qtyRow.querySelector('.qty-val');

                        async function submitQuantity(nextQty) {
                            const normalizedQty = normalizeCheckoutQuantity(nextQty);
                            if (normalizedQty === normalizeCheckoutQuantity(quantity)) {
                                if (input) input.value = String(normalizedQty);
                                return;
                            }

                            qtyRow.classList.add('is-updating');
                            qtyRow.querySelectorAll('.qty-btn, .qty-val').forEach(control => {
                                control.disabled = true;
                            });

                            try {
                                await onChange(normalizedQty);
                            } catch (error) {
                                if (input) input.value = String(normalizeCheckoutQuantity(quantity));
                                alert(error.message || 'Unable to update cart quantity.');
                            } finally {
                                qtyRow.classList.remove('is-updating');
                                qtyRow.querySelectorAll('.qty-btn, .qty-val').forEach(control => {
                                    control.disabled = false;
                                });
                            }
                        }

                        qtyRow.querySelectorAll('.qty-btn').forEach(btn => {
                            btn.addEventListener('click', event => {
                                event.preventDefault();
                                const delta = Number(btn.dataset.qtyDelta || 0);
                                const baseQty = input ? normalizeCheckoutQuantity(input.value) :
                                    normalizeCheckoutQuantity(quantity);
                                submitQuantity(baseQty + delta);
                            });
                        });

                        if (input) {
                            input.addEventListener('change', () => {
                                submitQuantity(input.value);
                            });
                            input.addEventListener('blur', () => {
                                input.value = String(normalizeCheckoutQuantity(input.value));
                            });
                            input.addEventListener('keydown', event => {
                                if (event.key === 'Enter') {
                                    event.preventDefault();
                                    submitQuantity(input.value);
                                }
                            });
                        }
                    }

                    async function updateServerCartQuantity(itemId, quantity) {
                        const res = await fetch(api.cartUpdateTemplate.replace('__ITEM__', itemId), {
                            method: 'PATCH',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                ...(api.csrf ? {
                                    'X-CSRF-TOKEN': api.csrf
                                } : {})
                            },
                            body: JSON.stringify({
                                quantity: normalizeCheckoutQuantity(quantity)
                            })
                        });

                        if (!res.ok) {
                            const errorPayload = await res.json().catch(() => ({}));
                            throw new Error(errorPayload.message || 'Unable to update cart quantity.');
                        }

                        return res.json().catch(() => ({}));
                    }

                    async function syncPendingCartToServer() {
                        const items = getPendingCartItems();
                        if (!items.length) return;
                        for (const item of items) {
                            await fetch(api.cartUrl, {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json',
                                    ...(api.csrf ? {
                                        'X-CSRF-TOKEN': api.csrf
                                    } : {})
                                },
                                body: JSON.stringify({
                                    product_id: item.product_id,
                                    product_variant_id: item.product_variant_id,
                                    quantity: item.quantity || 1
                                })
                            }).catch(() => null);
                        }
                        localStorage.removeItem(pendingCartKey);
                    }

                    function updatePriceUI(pricing, itemsCount) {
                        const mrp = Number(pricing.subtotal || 0);
                        const discount = Number(pricing.discount_total || 0);
                        const shipping = Number(pricing.shipping_total || 0);
                        const gst = Number(pricing.tax_total || 0);
                        const total = Number(pricing.grand_total || 0);

                        const mrpLabel = document.getElementById('pbMrpLabel');
                        const mrpValue = document.getElementById('pbMrpValue');
                        const productDiscount = document.getElementById('pbProductDiscount');
                        const delivery = document.getElementById('pbDelivery');
                        const gstEl = document.getElementById('pbGst');
                        const totalEl = document.getElementById('totalDisplay');
                        const savingsEl = document.getElementById('savingsAmt');
                        const loyaltyEl = document.getElementById('loyaltyPoints');

                        if (mrpLabel) mrpLabel.textContent = `MRP (${itemsCount} items)`;
                        if (mrpValue) mrpValue.textContent = `₹${mrp.toLocaleString('en-IN', { maximumFractionDigits: 2 })}`;
                        if (productDiscount) productDiscount.textContent =
                            `− ₹${discount.toLocaleString('en-IN', { maximumFractionDigits: 2 })}`;
                        if (delivery) delivery.textContent = shipping > 0 ?
                            `₹${shipping.toLocaleString('en-IN', { maximumFractionDigits: 2 })}` : 'FREE 🎉';
                        if (gstEl) {
                            gstEl.textContent = gst > 0 ?
                                `Incl. ₹${gst.toLocaleString('en-IN', { maximumFractionDigits: 2 })}` :
                                '₹0';
                        }
                        if (totalEl) totalEl.textContent = `₹${total.toLocaleString('en-IN', { maximumFractionDigits: 2 })}`;
                        if (savingsEl) savingsEl.textContent = `₹${discount.toLocaleString('en-IN', { maximumFractionDigits: 2 })}`;
                        if (loyaltyEl) loyaltyEl.textContent = `${Math.round(total / 20)} NutriBuddy Coins`;
                    }

                    function renderPendingCheckoutCart() {
                        const pending = getPendingCartItems();
                        const totalQuantity = pending.reduce((sum, it) => sum + Number(it.quantity || 0), 0);
                        const subtotal = pending.reduce((sum, it) => sum + (Number(it.unit_price || 0) * Number(it.quantity || 0)), 0);
                        currentTotal = subtotal;

                        updatePriceUI({
                            subtotal: subtotal,
                            discount_total: 0,
                            shipping_total: 0,
                            tax_total: 0,
                            grand_total: subtotal
                        }, totalQuantity);

                        const itemsCount = document.querySelector('.item-count');
                        if (itemsCount) itemsCount.textContent = `${totalQuantity} Items`;

                        const cartWrap = document.getElementById('checkoutCartItems');
                        if (!cartWrap) return;

                        cartWrap.innerHTML = '';
                        if (!pending.length) {
                            cartWrap.innerHTML = `<div style="padding:10px;color:var(--text-light)">Your cart is empty.</div>`;
                            return;
                        }

                        pending.forEach(it => {
                            const qty = Number(it.quantity || 0);
                            const linePrice = Number(it.unit_price || 0) * qty;
                            const row = document.createElement('div');
                            row.className = 'ci';
                            row.innerHTML = `
                              <div class="ci-img"><img src="${it.image || 'img/product2.png'}" alt=""></div>
                              <div class="ci-info">
                                <div class="ci-name">${it.product_name || 'Product'}</div>
                                <div class="ci-variant">${it.variant_name || 'Guest cart item'}</div>
                                <div class="ci-qty-row">
                                  ${createCheckoutQtyControls(qty)}
                                </div>
                              </div>
                              <div>
                                <div class="ci-price">₹${linePrice.toLocaleString('en-IN', { maximumFractionDigits: 2 })}</div>
                              </div>
                            `;
                            cartWrap.appendChild(row);
                            bindCheckoutQtyControls(row, qty, async nextQty => {
                                updatePendingCartItemQuantity(it.product_id, it.product_variant_id, nextQty);
                                renderPendingCheckoutCart();
                            });
                        });
                    }

                    function selectPayMethod(el, type) {
                        if (type !== 'cod') {
                            alert('Only Cash on Delivery is available right now.');
                            const cod = document.getElementById('payMethodCod');
                            if (cod) {
                                document.querySelectorAll('.pay-method').forEach(m => m.classList.remove('selected'));
                                cod.classList.add('selected');
                            }
                            type = 'cod';
                        }

                        document.querySelectorAll('.pay-method').forEach(m => m.classList.remove('selected'));
                        el.classList.add('selected');
                        const total = currentTotal;
                        const payBtn = document.getElementById('paymentPlaceBtn');
                        if (payBtn) payBtn.innerHTML = `🔒 Place Order — ₹${total.toLocaleString('en-IN')}`;
                        document.querySelector('.place-order-btn').innerHTML =
                            `🔒 Place Order Securely — ₹${total.toLocaleString('en-IN')}`;
                    }

                    function selectUpiApp(e, el) {
                        e.stopPropagation();
                        document.querySelectorAll('.upi-app').forEach(a => a.classList.remove('active'));
                        el.classList.add('active');
                    }

                    function verifyUPI() {
                        const id = document.getElementById('upiId').value.trim();
                        const msg = document.getElementById('upiMsg');
                        msg.style.display = 'block';
                        if (id.includes('@')) {
                            msg.textContent = '✅ UPI ID verified successfully!';
                            msg.style.color = 'var(--mn)';
                        } else {
                            msg.textContent = '❌ Invalid UPI ID. Format: name@bank';
                            msg.style.color = 'var(--or)';
                        }
                    }

                    function selectEmi(e, el) {
                        e.stopPropagation();
                        document.querySelectorAll('.emi-item').forEach(i => i.classList.remove('active'));
                        el.classList.add('active');
                    }

                    function selectWallet(e, el) {
                        e.stopPropagation();
                        document.querySelectorAll('.wallet-item').forEach(w => w.classList.remove('active'));
                        el.classList.add('active');
                    }

                    async function ensureCheckoutToken(couponCode = null, options = {}) {
                        const normalizedCode = typeof couponCode === 'string' ? couponCode.trim().toUpperCase() : '';
                        const forceRefresh = !!options.forceRefresh;

                        if (!forceRefresh && window.__checkoutToken) {
                            return {
                                success: true,
                                token: window.__checkoutToken,
                                payload: null
                            };
                        }

                        const res = await fetch(api.checkoutSummaryUrl, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                ...(api.csrf ? {
                                    'X-CSRF-TOKEN': api.csrf
                                } : {})
                            },
                            body: JSON.stringify({
                                coupon_code: normalizedCode || null
                            })
                        });

                        if (res.status === 401 || res.status === 419) {
                            return {
                                success: false,
                                requiresLogin: true,
                                message: 'Login at checkout to continue.'
                            };
                        }

                        const payload = await res.json().catch(() => ({}));
                        if (!res.ok || !payload.checkout_token) {
                            return {
                                success: false,
                                payload,
                                message: payload.message || 'Unable to prepare checkout. Please try again.'
                            };
                        }

                        window.__checkoutToken = payload.checkout_token;

                        return {
                            success: true,
                            token: window.__checkoutToken,
                            payload
                        };
                    }

                    /* ══ COUPON ══ */
                    async function applyCoupon() {
                        const code = document.getElementById('couponInput').value.trim().toUpperCase();
                        const msg = document.getElementById('couponMsg');
                        const row2 = document.getElementById('couponRow2');
                        const totalEl = document.getElementById('totalDisplay');
                        const savingsEl = document.getElementById('savingsAmt');
                        const loyaltyEl = document.getElementById('loyaltyPoints');
                        const couponRowEl = document.getElementById('couponRow');

                        msg.classList.add('show');
                        msg.style.color = 'var(--text-light)';
                        msg.textContent = 'Applying coupon...';

                        const tokenState = await ensureCheckoutToken(code, {
                            forceRefresh: true
                        });
                        if (tokenState.requiresLogin) {
                            msg.style.color = 'var(--or)';
                            msg.textContent = 'Login at checkout to apply coupon.';
                            openOtpModal();
                            return;
                        }

                        if (!tokenState.success) {
                            msg.style.color = 'var(--or)';
                            msg.textContent = tokenState.message || 'Invalid coupon.';
                            return;
                        }

                        const payload = tokenState.payload || {};
                        window.__couponCode = code;

                        const pricing = payload.pricing || {};
                        currentTotal = Number(pricing.grand_total || 0);
                        const totalQuantity = (payload.cart?.items || []).reduce((sum, it) => sum + Number(it.quantity || 0), 0);
                        updatePriceUI(pricing, totalQuantity);

                        msg.style.color = '#00a870';
                        msg.textContent = code ? `✅ Coupon "${code}" applied!` : '✅ Totals updated.';
                        row2.style.display = code ? 'flex' : 'none';
                        document.getElementById('couponDiscount').textContent = code ?
                            `− ₹${Number(pricing.discount_total || 0).toLocaleString('en-IN')}` : '− ₹0';
                        totalEl.textContent = `₹${Number(currentTotal).toLocaleString('en-IN', { maximumFractionDigits: 2 })}`;
                        savingsEl.textContent =
                            `₹${Number(pricing.discount_total || 0).toLocaleString('en-IN', { maximumFractionDigits: 2 })}`;
                        loyaltyEl.textContent = `${Math.round(currentTotal / 20)} NutriBuddy Coins`;
                        couponRowEl.classList.add('applied');

                        const totalWithCod = currentTotal;
                        const payBtn = document.getElementById('paymentPlaceBtn');
                        if (payBtn) payBtn.innerHTML = `🔒 Place Order — ₹${totalWithCod.toLocaleString('en-IN')}`;
                        const pob = document.querySelector('.place-order-btn');
                        if (pob) pob.innerHTML = `🔒 Place Order Securely — ₹${totalWithCod.toLocaleString('en-IN')}`;
                    }

                    /* ══ QTY ══ */
                    function updateQty(btn, delta) {
                        const valEl = btn.parentElement.querySelector('.qty-val');
                        let v = parseInt(valEl.textContent) + delta;
                        if (v < 1) v = 1;
                        if (v > 10) v = 10;
                        valEl.textContent = v;
                    }

                    /* ══ FORMAT CARD ══ */
                    function formatCard(input) {
                        let v = input.value.replace(/\D/g, '').substring(0, 16);
                        input.value = v.replace(/(.{4})/g, '$1 ').trim();
                    }

                    document.getElementById('couponInput').addEventListener('keydown', e => {
                        if (e.key === 'Enter') applyCoupon();
                    });

                    /* ════════════════════════════════════════
                       OTP MODAL LOGIC
                    ════════════════════════════════════════ */
                    let otpTimer = null;
                    const otpFieldIds = ['otp1', 'otp2', 'otp3', 'otp4', 'otp5', 'otp6'];

                    function openOtpModal() {
                        if (isLoggedIn) {
                            placeOrder();
                            return;
                        }
                        // Reset to phone step
                        showStep('stepPhone');
                        document.getElementById('phoneInput').value = '';
                        document.getElementById('phoneError').style.display = 'none';
                        clearOtpBoxes();
                        document.getElementById('otpError').classList.remove('show');
                        document.getElementById('otpModal').classList.add('show');
                        setTimeout(() => document.getElementById('phoneInput').focus(), 300);
                    }

                    function closeOtpModal() {
                        document.getElementById('otpModal').classList.remove('show');
                        clearInterval(otpTimer);
                    }

                    function showStep(id) {
                        document.querySelectorAll('.om-step').forEach(s => s.classList.remove('active'));
                        document.getElementById(id).classList.add('active');
                    }

                    function useSavedPhone() {
                        document.getElementById('phoneInput').value = '{{ auth()->user()->phone ?? '' }}';
                        document.getElementById('phoneError').style.display = 'none';
                    }

                    async function sendOtp() {
                        const phone = document.getElementById('phoneInput').value.trim();
                        const errEl = document.getElementById('phoneError');

                        if (phone.length !== 10) {
                            errEl.textContent = '⚠️ Please enter a valid 10-digit mobile number.';
                            errEl.style.display = 'block';
                            document.getElementById('phoneInput').focus();
                            return;
                        }

                        errEl.style.display = 'none';

                        const btn = document.getElementById('sendOtpBtn');
                        btn.disabled = true;
                        btn.textContent = 'Sending...';

                        const res = await fetch(api.sendOtpUrl, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                ...(api.csrf ? {
                                    'X-CSRF-TOKEN': api.csrf
                                } : {})
                            },
                            body: JSON.stringify({
                                phone
                            })
                        });

                        const payload = await res.json().catch(() => ({}));
                        btn.disabled = false;
                        btn.textContent = 'Send OTP →';

                        if (!res.ok) {
                            errEl.textContent = payload.message || 'Unable to send OTP.';
                            errEl.style.display = 'block';
                            return;
                        }

                        document.getElementById('sentToNum').textContent = '+91 ' + phone;
                        showStep('stepOtp');
                        clearOtpBoxes();
                        document.getElementById('otpError').classList.remove('show');
                        startResendTimer();
                        setTimeout(() => document.getElementById('otp1').focus(), 200);
                    }

                    function backToPhone() {
                        clearInterval(otpTimer);
                        showStep('stepPhone');
                        setTimeout(() => document.getElementById('phoneInput').focus(), 200);
                    }

                    /* OTP box helpers */
                    function clearOtpBoxes() {
                        otpFieldIds.forEach(id => {
                            const el = document.getElementById(id);
                            el.value = '';
                            el.classList.remove('filled');
                        });
                    }

                    function otpInput(el, prevId, nextId) {
                        el.value = el.value.replace(/\D/g, '');
                        if (el.value) {
                            el.classList.add('filled');
                            if (nextId) document.getElementById(nextId).focus();
                        } else {
                            el.classList.remove('filled');
                        }
                        const all = otpFieldIds.map(id => document.getElementById(id).value);
                        if (all.every(v => v !== '')) {
                            document.getElementById('otpError').classList.remove('show');
                        }
                    }

                    function otpKeydown(e, el, prevId, nextId) {
                        if (e.key === 'Backspace' && !el.value && prevId) {
                            document.getElementById(prevId).focus();
                        }
                        if (e.key === 'Enter') verifyOtp();
                    }

                    async function verifyOtp() {
                        const entered = otpFieldIds
                            .map(id => document.getElementById(id).value)
                            .join('');

                        if (entered.length < otpFieldIds.length) {
                            document.getElementById('otpError').textContent = '⚠️ Please enter all 6 digits.';
                            document.getElementById('otpError').classList.add('show');
                            return;
                        }

                        const btn = document.getElementById('verifyOtpBtn');

                        document.getElementById('otpError').classList.remove('show');
                        btn.textContent = '⏳ Verifying…';
                        btn.disabled = true;
                        btn.style.background = 'linear-gradient(135deg,var(--mn),#00a870)';

                        const phone = document.getElementById('phoneInput').value.trim();
                        const otp = entered;

                        const res = await fetch(api.verifyOtpUrl, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                ...(api.csrf ? {
                                    'X-CSRF-TOKEN': api.csrf
                                } : {})
                            },
                            body: JSON.stringify({
                                phone,
                                otp
                            })
                        });

                        const payload = await res.json().catch(() => ({}));
                        if (!res.ok || !payload.success) {
                            btn.textContent = '✅ Verify & Place Order';
                            btn.disabled = false;
                            btn.style.background = '';
                            document.getElementById('otpError').textContent = payload.message || '❌ Invalid OTP.';
                            document.getElementById('otpError').classList.add('show');
                            // Shake the boxes
                            otpFieldIds.forEach(id => {
                                const el = document.getElementById(id);
                                el.style.borderColor = 'var(--or)';
                                el.style.background = 'var(--orl)';
                                setTimeout(() => {
                                    el.style.borderColor = '';
                                    el.style.background = '';
                                    el.classList.remove('filled');
                                    el.value = '';
                                }, 600);
                            });
                            setTimeout(() => document.getElementById('otp1').focus(), 650);
                            return;
                        }

                        clearInterval(otpTimer);
                        isLoggedIn = true;
                        updateCsrfToken(payload.csrf_token || '');
                        await syncPendingCartToServer();

                        const hasAddressReady = await ensureCheckoutAddressReady();
                        const couponInput = document.getElementById('couponInput');
                        if (couponInput && couponInput.value.trim()) {
                            window.__checkoutToken = '';
                            await applyCoupon();
                        } else {
                            await loadCartSummary();
                        }

                        closeOtpModal();

                        if (!hasAddressReady) {
                            alert('Login successful. Please add or select a delivery address to place the order.');
                            return;
                        }

                        await placeOrder();
                    }

                    /* Resend timer */
                    function startResendTimer() {
                        let secs = 30;
                        const timerEl = document.getElementById('timerCount');
                        const timerText = document.getElementById('otpTimerText');
                        timerEl.textContent = secs + 's';
                        timerText.innerHTML = `Resend OTP in <strong id="timerCount">${secs}s</strong>`;

                        clearInterval(otpTimer);
                        otpTimer = setInterval(() => {
                            secs--;
                            document.getElementById('timerCount').textContent = secs + 's';
                            if (secs <= 0) {
                                clearInterval(otpTimer);
                                document.getElementById('otpTimerText').innerHTML =
                                    `<span class="resend-link" onclick="resendOtp()">Resend OTP</span>`;
                            }
                        }, 1000);
                    }

                    async function resendOtp() {
                        await sendOtp();
                        const otp1 = document.getElementById(otpFieldIds[0]);
                        if (otp1) otp1.focus();
                    }

                    /* ══ PLACE ORDER (called after OTP success) ══ */
                    async function placeOrder() {
                        const addressId = window.__selectedAddressId;
                        if (!addressId) {
                            alert('Please select a delivery address.');
                            return;
                        }

                        if (!window.__checkoutToken) {
                            const tokenState = await ensureCheckoutToken(window.__couponCode || null, {
                                forceRefresh: true
                            });
                            if (tokenState.requiresLogin) {
                                openOtpModal();
                                return;
                            }
                            if (!tokenState.success) {
                                alert(tokenState.message || 'Unable to prepare checkout. Please try again.');
                                return;
                            }
                        }

                        const res = await fetch(api.placeOrderUrl, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                ...(api.csrf ? {
                                    'X-CSRF-TOKEN': api.csrf
                                } : {})
                            },
                            body: JSON.stringify({
                                address_id: Number(addressId),
                                coupon_code: window.__couponCode || null,
                                payment_method: 'cod',
                                checkout_token: window.__checkoutToken || ''
                            })
                        });

                        if (res.status === 401 || res.status === 419) {
                            openOtpModal();
                            return;
                        }

                        const payload = await res.json().catch(() => ({}));
                        if (!res.ok) {
                            alert(payload.message || 'Unable to place order.');
                            return;
                        }

                        updateCsrfToken(payload.csrf_token || '');

                        const orderNumber = payload.order?.order_number || '';
                        const box = document.querySelector('#successOverlay .order-id-box');
                        if (box && orderNumber) box.textContent = `Order ID: ${orderNumber}`;

                        document.getElementById('progressFill').style.width = '100%';
                        setTimeout(() => document.getElementById('successOverlay').classList.add('show'), 400);
                    }

                    async function loadAddresses() {
                        const res = await fetch(api.addressesUrl, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        if (res.status === 401 || res.status === 419) {
                            const panel = document.getElementById('savedAddrPanel');
                            if (panel) panel.innerHTML =
                                `<div style="padding:14px;color:var(--text-light)">Login at checkout to load saved addresses.</div>`;
                            window.__selectedAddressId = '';
                            return;
                        }
                        if (!res.ok) return;

                        const payload = await res.json().catch(() => ({}));
                        const addresses = payload.data || [];
                        const panel = document.getElementById('savedAddrPanel');
                        panel.innerHTML = '';

                        if (!addresses.length) {
                            panel.innerHTML =
                                `<div style="padding:14px;color:var(--text-light)">No saved address. Please add a new address.</div>`;
                            window.__selectedAddressId = '';
                            return;
                        }

                        addresses.forEach((a, idx) => {
                            const div = document.createElement('div');
                            div.className = 'addr-item' + (idx === 0 ? ' selected' : '');
                            div.setAttribute('data-address-id', a.id);
                            div.setAttribute('onclick', 'selectAddr(this)');
                            div.innerHTML = `
                                <div class="addr-radio"></div>
                                <div class="addr-info">
                                    <div class="addr-name">${a.full_name} <span class="addr-type-tag">🏠 ${a.label || 'Saved'}</span></div>
                                    <div class="addr-line">${a.address_line_1}${a.address_line_2 ? (', ' + a.address_line_2) : ''}<br>${a.city}, ${a.state} – ${a.postal_code}</div>
                                    <div class="addr-phone">📞 +91 ${a.phone}</div>
                                </div>
                                <button class="addr-del-btn" type="button" title="Delete">🗑</button>
                            `;
                            div.querySelector('button').addEventListener('click', async (e) => {
                                e.stopPropagation();
                                await fetch(`${api.addressesUrl}/${a.id}`, {
                                    method: 'DELETE',
                                    headers: {
                                        ...(api.csrf ? {
                                            'X-CSRF-TOKEN': api.csrf
                                        } : {})
                                    }
                                });
                                loadAddresses();
                            });
                            panel.appendChild(div);
                        });

                        window.__selectedAddressId = String(addresses[0].id);
                    }

                    async function loadCartSummary() {
                        const res = await fetch(api.cartUrl, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        if (res.status === 401 || res.status === 419) {
                            renderPendingCheckoutCart();
                            return;
                        }
                        if (!res.ok) return;

                        const payload = await res.json().catch(() => ({}));
                        const items = payload.cart?.items || [];
                        const pricing = payload.pricing || {};
                        const totalQuantity = items.reduce((sum, it) => sum + Number(it.quantity || 0), 0);
                        currentTotal = Number(pricing.grand_total || 0);
                        updatePriceUI(pricing, totalQuantity);

                        const itemsCount = document.querySelector('.item-count');
                        if (itemsCount) itemsCount.textContent = `${totalQuantity} Items`;

                        const cartWrap = document.getElementById('checkoutCartItems');
                        if (cartWrap) {
                            cartWrap.innerHTML = '';
                            if (!items.length) {
                                cartWrap.innerHTML = `<div style="padding:10px;color:var(--text-light)">Your cart is empty.</div>`;
                            } else {
                                items.forEach(it => {
                                    const name = it.product?.name || 'Product';
                                    const qty = it.quantity || 1;
                                    const unitPrice = it.product_variant ? it.product_variant.price : it.product
                                        ?.base_price;
                                    const linePrice = Number(unitPrice || 0) * Number(qty || 0);
                                    const img = it.product?.primary_image?.image_path ? ('/storage/' + it.product
                                        .primary_image.image_path) : 'img/product2.png';

                                    const row = document.createElement('div');
                                    row.className = 'ci';
                                    row.innerHTML = `
                                      <div class="ci-img"><img src="${img}" alt=""></div>
                                      <div class="ci-info">
                                        <div class="ci-name">${name}</div>
                                        <div class="ci-variant">${it.product_variant?.name || ''}</div>
                                        <div class="ci-qty-row">
                                          ${createCheckoutQtyControls(qty)}
                                        </div>
                                      </div>
                                      <div>
                                        <div class="ci-price">₹${linePrice.toLocaleString('en-IN', { maximumFractionDigits: 2 })}</div>
                                      </div>
                                    `;
                                    cartWrap.appendChild(row);
                                    bindCheckoutQtyControls(row, qty, async nextQty => {
                                        await updateServerCartQuantity(it.id, nextQty);
                                        await loadCartSummary();
                                    });
                                });
                            }
                        }

                        // generate a token for idempotency
                        window.__checkoutToken = window.__checkoutToken || '';
                        if (!window.__checkoutToken) {
                            const tokenState = await ensureCheckoutToken(window.__couponCode || null);
                            if (tokenState.requiresLogin) {
                                return;
                            }
                        }

                        // COD only: update button with fee
                        const totalWithCod = currentTotal;
                        const payBtn = document.getElementById('paymentPlaceBtn');
                        if (payBtn) payBtn.innerHTML = `🔒 Place Order — ₹${totalWithCod.toLocaleString('en-IN')}`;
                        const pob = document.querySelector('.place-order-btn');
                        if (pob) pob.innerHTML = `🔒 Place Order Securely — ₹${totalWithCod.toLocaleString('en-IN')}`;
                    }

                    async function saveNewAddress() {
                        const payload = getNewAddressPayload();
                        if (!hasRequiredNewAddressFields(payload)) {
                            alert('Please fill all required address fields.');
                            return;
                        }

                        const address = await persistNewAddress(payload);
                        if (!address?.id) {
                            return;
                        }

                        await loadAddresses();
                        window.__selectedAddressId = String(address.id);
                        const savedTab = Array.from(document.querySelectorAll('.addr-tab')).find(t => t.textContent.includes(
                            'Saved'));
                        if (savedTab) setAddrTab(savedTab);
                    }

                    document.addEventListener('DOMContentLoaded', function() {
                        loadAddresses();
                        loadCartSummary();
                        const cod = document.getElementById('payMethodCod');
                        if (cod) selectPayMethod(cod, 'cod');
                    });

                    /* Close modal on backdrop click */
                    document.getElementById('otpModal').addEventListener('click', function(e) {
                        if (e.target === this) closeOtpModal();
                    });

                    document.getElementById('successOverlay').addEventListener('click', function(e) {
                        if (e.target === this) this.classList.remove('show');
                    });
                </script>
            @endpush


        @endsection
