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
let cur = document.getElementById('cur');
let ring = document.getElementById('cur-ring');

if (!cur) {
  cur = document.createElement('div');
  cur.id = 'cur';
  document.body.appendChild(cur);
}
if (!ring) {
  ring = document.createElement('div');
  ring.id = 'cur-ring';
  document.body.appendChild(ring);
}

let mouseX = window.innerWidth / 2;
let mouseY = window.innerHeight / 2;
let ringX = mouseX;
let ringY = mouseY;

const animateRing = () => {
  ringX += (mouseX - ringX) * 0.18;
  ringY += (mouseY - ringY) * 0.18;
  ring.style.left = ringX + 'px';
  ring.style.top = ringY + 'px';
  requestAnimationFrame(animateRing);
};
animateRing();

if (cur) {
  document.addEventListener('mousemove', e => {
    mouseX = e.clientX;
    mouseY = e.clientY;
    cur.style.left = mouseX + 'px';
    cur.style.top = mouseY + 'px';
    cur.style.opacity = '1';
    ring.style.opacity = '1';
  });
  document.addEventListener('mouseleave', () => { cur.style.opacity = '0'; ring.style.opacity = '0'; });
  document.addEventListener('mouseenter', () => { cur.style.opacity = '1'; ring.style.opacity = '1'; });
  document.querySelectorAll('button, a, .pc, .reel, .itab, .age-card, .gender-card, .prob-tag, .dpref, .atag, .imgcar-item, .wrev, .faq-q, .tc').forEach(el => {
    el.addEventListener('mouseenter', () => document.body.classList.add('cursor-hover'));
    el.addEventListener('mouseleave', () => document.body.classList.remove('cursor-hover'));
  });
  document.body.classList.add('has-custom-cursor');
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
const PENDING_CART_KEY = 'nb_pending_cart';

function getCartCount(items) {
  return (items || []).reduce((sum, it) => sum + Number(it.quantity || 0), 0);
}

function getPendingCartItems() {
  try {
    const raw = localStorage.getItem(PENDING_CART_KEY);
    const parsed = raw ? JSON.parse(raw) : [];
    if (!Array.isArray(parsed)) return [];

    const normalized = parsed.map(it => ({
      ...it,
      unit_price: normalizePendingUnitPrice(it?.unit_price)
    }));

    if (JSON.stringify(normalized) !== JSON.stringify(parsed)) {
      savePendingCartItems(normalized);
    }

    return normalized;
  } catch (_) {
    return [];
  }
}

function savePendingCartItems(items) {
  localStorage.setItem(PENDING_CART_KEY, JSON.stringify(items || []));
}

function getPendingCartItemKey(productId, productVariantId = null) {
  return `${Number(productId || 0)}::${Number(productVariantId || 0)}`;
}

function removePendingCartItem(productId, productVariantId = null) {
  const targetKey = getPendingCartItemKey(productId, productVariantId);
  savePendingCartItems(
    getPendingCartItems().filter(it => getPendingCartItemKey(it.product_id, it.product_variant_id) !== targetKey)
  );
}

function addPendingCartItem(productId, quantity = 1, productVariantId = null, meta = {}) {
  const current = getPendingCartItems();
  const found = current.find(it => getPendingCartItemKey(it.product_id, it.product_variant_id) === getPendingCartItemKey(productId, productVariantId));
  if (found) {
    found.quantity = Number(found.quantity || 0) + Number(quantity || 1);
    found.product_name = meta.product_name || found.product_name || 'Product';
    found.variant_name = meta.variant_name || found.variant_name || '';
    found.image = meta.image || found.image || '/img/product2.png';
    found.unit_price = Number(meta.unit_price ?? found.unit_price ?? 0);
    found.product_url = meta.product_url || found.product_url || '/product';
  } else {
    current.push({
      product_id: Number(productId),
      product_variant_id: productVariantId ? Number(productVariantId) : null,
      quantity: Number(quantity || 1),
      product_name: meta.product_name || 'Product',
      variant_name: meta.variant_name || '',
      image: meta.image || '/img/product2.png',
      unit_price: Number(meta.unit_price || 0),
      product_url: meta.product_url || '/product'
    });
  }
  savePendingCartItems(current);
}

function getPendingCartCount() {
  return getPendingCartItems().reduce((sum, it) => sum + Number(it.quantity || 0), 0);
}

function getPendingCartSubtotal() {
  return getPendingCartItems().reduce((sum, it) => sum + (Number(it.unit_price || 0) * Number(it.quantity || 0)), 0);
}

function updatePendingCartItemQuantity(productId, productVariantId = null, quantity = 1) {
  const targetKey = getPendingCartItemKey(productId, productVariantId);
  savePendingCartItems(
    getPendingCartItems().map(it => {
      if (getPendingCartItemKey(it.product_id, it.product_variant_id) !== targetKey) return it;
      return { ...it, quantity: Math.max(1, Number(quantity || 1)) };
    })
  );
}

function formatCartMoney(value, maximumFractionDigits = 2) {
  return `Rs. ${Number(value || 0).toLocaleString('en-IN', { maximumFractionDigits })}`;
}

function extractDisplayedPrice(value) {
  const text = String(value || '');
  const match = text.match(/\d[\d,]*(?:\.\d+)?/);
  if (!match) return 0;
  return Number(match[0].replace(/,/g, '')) || 0;
}

function normalizePendingUnitPrice(value) {
  const price = Number(value || 0);
  if (!Number.isFinite(price) || price <= 0) return 0;
  if (price < 100000) return price;

  const digits = String(Math.round(price)).replace(/\D/g, '');
  if (digits.length < 6) return price;

  const recovered = Number(digits.slice(0, Math.ceil(digits.length / 2))) || 0;
  return recovered > 0 ? recovered : price;
}

function normalizeCartImage(src) {
  if (!src) return '/img/product2.png';
  if (src.startsWith('http://') || src.startsWith('https://') || src.startsWith('/')) return src;
  return `/${src.replace(/^\/+/, '')}`;
}

function clampQty(v) {
  return Math.max(1, Math.min(10, parseInt(v, 10) || 1));
}

function createPopupQuantityField(quantity, onCommit) {
  const qty = clampQty(quantity);
  const wrap = document.createElement('div');
  wrap.className = 'ci-qty-row';
  wrap.innerHTML = `
    <button type="button" class="qty-btn" data-qty-delta="-1" aria-label="Decrease quantity">&minus;</button>
    <input type="number" min="1" max="10" class="qty-val" value="${qty}" aria-label="Quantity">
    <button type="button" class="qty-btn" data-qty-delta="1" aria-label="Increase quantity">+</button>
  `;

  const input = wrap.querySelector('.qty-val');
  let currentQty = qty;

  async function submit(nextVal) {
    const next = clampQty(nextVal);
    if (next === currentQty) { input.value = currentQty; return; }

    wrap.classList.add('is-updating');
    wrap.querySelectorAll('.qty-btn, .qty-val').forEach(el => el.disabled = true);

    try {
      await onCommit(next);
      currentQty = next;
      input.value = next;
    } catch (err) {
      input.value = currentQty;
    } finally {
      wrap.classList.remove('is-updating');
      wrap.querySelectorAll('.qty-btn, .qty-val').forEach(el => el.disabled = false);
    }
  }

  wrap.querySelectorAll('.qty-btn').forEach(btn => {
    btn.addEventListener('click', e => {
      e.stopPropagation();
      submit(clampQty(input.value) + Number(btn.dataset.qtyDelta || 0));
    });
  });

  input.addEventListener('change', () => submit(input.value));
  input.addEventListener('blur',   () => submit(input.value));
  input.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); submit(input.value); } });

  return wrap;
}

