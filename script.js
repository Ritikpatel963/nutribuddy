/* ══════════════════════════════════════════════════════════════
   NUTRIBUDDY — MAIN SCRIPT
   Sections:
   01. Custom Cursor (simple)
   02. Nav Scroll Shadow
   03. Hamburger / Mobile Menu
   04. Cart Popup
   05. Hero Slider + Sparks
   06. Stars Background (Ingredients)
   07. Ingredient Tabs
   08. Add to Cart
   09. Scroll Reveal
   10. Counter Animation
   11. GSAP Animations
   12. Video Reels (slider with prev/next buttons)
   13. FAQ Accordion
   14. Certificate Carousel
   15. Diet Chart — State & Logic
══════════════════════════════════════════════════════════════ */

function initNutriBuddy() {

/* ══════════════════════════════════════════
   01. CUSTOM CURSOR (simple dot, no ring)
══════════════════════════════════════════ */
const cur = document.getElementById('cur');
if (cur) {
  document.addEventListener('mousemove', e => {
    cur.style.left = e.clientX + 'px';
    cur.style.top  = e.clientY + 'px';
  });
  document.addEventListener('mouseleave', () => { cur.style.opacity = '0'; });
  document.addEventListener('mouseenter', () => { cur.style.opacity = '1'; });
  document.querySelectorAll('button, a, .pc, .reel, .itab, .age-card, .gender-card, .prob-tag, .dpref, .atag, .imgcar-item, .wrev, .faq-q, .tc').forEach(el => {
    el.addEventListener('mouseenter', () => document.body.classList.add('cursor-hover'));
    el.addEventListener('mouseleave', () => document.body.classList.remove('cursor-hover'));
  });
}

/* ══════════════════════════════════════════
   02. NAV SCROLL SHADOW
══════════════════════════════════════════ */
window.addEventListener('scroll', () => {
  const nav = document.getElementById('mainNav');
  if (nav) nav.classList.toggle('scrolled', window.scrollY > 60);
});

/* ══════════════════════════════════════════
   03. HAMBURGER / MOBILE MENU
══════════════════════════════════════════ */
const hamburgerBtn = document.getElementById('hamburgerBtn');
const mobileMenu   = document.getElementById('mobileMenu');
const menuOverlay  = document.getElementById('menuOverlay');

function openMenu() {
  hamburgerBtn.classList.add('open');
  mobileMenu.classList.add('open');
  menuOverlay.classList.add('open');
  hamburgerBtn.setAttribute('aria-expanded', 'true');
  mobileMenu.setAttribute('aria-hidden', 'false');
  document.body.style.overflow = 'hidden';
}
function closeMenu() {
  hamburgerBtn.classList.remove('open');
  mobileMenu.classList.remove('open');
  menuOverlay.classList.remove('open');
  hamburgerBtn.setAttribute('aria-expanded', 'false');
  mobileMenu.setAttribute('aria-hidden', 'true');
  document.body.style.overflow = '';
}

if (hamburgerBtn) {
  hamburgerBtn.addEventListener('click', () =>
    mobileMenu.classList.contains('open') ? closeMenu() : openMenu()
  );
}
if (menuOverlay) menuOverlay.addEventListener('click', closeMenu);
if (mobileMenu)  mobileMenu.querySelectorAll('a').forEach(a => a.addEventListener('click', closeMenu));
window.addEventListener('resize', () => { if (window.innerWidth > 640) closeMenu(); });
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeMenu(); });


/* ══════════════════════════════════════════
   04. CART POPUP
══════════════════════════════════════════ */
const cartPopup    = document.getElementById('cart-popup');
const closeCartBtn = document.getElementById('closeCart');
const cartIconBtn  = document.getElementById('cartIconBtn');
const cartCountEl  = document.getElementById('cartCount');

function toggleCart() {
  if (cartPopup) cartPopup.classList.toggle('open');
}

if (cartIconBtn) {
  cartIconBtn.addEventListener('click', e => {
    e.stopPropagation();
    toggleCart();
    if (cartCountEl) {
      cartCountEl.style.animation = 'none';
      void cartCountEl.offsetWidth;
      cartCountEl.style.animation = 'cartPop .3s cubic-bezier(.34,1.56,.64,1)';
    }
  });
}
if (closeCartBtn) closeCartBtn.addEventListener('click', () => cartPopup.classList.remove('open'));

