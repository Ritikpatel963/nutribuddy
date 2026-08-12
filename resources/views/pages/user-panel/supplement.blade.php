@extends('layouts.user-panel')
@section('title', 'supplemets')


@section('panel-content')


<!-- ══ PAGE ══ -->
<div class="nbPage">

  <!-- ── Header ── -->
  <div class="nbPageHead nbA1">
    <h1 class="nbPageTitle">Supplement Schedule</h1>
    <p class="nbPageMeta">
      Arjun
      <span class="nbMetaDot">·</span>
      5 years
      <span class="nbMetaDot">·</span>
      Pure vegetarian
      <span class="nbMetaDot">·</span>
      West India
    </p>
  </div>

  <!-- ── Cards Grid ── -->
  <div class="nbCardGrid nbA2">

    <!-- Immunity Booster Gummy -->
    <div class="nbCard" id="nbCardImmunity">
      <div class="nbCardTop">
        <div class="nbCardIcon nbIconImmunity">🛡️</div>
        <div class="nbCardMeta">
          <div class="nbCardName">Immunity Booster Gummy</div>
          <div class="nbCardWhen">After breakfast daily</div>
          <span class="nbStatusPill nbActive">Active</span>
        </div>
      </div>
      <div class="nbCardDivider"></div>
      <p class="nbCardDesc">
        Vitamin C + Zinc + Vitamin D3 + Amla + Tulsi + Ginger. Give after fatty meal — ghee helps Vit D absorb 3× better.
      </p>
      <div class="nbStatRow">
        <div class="nbStatBox">
          <div class="nbStatVal">1/day</div>
          <div class="nbStatLabel">Dosage</div>
        </div>
        <div class="nbStatBox">
          <div class="nbStatVal">Post breakfast</div>
          <div class="nbStatLabel">When</div>
        </div>
        <div class="nbStatBox">
          <div class="nbStatVal">4–6 wks</div>
          <div class="nbStatLabel">Results</div>
        </div>
      </div>
    </div>

    <!-- Brain Booster Gummy -->
    <div class="nbCard" id="nbCardBrain">
      <div class="nbCardTop">
        <div class="nbCardIcon nbIconBrain">🧠</div>
        <div class="nbCardMeta">
          <div class="nbCardName">Brain Booster Gummy</div>
          <div class="nbCardWhen">After lunch daily</div>
          <span class="nbStatusPill nbActive">Active</span>
        </div>
      </div>
      <div class="nbCardDivider"></div>
      <p class="nbCardDesc">
        DHA Omega-3 + Iron + Zinc + Brahmi + Shankhpushpi. After lunch = peak iron window. Never at breakfast (calcium blocks iron).
      </p>
      <div class="nbStatRow">
        <div class="nbStatBox">
          <div class="nbStatVal">1/day</div>
          <div class="nbStatLabel">Dosage</div>
        </div>
        <div class="nbStatBox">
          <div class="nbStatVal">Post lunch</div>
          <div class="nbStatLabel">When</div>
        </div>
        <div class="nbStatBox">
          <div class="nbStatVal">6–8 wks</div>
          <div class="nbStatLabel">Results</div>
        </div>
      </div>
    </div>

    <!-- Cold & Cough Effervescent — full width -->
    <div class="nbCard nbCardFull nbA3" id="nbCardCold">
      <div class="nbCardTop">
        <div class="nbCardIcon nbIconCold">🍊</div>
        <div class="nbCardMeta">
          <div class="nbCardName">Cold &amp; Cough Effervescent</div>
          <div class="nbCardWhen">On-demand · First sign of cold</div>
          <span class="nbStatusPill nbStock">In Stock</span>
        </div>
      </div>
      <div class="nbCardDivider"></div>
      <p class="nbCardDesc">
        High-dose Vit C + Zinc + Ginger + Mulethi + Tulsi. Dissolve ½ tablet in 50ml warm water. Start within 6 hours for best effect.
      </p>
      <button class="nbReorderBtn" id="nbReorderBtn" onclick="nbHandleReorder()">
        Reorder
        <span class="nbReorderArrow">→</span>
      </button>
    </div>

  </div>

  <!-- ── Critical Absorption Alert ── -->
  <div class="nbAlertBox nbA4">
    <div class="nbAlertHead">
      <div class="nbAlertHeadIcon">⚠</div>
      Critical absorption rules
    </div>
    <div class="nbAlertBody">
      <span class="nbAlertRule">Never give iron with calcium-rich food or milk</span>
      <span class="nbAlertSep">·</span>
      <span class="nbAlertRule">Vit D supplement only with fatty meal</span>
      <span class="nbAlertSep">·</span>
      <span class="nbAlertRule">Tea/coffee within 1hr of iron = 60% loss</span>
      <span class="nbAlertSep">·</span>
      <span class="nbAlertRule">Vitamin C with iron = 3× absorption</span>
      — always pair them
    </div>
  </div>

</div>
   @push('scripts')
<script>
/* ── Reorder button handler ── */
function nbHandleReorder() {
  const btn = document.getElementById('nbReorderBtn');
  btn.innerHTML = '✓ Reordering…';
  btn.style.background = 'var(--nb-mnd)';
  btn.style.color = '#fff';
  btn.style.borderColor = 'var(--nb-mnd)';
  btn.disabled = true;

  setTimeout(() => {
    btn.innerHTML = 'Reordered! <span style="color:var(--nb-mn)">✓</span>';
    btn.style.background = 'var(--nb-mnl)';
    btn.style.color = 'var(--nb-mnd)';
    btn.style.borderColor = 'var(--nb-mn)';

    setTimeout(() => {
      btn.innerHTML = 'Reorder <span class="nbReorderArrow">→</span>';
      btn.style.background = '';
      btn.style.color = '';
      btn.style.borderColor = '';
      btn.disabled = false;
    }, 2500);
  }, 1200);
}

/* ── Card stagger on scroll (IntersectionObserver) ── */
(function nbInitObserver() {
  if (!('IntersectionObserver' in window)) return;
  const cards = document.querySelectorAll('.nbCard');
  const obs = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.style.opacity = '1';
        entry.target.style.transform = 'translateY(0)';
        obs.unobserve(entry.target);
      }
    });
  }, { threshold: 0.12 });

  cards.forEach((card, i) => {
    card.style.opacity = '0';
    card.style.transform = 'translateY(20px)';
    card.style.transition = `opacity .45s ${i * 0.08}s ease, transform .45s ${i * 0.08}s ease, box-shadow .22s, box-shadow .22s`;
    obs.observe(card);
  });
})();
</script>


    @endpush
@endsection