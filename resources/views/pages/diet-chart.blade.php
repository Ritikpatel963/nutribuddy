@extends('layouts.main')
@section('title', 'NutriBuddy — Personalized Diet Chart')
@push('styles')
    <style>

        /* ══ LAYOUT ══ */
        .diet-section {
            max-width: 780px;
            margin: 0 auto;
            padding: 48px 20px 80px;
        }

        /* ══ HEADER ══ */
        .diet-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .sec-eye {
            display: inline-block;
            /* background: var(--mnl); */
            color: #00a870;
            border-radius: 50px;
            padding: 6px 18px;
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: .75rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 14px;
        }

        .sec-title {
            font-family: 'Fredoka One', cursive;
            font-size: clamp(1.8rem, 4vw, 2.6rem);
            color: var(--dk);
            line-height: 1.2;
            margin-bottom: 12px;
        }

        .acc {
            color: var(--pk);
        }

        .sec-sub {
            font-size: .95rem;
            color: #888;
            line-height: 1.7;
            max-width: 520px;
            margin: 0 auto;
        }

        /* ══ STEPPER ══ */
        .stepper-wrap {
            width: 100%;
        }

        .stepper-progress {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 28px;
            gap: 0;
            overflow-x: auto;
            padding: 4px 0;
        }

        .sp-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
        }

        .sp-ball {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #f0eaf8;
            color: #bbb;
            font-family: 'Fredoka One', cursive;
            font-size: .95rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all .3s;
        }

        .sp-step.active .sp-ball {
            background: var(--pk);
            color: #fff;
            box-shadow: 0 4px 14px rgba(255, 77, 143, .35);
        }

        .sp-step.done .sp-ball {
            background: var(--mn);
            color: #fff;
        }

        .sp-label {
            font-family: 'Nunito', sans-serif;
            font-weight: 800;
            font-size: .68rem;
            color: #bbb;
            text-align: center;
            white-space: nowrap;
        }

        .sp-step.active .sp-label {
            color: var(--pk);
        }

        .sp-step.done .sp-label {
            color: var(--mn);
        }

        .sp-line {
            flex: 1;
            height: 3px;
            background: #f0eaf8;
            border-radius: 4px;
            min-width: 20px;
            max-width: 70px;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .sp-line-fill {
            height: 100%;
            width: 0%;
            background: var(--mn);
            border-radius: 4px;
            transition: width .5s ease;
        }

        .sp-line-fill.done {
            width: 100%;
        }

        /* ══ DIET CARD ══ */
        .diet-card {
            background: var(--card);
            border-radius: 32px;
            padding: 36px 32px;
            box-shadow: var(--shadow);
            border: 1.5px solid var(--border);
        }

        /* ══ STEP PANELS ══ */
        .step-panel {
            display: none;
        }

        .step-panel.active,
        .loading-state.active,
        .result-state.active {
            display: block;
        }

        .step-head {
            text-align: center;
            margin-bottom: 28px;
        }

        .step-emoji {
            font-size: 2.4rem;
            display: block;
            margin-bottom: 10px;
        }

        .step-head h3 {
            font-family: 'Fredoka One', cursive;
            font-size: 1.4rem;
            color: var(--dk);
            margin-bottom: 8px;
        }

        .step-head p {
            font-size: .9rem;
            color: #888;
            line-height: 1.6;
        }

        /* Name input */
        .name-input-wrap {
            margin-bottom: 28px;
        }

        .name-input-label {
            font-family: 'Fredoka One', cursive;
            font-size: 1rem;
            color: var(--dk);
            margin-bottom: 10px;
            display: block;
        }

        .name-input-field {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #f0f0f0;
            border-radius: 16px;
            font-family: 'DM Sans', sans-serif;
            font-size: 1rem;
            color: var(--dk);
            background: #fafafa;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }

        .name-input-field:focus {
            border-color: var(--pk);
            box-shadow: 0 0 0 3px rgba(255, 77, 143, .1);
            background: #fff;
        }

        .name-input-field::placeholder {
            color: #bbb;
        }

        /* ══ AGE GRID ══ */
        .age-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
        }

        .age-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
            padding: 14px 8px;
            border: 2px solid #f0eaf8;
            border-radius: 18px;
            cursor: pointer;
            transition: all .25s;
            background: #fafafa;
            text-align: center;
        }

        .age-card:hover {
            border-color: var(--pkl);
            background: #fff;
            transform: translateY(-2px);
        }

        .age-card.selected {
            border-color: var(--pk);
            background: var(--pkl);
            box-shadow: 0 4px 14px rgba(255, 77, 143, .18);
        }

        .age-emoji {
            font-size: 1.5rem;
        }

        .age-range {
            font-family: 'Fredoka One', cursive;
            font-size: .75rem;
            color: var(--dk);
        }

        .age-label {
            font-size: .62rem;
            color: #aaa;
            font-family: 'Nunito', sans-serif;
            font-weight: 700;
        }

        .age-card.selected .age-label {
            color: var(--pk);
        }

        /* ══ GENDER ROW ══ */
        .gender-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }

        .gender-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            padding: 18px 12px;
            border: 2px solid #f0eaf8;
            border-radius: 18px;
            cursor: pointer;
            transition: all .25s;
            background: #fafafa;
        }

        .gender-card:hover {
            border-color: var(--pkl);
            background: #fff;
            transform: translateY(-2px);
        }

        .gender-card.selected {
            border-color: var(--pk);
            background: var(--pkl);
            box-shadow: 0 4px 14px rgba(255, 77, 143, .18);
        }

        .gender-emoji {
            font-size: 1.8rem;
        }

        .gender-name {
            font-family: 'Fredoka One', cursive;
            font-size: .85rem;
            color: var(--dk);
        }

        /* ══ HW ROW ══ */
        .hw-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 24px;
        }

        .hw-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .hw-group label {
            font-family: 'Fredoka One', cursive;
            font-size: .9rem;
            color: var(--dk);
        }

        .hw-group input {
            padding: 12px 16px;
            border: 2px solid #f0f0f0;
            border-radius: 14px;
            font-family: 'DM Sans', sans-serif;
            font-size: .95rem;
            color: var(--dk);
            background: #fafafa;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
            width: 100%;
        }

        .hw-group input:focus {
            border-color: var(--pk);
            box-shadow: 0 0 0 3px rgba(255, 77, 143, .1);
            background: #fff;
        }

        .hw-group input::placeholder {
            color: #bbb;
        }

        /* ══ PROBLEMS ══ */
        .problem-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        .prob-tag {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 10px 16px;
            border: 2px solid #f0eaf8;
            border-radius: 50px;
            font-family: 'Nunito', sans-serif;
            font-weight: 800;
            font-size: .82rem;
            cursor: pointer;
            transition: all .2s;
            background: #fafafa;
            color: var(--dk);
        }

        .prob-tag:hover {
            border-color: var(--pk);
            background: var(--pkl);
        }

        .prob-tag.selected {
            border-color: var(--pk);
            background: var(--pkl);
            color: var(--pk);
        }

        .prob-icon {
            font-size: 1rem;
        }

        /* ══ DIET PREF ══ */
        .diet-pref-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 12px;
        }

        .dpref {
            padding: 14px 10px;
            border: 2px solid #f0eaf8;
            border-radius: 16px;
            font-family: 'Nunito', sans-serif;
            font-weight: 800;
            font-size: .8rem;
            cursor: pointer;
            text-align: center;
            transition: all .2s;
            background: #fafafa;
            color: var(--dk);
        }

        .dpref:hover {
            border-color: var(--pk);
            background: var(--pkl);
        }

        .dpref.selected {
            border-color: var(--pk);
            background: var(--pkl);
            color: var(--pk);
        }

        /* ══ ALLERGIES ══ */
        .allergy-row {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .atag {
            padding: 8px 16px;
            border: 2px solid #f0eaf8;
            border-radius: 50px;
            font-family: 'Nunito', sans-serif;
            font-weight: 800;
            font-size: .78rem;
            cursor: pointer;
            transition: all .2s;
            background: #fafafa;
            color: var(--dk);
        }

        .atag:hover {
            border-color: var(--sk);
            background: #e6f9ff;
        }

        .atag.selected {
            border-color: var(--sk);
            background: #e6f9ff;
            color: #0088bb;
        }

        /* ══ ERROR ══ */
        .selection-error {
            display: none;
            color: #e53e3e;
            font-size: .8rem;
            font-family: 'Nunito', sans-serif;
            font-weight: 800;
            margin-top: 8px;
        }

        .selection-error.show {
            display: block;
        }

        /* ══ NAV BUTTONS ══ */
        .step-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 32px;
            gap: 12px;
        }

        .btn-next,
        .btn-generate {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, var(--pk), var(--pkd));
            color: #fff;
            border: none;
            border-radius: 50px;
            padding: 14px 28px;
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: .92rem;
            cursor: pointer;
            transition: all .3s;
            box-shadow: 0 6px 20px rgba(255, 77, 143, .3);
            white-space: nowrap;
        }

        .btn-next:hover,
        .btn-generate:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px rgba(255, 77, 143, .4);
        }

        .btn-back {
            background: none;
            border: 2px solid #f0eaf8;
            border-radius: 50px;
            padding: 12px 22px;
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: .88rem;
            color: #aaa;
            cursor: pointer;
            transition: all .2s;
            white-space: nowrap;
        }

        .btn-back:hover {
            border-color: #ddd;
            color: var(--dk);
        }

        .btn-restart {
            background: none;
            border: 2px solid #f0eaf8;
            border-radius: 50px;
            padding: 12px 22px;
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: .88rem;
            color: #aaa;
            cursor: pointer;
            transition: all .2s;
        }

        .btn-restart:hover {
            border-color: var(--pk);
            color: var(--pk);
        }

        /* ══ LOADING ══ */
        .loading-state {
            display: none;
            text-align: center;
            padding: 48px 24px;
        }

        .loading-state.active {
            display: block;
        }

        .loader-ring {
            width: 64px;
            height: 64px;
            border: 5px solid #f0eaf8;
            border-top-color: var(--pk);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 24px;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .loading-state h3 {
            font-family: 'Fredoka One', cursive;
            font-size: 1.4rem;
            color: var(--dk);
            margin-bottom: 8px;
        }

        .loading-state p {
            font-size: .9rem;
            color: #888;
            margin-bottom: 20px;
        }

        .loading-facts {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }

        .lfact {
            background: var(--pul);
            color: var(--pu);
            border-radius: 50px;
            padding: 7px 16px;
            font-family: 'Nunito', sans-serif;
            font-weight: 800;
            font-size: .78rem;
        }

        /* ══ RESULT ══ */
        .result-state {
            display: none;
        }

        .result-state.active {
            display: block;
        }

        .result-hero {
            background: linear-gradient(135deg, var(--dk2), #4a0090);
            border-radius: 28px;
            padding: 32px;
            margin-bottom: 28px;
            color: #fff;
            position: relative;
            overflow: hidden;
        }

        .result-hero::before {
            content: '';
            position: absolute;
            right: -10px;
            bottom: -20px;
            font-size: 8rem;
            opacity: .06;
            pointer-events: none;
        }

        .result-badge {
            display: inline-block;
            background: rgba(255, 214, 0, .18);
            border: 1px solid rgba(255, 214, 0, .3);
            color: var(--ye);
            border-radius: 50px;
            padding: 5px 16px;
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: .72rem;
            letter-spacing: .8px;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .result-hero h3 {
            font-family: 'Fredoka One', cursive;
            font-size: clamp(1.2rem, 3vw, 1.7rem);
            margin-bottom: 8px;
        }

        .result-hero p {
            font-size: .9rem;
            color: rgba(255, 255, 255, .65);
            line-height: 1.6;
            margin-bottom: 12px;
        }

        .result-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }

        .rtag {
            background: rgba(255, 255, 255, .1);
            border: 1px solid rgba(255, 255, 255, .15);
            color: rgba(255, 255, 255, .8);
            border-radius: 50px;
            padding: 5px 14px;
            font-family: 'Nunito', sans-serif;
            font-weight: 800;
            font-size: .75rem;
        }

        /* ══ BMI BADGE ══ */
        .bmi-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 16px;
            border-radius: 50px;
            font-family: 'Nunito', sans-serif;
            font-weight: 800;
            font-size: .8rem;
            margin-top: 12px;
        }

        /* ══ FREE PREVIEW BADGE ══ */
        .free-preview-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--mnl);
            color: #00a870;
            border-radius: 50px;
            padding: 5px 14px;
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: .72rem;
            letter-spacing: .8px;
            text-transform: uppercase;
        }

        /* ══ DAY PILL ══ */
        .day-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, var(--pk), var(--pkd));
            color: #fff;
            border-radius: 50px;
            padding: 7px 20px;
            font-family: 'Fredoka One', cursive;
            font-size: .92rem;
            margin-bottom: 16px;
            box-shadow: 0 4px 14px rgba(255, 77, 143, .28);
        }

        /* ══ MEAL GRID ══ */
        .meal-plan-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
            margin-bottom: 28px;
        }

        .meal-card {
            background: var(--card);
            border: 1.5px solid var(--border);
            border-radius: 20px;
            padding: 16px;
            transition: transform .2s, box-shadow .2s;
        }

        .meal-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(124, 58, 237, .1);
        }

        .meal-time {
            display: flex;
            align-items: center;
            gap: 7px;
            margin-bottom: 8px;
        }

        .meal-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .meal-label {
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: .72rem;
            text-transform: uppercase;
            letter-spacing: .6px;
        }

        .meal-icon {
            font-size: 1.6rem;
            display: block;
            margin-bottom: 6px;
        }

        .meal-name {
            font-family: 'Fredoka One', cursive;
            font-size: .92rem;
            color: var(--dk);
            margin-bottom: 6px;
        }

        .meal-items {
            font-size: .76rem;
            color: #888;
            line-height: 1.55;
            margin-bottom: 8px;
        }

        .meal-tag {
            display: inline-block;
            background: var(--mnl);
            color: #00a870;
            border-radius: 50px;
            padding: 3px 10px;
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: .66rem;
        }

        /* ══ NUTRIENTS ══ */
        .nutrients-section {
            background: var(--pul);
            border-radius: 24px;
            padding: 24px;
            margin-bottom: 24px;
        }

        .nutrients-title {
            font-family: 'Fredoka One', cursive;
            font-size: 1.1rem;
            color: var(--dk);
            margin-bottom: 18px;
        }

        .n-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }

        .n-label {
            font-family: 'Nunito', sans-serif;
            font-weight: 800;
            font-size: .78rem;
            color: var(--dk);
            min-width: 120px;
        }

        .n-track {
            flex: 1;
            height: 8px;
            background: rgba(124, 58, 237, .1);
            border-radius: 50px;
            overflow: hidden;
        }

        .n-fill {
            height: 100%;
            border-radius: 50px;
            width: 0%;
            transition: width 1s cubic-bezier(.4, 0, .2, 1);
        }

        .n-val {
            font-family: 'Fredoka One', cursive;
            font-size: .85rem;
            color: var(--pu);
            min-width: 70px;
            text-align: right;
        }

        /* ══ PRODUCT REC ══ */
        .product-rec {
            background: linear-gradient(135deg, #fff8e1, #fff3cd);
            border: 2px solid #ffe082;
            border-radius: 24px;
            padding: 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 28px;
            flex-wrap: wrap;
        }

        .prod-tag {
            display: inline-block;
            background: rgba(255, 214, 0, .25);
            color: #b8860b;
            border-radius: 50px;
            padding: 3px 12px;
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: .67rem;
            letter-spacing: .8px;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .prod-name {
            font-family: 'Fredoka One', cursive;
            font-size: 1.1rem;
            color: var(--dk);
            margin-bottom: 4px;
        }

        .prod-why {
            font-size: .78rem;
            color: #888;
            line-height: 1.55;
        }

        .prod-btn {
            background: var(--ye);
            border: none;
            border-radius: 50px;
            padding: 12px 22px;
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: .85rem;
            cursor: pointer;
            color: var(--dk);
            white-space: nowrap;
            transition: all .2s;
            box-shadow: 0 4px 14px rgba(255, 214, 0, .4);
            flex-shrink: 0;
        }

        .prod-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 214, 0, .5);
        }

        /* ══ TIPS ══ */
        .tips-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
            margin-bottom: 32px;
        }

        .tip-card {
            display: flex;
            gap: 14px;
            background: var(--card);
            border: 1.5px solid var(--border);
            border-radius: 20px;
            padding: 18px;
        }

        .tip-icon {
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .tip-title {
            font-family: 'Fredoka One', cursive;
            font-size: .9rem;
            color: var(--dk);
            margin-bottom: 4px;
        }

        .tip-text {
            font-size: .78rem;
            color: #888;
            line-height: 1.55;
        }

        /* ══ RESULT ACTIONS ══ */
        .result-actions {
            text-align: center;
            margin-top: 24px;
        }

        /* ══ LOCK BANNER ══ */
        .lock-banner {
            background: linear-gradient(135deg, var(--dk2), #2d0060);
            border-radius: 28px;
            padding: 40px 36px;
            text-align: center;
            position: relative;
            overflow: hidden;
            margin-bottom: 28px;
        }

        .lock-banner::before {
            content: '🔒';
            position: absolute;
            right: -20px;
            bottom: -30px;
            font-size: 9rem;
            opacity: .04;
            pointer-events: none;
        }

        .lock-icon-lg {
            font-size: 2.6rem;
            margin-bottom: 12px;
            display: block;
        }

        .lock-title {
            font-family: 'Fredoka One', cursive;
            font-size: clamp(1.3rem, 2.5vw, 1.9rem);
            color: #fff;
            margin-bottom: 10px;
        }

        .lock-sub {
            color: rgba(255, 255, 255, .55);
            font-size: .9rem;
            line-height: 1.65;
            max-width: 440px;
            margin: 0 auto 28px;
        }

        .plan-cards-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            max-width: 480px;
            margin: 0 auto 24px;
        }

        .plan-card {
            background: rgba(255, 255, 255, .06);
            border: 1.5px solid rgba(255, 255, 255, .1);
            border-radius: 22px;
            padding: 22px 18px;
            cursor: pointer;
            transition: all .3s;
            text-align: left;
        }

        .plan-card:hover {
            background: rgba(255, 255, 255, .1);
            transform: translateY(-4px);
        }

        .plan-card.active {
            border-color: var(--ye);
            background: rgba(255, 214, 0, .08);
            box-shadow: 0 0 0 1px var(--ye);
        }

        .plan-pop-badge {
            display: block;
            background: rgba(255, 214, 0, .18);
            border: 1px solid rgba(255, 214, 0, .3);
            color: var(--ye);
            border-radius: 50px;
            padding: 3px 12px;
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: .65rem;
            letter-spacing: .8px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .plan-name {
            font-family: 'Fredoka One', cursive;
            font-size: 1.05rem;
            color: #fff;
            margin-bottom: 4px;
        }

        .plan-price {
            font-family: 'Fredoka One', cursive;
            font-size: 1.6rem;
            color: var(--ye);
            margin-bottom: 8px;
        }

        .plan-price span {
            font-size: .75rem;
            color: rgba(255, 255, 255, .4);
            font-family: 'DM Sans', sans-serif;
            font-weight: 400;
        }

        .plan-features {
            font-size: .76rem;
            color: rgba(255, 255, 255, .55);
            line-height: 1.7;
        }

        .plan-features .pf {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .plan-features .pf::before {
            content: '✓';
            color: var(--mn);
            font-weight: 900;
            font-size: .7rem;
        }

        .btn-subscribe {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: var(--ye);
            color: var(--dk);
            border: none;
            border-radius: 50px;
            padding: 16px 42px;
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: 1rem;
            cursor: pointer;
            transition: all .3s;
            box-shadow: 0 8px 28px rgba(255, 214, 0, .4);
        }

        .btn-subscribe:hover {
            transform: translateY(-4px) scale(1.05);
            box-shadow: 0 16px 36px rgba(255, 214, 0, .55);
        }

        /* ══ SUCCESS BANNER ══ */
        .success-banner {
            display: none;
            background: linear-gradient(135deg, var(--mnl), #b8fff0);
            border: 2.5px solid var(--mn);
            border-radius: 28px;
            padding: 36px;
            text-align: center;
            margin-bottom: 28px;
            animation: popIn .5s ease both;
        }

        @keyframes popIn {
            from {
                transform: scale(.92);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .success-banner h3 {
            font-family: 'Fredoka One', cursive;
            font-size: 1.5rem;
            color: #00765A;
            margin-bottom: 8px;
        }

        .success-banner p {
            font-size: .9rem;
            color: #007A5A;
            margin-bottom: 18px;
        }

        /* ══ MODAL ══ */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(13, 0, 32, .6);
            z-index: 99999;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(6px);
            padding: 20px;
        }

        .modal-overlay.open {
            display: flex;
        }

        .modal-box {
            background: #fff;
            border-radius: 36px;
            padding: 44px 40px;
            max-width: 480px;
            width: 100%;
            position: relative;
            box-shadow: 0 32px 80px rgba(13, 0, 32, .35);
            animation: stepIn .45s cubic-bezier(.34, 1.2, .64, 1) both;
            max-height: 90vh;
            overflow-y: auto;
        }

        @keyframes stepIn {
            from {
                transform: scale(.88) translateY(30px);
                opacity: 0;
            }

            to {
                transform: scale(1) translateY(0);
                opacity: 1;
            }
        }

        .modal-close-btn {
            position: absolute;
            top: 16px;
            right: 18px;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: var(--pkl);
            border: none;
            cursor: pointer;
            color: var(--pk);
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all .2s;
        }

        .modal-close-btn:hover {
            background: var(--pk);
            color: #fff;
        }

        .modal-h {
            font-family: 'Fredoka One', cursive;
            font-size: 1.6rem;
            color: var(--dk);
            margin-bottom: 6px;
        }

        .modal-sub {
            font-size: .88rem;
            color: #888;
            line-height: 1.6;
            margin-bottom: 24px;
        }

        .mfield {
            margin-bottom: 14px;
        }

        .mfield label {
            font-family: 'Nunito', sans-serif;
            font-weight: 800;
            font-size: .78rem;
            color: #888;
            display: block;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .mfield input {
            width: 100%;
            padding: 13px 18px;
            border: 2px solid #f0f0f0;
            border-radius: 14px;
            font-family: 'DM Sans', sans-serif;
            font-size: .95rem;
            color: var(--dk);
            background: #fafafa;
            outline: none;
            transition: border-color .2s;
        }

        .mfield input:focus {
            border-color: var(--pu);
            background: #fff;
        }

        .order-summary-box {
            background: linear-gradient(135deg, rgba(124, 58, 237, .05), rgba(255, 77, 143, .05));
            border: 2px solid var(--pul);
            border-radius: 18px;
            padding: 16px 18px;
            margin-bottom: 18px;
        }

        .os-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: 'Nunito', sans-serif;
        }

        .os-lbl {
            font-weight: 700;
            font-size: .88rem;
            color: #888;
        }

        .os-val {
            font-family: 'Fredoka One', cursive;
            font-size: 1.15rem;
            color: var(--pu);
        }

        .secure-note {
            text-align: center;
            font-size: .75rem;
            color: #aaa;
            margin-top: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            font-family: 'Nunito', sans-serif;
            font-weight: 700;
        }

        /* ══ RESPONSIVE ══ */
        @media (max-width: 680px) {
            .diet-card {
                padding: 24px 18px;
                border-radius: 24px;
            }

            .age-grid {
                grid-template-columns: repeat(3, 1fr);
            }

            .meal-plan-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .tips-grid {
                grid-template-columns: 1fr;
            }

            .diet-pref-row {
                grid-template-columns: repeat(2, 1fr);
            }

            .plan-cards-grid {
                grid-template-columns: 1fr;
            }

            .lock-banner {
                padding: 28px 20px;
            }

            .modal-box {
                padding: 32px 22px;
                border-radius: 28px;
            }

            .step-nav {
                flex-direction: column-reverse;
                width: 100%;
            }

            .step-nav .btn-next,
            .step-nav .btn-generate {
                width: 100%;
                justify-content: center;
            }

            .step-nav .btn-back {
                width: 100%;
                text-align: center;
            }

            .n-label {
                min-width: 100px;
                font-size: .72rem;
            }

            .product-rec {
                flex-direction: column;
            }

            .prod-btn {
                width: 100%;
                text-align: center;
                justify-content: center;
            }
        }

        @media (max-width: 440px) {
            .age-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .meal-plan-grid {
                grid-template-columns: 1fr;
            }

            .gender-row {
                grid-template-columns: 1fr 1fr;
            }

            .gender-row .gender-card:last-child {
                grid-column: 1 / -1;
            }

            .hw-row {
                grid-template-columns: 1fr;
            }

            .stepper-progress {
                gap: 0;
            }

            .sp-label {
                display: none;
            }

            .sp-line {
                min-width: 14px;
            }

            .diet-section {
                padding: 32px 14px 60px;
            }

            .n-row {
                flex-wrap: wrap;
                gap: 6px;
            }

            .n-label {
                min-width: 100%;
            }

            .n-val {
                text-align: left;
            }
        }
    </style>
@endpush
@section('content')
    <main>
        <section class="diet-section" id="diet-chart">
            <div class="diet-header">
                <span class="sec-eye">Free for Every Parent</span>
                <h2 class="sec-title">Get Your Child's <span class="acc">Personalized</span><br>Diet Chart </h2>
                <p class="sec-sub">Answer 4 quick questions — get a free 2-day expert diet plan. Subscribe to unlock the full
                    7-day plan + PDF download.</p>
            </div>

            <div class="stepper-wrap">

                <!-- Stepper Progress -->
                <div class="stepper-progress">
                    <div class="sp-step active" id="sp1">
                        <div class="sp-ball">1</div>
                        <div class="sp-label">Child Info</div>
                    </div>
                    <div class="sp-line">
                        <div class="sp-line-fill" id="line1"></div>
                    </div>
                    <div class="sp-step" id="sp2">
                        <div class="sp-ball">2</div>
                        <div class="sp-label">Health Goals</div>
                    </div>
                    <div class="sp-line">
                        <div class="sp-line-fill" id="line2"></div>
                    </div>
                    <div class="sp-step" id="sp3">
                        <div class="sp-ball">3</div>
                        <div class="sp-label">Diet Type</div>
                    </div>
                    <div class="sp-line">
                        <div class="sp-line-fill" id="line3"></div>
                    </div>
                    <div class="sp-step" id="sp4">
                        <div class="sp-ball">4</div>
                        <div class="sp-label">Your Plan</div>
                    </div>
                </div>

                <!-- Diet Card -->
                <div class="diet-card">

                    <!-- STEP 1 -->
                    <div class="step-panel active" id="panel1">
                        <div class="step-head">
                            <span class="step-emoji">👶</span>
                            <h3>Tell us about your child</h3>
                            <p>Enter your child's name, select age, gender, and optional body measurements.</p>
                        </div>

                        <!-- Child Name -->
                        <div class="name-input-wrap">
                            <label class="name-input-label">👦 Child's Full Name</label>
                            <input type="text" class="name-input-field" id="childNameInput"
                                placeholder="e.g. Aarav Sharma" autocomplete="off">
                            <div class="selection-error" id="nameError">⚠️ Please enter your child's name to continue.</div>
                        </div>

                        <!-- Age -->
                        <div style="margin-bottom:24px">
                            <div
                                style="font-family:'Fredoka One',cursive;font-size:1rem;color:var(--dk);margin-bottom:14px">
                                Age Group
                            </div>
                            <div class="age-grid" id="ageGrid">
                                <div class="age-card" data-age="2-3" onclick="dcSelectAge(this)">
                                    <span class="age-emoji">👶</span>
                                    <span class="age-range">2–3 yrs</span>
                                    <span class="age-label">Toddler</span>
                                </div>
                                <div class="age-card" data-age="4-6" onclick="dcSelectAge(this)">
                                    <span class="age-emoji">🧒</span>
                                    <span class="age-range">4–6 yrs</span>
                                    <span class="age-label">Pre-School</span>
                                </div>
                                <div class="age-card" data-age="7-9" onclick="dcSelectAge(this)">
                                    <span class="age-emoji">🧑</span>
                                    <span class="age-range">7–9 yrs</span>
                                    <span class="age-label">Primary</span>
                                </div>
                                <div class="age-card" data-age="10-12" onclick="dcSelectAge(this)">
                                    <span class="age-emoji">🧑</span>
                                    <span class="age-range">10–12 yrs</span>
                                    <span class="age-label">Middle</span>
                                </div>
                                <div class="age-card" data-age="13-14" onclick="dcSelectAge(this)">
                                    <span class="age-emoji">🧑</span>
                                    <span class="age-range">13–14 yrs</span>
                                    <span class="age-label">Teen</span>
                                </div>
                            </div>
                            <div class="selection-error" id="ageError">⚠️ Please select an age group to continue.</div>
                        </div>

                        <!-- Gender -->
                        <div style="margin-bottom:24px">
                            <div
                                style="font-family:'Fredoka One',cursive;font-size:1rem;color:var(--dk);margin-bottom:14px">
                                Gender
                            </div>
                            <div class="gender-row" id="genderRow">
                                <div class="gender-card" data-gender="boy" onclick="dcSelectGender(this)">
                                    <span class="gender-emoji">👦</span>
                                    <div class="gender-name">Boy</div>
                                </div>
                                <div class="gender-card" data-gender="girl" onclick="dcSelectGender(this)">
                                    <span class="gender-emoji">👧</span>
                                    <div class="gender-name">Girl</div>
                                </div>
                                <div class="gender-card" data-gender="other" onclick="dcSelectGender(this)">
                                    <span class="gender-emoji">🧒</span>
                                    <div class="gender-name">Prefer not to say</div>
                                </div>
                            </div>
                            <div class="selection-error" id="genderError">⚠️ Please select a gender to continue.</div>
                        </div>

                        <!-- Height & Weight -->
                        <div>
                            <div
                                style="font-family:'Fredoka One',cursive;font-size:1rem;color:var(--dk);margin-bottom:6px">
                                Body Measurements
                                <span
                                    style="font-size:.72rem;font-family:'Nunito',sans-serif;font-weight:700;color:#aaa;margin-left:6px">(Optional
                                    — improves accuracy)</span>
                            </div>
                            <div class="hw-row">
                                <div class="hw-group">
                                    <label>📏 Height (cm)</label>
                                    <input type="number" id="heightInput" placeholder="e.g. 115" min="50"
                                        max="200">
                                </div>
                                <div class="hw-group">
                                    <label>⚖️ Weight (kg)</label>
                                    <input type="number" id="weightInput" placeholder="e.g. 22" min="5"
                                        max="100">
                                </div>
                            </div>
                        </div>

                        <div class="step-nav">
                            <div></div>
                            <button class="btn-next" onclick="dcGoStep(2)">Continue → Health Goals</button>
                        </div>
                    </div>

                    <!-- STEP 2 -->
                    <div class="step-panel" id="panel2">
                        <div class="step-head">
                            <span class="step-emoji">🎯</span>
                            <h3>What are your goals for your child?</h3>
                            <p>Select all that apply — we'll personalize the diet chart accordingly.</p>
                        </div>
                        <div class="problem-grid" id="problemGrid">
                            <div class="prob-tag" data-prob="immunity" onclick="dcToggleProb(this)"><span
                                    class="prob-icon">🛡️</span>
                                Boost Immunity</div>
                            <div class="prob-tag" data-prob="growth" onclick="dcToggleProb(this)"><span
                                    class="prob-icon">📏</span>
                                Height & Growth</div>
                            <div class="prob-tag" data-prob="brain" onclick="dcToggleProb(this)"><span
                                    class="prob-icon">🧠</span>
                                Brain & Focus</div>
                            <div class="prob-tag" data-prob="weight" onclick="dcToggleProb(this)"><span
                                    class="prob-icon">⚖️</span>
                                Healthy Weight</div>
                            <div class="prob-tag" data-prob="energy" onclick="dcToggleProb(this)"><span
                                    class="prob-icon">⚡</span>
                                More Energy</div>
                            <div class="prob-tag" data-prob="sleep" onclick="dcToggleProb(this)"><span
                                    class="prob-icon">😴</span>
                                Better Sleep</div>
                            <div class="prob-tag" data-prob="digestion" onclick="dcToggleProb(this)"><span
                                    class="prob-icon"></span>
                                Gut & Digestion</div>
                            <div class="prob-tag" data-prob="bones" onclick="dcToggleProb(this)"><span
                                    class="prob-icon">💪</span>
                                Strong Bones</div>
                            <div class="prob-tag" data-prob="mood" onclick="dcToggleProb(this)"><span
                                    class="prob-icon">😊</span> Mood
                                & Calm</div>
                            <div class="prob-tag" data-prob="skin" onclick="dcToggleProb(this)"><span
                                    class="prob-icon">✨</span> Skin
                                & Hair</div>
                            <div class="prob-tag" data-prob="appetite" onclick="dcToggleProb(this)"><span
                                    class="prob-icon">🍽️</span>
                                Picky Eater Fix</div>
                            <div class="prob-tag" data-prob="exam" onclick="dcToggleProb(this)"><span
                                    class="prob-icon">📚</span> Exam
                                Performance</div>
                        </div>
                        <div class="selection-error" id="probError">⚠️ Please select at least one health goal.</div>
                        <div class="step-nav">
                            <button class="btn-back" onclick="dcGoStep(1)">← Back</button>
                            <button class="btn-next" onclick="dcGoStep(3)">Next → Diet Preferences</button>
                        </div>
                    </div>

                    <!-- STEP 3 -->
                    <div class="step-panel" id="panel3">
                        <div class="step-head">
                            <span class="step-emoji"></span>
                            <h3>Any food preferences or allergies?</h3>
                            <p>This helps us build a plan your family will actually love and follow.</p>
                        </div>
                        <div style="margin-bottom:24px">
                            <div
                                style="font-family:'Fredoka One',cursive;font-size:1rem;color:var(--dk);margin-bottom:14px">
                                Diet Type
                            </div>
                            <div class="diet-pref-row" id="dietPrefRow">
                                <div class="dpref" data-pref="vegetarian" onclick="dcSelectPref(this)"> Vegetarian
                                </div>
                                <div class="dpref" data-pref="vegan" onclick="dcSelectPref(this)">🌱 Vegan</div>
                                <div class="dpref" data-pref="eggetarian" onclick="dcSelectPref(this)">🥚 Eggetarian
                                </div>
                                <div class="dpref" data-pref="non-veg" onclick="dcSelectPref(this)">🍗 Non-Veg</div>
                            </div>
                            <div class="selection-error" id="prefError">⚠️ Please select a diet preference.</div>
                        </div>
                        <div>
                            <div
                                style="font-family:'Fredoka One',cursive;font-size:1rem;color:var(--dk);margin-bottom:8px">
                                Known Allergies <span
                                    style="font-weight:400;font-size:.8rem;font-family:'DM Sans',sans-serif;color:#aaa">(Optional)</span>
                            </div>
                            <div class="allergy-row" id="allergyRow">
                                <div class="atag" data-allergy="dairy" onclick="dcToggleAllergy(this)">🥛 Dairy</div>
                                <div class="atag" data-allergy="gluten" onclick="dcToggleAllergy(this)">🌾 Gluten</div>
                                <div class="atag" data-allergy="nuts" onclick="dcToggleAllergy(this)">🥜 Nuts</div>
                                <div class="atag" data-allergy="soy" onclick="dcToggleAllergy(this)">🫘 Soy</div>
                                <div class="atag" data-allergy="eggs" onclick="dcToggleAllergy(this)">🥚 Eggs</div>
                                <div class="atag" data-allergy="none" onclick="dcToggleAllergy(this)">✅ No Allergies
                                </div>
                            </div>
                        </div>
                        <div class="step-nav">
                            <button class="btn-back" onclick="dcGoStep(2)">← Back</button>
                            <button class="btn-generate" onclick="dcGenerateChart()">✨ Generate My Diet Chart</button>
                        </div>
                    </div>

                    <!-- Loading -->
                    <div class="loading-state" id="loadingState">
                        <div class="loader-ring"></div>
                        <h3>Crafting Your Child's Plan </h3>
                        <p>Our Ayurvedic nutritionists are personalizing this just for you…</p>
                        <div class="loading-facts">
                            <span class="lfact">✅ FSSAI Certified Recipes</span>
                            <span class="lfact">👩‍⚕️ Pediatrician Approved</span>
                            <span class="lfact"> Ayurveda-Backed</span>
                        </div>
                    </div>

                    <!-- Result -->
                    <div class="result-state" id="resultState">

                        <div class="result-hero">
                            <div class="result-badge">Personalized Diet Chart</div>
                            <h3 id="resultTitle">Your Child's Nutrition Plan</h3>
                            <p id="resultDesc"></p>
                            <div id="bmiRow"></div>
                            <div class="result-tags" id="resultTags"></div>
                            <div style="margin-top:14px">
                                <span class="free-preview-badge">🎁 Free Preview: Day 1 & Day 2</span>
                            </div>
                        </div>

                        <div id="day1Section">
                            <div
                                style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;flex-wrap:wrap;gap:8px">
                                <div style="font-family:'Fredoka One',cursive;font-size:1.2rem;color:var(--dk)">Daily Meal
                                    Plan</div>
                            </div>
                            <div class="day-pill">📅 Day 1 — Monday</div>
                            <div class="meal-plan-grid" id="mealGrid1"></div>
                        </div>

                        <div id="day2Section">
                            <div class="day-pill"
                                style="background:linear-gradient(135deg,var(--sk),#0088bb);box-shadow:0 4px 14px rgba(0,191,255,.28)">
                                📅 Day 2 — Tuesday</div>
                            <div class="meal-plan-grid" id="mealGrid2"></div>
                        </div>

                        <div class="nutrients-section">
                            <div class="nutrients-title">Daily Nutrition Targets</div>
                            <div id="nutrientBars"></div>
                        </div>

                        <div class="product-rec" id="productRec"></div>

                        <div style="font-family:'Fredoka One',cursive;font-size:1.2rem;color:var(--dk);margin-bottom:16px">
                            💡 Parent
                            Tips</div>
                        <div class="tips-grid" id="tipsGrid"></div>

                        <!-- Lock Banner -->
                        <div class="lock-banner" id="lockBanner">
                            <span class="lock-icon-lg">🔒</span>
                            <div class="lock-title">Unlock the Full 7-Day Plan</div>
                            <div class="lock-sub">Get Day 3–7 personalized meal plans, weekly grocery list, supplement
                                stack, and a
                                downloadable PDF chart.</div>
                            <div class="plan-cards-grid">
                                <div class="plan-card" id="planBasic" onclick="dcSelectPlan('basic')">
                                    <div class="plan-name">7-Day Plan</div>
                                    <div class="plan-price">₹99 <span>/ one-time</span></div>
                                    <div class="plan-features">
                                        <div class="pf">Full 7-day meal plan</div>
                                        <div class="pf">PDF download</div>
                                        <div class="pf">Email delivery</div>
                                    </div>
                                </div>
                                <div class="plan-card active" id="planPro" onclick="dcSelectPlan('pro')">
                                    <span class="plan-pop-badge">⭐ Most Popular</span>
                                    <div class="plan-name">Monthly Subscription</div>
                                    <div class="plan-price">₹199 <span>/ month</span></div>
                                    <div class="plan-features">
                                        <div class="pf">New plan every month</div>
                                        <div class="pf">PDF downloads</div>
                                        <div class="pf">Progress tracker</div>
                                        <div class="pf">Priority support</div>
                                    </div>
                                </div>
                            </div>
                            <button class="btn-subscribe" onclick="dcOpenModal()">Subscribe & Unlock Full Plan →</button>
                        </div>

                        <!-- Success Banner -->
                        <div class="success-banner" id="successBanner">
                            <div style="font-size:3rem;margin-bottom:12px">🎉</div>
                            <h3>Payment Successful! Your PDF is Ready.</h3>
                            <p>The complete 7-day plan has been sent to your email. You can also download it directly below.
                            </p>
                            <button class="btn-generate" onclick="dcDownload()">⬇ Download PDF Chart</button>
                        </div>

                        <div class="result-actions">
                            <button class="btn-restart" onclick="dcRestart()">↩ Make Another Plan</button>
                        </div>

                    </div><!-- /result-state -->
                </div><!-- /diet-card -->
            </div><!-- /stepper-wrap -->
        </section>


        <!-- ══ MODAL ══ -->
        <div class="modal-overlay" id="dcModalOverlay" onclick="dcCloseModalOutside(event)">
            <div class="modal-box">
                <button class="modal-close-btn" onclick="dcCloseModal()">✕</button>
                <h2 class="modal-h" id="dcModalTitle">Unlock Your 7-Day Plan </h2>
                <p class="modal-sub" id="dcModalSub">Enter your details to receive the complete personalized diet chart
                    via
                    email.</p>
                <div class="mfield">
                    <label>Parent's name</label>
                    <input type="text" id="dcName" placeholder="e.g. Priya Sharma">
                </div>
                <div class="mfield">
                    <label>Email address</label>
                    <input type="email" id="dcEmail" placeholder="hello@gmail.com">
                </div>
                <div class="mfield">
                    <label>Phone number</label>
                    <input type="tel" id="dcPhone" placeholder="+91 98765 43210">
                </div>
                <div class="order-summary-box">
                    <div
                        style="font-family:'Nunito',sans-serif;font-weight:800;font-size:.75rem;color:#888;text-transform:uppercase;letter-spacing:1px;margin-bottom:10px">
                        Order Summary</div>
                    <div class="os-row">
                        <span class="os-lbl" id="dcOrderItem">7-Day Diet Chart (Monthly)</span>
                        <span class="os-val" id="dcOrderPrice">₹199</span>
                    </div>
                </div>
                <button class="btn-generate" style="width:100%;justify-content:center;font-size:.95rem"
                    onclick="dcProcessPayment()">
                    Pay & Download Chart →
                </button>
                <div class="secure-note">🔒 Secure payment &nbsp;·&nbsp; Instant PDF delivery &nbsp;·&nbsp; Cancel anytime
                </div>
            </div>
        </div>
    </main>
    @push('scripts')
        <script>
            (function() {

                var dcState = {
                    childName: null,
                    age: null,
                    gender: null,
                    problems: [],
                    pref: null,
                    allergies: [],
                    height: null,
                    weight: null,
                    plan: 'pro'
                };

                /* â”€â”€ Selectors â”€â”€ */
                window.dcSelectAge = function(el) {
                    document.querySelectorAll('#ageGrid .age-card').forEach(c => c.classList.remove('selected'));
                    el.classList.add('selected');
                    dcState.age = el.dataset.age;
                    document.getElementById('ageError').classList.remove('show');
                };

                window.dcSelectGender = function(el) {
                    document.querySelectorAll('#genderRow .gender-card').forEach(c => c.classList.remove('selected'));
                    el.classList.add('selected');
                    dcState.gender = el.dataset.gender;
                    document.getElementById('genderError').classList.remove('show');
                };

                window.dcToggleProb = function(el) {
                    el.classList.toggle('selected');
                    var p = el.dataset.prob;
                    if (el.classList.contains('selected')) {
                        if (!dcState.problems.includes(p)) dcState.problems.push(p);
                    } else {
                        dcState.problems = dcState.problems.filter(x => x !== p);
                    }
                    document.getElementById('probError').classList.remove('show');
                };

                window.dcSelectPref = function(el) {
                    document.querySelectorAll('#dietPrefRow .dpref').forEach(c => c.classList.remove('selected'));
                    el.classList.add('selected');
                    dcState.pref = el.dataset.pref;
                    document.getElementById('prefError').classList.remove('show');
                };

                window.dcToggleAllergy = function(el) {
                    if (el.dataset.allergy === 'none') {
                        document.querySelectorAll('#allergyRow .atag').forEach(a => a.classList.remove('selected'));
                        el.classList.add('selected');
                        dcState.allergies = ['none'];
                        return;
                    }
                    var noneTag = document.querySelector('#allergyRow .atag[data-allergy="none"]');
                    if (noneTag) noneTag.classList.remove('selected');
                    el.classList.toggle('selected');
                    var a = el.dataset.allergy;
                    if (el.classList.contains('selected')) {
                        dcState.allergies = dcState.allergies.filter(x => x !== 'none');
                        if (!dcState.allergies.includes(a)) dcState.allergies.push(a);
                    } else {
                        dcState.allergies = dcState.allergies.filter(x => x !== a);
                    }
                };

                window.dcSelectPlan = function(p) {
                    dcState.plan = p;
                    document.querySelectorAll('.plan-card').forEach(c => c.classList.remove('active'));
                    document.getElementById('plan' + p.charAt(0).toUpperCase() + p.slice(1)).classList.add('active');
                };

                /* â”€â”€ Step navigation â”€â”€ */
                window.dcGoStep = function(step) {
                    if (step === 2) {
                        // Validate name
                        var nameVal = document.getElementById('childNameInput').value.trim();
                        if (!nameVal) {
                            document.getElementById('nameError').classList.add('show');
                            return;
                        }
                        dcState.childName = nameVal;
                        document.getElementById('nameError').classList.remove('show');

                        if (!dcState.age) {
                            document.getElementById('ageError').classList.add('show');
                            return;
                        }
                        if (!dcState.gender) {
                            document.getElementById('genderError').classList.add('show');
                            return;
                        }
                        dcState.height = document.getElementById('heightInput').value || null;
                        dcState.weight = document.getElementById('weightInput').value || null;
                    }
                    if (step === 3) {
                        if (!dcState.problems.length) {
                            document.getElementById('probError').classList.add('show');
                            return;
                        }
                    }
                    dcShowPanel('panel' + step);
                    dcUpdateStepper(step);
                    document.getElementById('diet-chart').scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                };

                function dcShowPanel(id) {
                    document.querySelectorAll('.diet-card .step-panel, .loading-state, .result-state').forEach(p => {
                        p.classList.remove('active');
                        p.style.display = '';
                    });
                    var el = document.getElementById(id);
                    if (el) {
                        el.classList.add('active');
                        el.style.display = 'block';
                    }
                }

                function dcUpdateStepper(step) {
                    for (var i = 1; i <= 4; i++) {
                        var s = document.getElementById('sp' + i);
                        if (!s) continue;
                        s.classList.remove('active', 'done');
                        if (i < step) {
                            s.classList.add('done');
                            s.querySelector('.sp-ball').textContent = 'âœ“';
                        } else if (i === step) {
                            s.classList.add('active');
                            s.querySelector('.sp-ball').textContent = i;
                        } else {
                            s.querySelector('.sp-ball').textContent = i;
                        }
                    }
                    for (var j = 1; j <= 3; j++) {
                        var l = document.getElementById('line' + j);
                        if (l) l.classList.toggle('done', j < step);
                    }
                }

                /* â”€â”€ Generate â”€â”€ */
                window.dcGenerateChart = function() {
                    if (!dcState.pref) {
                        document.getElementById('prefError').classList.add('show');
                        return;
                    }
                    dcShowPanel('loadingState');
                    dcUpdateStepper(4);
                    setTimeout(dcBuildResult, 2400);
                };

                /* â”€â”€ BMI â”€â”€ */
                function dcGetBMI() {
                    if (!dcState.height || !dcState.weight) return null;
                    var h = parseFloat(dcState.height) / 100,
                        w = parseFloat(dcState.weight);
                    if (h <= 0 || w <= 0) return null;
                    var bmi = Math.round((w / (h * h)) * 10) / 10;
                    var status, color;
                    if (bmi < 14.5) {
                        status = 'Underweight';
                        color = '#FF6B35';
                    } else if (bmi < 22) {
                        status = 'Healthy Weight âœ“';
                        color = '#00D68F';
                    } else if (bmi < 25) {
                        status = 'Slightly Overweight';
                        color = '#FFD600';
                    } else {
                        status = 'High BMI â€” Focus on Healthy Weight';
                        color = '#FF4D8F';
                    }
                    return {
                        bmi: bmi,
                        status: status,
                        color: color
                    };
                }

                /* â”€â”€ Build result â”€â”€ */
                function dcBuildResult() {
                    var ageMap = {
                        '2-3': 'Toddler (2â€“3 yrs)',
                        '4-6': 'Pre-Schooler (4â€“6 yrs)',
                        '7-9': 'Primary Schooler (7â€“9 yrs)',
                        '10-12': 'Middle Schooler (10â€“12 yrs)',
                        '13-14': 'Teen (13â€“14 yrs)'
                    };
                    var pmap = {
                        immunity: 'Immunity',
                        brain: 'Brain Power',
                        growth: 'Growth',
                        sleep: 'Better Sleep',
                        energy: 'Energy',
                        weight: 'Healthy Weight',
                        digestion: 'Digestion',
                        bones: 'Strong Bones',
                        mood: 'Mood & Calm',
                        skin: 'Skin & Hair',
                        appetite: 'Picky Eating',
                        exam: 'Exam Focus'
                    };

                    // Title with child's name
                    var cname = dcState.childName ? dcState.childName + "'s" : "Your Child's";
                    document.getElementById('resultTitle').textContent = cname + ' 7-Day Nutrition Plan';
                    document.getElementById('resultDesc').textContent = 'Personalized for a ' + (ageMap[dcState.age] ||
                        dcState.age) + ' â€” focused on ' + dcState.problems.slice(0, 2).map(p => pmap[p] || p).join(
                        ' & ') + '.';

                    // BMI
                    var bmiInfo = dcGetBMI();
                    if (bmiInfo) {
                        document.getElementById('bmiRow').innerHTML = '<div class="bmi-badge" style="background:' + bmiInfo
                            .color + '22;color:' + bmiInfo.color + ';border:1.5px solid ' + bmiInfo.color +
                            '44"><span style="font-size:16px">âš–ï¸</span> BMI ' + bmiInfo.bmi + ' â€” ' + bmiInfo.status +
                            '</div>';
                    } else {
                        document.getElementById('bmiRow').innerHTML = '';
                    }

                    // Tags
                    var tags = [];
                    if (dcState.childName) tags.push('ðŸ‘¦ ' + dcState.childName);
                    if (dcState.pref) tags.push(dcState.pref.charAt(0).toUpperCase() + dcState.pref.slice(1));
                    if (dcState.age) tags.push(dcState.age + ' yrs');
                    if (dcState.height && dcState.weight) tags.push(dcState.height + 'cm Â· ' + dcState.weight + 'kg');
                    dcState.problems.slice(0, 3).forEach(p => {
                        if (pmap[p]) tags.push(pmap[p]);
                    });
                    document.getElementById('resultTags').innerHTML = tags.map(t => '<div class="rtag">' + t + '</div>')
                        .join('');

                    // Meals
                    var meals = dcGetMeals(dcState.age, dcState.pref, dcState.problems);
                    document.getElementById('mealGrid1').innerHTML = meals[0].map(m => dcMealCard(m)).join('');
                    document.getElementById('mealGrid2').innerHTML = meals[1].map(m => dcMealCard(m)).join('');

                    // Nutrients
                    var nuts = dcGetNutrients(dcState.age);
                    document.getElementById('nutrientBars').innerHTML = nuts.map(n =>
                        '<div class="n-row"><div class="n-label">' + n.name +
                        '</div><div class="n-track"><div class="n-fill" style="width:0%;background:' + n.color +
                        '"></div></div><div class="n-val">' + n.val + '</div></div>'
                    ).join('');
                    setTimeout(() => {
                        nuts.forEach((n, i) => {
                            var fills = document.querySelectorAll('#nutrientBars .n-fill');
                            if (fills[i]) fills[i].style.width = n.pct + '%';
                        });
                    }, 120);

                    // Product rec
                    var rec = dcGetProductRec(dcState.problems);
                    document.getElementById('productRec').innerHTML =
                        '<div class="prod-info"><div class="prod-tag">Recommended for ' + (dcState.childName ||
                            'Your Child') + '</div><div class="prod-name">' + rec.name + '</div><div class="prod-why">' +
                        rec.why + '</div></div>' +
                        '<button class="prod-btn" onclick="alert(\'Added to cart! ðŸ›’\')">Add to Cart â€” ' + rec.price +
                        '</button>';

                    // Tips
                    var tips = dcGetTips(dcState.problems);
                    document.getElementById('tipsGrid').innerHTML = tips.map(t =>
                        '<div class="tip-card"><div class="tip-icon">' + t.icon + '</div><div><div class="tip-title">' +
                        t.title + '</div><div class="tip-text">' + t.text + '</div></div></div>'
                    ).join('');

                    document.getElementById('lockBanner').style.display = 'block';
                    document.getElementById('successBanner').style.display = 'none';

                    dcShowPanel('resultState');
                    document.getElementById('diet-chart').scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }

                function dcMealCard(m) {
                    return '<div class="meal-card"><div class="meal-time"><div class="meal-dot" style="background:' + m
                        .color + '"></div><div class="meal-label" style="color:' + m.color + '">' + m.time +
                        '</div></div><span class="meal-icon">' + m.icon + '</span><div class="meal-name">' + m.name +
                        '</div><div class="meal-items">' + m.items + '</div><div class="meal-tag">ðŸŒ¿ ' + m.benefit +
                        '</div></div>';
                }

                /* â”€â”€ Meal data â”€â”€ */
                function dcGetMeals(age, pref, problems) {
                    var isVeg = ['vegetarian', 'vegan'].includes(pref);
                    var isEgg = pref === 'eggetarian';
                    var isBrain = problems.includes('brain') || problems.includes('exam');
                    var isSleep = problems.includes('sleep');
                    var isGrowth = problems.includes('growth') || problems.includes('bones');

                    var day1 = [{
                            time: 'Wake-Up',
                            icon: 'ðŸŒ…',
                            color: '#FF9900',
                            name: 'Morning Boost',
                            items: 'Warm water with lemon + 1 tsp Amla powder + 5 soaked almonds',
                            benefit: 'Immunity + Digestion'
                        },
                        {
                            time: 'Breakfast',
                            icon: 'ðŸ³',
                            color: '#FF4D8F',
                            name: isBrain ? 'Brain Power Breakfast' : 'Power Breakfast',
                            items: isBrain ? (isVeg ? 'Moong dal chilla + Walnut chutney + Fresh fruit smoothie' :
                                'Scrambled eggs + Multigrain toast + Walnut smoothie') : (isVeg ?
                                'Oats upma with veggies + Banana + Milk' : (isEgg ? 'Egg paratha + Banana + Milk' :
                                    'Chicken sandwich + Banana + Milk')),
                            benefit: isBrain ? 'Omega-3 + Focus' : 'Iron + Protein'
                        },
                        {
                            time: 'Mid-Morning',
                            icon: 'ðŸŽ',
                            color: '#00BFFF',
                            name: 'Smart Snack',
                            items: 'Seasonal fruit + Roasted chana or Makhana + Coconut water',
                            benefit: 'Vitamins + Hydration'
                        },
                        {
                            time: 'Lunch',
                            icon: 'ðŸ±',
                            color: '#7C3AED',
                            name: isGrowth ? 'Growth Power Lunch' : 'Nutrition Lunch',
                            items: isVeg ? 'Brown rice + Dal + Paneer sabzi + Salad + Curd' : (isEgg ?
                                'Brown rice + Dal + Egg curry + Salad + Curd' :
                                'Brown rice + Dal + Chicken curry + Salad + Curd'),
                            benefit: isGrowth ? 'Calcium + Protein' : 'Balanced Macros'
                        },
                        {
                            time: 'Evening',
                            icon: 'ðŸ¥¤',
                            color: '#00D68F',
                            name: 'After-School Refuel',
                            items: isBrain ? 'Turmeric milk + Dates + Dark chocolate square' :
                                'Banana smoothie + Roasted seeds + Whole wheat crackers',
                            benefit: isBrain ? 'Memory + Calm' : 'Energy Refuel'
                        },
                        {
                            time: 'Dinner',
                            icon: 'ðŸŒ™',
                            color: isSleep ? '#5B21B6' : '#FF6B35',
                            name: isSleep ? 'Sleep Calm Dinner' : 'Growth Dinner',
                            items: isSleep ? 'Light khichdi + Ghee + Steamed veggies + Warm chamomile milk' : (isVeg ?
                                'Roti + Sabzi + Dal + Curd' : (isEgg ? 'Roti + Sabzi + Dal + Egg bhurji' :
                                    'Roti + Sabzi + Dal + Grilled chicken')),
                            benefit: isSleep ? 'L-Theanine + Rest' : 'Protein + Iron'
                        }
                    ];

                    var day2 = [{
                            time: 'Wake-Up',
                            icon: 'ðŸŒ…',
                            color: '#FF9900',
                            name: 'Morning Ritual',
                            items: 'Soaked methi water + 1 tsp honey + 4 walnuts',
                            benefit: 'Anti-Inflammatory'
                        },
                        {
                            time: 'Breakfast',
                            icon: 'ðŸ¥ž',
                            color: '#FF4D8F',
                            name: 'Wholesome Breakfast',
                            items: isVeg ? 'Ragi dosa + Peanut chutney + Seasonal fruit + Buttermilk' : (isEgg ?
                                'Omelette + Multigrain toast + Orange juice' : 'Chicken sandwich + Orange juice'),
                            benefit: 'Calcium + Energy'
                        },
                        {
                            time: 'Mid-Morning',
                            icon: 'ðŸŒ',
                            color: '#00BFFF',
                            name: 'Power Snack',
                            items: 'Roasted makhana + A2 milk or coconut water + Seasonal fruit',
                            benefit: 'Protein + Hydration'
                        },
                        {
                            time: 'Lunch',
                            icon: 'ðŸ±',
                            color: '#7C3AED',
                            name: 'Power Lunch',
                            items: isVeg ? 'Wheat roti + Palak paneer + Rajma + Salad + Curd' : (isEgg ?
                                'Wheat roti + Palak dal + Boiled eggs + Salad' :
                                'Wheat roti + Fish curry + Sabzi + Salad'),
                            benefit: 'Iron + Protein'
                        },
                        {
                            time: 'Evening',
                            icon: 'ðŸ¥¤',
                            color: '#00D68F',
                            name: 'Energizing Snack',
                            items: 'Dates + Roasted chana + Coconut water + Seasonal fruit',
                            benefit: 'Electrolytes + Fibre'
                        },
                        {
                            time: 'Dinner',
                            icon: 'ðŸŒ™',
                            color: '#5B21B6',
                            name: 'Light Nourishing Dinner',
                            items: isVeg ? 'Vegetable daliya + Buttermilk + Salad' : (isEgg ?
                                'Multigrain roti + Egg curry + Sabzi' : 'Multigrain roti + Grilled fish + Sabzi'),
                            benefit: 'Probiotics + Recovery'
                        }
                    ];

                    return [day1, day2];
                }

                /* â”€â”€ Nutrients â”€â”€ */
                function dcGetNutrients(age) {
                    var base = {
                        '2-3': {
                            cal: '1,200 kcal',
                            pro: '35g',
                            ca: '700mg',
                            fe: '7mg',
                            vc: '40mg',
                            om: '0.5g'
                        },
                        '4-6': {
                            cal: '1,400 kcal',
                            pro: '45g',
                            ca: '1,000mg',
                            fe: '10mg',
                            vc: '45mg',
                            om: '0.9g'
                        },
                        '7-9': {
                            cal: '1,700 kcal',
                            pro: '55g',
                            ca: '1,000mg',
                            fe: '10mg',
                            vc: '45mg',
                            om: '1.2g'
                        },
                        '10-12': {
                            cal: '2,000 kcal',
                            pro: '65g',
                            ca: '1,300mg',
                            fe: '12mg',
                            vc: '50mg',
                            om: '1.4g'
                        },
                        '13-14': {
                            cal: '2,200 kcal',
                            pro: '75g',
                            ca: '1,300mg',
                            fe: '15mg',
                            vc: '65mg',
                            om: '1.6g'
                        }
                    };
                    var d = base[age] || base['7-9'];
                    return [{
                            name: 'ðŸ”¥ Calories',
                            val: d.cal,
                            pct: 72,
                            color: 'linear-gradient(90deg,#FF4D8F,#FFD600)'
                        },
                        {
                            name: 'ðŸ’ª Protein',
                            val: d.pro,
                            pct: 68,
                            color: 'linear-gradient(90deg,#7C3AED,#FF4D8F)'
                        },
                        {
                            name: 'ðŸ¦´ Calcium',
                            val: d.ca,
                            pct: 80,
                            color: 'linear-gradient(90deg,#00BFFF,#00D68F)'
                        },
                        {
                            name: 'ðŸ©¸ Iron',
                            val: d.fe,
                            pct: 60,
                            color: 'linear-gradient(90deg,#FF6B35,#FF4D8F)'
                        },
                        {
                            name: 'ðŸŠ Vitamin C',
                            val: d.vc,
                            pct: 90,
                            color: 'linear-gradient(90deg,#FFD600,#FF6B35)'
                        },
                        {
                            name: 'ðŸŸ Omega-3 DHA',
                            val: d.om,
                            pct: 55,
                            color: 'linear-gradient(90deg,#00BFFF,#7C3AED)'
                        }
                    ];
                }

                /* â”€â”€ Product rec â”€â”€ */
                function dcGetProductRec(problems) {
                    if (problems.includes('brain') || problems.includes('exam'))
                        return {
                            name: 'BrainBoost Chews',
                            price: 'â‚¹649',
                            why: 'Brahmi + Omega-3 DHA + Shankhpushpi â€” clinically shown to improve focus, memory, and learning scores in 8 weeks.'
                        };
                    if (problems.includes('sleep') || problems.includes('mood'))
                        return {
                            name: 'DreamCalm Drops',
                            price: 'â‚¹549',
                            why: 'Chamomile + L-Theanine + Jatamansi â€” helps even the most hyperactive kids sleep deeply and wake up refreshed.'
                        };
                    return {
                        name: 'GrowStrong Gummies',
                        price: 'â‚¹599',
                        why: 'Ashwagandha + Vitamin D3 + Zinc â€” builds immunity, supports height & bone growth, and tastes amazing!'
                    };
                }

                /* â”€â”€ Tips â”€â”€ */
                function dcGetTips(problems) {
                    var pool = [{
                            icon: 'ðŸ’§',
                            title: 'Hydration is Key',
                            text: 'Ensure 6â€“8 glasses of water daily. Add lemon or mint for taste. Coconut water is great for electrolytes.'
                        },
                        {
                            icon: 'â˜€ï¸',
                            title: 'Morning Sunlight',
                            text: '15 mins of sunlight before 9am gives natural Vitamin D â€” crucial for bone growth and mood regulation.'
                        },
                        {
                            icon: 'ðŸ•',
                            title: 'Consistent Meal Times',
                            text: 'Fixed meal timings regulate hunger hormones and improve digestion and energy levels throughout the day.'
                        },
                        {
                            icon: 'ðŸš«',
                            title: 'Avoid Ultra-Processed Foods',
                            text: 'Chips, packaged biscuits, and sugary drinks spike blood sugar and displace nutrient-dense foods.'
                        },
                        {
                            icon: 'ðŸ«™',
                            title: 'Add Ghee Daily',
                            text: '1 tsp of pure ghee in dal or roti boosts fat-soluble vitamin absorption and supports brain development.'
                        },
                        {
                            icon: 'ðŸ˜´',
                            title: 'Sleep Schedule',
                            text: '9â€“10 hours of sleep is non-negotiable for growth hormone release and memory consolidation.'
                        }
                    ];
                    if (problems.includes('brain')) pool.unshift({
                        icon: 'ðŸ³',
                        title: 'Breakfast Before School',
                        text: 'Never skip breakfast! The brain uses 20% of all energy. A good breakfast improves focus by up to 35%.'
                    });
                    if (problems.includes('immunity')) pool.unshift({
                        icon: 'ðŸ¥›',
                        title: 'Probiotic Power',
                        text: 'Include one portion of curd/yogurt daily. Gut health = immune health. 70% of immunity lives in the gut.'
                    });
                    return pool.slice(0, 4);
                }

                /* â”€â”€ Modal â”€â”€ */
                window.dcOpenModal = function() {
                    var titles = {
                        basic: 'Unlock 7-Day Plan ðŸŒ¿',
                        pro: 'Start Monthly Subscription ðŸŒ¿'
                    };
                    var subs = {
                        basic: 'One-time payment. Receive the complete 7-day chart as a PDF instantly via email.',
                        pro: 'Unlock new personalized plans every month, progress tracker, and priority support.'
                    };
                    var items = {
                        basic: '7-Day Diet Chart (One-Time)',
                        pro: '7-Day Diet Chart (Monthly)'
                    };
                    var prices = {
                        basic: 'â‚¹99',
                        pro: 'â‚¹199'
                    };
                    document.getElementById('dcModalTitle').textContent = titles[dcState.plan] || titles.pro;
                    document.getElementById('dcModalSub').textContent = subs[dcState.plan] || subs.pro;
                    document.getElementById('dcOrderItem').textContent = items[dcState.plan] || items.pro;
                    document.getElementById('dcOrderPrice').textContent = prices[dcState.plan] || 'â‚¹199';
                    document.getElementById('dcModalOverlay').classList.add('open');
                };

                window.dcCloseModal = function() {
                    document.getElementById('dcModalOverlay').classList.remove('open');
                };

                window.dcCloseModalOutside = function(e) {
                    if (e.target === document.getElementById('dcModalOverlay')) dcCloseModal();
                };

                window.dcProcessPayment = function() {
                    var name = document.getElementById('dcName').value.trim();
                    var email = document.getElementById('dcEmail').value.trim();
                    if (!name || !email) {
                        alert('Please enter your name and email to continue.');
                        return;
                    }
                    dcCloseModal();
                    document.getElementById('lockBanner').style.display = 'none';
                    var sb = document.getElementById('successBanner');
                    sb.style.display = 'block';
                    sb.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                };

                window.dcDownload = function() {
                    var lines = [
                        'NUTRIBUDDY â€” 7-DAY PERSONALIZED DIET CHART',
                        '='.repeat(46),
                        'Child: ' + (dcState.childName || 'N/A') + ' | Age: ' + (dcState.age || 'N/A') +
                        ' yrs | Diet: ' + (dcState.pref || 'N/A'),
                        'Goals: ' + dcState.problems.join(', '),
                        'Height: ' + (dcState.height || 'N/A') + ' cm | Weight: ' + (dcState.weight || 'N/A') +
                        ' kg',
                        '',
                        'Day 1 & Day 2 shown on screen.',
                        'Day 3â€“7: Personalized plan sent to your email.',
                        '',
                        'Thank you for choosing NutriBuddy Kids!',
                        'Generated by NutriBuddy â€” nutribuddy.in'
                    ].join('\n');
                    var blob = new Blob([lines], {
                        type: 'text/plain'
                    });
                    var a = document.createElement('a');
                    a.href = URL.createObjectURL(blob);
                    a.download = 'NutriBuddy_7Day_Diet_Chart.txt';
                    a.click();
                };

                /* â”€â”€ Restart â”€â”€ */
                window.dcRestart = function() {
                    dcState = {
                        childName: null,
                        age: null,
                        gender: null,
                        problems: [],
                        pref: null,
                        allergies: [],
                        height: null,
                        weight: null,
                        plan: 'pro'
                    };
                    document.querySelectorAll('.age-card, .gender-card, .prob-tag, .dpref, .atag').forEach(el => el
                        .classList.remove('selected'));
                    document.getElementById('childNameInput').value = '';
                    document.getElementById('heightInput').value = '';
                    document.getElementById('weightInput').value = '';
                    document.getElementById('lockBanner').style.display = 'block';
                    document.getElementById('successBanner').style.display = 'none';
                    dcGoStep(1);
                };

                window.goStep = window.dcGoStep;
                window.generateChart = window.dcGenerateChart;
                window.restart = window.dcRestart;
                window.printChart = window.dcDownload;

            })();
        </script>
    @endpush

@endsection