document.addEventListener('click', e => {
  if (cartPopup && cartPopup.classList.contains('open')) {
    if (!cartPopup.contains(e.target) && e.target !== cartIconBtn && !cartIconBtn.contains(e.target)) {
      cartPopup.classList.remove('open');
    }
  }
});

document.querySelectorAll('.single-cart-box button').forEach(btn => {
  btn.addEventListener('click', e => {
    e.stopPropagation();
    const box = btn.closest('.single-cart-box');
    box.style.transition = 'opacity .3s, transform .3s';
    box.style.opacity = '0';
    box.style.transform = 'translateX(30px)';
    setTimeout(() => box.remove(), 300);
  });
});


/* ══════════════════════════════════════════
   05. HERO SLIDER + SPARKS
══════════════════════════════════════════ */
let currentSlide = 0;
let sliderTimer;
const slides = document.querySelectorAll('.slide');
const dots   = document.querySelectorAll('.dot');

function goTo(n) {
  if (!slides.length) return;
  slides[currentSlide].classList.remove('active');
  if (dots[currentSlide]) dots[currentSlide].classList.remove('active');
  currentSlide = (n + slides.length) % slides.length;
  slides[currentSlide].classList.add('active');
  if (dots[currentSlide]) dots[currentSlide].classList.add('active');
  spawnSparks();
}
function startTimer() {
  clearInterval(sliderTimer);
  sliderTimer = setInterval(() => goTo(currentSlide + 1), 5000);
}

const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');
if (prevBtn) prevBtn.addEventListener('click', () => { goTo(currentSlide - 1); startTimer(); });
if (nextBtn) nextBtn.addEventListener('click', () => { goTo(currentSlide + 1); startTimer(); });
dots.forEach(d => d.addEventListener('click', () => { goTo(+d.dataset.dot); startTimer(); }));
startTimer();

function spawnSparks() {
  const colors = ['#FF4D8F', '#FFD600', '#00D68F'];
  for (let i = 0; i < 12; i++) {
    const sp = document.createElement('div');
    sp.style.cssText = `
      left:${20 + Math.random() * 60}%;
      top:${30 + Math.random() * 40}%;
      background:${colors[i % 3]};
      width:${6 + Math.random() * 8}px;
      height:${6 + Math.random() * 8}px;
      position:fixed; z-index:9999; border-radius:50%;
      pointer-events:none;
      animation:sparkUp ${.5 + Math.random() * .8}s ease forwards;
      animation-delay:${Math.random() * .2}s
    `;
    document.body.appendChild(sp);
    setTimeout(() => sp.remove(), 1200);
  }
}

const ks = document.createElement('style');
ks.textContent = '@keyframes sparkUp{0%{opacity:1;transform:translateY(0) scale(1) rotate(0deg)}100%{opacity:0;transform:translateY(-80px) scale(0) rotate(180deg)}}';
document.head.appendChild(ks);


/* ══════════════════════════════════════════
   06. STARS BACKGROUND (Ingredients)
══════════════════════════════════════════ */
const sb = document.getElementById('starsBg');
if (sb) {
  for (let i = 0; i < 140; i++) {
    const s = document.createElement('div');
    s.className = 'sdot';
    const sz = Math.random() * 2.5 + .8;
    s.style.cssText = `width:${sz}px;height:${sz}px;left:${Math.random()*100}%;top:${Math.random()*100}%;--dur:${2+Math.random()*4}s;--del:${Math.random()*5}s`;
    sb.appendChild(s);
  }
}


/* ══════════════════════════════════════════
   07. INGREDIENT TABS
══════════════════════════════════════════ */
document.querySelectorAll('.itab').forEach(tab => {
  tab.addEventListener('click', () => {
    document.querySelectorAll('.itab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.ing-panel').forEach(p => p.classList.remove('active'));
    tab.classList.add('active');
    const panel = document.getElementById('ing-panel-' + tab.dataset.ing);
    if (panel) panel.classList.add('active');
  });
});


/* ══════════════════════════════════════════
   08. ADD TO CART BUTTONS
══════════════════════════════════════════ */
document.querySelectorAll('.btn-add').forEach(btn => {
  btn.addEventListener('click', () => {
    btn.classList.add('added');
    setTimeout(() => btn.classList.remove('added'), 1800);
  });
});


