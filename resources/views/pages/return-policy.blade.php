@extends('layouts.main')
@section('title', 'Refunds & Return Policy — NutriBuddy Kids')

@push('styles')
    <style>
        /* ── HERO — pink/dark (matches T&C) ── */
        .page-hero {
            background: linear-gradient(135deg, var(--dk) 0%, #260050 50%, #0d0030 100%);
            padding: 130px 5% 56px;
            position: relative;
            overflow: hidden;
            text-align: center
        }

        .page-hero::before {
            content: '';
            position: absolute;
            top: -80px;
            right: -80px;
            width: 360px;
            height: 360px;
            background: radial-gradient(circle, rgba(255, 77, 143, .12), transparent 65%);
            border-radius: 50%;
            pointer-events: none
        }

        .page-hero::after {
            content: '';
            position: absolute;
            bottom: -60px;
            left: -60px;
            width: 280px;
            height: 280px;
            background: radial-gradient(circle, rgba(124, 58, 237, .1), transparent 65%);
            border-radius: 50%;
            pointer-events: none
        }

        .hero-eyebrow {
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: .72rem;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            color: var(--pk);
            margin-bottom: 12px;
            display: block;
            position: relative;
            z-index: 2
        }

        .hero-title {
            font-family: 'Fredoka One', cursive;
            font-size: clamp(2rem, 4vw, 3rem);
            color: #fff;
            margin-bottom: 14px;
            position: relative;
            z-index: 2;
            line-height: 1.1
        }

        .hero-title span {
            color: var(--ye)
        }

        .hero-subtitle {
            font-size: .95rem;
            color: rgba(255, 255, 255, .55);
            max-width: 540px;
            margin: 0 auto 24px;
            line-height: 1.7;
            position: relative;
            z-index: 2
        }

        .hero-meta {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            flex-wrap: wrap;
            position: relative;
            z-index: 2
        }

        .meta-pill {
            background: rgba(255, 255, 255, .08);
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 50px;
            padding: 7px 16px;
            font-family: 'Nunito', sans-serif;
            font-weight: 800;
            font-size: .75rem;
            color: rgba(255, 255, 255, .75);
            display: flex;
            align-items: center;
            gap: 6px
        }

        /* ── PROMISE STRIP ── */
        .promise-strip {
            background: var(--white);
            padding: 0 5%;
            border-bottom: 1.5px solid var(--border);
            box-shadow: 0 2px 10px rgba(0, 0, 0, .04)
        }

        .promise-inner {
            max-width: 1100px;
            margin: 0 auto;
            display: flex;
            gap: 0;
            overflow-x: auto
        }

        .promise-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 16px 20px;
            border-right: 1px solid var(--border);
            flex-shrink: 0;
            min-width: 190px
        }

        .promise-item:last-child {
            border-right: none
        }

        .pi-ico {
            font-size: 1.4rem;
            flex-shrink: 0
        }

        .pi-title {
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: .82rem;
            color: var(--dk)
        }

        .pi-sub {
            font-size: .7rem;
            color: var(--text2)
        }



        /* ── LAYOUT ── */
        .page-wrap {
            max-width: 1100px;
            margin: 0 auto;
            padding: 40px 5% 80px;
            display: grid;
            grid-template-columns: 260px 1fr;
            gap: 36px;
            align-items: start
        }

        aside {
            display: flex;
            flex-direction: column;
            gap: 14px
        }

        /* ── SIDEBAR ── */
        .toc-sidebar {
            position: sticky;
            top: 122px;
            background: var(--white);
            border: 1.5px solid var(--border);
            border-radius: 14px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .05);
            max-height: calc(100vh - 122px - 20px);
            overflow-y: auto
        }

        .toc-hdr {
            background: linear-gradient(135deg, var(--pk), var(--pkd));
            padding: 14px 18px;
            border-radius: 13px 13px 0 0
        }

        .toc-hdr-title {
            font-family: 'Fredoka One', cursive;
            color: #fff;
            font-size: .95rem
        }

        .toc-list {
            list-style: none;
            padding: 10px 0
        }

        .toc-list li a {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 9px 18px;
            font-family: 'Nunito', sans-serif;
            font-weight: 700;
            font-size: .78rem;
            color: var(--text2);
            text-decoration: none;
            border-left: 3px solid transparent;
            transition: all .2s
        }

        .toc-list li a:hover,
        .toc-list li a.active {
            color: var(--pk);
            background: var(--pkl);
            border-left-color: var(--pk)
        }

        .tnum {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: var(--pkl);
            color: var(--pk);
            font-size: .62rem;
            font-weight: 900;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0
        }

        .toc-list li a.active .tnum {
            background: var(--pk);
            color: #fff
        }

        .toc-divider {
            height: 1px;
            background: var(--border);
            margin: 6px 0
        }

        .toc-footer {
            padding: 12px 18px;
            font-size: .7rem;
            color: #bbb;
            font-family: 'Nunito', sans-serif;
            border-top: 1px solid var(--border);
            border-radius: 0 0 13px 13px
        }

        /* Quick Request Box */
        .quick-request {
            background: var(--white);
            border: 1.5px solid var(--border);
            border-radius: 14px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .05);
            position: sticky;
            top: calc(122px + 300px)
        }

        .qr-hdr {
            background: linear-gradient(135deg, var(--pk), var(--pkd));
            padding: 14px 18px;
            border-radius: 13px 13px 0 0
        }

        .qr-title {
            font-family: 'Fredoka One', cursive;
            color: #fff;
            font-size: .9rem
        }

        .qr-sub {
            font-size: .68rem;
            color: rgba(255, 255, 255, .75);
            margin-top: 2px
        }

        .qr-body {
            padding: 16px 18px;
            display: flex;
            flex-direction: column;
            gap: 9px;
            border-radius: 0 0 13px 13px
        }

        .qr-inp {
            width: 100%;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            padding: 9px 12px;
            font-size: .82rem;
            font-family: 'DM Sans', sans-serif;
            outline: none;
            transition: border-color .2s;
            background: #fff;
            color: #1a1a2e
        }

        .qr-inp:focus {
            border-color: var(--pk)
        }

        .qr-inp::placeholder {
            color: #bbb
        }

        .qr-btn {
            background: linear-gradient(135deg, var(--pk), var(--pkd));
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 10px;
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: .82rem;
            cursor: pointer;
            transition: all .2s;
            width: 100%
        }

        .qr-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(255, 77, 143, .3)
        }

        .qr-note {
            font-size: .68rem;
            color: #aaa;
            text-align: center;
            line-height: 1.5
        }

        /* ── CONTENT CARDS ── */
        .content-card {
            background: var(--white);
            border: 1.5px solid var(--border);
            border-radius: 14px;
            padding: 36px 40px;
            margin-bottom: 22px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .04);
            scroll-margin-top: 88px
        }

        .content-card:last-child {
            margin-bottom: 0
        }

        .sec-num {
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: .68rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--pk);
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 8px
        }

        .sec-num::before {
            content: '';
            display: inline-block;
            width: 20px;
            height: 2px;
            background: var(--pk);
            border-radius: 2px
        }

        .sec-h {
            font-family: 'Fredoka One', cursive;
            font-size: 1.45rem;
            color: var(--dk);
            margin-bottom: 16px;
            line-height: 1.2;
            display: flex;
            align-items: center;
            gap: 10px
        }

        .sh-ico {
            font-size: 1.3rem
        }

        .sec-intro {
            font-size: .9rem;
            color: var(--text2);
            line-height: 1.78;
            margin-bottom: 18px;
            padding: 16px 18px;
            background: linear-gradient(135deg, rgba(255, 77, 143, .04), rgba(124, 58, 237, .02));
            border-radius: 10px;
            border-left: 3px solid var(--pk)
        }

        .body-text {
            font-size: .875rem;
            color: #444;
            line-height: 1.80;
            margin-bottom: 14px
        }

        .body-text:last-child {
            margin-bottom: 0
        }

        .sub-h {
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: .9rem;
            color: var(--dk);
            margin: 16px 0 8px;
            display: flex;
            align-items: center;
            gap: 7px
        }

        .sub-h::before {
            content: '';
            display: inline-block;
            width: 3px;
            height: 14px;
            background: var(--pu);
            border-radius: 2px
        }

        /* Lists */
        .terms-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 16px
        }

        .terms-list li {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: .875rem;
            color: #444;
            line-height: 1.72
        }

        .li-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--pk);
            flex-shrink: 0;
            margin-top: 6px
        }

        .li-dot.red {
            background: #f44
        }

        .li-dot.orange {
            background: var(--or)
        }

        .li-dot.blue {
            background: var(--sk)
        }

        .li-dot.purple {
            background: var(--pu)
        }

        .li-dot.green {
            background: var(--mn)
        }

        /* Highlight boxes */
        .hbox {
            border-radius: 12px;
            padding: 18px 20px;
            margin-bottom: 16px;
            display: flex;
            gap: 12px;
            align-items: flex-start
        }

        .hb-pink {
            background: rgba(255, 77, 143, .06);
            border: 1.5px solid rgba(255, 77, 143, .18)
        }

        .hb-purple {
            background: rgba(124, 58, 237, .05);
            border: 1.5px solid rgba(124, 58, 237, .15)
        }

        .hb-green {
            background: var(--mnl);
            border: 1.5px solid rgba(0, 214, 143, .3)
        }

        .hb-yellow {
            background: var(--yel);
            border: 1.5px solid rgba(255, 214, 0, .4)
        }

        .hb-blue {
            background: var(--skl);
            border: 1.5px solid rgba(0, 191, 255, .3)
        }

        .hb-orange {
            background: var(--orl);
            border: 1.5px solid rgba(255, 107, 53, .25)
        }

        .hb-red {
            background: #fff0f0;
            border: 1.5px solid #ffcccc
        }

        .hbox-ico {
            font-size: 1.3rem;
            flex-shrink: 0;
            margin-top: 1px
        }

        .hbox-body .hbox-title {
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: .85rem;
            color: var(--dk);
            margin-bottom: 4px
        }

        .hbox-body p {
            font-size: .83rem;
            color: #555;
            line-height: 1.68
        }

        .hbox-body strong {
            color: var(--dk)
        }

        /* Steps */
        .steps-flow {
            display: flex;
            flex-direction: column;
            gap: 0;
            margin-bottom: 16px;
            position: relative
        }

        .steps-flow::before {
            content: '';
            position: absolute;
            left: 19px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(to bottom, var(--pk), var(--pu));
            border-radius: 2px;
            opacity: .25
        }

        .step-item {
            display: grid;
            grid-template-columns: 40px 1fr;
            gap: 14px;
            padding: 14px 0;
            align-items: start;
            position: relative
        }

        .step-num {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--pk), var(--pkd));
            color: #fff;
            font-family: 'Fredoka One', cursive;
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            position: relative;
            z-index: 1;
            box-shadow: 0 2px 8px rgba(255, 77, 143, .3)
        }

        .step-body .step-title {
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: .9rem;
            color: var(--dk);
            margin-bottom: 4px
        }

        .step-body .step-desc {
            font-size: .83rem;
            color: #666;
            line-height: 1.65
        }

        .step-body .step-time {
            font-size: .7rem;
            font-family: 'Nunito', sans-serif;
            font-weight: 800;
            color: var(--pk);
            margin-top: 4px;
            display: inline-flex;
            align-items: center;
            gap: 4px
        }

        /* Table */
        .policy-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            border-radius: 10px;
            overflow: hidden;
            border: 1.5px solid var(--border)
        }

        .policy-table th {
            background: linear-gradient(135deg, var(--dk), #260050);
            color: #fff;
            padding: 12px 16px;
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: .78rem;
            text-align: left;
            letter-spacing: .3px
        }

        .policy-table td {
            padding: 11px 16px;
            font-size: .83rem;
            color: #444;
            border-bottom: 1px solid var(--border);
            line-height: 1.5;
            vertical-align: top
        }

        .policy-table tr:last-child td {
            border-bottom: none
        }

        .policy-table tr:nth-child(even) td {
            background: #fafafa
        }

        .policy-table tr:hover td {
            background: var(--pkl)
        }

        .eligible-yes {
            color: var(--mn);
            font-weight: 800;
            font-family: 'Nunito', sans-serif
        }

        .eligible-no {
            color: #f44;
            font-weight: 800;
            font-family: 'Nunito', sans-serif
        }

        .eligible-maybe {
            color: var(--or);
            font-weight: 800;
            font-family: 'Nunito', sans-serif
        }

        /* Timeline */
        .timeline {
            display: flex;
            gap: 0;
            margin: 18px 0;
            overflow-x: auto;
            padding-bottom: 6px
        }

        .tl-item {
            flex: 1;
            min-width: 120px;
            text-align: center;
            position: relative
        }

        .tl-item:not(:last-child)::after {
            content: '→';
            position: absolute;
            right: -12px;
            top: 14px;
            font-size: 1rem;
            color: var(--pk);
            font-family: 'Fredoka One', cursive
        }

        .tl-ball {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--pk), var(--pkd));
            color: #fff;
            font-family: 'Fredoka One', cursive;
            font-size: .85rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 8px;
            box-shadow: 0 2px 8px rgba(255, 77, 143, .3)
        }

        .tl-label {
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: .72rem;
            color: var(--dk);
            margin-bottom: 2px
        }

        .tl-time {
            font-size: .65rem;
            color: var(--text2)
        }

        /* Contact */
        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-top: 16px
        }

        .contact-item {
            background: linear-gradient(135deg, rgba(255, 77, 143, .05), rgba(124, 58, 237, .03));
            border: 1.5px solid var(--pkl);
            border-radius: 12px;
            padding: 18px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all .25s
        }

        .contact-item:hover {
            border-color: var(--pk);
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(255, 77, 143, .1)
        }

        .ci-ico {
            font-size: 1.5rem;
            flex-shrink: 0
        }

        .ci-body .ci-label {
            font-size: .68rem;
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #aaa;
            margin-bottom: 3px
        }

        .ci-body a,
        .ci-body span {
            font-family: 'Nunito', sans-serif;
            font-weight: 800;
            font-size: .85rem;
            color: var(--pk);
            text-decoration: none
        }

        .ci-body a:hover {
            text-decoration: underline
        }

        /* Action bar */
        .action-bar {
            background: linear-gradient(135deg, var(--dk), #260050);
            border-radius: 14px;
            padding: 20px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 22px
        }

        .ab-text .ab-title {
            font-family: 'Fredoka One', cursive;
            color: #fff;
            font-size: 1rem;
            margin-bottom: 2px
        }

        .ab-text .ab-sub {
            font-size: .76rem;
            color: rgba(255, 255, 255, .45)
        }

        .ab-btns {
            display: flex;
            gap: 9px
        }

        .ab-btn {
            padding: 9px 16px;
            border-radius: 8px;
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: .78rem;
            cursor: pointer;
            transition: all .2s;
            border: none;
            display: flex;
            align-items: center;
            gap: 5px
        }

        .ab-btn.print {
            background: rgba(255, 255, 255, .1);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, .2)
        }

        .ab-btn.print:hover {
            background: rgba(255, 255, 255, .2)
        }

        .ab-btn.dl {
            background: var(--pk);
            color: #fff
        }

        .ab-btn.dl:hover {
            background: var(--pkd)
        }

        /* Back to top */
        .back-top {
            position: fixed;
            bottom: 28px;
            right: 28px;
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--pk), var(--pkd));
            color: #fff;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 14px rgba(255, 77, 143, .4);
            transition: all .3s;
            z-index: 100;
            opacity: 0;
            transform: translateY(10px)
        }

        .back-top.show {
            opacity: 1;
            transform: translateY(0)
        }

        .back-top:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 22px rgba(255, 77, 143, .55)
        }

        /* Footer */
        .mini-footer {
            background: linear-gradient(135deg, var(--dk), #260050);
            padding: 28px 5%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px
        }

        .mf-brand {
            font-family: 'Fredoka One', cursive;
            font-size: 1.2rem;
            color: var(--pk)
        }

        .mf-links {
            display: flex;
            gap: 20px;
            list-style: none;
            flex-wrap: wrap
        }

        .mf-links a {
            color: rgba(255, 255, 255, .5);
            text-decoration: none;
            font-size: .78rem;
            transition: color .2s
        }

        .mf-links a:hover {
            color: #fff
        }

        .mf-copy {
            color: rgba(255, 255, 255, .35);
            font-size: .72rem
        }

        /* Toast */
        .toast {
            position: fixed;
            bottom: 80px;
            left: 50%;
            transform: translateX(-50%) translateY(20px);
            background: var(--dk);
            color: #fff;
            font-family: 'Nunito', sans-serif;
            font-weight: 800;
            font-size: .84rem;
            padding: 12px 24px;
            border-radius: 50px;
            z-index: 999;
            opacity: 0;
            transition: all .3s;
            pointer-events: none;
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap
        }

        .toast.show {
            opacity: 1;
            transform: translateX(-50%) translateY(0)
        }

        /* ── RESPONSIVE ── */
        @media(max-width:860px) {
            .page-wrap {
                grid-template-columns: 1fr;
                gap: 24px
            }

            .toc-sidebar,
            .quick-request {
                display: none
            }

            aside {
                width: 100%
            }

            .nav-links,
            .nav-cta {
                display: none
            }

            .hamburger {
                display: flex
            }

            .mob-menu {
                display: block
            }

            .promise-inner {
                gap: 0;
                overflow-x: auto
            }

            .promise-item {
                min-width: 160px;
                padding: 14px 14px
            }

            .content-card {
                padding: 24px 24px
            }

            .sec-h {
                font-size: 1.25rem
            }

            .policy-table {
                font-size: .8rem
            }

            .policy-table th,
            .policy-table td {
                padding: 10px 12px
            }
        }

        @media(max-width:640px) {
            .page-wrap {
                padding: 20px 4% 60px;
                gap: 16px
            }

            .content-card {
                padding: 20px 16px;
                margin-bottom: 18px
            }

            .contact-grid {
                grid-template-columns: 1fr
            }

            .action-bar {
                flex-direction: column;
                align-items: flex-start;
                padding: 16px 18px
            }

            .timeline {
                gap: 6px
            }

            .tl-item {
                min-width: 80px;
                font-size: .75rem
            }

            .tl-item:not(:last-child)::after {
                right: -8px;
                font-size: .85rem;
                top: 10px
            }

            .back-top {
                bottom: 18px;
                right: 16px;
                width: 38px;
                height: 38px;
                font-size: .85rem
            }

            .hero-title {
                font-size: clamp(1.5rem, 3vw, 2.25rem)
            }

            .hero-subtitle {
                font-size: .85rem
            }

            .hero-meta {
                gap: 8px;
                font-size: .65rem
            }

            .meta-pill {
                font-size: .65rem;
                padding: 5px 10px
            }

            .sec-h {
                font-size: 1.1rem
            }

            .sec-intro {
                padding: 12px 14px;
                font-size: .85rem
            }

            .body-text {
                font-size: .82rem
            }

            .policy-table {
                font-size: .75rem;
                margin-bottom: 12px
            }

            .policy-table th,
            .policy-table td {
                padding: 8px 8px
            }

            .step-item {
                grid-template-columns: 32px 1fr;
                gap: 10px;
                padding: 10px 0
            }

            .step-num {
                width: 32px;
                height: 32px;
                font-size: .9rem
            }

            .contact-item {
                padding: 14px 12px;
                flex-direction: column;
                text-align: center
            }
        }

        @media(max-width:500px) {
            .page-hero {
                padding: 60px 4% 40px
            }

            .hero-eyebrow {
                font-size: .65rem;
                margin-bottom: 8px
            }

            .hero-subtitle {
                font-size: .8rem;
                padding: 0 4%
            }

            .meta-pill {
                font-size: .6rem;
                padding: 4px 8px
            }

            .page-wrap {
                padding: 7px 0% 0px
            }

            .ab-text {
                flex: 1
            }

            .ab-title {
                font-size: .9rem
            }

            .ab-sub {
                font-size: .7rem
            }

            .ab-btn {
                font-size: .75rem;
                padding: 8px 12px
            }
        }

        @media(max-width:420px) {
            .promise-strip {
                display: none
            }

            .page-hero {
                padding: 108px 4% 32px
            }

            .hero-title {
                font-size: 1.4rem
            }

            .contact-grid {
                grid-template-columns: 1fr
            }

            .step-item {
                grid-template-columns: 28px 1fr;
                gap: 8px
            }

            .step-num {
                width: 28px;
                height: 28px;
                font-size: .8rem
            }

            .content-card {
                padding: 16px 14px;
                border-radius: 10px
            }

            .contact-item {
                padding: 12px 10px;
                gap: 8px
            }

            .ci-ico {
                font-size: 1.3rem
            }
        }
    </style>
@endpush

@section('content')


    <!-- HERO -->
    <div class="page-hero">
        <span class="hero-eyebrow">Policy · NutriBuddy Kids</span>
        <h1 class="hero-title">Refunds &amp; <span>Return Policy</span></h1>
        <p class="hero-subtitle">We stand behind every product with our happiness guarantee. If something isn't right, we'll
            make it right — quickly and without any hassle.</p>
        <div class="hero-meta">
            <div class="meta-pill">📅 Last Updated: June 1, 2025</div>
            <div class="meta-pill">🔄 30-Day Guarantee</div>
            <div class="meta-pill">⚡ 5–7 Day Refunds</div>
            <div class="meta-pill">🇮🇳 Governed by Indian Law</div>
        </div>
    </div>

    <!-- PROMISE STRIP -->
    <div class="promise-strip">
        <div class="promise-inner">
            <div class="promise-item">
                <div class="pi-ico">😊</div>
                <div>
                    <div class="pi-title">30-Day Taste Guarantee</div>
                    <div class="pi-sub">Full refund if your child hates it</div>
                </div>
            </div>
            <div class="promise-item">
                <div class="pi-ico">📦</div>
                <div>
                    <div class="pi-title">Damaged in Transit</div>
                    <div class="pi-sub">Free replacement, no questions</div>
                </div>
            </div>
            <div class="promise-item">
                <div class="pi-ico">⚡</div>
                <div>
                    <div class="pi-title">Fast Refunds</div>
                    <div class="pi-sub">Back to you in 5–7 business days</div>
                </div>
            </div>
            <div class="promise-item">
                <div class="pi-ico">💬</div>
                <div>
                    <div class="pi-title">Dedicated Support</div>
                    <div class="pi-sub">Real humans, Mon–Sat 9AM–7PM</div>
                </div>
            </div>
        </div>
    </div>



    <!-- MAIN -->
    <div class="page-wrap">

        <!-- SIDEBAR -->
        <aside>
            <div class="toc-sidebar">
                <div class="toc-hdr">
                    <div class="toc-hdr-title">📋 Table of Contents</div>
                </div>
                <ul class="toc-list">
                    <li><a href="#s1" class="active"><span class="tnum">1</span>Our Guarantee</a></li>
                    <li><a href="#s2"><span class="tnum">2</span>What's Eligible</a></li>
                    <li><a href="#s3"><span class="tnum">3</span>What's Not Eligible</a></li>
                    <li><a href="#s4"><span class="tnum">4</span>How to Request</a></li>
                    <li><a href="#s5"><span class="tnum">5</span>Refund Timeline</a></li>
                    <li><a href="#s6"><span class="tnum">6</span>Return Shipping</a></li>
                    <li><a href="#s7"><span class="tnum">7</span>Replacements</a></li>
                    <li><a href="#s8"><span class="tnum">8</span>Subscription Orders</a></li>
                    <li><a href="#s9"><span class="tnum">9</span>Cancellations</a></li>
                    <li><a href="#s10"><span class="tnum">10</span>Contact Us</a></li>
                </ul>
                <div class="toc-divider"></div>
                <div class="toc-footer">Last updated: 1 June 2025 · Version 1.8</div>
            </div>

            <!-- Quick Refund Request -->
            <div class="quick-request">
                <div class="qr-hdr">
                    <div class="qr-title">🔄 Quick Refund Request</div>
                    <div class="qr-sub">Fill in your order details below</div>
                </div>
                <div class="qr-body">
                    <input class="qr-inp" type="text" placeholder="Order ID (e.g. NB-2025-12345)">
                    <input class="qr-inp" type="email" placeholder="Registered email address">
                    <select class="qr-inp" style="appearance:none;cursor:pointer">
                        <option value="">Select reason</option>
                        <option>Damaged / Tampered packaging</option>
                        <option>Wrong product delivered</option>
                        <option>Taste not accepted by child</option>
                        <option>Product expired</option>
                        <option>Order not delivered</option>
                        <option>Allergic reaction</option>
                        <option>Changed my mind</option>
                        <option>Other</option>
                    </select>
                    <button class="qr-btn" onclick="submitRequest()">Submit Request →</button>
                    <div class="qr-note">We'll respond within <strong style="color:var(--pk)">24 business
                            hours</strong>.<br>For
                        urgent issues call 1800-123-4567.</div>
                </div>
            </div>
        </aside>

        <!-- CONTENT -->
        <main>

            <!-- Action bar -->
            <div class="action-bar">
                <div class="ab-text">
                    <div class="ab-title">Refunds &amp; Return Policy — NutriBuddy Kids</div>
                    <div class="ab-sub">Effective 1 June 2025 · Version 1.8 · Bengaluru, India</div>
                </div>
                <div class="ab-btns">
                    <button class="ab-btn print" onclick="window.print()">🖨 Print</button>
                    <button class="ab-btn dl" onclick="window.print()">⬇ Save PDF</button>
                </div>
            </div>

            <!-- S1 -->
            <div class="content-card" id="s1">
                <div class="sec-num">Section 01</div>
                <h2 class="sec-h"><span class="sh-ico">🏆</span>Our NutriBuddy Happiness Guarantee</h2>
                <div class="sec-intro">We believe in our products unconditionally. If you or your child are not completely
                    satisfied — for any reason — within 30 days of purchase, we will refund you in full. No lengthy forms,
                    no
                    interrogations, no drama.</div>
                <p class="body-text">At NutriBuddy, we don't just sell gummies — we build long-term trust with families
                    across
                    India. Our happiness guarantee is our promise that every rupee you spend with us is fully protected.</p>
                <div class="hbox hb-pink">
                    <div class="hbox-ico">✅</div>
                    <div class="hbox-body">
                        <div class="hbox-title">30-Day Taste Guarantee — Unconditional</div>
                        <p>If your child genuinely does not enjoy the flavor of any NutriBuddy product within 30 days of
                            delivery,
                            we'll give you a <strong>full refund or free replacement</strong> — your choice. We're that
                            confident.
                            (98% of kids love it, but that 2% matters to us!)</p>
                    </div>
                </div>
                <div class="hbox hb-purple">
                    <div class="hbox-ico">📦</div>
                    <div class="hbox-body">
                        <div class="hbox-title">Damaged in Transit — Zero Cost to You</div>
                        <p>If your product arrives with damaged, tampered, or broken packaging, we will send a <strong>free
                                replacement within 48 hours</strong> of your report. Just send us a photo via WhatsApp or
                            email. You
                            don't need to return anything.</p>
                    </div>
                </div>
            </div>

            <!-- S2 -->
            <div class="content-card" id="s2">
                <div class="sec-num">Section 02</div>
                <h2 class="sec-h"><span class="sh-ico">✅</span>What's Eligible for Return or Refund</h2>
                <div class="sec-intro">The following situations qualify for a full refund or free replacement. We've made
                    this
                    as straightforward as possible — we want you to feel safe purchasing from us.</div>
                <table class="policy-table">
                    <thead>
                        <tr>
                            <th>Reason</th>
                            <th>Eligible?</th>
                            <th>Resolution</th>
                            <th>Timeline</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Damaged / tampered packaging on delivery</td>
                            <td><span class="eligible-yes">✅ Yes</span></td>
                            <td>Free replacement</td>
                            <td>Within 48 hrs</td>
                        </tr>
                        <tr>
                            <td>Wrong product delivered</td>
                            <td><span class="eligible-yes">✅ Yes</span></td>
                            <td>Correct product sent + refund option</td>
                            <td>Within 48 hrs</td>
                        </tr>
                        <tr>
                            <td>Product expired / near expiry on delivery</td>
                            <td><span class="eligible-yes">✅ Yes</span></td>
                            <td>Full refund or replacement</td>
                            <td>5–7 business days</td>
                        </tr>
                        <tr>
                            <td>Child doesn't like the taste (30-day window)</td>
                            <td><span class="eligible-yes">✅ Yes</span></td>
                            <td>Full refund or replacement</td>
                            <td>5–7 business days</td>
                        </tr>
                        <tr>
                            <td>Order not delivered (confirmed non-delivery)</td>
                            <td><span class="eligible-yes">✅ Yes</span></td>
                            <td>Full refund or re-shipment</td>
                            <td>5–7 business days</td>
                        </tr>
                        <tr>
                            <td>Allergic reaction (with doctor's note)</td>
                            <td><span class="eligible-yes">✅ Yes</span></td>
                            <td>Full refund</td>
                            <td>3–5 business days</td>
                        </tr>
                        <tr>
                            <td>Significant product defect (e.g. melted, clumped)</td>
                            <td><span class="eligible-yes">✅ Yes</span></td>
                            <td>Full refund or replacement</td>
                            <td>5–7 business days</td>
                        </tr>
                        <tr>
                            <td>Missing items in order</td>
                            <td><span class="eligible-yes">✅ Yes</span></td>
                            <td>Missing items dispatched free</td>
                            <td>Within 24 hrs</td>
                        </tr>
                    </tbody>
                </table>
                <div class="hbox hb-yellow">
                    <div class="hbox-ico">⏱️</div>
                    <div class="hbox-body">
                        <div class="hbox-title">Report Window</div>
                        <p>For damaged/tampered products: report within <strong>48 hours</strong> of delivery.<br>For taste
                            guarantee: report within <strong>30 days</strong> of delivery date.<br>For non-delivery: report
                            within
                            <strong>15 days</strong> of expected delivery date.
                        </p>
                    </div>
                </div>
            </div>

            <!-- S3 -->
            <div class="content-card" id="s3">
                <div class="sec-num">Section 03</div>
                <h2 class="sec-h"><span class="sh-ico">❌</span>What's Not Eligible</h2>
                <div class="sec-intro">In the interest of fairness and to prevent misuse, the following situations do not
                    qualify for returns or refunds. We appreciate your understanding.</div>
                <ul class="terms-list">
                    <li>
                        <div class="li-dot red"></div><span>Products where <strong>more than 30% of the contents have been
                                consumed</strong> and the reason is not a defect, allergy, or wrong delivery.</span>
                    </li>
                    <li>
                        <div class="li-dot red"></div><span>Requests made <strong>after 30 days</strong> of the confirmed
                            delivery
                            date (except for subscription billing errors).</span>
                    </li>
                    <li>
                        <div class="li-dot red"></div><span>Products purchased from <strong>unauthorized third-party
                                sellers</strong> or resellers (only direct purchases from nutribuddy.in are covered).</span>
                    </li>
                    <li>
                        <div class="li-dot red"></div><span>Products with <strong>tampered or removed seals</strong> where
                            tampering
                            was caused by the customer, not in transit.</span>
                    </li>
                    <li>
                        <div class="li-dot red"></div><span>Refund requests where the <strong>original product is not
                                returned</strong> when return is required (see Section 6).</span>
                    </li>
                    <li>
                        <div class="li-dot red"></div><span>Change of mind after <strong>more than 30 days</strong> from
                            delivery.</span>
                    </li>
                    <li>
                        <div class="li-dot red"></div><span>Products that were clearly used inappropriately or not as per
                            the label
                            instructions (e.g. given to adults).</span>
                    </li>
                </ul>
                <div class="hbox hb-orange">
                    <div class="hbox-ico">⚠️</div>
                    <div class="hbox-body">
                        <div class="hbox-title">Misuse of Policy</div>
                        <p>Repeated or fraudulent refund requests may result in account suspension. We trust our customers
                            and this
                            policy is designed for genuine cases. Please reach out to us if you have any concerns — we
                            always resolve
                            issues fairly.</p>
                    </div>
                </div>
            </div>

            <!-- S4 -->
            <div class="content-card" id="s4">
                <div class="sec-num">Section 04</div>
                <h2 class="sec-h"><span class="sh-ico">📝</span>How to Request a Refund or Return</h2>
                <div class="sec-intro">We've made the return and refund process as simple as possible — no phone queues, no
                    complicated forms. Most requests are resolved within 24 business hours of submission.</div>
                <div class="steps-flow">
                    <div class="step-item">
                        <div class="step-num">1</div>
                        <div class="step-body">
                            <div class="step-title">Contact Us with Your Order Details</div>
                            <div class="step-desc">Email hello@nutribuddy.in or WhatsApp +91 98765 43210 with your Order
                                ID,
                                registered email, and reason for return/refund. You can also use the Quick Request form on
                                this page.
                            </div>
                            <div class="step-time">⏱ Anytime, 24/7</div>
                        </div>
                    </div>
                    <div class="step-item">
                        <div class="step-num">2</div>
                        <div class="step-body">
                            <div class="step-title">Share Photos (if applicable)</div>
                            <div class="step-desc">For damaged, wrong, or defective products — share clear photos of the
                                product and
                                packaging. This helps us process your request faster and improve our quality control.</div>
                            <div class="step-time">⏱ Within 48 hrs of delivery</div>
                        </div>
                    </div>
                    <div class="step-item">
                        <div class="step-num">3</div>
                        <div class="step-body">
                            <div class="step-title">Our Team Reviews &amp; Approves</div>
                            <div class="step-desc">Our customer happiness team reviews your request and approves it
                                typically within 1
                                business day. We may ask 1–2 clarifying questions in rare cases.</div>
                            <div class="step-time">⏱ Within 1 business day</div>
                        </div>
                    </div>
                    <div class="step-item">
                        <div class="step-num">4</div>
                        <div class="step-body">
                            <div class="step-title">Return Product (if required)</div>
                            <div class="step-desc">For change-of-mind returns after 7 days, you may be asked to return the
                                unused
                                portion. We'll send a prepaid return label — you don't pay for shipping. For damaged/wrong
                                items, return
                                is NOT required.</div>
                            <div class="step-time">⏱ Return label sent within 24 hrs</div>
                        </div>
                    </div>
                    <div class="step-item">
                        <div class="step-num">5</div>
                        <div class="step-body">
                            <div class="step-title">Refund Processed</div>
                            <div class="step-desc">Refund is initiated to your original payment method. UPI and wallet
                                refunds are
                                typically instant. Card refunds take 5–7 business days depending on your bank.</div>
                            <div class="step-time">⏱ 5–7 business days</div>
                        </div>
                    </div>
                </div>
                <div class="hbox hb-pink">
                    <div class="hbox-ico">💬</div>
                    <div class="hbox-body">
                        <div class="hbox-title">Multiple Ways to Reach Us</div>
                        <p>Email: <strong>hello@nutribuddy.in</strong> · WhatsApp: <strong>+91 98765 43210</strong> ·
                            Toll-Free:
                            <strong>1800-123-4567</strong> (Mon–Sat, 9 AM–7 PM IST) · Live Chat on website during business
                            hours.
                        </p>
                    </div>
                </div>
            </div>

            <!-- S5 -->
            <div class="content-card" id="s5">
                <div class="sec-num">Section 05</div>
                <h2 class="sec-h"><span class="sh-ico">⚡</span>Refund Timeline</h2>
                <div class="sec-intro">Once your return/refund is approved, here is the expected timeline for receiving
                    your
                    money back based on your payment method.</div>
                <div class="timeline">
                    <div class="tl-item">
                        <div class="tl-ball">1</div>
                        <div class="tl-label">Request Approved</div>
                        <div class="tl-time">Day 0</div>
                    </div>
                    <div class="tl-item">
                        <div class="tl-ball">2</div>
                        <div class="tl-label">Refund Initiated</div>
                        <div class="tl-time">Day 1</div>
                    </div>
                    <div class="tl-item">
                        <div class="tl-ball">3</div>
                        <div class="tl-label">Bank Processing</div>
                        <div class="tl-time">Day 2–5</div>
                    </div>
                    <div class="tl-item">
                        <div class="tl-ball">4</div>
                        <div class="tl-label">Refund Credited</div>
                        <div class="tl-time">Day 5–7</div>
                    </div>
                </div>
                <table class="policy-table">
                    <thead>
                        <tr>
                            <th>Payment Method</th>
                            <th>Refund Timeline</th>
                            <th>Where It Goes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>UPI (PhonePe, GPay, Paytm, BHIM)</td>
                            <td>Instant – 24 hours</td>
                            <td>Back to UPI account</td>
                        </tr>
                        <tr>
                            <td>Credit Card</td>
                            <td>5–7 business days</td>
                            <td>Back to credit card</td>
                        </tr>
                        <tr>
                            <td>Debit Card</td>
                            <td>3–5 business days</td>
                            <td>Back to bank account</td>
                        </tr>
                        <tr>
                            <td>Net Banking</td>
                            <td>3–5 business days</td>
                            <td>Back to bank account</td>
                        </tr>
                        <tr>
                            <td>Digital Wallets</td>
                            <td>Instant – 48 hours</td>
                            <td>Back to wallet balance</td>
                        </tr>
                        <tr>
                            <td>Cash on Delivery</td>
                            <td>5–7 business days</td>
                            <td>Bank transfer (NEFT)</td>
                        </tr>
                        <tr>
                            <td>NutriBuddy Coins</td>
                            <td>Instant</td>
                            <td>Coins reinstated to account</td>
                        </tr>
                    </tbody>
                </table>
                <div class="hbox hb-purple">
                    <div class="hbox-ico">ℹ️</div>
                    <div class="hbox-body">
                        <div class="hbox-title">Bank Processing Times</div>
                        <p>Refund timelines on the bank side are outside our control. If you don't see your refund within
                            the stated
                            timeframe, please contact your bank with the refund reference ID we provide. We are always happy
                            to follow
                            up on your behalf.</p>
                    </div>
                </div>
            </div>

            <!-- S6 -->
            <div class="content-card" id="s6">
                <div class="sec-num">Section 06</div>
                <h2 class="sec-h"><span class="sh-ico">📦</span>Return Shipping</h2>
                <div class="sec-intro">We believe you shouldn't pay a rupee to fix our mistakes. Here's our complete return
                    shipping policy.</div>
                <ul class="terms-list">
                    <li>
                        <div class="li-dot"></div><span>For <strong>damaged, wrong, or defective products</strong>: No
                            return
                            required. We trust you. Just share a photo and we'll process your request immediately.</span>
                    </li>
                    <li>
                        <div class="li-dot"></div><span>For <strong>taste guarantee refunds within 30 days</strong> where
                            less than
                            30% is consumed: No return required. We want to make your experience seamless.</span>
                    </li>
                    <li>
                        <div class="li-dot"></div><span>For <strong>change-of-mind returns</strong> (sealed/unopened
                            products within
                            7 days): We provide a <strong>prepaid return shipping label</strong>. No cost to you.</span>
                    </li>
                    <li>
                        <div class="li-dot"></div><span>For <strong>subscription cancellation returns</strong> of sealed
                            products:
                            Prepaid return label provided within 24 hours of approval.</span>
                    </li>
                </ul>
                <div class="hbox hb-pink">
                    <div class="hbox-ico">🚚</div>
                    <div class="hbox-body">
                        <div class="hbox-title">You Never Pay Return Shipping</div>
                        <p>In all eligible return scenarios, NutriBuddy covers the full cost of return shipping. We will
                            email you a
                            prepaid waybill from our logistics partner. Simply drop the parcel at the nearest pickup point.
                        </p>
                    </div>
                </div>
            </div>

            <!-- S7 -->
            <div class="content-card" id="s7">
                <div class="sec-num">Section 07</div>
                <h2 class="sec-h"><span class="sh-ico">🔄</span>Replacements</h2>
                <div class="sec-intro">In many cases, we offer free product replacement as an alternative to a refund.
                    Here's
                    how it works.</div>
                <ul class="terms-list">
                    <li>
                        <div class="li-dot"></div><span>Replacements are dispatched <strong>within 48 hours</strong> of
                            approval for
                            damaged or wrong deliveries.</span>
                    </li>
                    <li>
                        <div class="li-dot"></div><span>You may request a <strong>different flavor as a
                                replacement</strong> for
                            taste-related returns — we encourage you to try another flavor your child might enjoy.</span>
                    </li>
                    <li>
                        <div class="li-dot"></div><span>Replacements are always sent with <strong>free priority
                                shipping</strong>,
                            regardless of order value.</span>
                    </li>
                    <li>
                        <div class="li-dot"></div><span>If a replacement is not available due to stock constraints, a
                            <strong>full
                                refund</strong> will be processed automatically.</span>
                    </li>
                    <li>
                        <div class="li-dot"></div><span>Each order is eligible for <strong>one replacement</strong> per
                            incident.
                            Subsequent issues will be handled as refunds.</span>
                    </li>
                </ul>
            </div>

            <!-- S8 -->
            <div class="content-card" id="s8">
                <div class="sec-num">Section 08</div>
                <h2 class="sec-h"><span class="sh-ico">🔁</span>Subscription Orders</h2>
                <div class="sec-intro">Our Subscribe &amp; Save program offers the best value for recurring customers.
                    Here's
                    how refunds and cancellations work for subscription orders.</div>
                <ul class="terms-list">
                    <li>
                        <div class="li-dot blue"></div><span>Cancel your subscription anytime from your account dashboard
                            or by
                            contacting us — <strong>no penalties, no fees, ever</strong>.</span>
                    </li>
                    <li>
                        <div class="li-dot blue"></div><span>To avoid being charged for the next cycle, cancel at least
                            <strong>24
                                hours before the next billing date</strong>.</span>
                    </li>
                    <li>
                        <div class="li-dot blue"></div><span>If a subscription order is dispatched before cancellation is
                            processed,
                            the standard 30-day return policy applies.</span>
                    </li>
                    <li>
                        <div class="li-dot blue"></div><span>Subscription billing errors (e.g. double charges) are refunded
                            within
                            <strong>1 business day</strong> of reporting.</span>
                    </li>
                    <li>
                        <div class="li-dot blue"></div><span>Pausing a subscription is available for up to <strong>3
                                months</strong>
                            without losing your Subscribe &amp; Save discount.</span>
                    </li>
                </ul>
                <div class="hbox hb-purple">
                    <div class="hbox-ico">💡</div>
                    <div class="hbox-body">
                        <div class="hbox-title">Pause Instead of Cancel</div>
                        <p>Going on holiday or have enough stock? You can <strong>pause your subscription</strong> from your
                            account
                            dashboard for up to 3 months. Your discount is preserved and you won't be charged during the
                            pause period.
                        </p>
                    </div>
                </div>
            </div>

            <!-- S9 -->
            <div class="content-card" id="s9">
                <div class="sec-num">Section 09</div>
                <h2 class="sec-h"><span class="sh-ico">🚫</span>Order Cancellations</h2>
                <div class="sec-intro">Need to cancel an order you've just placed? Here's how quickly you need to act.
                </div>
                <table class="policy-table">
                    <thead>
                        <tr>
                            <th>Cancellation Window</th>
                            <th>Eligible?</th>
                            <th>Refund</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Within 1 hour of placing order</td>
                            <td><span class="eligible-yes">✅ Yes</span></td>
                            <td>Full refund, instant processing</td>
                        </tr>
                        <tr>
                            <td>1–12 hours after placing order</td>
                            <td><span class="eligible-yes">✅ Yes</span></td>
                            <td>Full refund in 24–48 hours</td>
                        </tr>
                        <tr>
                            <td>After order is dispatched (in transit)</td>
                            <td><span class="eligible-maybe">⚠️ Limited</span></td>
                            <td>Refund after return received (minus shipping)</td>
                        </tr>
                        <tr>
                            <td>After delivery (within 30 days)</td>
                            <td><span class="eligible-yes">✅ Yes</span></td>
                            <td>Standard return policy applies</td>
                        </tr>
                        <tr>
                            <td>After 30 days of delivery</td>
                            <td><span class="eligible-no">❌ No</span></td>
                            <td>Not eligible except defect cases</td>
                        </tr>
                    </tbody>
                </table>
                <p class="body-text">To cancel an order, contact us immediately at <strong>hello@nutribuddy.in</strong> or
                    call
                    <strong>1800-123-4567</strong>. The faster you act, the smoother the cancellation.
                </p>
            </div>

            <!-- S10 -->
            <div class="content-card" id="s10">
                <div class="sec-num">Section 10</div>
                <h2 class="sec-h"><span class="sh-ico">📞</span>Contact Us</h2>
                <div class="sec-intro">Our customer happiness team is here for you. Reach us through any channel below and
                    we
                    promise a response within 1 business day — usually much faster.</div>
                <div class="contact-grid">
                    <div class="contact-item">
                        <div class="ci-ico">✉️</div>
                        <div class="ci-body">
                            <div class="ci-label">Email (Refunds)</div><a
                                href="mailto:hello@nutribuddy.in">hello@nutribuddy.in</a>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="ci-ico">💬</div>
                        <div class="ci-body">
                            <div class="ci-label">WhatsApp</div><a href="tel:+919876543210">+91 98765 43210</a>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="ci-ico">📞</div>
                        <div class="ci-body">
                            <div class="ci-label">Toll-Free</div><a href="tel:18001234567">1800-123-4567</a>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="ci-ico">⏰</div>
                        <div class="ci-body">
                            <div class="ci-label">Support Hours</div><span>Mon–Sat, 9 AM – 7 PM IST</span>
                        </div>
                    </div>
                </div>
                <div class="hbox hb-pink" style="margin-top:18px">
                    <div class="hbox-ico">🤝</div>
                    <div class="hbox-body">
                        <div class="hbox-title">Our Promise to You</div>
                        <p>At NutriBuddy, every interaction with our team is handled by a real human who genuinely cares. We
                            don't
                            use scripts or bots for refund requests. We treat every case individually with empathy and
                            fairness —
                            because your trust is more valuable to us than any single transaction.</p>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- ══════════════════════════════════════════
           NEWSLETTER
      ══════════════════════════════════════════ -->
    <div class="newsletter reveal">
        <span class="sec-eye">Stay in the Loop</span>
        <h2 class="sec-title">Wellness Tips for Your Little Ones</h2>
        <p class="nl-sub">Join 25,000+ parents getting Ayurvedic parenting tips, exclusive discounts & early product
            access every week.</p>
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
                style="width:300px;height:300px;background:var(--pk);top:-80px;left:-80px;--dur:8s;--del:0s">
            </div>
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

            <!-- Newsletter Column -->
            <div class="fw-subscribe">
                <h4>Stay Updated</h4>
                <p>Join 25,000+ parents getting Ayurvedic parenting tips, exclusive offers &amp; early access every
                    week.</p>
                <div class="subscribe-wrap">
                    <input type="email" maxlength="50" placeholder="Enter your email" class="subs-input">
                    <button class="subs-btn">Subscribe</button>
                </div>
            </div>

        </div><!-- /footer-widget-area -->

        <!-- Payment Row -->
        <div class="footer-payment-row">
            <p style="color:#888;font-size:.78rem;font-family:'Nunito',sans-serif;font-weight:700">Secure Payments</p>
            <div class="footer-payment-cards">
                <div class="payment-card"><img src="img/visa.webp" alt="Visa"
                        onerror="this.outerHTML='<span>VISA</span>'">
                </div>
                <div class="payment-card"><img src="img/upi.png" alt="UPI"
                        onerror="this.outerHTML='<span>UPI</span>'"></div>
                <div class="payment-card"><img src="img/phonepe.webp" alt="PhonePe"
                        onerror="this.outerHTML='<span>PhonePe</span>'"></div>
                <div class="payment-card"><img src="img/Paytm-logo.webp" alt="Paytm"
                        onerror="this.outerHTML='<span>Paytm</span>'"></div>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="footer-bottom-bar">
            <div class="copyright">© 2025 <a href="#">NutriBuddy Kids</a>. All rights reserved.</div>
            <ul class="foot-links">
                <li><a href="#">Privacy Policy</a></li>
                <li><a href="#">Terms of Service</a></li>
                <li><a href="#">Cookie Policy</a></li>
            </ul>
        </div>

    </footer>


    <button class="back-top" id="backTop" onclick="window.scrollTo({top:0,behavior:'smooth'})">↑</button>


    @push('scripts')
        <script>
            const hamBtn = document.getElementById('hamBtn'),
                mobMenu = document.getElementById('mobMenu');
            hamBtn.addEventListener('click', () => {
                hamBtn.classList.toggle('open');
                mobMenu.classList.toggle('open')
            });
            window.addEventListener('scroll', () => {
                document.getElementById('backTop').classList.toggle('show', window.scrollY > 400)
            });
            const sections = document.querySelectorAll('.content-card[id]');
            const tocLinks = document.querySelectorAll('.toc-list a');
            const observer = new IntersectionObserver(entries => {
                entries.forEach(e => {
                    if (e.isIntersecting) {
                        tocLinks.forEach(l => l.classList.remove('active'));
                        const a = document.querySelector('.toc-list a[href="#' + e.target.id + '"]');
                        if (a) a.classList.add('active');
                    }
                });
            }, {
                threshold: .4,
                rootMargin: '-80px 0px -60% 0px'
            });
            sections.forEach(s => observer.observe(s));

            function submitRequest() {
                const t = document.getElementById('toast');
                t.classList.add('show');
                setTimeout(() => t.classList.remove('show'), 3500);
            }
        </script>
    @endpush
@endsection
