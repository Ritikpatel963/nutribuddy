@extends('layouts.user-panel')
@section('title', 'Health Scores')


@section('panel-content')

<!-- ══ PAGE ══ -->
<div class="hs-page">

  <!-- ── Header ── -->
  <div class="hs-page-head hs-anim-1">
    <h1 class="hs-page-title">Arjun's Health Scores</h1>
    <p class="hs-page-meta">
      3-month progress · Started at 54 · Now 75 ·
      <span class="hs-pts-badge">↑ +21 points</span>
    </p>
  </div>

  <!-- ── Stat Tiles ── -->
  <div class="hs-tiles hs-anim-2">

    <div class="hs-tile">
      <div class="hs-tile-val color-mn" id="score-val">75</div>
      <div class="hs-tile-label">Overall score</div>
      <span class="hs-tile-change up">↑ +21 pts in 90 days</span>
    </div>

    <div class="hs-tile">
      <div class="hs-tile-val color-or">18 <small style="font-size:.55em;letter-spacing:0">kg</small></div>
      <div class="hs-tile-label">Weight</div>
      <span class="hs-tile-change up">↑ +1 kg this quarter</span>
    </div>

    <div class="hs-tile">
      <div class="hs-tile-val color-pu">106 <small style="font-size:.45em;letter-spacing:0">cm</small></div>
      <div class="hs-tile-label">Height</div>
      <span class="hs-tile-change up">↑ +2 cm in 60 days</span>
    </div>

    <div class="hs-tile">
      <div class="hs-tile-val color-red">1<small style="font-size:.5em;letter-spacing:0">×</small></div>
      <div class="hs-tile-label">Sick this qtr</div>
      <span class="hs-tile-change good">↓ from 4× before</span>
    </div>

  </div>

  <!-- ── Score Cards 2×2 ── -->
  <div class="hs-score-grid hs-anim-3">

    <!-- Immunity -->
    <div class="hs-score-card">
      <div class="hs-score-top">
        <div class="hs-score-left">
          <div class="hs-score-name">
            <span class="hs-score-icon" style="background:var(--mnl);color:var(--mn);">💧</span>
            Immunity
          </div>
          <div class="hs-score-subs">Vit C · Zinc · Vit D · Probiotics</div>
        </div>
        <div class="hs-score-num" style="color:var(--mnd);">87</div>
      </div>
      <div class="hs-bar-wrap">
        <div class="hs-bar-fill c-mn" style="width:87%"></div>
      </div>
      <div class="hs-score-note">Sick episodes: 4 → 1 this quarter</div>
    </div>

    <!-- Brain & Focus -->
    <div class="hs-score-card">
      <div class="hs-score-top">
        <div class="hs-score-left">
          <div class="hs-score-name">
            <span class="hs-score-icon" style="background:var(--pkl);color:var(--pk);">🧠</span>
            Brain &amp; Focus
          </div>
          <div class="hs-score-subs">Omega-3 · Iron · B12 · Folate</div>
        </div>
        <div class="hs-score-num" style="color:var(--pu);">72</div>
      </div>
      <div class="hs-bar-wrap">
        <div class="hs-bar-fill c-pu" style="width:72%"></div>
      </div>
      <div class="hs-score-note">Omega-3 gap remains — Brain gummy filling shortfall</div>
    </div>

    <!-- Growth -->
    <div class="hs-score-card">
      <div class="hs-score-top">
        <div class="hs-score-left">
          <div class="hs-score-name">
            <span class="hs-score-icon" style="background:var(--yel);color:#9a6700;">📏</span>
            Growth
          </div>
          <div class="hs-score-subs">Calcium · Protein · Vit D · Zinc</div>
        </div>
        <div class="hs-score-num" style="color:var(--or);">79</div>
      </div>
      <div class="hs-bar-wrap">
        <div class="hs-bar-fill c-ye" style="width:79%"></div>
      </div>
      <div class="hs-score-note">Height: 104 → 106 cm (+2 cm) · Weight: 17 → 18 kg (+1 kg)</div>
    </div>

    <!-- Diet Adherence -->
    <div class="hs-score-card">
      <div class="hs-score-top">
        <div class="hs-score-left">
          <div class="hs-score-name">
            <span class="hs-score-icon" style="background:var(--redl);color:var(--red);">🍎</span>
            Diet adherence
          </div>
          <div class="hs-score-subs">Meal plan · New foods tried</div>
        </div>
        <div class="hs-score-num" style="color:var(--red);">68</div>
      </div>
      <div class="hs-bar-wrap">
        <div class="hs-bar-fill c-red" style="width:68%"></div>
      </div>
      <div class="hs-score-note">7 new foods accepted in 3 months (target: 9)</div>
    </div>

  </div>

  <!-- ── Nutrient Gap Status ── -->
  <div class="hs-gap-card hs-anim-4">
    <div class="hs-gap-title-bar">
      <div class="hs-gap-title">Nutrient gap status</div>
    </div>

    <div class="hs-gap-row critical">
      <div class="hs-gap-info">
        <div class="hs-gap-name">Vitamin D</div>
        <div class="hs-gap-desc">Diet: 2 mcg/day — gap: 13 mcg</div>
      </div>
      <span class="hs-gap-status critical">⚠ Critical</span>
    </div>

    <div class="hs-gap-row gap">
      <div class="hs-gap-info">
        <div class="hs-gap-name">Omega-3 DHA</div>
        <div class="hs-gap-desc">Plant sources only — conversion low</div>
      </div>
      <span class="hs-gap-status gap">Gap</span>
    </div>

    <div class="hs-gap-row gap">
      <div class="hs-gap-info">
        <div class="hs-gap-name">Iron</div>
        <div class="hs-gap-desc">7.2 mg/day — target: 9 mg</div>
      </div>
      <span class="hs-gap-status gap">Gap</span>
    </div>

    <div class="hs-gap-row partial">
      <div class="hs-gap-info">
        <div class="hs-gap-name">Calcium</div>
        <div class="hs-gap-desc">540 mg/day — target: 600 mg</div>
      </div>
      <span class="hs-gap-status partial">Partial</span>
    </div>

    <div class="hs-gap-row met">
      <div class="hs-gap-info">
        <div class="hs-gap-name">Vitamin C</div>
        <div class="hs-gap-desc">48 mg/day — target: 40 mg ✓</div>
      </div>
      <span class="hs-gap-status met">Met ✓</span>
    </div>

    <div class="hs-gap-row met">
      <div class="hs-gap-info">
        <div class="hs-gap-name">Protein</div>
        <div class="hs-gap-desc">15.8 g/day — target: 16 g</div>
      </div>
      <span class="hs-gap-status met">Met ✓</span>
    </div>

  </div>

  <!-- ── CTA ── -->
  <div class="hs-cta-wrap hs-anim-5">
    <a href="{{ route('check-in') }}">
      <button class="hs-cta-btn" style="cursor: pointer;">
        Quarterly check-in → update scores
        <span class="hs-cta-arrow">→</span>
      </button>
    </a>
  </div>

</div>

   @push('scripts')

<script>
/* ── animated counter for score tiles ── */
function countUp(el, target, duration = 1200) {
  const start = 0;
  const step = target / (duration / 16);
  let current = start;
  const tick = () => {
    current = Math.min(current + step, target);
    el.textContent = Math.round(current);
    if (current < target) requestAnimationFrame(tick);
  };
  requestAnimationFrame(tick);
}

/* run on load */
window.addEventListener('DOMContentLoaded', () => {
  const scoreEl = document.getElementById('score-val');
  setTimeout(() => countUp(scoreEl, 75, 1400), 200);
});

/* CTA handler */
function handleCheckin() {
  const btn = document.querySelector('.hs-cta-btn');
  btn.textContent = '✓ Check-in initiated…';
  btn.style.background = 'linear-gradient(135deg, var(--mn), var(--pu))';
  setTimeout(() => {
    btn.innerHTML = 'Quarterly check-in → update scores <span class="hs-cta-arrow">→</span>';
    btn.style.background = '';
  }, 2500);
}
</script>

    @endpush
@endsection