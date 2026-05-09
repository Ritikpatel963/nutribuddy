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

    /* SECTION */
    .aj-trust-section {
      background: linear-gradient(145deg, #FFF0FA 0%, #F0E5FF 50%, #FFDCF0 100%);
      padding: 80px 5%;
      font-family: 'DM Sans', sans-serif;
    }

    /* CONTAINER */
    .aj-trust-container {
      max-width: 1200px;
      margin: auto;
    }

    /* DESCRIPTION */
    .aj-trust-desc {
      text-align: center;
      max-width: 900px;
      margin: 0 auto 40px;
      color: #4A4A5A;
      line-height: 1.75;
      font-size: clamp(.95rem, 1.1vw, 1.08rem);
    }

    /* FLEX LAYOUT */
    .aj-trust-flex {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 50px;
    }

    /* GRID */
    .aj-trust-grid {
      flex: 1;
      display: grid;
      grid-template-columns: repeat(5, 1fr);
      gap: 10px;
    }

    /* IMAGES */
    .aj-trust-grid img {
      width: 100%;
      height: 100%;
      border-radius: 8px;
      object-fit: cover;
      box-shadow: 0 10px 26px rgba(13, 0, 32, .08);
    }

    /* MASONRY EFFECT */
    .aj-trust-grid img:nth-child(3n) {
      grid-row: span 2;
    }

    .aj-trust-grid img:nth-child(5n) {
      grid-row: span 1;
    }

    /* RIGHT CONTENT */
    .aj-trust-content {
      flex: 1;
      text-align: center;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      gap: 8px;
      min-width: 280px;
      padding: 0 10px;
    }

    /* ICON */
    .aj-trust-icon {
      width: 90px;
      height: 90px;
      background: var(--pk);
      border-radius: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      font-size: 38px;
      margin: 0 auto 16px;
      box-shadow: 0 20px 44px rgba(255, 77, 143, .32);
      border: 2.5px solid rgba(255, 214, 232, .9);
    }

    /* TITLE */
    .aj-trust-title {
      font-family: 'Nunito', sans-serif;
      letter-spacing: 2.2px;
      text-transform: uppercase;
      color: var(--pkd);
      font-weight: 900;
      font-size: .83rem;
      margin: 0 0 14px 0;
      line-height: 1.2;
    }

    /* MAIN HEADLINE */
    .aj-trust-highlight {
      font-family: 'Fredoka One';
      font-size: clamp(1.6rem, 4.2vw, 4.8rem);
      font-weight: 400;
      margin: 0 0 4px 0;
      color: var(--dk);
      line-height: 1;
      letter-spacing: -1px;
    }

    /* SUBHEADING */
    .aj-trust-content .aj-trust-subtitle {
      font-family: 'Fredoka one', cursive;
      font-size: clamp(1.8rem, 3.2vw, 2.2rem);
      font-weight: 700;
      margin: 2px 0 18px 0;
      color: var(--dk);
      line-height: 1.1;
      letter-spacing: -0.5px;
    }

    /* STATS */
    .aj-trust-stats {
      display: flex;
      justify-content: center;
      align-items: center;
      flex-wrap: wrap;
      gap: 32px;
      margin-top: 8px;
    }

    .aj-trust-stat {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 4px;
      min-width: 85px;
    }

    .aj-trust-stat strong {
      font-family: 'DM Sans', sans-serif;
      font-size: clamp(1.1rem, 2vw, 1.4rem);
      font-weight: 900;
      color: var(--dk);
      letter-spacing: -0.5px;
      line-height: 1;
    }

    .aj-trust-stat span {
      font-family: 'Nunito', sans-serif;
      font-size: clamp(.8rem, 1.2vw, .95rem);
      color: var(--muted);
      line-height: 1.3;
      font-weight: 500;
    }

    /* RESPONSIVE */
    @media (max-width: 900px) {
      .aj-trust-flex {
        flex-direction: column;
        gap: 24px;
      }

      .aj-trust-icon {
        width: 80px;
        height: 80px;
        font-size: 32px;
        margin-bottom: 12px;
      }

      .aj-trust-stats {
        gap: 28px;
      }
    }

    @media (max-width: 600px) {
      .aj-trust-content {
        gap: 6px;
      }

      .aj-trust-icon {
        width: 72px;
        height: 72px;
        font-size: 28px;
        margin-bottom: 8px;
      }

      .aj-trust-title {
        font-size: .78rem;
        letter-spacing: 1.8px;
        margin-bottom: 8px;
      }

      .aj-trust-highlight {
        font-size: 2.2rem;
        margin-bottom: 0;
      }

      .aj-trust-content .aj-trust-subtitle {
        font-size: 1.4rem;
        margin: 6px 0 12px 0;
      }

      .aj-trust-stats {
        gap: 24px;
        margin-top: 0;
      }

      .aj-trust-stat strong {
        font-size: 1rem;
      }

      .aj-trust-stat span {
        font-size: .75rem;
      }
    }

    /* RESPONSIVE */
    @media (max-width: 1024px) {
      .aj-trust-flex {
        flex-direction: column;
      }

      .aj-trust-grid {
        grid-template-columns: repeat(4, 1fr);
      }
    }

    @media (max-width: 600px) {
      .aj-trust-section {
        padding: 60px 5%;
      }

      .aj-trust-grid {
        grid-template-columns: repeat(3, 1fr);
      }

      .aj-trust-highlight {
        font-size: clamp(1.7rem, 7vw, 2.2rem);
      }

      .aj-trust-desc {
        margin-bottom: 28px;
      }
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

      .why-section,
      .closing-section {
        padding: 60px 5%;
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

      .wc {
        padding: 18px 16px;
      }
    }

    .ns-story-hero {
      --ns-pink-hot: #e91e8c;
      --ns-navy: #1a1464;
      --ns-purple: #7c5cbf;
      --ns-gold: #f5a623;
      --ns-green: #4caf50;
      --ns-font: 'DM Sans', sans-serif;
      --ns-heading: 'Fredoka One', cursive;
      --ns-hand: 'Nunito', sans-serif;
      font-family: var(--ns-font);
      background: linear-gradient(160deg, #fce4ec 0%, #fad4e4 55%, #f8bbd0 100%);
      overflow-x: hidden;
    }

    .ns-story-hero .ns-wrap {
      margin: 0 auto;
      position: relative;
    }

    .ns-story-hero .ns-section {
      position: relative;
      padding: 130px 190px 0;
      overflow: hidden;
    }

    .ns-story-hero .ns-wave {
      position: absolute;
      bottom: 0;
      left: -5%;
      right: -5%;
      pointer-events: none;
      z-index: 0;
    }

    .ns-story-hero .ns-grid {
      display: grid;
      grid-template-columns: 436px 1fr;
      gap: 32px;
      align-items: flex-start;
      position: relative;
      z-index: 2;
    }

    .ns-story-hero .ns-left {
      display: flex;
      flex-direction: column;
      gap: 18px;
      padding-bottom: 130px;
      position: relative;
    }

    .ns-story-hero .ns-tag {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      width: max-content;
      background: #fff;
      border: 1.5px solid #f0b8d0;
      border-radius: 999px;
      padding: 6px 18px 6px 12px;
      font-family: var(--ns-hand);
      font-size: .78rem;
      font-weight: 800;
      color: var(--ns-navy);
      letter-spacing: 1.8px;
      text-transform: uppercase;
      box-shadow: 0 2px 10px rgba(233, 30, 140, .1);
    }

    .ns-story-hero .ns-headline {
      font-family: var(--ns-heading);
      font-size: 53px;
      font-weight: 300;
      line-height: 1.08;
      color: var(--ns-navy);
      letter-spacing: 0;
    }

    .ns-story-hero .ns-headline-accent {
      color: var(--ns-pink-hot);
      font-style: normal;
      position: relative;
      display: inline-block;
    }

    .ns-story-hero .ns-headline-accent::after {
      content: '\2726';
      font-style: normal;
      font-size: 18px;
      color: var(--ns-pink-hot);
      margin-left: 5px;
      vertical-align: super;
      font-weight: 400;
    }

    .ns-story-hero .ns-divider {
      width: 52px;
      height: 4px;
      background: var(--ns-pink-hot);
      border-radius: 4px;
    }

    .ns-story-hero .ns-body {
      font-family: var(--ns-font);
      font-size: 1.08rem;
      line-height: 1.75;
      color: #4A4A5A;
      max-width: 400px;
    }

    .ns-story-hero .ns-deco-heart-top img {
      height: 55px;
    }

    .ns-story-hero .ns-deco-heart-top {
      position: absolute;
      top: -4px;
      left: 100%;
      pointer-events: none;
      z-index: 3;
      animation: ns-pulse 2s ease-in-out infinite;
    }

    @keyframes ns-pulse {

      0%,
      100% {
        transform: scale(1);
      }

      50% {
        transform: scale(1.2);
      }
    }

    .ns-story-hero .ns-mascot-wrap {
      position: absolute;
      bottom: 0;
      left: 447px;
      z-index: 10;
      width: 197px;
    }

    .ns-story-hero .ns-mascot-img {
      width: 100%;
      filter: drop-shadow(0 10px 22px rgba(124, 92, 191, .3));
      animation: ns-float 3s ease-in-out infinite;
    }

    @keyframes ns-float {

      0%,
      100% {
        transform: translateY(0);
      }

      50% {
        transform: translateY(-9px);
      }
    }

    .ns-story-hero .ns-right {
      position: relative;
      height: 440px;
    }

    .ns-story-hero .ns-tape {
      position: absolute;
      top: -10px;
      left: 50%;
      transform: translateX(-50%) rotate(-2deg);
      width: 54px;
      height: 17px;
      background: rgba(255, 255, 170, .8);
      border-radius: 2px;
      z-index: 5;
    }

    .ns-story-hero .ns-polar {
      position: absolute;
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 8px 28px rgba(0, 0, 0, .14);
    }

    .ns-story-hero .ns-polar img {
      display: block;
      width: 100%;
      object-fit: cover;
      border-radius: 6px 6px 0 0;
    }

    .ns-story-hero .ns-polar-caption {
      font-family: var(--ns-hand);
      font-size: .88rem;
      font-weight: 700;
      color: #444;
      text-align: center;
      padding: 8px 10px 10px;
      line-height: 1.3;
    }

    .ns-story-hero .ns-polar--worry {
      width: 192px;
      top: 10px;
      left: 120px;
      transform: rotate(-7deg);
      z-index: 3;
      padding: 8px 8px 0;
    }

    .ns-story-hero .ns-polar--worry img {
      height: 178px;
    }

    .ns-story-hero .ns-polar--happy {
      width: 305px;
      top: 28px;
      right: 14px;
      transform: rotate(2.5deg);
      z-index: 4;
      padding: 8px 8px 0;
    }

    .ns-story-hero .ns-polar--happy img {
      height: 268px;
    }

    .ns-story-hero .ns-arrow-svg {
      position: absolute;
      top: 198px;
      left: 192px;
      z-index: 6;
      pointer-events: none;
    }

    .ns-story-hero .ns-promise {
      position: absolute;
      bottom: 22px;
      right: 8px;
      background: #fffde7;
      border-radius: 3px;
      padding: 14px 16px;
      width: 188px;
      font-family: var(--ns-font);
      font-size: .92rem;
      line-height: 1.7;
      color: #444;
      box-shadow: 2px 4px 14px rgba(0, 0, 0, .13);
      z-index: 7;
      transform: rotate(1.8deg);
    }

    .ns-story-hero .ns-promise::before {
      content: '';
      position: absolute;
      top: -9px;
      left: 50%;
      transform: translateX(-50%);
      width: 44px;
      height: 9px;
      background: rgba(255, 235, 59, .8);
      border-radius: 2px;
    }

    .ns-story-hero .ns-deco {
      position: absolute;
      pointer-events: none;
      z-index: 8;
      font-size: 18px;
    }

    .ns-story-hero .ns-deco--hrt1 {
      top: 14px;
      left: 222px;
      color: var(--ns-pink-hot);
      font-size: 22px;
    }

    .ns-story-hero .ns-deco--star1 {
      top: 22px;
      right: 14px;
      color: var(--ns-gold);
      font-size: 16px;
    }

    .ns-story-hero .ns-deco--star2 {
      top: 62px;
      left: 38px;
      color: var(--ns-purple);
      font-size: 13px;
    }

    .ns-story-hero .ns-deco--plus1 {
      bottom: 202px;
      left: 58px;
      color: var(--ns-purple);
      font-size: 20px;
      font-weight: 900;
    }

    .ns-story-hero .ns-deco--leaf {
      bottom: 52px;
      right: 200px;
      font-size: 28px;
      transform: rotate(30deg);
    }

    .ns-story-hero .ns-gummy {
      position: absolute;
      pointer-events: none;
      z-index: 1;
    }

    .ns-story-hero .ns-gummy--bear {
      width: 60px;
      bottom: 88px;
      left: 10px;
    }

    .ns-story-hero .ns-gummy--bear img {
      height: 65px;
    }

    .ns-story-hero .ns-gummy--berry {
      width: 52px;
      bottom: 60px;
      right: 14px;
    }

    .for-left img {
      height: 56px;
    }

    .ns-story-hero .ns-gummy--berry svg {
      width: 100%;
      height: auto;
      display: block;
    }

    .ns-story-hero .ns-star-deco {
      position: absolute;
      bottom: 112px;
      left: 74px;
      color: var(--ns-pink-hot);
      font-size: 18px;
      pointer-events: none;
      z-index: 2;
    }

    .ns-story-hero .ns-stats-outer {
      position: relative;
      z-index: 10;
    }

    .ns-story-hero .ns-stats {
      background: #fff;
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      padding: 28px 210px;
      gap: 8px;
      border-radius: 0;
    }

    .ns-story-hero .ns-stat {
      display: flex;
      align-items: center;
      gap: 14px;
      border-right: 1px solid #f0dce8;
      padding: 0 20px;
    }

    .ns-story-hero .ns-stat:first-child {
      padding-left: 0;
    }

    .ns-story-hero .ns-stat:last-child {
      border-right: none;
    }

    .ns-story-hero .ns-stat-icon {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 22px;
      flex-shrink: 0;
    }

    .ns-story-hero .ns-stat-icon--pink {
      background: #fde8f0;
    }

    .ns-story-hero .ns-stat-icon--purple {
      background: #ede7f6;
    }

    .ns-story-hero .ns-stat-icon--green {
      background: #e8f5e9;
    }

    .ns-story-hero .ns-stat-icon--gold {
      background: #fff8e1;
    }

    .ns-story-hero .ns-stat-val {
      font-family: var(--ns-heading);
      font-size: 2rem;
      font-weight: 400;
      color: var(--ns-navy);
      line-height: 1;
    }

    .ns-story-hero .ns-stat-lbl {
      font-family: var(--ns-hand);
      font-size: .76rem;
      font-weight: 800;
      color: #999;
      margin-top: 3px;
    }

    @media(max-width:960px) {
     
      .ns-story-hero .ns-section {
        padding: 96px 28px 0;
      }

      .ns-story-hero .ns-grid {
        grid-template-columns: 1fr;
        gap: 0;
      }

      .ns-story-hero .ns-left {
        padding-bottom: 90px;
      }

      .ns-story-hero .ns-right {
        height: 370px;
        margin-top: 16px;
      }

      .ns-story-hero .ns-polar--worry {
        width: 162px;
      }

      .ns-story-hero .ns-polar--worry img {
        height: 150px;
      }

      .ns-story-hero .ns-polar--happy {
        width: 252px;
      }

      .ns-story-hero .ns-polar--happy img {
        height: 220px;
      }

      .ns-story-hero .ns-mascot-wrap {
        left: 160px;
        width: 128px;
      }

      .ns-story-hero .ns-stats {
        padding: 20px 24px;
      }
    }

    @media(max-width:640px) {
      .ns-story-hero .ns-section {
        padding: 88px 16px 0;
      }

      .ns-story-hero .ns-right {
        height: 310px;
      }

      .ns-story-hero .ns-polar--worry {
        width: 132px;
        left: 6px;
      }

      .ns-story-hero .ns-polar--worry img {
        height: 120px;
      }

      .ns-story-hero .ns-polar--happy {
        width: 200px;
        right: 6px;
      }

      .ns-story-hero .ns-polar--happy img {
        height: 172px;
      }

      .ns-story-hero .ns-promise {
        width: 148px;
        font-size: 13px;
        bottom: 10px;
        right: 4px;
      }

      .ns-story-hero .ns-mascot-wrap {
        left: 120px;
        width: 108px;
      }

      .ns-story-hero .ns-stats {
        grid-template-columns: repeat(2, 1fr);
        padding: 16px;
      }

      .ns-story-hero .ns-stat {
        border-right: none;
        padding: 8px 0;
        border-bottom: 1px solid #f0dce8;
      }

      .ns-story-hero .ns-stat:nth-child(odd) {
        border-right: 1px solid #f0dce8;
        padding-right: 12px;
      }

      .ns-story-hero .ns-stat:nth-child(3),
      .ns-story-hero .ns-stat:nth-child(4) {
        border-bottom: none;
      }

      .ns-story-hero .ns-gummy--bear {
        width: 42px;
        bottom: 15px;
      }

      .ns-story-hero .ns-gummy--berry {
        width: 50px;
        bottom: 235px;
      }
    }

    @media(max-width:420px) {
      .ns-story-hero .ns-headline {
        font-size: 26px;
      }

      .ns-story-hero .ns-right {
        height: 264px;
      }

      .ns-story-hero .ns-polar--happy {
        width: 170px;
      }

      .ns-story-hero .ns-polar--happy img {
        height: 144px;
      }

      .ns-story-hero .ns-polar--worry {
        width: 112px;
      }

      .ns-story-hero .ns-polar--worry img {
        height: 100px;
      }

      .ns-story-hero .ns-promise {
        display: none;
      }

      .ns-story-hero .ns-mascot-wrap {
        left: 90px;
        width: 96px;
      }
    }


    /* ============================================================
     SECTION & BACKGROUNDS 2nd
  =========================================================== */
    .nbs-section {
      position: relative;
      padding: 80px 20px;
      font-family: 'DM Sans', sans-serif;
      background-image: radial-gradient(circle at 50% 0%, #ffffff 0%, transparent 70%);
    }

    /* Background Blob Graphics */
    .nbs-bg-shapes {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      overflow: hidden;
      pointer-events: none;
      z-index: 0;
    }
.nbs-bg-shapes img {
  height: 85px;
}
    .nbs-blob-tl {
      position: absolute;
      top: -100px;
      left: -100px;
      width: 500px;
      height: 500px;
      background: var(--pkl);
      border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
      opacity: 0.7;
      filter: blur(40px);
    }

    .nbs-blob-br {
      position: absolute;
      bottom: -150px;
      right: -50px;
      width: 600px;
      height: 500px;
      background: var(--pkl);
      border-radius: 60% 40% 30% 70% / 50% 60% 40% 50%;
      opacity: 0.9;
    }

    /* Leaf decos in background */
    .nbs-bg-leaf {
      position: absolute;
      opacity: 0.5;
    }

    .nbs-bg-leaf-1 {
      top: 10%;
      right: 15%;
      width: 40px;
      transform: rotate(15deg);
    }

    .nbs-bg-leaf-2 {
      bottom: 15%;
      right: 5%;
      width: 60px;
      transform: rotate(-20deg);
      opacity: 0.3;
    }

    .nbs-container {
      max-width: 1100px;
      margin: 0 auto;
      position: relative;
      z-index: 2;
    }

    /* ============================================================
     HEADER TAGS
  =========================================================== */
    .nbs-our-story-tag {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-family: 'Nunito', sans-serif;
      font-size: 14px;
      font-weight: 800;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      color: var(--pk);
      margin-bottom: 40px;
    }

    .nbs-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-family: 'Nunito', sans-serif;
      background: var(--pul);
      color: var(--pu);
      font-size: 12px;
      font-weight: 800;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      padding: 8px 16px;
      border-radius: 50px;
      margin-bottom: 20px;
    }

    /* ============================================================
     MAIN GRID
  =========================================================== */
    .nbs-main-grid {
      display: grid;
      grid-template-columns: 45% 50%;
      gap: 5%;
      align-items: start;
    }

    /* ============================================================
     LEFT – IMAGE COLUMN
  =========================================================== */
    .nbs-img-col {
      position: relative;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    /* polaroid card */
    .nbs-photo-card {
      background: #fff;
      border-radius: 12px;
      padding: 10px 10px 25px 10px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
      position: relative;
      width: 80%;
    }

    .nbs-photo-fill {
      border-radius: 8px;
      width: 100%;
      aspect-ratio: 16/10;
      overflow: hidden;
      background: #eee;
    }

    .nbs-photo-fill img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    /* Tape effect */
    .nbs-tape {
      position: absolute;
      width: 40px;
      height: 16px;
      background: rgba(255, 255, 255, 0.7);
      backdrop-filter: blur(4px);
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
      border-radius: 2px;
      z-index: 10;
    }

    /* Colored tape variants */
    .nbs-tape-pu {
      background: rgba(135, 75, 222, 0.4);
    }

    .nbs-tape-ye {
      background: rgba(255, 201, 51, 0.4);
    }

    .nbs-card-1 {
      transform: rotate(-3deg) translateX(-10%);
      z-index: 3;
    }

    .nbs-card-1 .nbs-tape {
      top: -6px;
      right: 20px;
      transform: rotate(5deg);
    }

    .nbs-card-2 {
      transform: rotate(2deg) translateX(10%);
      z-index: 4;
      margin-top: -30px;
    }

    .nbs-card-2 .nbs-tape {
      top: -6px;
      left: 20px;
      transform: rotate(-5deg);
    }

    .nbs-card-3 {
      transform: rotate(-1deg);
      z-index: 3;
      margin-top: -20px;
    }

    .nbs-card-3 .nbs-tape {
      top: -6px;
      right: 40px;
      transform: rotate(3deg);
    }

    /* Decorative dotted lines / arrows */
    .nbs-arrow-svg {
      position: absolute;
      z-index: 1;
      pointer-events: none;
      overflow: visible;
    }

    .nbs-arrow-1 {
      top: 28%;
      left: -5%;
      width: 80px;
      height: 100px;
    }

    .nbs-arrow-2 {
      top: 58%;
      left: -5%;
      width: 100px;
      height: 120px;
    }

    /* Gummy bears */
    .nbs-gummies {
      position: absolute;
      bottom: 120px;
      left: -30px;
      display: flex;
      align-items: flex-end;
      gap: 5px;
      z-index: 6;
      pointer-events: none;
    }

    .nbs-gummy {
      font-size: 40px;
      animation: nbsFloat 4s ease-in-out infinite;
      display: block;
      filter: drop-shadow(0 5px 5px rgba(0, 0, 0, 0.1));
    }

    .nbs-gummy:nth-child(2) {
      font-size: 30px;
      animation-delay: 1.2s;
      margin-bottom: 10px;
    }

    @keyframes nbsFloat {

      0%,
      100% {
        transform: translateY(0);
      }

      50% {
        transform: translateY(-10px);
      }
    }

    /* ============================================================
     RIGHT – CONTENT COLUMN
  =========================================================== */
    .nbs-content-col {
      position: relative;
      padding-top: 0px;
    }

    /* Heading */
    .nbs-heading {
      font-family: 'Fredoka One', cursive;
      font-weight: 900;
      font-size: clamp(36px, 4vw, 56px);
      line-height: 1.15;
      color: var(--dk);
      margin-bottom: 12px;
      letter-spacing: -0.5px;
    }

    .nbs-heading .nbs-hd-pink {
      color: var(--pk);
      display: block;
    }

    /* Yellow accent underline */
    .nbs-yellow-acc {
      width: 60px;
      height: 4px;
      background: var(--ye);
      border-radius: 4px;
      margin-bottom: 40px;
    }

    /* ── STEPS TIMELINE ── */
    .nbs-steps {
      display: flex;
      flex-direction: column;
      position: relative;
    }

    /* The vertical dashed line behind the icons */
    .nbs-steps::before {
      content: '';
      position: absolute;
      top: 20px;
      bottom: 40px;
      left: 42px;
      /* Center of the 48px icons */
      width: 2px;
      border-left: 2px dashed #DCCFE0;
      z-index: 0;
    }

    .nbs-step {
      display: flex;
      gap: 24px;
      align-items: flex-start;
      margin-bottom: 28px;
      position: relative;
      z-index: 1;
    }

    .nbs-step-icon {
      width: 83px;
      height: 83px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      background: #fff;
      /* Box shadow for depth like the image */
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
    }

    .nbs-step-icon img,
    .nbs-step-icon svg {
      width: 75px;
      height: 75px;
      object-fit: contain;
    }

    .nbs-step-body {
      padding-top: 10px;
      font-family: 'DM Sans', sans-serif;
      font-size: 15px;
      line-height: 1.6;
      color: var(--text);
      font-weight: 600;
    }

    /* icon bg colors */
    .nbs-ic-pk {
      background: var(--pkl);
    }

    .nbs-ic-ye {
      background: #f5e6c1;
    }

    .nbs-ic-pu {
      background: #f0e2f9;
    }

    .nbs-ic-mn {
      background: #e7f1d6;
    }

    /* inline highlights */
    .nbs-hl-pk {
      color: var(--pk);
      font-weight: 800;
    }

    .nbs-hl-pu {
      color: var(--pu);
      font-weight: 800;
    }

    .nbs-hl-mn {
      color: var(--mn);
      font-weight: 800;
    }

    .nbs-hl-ye {
      color: #d19a00;
      font-weight: 800;
    }

    /* ============================================================
     STATS BAR
  =========================================================== */
    .nbs-stats-bar {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      background: var(--wh);
      border-radius: 20px;
      padding: 24px 32px;
      margin-top: 50px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
      position: relative;
      z-index: 5;
    }

    .nbs-stat-item {
      display: flex;
      align-items: center;
      gap: 16px;
      padding: 0 20px;
      border-right: 1.5px solid var(--border);
    }

    .nbs-stat-item:first-child {
      padding-left: 0;
    }

    .nbs-stat-item:last-child {
      border-right: none;
      padding-right: 0;
    }

    .nbs-stat-ico {
      width: 75px;
      height: 75px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .nbs-stat-ico img,
    .nbs-stat-ico svg {
      width: 60px;
      height: 60px;
    }

    .nbs-si-pk {
      background: var(--pkl);
    }

    .nbs-si-pu {
      background: var(--pul);
    }

    .nbs-si-mn {
      background: var(--mnl);
    }

    .nbs-si-ye {
      background: var(--yel);
    }

    .nbs-stat-text {
      display: flex;
      flex-direction: column;
    }

    .nbs-stat-num {
      font-family: 'DM Sans', sans-serif;
      font-size: 24px;
      font-weight: 800;
      line-height: 1.2;
    }

    .nbs-sn-pk {
      color: var(--pk);
    }

    .nbs-sn-pu {
      color: var(--pu);
    }

    .nbs-sn-mn {
      color: var(--mn);
    }

    .nbs-sn-ye {
      color: #d19a00;
    }

    .nbs-stat-lbl {
      font-size: 13px;
      font-weight: 700;
      color: var(--text);
    }

    /* ============================================================
     RESPONSIVE
  =========================================================== */
    @media (max-width: 980px) {
      .nbs-main-grid {
        grid-template-columns: 1fr;
        gap: 60px 0;
      }

      .nbs-img-col {
        max-width: 500px;
        margin: 0 auto;
        width: 100%;
        padding-top: 0;
      }

      .nbs-arrow-1 {
        left: -10%;
      }

      .nbs-arrow-2 {
        left: -10%;
      }

      .nbs-stats-bar {
        grid-template-columns: repeat(2, 1fr);
        gap: 30px 0;
        padding: 30px;
      }

      .nbs-stat-item {
        padding: 0 10px;
        border-right: none;
      }

      .nbs-stat-item:nth-child(odd) {
        border-right: 1.5px solid var(--border);
      }

      .nbs-gummies {
        bottom: 0;
        left: 0;
      }
    }

    @media (max-width: 600px) {
      .nbs-section {
        padding: 50px 16px;
      }

      .nbs-heading {
        font-size: 32px;
      }

      .nbs-step-body {
        font-size: 14px;
      }

      .nbs-photo-card {
        width: 90%;
      }

      .nbs-stats-bar {
        grid-template-columns: 1fr;
        gap: 20px 0;
        padding: 24px;
      }

      .nbs-stat-item:nth-child(odd) {
        border-right: none;
      }

      .nbs-stat-item {
        padding: 0;
        border-bottom: 1.5px solid var(--border);
        padding-bottom: 20px;
      }

      .nbs-stat-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
      }
    }





    /* third */
    /* ── SECTION ── */
    .origin-section {
      padding: 80px 5%;
      background: var(--cr);
      position: relative;
      overflow: hidden;
    }

    .origin-grid {
      max-width: 1200px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 70px;
      align-items: stretch;
    }

    /* ── LEFT: Dark Story Card ── */
    .origin-visual {
      position: relative;
      display: flex;
      align-items: stretch;
    }

    .story-card-main {
      background: linear-gradient(145deg, var(--dk2), #2d0060);
      border-radius: 32px;
      padding: 44px 40px;
      position: relative;
      overflow: hidden;
      box-shadow: 0 24px 60px rgba(13, 0, 32, 0.25);
      display: flex;
      flex-direction: column;
      justify-content: center;
      width: 100%;
    }

    .story-card-main::before {
      content: '';
      position: absolute;
      top: -60px;
      right: -60px;
      width: 200px;
      height: 200px;
      border-radius: 50%;
      background: rgba(124, 58, 237, 0.15);
      pointer-events: none;
    }

    .story-card-main .eyebrow {
      display: block;
      margin-bottom: 14px;
      font-size: 0.72rem;
      font-weight: 900;
      letter-spacing: 2px;
      text-transform: uppercase;
      color: var(--ye);
    }

    .story-card-main h2 {
      margin-bottom: 16px;
      font-family: 'Fredoka One', cursive;
      font-size: clamp(1.6rem, 2.5vw, 2.1rem);
      line-height: 1.25;
      color: #fff;
    }

    .story-card-main p {
      margin-bottom: 14px;
      font-size: 0.96rem;
      line-height: 1.8;
      color: rgba(255, 255, 255, 0.72);
    }

    .story-pills {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-top: 22px;
    }

    .spill {
      padding: 8px 15px;
      border: 1px solid rgba(255, 255, 255, 0.14);
      border-radius: 50px;
      background: rgba(255, 255, 255, 0.08);
      font-size: 0.75rem;
      font-weight: 800;
      color: rgba(255, 255, 255, 0.85);
    }

    /* Float badges */
    .float-accent {
      position: absolute;
      z-index: 5;
      padding: 12px 18px;
      border-radius: 18px;
      background: #fff;
      box-shadow: 0 10px 32px rgba(0, 0, 0, 0.12);
      font-size: 0.82rem;
      font-weight: 900;
      color: var(--dk);
      white-space: nowrap;
    }

    .fa-1 {
      top: -18px;
      right: -24px;
      border: 2px solid #fde8f3;
    }

    .fa-2 {
      bottom: -18px;
      left: -24px;
      border: 2px solid #e8f0ff;
    }

    /* ── RIGHT: Accordion Side ── */
    .origin-text {
      display: flex;
      flex-direction: column;
    }

    .sec-eye {
      display: block;
      margin-bottom: 10px;
      font-size: 0.85rem;
      font-weight: 900;
      letter-spacing: 2.5px;
      text-transform: uppercase;
      color: var(--pk);
    }

    .origin-text h2 {
      margin-bottom: 12px;
      font-family: 'Fredoka One', cursive;
      font-size: clamp(2rem, 3vw, 2.75rem);
      line-height: 1.1;
      color: var(--dk);
    }

    .origin-text h2 .acc {
      color: var(--pu);
    }

    .origin-text>p {
      margin-bottom: 24px;
      font-size: 0.95rem;
      line-height: 1.75;
      color: #666;
    }

    /* ── ACCORDION ── */
    .accordion-list {
      display: flex;
      flex-direction: column;
      gap: 10px;
      list-style: none;
    }

    .acc-item {
      background: #fff;
      border: 1.5px solid #ede8f5;
      border-radius: 20px;
      overflow: hidden;
      transition: border-color 0.25s ease, box-shadow 0.25s ease;
    }

    .acc-item.open {
      border-color: #e0d3f8;
      box-shadow: 0 6px 24px rgba(124, 58, 237, 0.08);
    }

    .acc-item.open .acc-header {
      background: #fdf7ff;
    }

    .acc-header {
      display: flex;
      align-items: center;
      gap: 14px;
      padding: 16px 20px;
      cursor: pointer;
      user-select: none;
      transition: background 0.2s ease;
      border-radius: 18px;
    }

    .acc-header:hover {
      background: #fdf7ff;
    }
    .acc-icon-wrap img {
      width: 80%;
      height: 80%;
      object-fit: contain;
    }
    .acc-icon-wrap {
      width: 44px;
      height: 44px;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      font-size: 20px;
      transition: transform 0.2s ease;
    }

    .acc-item.open .acc-icon-wrap {
      transform: scale(1.08);
    }

    .acc-title {
      flex: 1;
      font-size: 0.97rem;
      font-weight: 900;
      color: var(--dk);
    }

    .acc-toggle {
      width: 30px;
      height: 30px;
      border-radius: 50%;
      border: 1.5px solid #e0d3f8;
      background: #f5f0ff;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      transition: background 0.2s ease, transform 0.3s ease;
      color: var(--pu);
    }

    .acc-item.open .acc-toggle {
      background: var(--pu);
      border-color: var(--pu);
      transform: rotate(45deg);
      color: #fff;
    }

    .acc-toggle svg {
      width: 14px;
      height: 14px;
      stroke: currentColor;
      fill: none;
      stroke-width: 2.5;
      stroke-linecap: round;
    }

    .acc-body {
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.35s cubic-bezier(0.4, 0, 0.2, 1), padding 0.25s ease;
    }

    .acc-item.open .acc-body {
      max-height: 200px;
    }

    .acc-body-inner {
      padding: 0 20px 18px 78px;
    }

    .acc-body-inner ul {
      list-style: none;
      display: flex;
      flex-direction: column;
      gap: 7px;
    }

    .acc-body-inner ul li {
      font-size: 0.85rem;
      line-height: 1.55;
      color: #777;
      display: flex;
      align-items: flex-start;
      gap: 8px;
    }

    .acc-body-inner ul li::before {
      content: '';
      width: 7px;
      height: 7px;
      border-radius: 50%;
      background: var(--pk);
      flex-shrink: 0;
      margin-top: 5px;
    }

    .acc-body-inner ul li:nth-child(2)::before {
      background: var(--pu);
    }

    .acc-body-inner ul li:nth-child(3)::before {
      background: #22c55e;
    }

    /* ── RESPONSIVE ── */
    @media (max-width: 991px) {
     
      .origin-grid {
        grid-template-columns: 1fr;
        gap: 40px;
      }

      .origin-visual {
        order: -1;
      }

      .fa-1 {
        top: -12px;
        right: -8px;
      }

      .fa-2 {
        bottom: -12px;
        left: -8px;
      }
    }

    @media (max-width: 640px) {
        .proper-set {
      height: 32px !important;
    }
    .gapfix-footer-banner img {
    height: 12%;
    width: 73% !important;
}
      .origin-section {
        padding: 60px 5%;
      }

      .story-card-main {
        padding: 30px 24px;
        border-radius: 24px;
      }

      .origin-text h2 {
        font-size: 1.8rem;
      }

      .acc-header {
        padding: 14px 16px;
        gap: 12px;
      }

      .acc-icon-wrap {
        width: 38px;
        height: 38px;
        font-size: 17px;
        border-radius: 11px;
      }

      .acc-body-inner {
        padding: 0 16px 16px 66px;
      }

      .acc-title {
        font-size: 0.9rem;
      }

      .fa-1,
      .fa-2 {
        display: none;
      }
    }


    /* gap we found */

    /* ── SECTION ── */
    .gapfix-section {
      padding: 80px 20px 100px;
      font-family: 'DM Sans', sans-serif;
      background: linear-gradient(160deg, #fdf4ff 0%, #f0f7ff 40%, #f5fff9 100%);
      position: relative;
      overflow: hidden;
    }

    .gapfix-section::before {
      content: '';
      position: absolute;
      inset: 0;
      background:
        radial-gradient(ellipse 60% 40% at 10% 20%, rgba(255, 77, 143, .07) 0%, transparent 70%),
        radial-gradient(ellipse 50% 40% at 90% 80%, rgba(0, 214, 143, .08) 0%, transparent 70%);
      pointer-events: none;
    }

    /* ── HEADER ── */
    .gapfix-eyebrow {
      display: inline-block;
      font-family: 'Nunito', sans-serif;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: 2px;
      text-transform: uppercase;
      color: var(--pu);
      background: var(--pul);
      border: 1.5px solid #d4b9ff;
      padding: 5px 16px;
      border-radius: 50px;
      margin-bottom: 18px;
    }

    .gapfix-heading {
      font-family: 'Fredoka One', cursive;
      font-size: clamp(28px, 5vw, 52px);
      font-weight: 800;
      color: var(--dk);
      line-height: 1.15;
      margin-bottom: 14px;
    }
    .proper-set {
      height: 62px;
    }

    .gapfix-heading span {
      color: var(--pu);
    }

    .gapfix-subtext {
      font-size: clamp(14px, 2vw, 16px);
      color: var(--muted);
      max-width: 560px;
      margin: 0 auto 56px;
      line-height: 1.7;
    }

    .gapfix-top {
      text-align: center;
    }

    /* ── COLUMN LABELS ── */
    .gapfix-col-labels {
      display: flex;
      justify-content: space-between;
      max-width: 1100px;
      margin: 0 auto 20px;
      padding: 0 10px;
    }

    .gapfix-label {
      display: flex;
      align-items: center;
      gap: 8px;
      font-family: 'Nunito', sans-serif;
      font-size: 13px;
      font-weight: 700;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      padding: 7px 20px;
      border-radius: 50px;
    }

    .gapfix-label--problem {
      color: var(--pk);
      background: var(--pkl);
      border: 1.5px solid #ffb3d0;
    }

    .gapfix-label--fix {
      color: var(--mn);
      background: var(--mnl);
      border: 1.5px solid #7fe8c8;
    }

    .gapfix-label svg {
      flex-shrink: 0;
    }

    /* ── ROWS ── */
    .gapfix-rows {
      display: flex;
      flex-direction: column;
      gap: 20px;
      max-width: 1100px;
      margin: 0 auto;
    }

    .gapfix-row {
      display: grid;
      grid-template-columns: 1fr 56px 1fr;
      align-items: center;
      gap: 16px;
    }

    /* ── CARD ── */
    .gapfix-card {
      display: flex;
      align-items: center;
      gap: 18px;
      background: var(--white);
      border-radius: var(--r);
      padding: 22px 24px;
      box-shadow: 0 2px 16px rgba(13, 0, 32, .06);
      border: 1.5px solid var(--border);
      transition: transform .25s ease, box-shadow .25s ease;
    }

    .gapfix-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 30px rgba(13, 0, 32, .11);
    }

    /* ── ICON BOX ── */
    .gapfix-icon-box {
      flex-shrink: 0;
      width: 85px;
      height: 85px;
      border-radius: 18px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 30px;
    }

    .gapfix-icon-box--problem img {
      width: 75px;
      height: 75px;
    }

    .gapfix-icon-box--fix img {
      width: 75px;
      height: 75px;
    }

    .gapfix-icon-box--problem {
      background: var(--pkl);
    }

    .gapfix-icon-box--fix {
      background: var(--mnl);
    }

    /* ── BADGE NUMBER ── */
    .gapfix-num {
      flex-shrink: 0;
      width: 34px;
      height: 34px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Nunito', sans-serif;
      font-size: 13px;
      font-weight: 800;
      color: var(--white);
    }

    .gapfix-num--problem {
      background: var(--pk);
    }

    .gapfix-num--fix {
      background: var(--mn);
    }

    /* ── CARD TEXT ── */
    .gapfix-card-body {
      flex: 1;
      min-width: 0;
    }

    .gapfix-card-title {
      font-family: 'DM Sans', sans-serif;
      font-size: clamp(14px, 2vw, 16px);
      font-weight: 700;
      margin-bottom: 5px;
      color: var(--dk);
    }

    .gapfix-card-desc {
      font-size: clamp(12px, 1.5vw, 13.5px);
      color: var(--muted);
      line-height: 1.65;
    }

    /* zero-sugar badge */
    .gapfix-badge-tag {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      font-size: 12px;
      font-weight: 700;
      color: var(--mn);
      background: var(--mnl);
      border: 1.5px solid #7fe8c8;
      border-radius: 50px;
      padding: 3px 12px;
      margin-bottom: 5px;
    }

    /* ── ARROW ── */
    .gapfix-arrow {
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--pu);
      font-size: 24px;
    }

    /* ── FOOTER BANNER ── */
    .gapfix-footer-banner {
      max-width: 1100px;
      margin: 52px auto 0;
      background: linear-gradient(120deg, var(--yel) 0%, #fff9e0 100%);
      border: 2px solid var(--ye);
      border-radius: var(--rL);
      padding: 28px 40px;
      display: flex;
      align-items: center;
      gap: 24px;
    }
    .gapfix-footer-banner img {
          height: 12%;
    width: 22%;
    }

    .gapfix-footer-icon {
      font-size: 48px;
      flex-shrink: 0;
    }
 .gapfix-footer-icon img {
      width: 95px;
      height: 95px;
      object-fit: contain;
    }
    .gapfix-footer-text {
      font-family: 'DM Sans', sans-serif;
      font-size: clamp(16px, 3vw, 22px);
      font-weight: 700;
      color: var(--dk);
      line-height: 1.4;
    }

    .gapfix-footer-text em {
      font-style: normal;
      color: var(--pu);
    }

    /* ── RESPONSIVE ── */
    @media (max-width: 820px) {
      .gapfix-col-labels {
        display: none;
      }

      .gapfix-row {
        grid-template-columns: 1fr;
        gap: 10px;
        position: relative;
      }

      .gapfix-arrow {
        transform: rotate(90deg);
        margin: -4px auto;
      }

      .gapfix-footer-banner {
        flex-direction: column;
        text-align: center;
        padding: 28px 24px;
      }
    }

    @media (max-width: 520px) {
      .gapfix-section {
        padding: 48px 14px 60px;
      }

      .gapfix-card {
        padding: 16px;
        gap: 12px;
      }

      .gapfix-icon-box {
        width: 50px;
        height: 50px;
        font-size: 22px;
      }
    }


    /* approch section0 */
  </style>
@endpush

@section('content')
  <!-- ══════════════════════════════════════════
                               HERO
                          ══════════════════════════════════════════ -->
  <section class="ns-story-hero">
    <div class="ns-wrap">
      <section class="ns-section">

        <div class="ns-gummy ns-gummy--bear">
          <img src="{{ asset('img/yammi.png') }}" alt="Gummy bear">
        </div>

        <div class="ns-gummy ns-gummy--berry for-left">
          <img src="{{ asset('img/yammi.png') }}" alt="Gummy berry">
        </div>

        <span class="ns-star-deco">&#10022;</span>

        <div class="ns-grid">

          <div class="ns-left">
            <span class="ns-deco-heart-top">
              <img src="{{ asset('img/heart.png') }}" alt="">
            </span>

            <span class="ns-tag">OUR STORY</span>

            <h1 class="ns-headline">
              Every Great Idea<br>
              Starts With<br>
              A <span class="ns-headline-accent">Real Moment</span>
            </h1>

            <div class="ns-divider"></div>

            <p class="ns-body">
              NutriBuddy began not in a lab, but in a moment of worry.
              When our own child kept falling sick and nothing seemed
              to help, we realized the problem was not just with us.
              It was with the way kids eat, and what they don't get.
              So we decided to build the nutrition we wished
              our child had, and every child deserves.
            </p>

            <div class="ns-mascot-wrap">
              <img class="ns-mascot-img" src="{{ asset('img/NW.png') }}" alt="NutriBuddy mascot">
            </div>
          </div>

          <div class="ns-right">
            <span class="ns-deco ns-deco--hrt1">&hearts;</span>
            <span class="ns-deco ns-deco--star1">&#10022;</span>
            <span class="ns-deco ns-deco--star2">&#10022;</span>
            <span class="ns-deco ns-deco--plus1">&#10022;</span>
            <span class="ns-deco ns-deco--leaf">&#10022;</span>

            <div class="ns-polar ns-polar--worry">
              <div class="ns-tape"></div>
              <img src="https://images.unsplash.com/photo-1576765608535-5f04d1e3f289?w=320&q=80" alt="Worried child" />
              <div class="ns-polar-caption">The worry we felt</div>
            </div>

            <div class="ns-polar ns-polar--happy">
              <div class="ns-tape"></div>
              <img src="/img/mommi.jpeg" alt="Happy mother and child" />
              <div class="ns-polar-caption">The reason we started</div>
            </div>

            <svg class="ns-arrow-svg" width="72" height="58" viewBox="0 0 72 58" fill="none">
              <path d="M6 6 C18 6 24 36 54 48" stroke="#cc1177" stroke-width="2.2" stroke-dasharray="5.5 4.5"
                stroke-linecap="round" fill="none" />
              <path d="M47 54 L57 48 L51 39" stroke="#cc1177" stroke-width="2.2" stroke-linecap="round"
                stroke-linejoin="round" fill="none" />
            </svg>

            <div class="ns-promise">
              Our promise is simple <br>
              Real nutrition,<br>
              made for kids.<br>
              With love, always.
            </div>
          </div>
        </div>

        <svg class="ns-wave" viewBox="0 0 1440 110" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M0,65 C180,15 360,105 540,65 C720,25 900,105 1100,58 C1260,26 1380,78 1440,65 L1440,110 L0,110 Z"
            fill="rgba(255,255,255,.5)" />
          <path d="M0,82 C200,42 400,115 650,78 C850,50 1050,110 1250,75 C1340,59 1400,92 1440,84 L1440,110 L0,110 Z"
            fill="rgba(255,255,255,.85)" />
        </svg>
      </section>

      <div class="ns-stats-outer">
        <div class="ns-stats">

          <div class="ns-stat">
            <div class="ns-stat-icon ns-stat-icon--pink">
              <svg width="26" height="22" viewBox="0 0 26 22" fill="none">
                <circle cx="8" cy="8" r="4.5" fill="#e91e8c" opacity=".7" />
                <circle cx="18" cy="8" r="4.5" fill="#e91e8c" opacity=".7" />
                <ellipse cx="13" cy="17" rx="9" ry="5" fill="#e91e8c" />
                <ellipse cx="4" cy="17" rx="4" ry="4.5" fill="#e91e8c" opacity=".6" />
                <ellipse cx="22" cy="17" rx="4" ry="4.5" fill="#e91e8c" opacity=".6" />
              </svg>
            </div>
            <div>
              <div class="ns-stat-val">50K+</div>
              <div class="ns-stat-lbl">Happy Families</div>
            </div>
          </div>

          <div class="ns-stat">
            <div class="ns-stat-icon ns-stat-icon--purple">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path
                  d="M17 8C8 10 5.9 16.17 3.82 19.93 2.19 18.6 2 17.5 2 17.5S2.6 22 9 22c6.64 0 9-4.5 9-4.5S17.8 22 22 22C22 22 20.26 11.29 17 8z"
                  fill="#9c8abf" />
              </svg>
            </div>
            <div>
              <div class="ns-stat-val">12+</div>
              <div class="ns-stat-lbl">Expert Formulas</div>
            </div>
          </div>

          <div class="ns-stat">
            <div class="ns-stat-icon ns-stat-icon--green">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path
                  d="M17 8C8 10 5.9 16.17 3.82 19.93 2.19 18.6 2 17.5 2 17.5S2.6 22 9 22c6.64 0 9-4.5 9-4.5S17.8 22 22 22C22 22 20.26 11.29 17 8z"
                  fill="#4caf50" />
              </svg>
            </div>
            <div>
              <div class="ns-stat-val">100%</div>
              <div class="ns-stat-lbl">Natural Ingredients</div>
            </div>
          </div>

          <div class="ns-stat">
            <div class="ns-stat-icon ns-stat-icon--gold">
              <svg width="26" height="26" viewBox="0 0 26 26" fill="none">
                <path d="M13 2 L15.5 9.5 H23.5 L17.2 14.2 L19.7 21.7 L13 17 L6.3 21.7 L8.8 14.2 L2.5 9.5 H10.5 Z"
                  fill="#f5a623" />
              </svg>
            </div>
            <div>
              <div class="ns-stat-val">4.9</div>
              <div class="ns-stat-lbl">Parent Rating</div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </section>



  <section class="nbs-section">

    <!-- Background decorations -->
    <div class="nbs-bg-shapes">
      <div class="nbs-blob-tl"></div>
      <div class="nbs-blob-br"></div>
      <!-- Leaves -->
     <img src="img/btn-leafimg.png" alt="">
      
    </div>

    <div class="nbs-container">

      <!-- OUR STORY tag -->
      <div class="nbs-our-story-tag">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#5AB25A" stroke-width="2.5"
          stroke-linecap="round" stroke-linejoin="round">
          <path d="M12 2C7 2 4 8 4 13c0 3.5 2 6.5 5 8" />
          <path d="M12 2c5 0 8 6 8 11 0 3.5-2 6.5-5 8" />
          <line x1="12" y1="21" x2="12" y2="11" />
        </svg>
        Our Story
      </div>

      <!-- ═══════════════ MAIN GRID ═══════════════ -->
      <div class="nbs-main-grid">

        <!-- ────────── LEFT: IMAGES ────────── -->
        <div class="nbs-img-col">

          <!-- Arrow 1 SVG (top to middle) -->
          <svg class="nbs-arrow-svg nbs-arrow-1" viewBox="0 0 100 120" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M 85 10 C -10 10, -10 110, 85 110" stroke="#874BDE" stroke-width="2" stroke-dasharray="6 6"
              stroke-linecap="round" />
            <path d="M 70 100 L 85 110 L 70 120" stroke="#874BDE" stroke-width="2" stroke-linecap="round"
              stroke-linejoin="round" />
          </svg>

          <!-- Arrow 2 SVG (middle to bottom) -->
          <svg class="nbs-arrow-svg nbs-arrow-2" viewBox="0 0 100 120" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M 85 10 C -10 10, -10 110, 85 110" stroke="#874BDE" stroke-width="2" stroke-dasharray="6 6"
              stroke-linecap="round" />
            <path d="M 70 100 L 85 110 L 70 120" stroke="#874BDE" stroke-width="2" stroke-linecap="round"
              stroke-linejoin="round" />
          </svg>

          <!-- Card 1 – sick child -->
          <div class="nbs-photo-card nbs-card-1">
            <div class="nbs-tape"></div>
            <div class="nbs-photo-fill"><img src="/img/d1.jpeg" alt=""></div>
          </div>

          <!-- Card 2 – doctor visit -->
          <div class="nbs-photo-card nbs-card-2">
            <div class="nbs-tape nbs-tape-ye"></div>
            <div class="nbs-photo-fill"><img src="/img/d1.jpeg" alt=""></div>
          </div>

          <!-- Card 3 – happy running child -->
          <div class="nbs-photo-card nbs-card-3">
            <div class="nbs-tape nbs-tape-pu"></div>
            <div class="nbs-photo-fill"><img src="/img/d1.jpeg" alt=""></div>
          </div>

        </div><!-- /nbs-img-col -->

        <!-- ────────── RIGHT: CONTENT ────────── -->
        <div class="nbs-content-col">

          <!-- Badge -->
          <div class="nbs-badge">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
              stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 2C7 5 4 10 6 16c2 5 8 7 12 3 3-4 2-10-2-14C14 4 13 3 12 2z" />
              <line x1="12" y1="22" x2="12" y2="14" />
            </svg>
            The Beginning
          </div>

          <!-- Heading -->
          <h2 class="nbs-heading">
            It Started With<br />
            <span class="nbs-hd-pink">One Little Boy</span>
          </h2>

          <!-- Yellow line -->
          <div class="nbs-yellow-acc"></div>

          <!-- STEPS -->
          <div class="nbs-steps">

            <!-- 1 -->
            <div class="nbs-step">
              <div class="nbs-step-left">
                <div class="nbs-step-icon nbs-ic-pk">
                  <img src="/img/about-boy.png" alt="">
                </div>
              </div>
              <p class="nbs-step-body">
                At just 2 years old, he was <span class="nbs-hl-pk">constantly falling sick.</span><br />
                Cold, cough, weakness, it became a cycle.
              </p>
            </div>

            <!-- 2 -->
            <div class="nbs-step">
              <div class="nbs-step-left">
                <div class="nbs-step-icon nbs-ic-ye">
                  <img src="/img/first-addcart.png" alt="">
                </div>
              </div>
              <p class="nbs-step-body">
                Doctor visits became routine.<br />
                Medicines gave relief, but <span class="nbs-hl-pk">never a real solution.</span>
              </p>
            </div>

            <!-- 3 -->
            <div class="nbs-step">
              <div class="nbs-step-left">
                <div class="nbs-step-icon nbs-ic-pu">
                  <img src="/img/herbs.png" alt="">
                </div>
              </div>
              <p class="nbs-step-body">
                One day, someone suggested something simple,<br />
                <span class="nbs-hl-pu">honey</span> and a few <span class="nbs-hl-pu">Ayurvedic herbs.</span><br />
                His cough improved. But something still felt missing.
              </p>
            </div>

            <!-- 4 -->
            <div class="nbs-step">
              <div class="nbs-step-left">
                <div class="nbs-step-icon nbs-ic-mn">
                  <img src="/img/leave-one.png" alt="">
                </div>
              </div>
              <p class="nbs-step-body">
                That's when we realised<br />
                the real issue wasn't illness, <span class="nbs-hl-mn">it was nutrition.</span>
              </p>
            </div>

            <!-- 5 -->
            <div class="nbs-step">
              <div class="nbs-step-left">
                <div class="nbs-step-icon nbs-ic-pk">
                  <img src="/img/three.png" alt="">
                </div>
              </div>
              <p class="nbs-step-body">
                And if one child was facing this, thousands were too.<br />
                That moment became the beginning of <span class="nbs-hl-pk">NutriBuddy.</span>
              </p>
            </div>

          </div><!-- /nbs-steps -->
        </div><!-- /nbs-content-col -->

      </div><!-- /nbs-main-grid -->

      <!-- ═══════════════ STATS BAR ═══════════════ -->
      <div class="nbs-stats-bar">

        <div class="nbs-stat-item">
          <div class="nbs-stat-ico nbs-si-pk">
            <img src="/img/three.png" alt="">
          </div>
          <div class="nbs-stat-text">
            <span class="nbs-stat-num nbs-sn-pk">50K+</span>
            <span class="nbs-stat-lbl">Happy Families</span>
          </div>
        </div>

        <div class="nbs-stat-item">
          <div class="nbs-stat-ico nbs-si-pu">
            <img src="/img/labb-about.png" alt="">
          </div>
          <div class="nbs-stat-text">
            <span class="nbs-stat-num nbs-sn-pu">12+</span>
            <span class="nbs-stat-lbl">Expert Formulas</span>
          </div>
        </div>

        <div class="nbs-stat-item">
          <div class="nbs-stat-ico nbs-si-mn">
            <img src="/img/leaves.png" alt="">
          </div>
          <div class="nbs-stat-text">
            <span class="nbs-stat-num nbs-sn-mn">100%</span>
            <span class="nbs-stat-lbl">Natural Ingredients</span>
          </div>
        </div>

        <div class="nbs-stat-item">
          <div class="nbs-stat-ico nbs-si-ye">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="#FFC933" stroke="#d19a00" stroke-width="1.5"
              stroke-linecap="round" stroke-linejoin="round">
              <polygon
                points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
            </svg>
          </div>
          <div class="nbs-stat-text">
            <span class="nbs-stat-num nbs-sn-ye">4.9 ★</span>
            <span class="nbs-stat-lbl">Parent Rating</span>
          </div>
        </div>

      </div><!-- /nbs-stats-bar -->

      <!-- Gummy bears bottom-left -->
      <!-- <div class="nbs-gummies">
        <span class="nbs-gummy"><img src="img/blue-4.png" alt=""></span>
        <span class="nbs-gummy">🟡</span>
      </div> -->

    </div><!-- /nbs-container -->
  </section>


  <section class="origin-section">
    <div class="origin-grid">

      <!-- LEFT: Dark story card -->
      <div class="origin-visual">
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
        <div class="float-accent fa-1">Pediatrician Approved</div>
        <div class="float-accent fa-2">Zero Hidden Sugars</div>
      </div>

      <!-- RIGHT: Accordion content -->
      <div class="origin-text">
        <span class="sec-eye">Why We Started</span>
        <h2>The Problem Was <span class="acc">Hiding</span> in Plain Sight</h2>
        <p>Every parent faces everyday challenges that no one talks about enough. Click to see what really troubles us.
        </p>

        <ul class="accordion-list">

          <li class="acc-item open" data-index="0">
            <div class="acc-header" onclick="toggle(this)">
              <div class="acc-icon-wrap" style="background:#fde8f3;"> <img src="/img/sec-2.png" alt=""></div>
              <span class="acc-title">Picky eating, every day</span>
              <span class="acc-toggle">
                <svg viewBox="0 0 14 14">
                  <line x1="7" y1="2" x2="7" y2="12" />
                  <line x1="2" y1="7" x2="12" y2="7" />
                </svg>
              </span>
            </div>
            <div class="acc-body">
              <div class="acc-body-inner">
                <ul>
                  <li>One day they eat well, the next day they refuse everything.</li>
                  <li>You don't know what's normal anymore.</li>
                </ul>
              </div>
            </div>
          </li>

          <li class="acc-item" data-index="1">
            <div class="acc-header" onclick="toggle(this)">
              <div class="acc-icon-wrap" style="background:#fff3e0;"><img src="/img/sec-3.png" alt=""></div>
              <span class="acc-title">Constant worry in the back of your mind</span>
              <span class="acc-toggle">
                <svg viewBox="0 0 14 14">
                  <line x1="7" y1="2" x2="7" y2="12" />
                  <line x1="2" y1="7" x2="12" y2="7" />
                </svg>
              </span>
            </div>
            <div class="acc-body">
              <div class="acc-body-inner">
                <ul>
                  <li>Is my child getting enough nutrition each day?</li>
                  <li>Am I doing enough as a parent?</li>
                </ul>
              </div>
            </div>
          </li>

          <li class="acc-item" data-index="2">
            <div class="acc-header" onclick="toggle(this)">
              <div class="acc-icon-wrap" style="background:#e8f0ff;"><img src="/img/sec-5.png" alt=""></div>
              <span class="acc-title">Too much advice, too little clarity</span>
              <span class="acc-toggle">
                <svg viewBox="0 0 14 14">
                  <line x1="7" y1="2" x2="7" y2="12" />
                  <line x1="2" y1="7" x2="12" y2="7" />
                </svg>
              </span>
            </div>
            <div class="acc-body">
              <div class="acc-body-inner">
                <ul>
                  <li>Every doctor, blog, and relative says something different.</li>
                  <li>Trusted, science-backed answers are hard to find.</li>
                </ul>
              </div>
            </div>
          </li>

          <li class="acc-item" data-index="3">
            <div class="acc-header" onclick="toggle(this)">
              <div class="acc-icon-wrap" style="background:#e8faf2;"><img src="/img/sec-4.png" alt=""></div>
              <span class="acc-title">Hidden ingredients you can't see</span>
              <span class="acc-toggle">
                <svg viewBox="0 0 14 14">
                  <line x1="7" y1="2" x2="7" y2="12" />
                  <line x1="2" y1="7" x2="12" y2="7" />
                </svg>
              </span>
            </div>
            <div class="acc-body">
              <div class="acc-body-inner">
                <ul>
                  <li>Most supplements hide fillers, sugar, and artificial colours.</li>
                  <li>Transparency is rare; parents deserve better.</li>
                </ul>
              </div>
            </div>
          </li>

          <li class="acc-item" data-index="4">
            <div class="acc-header" onclick="toggle(this)">
              <div class="acc-icon-wrap" style="background:#fde8f3;"><img src="/img/sec-6.png" alt=""></div>
              <span class="acc-title">Frequent sickness and low immunity</span>
              <span class="acc-toggle">
                <svg viewBox="0 0 14 14">
                  <line x1="7" y1="2" x2="7" y2="12" />
                  <line x1="2" y1="7" x2="12" y2="7" />
                </svg>
              </span>
            </div>
            <div class="acc-body">
              <div class="acc-body-inner">
                <ul>
                  <li>City life, pollution, and stress weaken a child's defences.</li>
                  <li>Quick medicine only fixes symptoms, not the root cause.</li>
                </ul>
              </div>
            </div>
          </li>

          <li class="acc-item" data-index="5">
            <div class="acc-header" onclick="toggle(this)">
              <div class="acc-icon-wrap" style="background:#fff3e0;"><img src="/img/sec-1.png" alt=""></div>
              <span class="acc-title">Not made for Indian kids</span>
              <span class="acc-toggle">
                <svg viewBox="0 0 14 14">
                  <line x1="7" y1="2" x2="7" y2="12" />
                  <line x1="2" y1="7" x2="12" y2="7" />
                </svg>
              </span>
            </div>
            <div class="acc-body">
              <div class="acc-body-inner">
                <ul>
                  <li>Most global brands ignore Indian dietary patterns and deficiencies.</li>
                  <li>Our kids need solutions built specifically for them.</li>
                </ul>
              </div>
            </div>
          </li>

        </ul>
      </div>

    </div>
  </section>


  <!-- gap -->
  <section class="gapfix-section">

    <!-- Header -->
    <div class="gapfix-top">
      <span class="gapfix-eyebrow">The Gap We Found</span>
      <h2 class="gapfix-heading">The gap we found — and <span>fixed.</span> <img class="proper-set" src="/img/heartmim.png" alt=""></h2>
      <p class="gapfix-subtext">
        After months of research, lab testing, and consulting paediatricians and Ayurvedic practitioners,
        we identified four critical gaps in children's nutrition products available in India.
      </p>
    </div>

    <!-- Column Labels -->
    <div class="gapfix-col-labels">
      <div class="gapfix-label gapfix-label--problem">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
          <path
            d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"
            stroke="#FF4D8F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        The Problem
      </div>
      <div class="gapfix-label gapfix-label--fix">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
          <circle cx="12" cy="12" r="10" stroke="#00D68F" stroke-width="2" />
          <path d="M8 12l3 3 5-5" stroke="#00D68F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        Our Fix
      </div>
    </div>

    <!-- Rows -->
    <div class="gapfix-rows">

      <!-- Row 1 -->
      <div class="gapfix-row">
        <div class="gapfix-card">
          <div class="gapfix-icon-box gapfix-icon-box--problem"> <img src="/img/btn-1.png" alt=""></div>
          <div class="gapfix-num gapfix-num--problem">01</div>
          <div class="gapfix-card-body">
            <p class="gapfix-card-title">The Hidden Sugar Trap</p>
            <p class="gapfix-card-desc">Most kids' nutrition drinks pack 8–14g of sugar per serving — disguised as
              flavour.</p>
          </div>
        </div>

        <div class="gapfix-arrow">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
            <path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
              stroke-linejoin="round" />
          </svg>
        </div>

        <div class="gapfix-card">
          <div class="gapfix-icon-box gapfix-icon-box--fix"> <img src="/img/new-btn-1.png" alt=""></div>
          <div class="gapfix-num gapfix-num--fix">01</div>
          <div class="gapfix-card-body">
            <p class="gapfix-card-title">We use monk fruit + stevia at safe levels.</p>
            <span class="gapfix-badge-tag">✅ Zero added sugar</span>
            <p class="gapfix-card-desc">Great taste, no hidden sweetness.</p>
          </div>
        </div>
      </div>

      <!-- Row 2 -->
      <div class="gapfix-row">
        <div class="gapfix-card">
          <div class="gapfix-icon-box gapfix-icon-box--problem"><img src="/img/btn-2.png" alt=""></div>
          <div class="gapfix-num gapfix-num--problem">02</div>
          <div class="gapfix-card-body">
            <p class="gapfix-card-title">Nutrients That Don't Absorb</p>
            <p class="gapfix-card-desc">Iron and calcium on the label rarely reach the bloodstream.</p>
          </div>
        </div>

        <div class="gapfix-arrow">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
            <path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
              stroke-linejoin="round" />
          </svg>
        </div>

        <div class="gapfix-card">
          <div class="gapfix-icon-box gapfix-icon-box--fix"> <img src="/img/new-btn-2.png" alt=""></div>
          <div class="gapfix-num gapfix-num--fix">02</div>
          <div class="gapfix-card-body">
            <p class="gapfix-card-title">Better Absorption, Better Results</p>
            <p class="gapfix-card-desc">Highly bioavailable minerals that your child's body can absorb and use.</p>
          </div>
        </div>
      </div>

      <!-- Row 3 -->
      <div class="gapfix-row">
        <div class="gapfix-card">
          <div class="gapfix-icon-box gapfix-icon-box--problem"><img src="/img/btn-3.png" alt=""></div>
          <div class="gapfix-num gapfix-num--problem">03</div>
          <div class="gapfix-card-body">
            <p class="gapfix-card-title">The Ingredient Maze</p>
            <p class="gapfix-card-desc">Long lists of artificial colors, preservatives and 'natural identical' flavors
              hide in fine print.</p>
          </div>
        </div>

        <div class="gapfix-arrow">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
            <path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
              stroke-linejoin="round" />
          </svg>
        </div>

        <div class="gapfix-card">
          <div class="gapfix-icon-box gapfix-icon-box--fix"><img src="/img/new-btn-3.png" alt=""></div>
          <div class="gapfix-num gapfix-num--fix">03</div>
          <div class="gapfix-card-body">
            <p class="gapfix-card-title">Clean. Transparent. Honest.</p>
            <p class="gapfix-card-desc">No artificial colors, preservatives or hidden ingredients. Just clean nutrition
              you can trust.</p>
          </div>
        </div>
      </div>

      <!-- Row 4 -->
      <div class="gapfix-row">
        <div class="gapfix-card">
          <div class="gapfix-icon-box gapfix-icon-box--problem"><img src="/img/btn-4.png" alt=""></div>
          <div class="gapfix-num gapfix-num--problem">04</div>
          <div class="gapfix-card-body">
            <p class="gapfix-card-title">Built For Others, Not For Us</p>
            <p class="gapfix-card-desc">Global brands are formulated for Western diets — not dal-chawal, not Indian
              climates, not our gut flora.</p>
          </div>
        </div>

        <div class="gapfix-arrow">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
            <path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
              stroke-linejoin="round" />
          </svg>
        </div>

        <div class="gapfix-card">
          <div class="gapfix-icon-box gapfix-icon-box--fix"><img src="/img/new-btn-4.png" alt=""></div>
          <div class="gapfix-num gapfix-num--fix">04</div>
          <div class="gapfix-card-body">
            <p class="gapfix-card-title">Built For Indian Kids</p>
            <p class="gapfix-card-desc">Thoughtfully formulated for Indian diets, lifestyles and growing bodies. Gentle on
              the gut. Perfect for our kids.</p>
          </div>
        </div>
      </div>

    </div><!-- /.gapfix-rows -->

    <!-- Footer Banner -->
    <div class="gapfix-footer-banner">
      <div class="gapfix-footer-icon"><img src="/img/sheeld.png" alt=""></div>
      <p class="gapfix-footer-text">
        Because your child deserves more than just nutrition —<br>
        they deserve <em>the right nutrition.</em>
      </p>
      <img src="/img/taddy.png" alt="">
    </div>

  </section>

  <!-- how we build every product -->
    <style>
      *,
      *::before,
      *::after {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
      }

      :root {
        
        --nbap-ye: #ffd93d;
        --nbap-pk: #ff4d8f;
        --nbap-wh: #ffffff;
        --nbap-dim: rgba(255, 255, 255, 0.60);
        --nbap-c1: #a8ff3e;
        --nbap-c2: #3eaaff;
        --nbap-c3: #ff4d8f;
        --nbap-c4: #ffb347;
        --nbap-c5: #a8ff3e;
      }

      html {
        scroll-behavior: smooth;
      }

      body {
        font-family: 'Nunito', sans-serif;
        background: var(--nbap-bg);
        -webkit-font-smoothing: antialiased;
      }

      /* ══════════════════════════
     SECTION
  ══════════════════════════ */
      .nbap {
        position: relative;
        padding: 80px 5% 64px;
        background: #0d0028;
        overflow: hidden;
      }

      .nbap-blob {
        position: absolute;
        border-radius: 50%;
        pointer-events: none;
        filter: blur(100px);
      }

      .nbap-blob-a {
        width: 500px;
        height: 500px;
        background: rgba(124, 58, 237, 0.22);
        top: -140px;
        left: -150px;
      }

      .nbap-blob-b {
        width: 380px;
        height: 380px;
        background: rgba(255, 77, 143, 0.18);
        bottom: -90px;
        right: -110px;
      }

      .nbap-blob-c {
        width: 280px;
        height: 280px;
        background: rgba(62, 170, 255, 0.12);
        top: 35%;
        left: 40%;
      }

      /* ══════════════════════════
     HEADER
  ══════════════════════════ */
      .nbap-head {
        text-align: center;
        margin-bottom: 60px;
        position: relative;
        z-index: 3;
      }

      .nbap-eye {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 14px;
        font-size: 0.74rem;
        font-weight: 900;
        letter-spacing: 3.5px;
        text-transform: uppercase;
        color: var(--nbap-ye);
      }

      .nbap-eye b {
        display: inline-block;
        width: 26px;
        height: 1.5px;
        background: var(--nbap-ye);
        border-radius: 2px;
        font-weight: 400;
      }

      .nbap-head h2 {
        font-family: 'Fredoka One', cursive;
        font-size: clamp(1.9rem, 5vw, 3.4rem);
        line-height: 1.1;
        color: var(--nbap-wh);
      }

      .nbap-head h2 em {
        font-style: normal;
        color: var(--nbap-ye);
      }

      .nbap-head p {
        margin-top: 14px;
        font-size: clamp(0.88rem, 1.4vw, 1rem);
        line-height: 1.75;
        color: var(--nbap-dim);
        max-width: 480px;
        margin-left: auto;
        margin-right: auto;
      }

      .nbap-head p strong {
        color: var(--nbap-pk);
        font-weight: 900;
      }

      /* ══════════════════════════
     STEPS — DESKTOP (5-col flex row)
  ══════════════════════════ */
      .nbap-steps {
        position: relative;
        z-index: 3;
        max-width: 1300px;
        margin: 0 auto 52px;
      }

      .nbap-row {
        display: flex;
        align-items: flex-start;
        justify-content: center;
      }

      /* ── STEP CARD ── */
      .nbap-card {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding: 0 6px;
        animation: nbapUp 0.55s ease both;
      }

      .nbap-card:nth-child(1) {
        animation-delay: 0.05s;
      }

      .nbap-card:nth-child(3) {
        animation-delay: 0.15s;
      }

      .nbap-card:nth-child(5) {
        animation-delay: 0.25s;
      }

      .nbap-card:nth-child(7) {
        animation-delay: 0.35s;
      }

      .nbap-card:nth-child(9) {
        animation-delay: 0.45s;
      }

      @keyframes nbapUp {
        from {
          opacity: 0;
          transform: translateY(26px);
        }

        to {
          opacity: 1;
          transform: translateY(0);
        }
      }

      /* ── ARROW ── */
      .nbap-arr {
        flex-shrink: 0;
        width: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding-top: 82px;
      }

      .nbap-arr svg {
        width: 28px;
        height: 28px;
      }

      /* ── BUBBLE ── */
      .nbap-bubble {
        position: relative;
        width: clamp(128px, 14vw, 182px);
        height: clamp(128px, 14vw, 182px);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
        transition: transform 0.32s ease;
      }

      .nbap-card:hover .nbap-bubble {
        transform: translateY(-10px) scale(1.05);
      }

      .nbap-card[data-s="1"] .nbap-bubble {
        /* background: radial-gradient(circle at 38% 38%, #1e5506, #0a2602); */
        box-shadow: 0 0 0 3px rgba(168, 255, 62, 0.28), 0 18px 50px rgba(0, 0, 0, 0.5);
      }

      .nbap-card[data-s="2"] .nbap-bubble {
        /* background: radial-gradient(circle at 38% 38%, #0b2572, #04103d); */
        box-shadow: 0 0 0 3px rgba(62, 170, 255, 0.28), 0 18px 50px rgba(0, 0, 0, 0.5);
      }

      .nbap-card[data-s="3"] .nbap-bubble {
        /* background: radial-gradient(circle at 38% 38%, #680650, #38022c); */
        box-shadow: 0 0 0 3px rgba(255, 77, 143, 0.28), 0 18px 50px rgba(0, 0, 0, 0.5);
      }

      .nbap-card[data-s="4"] .nbap-bubble {
        /* background: radial-gradient(circle at 38% 38%, #602900, #311500); */
        box-shadow: 0 0 0 3px rgba(255, 179, 71, 0.28), 0 18px 50px rgba(0, 0, 0, 0.5);
      }

      .nbap-card[data-s="5"] .nbap-bubble {
        /* background: radial-gradient(circle at 38% 38%, #1d4a08, #0a2302); */
        box-shadow: 0 0 0 3px rgba(168, 255, 62, 0.34), 0 18px 50px rgba(0, 0, 0, 0.5);
      }

      .nbap-bubble::after {
        content: '';
        position: absolute;
        inset: -10px;
        border-radius: 50%;
        border: 1.5px solid transparent;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.3s;
      }

      .nbap-card[data-s="1"] .nbap-bubble::after {
        border-color: rgba(168, 255, 62, 0.55);
      }

      .nbap-card[data-s="2"] .nbap-bubble::after {
        border-color: rgba(62, 170, 255, 0.55);
      }

      .nbap-card[data-s="3"] .nbap-bubble::after {
        border-color: rgba(255, 77, 143, 0.55);
      }

      .nbap-card[data-s="4"] .nbap-bubble::after {
        border-color: rgba(255, 179, 71, 0.55);
      }

      .nbap-card[data-s="5"] .nbap-bubble::after {
        border-color: rgba(168, 255, 62, 0.6);
      }

      .nbap-card:hover .nbap-bubble::after {
        opacity: 1;
      }

      .nbap-bubble img {
        width: 68%;
        height: 68%;
        object-fit: contain;
        filter: drop-shadow(0 4px 16px rgba(0, 0, 0, 0.45));
        display: block;
      }

      .nbap-emoji {
        font-size: 56px;
        line-height: 1;
      }

      .nbap-num {
        position: absolute;
        top: -4px;
        left: 50%;
        transform: translateX(-50%);
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Fredoka One', cursive;
        font-size: 0.92rem;
        color: #0d0028;
        z-index: 4;
        box-shadow: 0 3px 12px rgba(0, 0, 0, 0.35);
      }

      .nbap-card[data-s="1"] .nbap-num {
        background: var(--nbap-c1);
      }

      .nbap-card[data-s="2"] .nbap-num {
        background: var(--nbap-c2);
      }

      .nbap-card[data-s="3"] .nbap-num {
        background: var(--nbap-c3);
      }

      .nbap-card[data-s="4"] .nbap-num {
        background: var(--nbap-c4);
      }

      .nbap-card[data-s="5"] .nbap-num {
        background: var(--nbap-c5);
      }

      .nbap-title {
        font-family: 'Fredoka One', cursive;
        font-size: clamp(0.9rem, 1.25vw, 1.08rem);
        line-height: 1.3;
        margin-bottom: 8px;
      }

      .nbap-card[data-s="1"] .nbap-title {
        color: var(--nbap-c1);
      }

      .nbap-card[data-s="2"] .nbap-title {
        color: var(--nbap-c2);
      }

      .nbap-card[data-s="3"] .nbap-title {
        color: var(--nbap-c3);
      }

      .nbap-card[data-s="4"] .nbap-title {
        color: var(--nbap-c4);
      }

      .nbap-card[data-s="5"] .nbap-title {
        color: var(--nbap-c5);
      }

      .nbap-desc {
        font-size: clamp(0.74rem, 0.92vw, 0.84rem);
        line-height: 1.65;
        color: var(--nbap-dim);
        max-width: 168px;
      }

      /* ══════════════════════════
     BADGE BAR
  ══════════════════════════ */
      .nbap-badges {
        position: relative;
        z-index: 3;
        max-width: 920px;
        margin: 0 auto;
        background: rgba(255, 255, 255, 0.055);
        border: 1px solid rgba(255, 255, 255, 0.10);
        border-radius: 60px;
        padding: 18px 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-wrap: nowrap;
        gap: 0;
      }

      .nbap-badge {
        display: flex;
        align-items: center;
        gap: 9px;
        font-size: 0.82rem;
        font-weight: 800;
        color: var(--nbap-wh);
        white-space: nowrap;
        padding: 0 18px;
      }

      .nbap-badge:first-child {
        padding-left: 0;
      }

      .nbap-badge:last-child {
        padding-right: 0;
      }

      .nbap-bico {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        flex-shrink: 0;
      }

      .nbap-badge:nth-child(1) .nbap-bico {
        background: rgba(168, 255, 62, 0.18);
      }

      .nbap-badge:nth-child(3) .nbap-bico {
        background: rgba(62, 170, 255, 0.18);
      }

      .nbap-badge:nth-child(5) .nbap-bico {
        background: rgba(255, 77, 143, 0.18);
      }

      .nbap-badge:nth-child(7) .nbap-bico {
        background: rgba(255, 179, 71, 0.20);
      }

      .nbap-badge:nth-child(9) .nbap-bico {
        background: rgba(168, 255, 62, 0.22);
      }
      .nbap-bico img {
            height: 24px;
      }
      .nbap-bsep {
        width: 1px;
        height: 26px;
        background: rgba(255, 255, 255, 0.12);
        flex-shrink: 0;
      }

      /* ══════════════════════════
     TABLET  769px – 1024px
     2 + 1 + 2 layout
  ══════════════════════════ */
      @media (max-width: 1024px) and (min-width: 769px) {
        .nbap {
          padding: 64px 5% 52px;
        }

        .nbap-head {
          margin-bottom: 48px;
        }

        .nbap-row {
          display: grid;
          grid-template-columns: 1fr 44px 1fr;
          grid-template-rows: auto auto auto;
          row-gap: 36px;
          column-gap: 0;
          justify-items: center;
          align-items: start;
        }

        /* Row 1: card1 – arrow – card2 */
        .nbap-card[data-s="1"] {
          grid-column: 1;
          grid-row: 1;
        }

        .nbap-arr[data-a="1"] {
          grid-column: 2;
          grid-row: 1;
          padding-top: 76px;
        }

        .nbap-card[data-s="2"] {
          grid-column: 3;
          grid-row: 1;
        }

        /* Row 2: card3 centred */
        .nbap-card[data-s="3"] {
          grid-column: 1 / 4;
          grid-row: 2;
          justify-self: center;
        }

        /* Row 3: card4 – arrow – card5 */
        .nbap-card[data-s="4"] {
          grid-column: 1;
          grid-row: 3;
        }

        .nbap-arr[data-a="4"] {
          grid-column: 2;
          grid-row: 3;
          padding-top: 76px;
        }

        .nbap-card[data-s="5"] {
          grid-column: 3;
          grid-row: 3;
        }

        /* Hide middle arrows */
        .nbap-arr[data-a="2"],
        .nbap-arr[data-a="3"] {
          display: none;
        }

        .nbap-bubble {
          width: clamp(118px, 20vw, 160px);
          height: clamp(118px, 20vw, 160px);
        }

        .nbap-desc {
          max-width: 200px;
        }

        /* Badge bar wraps */
        .nbap-badges {
          border-radius: 24px;
          padding: 16px 24px;
          flex-wrap: wrap;
          gap: 10px;
          justify-content: center;
        }

        .nbap-badge {
          padding: 0 10px;
        }

        .nbap-bsep {
          display: none;
        }
      }

      /* ══════════════════════════
     SMALL TABLET  541px – 768px
     vertical cards, image left
  ══════════════════════════ */
      @media (max-width: 768px) and (min-width: 541px) {
        .nbap {
          padding: 56px 5% 48px;
        }

        .nbap-head {
          margin-bottom: 40px;
        }

        .nbap-steps {
          margin-bottom: 40px;
        }

        .nbap-row {
          display: flex;
          flex-direction: column;
          align-items: stretch;
          gap: 0;
          position: relative;
          padding-left: 20px;
        }

        .nbap-row::before {
          content: '';
          position: absolute;
          left: 0;
          top: 24px;
          bottom: 24px;
          width: 3px;
          background: linear-gradient(to bottom, var(--nbap-c1) 0%, var(--nbap-c2) 25%, var(--nbap-c3) 50%, var(--nbap-c4) 75%, var(--nbap-c5) 100%);
          border-radius: 3px;
          opacity: 0.35;
        }

        .nbap-arr {
          display: none;
        }

        .nbap-card {
          flex-direction: row;
          align-items: center;
          text-align: left;
          padding: 0;
          gap: 18px;
          margin-bottom: 28px;
          position: relative;
        }

        .nbap-card:last-child {
          margin-bottom: 0;
        }

        .nbap-card::before {
          content: '';
          position: absolute;
          left: -26px;
          top: 50%;
          transform: translateY(-50%);
          width: 12px;
          height: 12px;
          border-radius: 50%;
          border: 2px solid var(--nbap-bg);
          z-index: 2;
        }

        .nbap-card[data-s="1"]::before {
          background: var(--nbap-c1);
          box-shadow: 0 0 0 3px rgba(168, 255, 62, 0.25);
        }

        .nbap-card[data-s="2"]::before {
          background: var(--nbap-c2);
          box-shadow: 0 0 0 3px rgba(62, 170, 255, 0.25);
        }

        .nbap-card[data-s="3"]::before {
          background: var(--nbap-c3);
          box-shadow: 0 0 0 3px rgba(255, 77, 143, 0.25);
        }

        .nbap-card[data-s="4"]::before {
          background: var(--nbap-c4);
          box-shadow: 0 0 0 3px rgba(255, 179, 71, 0.25);
        }

        .nbap-card[data-s="5"]::before {
          background: var(--nbap-c5);
          box-shadow: 0 0 0 3px rgba(168, 255, 62, 0.3);
        }

        .nbap-bubble {
          width: 110px;
          height: 110px;
          flex-shrink: 0;
          margin-bottom: 0;
        }

        .nbap-card:hover .nbap-bubble {
          transform: scale(1.04);
        }

        .nbap-text-wrap {
          flex: 1;
          min-width: 0;
        }

        .nbap-title {
          font-size: 1rem;
          margin-bottom: 6px;
        }

        .nbap-desc {
          font-size: 0.83rem;
          max-width: 100%;
        }

        .nbap-num {
          width: 28px;
          height: 28px;
          font-size: 0.8rem;
        }

        /* Badge bar */
        .nbap-badges {
          border-radius: 20px;
          padding: 16px 20px;
          flex-wrap: wrap;
          gap: 10px;
          justify-content: center;
        }

        .nbap-bsep {
          display: none;
        }

        .nbap-badge {
          padding: 0 8px;
        }
      }

      /* ══════════════════════════
     MOBILE  ≤ 540px
  ══════════════════════════ */
      @media (max-width: 540px) {
         .ns-story-hero .ns-arrow-svg {
            top: 130px;
    left: 122px;
      }
        .nbap {
          padding: 48px 4% 44px;
        }

        .nbap-head {
          margin-bottom: 36px;
        }

        .nbap-steps {
          margin-bottom: 36px;
        }

        .nbap-row {
          display: flex;
          flex-direction: column;
          align-items: stretch;
          gap: 0;
          position: relative;
          padding-left: 16px;
        }

        .nbap-row::before {
          content: '';
          position: absolute;
          left: 0;
          top: 20px;
          bottom: 20px;
          width: 3px;
          background: linear-gradient(to bottom, var(--nbap-c1) 0%, var(--nbap-c2) 25%, var(--nbap-c3) 50%, var(--nbap-c4) 75%, var(--nbap-c5) 100%);
          border-radius: 3px;
          opacity: 0.35;
        }

        .nbap-arr {
          display: none;
        }

        .nbap-card {
          flex-direction: row;
          align-items: center;
          text-align: left;
          padding: 0;
          gap: 14px;
          margin-bottom: 24px;
          position: relative;
        }

        .nbap-card:last-child {
          margin-bottom: 0;
        }

        .nbap-card::before {
          content: '';
          position: absolute;
          left: -22px;
          top: 50%;
          transform: translateY(-50%);
          width: 10px;
          height: 10px;
          border-radius: 50%;
          border: 2px solid var(--nbap-bg);
          z-index: 2;
        }

        .nbap-card[data-s="1"]::before {
          background: var(--nbap-c1);
        }

        .nbap-card[data-s="2"]::before {
          background: var(--nbap-c2);
        }

        .nbap-card[data-s="3"]::before {
          background: var(--nbap-c3);
        }

        .nbap-card[data-s="4"]::before {
          background: var(--nbap-c4);
        }

        .nbap-card[data-s="5"]::before {
          background: var(--nbap-c5);
        }

        .nbap-bubble {
          width: 92px;
          height: 92px;
          flex-shrink: 0;
          margin-bottom: 0;
        }

        .nbap-card:hover .nbap-bubble {
          transform: scale(1.04);
        }

        .nbap-emoji {
          font-size: 36px;
        }

        .nbap-text-wrap {
          flex: 1;
          min-width: 0;
        }

        .nbap-title {
          font-size: 0.95rem;
          margin-bottom: 5px;
        }

        .nbap-desc {
          font-size: 0.8rem;
          max-width: 100%;
        }

        .nbap-num {
          width: 26px;
          height: 26px;
          font-size: 0.76rem;
          top: -2px;
        }

        /* Badge bar — 2-column grid */
        .nbap-badges {
          border-radius: 18px;
          padding: 14px 16px;
          display: grid;
          grid-template-columns: 1fr 1fr;
          gap: 12px;
          flex-wrap: unset;
          justify-content: unset;
        }

        .nbap-bsep {
          display: none;
        }

        .nbap-badge {
          padding: 0;
          font-size: 0.78rem;
        }

        .nbap-badge:nth-child(9) {
          grid-column: 1 / -1;
          justify-content: center;
        }
      }

      /* ══════════════════════════
     TINY  ≤ 360px
  ══════════════════════════ */
      @media (max-width: 360px) {
        .nbap-bubble {
          width: 80px;
          height: 80px;
        }

        .nbap-title {
          font-size: 0.9rem;
        }

        .nbap-desc {
          font-size: 0.75rem;
        }

        .nbap-row {
          padding-left: 12px;
        }

        .nbap-card::before {
          left: -18px;
        }

        .nbap-badges {
          grid-template-columns: 1fr;
        }

        .nbap-badge:nth-child(9) {
          grid-column: 1;
          justify-content: flex-start;
        }
      }
    </style>
    <section class="nbap">
      <div class="nbap-blob nbap-blob-a"></div>
      <div class="nbap-blob nbap-blob-b"></div>
      <div class="nbap-blob nbap-blob-c"></div>

      <!-- HEADER -->
      <div class="nbap-head">
        <div class="nbap-eye"><b></b> Our Approach <b></b></div>
        <h2>How We Build <em>Every Product</em></h2>
        <p>Every NutriBuddy formula is born from a simple but powerful equation that took us <strong>two years</strong> to
          perfect.</p>
      </div>

      <!-- STEPS -->
      <div class="nbap-steps">
        <div class="nbap-row">

          <!-- ── Card 1 ── -->
          <div class="nbap-card" data-s="1">
            <div class="nbap-bubble">
              <span class="nbap-num">01</span>
              <img src="/img/blue1.png" alt="Carefully Selected Ingredients"
                onerror="this.replaceWith(Object.assign(document.createElement('span'),{className:'nbap-emoji',textContent:'🌿'}))">
            </div>
            <div class="nbap-text-wrap">
              <h3 class="nbap-title">Carefully Selected<br>Ingredients</h3>
              <p class="nbap-desc">We handpick the best natural herbs, fruits, vitamins &amp; minerals from trusted sources.
              </p>
            </div>
          </div>

          <!-- Arrow 1→2 -->
          <div class="nbap-arr" data-a="1">
            <svg viewBox="0 0 28 28" fill="none">
              <circle cx="14" cy="14" r="13" stroke="rgba(255,255,255,0.15)" stroke-width="1.5" />
              <path d="M10 14h8M15 11l3 3-3 3" stroke="rgba(255,255,255,0.75)" stroke-width="1.8" stroke-linecap="round"
                stroke-linejoin="round" />
            </svg>
          </div>

          <!-- ── Card 2 ── -->
          <div class="nbap-card" data-s="2">
            <div class="nbap-bubble">
              <span class="nbap-num">02</span>
              <img src="/img/blue2.png" alt="Backed by Science and Experts"
                onerror="this.replaceWith(Object.assign(document.createElement('span'),{className:'nbap-emoji',textContent:'🔬'}))">
            </div>
            <div class="nbap-text-wrap">
              <h3 class="nbap-title">Backed by Science<br>&amp; Experts</h3>
              <p class="nbap-desc">Our formulas are developed by nutrition experts to support kids' growth, immunity &amp;
                brain development.</p>
            </div>
          </div>

          <!-- Arrow 2→3 -->
          <div class="nbap-arr" data-a="2">
            <svg viewBox="0 0 28 28" fill="none">
              <circle cx="14" cy="14" r="13" stroke="rgba(255,255,255,0.15)" stroke-width="1.5" />
              <path d="M10 14h8M15 11l3 3-3 3" stroke="rgba(255,255,255,0.75)" stroke-width="1.8" stroke-linecap="round"
                stroke-linejoin="round" />
            </svg>
          </div>

          <!-- ── Card 3 ── -->
          <div class="nbap-card" data-s="3">
            <div class="nbap-bubble">
              <span class="nbap-num">03</span>
              <img src="/img/blue-3.png" alt="Clean and Safe Manufacturing"
                onerror="this.replaceWith(Object.assign(document.createElement('span'),{className:'nbap-emoji',textContent:'🏭'}))">
            </div>
            <div class="nbap-text-wrap">
              <h3 class="nbap-title">Clean &amp; Safe<br>Manufacturing</h3>
              <p class="nbap-desc">Made in certified facilities with strict quality checks. No harmful chemicals. Ever.</p>
            </div>
          </div>

          <!-- Arrow 3→4 -->
          <div class="nbap-arr" data-a="3">
            <svg viewBox="0 0 28 28" fill="none">
              <circle cx="14" cy="14" r="13" stroke="rgba(255,255,255,0.15)" stroke-width="1.5" />
              <path d="M10 14h8M15 11l3 3-3 3" stroke="rgba(255,255,255,0.75)" stroke-width="1.8" stroke-linecap="round"
                stroke-linejoin="round" />
            </svg>
          </div>

          <!-- ── Card 4 ── -->
          <div class="nbap-card" data-s="4">
            <div class="nbap-bubble">
              <span class="nbap-num">04</span>
              <img src="/img/blue-4.png" alt="Made Fun and Yummy"
                onerror="this.replaceWith(Object.assign(document.createElement('span'),{className:'nbap-emoji',textContent:'🐻'}))">
            </div>
            <div class="nbap-text-wrap">
              <h3 class="nbap-title">Made Fun<br>&amp; Yummy</h3>
              <p class="nbap-desc">We turn nutrition into delicious gummies kids love to eat, every single day!</p>
            </div>
          </div>

          <!-- Arrow 4→5 -->
          <div class="nbap-arr" data-a="4">
            <svg viewBox="0 0 28 28" fill="none">
              <circle cx="14" cy="14" r="13" stroke="rgba(255,255,255,0.15)" stroke-width="1.5" />
              <path d="M10 14h8M15 11l3 3-3 3" stroke="rgba(255,255,255,0.75)" stroke-width="1.8" stroke-linecap="round"
                stroke-linejoin="round" />
            </svg>
          </div>

          <!-- ── Card 5 ── -->
          <div class="nbap-card" data-s="5">
            <div class="nbap-bubble">
              <span class="nbap-num">05</span>
              <img src="/img/kid.png" alt="Better Nutrition Brighter Future"
                onerror="this.replaceWith(Object.assign(document.createElement('span'),{className:'nbap-emoji',textContent:'💪'}))">
            </div>
            <div class="nbap-text-wrap">
              <h3 class="nbap-title">Better Nutrition,<br>Brighter Future</h3>
              <p class="nbap-desc">Every NutriBuddy product helps kids grow stronger, smarter &amp; healthier.</p>
            </div>
          </div>

        </div>
      </div>

      <!-- BADGE BAR -->
      <div class="nbap-badges">
        <div class="nbap-badge">
          <div class="nbap-bico" ><img src="/img/new-btn-2.png" alt=""></div><span>100% Natural</span>
        </div>
        <div class="nbap-bsep"></div>
        <div class="nbap-badge">
          <div class="nbap-bico">🧪</div><span>No Nasties</span>
        </div>
        <div class="nbap-bsep"></div>
        <div class="nbap-badge">
          <div class="nbap-bico">🎲</div><span>No Added Sugar</span>
        </div>
        <div class="nbap-bsep"></div>
        <div class="nbap-badge">
          <div class="nbap-bico"><img src="/img/new-btn-1.png" alt=""></div><span>Vegetarian Friendly</span>
        </div>
        <div class="nbap-bsep"></div>
        <div class="nbap-badge">
          <div class="nbap-bico">🛡️</div><span>Safe for Everyday Use</span>
        </div>
      </div>
    </section>


  <section class="aj-trust-section">
    <div class="aj-trust-container">
      <div class="closing-inner reveal">

        <h2>
          TRUST & LOVED BY
          <span class="pop">PARENTS</span>
        </h2>




      </div>
      <!-- TOP PARAGRAPH -->
      <p class="aj-trust-desc">
        We understand that our customers rely on us to provide them with safe, effective, and reliable products.
        That's why we go to great lengths to ensure that every product that bears our name is of the highest
        quality.
        Our team of experts works tirelessly to source the best possible ingredients and to craft formulations that
        are gentle, effective, and backed by science.
      </p>

      <!-- MAIN CONTENT -->
      <div class="aj-trust-flex">

        <!-- LEFT IMAGE GRID -->
        <div class="aj-trust-grid">
          <!-- Replace with your real images -->
          <img src="/img/girl.jpeg">
          <img src="/img/cidss.jpeg">
          <img src="/img/BUSY-P.jpg">
          <img src="/img/mom.png">
          <img src="/img/girl.jpeg">
          <img src="/img/cidss.jpeg">
          <img src="/img/BUSY-P.jpg">
          <img src="/img/mom.png">
          <img src="/img/girl.jpeg">
          <img src="/img/cidss.jpeg">
          <img src="/img/BUSY-P.jpg">
          <img src="/img/mom.png">
          <img src="/img/girl.jpeg">
          <img src="/img/cidss.jpeg">
          <img src="/img/BUSY-P.jpg">
          <img src="/img/mom.png">
        </div>

        <!-- RIGHT CONTENT -->
        <div class="aj-trust-content">
          <div class="aj-trust-icon">❤</div>
          <p class="aj-trust-title">Trusted and Loved By</p>
          <h2 class="aj-trust-highlight">1Million+</h2>
          <div class="aj-trust-subtitle">Parents, Kids & Experts</div>
          <div class="aj-trust-stats">
            <div class="aj-trust-stat">
              <strong>500K+</strong>
              <span>Parents</span>
            </div>
            <div class="aj-trust-stat">
              <strong>4.9★</strong>
              <span>Rating</span>
            </div>
            <div class="aj-trust-stat">
              <strong>100%</strong>
              <span>Safe</span>
            </div>
          </div>
        </div>

      </div>


    </div>
  </section>
 
  <div class="newsletter reveal">
    <span class="sec-eye">Stay in the Loop</span>
    <h2 class="sec-title">Wellness Tips for Your Little Ones</h2>
    <p class="nl-sub">Join 25,000+ parents getting Ayurvedic parenting tips, exclusive discounts & early product access
      every week.</p>
    <form class="nl-form newsletterSubscribeForm" action="{{ route('newsletter.subscribe') }}" method="POST">
      @csrf
      <input type="hidden" name="source" value="newsletter_block">
      <input class="nl-input" type="email" name="email" maxlength="50" placeholder="Enter your email address" required>
      <button class="hbtn hbtn-main" type="submit" style="padding:13px 28px;font-size:.9rem">Subscribe</button>
      <div class="newsletterSubscribeMessage" style="display:none;width:100%;margin-top:8px;font-size:.82rem;font-weight:800;text-align:center;"></div>
    </form>
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

    // Accordion toggle function

    function toggle(header) {
      const item = header.closest('.acc-item');
      const isOpen = item.classList.contains('open');
      document.querySelectorAll('.acc-item.open').forEach(el => el.classList.remove('open'));
      if (!isOpen) item.classList.add('open');
    }
  </script>
@endpush