/* ══════════════════════════════════════════
   09. SCROLL REVEAL
══════════════════════════════════════════ */
const revObs = new IntersectionObserver(entries => {
  entries.forEach(e => {
    if (e.isIntersecting) {
      e.target.classList.add('visible');
      revObs.unobserve(e.target);
    }
  });
}, { threshold: .1 });
document.querySelectorAll('.reveal').forEach(r => revObs.observe(r));


/* ══════════════════════════════════════════
   10. COUNTER ANIMATION
══════════════════════════════════════════ */
const countObs = new IntersectionObserver(entries => {
  entries.forEach(e => {
    if (!e.isIntersecting) return;
    const el     = e.target;
    const target = +el.dataset.count;
    const suffix = el.textContent.includes('%') ? '%' : '+';
    let start = 0;
    const dur = 1800, step = dur / 60, inc = target / 60;
    const iv = setInterval(() => {
      start = Math.min(start + inc, target);
      el.textContent = Math.round(start).toLocaleString('en-IN') + (start >= target ? suffix : '');
      if (start >= target) clearInterval(iv);
    }, step);
    countObs.unobserve(el);
  });
}, { threshold: .5 });
document.querySelectorAll('[data-count]').forEach(el => countObs.observe(el));


/* ══════════════════════════════════════════
   11. GSAP ANIMATIONS
══════════════════════════════════════════ */
if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
  gsap.registerPlugin(ScrollTrigger);

  gsap.utils.toArray('.pc').forEach((c, i) => {
    gsap.from(c, { y: 70, opacity: 0, rotation: -4, duration: .9, delay: i * .15, ease: 'back.out(1.5)', scrollTrigger: { trigger: c, start: 'top 88%' } });
  });
  gsap.utils.toArray('.wc').forEach((c, i) => {
    gsap.from(c, { y: 50, opacity: 0, duration: .75, delay: i * .1, ease: 'power3.out', scrollTrigger: { trigger: c, start: 'top 88%' } });
  });
  gsap.utils.toArray('.tc').forEach((c, i) => {
    gsap.from(c, { y: 40, opacity: 0, scale: .92, duration: .7, delay: i * .08, ease: 'back.out(1.3)', scrollTrigger: { trigger: c, start: 'top 90%' } });
  });
  gsap.utils.toArray('.step').forEach((s, i) => {
    gsap.from(s, { x: -40, opacity: 0, duration: .7, delay: i * .18, ease: 'power3.out', scrollTrigger: { trigger: s, start: 'top 88%' } });
  });
  gsap.utils.toArray('.wrev').forEach((r, i) => {
    gsap.from(r, { y: 40, opacity: 0, duration: .65, delay: i * .12, ease: 'power3.out', scrollTrigger: { trigger: r, start: 'top 88%' } });
  });

  document.querySelectorAll('.ing-planet').forEach((p, i) => {
    gsap.to(p, {
      boxShadow: `0 0 120px var(--pglow,rgba(0,214,143,.5)),inset 0 0 40px rgba(255,255,255,.06)`,
      duration: 2, repeat: -1, yoyo: true, ease: 'sine.inOut', delay: i * .5
    });
  });

  document.querySelectorAll('.sball').forEach(b => {
    b.addEventListener('mouseenter', () => gsap.to(b, { rotation: 20, scale: 1.18, duration: .35, ease: 'back.out(2)' }));
    b.addEventListener('mouseleave', () => gsap.to(b, { rotation: 0, scale: 1, duration: .45, ease: 'elastic.out(1,.5)' }));
  });

  gsap.from('.imgcar-item', {
    y: 50, opacity: 0, scale: .92,
    stagger: .1, duration: .75,
    ease: 'back.out(1.4)',
    scrollTrigger: { trigger: '#certificates', start: 'top 82%' }
  });
}