function resolveCartItemMeta(productId, productVariantId = null, sourceEl = null) {
  const productPageName  = document.querySelector('.pdp-name');
  const productPagePrice = document.querySelector('.price-now');
  const productPageImage = document.getElementById('mainPdpImage');
  const selectedVariant  = document.querySelector('.qty-opt.active .flavor-name, .qty-opt.active');

  if (productPageName && productPagePrice) {
    return {
      product_name: productPageName.textContent.trim(),
      variant_name: productVariantId ? (selectedVariant?.textContent || '').trim() : '',
      image: normalizeCartImage(productPageImage?.getAttribute('src') || ''),
      unit_price: extractDisplayedPrice(productPagePrice.textContent || ''),
      product_url: window.location.pathname
    };
  }

  const card = sourceEl ? sourceEl.closest('.pc') : null;
  const cardPriceNow = card?.querySelector('.pc-price-now');
  if (card) {
    return {
      product_name: card.querySelector('.pc-name')?.textContent?.trim() || 'Product',
      variant_name: '',
      image: normalizeCartImage(card.querySelector('.default-img, .hover-img, img')?.getAttribute('src') || ''),
      unit_price: extractDisplayedPrice(cardPriceNow?.textContent || card.querySelector('.pc-price')?.textContent || ''),
      product_url: card.querySelector('.pc-name a, .pc-emoji')?.getAttribute('href') || '/product'
    };
  }

  return {
    product_name: 'Product',
    variant_name: '',
    image: '/img/product2.png',
    unit_price: 0,
    product_url: '/product'
  };
}

globalThis.resolveCartItemMeta = resolveCartItemMeta;

if (cartCountEl) {
  cartCountEl.textContent = String(getPendingCartCount());
}

async function syncCartCount() {
  if (!cartCountEl) return;
  try {
    let res = await fetch('/user/cart', { headers: { 'Accept': 'application/json' } });
    if (res.status === 401 || res.status === 419) {
      cartCountEl.textContent = String(getPendingCartCount());
      return;
    }
    if (!res.ok) return;

    if (getPendingCartItems().length) {
      const synced = await syncPendingCartToServer();
      if (synced) {
        res = await fetch('/user/cart', { headers: { 'Accept': 'application/json' } });
        if (!res.ok) return;
      }
    }

    const payload = await res.json().catch(() => ({}));
    const items = payload.cart?.items || [];
    cartCountEl.textContent = String(getCartCount(items));
  } catch (_) {}
}

async function syncPendingCartToServer() {
  const pendingItems = getPendingCartItems();
  if (!pendingItems.length) return false;

  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

  for (const item of pendingItems) {
    const res = await fetch('/user/cart', {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        ...(csrf ? { 'X-CSRF-TOKEN': csrf } : {}),
      },
      body: JSON.stringify({
        product_id: item.product_id,
        product_variant_id: item.product_variant_id,
        quantity: item.quantity || 1,
      })
    }).catch(() => null);

    if (!res || !res.ok) return false;
  }

  localStorage.removeItem(PENDING_CART_KEY);
  return true;
}

async function updateServerCartItemQuantity(itemId, quantity) {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  const res = await fetch(`/user/cart/items/${itemId}`, {
    method: 'PATCH',
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      ...(csrf ? { 'X-CSRF-TOKEN': csrf } : {})
    },
    body: JSON.stringify({ quantity: Math.max(1, Number(quantity || 1)) })
  }).catch(() => null);

  return !!(res && res.ok);
}

