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

        .cart-items-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .single-cart-box {
            display: grid;
            grid-template-columns: 88px minmax(0, 1fr) auto;
            gap: 14px;
            align-items: center;
            padding: 16px;
            border: 1px solid var(--border);
            border-radius: 18px;
            background: #fff;
        }

        .single-cart-box.is-updating {
            opacity: .7;
            pointer-events: none;
        }

        .single-cart-box .image-box {
            width: 88px;
            height: 88px;
            border-radius: 16px;
            overflow: hidden;
            background: #f7f7f7;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .image-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .cart-item-content {
            min-width: 0;
        }

        .cart-item-content h5 {
            margin: 0 0 6px;
            font-family: 'Nunito', sans-serif;
            font-size: 1rem;
            font-weight: 900;
            color: var(--dk);
        }

        .cart-item-price {
            margin: 0;
            color: var(--pk);
            font-family: 'Fredoka One', cursive;
            font-size: 1rem;
        }

        .cart-item-actions {
            margin-top: 12px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .cart-qty-btn {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            border: 1.5px solid var(--border);
            background: #fff;
            color: var(--dk);
            font-size: 1rem;
            font-weight: 900;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all .2s;
            touch-action: manipulation;
        }

        .cart-qty-btn:hover {
            border-color: var(--pk);
            background: var(--pkl);
            color: var(--pk);
        }

        .cart-qty-btn:disabled {
            opacity: .55;
            cursor: wait;
        }

        .cart-qty-input {
            width: 58px;
            height: 36px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            background: #fff;
            text-align: center;
            font-size: .95rem;
            font-weight: 800;
            color: var(--dk);
            outline: none;
        }

        .cart-remove-btn {
            width: 38px;
            height: 38px;
            border-radius: 999px;
            border: 1.5px solid #ffd2de;
            background: #fff4f7;
            color: #e14b74;
            font-size: 1.1rem;
            font-weight: 900;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all .2s;
            align-self: start;
        }

        .cart-remove-btn:hover {
            background: #ffe3ea;
            border-color: #ffb4c9;
        }

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

            .single-cart-box {
                grid-template-columns: 72px minmax(0, 1fr);
                gap: 12px;
                padding: 14px;
                position: relative;
            }

            .single-cart-box .image-box {
                width: 72px;
                height: 72px;
                border-radius: 14px;
            }

            .cart-remove-btn {
                position: absolute;
                top: 10px;
                right: 10px;
                width: 34px;
                height: 34px;
            }

            .cart-item-content {
                padding-right: 42px;
            }

            .cart-item-actions {
                width: 100%;
                gap: 6px;
            }

            .cart-qty-btn {
                width: 34px;
                height: 34px;
            }

            .cart-qty-input {
                flex: 1;
                min-width: 0;
                width: auto;
            }
        }
    </style>
@endpush