/* ══════════════════════════════════════════
   12. VIDEO REELS — slider with prev/next buttons
══════════════════════════════════════════ */
(function () {
  const track    = document.getElementById('reelsRow');
  const viewport = document.getElementById('reelsViewport');
  const btnPrev  = document.getElementById('reelPrev');
  const btnNext  = document.getElementById('reelNext');
  const dots     = document.querySelectorAll('.reels-dot');
  if (!track || !viewport) return;

  const cards    = Array.from(track.querySelectorAll('.reel'));
  const GAP      = 16;
  let current    = 0;
  let activeReel = -1;
  let reelTimers = {};

  function perView() {
    const vw = viewport.offsetWidth;
    const cw = cards[0] ? cards[0].offsetWidth : 200;
    return Math.max(1, Math.floor((vw + GAP) / (cw + GAP)));
  }

  function maxIndex() {
    return Math.max(0, cards.length - perView());
  }

  function goTo(idx) {
    current = Math.max(0, Math.min(idx, maxIndex()));
    const cw     = cards[0] ? cards[0].offsetWidth : 200;
    const offset = current * (cw + GAP);
    track.style.transform = 'translateX(-' + offset + 'px)';

    dots.forEach((d, i) => d.classList.toggle('active', i === current));
    if (btnPrev) btnPrev.disabled = current === 0;
    if (btnNext) btnNext.disabled = current >= maxIndex();
  }

  if (btnPrev) btnPrev.addEventListener('click', () => goTo(current - 1));
  if (btnNext) btnNext.addEventListener('click', () => goTo(current + 1));
  dots.forEach(d => d.addEventListener('click', () => goTo(+d.dataset.index)));

  let touchStartX = 0;
  viewport.addEventListener('touchstart', e => {
    touchStartX = e.touches[0].clientX;
  }, { passive: true });
  viewport.addEventListener('touchend', e => {
    const dx = touchStartX - e.changedTouches[0].clientX;
    if (Math.abs(dx) > 40) goTo(dx > 0 ? current + 1 : current - 1);
  });

  let resizeTimer;
  window.addEventListener('resize', () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(() => goTo(Math.min(current, maxIndex())), 120);
  });

  function playReel(idx) {
    stopAllReels();
    const card = document.querySelector('.reel[data-reel="' + idx + '"]');
    const bar  = document.getElementById('rb' + idx);
    const btn  = document.getElementById('rp' + idx);
    if (!card) return;
    activeReel = idx;
    if (bar) { bar.style.transition = 'width 8s linear'; bar.style.width = '100%'; }
    if (btn) btn.textContent = '⏸';
    reelTimers[idx] = setTimeout(() => {
      stopReel(idx);
      playReel((idx + 1) % cards.length);
    }, 8000);
  }

  function stopReel(idx) {
    const bar = document.getElementById('rb' + idx);
    const btn = document.getElementById('rp' + idx);
    if (bar) { bar.style.transition = 'none'; bar.style.width = '0%'; }
    if (btn) btn.textContent = '▶';
    clearTimeout(reelTimers[idx]);
    if (activeReel === idx) activeReel = -1;
  }

  function stopAllReels() {
    cards.forEach((_, i) => stopReel(i));
  }

  cards.forEach(r => {
    r.addEventListener('click', () => {
      const i = +r.dataset.reel;
      if (activeReel === i) stopReel(i);
      else playReel(i);
    });
  });

  const rvObs = new IntersectionObserver(entries => {
    if (entries[0].isIntersecting && activeReel === -1) {
      setTimeout(() => playReel(0), 700);
      rvObs.disconnect();
    }
  }, { threshold: .3 });
  const testiSection = document.querySelector('.testi-section');
  if (testiSection) rvObs.observe(testiSection);

  goTo(0);
})();


/* ══════════════════════════════════════════
   13. FAQ ACCORDION
══════════════════════════════════════════ */
document.querySelectorAll('.faq-q').forEach(btn => {
  btn.addEventListener('click', () => {
    const item    = btn.parentElement;
    const wasOpen = item.classList.contains('open');
    document.querySelectorAll('.faq-item').forEach(i => i.classList.remove('open'));
    if (!wasOpen) item.classList.add('open');
  });
});


