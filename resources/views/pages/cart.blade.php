@extends('layouts.main')
@section('title', 'Cart - NutriBuddy Kids')

@push('styles')
    <style>
        .cart-page {
            padding: 40px 5% 80px;
            max-width: 1100px;
            margin: 100px auto 0;
        }

        .cart-page-head {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .cart-page-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 360px;
            gap: 22px;
            margin-top: 22px;
            align-items: start;
        }

        .cart-panel {
            background: var(--wh);
            border: 2px solid var(--border);
            border-radius: 20px;
            padding: 18px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .04);
        }

        .cart-summary-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid var(--border);
        }

        .cart-page-items-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .cart-page-item {
            display: grid;
            grid-template-columns: 88px minmax(0, 1fr) auto;
            gap: 14px;
            align-items: center;
            padding: 16px;
            border: 1px solid var(--border);
            border-radius: 18px;
            background: #fff;
            transition: opacity .2s;
        }

        .cart-page-item.is-updating {
            opacity: .7;
            pointer-events: none;
        }

        .cart-page-item-image {
            width: 88px;
            height: 88px;
            border-radius: 16px;
            overflow: hidden;
            background: #f7f7f7;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cart-page-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .cart-page-item-content {
            min-width: 0;
        }

        .cart-page-item-content h5 {
            margin: 0 0 4px;
            font-family: 'Nunito', sans-serif;
            font-size: 1rem;
            font-weight: 900;
            color: var(--dk);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .cart-page-item-price {
            margin: 0 0 10px;
            color: var(--pk);
            font-family: 'Fredoka One', cursive;
            font-size: 1rem;
        }

        /* ── Quantity Row ── */
        .cart-page-qty-row {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f8f8f8;
            border: 1.5px solid var(--border);
            border-radius: 12px;
            padding: 4px 6px;
        }

        .cart-page-qty-row.is-updating {
            opacity: .6;
            pointer-events: none;
        }

        .cart-page-qty-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: none;
            background: #fff;
            color: var(--dk);
            font-size: 1.1rem;
            font-weight: 900;
            line-height: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background .15s, color .15s, box-shadow .15s;
            touch-action: manipulation;
            box-shadow: 0 1px 4px rgba(0,0,0,.07);
            flex-shrink: 0;
        }

        .cart-page-qty-btn:hover:not(:disabled) {
            background: var(--pk, #e14b74);
            color: #fff;
            box-shadow: 0 2px 8px rgba(225,75,116,.25);
        }

        .cart-page-qty-btn:active:not(:disabled) {
            transform: scale(.93);
        }

        .cart-page-qty-btn:disabled {
            opacity: .45;
            cursor: wait;
        }

        .cart-page-qty-val {
            width: 42px;
            height: 32px;
            border: none;
            background: transparent;
            text-align: center;
            font-size: .95rem;
            font-weight: 800;
            font-family: 'Nunito', sans-serif;
            color: var(--dk);
            outline: none;
            -moz-appearance: textfield;
        }

        .cart-page-qty-val::-webkit-inner-spin-button,
        .cart-page-qty-val::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* ── Remove button ── */
        .cart-page-remove-btn {
            width: 38px;
            height: 38px;
            border-radius: 999px;
            border: 1.5px solid #ffd2de;
            background: #fff4f7;
            color: #e14b74;
            font-size: 1.2rem;
            font-weight: 900;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background .2s, border-color .2s, transform .15s;
            align-self: start;
            flex-shrink: 0;
        }

        .cart-page-remove-btn:hover {
            background: #ffe3ea;
            border-color: #ffb4c9;
            transform: scale(1.08);
        }

        /* ── Responsive ── */
        @media (max-width: 900px) {
            .cart-page-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .cart-page {
                padding: 24px 4% 60px;
                margin-top: 80px;
            }

            .cart-page-head .nav-cta {
                width: 100%;
                text-align: center;
            }

            .cart-panel {
                padding: 14px;
                border-radius: 18px;
            }

            .cart-page-item {
                grid-template-columns: 72px minmax(0, 1fr);
                gap: 12px;
                padding: 14px;
                position: relative;
            }

            .cart-page-item-image {
                width: 72px;
                height: 72px;
                border-radius: 14px;
            }

            .cart-page-remove-btn {
                position: absolute;
                top: 10px;
                right: 10px;
                width: 32px;
                height: 32px;
            }

            .cart-page-item-content {
                padding-right: 44px;
            }

            .cart-page-qty-row {
                width: 100%;
                justify-content: space-between;
            }

            .cart-page-qty-val {
                flex: 1;
                min-width: 0;
            }
        }
    </style>
@endpush

@section('content')
    <section class="cart-page">
        <div class="cart-page-head">
            <div>
                <h1 style="font-family:'Fredoka One',cursive;color:var(--dk);margin:0 0 6px;">Your Cart</h1>
            </div>
        </div>

        <div class="cart-page-grid">
            <!-- Cart Items -->
            <div class="cart-inner cart-panel">
                <h4 class="title-text" style="margin:0 0 14px;">
                    Cart Items <span id="cartPageCount">0</span> 
                </h4>
                <div id="cartPageItems" class="cart-page-items-list"></div>
                <div id="cartPageEmpty"
                    style="display:none;padding:18px;border:2px dashed var(--border);border-radius:16px;color:var(--text-light);text-align:center;">
                    Your cart is empty.
                    <a href="{{ route('product') }}" style="color:var(--pk);font-weight:800;text-decoration:none;">Shop now</a>
                </div>
            </div>

            <!-- Summary -->
            <div class="cart-panel">
                <h3 style="margin:0 0 10px;font-family:'Nunito',sans-serif;font-weight:900;color:var(--dk);">Summary</h3>
                <div class="text-box cart-summary-box">
                    <h5 style="margin:0;" id="cartSummaryLabel">Total Amount</h5>
                    <span id="cartPageSubtotal">Rs. 0</span>
                </div>
                <div style="margin-top:14px;display:flex;gap:10px;flex-direction:column;">
                    <a href="{{ route('checkout') }}" class="nav-cta"
                        style="text-align:center;text-decoration:none;">Checkout</a>
                    <a href="{{ route('product') }}" class="nav-cta"
                        style="border:2px solid var(--pkl);text-align:center;text-decoration:none;">Continue Shopping</a>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
        (function () {
            const cartUrl        = @json(route('user.cart.index'));
            const deleteTemplate = @json(route('user.cart.items.destroy', ['itemId' => '__ITEM__']));
            const updateTemplate = @json(route('user.cart.items.update',  ['itemId' => '__ITEM__']));
            const csrf           = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            /* ── helpers ── */
            function money(v) {
                return `Rs. ${Number(v || 0).toLocaleString('en-IN', { maximumFractionDigits: 0 })}`;
            }

            function clampQty(v) {
                return Math.max(1, Math.min(10, parseInt(v, 10) || 1));
            }

            /* ── pending (guest) cart helpers ── */
            function getPendingItems() {
                try {
                    const raw = localStorage.getItem('nb_pending_cart');
                    const parsed = raw ? JSON.parse(raw) : [];
                    return Array.isArray(parsed) ? parsed : [];
                } catch (_) { return []; }
            }

            function savePendingItems(items) {
                localStorage.setItem('nb_pending_cart', JSON.stringify(items || []));
            }

            function pendingKey(productId, variantId = null) {
                return `${Number(productId || 0)}::${Number(variantId || 0)}`;
            }

            function removePendingItem(productId, variantId = null) {
                const key = pendingKey(productId, variantId);
                savePendingItems(getPendingItems().filter(it => pendingKey(it.product_id, it.product_variant_id) !== key));
            }

            function updatePendingQty(productId, variantId = null, qty = 1) {
                const key = pendingKey(productId, variantId);
                savePendingItems(getPendingItems().map(it =>
                    pendingKey(it.product_id, it.product_variant_id) === key
                        ? { ...it, quantity: clampQty(qty) }
                        : it
                ));
            }

            /* ── summary ── */
            function setCartSummary(count, total) {
                document.getElementById('cartPageCount').textContent   = count;
                document.getElementById('cartPageSubtotal').textContent = money(total);
            }

            /* ── build a cart row DOM element ── */
            function createCartRow(image, name, price, qty) {
                const row = document.createElement('div');
                row.className = 'cart-page-item';
                row.innerHTML = `
                    <div class="cart-page-item-image">
                        <img src="${image}" alt="${name}" loading="lazy">
                    </div>
                    <div class="cart-page-item-content">
                        <h5>${name}</h5>
                        <p class="cart-page-item-price">${money(price)}</p>
                        <div class="cart-page-qty-row">
                            <button type="button" class="cart-page-qty-btn" data-qty-delta="-1" aria-label="Decrease quantity">-</button>
                            <input type="number" min="1" max="10" class="cart-page-qty-val" value="${clampQty(qty)}" aria-label="Quantity">
                            <button type="button" class="cart-page-qty-btn" data-qty-delta="1" aria-label="Increase quantity">+</button>
                        </div>
                    </div>
                    <button type="button" class="cart-page-remove-btn" aria-label="Remove item">&times;</button>
                `;
                return row;
            }

            /* ── bind +/- controls on a row ── */
            function bindQtyControls(row, initialQty, onCommit) {
                const qtyRow = row.querySelector('.cart-page-qty-row');
                const input  = qtyRow.querySelector('.cart-page-qty-val');
                let   currentQty = clampQty(initialQty);

                async function submit(nextVal) {
                    const next = clampQty(nextVal);
                    if (next === currentQty) { input.value = currentQty; return; }

                    /* disable while saving */
                    qtyRow.classList.add('is-updating');
                    qtyRow.querySelectorAll('.cart-page-qty-btn, .cart-page-qty-val').forEach(el => el.disabled = true);

                    try {
                        await onCommit(next);
                        currentQty  = next;
                        input.value = next;
                    } catch (err) {
                        input.value = currentQty;
                        alert(err.message || 'Unable to update quantity.');
                    } finally {
                        qtyRow.classList.remove('is-updating');
                        qtyRow.querySelectorAll('.cart-page-qty-btn, .cart-page-qty-val').forEach(el => el.disabled = false);
                    }
                }

                qtyRow.querySelectorAll('.cart-page-qty-btn').forEach(btn => {
                    btn.addEventListener('click', e => {
                        e.preventDefault();
                        submit(clampQty(input.value) + Number(btn.dataset.qtyDelta || 0));
                    });
                });

                input.addEventListener('change',  () => submit(input.value));
                input.addEventListener('blur',    () => submit(input.value));
                input.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); submit(input.value); } });
            }

            /* ── render guest/pending cart ── */
            function renderPendingCart() {
                const items     = getPendingItems();
                const subtotal  = items.reduce((s, it) => s + Number(it.unit_price || 0) * Number(it.quantity || 0), 0);
                const totalQty  = items.reduce((s, it) => s + Number(it.quantity || 0), 0);

                setCartSummary(totalQty, subtotal);

                const list  = document.getElementById('cartPageItems');
                const empty = document.getElementById('cartPageEmpty');
                list.innerHTML = '';

                if (!items.length) { empty.style.display = 'block'; return; }
                empty.style.display = 'none';

                items.forEach(it => {
                    const qty = Number(it.quantity || 1);
                    const row = createCartRow(
                        it.image || '/img/product2.png',
                        it.product_name || 'Product',
                        it.unit_price,
                        qty
                    );

                    bindQtyControls(row, qty, async value => {
                        updatePendingQty(it.product_id, it.product_variant_id, value);
                        renderPendingCart();
                    });

                    row.querySelector('.cart-page-remove-btn').addEventListener('click', () => {
                        removePendingItem(it.product_id, it.product_variant_id);
                        renderPendingCart();
                    });

                    list.appendChild(row);
                });
            }

            /* ── API calls ── */
            async function apiUpdateQty(itemId, qty) {
                const res = await fetch(updateTemplate.replace('__ITEM__', itemId), {
                    method: 'PATCH',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        ...(csrf ? { 'X-CSRF-TOKEN': csrf } : {})
                    },
                    body: JSON.stringify({ quantity: clampQty(qty) })
                });
                if (!res.ok) {
                    const p = await res.json().catch(() => ({}));
                    throw new Error(p.message || 'Unable to update quantity.');
                }
                return res.json().catch(() => ({}));
            }

            async function apiRemoveItem(itemId) {
                const res = await fetch(deleteTemplate.replace('__ITEM__', itemId), {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        ...(csrf ? { 'X-CSRF-TOKEN': csrf } : {})
                    }
                });
                if (!res.ok) {
                    const p = await res.json().catch(() => ({}));
                    throw new Error(p.message || 'Unable to remove item.');
                }
                return res.json().catch(() => ({}));
            }

            /* ── render authenticated cart ── */
            async function loadCart() {
                const res = await fetch(cartUrl, { headers: { 'Accept': 'application/json' } });

                if (res.status === 401 || res.status === 419) { renderPendingCart(); return; }
                if (!res.ok) return;

                const payload  = await res.json().catch(() => ({}));
                const items    = payload.cart?.items || [];
                const total = payload.pricing?.grand_total ?? payload.pricing?.subtotal ?? 0;
                const totalQty = items.reduce((s, it) => s + Number(it.quantity || 0), 0);

                setCartSummary(totalQty, total);

                const list  = document.getElementById('cartPageItems');
                const empty = document.getElementById('cartPageEmpty');
                list.innerHTML = '';

                if (!items.length) { empty.style.display = 'block'; return; }
                empty.style.display = 'none';

                items.forEach(it => {
                    const qty   = Number(it.quantity || 1);
                    const price = it.product_variant ? it.product_variant.price : it.product?.base_price;
                    const image = it.product?.primary_image?.image_path
                        ? '/storage/' + it.product.primary_image.image_path
                        : '/img/product2.png';

                    const row = createCartRow(image, it.product?.name || 'Product', price, qty);

                    bindQtyControls(row, qty, async value => {
                        row.classList.add('is-updating');
                        try {
                            await apiUpdateQty(it.id, value);
                            await loadCart();
                        } finally {
                            row.classList.remove('is-updating');
                        }
                    });

                    row.querySelector('.cart-page-remove-btn').addEventListener('click', async () => {
                        row.classList.add('is-updating');
                        try {
                            await apiRemoveItem(it.id);
                            await loadCart();
                        } catch (err) {
                            alert(err.message || 'Unable to remove item.');
                            row.classList.remove('is-updating');
                        }
                    });

                    list.appendChild(row);
                });
            }

            document.addEventListener('DOMContentLoaded', loadCart);
        })();
        </script>
    @endpush
@endsection