@section('content')
    <section class="cart-page">
        <div class="cart-page-head">
            <div>
                <h1 style="font-family:'Fredoka One',cursive;color:var(--dk);margin:0 0 6px;">Your Cart</h1>
                <p style="color:var(--text-light);margin:0;">Review items and proceed to checkout (COD only).</p>
            </div>
            <a href="{{ route('checkout') }}" class="nav-cta" style="text-decoration:none;">Checkout -></a>
        </div>

        <div class="cart-page-grid">
            <div class="cart-inner cart-panel">
                <h4 class="title-text" style="margin:0 0 14px;"><span id="cartPageCount">0</span> Cart Items</h4>
                <div id="cartPageItems" class="cart-items-list"></div>
                <div id="cartPageEmpty"
                    style="display:none;padding:18px;border:2px dashed var(--border);border-radius:16px;color:var(--text-light);text-align:center;">
                    Your cart is empty. <a href="{{ route('product') }}"
                        style="color:var(--pk);font-weight:800;text-decoration:none;">Shop now</a>
                </div>
            </div>

            <div class="cart-panel">
                <h3 style="margin:0 0 10px;font-family:'Nunito',sans-serif;font-weight:900;color:var(--dk);">Summary</h3>
                <div class="text-box cart-summary-box">
                    <h5 style="margin:0;">Subtotal</h5>
                    <span id="cartPageSubtotal">Rs. 0</span>
                </div>
                <div style="margin-top:14px;display:flex;gap:10px;flex-direction:column;">
                    <a href="{{ route('checkout') }}" class="nav-cta"
                        style="text-align:center;text-decoration:none;">Checkout -></a>
                    <a href="{{ route('product') }}" class="nav-cta"
                        style="border:2px solid var(--pkl);text-align:center;text-decoration:none;">Continue
                        Shopping</a>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            (function() {
                const cartUrl = @json(route('user.cart.index'));
                const deleteTemplate = @json(route('user.cart.items.destroy', ['itemId' => '__ITEM__']));
                const updateTemplate = @json(route('user.cart.items.update', ['itemId' => '__ITEM__']));
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

                function money(v) {
                    return `Rs. ${Number(v || 0).toLocaleString('en-IN', { maximumFractionDigits: 0 })}`;
                }

                function normalizeQuantity(quantity) {
                    return Math.max(1, Math.min(999, Number(quantity || 1)));
                }

                function getPendingCartItems() {
                    try {
                        const raw = localStorage.getItem('nb_pending_cart');
                        const parsed = raw ? JSON.parse(raw) : [];
                        return Array.isArray(parsed) ? parsed : [];
                    } catch (_) {
                        return [];
                    }
                }

                function savePendingCartItems(items) {
                    localStorage.setItem('nb_pending_cart', JSON.stringify(items || []));
                }

                function removePendingCartItem(productId, productVariantId = null) {
                    const targetKey = `${Number(productId || 0)}::${Number(productVariantId || 0)}`;
                    const nextItems = getPendingCartItems().filter(it =>
                        `${Number(it.product_id || 0)}::${Number(it.product_variant_id || 0)}` !== targetKey
                    );
                    savePendingCartItems(nextItems);
                }

                function updatePendingCartItemQuantity(productId, productVariantId = null, quantity = 1) {
                    const targetKey = `${Number(productId || 0)}::${Number(productVariantId || 0)}`;
                    const nextItems = getPendingCartItems().map(it => {
                        const itemKey = `${Number(it.product_id || 0)}::${Number(it.product_variant_id || 0)}`;
                        if (itemKey !== targetKey) return it;

                        return {
                            ...it,
                            quantity: normalizeQuantity(quantity)
                        };
                    });
                    savePendingCartItems(nextItems);
                }

                function createQuantityField(quantity, onCommit) {
                    const wrap = document.createElement('div');
                    wrap.className = 'cart-item-actions';

                    const minus = document.createElement('button');
                    minus.type = 'button';
                    minus.className = 'cart-qty-btn';
                    minus.textContent = '-';

                    const input = document.createElement('input');
                    input.type = 'number';
                    input.min = '1';
                    input.className = 'cart-qty-input';
                    input.value = String(normalizeQuantity(quantity));

                    const plus = document.createElement('button');
                    plus.type = 'button';
                    plus.className = 'cart-qty-btn';
                    plus.textContent = '+';

                    minus.addEventListener('click', () => onCommit(normalizeQuantity(input.value) - 1));
                    plus.addEventListener('click', () => onCommit(normalizeQuantity(input.value) + 1));
                    input.addEventListener('change', () => onCommit(normalizeQuantity(input.value)));
                    input.addEventListener('blur', () => onCommit(normalizeQuantity(input.value)));
                    input.addEventListener('keydown', e => {
                        if (e.key === 'Enter') {
                            onCommit(normalizeQuantity(input.value));
                        }
                    });

                    wrap.appendChild(minus);
                    wrap.appendChild(input);
                    wrap.appendChild(plus);

                    return wrap;
                }

                function setCartSummary(count, subtotal) {
                    document.getElementById('cartPageCount').textContent = count;
                    document.getElementById('cartPageSubtotal').textContent = money(subtotal);
                }

                function createCartRow(image, name, price) {
                    const row = document.createElement('div');
                    row.className = 'single-cart-box';
                    row.innerHTML = `
                        <div class="image-box"><img src="${image}" alt=""></div>
                        <div class="cart-item-content">
                            <h5>${name}</h5>
                            <h4 class="cart-item-price">${money(price)}</h4>
                        </div>
                        <button type="button" class="cart-remove-btn" aria-label="Remove item">×</button>
                    `;

                    return row;
                }

                function renderPendingCart() {
                    const items = getPendingCartItems();
                    const subtotal = items.reduce((sum, it) => sum + (Number(it.unit_price || 0) * Number(it.quantity || 0)), 0);
                    const totalQuantity = items.reduce((sum, it) => sum + Number(it.quantity || 0), 0);

                    setCartSummary(totalQuantity, subtotal);

                    const list = document.getElementById('cartPageItems');
                    const empty = document.getElementById('cartPageEmpty');
                    list.innerHTML = '';

                    if (!items.length) {
                        empty.style.display = 'block';
                        return;
                    }

                    empty.style.display = 'none';

                    items.forEach(it => {
                        const qty = Number(it.quantity || 1);
                        const row = createCartRow(it.image || '/img/product2.png', it.product_name || 'Product', it.unit_price);
                        const content = row.querySelector('.cart-item-content');

                        content.appendChild(createQuantityField(qty, value => {
                            updatePendingCartItemQuantity(it.product_id, it.product_variant_id, value);
                            renderPendingCart();
                        }));

                        row.querySelector('.cart-remove-btn').addEventListener('click', () => {
                            removePendingCartItem(it.product_id, it.product_variant_id);
                            renderPendingCart();
                        });

                        list.appendChild(row);
                    });
                }

                async function updateServerCartQuantity(itemId, quantity) {
                    const res = await fetch(updateTemplate.replace('__ITEM__', itemId), {
                        method: 'PATCH',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            ...(csrf ? {
                                'X-CSRF-TOKEN': csrf
                            } : {})
                        },
                        body: JSON.stringify({
                            quantity: normalizeQuantity(quantity)
                        })
                    });

                    if (!res.ok) {
                        const payload = await res.json().catch(() => ({}));
                        throw new Error(payload.message || 'Unable to update cart quantity.');
                    }

                    return res.json().catch(() => ({}));
                }

                async function removeServerCartItem(itemId) {
                    const res = await fetch(deleteTemplate.replace('__ITEM__', itemId), {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            ...(csrf ? {
                                'X-CSRF-TOKEN': csrf
                            } : {})
                        }
                    });

                    if (!res.ok) {
                        const payload = await res.json().catch(() => ({}));
                        throw new Error(payload.message || 'Unable to remove cart item.');
                    }

                    return res.json().catch(() => ({}));
                }

                async function loadCart() {
                    const res = await fetch(cartUrl, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    if (res.status === 401 || res.status === 419) {
                        renderPendingCart();
                        return;
                    }
                    if (!res.ok) return;

                    const payload = await res.json().catch(() => ({}));
                    const items = payload.cart?.items || [];
                    const subtotal = payload.pricing?.subtotal || 0;
                    const totalQuantity = items.reduce((sum, it) => sum + Number(it.quantity || 0), 0);

                    setCartSummary(totalQuantity, subtotal);

                    const list = document.getElementById('cartPageItems');
                    const empty = document.getElementById('cartPageEmpty');
                    list.innerHTML = '';

                    if (!items.length) {
                        empty.style.display = 'block';
                        return;
                    }

                    empty.style.display = 'none';

                    items.forEach(it => {
                        const qty = Number(it.quantity || 1);
                        const price = it.product_variant ? it.product_variant.price : it.product?.base_price;
                        const image = it.product?.primary_image?.image_path ? ('/storage/' + it.product.primary_image.image_path) :
                            '/img/product2.png';
                        const row = createCartRow(image, it.product?.name || 'Product', price);
                        const content = row.querySelector('.cart-item-content');

                        content.appendChild(createQuantityField(qty, async value => {
                            row.classList.add('is-updating');
                            try {
                                await updateServerCartQuantity(it.id, value);
                                await loadCart();
                            } catch (error) {
                                alert(error.message || 'Unable to update cart quantity.');
                                row.classList.remove('is-updating');
                            }
                        }));

                        row.querySelector('.cart-remove-btn').addEventListener('click', async () => {
                            row.classList.add('is-updating');
                            try {
                                await removeServerCartItem(it.id);
                                await loadCart();
                            } catch (error) {
                                alert(error.message || 'Unable to remove cart item.');
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