/* ══════════════════════════════════════════
   14. CERTIFICATE CAROUSEL
══════════════════════════════════════════ */
(function () {
  const GAP  = 20;
  const AUTO = 3500;

  const track = document.getElementById('imgcarTrack');
  const vp    = document.getElementById('imgcarViewport');
  const pbar  = document.getElementById('imgcarPbar');
  const dotsW = document.getElementById('imgcarDots');
  const btnP  = document.getElementById('imgcarPrev');
  const btnN  = document.getElementById('imgcarNext');

  if (!track || !vp) return;

  const items = Array.from(track.querySelectorAll('.imgcar-item'));
  let cur = 0, raf, t0;

  function perView() {
    const w = vp.offsetWidth;
    if (w < 420) return 1;
    if (w < 720) return 2;
    return 3;
  }
  function totalPages() { return Math.ceil(items.length / perView()); }

  function buildDots() {
    dotsW.innerHTML = '';
    for (let i = 0; i < totalPages(); i++) {
      const d = document.createElement('button');
      d.className = 'imgcar-dot' + (i === cur ? ' active' : '');
      d.setAttribute('aria-label', 'Slide ' + (i + 1));
      d.addEventListener('click', () => goTo(i));
      dotsW.appendChild(d);
    }
  }

  function render() {
    const n     = perView();
    const vpW   = vp.offsetWidth;
    const cardW = (vpW - GAP * (n - 1)) / n;
    items.forEach(item => {
      item.style.flex     = '0 0 ' + cardW + 'px';
      item.style.maxWidth = cardW + 'px';
    });
    const offset = -(cur * n) * (cardW + GAP);
    track.style.transform = 'translateX(' + offset + 'px)';
    dotsW.querySelectorAll('.imgcar-dot').forEach((d, i) => d.classList.toggle('active', i === cur));
  }

  function goTo(idx) {
    cur = ((idx % totalPages()) + totalPages()) % totalPages();
    render();
    restartAuto();
  }

  function tick(ts) {
    if (!t0) t0 = ts;
    const pct = Math.min(((ts - t0) / AUTO) * 100, 100);
    if (pbar) pbar.style.width = pct + '%';
    if (pct >= 100) {
      if (pbar) pbar.style.width = '0%';
      t0 = null;
      goTo(cur + 1);
      return;
    }
    raf = requestAnimationFrame(tick);
  }
  function restartAuto() {
    cancelAnimationFrame(raf);
    if (pbar) pbar.style.width = '0%';
    t0 = null;
    raf = requestAnimationFrame(tick);
  }
  function stopAuto() {
    cancelAnimationFrame(raf);
    if (pbar) pbar.style.width = '0%';
  }

  if (btnP) btnP.addEventListener('click', () => goTo(cur - 1));
  if (btnN) btnN.addEventListener('click', () => goTo(cur + 1));
  vp.addEventListener('mouseenter', stopAuto);
  vp.addEventListener('mouseleave', restartAuto);

  let tx = 0;
  vp.addEventListener('touchstart', e => { tx = e.touches[0].clientX; }, { passive: true });
  vp.addEventListener('touchend',   e => {
    const dx = tx - e.changedTouches[0].clientX;
    if (Math.abs(dx) > 40) goTo(dx > 0 ? cur + 1 : cur - 1);
  });

  let resizeTimer;
  window.addEventListener('resize', () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(() => {
      if (cur >= totalPages()) cur = totalPages() - 1;
      buildDots();
      render();
    }, 120);
  });

  buildDots();
  render();
  requestAnimationFrame(tick);
})();


/* ══════════════════════════════════════════
   15. DIET CHART — STATE & LOGIC
══════════════════════════════════════════ */
let state = { age: null, gender: null, problems: [], pref: null, allergies: [] };

function selectAge(el) {
  document.querySelectorAll('.age-card').forEach(c => c.classList.remove('selected'));
  el.classList.add('selected');
  state.age = el.dataset.age;
  document.getElementById('ageError').classList.remove('show');
}

function selectGender(el) {
  document.querySelectorAll('.gender-card').forEach(c => c.classList.remove('selected'));
  el.classList.add('selected');
  state.gender = el.dataset.gender;
  document.getElementById('genderError').classList.remove('show');
}

function toggleProb(el) {
  el.classList.toggle('selected');
  const p = el.dataset.prob;
  if (el.classList.contains('selected')) state.problems.push(p);
  else state.problems = state.problems.filter(x => x !== p);
  document.getElementById('probError').classList.remove('show');
}

function selectPref(el) {
  document.querySelectorAll('.dpref').forEach(c => c.classList.remove('selected'));
  el.classList.add('selected');
  state.pref = el.dataset.pref;
  document.getElementById('prefError').classList.remove('show');
}

function toggleAllergy(el) {
  if (el.dataset.allergy === 'none') {
    document.querySelectorAll('.atag').forEach(a => a.classList.remove('selected'));
    el.classList.add('selected');
    state.allergies = ['none'];
    return;
  }
  document.querySelector('.atag[data-allergy="none"]')?.classList.remove('selected');
  el.classList.toggle('selected');
  const a = el.dataset.allergy;
  if (el.classList.contains('selected')) {
    state.allergies = state.allergies.filter(x => x !== 'none');
    state.allergies.push(a);
  } else {
    state.allergies = state.allergies.filter(x => x !== a);
  }
}