function toggleCart() {
  if (!cartPopup) return;
  const willOpen = !cartPopup.classList.contains('open');
  cartPopup.classList.toggle('open');
  if (willOpen) {
    syncCartCount();
    loadCartPopup();
  }
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

window.addEventListener('pageshow', () => syncCartCount());
window.addEventListener('focus',    () => syncCartCount());
document.addEventListener('visibilitychange', () => { if (!document.hidden) syncCartCount(); });

document.addEventListener('click', e => {
  if (cartPopup && cartPopup.classList.contains('open')) {
    if (!cartPopup.contains(e.target) && e.target !== cartIconBtn && !cartIconBtn.contains(e.target)) {
      cartPopup.classList.remove('open');
    }
  }
});

async function loadCartPopup() {
  const itemsWrap = document.getElementById('cartPopupItems');
  const countEl   = document.getElementById('cartPopupCount');
  const subtotalEl = document.getElementById('cartPopupSubtotal');
  if (!itemsWrap || !countEl || !subtotalEl) return;

  itemsWrap.innerHTML = '<div style="padding:10px;color:#888;font-size:.9rem;">Loading...</div>';

  try {
    const res = await fetch('/user/cart', { headers: { 'Accept': 'application/json' } });

    if (res.status === 401 || res.status === 419) {
      /* ── Guest / pending cart ── */
      const pendingItems = getPendingCartItems();
      const pendingCount = getPendingCartCount();
      if (cartCountEl) cartCountEl.textContent = String(pendingCount);
      countEl.textContent   = String(pendingCount);
      subtotalEl.textContent = formatCartMoney(getPendingCartSubtotal());

      if (!pendingItems.length) {
        itemsWrap.innerHTML = '<div style="padding:10px;color:#888;font-size:.9rem;">Your cart is empty.</div>';
        return;
      }

      itemsWrap.innerHTML = '';
      pendingItems.forEach(it => {
        const qty = Number(it.quantity || 1);
        const row = document.createElement('div');
        row.className = 'single-cart-box';
        row.innerHTML = `
          <div class="image-box"><img src="${normalizeCartImage(it.image)}" alt=""></div>
          <div class="cart-popup-content">
            <h5>${it.product_name || 'Product'}</h5>
            <h4>${formatCartMoney(it.unit_price)}</h4>
          </div>
          <button type="button" class="cart-remove-btn" aria-label="Remove">&times;</button>
        `;
        const content = row.querySelector('.cart-popup-content');
        content.appendChild(createPopupQuantityField(qty, async value => {
          updatePendingCartItemQuantity(it.product_id, it.product_variant_id, value);
          if (cartCountEl) cartCountEl.textContent = String(getPendingCartCount());
          loadCartPopup();
        }));
        row.querySelector('.cart-remove-btn').addEventListener('click', e => {
          e.stopPropagation();
          removePendingCartItem(it.product_id, it.product_variant_id);
          if (cartCountEl) cartCountEl.textContent = String(getPendingCartCount());
          loadCartPopup();
        });
        itemsWrap.appendChild(row);
      });
      return;
    }

    if (!res.ok) throw new Error('cart');

    /* ── Authenticated cart ── */
    const payload  = await res.json().catch(() => ({}));
    const items    = payload.cart?.items || [];
    const subtotal = payload.pricing?.subtotal || 0;
    const count    = getCartCount(items);

    if (cartCountEl) cartCountEl.textContent = String(count);
    countEl.textContent    = String(count);
    subtotalEl.textContent = formatCartMoney(subtotal);

    if (!items.length) {
      itemsWrap.innerHTML = '<div style="padding:10px;color:#888;font-size:.9rem;">Your cart is empty.</div>';
      return;
    }

    itemsWrap.innerHTML = '';
    items.forEach(it => {
      const name  = it.product?.name || 'Product';
      const qty   = Number(it.quantity || 1);
      const price = it.product_variant ? it.product_variant.price : it.product?.base_price;
      const img   = it.product?.primary_image?.image_path
        ? '/storage/' + it.product.primary_image.image_path
        : '/img/product2.png';

      const row = document.createElement('div');
      row.className = 'single-cart-box';
      row.innerHTML = `
        <div class="image-box"><img src="${img}" alt=""></div>
        <div class="cart-popup-content">
          <h5>${name}</h5>
          <h4>${formatCartMoney(price)}</h4>
        </div>
        <button type="button" class="cart-remove-btn" aria-label="Remove">&times;</button>
      `;
      const content = row.querySelector('.cart-popup-content');
      content.appendChild(createPopupQuantityField(qty, async value => {
        row.classList.add('is-updating');
        try {
          const ok = await updateServerCartItemQuantity(it.id, value);
          if (ok) loadCartPopup();
        } finally {
          row.classList.remove('is-updating');
        }
      }));
      row.querySelector('.cart-remove-btn').addEventListener('click', async e => {
        e.stopPropagation();
        row.classList.add('is-updating');
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        await fetch(`/user/cart/items/${it.id}`, {
          method: 'DELETE',
          headers: { 'Accept': 'application/json', ...(csrf ? { 'X-CSRF-TOKEN': csrf } : {}) }
        });
        loadCartPopup();
        syncCartCount();
      });
      itemsWrap.appendChild(row);
    });

  } catch (_) {
    itemsWrap.innerHTML = '<div style="padding:10px;color:#888;font-size:.9rem;">Unable to load cart.</div>';
  }
}

/* ══════════════════════════════════════════
   PRODUCT GALLERY
══════════════════════════════════════════ */
const thumbs    = document.querySelectorAll('.thumb');
const mainImgWrap = document.querySelector('.main-img-wrap .p-image');

thumbs.forEach(thumb => {
  thumb.addEventListener('click', () => {
    thumbs.forEach(t => t.classList.remove('active'));
    thumb.classList.add('active');
    const thumbImg = thumb.querySelector('img');
    if (thumbImg && mainImgWrap) {
      const mainImage = mainImgWrap.querySelector('img');
      if (mainImage) mainImage.src = thumbImg.src;
    }
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
  btn.addEventListener('click', async () => {
    const productId = btn.getAttribute('data-id');
    if (!productId) return;

    const prevHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = 'Adding...';

    const added = await addToCart(productId, 1, null, btn);
    if (!added) {
      btn.innerHTML = prevHtml;
      btn.disabled = false;
      return;
    }

    btn.classList.add('added');
    btn.innerHTML = 'Added ✓';
    setTimeout(() => {
      btn.classList.remove('added');
      btn.innerHTML = prevHtml;
      btn.disabled = false;
    }, 1200);
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

  if (document.querySelector('.pc')) {
    gsap.utils.toArray('.pc').forEach((c, i) => {
      gsap.from(c, { y: 70, opacity: 0, rotation: -4, duration: .9, delay: i * .15, ease: 'back.out(1.5)', scrollTrigger: { trigger: c, start: 'top 88%' } });
    });
  }
  if (document.querySelector('.wc')) {
    gsap.utils.toArray('.wc').forEach((c, i) => {
      gsap.from(c, { y: 50, opacity: 0, duration: .75, delay: i * .1, ease: 'power3.out', scrollTrigger: { trigger: c, start: 'top 88%' } });
    });
  }
  if (document.querySelector('.tc')) {
    gsap.utils.toArray('.tc').forEach((c, i) => {
      gsap.from(c, { y: 40, opacity: 0, scale: .92, duration: .7, delay: i * .08, ease: 'back.out(1.3)', scrollTrigger: { trigger: c, start: 'top 90%' } });
    });
  }
  if (document.querySelector('.step')) {
    gsap.utils.toArray('.step').forEach((s, i) => {
      gsap.from(s, { x: -40, opacity: 0, duration: .7, delay: i * .18, ease: 'power3.out', scrollTrigger: { trigger: s, start: 'top 88%' } });
    });
  }
  if (document.querySelector('.wrev')) {
    gsap.utils.toArray('.wrev').forEach((r, i) => {
      gsap.from(r, { y: 40, opacity: 0, duration: .65, delay: i * .12, ease: 'power3.out', scrollTrigger: { trigger: r, start: 'top 88%' } });
    });
  }

  if (document.querySelector('.ing-planet')) {
    document.querySelectorAll('.ing-planet').forEach((p, i) => {
      gsap.to(p, {
        boxShadow: `0 0 120px var(--pglow,rgba(0,214,143,.5)),inset 0 0 40px rgba(255,255,255,.06)`,
        duration: 2, repeat: -1, yoyo: true, ease: 'sine.inOut', delay: i * .5
      });
    });
  }

  if (document.querySelector('.sball')) {
    document.querySelectorAll('.sball').forEach(b => {
      b.addEventListener('mouseenter', () => gsap.to(b, { rotation: 20, scale: 1.18, duration: .35, ease: 'back.out(2)' }));
      b.addEventListener('mouseleave', () => gsap.to(b, { rotation: 0, scale: 1, duration: .45, ease: 'elastic.out(1,.5)' }));
    });
  }

  if (document.querySelector('.imgcar-item') && document.getElementById('certificates')) {
    gsap.from('.imgcar-item', {
      y: 50, opacity: 0, scale: .92,
      stagger: .1, duration: .75,
      ease: 'back.out(1.4)',
      scrollTrigger: { trigger: '#certificates', start: 'top 82%' }
    });
  }
}


/* ══════════════════════════════════════════
   12. VIDEO REELS — slider with prev/next buttons
══════════════════════════════════════════ */
(function () {
  const track    = document.getElementById('reelsRow');
  const viewport = document.getElementById('reelsViewport');
  const btnPrev  = document.getElementById('reelPrev');
  const btnNext  = document.getElementById('reelNext');
  const reelDots = Array.from(document.querySelectorAll('.reels-dot'));
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

  function updateReelDots() {
    const max = maxIndex();
    reelDots.forEach((d, i) => {
      d.style.display = i <= max ? '' : 'none';
      d.classList.toggle('active', i === current);
    });
  }

  function goToReel(idx) {
    current = Math.max(0, Math.min(idx, maxIndex()));
    const cw     = cards[0] ? cards[0].offsetWidth : 200;
    const offset = current * (cw + GAP);
    track.style.transform = 'translateX(-' + offset + 'px)';
    updateReelDots();
    if (btnPrev) btnPrev.disabled = current === 0;
    if (btnNext) btnNext.disabled = current >= maxIndex();
  }

  if (btnPrev) btnPrev.addEventListener('click', () => goToReel(current - 1));
  if (btnNext) btnNext.addEventListener('click', () => goToReel(current + 1));
  reelDots.forEach(d => d.addEventListener('click', () => goToReel(+d.dataset.index)));

  function refresh() { goToReel(Math.min(current, maxIndex())); }
  window.addEventListener('load', refresh);
  document.addEventListener('DOMContentLoaded', refresh);
  refresh();

  let touchStartX = 0;
  viewport.addEventListener('touchstart', e => { touchStartX = e.touches[0].clientX; }, { passive: true });
  viewport.addEventListener('touchend',   e => {
    const dx = touchStartX - e.changedTouches[0].clientX;
    if (Math.abs(dx) > 40) goToReel(dx > 0 ? current + 1 : current - 1);
  });

  let resizeTimer;
  window.addEventListener('resize', () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(refresh, 120);
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

  function stopAllReels() { cards.forEach((_, i) => stopReel(i)); }

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

  goToReel(0);
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
      d.addEventListener('click', () => goToCert(i));
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

  function goToCert(idx) {
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
      goToCert(cur + 1);
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

  if (btnP) btnP.addEventListener('click', () => goToCert(cur - 1));
  if (btnN) btnN.addEventListener('click', () => goToCert(cur + 1));
  vp.addEventListener('mouseenter', stopAuto);
  vp.addEventListener('mouseleave', restartAuto);

  let tx = 0;
  vp.addEventListener('touchstart', e => { tx = e.touches[0].clientX; }, { passive: true });
  vp.addEventListener('touchend',   e => {
    const dx = tx - e.changedTouches[0].clientX;
    if (Math.abs(dx) > 40) goToCert(dx > 0 ? cur + 1 : cur - 1);
  });

  let certResizeTimer;
  window.addEventListener('resize', () => {
    clearTimeout(certResizeTimer);
    certResizeTimer = setTimeout(() => {
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
    if (i < step)        { s.classList.add('done');   s.querySelector('.sp-ball').textContent = '✓'; }
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
      <span class="meal-icon">${m.icon}</span>
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
    {
      time: 'Wake-Up', icon: '🌅', color: '#FF9900',
      name: 'Morning Boost',
      items: 'Warm water with lemon + 1 tsp Amla powder\n+ 5 soaked almonds',
      benefit: 'Immunity + Digestion'
    },
    {
      time: 'Breakfast', icon: '🍳', color: '#FF4D8F',
      name: isBrain ? 'Brain Power Breakfast' : 'Power Breakfast',
      items: isBrain
        ? (isVeg ? 'Moong dal chilla + Walnut chutney + Fresh fruit smoothie' : 'Scrambled eggs + Multigrain toast + Walnut smoothie')
        : (isVeg ? 'Oats upma with veggies + Banana + Milk/Soy milk' : 'Egg paratha + Banana + Milk'),
      benefit: isBrain ? 'Omega-3 + Focus' : 'Iron + Protein'
    },
    {
      time: 'Mid-Morning', icon: '🍎', color: '#00BFFF',
      name: 'Smart Snack',
      items: isVeg
        ? 'Seasonal fruit + Roasted chana or Makhana\n+ Coconut water'
        : 'Seasonal fruit + Boiled egg or Roasted chana\n+ Coconut water',
      benefit: 'Vitamins + Hydration'
    },
    {
      time: 'Lunch', icon: '🍱', color: '#7C3AED',
      name: isGrowth ? 'Growth Power Lunch' : 'Nutrition Lunch',
      items: isVeg
        ? 'Brown rice + Dal + Paneer sabzi + Salad + Curd'
        : 'Brown rice + Dal + Chicken curry or Fish + Salad + Curd',
      benefit: isGrowth ? 'Calcium + Protein' : 'Balanced Macro'
    },
    {
      time: 'Evening', icon: '🥤', color: '#00D68F',
      name: 'After-School Refuel',
      items: isBrain
        ? 'Turmeric milk (Haldi doodh) + Dates + Dark chocolate square'
        : 'Banana smoothie + Roasted seeds\n+ Whole wheat crackers',
      benefit: isBrain ? 'Memory + Calm' : 'Energy Refuel'
    },
    {
      time: 'Dinner', icon: '🌙', color: isSleep ? '#5B21B6' : '#FF6B35',
      name: isSleep ? 'Sleep Calm Dinner' : 'Growth Dinner',
      items: isSleep
        ? 'Light khichdi + Ghee + Steamed veggies\n+ Warm chamomile milk at bedtime'
        : (isNonVeg ? 'Roti + Sabzi + Dal + Grilled chicken or Fish' : 'Roti + Sabzi + Dal + Curd or Paneer'),
      benefit: isSleep ? 'L-Theanine + Rest' : 'Protein + Iron'
    }
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

/* ══════════════════════════════════════════
   PRODUCT PAGE — Variant / Pincode / Cart
══════════════════════════════════════════ */
/* 
  NOTE: selectVariant is defined in product.blade.php with 6 parameters
  for the advanced product page. These are stubs to prevent errors.
*/
function selectVariant(el, id, price, comparePrice) {
  // Stub: selectVariant is defined in product.blade.php if needed
}

function selectFlavor(el, val) {
  // Stub: selectFlavor is defined in product.blade.php if needed
}
function selectQtyOpt(el, targetId, val) {
  // Stub: selectQtyOpt is defined inline in pages if needed
}

function checkPincode() {
  const v   = document.getElementById('pincodeInput').value;
  const res = document.getElementById('pincode-result');
  if (v.length === 6 && /^\d+$/.test(v)) {
    res.style.display = 'block';
    res.textContent   = '✅ Delivery by Tomorrow — Free Shipping!';
    res.style.color   = 'var(--mn)';
  } else {
    res.style.display = 'block';
    res.textContent   = '⚠️ Please enter a valid 6-digit pincode.';
    res.style.color   = 'var(--or)';
  }
}
const pincodeInput = document.getElementById('pincodeInput');
if (pincodeInput) pincodeInput.addEventListener('keydown', e => { if (e.key === 'Enter') checkPincode(); });

async function addToCart(productId, quantity = 1, productVariantId = null, sourceEl = null) {
  let requestSucceeded = false;
  try {
    const resolveMeta = typeof globalThis.resolveCartItemMeta === 'function'
      ? globalThis.resolveCartItemMeta
      : () => ({ product_name: 'Product', variant_name: '', image: '/img/product2.png', unit_price: 0, product_url: '/product' });

    const itemMeta = resolveMeta(productId, productVariantId, sourceEl);
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const res = await fetch('/user/cart', {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        ...(csrf ? { 'X-CSRF-TOKEN': csrf } : {}),
      },
      body: JSON.stringify({ product_id: productId, product_variant_id: productVariantId, quantity })
    });

    const responseUrl            = res.url || '';
    const wasRedirectedToLogin   = res.redirected && /\/login(?:[/?#]|$)/i.test(responseUrl);
    const isGuestFallback        = res.status === 401 || res.status === 403 || res.status === 419 || wasRedirectedToLogin;

    if (isGuestFallback) {
      addPendingCartItem(productId, quantity, productVariantId, itemMeta);
      const pendingCount = getPendingCartCount();
      if (cartCountEl) cartCountEl.textContent = String(pendingCount);
      loadCartPopup();
      _flashCartBtn(sourceEl);
      return true;
    }

    if (!res.ok) {
      const payload = await res.json().catch(() => ({}));
      alert(payload.message || 'Unable to add item to cart.');
      return false;
    }
    requestSucceeded = true;

    const payload   = await res.json().catch(() => ({}));
    const items     = payload.cart?.items || [];
    const countNow  = Number(payload.cart_count || 0) || items.reduce((sum, it) => sum + Number(it.quantity || 0), 0);
    if (cartCountEl) cartCountEl.textContent = String(countNow);
    _flashCartBtn(sourceEl);
    return true;

  } catch (_) {
    if (!requestSucceeded) alert('Unable to add item to cart.');
    return false;
  }
}

function _flashCartBtn(sourceEl) {
  const btn = sourceEl || document.querySelector('.btn-cart');
  if (!btn) return;
  const orig = btn.innerHTML;
  btn.innerHTML         = 'Added to Cart!';
  btn.style.background  = 'var(--mnl)';
  btn.style.color       = 'var(--mn)';
  setTimeout(() => { btn.innerHTML = orig; btn.style.background = ''; btn.style.color = ''; }, 2000);
}

function buyNow(productId, quantity = 1, productVariantId = null, sourceEl = null) {
  addToCart(productId, quantity, productVariantId, sourceEl).then(added => {
    if (added) window.location.href = '/checkout';
  });
}

/* ══════════════════════════════════════════
   EXPOSE GLOBALS
══════════════════════════════════════════ */
window.selectAge       = selectAge;
window.selectGender    = selectGender;
window.toggleProb      = toggleProb;
window.selectPref      = selectPref;
window.toggleAllergy   = toggleAllergy;
window.goStep          = goStep;
window.generateChart   = generateChart;
window.restart         = restart;
window.printChart      = printChart;

// dc-prefixed aliases used in HTML onclick attributes
window.dcSelectAge     = selectAge;
window.dcSelectGender  = selectGender;
window.dcToggleProb    = toggleProb;
window.dcSelectPref    = selectPref;
window.dcToggleAllergy = toggleAllergy;
window.dcGoStep        = goStep;
window.dcGenerateChart = generateChart;
window.dcRestart       = restart;
window.dcPrintChart    = printChart;

window.selectVariant   = selectVariant;
window.selectFlavor    = selectFlavor;
window.selectQtyOpt    = selectQtyOpt;
window.checkPincode    = checkPincode;
window.addToCart       = addToCart;
window.buyNow          = buyNow;
window.syncCartCount   = syncCartCount;   // ✅ exposed so external calls work too

/* ── Initial cart count sync (inside scope — fixes the ReferenceError) ── */
syncCartCount();

} // end initNutriBuddy


/* ══════════════════════════════════════════
   BOOT
══════════════════════════════════════════ */
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initNutriBuddy);
} else {
  initNutriBuddy();
}


/* ══════════════════════════════════════════
   PRODUCT PAGE — SCROLL REVEAL (outside)
══════════════════════════════════════════ */
const ro = new IntersectionObserver(entries => {
  entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); ro.unobserve(e.target); } });
}, { threshold: .1 });
document.querySelectorAll('.reveal').forEach(r => ro.observe(r));


/* ══════════════════════════════════════════
   PRODUCT PAGE — INGREDIENT SECTION
══════════════════════════════════════════ */
(function () {
  const fallbackIngredients = [
    { id:'ashwagandha', name:'Ashwagandha (KSM-66®)', emoji:'', cat:'ay', catLabel:'Ayurvedic', latin:'Withania somnifera',
      desc:'The king of Ayurvedic adaptogens. KSM-66® is the most clinically studied Ashwagandha extract — proven to reduce cortisol, support growth, enhance immunity, and improve sleep quality in children.',
      benefits:['Reduces Stress','Supports Growth','Boosts Immunity','Improves Sleep'],
      dosage:'150mg per gummy', badgeColor:'#00D68F', badgeBg:'rgba(0,214,143,.12)', emojiBg:'rgba(0,214,143,.1)' },
    { id:'brahmi', name:'Brahmi', emoji:'🧠', cat:'ay', catLabel:'Ayurvedic', latin:'Bacopa monnieri',
      desc:'Used in Ayurveda for over 3000 years as a cognitive enhancer. Brahmi improves memory formation, attention span, and information processing speed in growing minds.',
      benefits:['Memory Boost','Focus & Attention','Cognitive Development','Nerve Health'],
      dosage:'100mg per gummy', badgeColor:'#00D68F', badgeBg:'rgba(0,214,143,.12)', emojiBg:'rgba(0,214,143,.1)' },
    { id:'shatavari', name:'Shatavari', emoji:'🌱', cat:'ay', catLabel:'Ayurvedic', latin:'Asparagus racemosus',
      desc:'A powerful rejuvenative herb that supports digestive health, strengthens immunity, and provides natural antioxidant protection for growing bodies.',
      benefits:['Digestive Health','Immunity','Antioxidant','Rejuvenative'],
      dosage:'75mg per gummy', badgeColor:'#00D68F', badgeBg:'rgba(0,214,143,.12)', emojiBg:'rgba(0,214,143,.1)' },
    { id:'vitd3', name:'Vitamin D3', emoji:'☀️', cat:'vi', catLabel:'Vitamin', latin:'Cholecalciferol',
      desc:'The sunshine vitamin — essential for calcium absorption, bone mineralization, and immune function. Over 70% of Indian children are deficient.',
      benefits:['Bone Growth','Calcium Absorption','Immunity','Mood Support'],
      dosage:'400 IU per gummy', badgeColor:'#00BFFF', badgeBg:'rgba(0,191,255,.12)', emojiBg:'rgba(0,191,255,.1)' },
    { id:'vitc', name:'Vitamin C', emoji:'🍊', cat:'vi', catLabel:'Vitamin', latin:'Ascorbic Acid',
      desc:'A powerful antioxidant that strengthens the immune system, aids collagen formation for healthy skin and gums, and enhances iron absorption from plant-based foods.',
      benefits:['Immune Defense','Collagen Formation','Iron Absorption','Antioxidant'],
      dosage:'40mg per gummy', badgeColor:'#00BFFF', badgeBg:'rgba(0,191,255,.12)', emojiBg:'rgba(0,191,255,.1)' },
    { id:'vitb12', name:'Vitamin B12', emoji:'💉', cat:'vi', catLabel:'Vitamin', latin:'Methylcobalamin',
      desc:'Critical for nerve development, red blood cell formation, and energy metabolism. Particularly important for vegetarian children who may not get enough from diet alone.',
      benefits:['Nerve Health','Energy','Red Blood Cells','Brain Development'],
      dosage:'1.2mcg per gummy', badgeColor:'#00BFFF', badgeBg:'rgba(0,191,255,.12)', emojiBg:'rgba(0,191,255,.1)' },
    { id:'vita', name:'Vitamin A', emoji:'👁️', cat:'vi', catLabel:'Vitamin', latin:'Retinol Palmitate',
      desc:'Essential for vision, skin health, and immune function. Supports the maintenance of healthy mucous membranes — the body\'s first line of defense against infections.',
      benefits:['Vision','Skin Health','Immunity','Cell Growth'],
      dosage:'300mcg per gummy', badgeColor:'#00BFFF', badgeBg:'rgba(0,191,255,.12)', emojiBg:'rgba(0,191,255,.1)' },
    { id:'vite', name:'Vitamin E', emoji:'🛡️', cat:'vi', catLabel:'Vitamin', latin:'D-Alpha Tocopherol',
      desc:'A natural fat-soluble antioxidant that protects cell membranes from damage, supports immune health, and also serves as our clean natural preservative.',
      benefits:['Antioxidant','Cell Protection','Skin Health','Immune Support'],
      dosage:'5mg per gummy', badgeColor:'#00BFFF', badgeBg:'rgba(0,191,255,.12)', emojiBg:'rgba(0,191,255,.1)' },
    { id:'vitb6', name:'Vitamin B6', emoji:'⚡', cat:'vi', catLabel:'Vitamin', latin:'Pyridoxine HCl',
      desc:'Crucial for brain development, neurotransmitter synthesis, and energy metabolism. Helps convert food into usable energy for active kids.',
      benefits:['Brain Development','Energy','Neurotransmitters','Metabolism'],
      dosage:'0.6mg per gummy', badgeColor:'#00BFFF', badgeBg:'rgba(0,191,255,.12)', emojiBg:'rgba(0,191,255,.1)' },
    { id:'folic', name:'Folic Acid', emoji:'🧬', cat:'vi', catLabel:'Vitamin', latin:'Pteroylglutamic Acid',
      desc:'Essential for DNA synthesis, cell division, and rapid growth phases. Particularly important during childhood and adolescence when cells are dividing rapidly.',
      benefits:['DNA Synthesis','Cell Division','Growth','Blood Health'],
      dosage:'100mcg per gummy', badgeColor:'#00BFFF', badgeBg:'rgba(0,191,255,.12)', emojiBg:'rgba(0,191,255,.1)' },
    { id:'zinc', name:'Zinc Bisglycinate', emoji:'⚙️', cat:'mi', catLabel:'Mineral', latin:'Zinc Bisglycinate Chelate',
      desc:'The most bioavailable form of zinc — gentle on tiny tummies. Essential for immune function, wound healing, taste perception, and over 300 enzymatic reactions in the body.',
      benefits:['Immunity','Growth','Wound Healing','Taste & Appetite'],
      dosage:'5mg per gummy', badgeColor:'#FFD600', badgeBg:'rgba(255,214,0,.12)', emojiBg:'rgba(255,214,0,.1)' },
    { id:'calcium', name:'Calcium', emoji:'🦴', cat:'mi', catLabel:'Mineral', latin:'Calcium Citrate',
      desc:'The building block of strong bones and teeth. Calcium Citrate is chosen for superior absorption, especially important during the rapid growth years of childhood.',
      benefits:['Bone Strength','Teeth Health','Muscle Function','Nerve Signaling'],
      dosage:'100mg per gummy', badgeColor:'#FFD600', badgeBg:'rgba(255,214,0,.12)', emojiBg:'rgba(255,214,0,.1)' },
    { id:'iron', name:'Iron', emoji:'🩸', cat:'mi', catLabel:'Mineral', latin:'Ferrous Bisglycinate',
      desc:'A chelated form of iron that is up to 4x more absorbable than standard iron, with zero stomach upset. Crucial for oxygen transport, energy, and cognitive development.',
      benefits:['Oxygen Transport','Energy','Cognitive Function','Blood Health'],
      dosage:'4mg per gummy', badgeColor:'#FFD600', badgeBg:'rgba(255,214,0,.12)', emojiBg:'rgba(255,214,0,.1)' },
    { id:'iodine', name:'Iodine', emoji:'🧪', cat:'mi', catLabel:'Mineral', latin:'Potassium Iodide',
      desc:'Essential for thyroid hormone production which regulates metabolism, growth, and brain development. Mild deficiency can impair cognitive function in children.',
      benefits:['Thyroid Health','Brain Development','Metabolism','Growth'],
      dosage:'90mcg per gummy', badgeColor:'#FFD600', badgeBg:'rgba(255,214,0,.12)', emojiBg:'rgba(255,214,0,.1)' },
    { id:'selenium', name:'Selenium', emoji:'✨', cat:'mi', catLabel:'Mineral', latin:'Sodium Selenite',
      desc:'A trace mineral with powerful antioxidant properties. Works synergistically with Vitamin E to protect cells and support immune function.',
      benefits:['Antioxidant','Immune Support','Cell Protection','Thyroid'],
      dosage:'20mcg per gummy', badgeColor:'#FFD600', badgeBg:'rgba(255,214,0,.12)', emojiBg:'rgba(255,214,0,.1)' },
    { id:'amla', name:'Amla Extract', emoji:'🫐', cat:'ex', catLabel:'Extract', latin:'Emblica officinalis',
      desc:'Indian Gooseberry — one of the richest natural sources of Vitamin C. Provides 20x more Vitamin C than oranges, along with powerful polyphenol antioxidants.',
      benefits:['Vitamin C Rich','Antioxidant','Digestion','Hair & Skin'],
      dosage:'50mg per gummy', badgeColor:'#FF6B35', badgeBg:'rgba(255,107,53,.12)', emojiBg:'rgba(255,107,53,.1)' },
    { id:'beetroot', name:'Beetroot Extract', emoji:'🫡', cat:'ex', catLabel:'Extract', latin:'Beta vulgaris',
      desc:'Our natural coloring agent. Rich in nitrates, betalains, and folate. Provides the vibrant pink-red color naturally while adding nutritional value — no synthetic dyes needed.',
      benefits:['Natural Color','Nitric Oxide','Blood Health','Endurance'],
      dosage:'30mg per gummy', badgeColor:'#FF6B35', badgeBg:'rgba(255,107,53,.12)', emojiBg:'rgba(255,107,53,.1)' },
    { id:'turmeric', name:'Turmeric Extract', emoji:'🌾', cat:'ex', catLabel:'Extract', latin:'Curcuma longa',
      desc:'Contains curcumin — a powerful anti-inflammatory compound used in Ayurveda for millennia. Supports immunity, digestion, and provides natural golden coloring.',
      benefits:['Anti-inflammatory','Antioxidant','Digestion','Natural Color'],
      dosage:'25mg per gummy', badgeColor:'#FF6B35', badgeBg:'rgba(255,107,53,.12)', emojiBg:'rgba(255,107,53,.1)' },
    { id:'spirulina', name:'Spirulina Extract', emoji:'🌊', cat:'ex', catLabel:'Extract', latin:'Arthrospira platensis',
      desc:'A superfood algae used for natural blue-green coloring. One of the most nutrient-dense foods on the planet, packed with protein, vitamins, and minerals.',
      benefits:['Natural Color','Protein Rich','Detox','Energy'],
      dosage:'15mg per gummy', badgeColor:'#FF6B35', badgeBg:'rgba(255,107,53,.12)', emojiBg:'rgba(255,107,53,.1)' },
    { id:'fruitconc', name:'Fruit Concentrates', emoji:'🍓', cat:'ex', catLabel:'Extract', latin:'Mixed Berry, Mango, Orange',
      desc:'Real fruit concentrates provide our gummies with authentic, delicious flavors — not synthetic chemicals. Each flavor comes from actual fruits, ensuring kids love taking their vitamins.',
      benefits:['Natural Flavor','Phytonutrients','No Artificial Flavors','Kid-Approved Taste'],
      dosage:'Natural flavoring', badgeColor:'#FF6B35', badgeBg:'rgba(255,107,53,.12)', emojiBg:'rgba(255,107,53,.1)' },
    { id:'pectin', name:'Pectin', emoji:'🍎', cat:'ba', catLabel:'Base', latin:'Plant-derived polysaccharide',
      desc:'Our plant-based geling agent — derived from citrus fruit peels. Unlike gelatin (made from animal bones/skin), pectin is 100% vegetarian, clean, and suitable for all Indian families.',
      benefits:['100% Vegetarian','Gelatin-Free','Clean Label','Prebiotic Fiber'],
      dosage:'Gummy base', badgeColor:'rgba(255,255,255,.4)', badgeBg:'rgba(255,255,255,.06)', emojiBg:'rgba(255,255,255,.05)' },
    { id:'stevia', name:'Stevia + Monk Fruit', emoji:'🍃', cat:'ba', catLabel:'Base', latin:'Stevia rebaudiana + Siraitia grosvenorii',
      desc:'Our natural zero-calorie sweetener blend. 300x sweeter than sugar but with zero glycemic impact — no sugar crashes, no tooth decay. Kids get the sweetness without any downsides.',
      benefits:['Zero Sugar','Zero Calories','No Glycemic Impact','No Tooth Decay'],
      dosage:'Natural sweetener', badgeColor:'rgba(255,255,255,.4)', badgeBg:'rgba(255,255,255,.06)', emojiBg:'rgba(255,255,255,.05)' },
    { id:'mct', name:'MCT Oil', emoji:'🥥', cat:'ba', catLabel:'Base', latin:'Medium-chain triglycerides',
      desc:'Derived from coconut oil, MCT oil enhances the absorption of fat-soluble vitamins (A, D, E, K) and provides quick, clean energy for growing brains and bodies.',
      benefits:['Fat-Soluble Vitamin Absorption','Brain Energy','Clean Carrier','Coconut-Derived'],
      dosage:'Carrier oil', badgeColor:'rgba(255,255,255,.4)', badgeBg:'rgba(255,255,255,.06)', emojiBg:'rgba(255,255,255,.05)' },
    { id:'tocopherol', name:'Natural Tocopherol', emoji:'🛡️', cat:'ba', catLabel:'Base', latin:'d-alpha-Tocopherol',
      desc:'Our natural preservative — Vitamin E derived from sunflower oil. Keeps every gummy fresh without synthetic preservatives like BHA, BHT, or EDTA.',
      benefits:['Natural Preservative','No Synthetic Chemicals','Antioxidant','Clean Label'],
      dosage:'Clean preservative', badgeColor:'rgba(255,255,255,.4)', badgeBg:'rgba(255,255,255,.06)', emojiBg:'rgba(255,255,255,.05)' },
    { id:'coconutoil', name:'Coconut Oil', emoji:'🥥', cat:'ba', catLabel:'Base', latin:'Cocos nucifera',
      desc:'Used as a natural coating agent to prevent gummies from sticking. Also provides a subtle tropical flavor and additional medium-chain fatty acids for energy.',
      benefits:['Non-Stick Coating','Natural','Antimicrobial','Energy'],
      dosage:'Coating agent', badgeColor:'rgba(255,255,255,.4)', badgeBg:'rgba(255,255,255,.06)', emojiBg:'rgba(255,255,255,.05)' }
  ];

  function readIngredientPayload() {
    const payloadEl = document.getElementById('nbIngredientsData');
    if (!payloadEl) return null;
    try {
      const parsed = JSON.parse(payloadEl.textContent || '[]');
      return Array.isArray(parsed) ? parsed : null;
    } catch (error) {
      console.warn('Unable to parse ingredient payload.', error);
      return null;
    }
  }

  function normalizeIngredient(item, index) {
    const safeBenefits = Array.isArray(item && item.benefits) ? item.benefits.filter(Boolean) : [];
    return {
      id:        item && item.id        ? item.id        : `ingredient-${index}`,
      name:      item && item.name      ? item.name      : (item && item.shortName ? item.shortName : 'Ingredient'),
      shortName: item && item.shortName ? item.shortName : (item && item.name      ? item.name      : 'Ingredient'),
      emoji:     item && item.emoji     ? item.emoji     : '',
      cat:       item && item.cat       ? item.cat       : 'ot',
      catLabel:  item && item.catLabel  ? item.catLabel  : 'Other',
      latin:     item && item.latin     ? item.latin     : (item && item.shortName ? item.shortName : ''),
      desc:      item && item.desc      ? item.desc      : '',
      benefits:  safeBenefits,
      dosage:    item && item.dosage    ? item.dosage    : 'Details available in ingredient panel',
      badgeColor:item && item.badgeColor? item.badgeColor: '#7C3AED',
      badgeBg:   item && item.badgeBg   ? item.badgeBg   : 'rgba(124,58,237,.12)',
      emojiBg:   item && item.emojiBg   ? item.emojiBg   : 'rgba(124,58,237,.1)',
      image:     item && item.image     ? item.image     : ''
    };
  }

  const liveIngredients = readIngredientPayload();
  const ingredients = (liveIngredients && liveIngredients.length ? liveIngredients : fallbackIngredients).map(normalizeIngredient);

  const listEl     = document.getElementById('nbList');
  const detailEl   = document.getElementById('nbDetailCards');
  const emptyEl    = document.getElementById('nbDetailEmpty');
  const mobCardsEl = document.getElementById('nbMobCards');

  if (!listEl || !detailEl) return;

  let activeId      = null;
  let currentFilter = 'all';

  function renderList(filter) {
    currentFilter = filter;
    const filtered = filter === 'all' ? ingredients : ingredients.filter(i => i.cat === filter);
    listEl.innerHTML = filtered.map(i => `
      <div class="nb-ing-row${activeId === i.id ? ' nb-row-active' : ''}" data-id="${i.id}">
        <div class="nb-ing-row-emoji" style="background:${i.emojiBg}">${i.emoji}</div>
        <div>
          <div class="nb-ing-row-name">${i.name}</div>
          <div class="nb-ing-row-cat">${i.catLabel}</div>
        </div>
        <div class="nb-ing-row-badge" style="background:${i.badgeBg};color:${i.badgeColor}">${i.catLabel}</div>
      </div>
    `).join('');
    listEl.querySelectorAll('.nb-ing-row').forEach(row => {
      row.addEventListener('click', () => showDetail(row.dataset.id));
    });
  }

  function renderDetailCards() {
    detailEl.innerHTML = ingredients.map(i => `
      <div class="nb-detail-card" data-detail="${i.id}">
        <div class="nb-detail-header" style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
          <div style="width:72px;height:72px;border-radius:12px;overflow:hidden;border:1px solid rgba(255,255,255,.14);background:rgba(255,255,255,.04);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <img src="${i.image || 'img/logo.png'}" alt="${i.name}" style="width:100%;height:100%;object-fit:cover;">
          </div>
          <div style="flex:1;min-width:220px;">
            <div class="nb-detail-name">${i.name}</div>
            <div class="nb-detail-latin">${i.latin || ''}</div>
            <div style="margin-top:8px;display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
              <div class="nb-detail-cat-badge" style="background:${i.badgeBg};color:${i.badgeColor}">${i.catLabel}</div>
            </div>
          </div>
        </div>
        <div class="nb-detail-desc">${i.desc}</div>
        <div class="nb-detail-benefits">
          <h5>Key Benefits</h5>
          <div class="nb-benefit-tags">
            ${i.benefits.map(b => `<span class="nb-benefit-tag">${b}</span>`).join('')}
          </div>
        </div>
        <div class="nb-detail-dosage">
          <div class="nb-dosage-icon">💊</div>
          <div>
            <div class="nb-dosage-label">Dosage per Gummy</div>
            <div class="nb-dosage-val">${i.dosage}</div>
          </div>
        </div>
      </div>
    `).join('');
  }

  function showDetail(id) {
    activeId = id;
    listEl.querySelectorAll('.nb-ing-row').forEach(r => r.classList.toggle('nb-row-active', r.dataset.id === id));
    if (emptyEl) emptyEl.classList.add('nb-hidden');
    detailEl.querySelectorAll('.nb-detail-card').forEach(c => c.classList.toggle('nb-card-active', c.dataset.detail === id));
  }

  function renderMobCards(filter) {
    if (!mobCardsEl) return;
    const filtered = filter === 'all' ? ingredients : ingredients.filter(i => i.cat === filter);
    mobCardsEl.innerHTML = filtered.map(i => `
      <div class="nb-mob-card" data-mobid="${i.id}">
        <div class="nb-mob-card-head">
          <div class="nb-mob-card-emoji">${i.emoji}</div>
          <div class="nb-mob-card-name">${i.name}</div>
          <div class="nb-mob-card-arrow">▼</div>
        </div>
        <div class="nb-mob-card-body">
          <div class="nb-mob-card-desc">${i.desc}</div>
          <div class="nb-mob-card-benefits">
            ${i.benefits.map(b => `<span class="nb-mob-benefit">${b}</span>`).join('')}
          </div>
        </div>
      </div>
    `).join('');
    mobCardsEl.querySelectorAll('.nb-mob-card-head').forEach(head => {
      head.addEventListener('click', () => {
        const card    = head.closest('.nb-mob-card');
        const wasOpen = card.classList.contains('nb-mob-open');
        mobCardsEl.querySelectorAll('.nb-mob-card').forEach(c => c.classList.remove('nb-mob-open'));
        if (!wasOpen) card.classList.add('nb-mob-open');
      });
    });
  }

  window.nbFilter = function (cat, btn) {
    document.querySelectorAll('.nb-cat-pill').forEach(p => p.classList.remove('nb-active'));
    btn.classList.add('nb-active');
    renderList(cat);
    activeId = null;
    if (emptyEl) emptyEl.classList.remove('nb-hidden');
    detailEl.querySelectorAll('.nb-detail-card').forEach(c => c.classList.remove('nb-card-active'));
  };

  window.nbMobFilter = function (cat, btn) {
    document.querySelectorAll('.nb-mob-tab').forEach(t => t.classList.remove('nb-sel-mob'));
    btn.classList.add('nb-sel-mob');
    renderMobCards(cat);
  };

  renderList('all');
  renderDetailCards();
  renderMobCards('all');
})();


/* ══════════════════════════════════════════
   ABOUT US — SCROLL REVEAL + COUNTERS
══════════════════════════════════════════ */
const nbAboutRevObs = new IntersectionObserver(entries => {
  entries.forEach(e => {
    if (e.isIntersecting) {
      e.target.classList.add('nb-about-visible');
      nbAboutRevObs.unobserve(e.target);
    }
  });
}, { threshold: 0.1 });
document.querySelectorAll('.nb-about-reveal').forEach(r => nbAboutRevObs.observe(r));

const nbAboutCountObs = new IntersectionObserver(entries => {
  entries.forEach(e => {
    if (!e.isIntersecting) return;
    const el      = e.target;
    const raw     = el.textContent;
    const hasK    = raw.includes('K');
    const hasStar = raw.includes('★');
    const hasPct  = raw.includes('%');
    const num     = parseFloat(raw.replace(/[^0-9.]/g, ''));
    let start = 0;
    const dur = 1600, steps = 60, inc = num / steps;
    const iv = setInterval(() => {
      start = Math.min(start + inc, num);
      let display = Number.isInteger(num) ? Math.round(start) : start.toFixed(1);
      if (hasK)         display += 'K+';
      else if (hasStar) display += '★';
      else if (hasPct)  display += '%';
      else              display += '+';
      el.textContent = display;
      if (start >= num) clearInterval(iv);
    }, dur / steps);
    nbAboutCountObs.unobserve(el);
  });
}, { threshold: 0.5 });
document.querySelectorAll('.nb-about-hstat-num').forEach(el => nbAboutCountObs.observe(el));
