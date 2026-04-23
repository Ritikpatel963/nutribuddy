@extends('layouts.main')
@section('title', 'About Us — NutriBuddy Kids')

@push('styles')
    <style>
        .about-hero {
            background: linear-gradient(145deg, #FFF0FA 0%, #F0E5FF 50%, #FFDCF0 100%);
            padding: 130px 5% 80px;
            position: relative;
            overflow: hidden;
            text-align: center;
        }

        .about-hero::before {
            content: '';
            position: absolute;
            width: 560px;
            height: 560px;
            border-radius: 62% 38% 56% 44%/48% 62% 38% 52%;
            background: radial-gradient(circle, rgba(255, 77, 143, .12), transparent 70%);
            top: -160px;
            right: -120px;
            animation: blobMorph 10s ease-in-out infinite;
            pointer-events: none;
        }

        .about-hero::after {
            content: '';
            position: absolute;
            width: 380px;
            height: 380px;
            border-radius: 38% 62% 44% 56%/62% 38% 55% 45%;
            background: radial-gradient(circle, rgba(124, 58, 237, .09), transparent 70%);
            bottom: -80px;
            left: -80px;
            animation: blobMorph 14s ease-in-out infinite reverse;
            pointer-events: none;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--pkl);
            color: var(--pkd);
            border-radius: 50px;
            padding: 8px 20px;
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: .78rem;
            letter-spacing: 1.8px;
            text-transform: uppercase;
            margin-bottom: 24px;
            animation: badgePulse 3s ease-in-out infinite;
        }

        .about-hero h1 {
            font-family: 'Fredoka One', cursive;
            font-size: clamp(2.6rem, 5vw, 4.2rem);
            line-height: 1.08;
            color: var(--dk);
            margin-bottom: 20px;
        }

        .about-hero h1 .pop {
            color: var(--pk);
            position: relative;
            display: inline-block;
        }

        .about-hero h1 .pop::after {
            content: '';
            position: absolute;
            bottom: 4px;
            left: 0;
            right: 0;
            height: 10px;
            background: var(--ye);
            border-radius: 4px;
            z-index: -1;
            transform: skewX(-3deg);
        }

        .hero-desc {
            max-width: 620px;
            margin: 0 auto 40px;
            font-size: 1.08rem;
            color: #4A4A5A;
            line-height: 1.75;
        }

        .hero-stats {
            display: flex;
            justify-content: center;
            gap: 16px;
            flex-wrap: wrap;
            position: relative;
            z-index: 2;
        }

        .hstat {
            background: rgba(255, 255, 255, .82);
            backdrop-filter: blur(12px);
            border: 2px solid rgba(255, 255, 255, .9);
            border-radius: 22px;
            padding: 18px 26px;
            text-align: center;
            box-shadow: 0 8px 28px rgba(0, 0, 0, .07);
            animation: floatY 3s ease-in-out infinite;
            min-width: 130px;
        }

        .hstat:nth-child(2) {
            animation-delay: .5s;
        }

        .hstat:nth-child(3) {
            animation-delay: 1s;
        }

        .hstat:nth-child(4) {
            animation-delay: 1.5s;
        }

        .hstat-num {
            font-family: 'Fredoka One', cursive;
            font-size: 2rem;
            color: var(--pk);
            line-height: 1;
            margin-bottom: 4px;
        }

        .hstat-lbl {
            font-family: 'Nunito', sans-serif;
            font-weight: 800;
            font-size: .76rem;
            color: var(--dk);
            opacity: .75;
        }

        .origin-section {
            padding: 70px 5%;
            background: var(--cr);
            position: relative;
        }

        .origin-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 70px;
            align-items: stretch;
            margin: 0 auto;
        }

        .origin-visual {
            position: relative;
            display: flex;
            align-items: stretch;
        }

        .story-card-main {
            background: linear-gradient(145deg, var(--dk2), #2d0060);
            border-radius: 36px;
            padding: 44px 40px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 24px 60px rgba(13, 0, 32, .25);
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: 100%;
        }

        .story-card-main::before {
            content: '';
            position: absolute;
            right: -20px;
            bottom: -30px;
            font-size: 10rem;
            opacity: .05;
            pointer-events: none;
        }

        .story-card-main .eyebrow {
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: .68rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--ye);
            margin-bottom: 14px;
            display: block;
        }

        .story-card-main h2 {
            font-family: 'Fredoka One', cursive;
            font-size: 1.7rem;
            color: #fff;
            margin-bottom: 16px;
            line-height: 1.2;
        }

        .story-card-main p {
            font-size: .92rem;
            color: rgba(255, 255, 255, .65);
            line-height: 1.78;
            margin-bottom: 14px;
        }

        .story-pills {
            display: flex;
            gap: 9px;
            flex-wrap: wrap;
            margin-top: 22px;
        }

        .spill {
            background: rgba(255, 255, 255, .08);
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 50px;
            padding: 7px 15px;
            font-family: 'Nunito', sans-serif;
            font-weight: 800;
            font-size: .75rem;
            color: rgba(255, 255, 255, .82);
        }

        .float-accent {
            position: absolute;
            background: white;
            border-radius: 20px;
            padding: 14px 18px;
            box-shadow: 0 10px 32px rgba(0, 0, 0, .12);
            display: flex;
            align-items: center;
            gap: 10px;
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: .8rem;
            color: var(--dk);
            animation: floatY 4s ease-in-out infinite;
            white-space: nowrap;
            z-index: 5;
        }

        .fa-1 {
            top: -20px;
            right: -30px;
            animation-delay: 0s;
            border: 2px solid var(--pkl);
        }

        .fa-2 {
            bottom: -22px;
            left: -28px;
            animation-delay: 1.2s;
            border: 2px solid var(--skl);
        }

        /* Right column */
        .origin-text .sec-eye {
            font-family: 'Nunito', sans-serif;
            font-size: .88rem;
            font-weight: 900;
            letter-spacing: 2.2px;
            text-transform: uppercase;
            color: var(--pk);
            margin-bottom: 12px;
            display: block;
        }

        .origin-text h2 {
            font-family: 'Fredoka One', cursive;
            font-size: clamp(2rem, 3vw, 2.8rem);
            color: var(--dk);
            line-height: 1.12;
            margin-bottom: 18px;
        }

        .origin-text h2 .acc {
            color: var(--pu);
        }

        .origin-text p {
            font-size: .97rem;
            color: #555;
            line-height: 1.8;
            margin-bottom: 18px;
        }

        .problem-cards {
            display: flex;
            flex-direction: column;
            gap: 14px;
            margin-top: 28px;
        }

        .prob-card {
            background: white;
            border: 2.5px solid #f0f0f0;
            border-radius: 20px;
            padding: 18px 20px;
            display: flex;
            align-items: flex-start;
            gap: 15px;
            transition: all .3s;
        }

        .prob-card:hover {
            border-color: var(--pkl);
            transform: translateX(6px);
            box-shadow: 0 8px 24px rgba(255, 77, 143, .09);
        }

        .prob-icon img {
            height: 43px;
        }

        .prob-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
        }

        .prob-card h4 {
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: .95rem;
            color: var(--dk);
            margin-bottom: 4px;
        }

        .prob-card p {
            font-size: .82rem;
            color: #888;
            line-height: 1.55;
            margin-bottom: 0;
        }

        .origin {
            padding: 65px 6%;
            background: #fff
        }

        .origin-inner {
            margin: 0 auto
        }

        .sec-label {
            font-family: 'Nunito', sans-serif;
            font-size: 0.88rem;
            font-weight: 900;
            letter-spacing: 2.2px;
            text-transform: uppercase;
            color: var(--pk);
            margin-bottom: 12px;
            display: block;
        }

        .for-fix {
            font-family: 'Fredoka One', cursive;
            font-size: clamp(2rem, 3vw, 2.8rem);
            color: var(--dk);
            line-height: 1.12;
            margin-bottom: 18px;
        }

        .sec-label::before {
            content: '';
            display: block;
            width: 24px;
            height: 2px;
            background: var(--pk);
            border-radius: 2px
        }

        .origin-pair {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
            align-items: center;
            margin-bottom: 80px;
        }

        .origin-pair:last-child {
            margin-bottom: 0
        }

        .origin-pair.rev .op-img {
            order: 2
        }

        .origin-pair.rev .op-text {
            order: 1
        }

        /* Image side */
        .op-img {
            position: relative
        }

        .img-frame {
            border-radius: 36px;
            overflow: hidden;
            position: relative;
            aspect-ratio: 1/1.05;
        }

        .img-frame-inner {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8rem;
        }

        .img-frame-inner img {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;

        }

        .frame-1 {
            background: linear-gradient(145deg, #FFF0FA, #FFD6E8)
        }

        .frame-2 {
            background: linear-gradient(145deg, #EDE9FE, #DDD6FE)
        }

        .frame-3 {
            background: linear-gradient(145deg, #D0FFF2, #A7F3D0)
        }


        @media(max-width:640px) {
            .origin {
                padding: 60px 5%
            }
        }

        @media(max-width:1024px) {
            .origin-pair {
                grid-template-columns: 1fr;
                gap: 40px
            }

            .origin-pair.rev .op-img {
                order: 0
            }

            .origin-pair.rev .op-text {
                order: 0
            }
        }

        /* ── Discovery Section ── */
        .discovery-section {
            padding: 90px 5%;
            background: #f7def6;
            position: relative;
            overflow: hidden;
        }

        .paragraph {
            font-size: .97rem;
            color: #555;
            line-height: 1.8;
            margin-bottom: 18px;
        }

        .disc-header {
            max-width: 60%;
            margin-bottom: 52px;
        }

        .disc-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: #1a1a2e;
            color: #fff;
            border-radius: 50px;
            padding: 7px 16px;
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: .72rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 20px;
        }

        .disc-header h2 {
            font-family: 'Fredoka One', cursive;
            font-size: clamp(2rem, 4vw, 3rem);
            color: #1a1a2e;
            line-height: 1.08;
            margin-bottom: 16px;
        }

        .disc-header h2 .gap-accent {
            color: #FF6B6B;
        }

        .disc-header p {
            font-size: .95rem;
            color: #666;
            line-height: 1.75;

        }

        /* Split layout */
        .disc-body {
            display: grid;
            grid-template-columns: 360px 1fr;
            gap: 32px;
            align-items: start;
        }

        /* Left featured card */
        .disc-featured {
            border-radius: 28px;
            background: linear-gradient(145deg, #00C9A7 0%, #00B4D8 100%);
            padding: 36px 30px;
            color: #fff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0, 180, 216, .28);
            min-height: 420px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .disc-featured::before {
            content: '';
            position: absolute;
            top: -40px;
            right: -40px;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .08);
            pointer-events: none;
        }

        .disc-feat-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8px;
        }

        .disc-feat-num {
            font-family: 'Fredoka One', cursive;
            font-size: 5rem;
            line-height: 1;
            color: rgba(255, 255, 255, .9);
        }

        .disc-feat-icon img {
            height: 43px !important;
        }

        .disc-feat-icon {
            width: 66px;
            height: 61px;
            border-radius: 14px;
            background: rgba(255, 255, 255, .18);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }

        .disc-feat-title {
            font-family: 'Fredoka One', cursive;
            font-size: 1.55rem;
            line-height: 1.2;
            color: #fff;
            margin-bottom: 18px;
        }

        .disc-feat-label {
            font-family: 'Nunito', sans-serif;
            font-size: .65rem;
            font-weight: 900;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .65);
            margin-bottom: 6px;
            display: block;
        }

        .disc-feat-body {
            font-size: .85rem;
            color: rgba(255, 255, 255, .82);
            line-height: 1.65;
            margin-bottom: 14px;
        }

        .disc-feat-fix {
            background: rgba(255, 255, 255, .18);
            border-radius: 10px;
            padding: 10px 14px;
            font-size: .83rem;
            color: #fff;
            line-height: 1.6;
            margin-bottom: 18px;
        }

        .disc-feat-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, .22);
            border: 1px solid rgba(255, 255, 255, .35);
            border-radius: 50px;
            padding: 6px 14px;
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: .76rem;
            color: #fff;
        }

        .disc-feat-credit {
            font-size: .75rem;
            color: rgba(255, 255, 255, .55);
            margin-top: 14px;
            line-height: 1.5;
        }

        /* Right list */
        .disc-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .disc-item {
            background: #fff;
            border: 1.5px solid #EBEBEB;
            border-radius: 20px;
            padding: 20px 22px;
            display: flex;
            align-items: flex-start;
            gap: 18px;
            cursor: pointer;
            transition: all .3s cubic-bezier(.34, 1.3, .64, 1);
            position: relative;
        }

        .disc-item:hover,
        .disc-item.active {
            border-color: #1a1a2e;
            box-shadow: 0 8px 28px rgba(26, 26, 46, .1);
            transform: translateX(4px);
        }

        .disc-item-num {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Fredoka One', cursive;
            font-size: 1rem;
            color: #fff;
            flex-shrink: 0;
        }

        .disc-item-body {
            flex: 1;
        }

        .disc-item-head {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 5px;
        }

        .disc-item-title {
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: 1rem;
            color: #1a1a2e;
        }

        .disc-gap-tag {
            font-family: 'Nunito', sans-serif;
            font-size: .62rem;
            font-weight: 900;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 3px 9px;
            border-radius: 50px;
            background: #F0F0F0;
            color: #999;
        }

        .disc-item-desc {
            font-size: .84rem;
            color: #777;
            line-height: 1.6;
        }

        .disc-item-fix {
            font-size: .82rem;
            color: #1a1a2e;
            font-weight: 700;
            margin-top: 6px;
            display: none;
        }

        .disc-item.active .disc-item-fix {
            display: block;
        }

        .disc-item-arrow {
            font-size: 1rem;
            color: #ccc;
            flex-shrink: 0;
            align-self: center;
            transition: color .3s, transform .3s;
        }

        .disc-item:hover .disc-item-arrow,
        .disc-item.active .disc-item-arrow {
            color: #1a1a2e;
            transform: translateX(3px);
        }

        /* num colours */
        .disc-num-1 {
            background: #FF8C42;
        }

        .disc-num-2 {
            background: linear-gradient(135deg, #00C9A7, #00B4D8);
        }

        .disc-num-3 {
            background: #A855F7;
        }

        .disc-num-4 {
            background: #EC4899;
        }

        /* Responsive */
        @media (max-width: 960px) {
            .disc-body {
                grid-template-columns: 1fr;
            }

            .disc-featured {
                min-height: unset;
            }
        }

        @media (max-width: 640px) {
            .discovery-section {
                padding: 60px 5%;
            }

            .disc-feat-num {
                font-size: 3.5rem;
            }
        }

        .formula-section {
            padding: 90px 5%;
            background: linear-gradient(135deg, var(--dk2), #2d0060);
            position: relative;
            overflow: hidden;
        }

        .formula-section::before {
            content: '';
            position: absolute;
            top: -150px;
            right: -150px;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(124, 58, 237, .14), transparent 60%);
            border-radius: 50%;
            pointer-events: none;
        }

        .formula-section::after {
            content: '';
            position: absolute;
            bottom: -100px;
            left: -100px;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255, 77, 143, .1), transparent 60%);
            border-radius: 50%;
            pointer-events: none;
        }

        .formula-header {
            text-align: center;
            max-width: 680px;
            margin: 0 auto 60px;
            position: relative;
            z-index: 2;
        }

        .formula-header .sec-eye {
            font-family: 'Nunito', sans-serif;
            font-size: .88rem;
            font-weight: 900;
            letter-spacing: 2.2px;
            text-transform: uppercase;
            color: var(--ye);
            margin-bottom: 12px;
            display: block;
        }

        .formula-header h2 {
            font-family: 'Fredoka One', cursive;
            font-size: clamp(2rem, 3vw, 2.8rem);
            color: #fff;
            line-height: 1.12;
            margin-bottom: 14px;
        }

        .formula-header h2 .acc {
            color: var(--ye);
        }

        .formula-header p {
            font-size: .97rem;
            color: rgba(255, 255, 255, .55);
            line-height: 1.75;
        }

        .formula-eq {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            flex-wrap: wrap;

            margin: 0 auto 60px;
            position: relative;
            z-index: 2;
        }

        .feq-item {
            background: rgba(255, 255, 255, .06);
            border: 1.5px solid rgba(255, 255, 255, .12);
            border-radius: 24px;
            padding: 26px 20px;
            text-align: center;
            transition: all .3s;
            flex: 1;
            min-width: 160px;
          
        }

        .feq-item:hover {
            background: rgba(255, 255, 255, .11);
            transform: translateY(-6px);
        }

        .feq-icon {
            font-size: 2.4rem;
            margin-bottom: 16px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 60px;
        }

        .feq-icon img {
            width: 150px;
            height: 140px;

            object-fit: cover;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }

        .feq-title {
            font-family: 'Fredoka One', cursive;
            font-size: 1rem;
            color: #fff;
            margin-bottom: 5px;
        }

        .feq-sub {
            font-size: .76rem;
            color: rgba(255, 255, 255, .45);
            line-height: 1.55;
        }

        .feq-op {
            font-family: 'Fredoka One', cursive;
            font-size: 2rem;
            color: var(--ye);
            flex-shrink: 0;
        }

        .formula-result {
            /* margin: 0 auto;
                background: linear-gradient(135deg, rgba(255, 77, 143, .15), rgba(255, 214, 0, .1));
                border: 2px solid rgba(255, 214, 0, .25);
                border-radius: 28px;
                padding: 36px;

                position: relative;
                z-index: 2;
                box-shadow: 0 0 60px rgba(255, 77, 143, .15); */
            text-align: center;
        }

        .formula-result h3 {
            font-family: 'Fredoka One', cursive;
            font-size: 1.6rem;
            color: #fff;
            margin-bottom: 10px;
        }

        .formula-result p {
            font-size: .92rem;
            color: rgba(255, 255, 255, .6);
            line-height: 1.7;
        }

        .closing-section {
            padding: 36px 5%;
            background: linear-gradient(145deg, #FFF0FA 0%, #F0E5FF 50%, #FFDCF0 100%);
            position: relative;
            overflow: hidden;
            text-align: center;
        }

        .closing-section::before {
            content: '';
            position: absolute;
            width: 520px;
            height: 520px;
            border-radius: 62% 38% 56% 44%/48% 62% 38% 52%;
            background: radial-gradient(circle, rgba(255, 77, 143, .09), transparent 70%);
            top: -140px;
            right: -100px;
            animation: blobMorph 11s ease-in-out infinite;
            pointer-events: none;
        }

        .closing-inner {
            max-width: 820px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }

        .closing-emoji {
            font-size: 4rem;
            margin-bottom: 20px;
            display: block;
        }

        .closing-inner h2 {
            font-family: 'Fredoka One', cursive;
            font-size: clamp(2.2rem, 4vw, 3.4rem);
            color: var(--dk);
            line-height: 1.12;
            margin-bottom: 20px;
        }

        .closing-inner h2 .pop {
            color: var(--pu);
            position: relative;
            display: inline-block;
        }


        .closing-inner>p {
            font-size: 1.02rem;
            color: #4A4A5A;
            line-height: 1.8;
            max-width: 600px;
            margin: 0 auto 36px;
        }

        .closing-btns {
            display: flex;
            gap: 14px;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 44px;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            background: var(--btn);
            color: #fff;
            border: none;
            border-radius: 50px;
            padding: 16px 34px;
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: .98rem;
            cursor: pointer;
            text-decoration: none;
            transition: all .3s;
            box-shadow: 0 10px 30px rgba(255, 77, 143, .38);
        }

        .btn-primary:hover {
            transform: translateY(-4px) scale(1.04);
            box-shadow: 0 18px 40px rgba(255, 77, 143, .52);
        }

        .btn-ghost {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            background: transparent;
            color: var(--dk);
            border: 2.5px solid rgba(13, 0, 32, .2);
            border-radius: 50px;
            padding: 16px 34px;
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: .98rem;
            cursor: pointer;
            text-decoration: none;
            transition: all .3s;
        }

        .btn-ghost:hover {
            background: var(--btn);
            color: #fff;
            transform: translateY(-3px);
        }

        .trust-row {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .trust-pill {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, .8);
            backdrop-filter: blur(10px);
            border: 1.5px solid rgba(255, 255, 255, .9);
            border-radius: 50px;
            padding: 9px 18px;
            font-family: 'Nunito', sans-serif;
            font-weight: 800;
            font-size: .8rem;
            color: var(--dk);
            box-shadow: 0 4px 14px rgba(0, 0, 0, .07);
            animation: floatY 3s ease-in-out infinite;
        }

        .trust-pill:nth-child(2) {
            animation-delay: .6s;
        }

        .trust-pill:nth-child(3) {
            animation-delay: 1.2s;
        }

        .trust-pill:nth-child(4) {
            animation-delay: 1.8s;
        }

        .reveal {
            opacity: 0;
            transform: translateY(40px);
            transition: opacity .7s cubic-bezier(.34, 1.1, .64, 1), transform .7s cubic-bezier(.34, 1.1, .64, 1);
        }

        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .reveal-d1 {
            transition-delay: .1s;
        }

        .reveal-d2 {
            transition-delay: .2s;
        }

        .reveal-d3 {
            transition-delay: .3s;
        }

        .reveal-d4 {
            transition-delay: .4s;
        }

        @keyframes blobMorph {

            0%,
            100% {
                border-radius: 62% 38% 56% 44%/48% 62% 38% 52%;
            }

            50% {
                border-radius: 38% 62% 44% 56%/62% 38% 55% 45%;
            }
        }

        @keyframes floatY {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-9px);
            }
        }

        @keyframes badgePulse {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(255, 77, 143, 0);
            }

            50% {
                box-shadow: 0 0 0 7px rgba(255, 77, 143, .1);
            }
        }

        @media (max-width: 900px) {
            .origin-grid {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            .origin-visual {
                order: -1;
            }

            .fa-1 {
                top: -16px;
                right: -10px;
            }

            .fa-2 {
                bottom: -16px;
                left: -10px;
            }

            .formula-eq {
                gap: 10px;
            }

            .feq-op {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 640px) {
            .about-hero {
                padding: 80px 5% 60px;
            }

            .hero-stats {
                gap: 10px;
            }

            .hstat {
                min-width: 100px;
                padding: 14px 18px;
            }

            .hstat-num {
                font-size: 1.6rem;
            }

            .origin-section,
            .discovery-section,
            .formula-section,
            .why-section,
            .closing-section {
                padding: 60px 5%;
            }

            .story-card-main {
                padding: 30px 24px;
            }

            .fa-1,
            .fa-2 {
                display: none;
            }

            .formula-eq {
                flex-direction: column;
                align-items: center;
            }

            .feq-op {
                transform: rotate(90deg);
            }

            .feq-item {
                max-width: 100%;
                width: 100%;
            }

            .closing-btns {
                flex-direction: column;
                align-items: center;
            }

            .btn-primary,
            .btn-ghost {
                width: 100%;
                max-width: 320px;
                justify-content: center;
            }
        }

        @media (max-width: 400px) {
            .about-hero h1 {
                font-size: 2.2rem;
            }

            .hstat {
                min-width: 80px;
            }

            .disc-item,
            .wc {
                padding: 18px 16px;
            }
        }

        .stat-boxes {
            display: flex;
              gap: 12px;
            margin-top: 25px;
        }

        .stat-box {
            background: white;
            border: 2px solid #f0f0f0;
            border-radius: 16px;
            padding: 20px 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            flex: 1;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        }

        .stat-box:hover {
            transform: translateY(-5px);
            border-color: var(--pkl);
            box-shadow: 0 10px 25px rgba(255, 77, 143, 0.1);
        }

        .stat-icon img {
            height: 43px;
        }

        .stat-icon {
            width: 54px;
            height: 54px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 12px;
        }

        .pu-bg {
            background: var(--pul);
        }

        .mn-bg {
            background: var(--mnl);
        }

        .stat-text {
            font-family: 'Nunito', sans-serif;
            font-size: 0.9rem;
            color: #777;
            line-height: 1.4;
        }

        .stat-text strong {
            font-family: 'Fredoka One', cursive;
            font-size: 1.4rem;
            color: var(--dk);
            display: block;
            margin-bottom: 4px;
            font-weight: normal;
        }
    </style>
@endpush

@section('content')
    <!-- ══════════════════════════════════════════
                             HERO
                        ══════════════════════════════════════════ -->
    <section class="about-hero">
        <div class="hero-badge"> Our Story</div>

        <h1>
            We Didn't Find a <span class="pop">Perfect</span><br>
            Product — So We <span style="color:var(--pu)">Built One</span>
        </h1>

        <p class="hero-desc">
            NutriBuddy was born from a real parent's frustration, a doctor's incomplete answers,
            and a child who kept falling sick. Today, we help 50,000+ families across India give
            their kids the nutrition they actually need.
        </p>

        <div class="hero-stats">
            <div class="hstat">
                <div class="hstat-num">50K+</div>
                <div class="hstat-lbl">Happy Families</div>
            </div>
            <div class="hstat">
                <div class="hstat-num">12+</div>
                <div class="hstat-lbl">Expert Formulas</div>
            </div>
            <div class="hstat">
                <div class="hstat-num">100%</div>
                <div class="hstat-lbl">Natural Ingredients</div>
            </div>
            <div class="hstat">
                <div class="hstat-num">4.9★</div>
                <div class="hstat-lbl">Parent Rating</div>
            </div>
        </div>
    </section>

    <section class="origin">
        <div class="origin-inner">

            <!-- Pair 2 (reversed) -->
            <div class="origin-pair rev reveal">
                <div class="op-img">
                    <div class="img-frame frame-2">
                        <div class="img-frame-inner"
                            style="background-image: url('img/girl.webp'); background-size: cover; background-position: center; ">
                        </div>
                    </div>

                </div>
                <div class="op-text">
                    <div class="sec-label">The Research</div>
                    <h2 class="for-fix">The Problem Was <span class="acc2">Hiding</span><br>in Plain Sight</h2>
                    <p class="paragraph">
                        Quick medicine could fix a symptom, but it couldn't fix the root — a child's body wasn't
                        getting the right nutrients consistently. Most parents face this daily but don't know where to turn.
                    </p>
                    <p class="paragraph">
                        We spoke to 200+ pediatricians and 1,000+ parents across India. The finding was unanimous:
                        the gap between what children need and what's available is enormous — and the market wasn't
                        doing anything about it.
                    </p>
                    <div class="stat-boxes">
                        <div class="stat-box">
                            <div class="stat-icon pu-bg"><img src="img/pediatrician.png" alt=""></div>
                            <div class="stat-text"><strong>200+</strong>Pediatricians</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-icon mn-bg"><img src="img/family.png" alt=""></div>
                            <div class="stat-text"><strong>1,000+</strong>Parents</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-icon mn-bg"><img src="img/rating.png" alt=""></div>
                            <div class="stat-text"><strong>1,000+</strong>Customer</div>
                        </div>

                    </div>
                </div>
            </div>



        </div>
    </section>
    <!-- ══════════════════════════════════════════
                             ORIGIN STORY
                        ══════════════════════════════════════════ -->
    <section class="origin-section">
        <div class="origin-grid">

            <!-- LEFT: Dark story card -->
            <div class="origin-visual reveal">
                <div class="story-card-main">
                    <span class="eyebrow">The Beginning</span>
                    <h2>A Mom. A Sick Child. A Doctor's Frustrating Answer.</h2>
                    <p>
                        My newborn used to get sick very often — city life, poor immunity, changing
                        weather, and unhealthy food habits took a toll. His paediatrician suggested
                        nutrition supplements to fill the gaps.
                    </p>
                    <p>
                        But what we found in the market was shocking: fancy packaging, poor ingredients,
                        hidden sugars, and zero transparency. None of it felt right for our child.
                    </p>
                    <div class="story-pills">
                        <div class="spill">Real Parent Story</div>
                        <div class="spill">Doctor's Advice</div>
                        <div class="spill">A Better Way</div>
                    </div>
                </div>

                <div class="float-accent fa-1">
                    Pediatrician Approved
                </div>
                <div class="float-accent fa-2">
                    Zero Hidden Sugars
                </div>
            </div>

            <!-- RIGHT: Text content -->
            <div class="origin-text reveal reveal-d1">
                <span class="sec-eye">Why We Started</span>
                <h2>The Problem Was <span class="acc">Hiding</span> in Plain Sight</h2>
                <p>
                    Quick medicine could fix a symptom, but it couldn't fix the root — a child's body
                    wasn't getting the right nutrients consistently. Most parents face this daily but
                    don't know where to turn.
                </p>


                <div class="problem-cards">
                    <div class="prob-card">
                        <div class="prob-icon" style="background:var(--pkl)"><img src="img/dermal.png" alt=""></div>
                        <div>
                            <h4>Products Full of Fillers</h4>
                            <p>Most children's supplements are 70% filler, sugar, and artificial colour. Parents deserve
                                better
                                transparency.</p>
                        </div>
                    </div>
                    <div class="prob-card">
                        <div class="prob-icon" style="background:var(--skl)"><img src="img/health.png" alt=""></div>
                        <div>
                            <h4>Incomplete Medical Guidance</h4>
                            <p>Doctors advise supplements but rarely guide parents to safe, high-quality options. The gap is
                                real.</p>
                        </div>
                    </div>
                    <div class="prob-card">
                        <div class="prob-icon" style="background:var(--mnl)"><img src="img/planet-earth.png" alt=""></div>
                        <div>
                            <h4>Nature vs. Chemistry</h4>
                            <p>Ayurveda and modern science don't have to compete — combined thoughtfully, they're the most
                                powerful
                                pair.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>




    <!-- ══════════════════════════════════════════
                             THE DISCOVERY SECTION
                        ══════════════════════════════════════════ -->
    <section class="discovery-section">

        <!-- Header -->
        <div class="disc-header reveal">
            <div class="disc-badge">✦ The Discovery</div>
            <h2>The gap we found — and fixed.</h2>
            <p>
                After months of research, lab testing, and consulting paediatricians and
                Ayurvedic practitioners, we identified four critical gaps in children's
                nutrition products available in India.
            </p>
        </div>

        <!-- Body: featured card + list -->
        <div class="disc-body">

            <!-- LEFT: Featured rotating card -->
            <div class="disc-featured reveal" id="discFeatured">
                <div>
                    <div class="disc-feat-top">
                        <div class="disc-feat-num" id="featNum">01</div>
                        <div class="disc-feat-icon"><img src="img/analysis.png" alt=""></div>
                    </div>
                    <div class="disc-feat-title" id="featTitle">The Hidden Sugar Trap</div>

                    <span class="disc-feat-label">The Problem</span>
                    <p class="disc-feat-body" id="featProblem">
                        Most kids' nutrition drinks pack 8–14g of sugar per serving —
                        disguised as flavour.
                    </p>

                    <span class="disc-feat-label">Our Fix</span>
                    <div class="disc-feat-fix" id="featFix">
                        We use monk fruit + stevia at safe levels, giving great taste
                        with zero added sugar.
                    </div>

                    <div class="disc-feat-badge" id="featBadge">
                        🍃 Zero added sugar
                    </div>
                </div>
                <p class="disc-feat-credit">Formulated with paediatricians and Ayurvedic practitioners across India.</p>
            </div>

            <!-- RIGHT: Gap list -->
            <div class="disc-list">

                <div class="disc-item reveal reveal-d1 active" data-index="0" data-num="01"
                    data-title="The Hidden Sugar Trap" data-tag="GAP #01"
                    data-desc="Most kids' nutrition drinks pack 8–14g of sugar per serving — disguised as flavour."
                    data-fix="We use monk fruit + stevia at safe levels — zero added sugar.">
                    <div class="disc-item-num disc-num-1">01</div>
                    <div class="disc-item-body">
                        <div class="disc-item-head">
                            <span class="disc-item-title">The Hidden Sugar Trap</span>
                            <span class="disc-gap-tag">GAP #01</span>
                        </div>
                        <p class="disc-item-desc">Most kids' nutrition drinks pack 8–14g of sugar per serving — disguised as
                            flavour.</p>
                        <p class="disc-item-fix">— We use monk fruit + stevia at safe levels — zero added sugar.</p>
                    </div>
                    <span class="disc-item-arrow">→</span>
                </div>

                <div class="disc-item reveal reveal-d2" data-index="1" data-num="02"
                    data-title="Nutrients That Don't Absorb" data-tag="GAP #02"
                    data-desc="Iron and calcium on the label rarely reach the bloodstream ."
                    data-fix="— Chelated minerals + Ayurvedic carriers like ghee and pippali .">
                    <div class="disc-item-num disc-num-2">02</div>
                    <div class="disc-item-body">
                        <div class="disc-item-head">
                            <span class="disc-item-title">Nutrients That Don't Absorb</span>
                            <span class="disc-gap-tag">GAP #02</span>
                        </div>
                        <p class="disc-item-desc">Iron and calcium on the label rarely reach the bloodstream.</p>
                        <p class="disc-item-fix">— Chelated minerals + Ayurvedic carriers like ghee
                            .</p>
                    </div>
                    <span class="disc-item-arrow">→</span>
                </div>

                <div class="disc-item reveal reveal-d3" data-index="2" data-num="03" data-title="The Ingredient Maze"
                    data-tag="GAP #03"
                    data-desc="Long lists of artificial colors, preservatives and 'natural identical' flavors hide in fine print."
                    data-fix="— We list every ingredient in plain language — no jargon, no surprises.">
                    <div class="disc-item-num disc-num-3">03</div>
                    <div class="disc-item-body">
                        <div class="disc-item-head">
                            <span class="disc-item-title">The Ingredient Maze</span>
                            <span class="disc-gap-tag">GAP #03</span>
                        </div>
                        <p class="disc-item-desc">Long lists of artificial colors, preservatives and 'natural identical'
                            flavors hide in fine print.</p>
                        <p class="disc-item-fix">— We list every ingredient in plain language — no jargon, no surprises.</p>
                    </div>
                    <span class="disc-item-arrow">→</span>
                </div>

                <div class="disc-item reveal reveal-d4" data-index="3" data-num="04" data-title="Built For Indian Kids"
                    data-tag="GAP #04"
                    data-desc="Global brands are formulated for Western diets — not dal-chawal, not Indian climates, not our gut flora."
                    data-fix="— Our formulas are co-developed with Indian paediatricians for Indian bodies.">
                    <div class="disc-item-num disc-num-4">04</div>
                    <div class="disc-item-body">
                        <div class="disc-item-head">
                            <span class="disc-item-title">Built For Indian Kids</span>
                            <span class="disc-gap-tag">GAP #04</span>
                        </div>
                        <p class="disc-item-desc">Global brands are formulated for Western diets — not dal-chawal, not
                            Indian climates, not our gut flora.</p>
                        <p class="disc-item-fix">— Our formulas are co-developed with Indian paediatricians for Indian
                            bodies.</p>
                    </div>
                    <span class="disc-item-arrow">→</span>
                </div>

            </div><!-- /disc-list -->
        </div><!-- /disc-body -->

    </section>


    <!-- ══════════════════════════════════════════
                             OUR FORMULA
                        ══════════════════════════════════════════ -->
    <section class="formula-section">

        <div class="formula-header reveal">
            <span class="sec-eye">Our Approach</span>
            <h2>How We Build <span class="acc">Every Product</span></h2>
            <p>
                Every NutriBuddy formula is born from a simple but powerful equation that took us
                two years to perfect.
            </p>
        </div>
        <!-- hide part -->
        <div class="formula-eq reveal reveal-d1">
            <div class="feq-item">
                <span class="feq-icon"><img src="img/rishi.jpg" alt=""></span>
                <div class="feq-title">Ayurvedic Wisdom</div>
                <div class="feq-sub">5,000 years of plant-based healing, distilled</div>
            </div>

            <div class="feq-op">+</div>

            <div class="feq-item">
                <span class="feq-icon"><img src="img/labs.jpeg" alt=""></span>
                <div class="feq-title">Modern Science</div>
                <div class="feq-sub">Clinical validation & optimal bioavailability</div>
            </div>

            <div class="feq-op">+</div>

            <div class="feq-item">
                <span class="feq-icon"><img src="img/cidss.png" alt=""></span>
                <div class="feq-title">Kid-Approved Taste</div>
                <div class="feq-sub">8 months of flavour trials with real children</div>
            </div>

            <div class="feq-op">=</div>

            <div class="feq-item" style="background:rgba(255,214,0,.1);border-color:rgba(255,214,0,.3)">
                <span class="feq-icon"><img src="img/gummies.jpeg" alt=""></span>
                <div class="feq-title" style="color:var(--ye)">NutriBuddy</div>
                <div class="feq-sub">Products your child will love and thrive on</div>
            </div>
        </div>
        <!-- hide -->

        <div class="formula-result reveal reveal-d2">
            <h3>The Result? Products kids ask for by name.</h3>
            <p>
                When a child says "Mama, where's my gummy?" — that's when we know we've done our job right.
                Nutrition that works only works if it's taken consistently. That's the NutriBuddy promise.
            </p>
        </div>

    </section>

    <!-- ══════════════════════════════════════════
                             CLOSING CTA
                        ══════════════════════════════════════════ -->
    <section class="closing-section">
        <div class="closing-inner reveal">
            <!-- <span class="closing-emoji"></span> -->

            <h2>
                Because Your Child Deserves<br>
                the <span class="pop">Very Best</span>
            </h2>

            <p>
                Every parent wants the same thing — a healthy, happy, thriving child. NutriBuddy exists
                to make that easier, more affordable, and backed by real science. This is more than a
                product. It's our promise to you and your family.
            </p>

            <div class="closing-btns">
                <a href="#" class="btn-primary"> Shop Our Products</a>
                <a href="#" class="btn-ghost"> Get Free Diet Chart</a>
            </div>

            <div class="trust-row">
                <div class="trust-pill">FSSAI Certified</div>
                <div class="trust-pill">Paediatrician Approved</div>
                <div class="trust-pill">100% Natural</div>
                <div class="trust-pill">🇮🇳 Made for India</div>
            </div>
        </div>
    </section>
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
@endsection
@push('scripts')
    <script>
        // Scroll Reveal
        const revObs = new IntersectionObserver(entries => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    e.target.classList.add('visible');
                    revObs.unobserve(e.target);
                }
            });
        }, {
            threshold: 0.1
        });
        document.querySelectorAll('.reveal').forEach(r => revObs.observe(r));

        // Counter animation
        const countObs = new IntersectionObserver(entries => {
            entries.forEach(e => {
                if (!e.isIntersecting) return;
                const el = e.target;
                const raw = el.textContent;
                const hasK = raw.includes('K');
                const hasStar = raw.includes('★');
                const hasPct = raw.includes('%');
                const num = parseFloat(raw.replace(/[^0-9.]/g, ''));
                let start = 0;
                const dur = 1600,
                    steps = 60,
                    inc = num / steps;
                const iv = setInterval(() => {
                    start = Math.min(start + inc, num);
                    let display = Number.isInteger(num) ? Math.round(start) : start.toFixed(1);
                    if (hasK) display += 'K+';
                    else if (hasStar) display += '★';
                    else if (hasPct) display += '%';
                    else display += '+';
                    el.textContent = display;
                    if (start >= num) clearInterval(iv);
                }, dur / steps);
                countObs.unobserve(el);
            });
        }, {
            threshold: 0.5
        });
        document.querySelectorAll('.hstat-num').forEach(el => countObs.observe(el));

        // Discovery section – interactive featured card
        (function () {
            const items = document.querySelectorAll('.disc-item');
            const featNum = document.getElementById('featNum');
            const featTitle = document.getElementById('featTitle');
            const featProblem = document.getElementById('featProblem');
            const featFix = document.getElementById('featFix');
            const featBadge = document.getElementById('featBadge');

            const badges = [
                ' Zero added sugar',
                ' 3× absorption',
                ' Full transparency',
                '🇮🇳 Made for India'
            ];

            if (!featNum) return; // safety guard

            items.forEach(item => {
                item.addEventListener('click', () => {
                    items.forEach(i => i.classList.remove('active'));
                    item.classList.add('active');

                    const idx = parseInt(item.dataset.index);
                    featNum.textContent = item.dataset.num;
                    featTitle.textContent = item.dataset.title;
                    featProblem.textContent = item.dataset.desc;
                    featFix.textContent = item.dataset.fix;
                    featBadge.textContent = badges[idx] || badges[0];

                    // Smooth fade transition on the featured card
                    const card = document.getElementById('discFeatured');
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(8px)';
                    setTimeout(() => {
                        card.style.transition = 'opacity .35s ease, transform .35s ease';
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 60);
                });
            });
        })();
    </script>
@endpush