function goStep(step) {
  if (step === 2) {
    if (!state.age)    { document.getElementById('ageError').classList.add('show'); return; }
    if (!state.gender) { document.getElementById('genderError').classList.add('show'); return; }
  }
  if (step === 3) {
    if (!state.problems.length) { document.getElementById('probError').classList.add('show'); return; }
  }

  document.querySelectorAll('.step-panel').forEach(p => p.classList.remove('active'));
  document.getElementById('panel' + step).classList.add('active');

  for (let i = 1; i <= 4; i++) {
    const s = document.getElementById('sp' + i);
    if (!s) continue;
    s.classList.remove('active', 'done');
    if (i < step)       { s.classList.add('done');   s.querySelector('.sp-ball').textContent = '✓'; }
    else if (i === step) { s.classList.add('active'); s.querySelector('.sp-ball').textContent = i; }
    else                 { s.querySelector('.sp-ball').textContent = i; }
  }
  for (let i = 1; i <= 3; i++) {
    const l = document.getElementById('line' + i);
    if (l) l.classList.toggle('done', i < step);
  }

  document.getElementById('diet-chart').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function generateChart() {
  if (!state.pref) { document.getElementById('prefError').classList.add('show'); return; }

  document.querySelectorAll('.step-panel').forEach(p => p.classList.remove('active'));
  document.getElementById('loadingState').classList.add('active');

  for (let i = 1; i <= 3; i++) {
    const s = document.getElementById('sp' + i);
    if (s) { s.classList.remove('active'); s.classList.add('done'); s.querySelector('.sp-ball').textContent = '✓'; }
  }
  const sp4 = document.getElementById('sp4');
  if (sp4) sp4.classList.add('active');
  ['line1','line2','line3'].forEach(id => {
    const l = document.getElementById(id);
    if (l) l.classList.add('done');
  });

  setTimeout(() => {
    document.getElementById('loadingState').classList.remove('active');
    buildResult();
    document.getElementById('resultState').classList.add('active');
    document.getElementById('diet-chart').scrollIntoView({ behavior: 'smooth', block: 'start' });
  }, 2600);
}

function buildResult() {
  const ageLabel = {
    '2-3':  'Toddler (2–3 yrs)',
    '4-6':  'Pre-Schooler (4–6 yrs)',
    '7-9':  'Primary Schooler (7–9 yrs)',
    '10-12':'Middle Schooler (10–12 yrs)',
    '13-14':'Teen (13–14 yrs)'
  };
  const probLabel = {
    immunity:'Immunity', brain:'Brain Power', growth:'Growth',
    sleep:'Better Sleep', energy:'Energy', weight:'Healthy Weight',
    digestion:'Digestion', bones:'Strong Bones', mood:'Mood',
    skin:'Skin Health', appetite:'Picky Eating', exam:'Exam Focus'
  };

  document.getElementById('resultTitle').textContent = `Your Child's 7-Day Nutrition Plan`;
  document.getElementById('resultDesc').textContent  =
    `Personalized for a ${ageLabel[state.age]} — focused on ${state.problems.slice(0,2).map(p => probLabel[p] || p).join(' & ')}.`;

  const tags = [];
  if (state.pref) tags.push({ l: state.pref.charAt(0).toUpperCase() + state.pref.slice(1) });
  if (state.age)  tags.push({ l: state.age + ' years' });
  state.problems.slice(0,3).forEach(p => { if (probLabel[p]) tags.push({ l: probLabel[p] }); });
  document.getElementById('resultTags').innerHTML = tags.map(t => `<div class="rtag">${t.l}</div>`).join('');

  const meals = getMealPlan(state.age, state.pref, state.problems);
  document.getElementById('mealGrid').innerHTML = meals.map(m => `
    <div class="meal-card">
      <div class="meal-time">
        <div class="meal-dot" style="background:${m.color}"></div>
        <div class="meal-label" style="color:${m.color}">${m.time}</div>
      </div>
      <div class="meal-name">${m.name}</div>
      <div class="meal-items">${m.items}</div>
      <div class="meal-tag">${m.benefit}</div>
    </div>
  `).join('');

  const nutrients = getNutrients(state.age);
  document.getElementById('nutrientBars').innerHTML = nutrients.map(n => `
    <div class="n-row">
      <div class="n-label">${n.name}</div>
      <div class="n-track"><div class="n-fill" style="width:${n.pct}%;background:${n.color}"></div></div>
      <div class="n-val">${n.val}</div>
    </div>
  `).join('');

  const rec = getProductRec(state.problems);
  document.getElementById('productRec').innerHTML = `
    <div class="prod-info">
      <div class="prod-tag">Recommended for Your Child</div>
      <div class="prod-name">${rec.name}</div>
      <div class="prod-why">${rec.why}</div>
    </div>
    <button class="prod-btn" onclick="alert('Added to cart! 🛒')">Add to Cart — ${rec.price}</button>
  `;

  const tips = getTips(state.problems);
  document.getElementById('tipsGrid').innerHTML = tips.map(t => `
    <div class="tip-card">
      <div class="tip-icon">${t.icon}</div>
      <div>
        <div class="tip-title">${t.title}</div>
        <div class="tip-text">${t.text}</div>
      </div>
    </div>
  `).join('');
}

function getMealPlan(age, pref, problems) {
  const isBrain  = problems.includes('brain') || problems.includes('exam');
  const isGrowth = problems.includes('growth') || problems.includes('bones');
  const isSleep  = problems.includes('sleep');
  const isVeg    = ['vegetarian','vegan'].includes(pref);
  const isNonVeg = pref === 'non-veg';
  return [
    { time:'Wake-Up',     color:'#FF9900', name:'Morning Boost',
      items:'Warm water with lemon + 1 tsp Amla powder + 5 soaked almonds',
      benefit:'Immunity + Digestion' },
    { time:'Breakfast',   color:'#FF4D8F',
      name: isBrain ? 'Brain Power Breakfast' : 'Power Breakfast',
      items: isBrain
        ? (isVeg ? 'Moong dal chilla + Walnut chutney + Fresh fruit smoothie'
                 : 'Scrambled eggs + Multigrain toast + Walnut smoothie')
        : (isVeg ? 'Oats upma with veggies + Banana + Milk'
                 : 'Egg paratha + Banana + Milk'),
      benefit: isBrain ? 'Omega-3 + Focus' : 'Iron + Protein' },
    { time:'Mid-Morning', color:'#00BFFF', name:'Smart Snack',
      items: isVeg ? 'Seasonal fruit + Roasted chana + Coconut water'
                   : 'Seasonal fruit + Boiled egg + Coconut water',
      benefit:'Vitamins + Hydration' },
    { time:'Lunch',       color:'#7C3AED',
      name: isGrowth ? 'Growth Power Lunch' : 'Nutrition Lunch',
      items: isVeg ? 'Brown rice + Dal + Paneer sabzi + Salad + Curd'
                   : 'Brown rice + Dal + Chicken curry + Salad + Curd',
      benefit: isGrowth ? 'Calcium + Protein' : 'Balanced Macro' },
    { time:'Evening',     color:'#00D68F', name:'After-School Refuel',
      items: isBrain ? 'Turmeric milk + Dates + Dark chocolate square'
                     : 'Banana smoothie + Roasted seeds + Whole wheat crackers',
      benefit: isBrain ? 'Memory + Calm' : 'Energy Refuel' },
    { time:'Dinner',      color: isSleep ? '#5B21B6' : '#FF6B35',
      name: isSleep ? 'Sleep Calm Dinner' : 'Growth Dinner',
      items: isSleep
        ? 'Light khichdi + Ghee + Steamed veggies + Warm chamomile milk'
        : (isNonVeg ? 'Roti + Sabzi + Dal + Grilled chicken'
                    : 'Roti + Sabzi + Dal + Curd'),
      benefit: isSleep ? 'L-Theanine + Rest' : 'Protein + Iron' },
  ];
}

function getNutrients(age) {
  const base = {
    '2-3':  { cal:'1200 kcal', pro:'35g',  cal2:'700mg',  iron:'7mg',  vitC:'40mg', omega:'0.5g' },
    '4-6':  { cal:'1400 kcal', pro:'45g',  cal2:'1000mg', iron:'10mg', vitC:'45mg', omega:'0.9g' },
    '7-9':  { cal:'1700 kcal', pro:'55g',  cal2:'1000mg', iron:'10mg', vitC:'45mg', omega:'1.2g' },
    '10-12':{ cal:'2000 kcal', pro:'65g',  cal2:'1300mg', iron:'12mg', vitC:'50mg', omega:'1.4g' },
    '13-14':{ cal:'2200 kcal', pro:'75g',  cal2:'1300mg', iron:'15mg', vitC:'65mg', omega:'1.6g' },
  };
  const d = base[age] || base['7-9'];
  return [
    { name:'Calories',    val:d.cal,   pct:72, color:'linear-gradient(90deg,#FF4D8F,#FFD600)' },
    { name:'Protein',     val:d.pro,   pct:68, color:'linear-gradient(90deg,#7C3AED,#FF4D8F)' },
    { name:'Calcium',     val:d.cal2,  pct:80, color:'linear-gradient(90deg,#00BFFF,#00D68F)' },
    { name:'Iron',        val:d.iron,  pct:60, color:'linear-gradient(90deg,#FF6B35,#FF4D8F)' },
    { name:'Vitamin C',   val:d.vitC,  pct:90, color:'linear-gradient(90deg,#FFD600,#FF6B35)' },
    { name:'Omega-3 DHA', val:d.omega, pct:55, color:'linear-gradient(90deg,#00BFFF,#7C3AED)' },
  ];
}

function getProductRec(problems) {
  if (problems.includes('brain') || problems.includes('exam'))
    return { name:'BrainBoost Chews', price:'₹649',
             why:'Brahmi + Omega-3 DHA + Shankhpushpi — clinically shown to improve focus, memory, and learning scores in 8 weeks.' };
  if (problems.includes('sleep') || problems.includes('mood'))
    return { name:'DreamCalm Drops', price:'₹549',
             why:'Chamomile + L-Theanine + Jatamansi — helps even the most hyperactive kids sleep deeply and wake up refreshed.' };
  return { name:'GrowStrong Gummies', price:'₹599',
           why:'Ashwagandha + Vitamin D3 + Zinc — builds immunity, supports height & bone growth, and tastes amazing!' };
}

function getTips(problems) {
  const pool = [
    { icon:'💧', title:'Hydration is Key',
      text:'Ensure 6–8 glasses of water daily. Add lemon or mint for taste. Coconut water is great for electrolytes.' },
    { icon:'☀️', title:'Morning Sunlight',
      text:'15 mins of sunlight before 9am gives natural Vitamin D — crucial for bone growth and mood.' },
    { icon:'🕐', title:'Consistent Meal Times',
      text:'Fixed meal timings regulate hunger hormones and improve digestion and energy levels throughout the day.' },
    { icon:'🚫', title:'Avoid Ultra-Processed Foods',
      text:'Chips, packaged biscuits, and sugary drinks spike blood sugar and displace nutrient-dense foods.' },
    { icon:'🫙', title:'Add Ghee Daily',
      text:'1 tsp of pure ghee in dal or roti boosts fat-soluble vitamin absorption and supports brain development.' },
    { icon:'😴', title:'Sleep Schedule',
      text:'9–10 hours of sleep is non-negotiable for growth hormone release and memory consolidation.' },
  ];
  if (problems.includes('brain'))
    pool.unshift({ icon:'🍳', title:'Breakfast Before School',
      text:'Never skip breakfast! The brain uses 20% of all energy. A good breakfast improves focus by up to 35%.' });
  if (problems.includes('immunity'))
    pool.unshift({ icon:'🥛', title:'Probiotic Power',
      text:'Include one portion of curd/yogurt daily. Gut health = immune health. 70% of immunity lives in the gut.' });
  return pool.slice(0, 4);
}

function restart() {
  state = { age: null, gender: null, problems: [], pref: null, allergies: [] };
  document.querySelectorAll('.age-card,.gender-card,.prob-tag,.dpref,.atag').forEach(el => el.classList.remove('selected'));
  document.getElementById('resultState').classList.remove('active');
  document.querySelectorAll('.step-panel').forEach(p => p.classList.remove('active'));
  document.getElementById('panel1').classList.add('active');
  for (let i = 1; i <= 4; i++) {
    const s = document.getElementById('sp' + i);
    if (s) { s.classList.remove('active','done'); s.querySelector('.sp-ball').textContent = i; }
  }
  document.getElementById('sp1').classList.add('active');
  ['line1','line2','line3'].forEach(id => {
    const l = document.getElementById(id);
    if (l) l.classList.remove('done');
  });
  document.getElementById('diet-chart').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function printChart() { window.print(); }

window.selectAge = selectAge;
window.selectGender = selectGender;
window.toggleProb = toggleProb;
window.selectPref = selectPref;
window.toggleAllergy = toggleAllergy;
window.goStep = goStep;
window.generateChart = generateChart;
window.restart = restart;
window.printChart = printChart;

}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initNutriBuddy);
} else {
  initNutriBuddy();
}