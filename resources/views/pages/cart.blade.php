@extends('layouts.main')
@section('title', 'Cart — NutriBuddy Kids')

@section('content')
    <section style="padding:40px 5% 80px;max-width:1100px;margin:0 auto; margin-top: 100px;">
        <div style="display:flex;align-items:flex-end;justify-content:space-between;gap:16px;flex-wrap:wrap;">
            <div>
                <h1 style="font-family:'Fredoka One',cursive;color:var(--dk);margin:0 0 6px;">Your Cart</h1>
                <p style="color:var(--text-light);margin:0;">Review items and proceed to checkout (COD only).</p>
            </div>
            <a href="{{ route('checkout') }}" class="nav-cta" style="text-decoration:none;">Checkout →</a>
        </div>

        <div style="display:grid;grid-template-columns:1fr 360px;gap:22px;margin-top:22px;align-items:start;">
            <div class="cart-inner"
                style="background:var(--wh);border:2px solid var(--border);border-radius:20px;padding:18px;">
                <h4 class="title-text" style="margin:0 0 14px;"><span id="cartPageCount">0</span> Cart Items</h4>
                <div id="cartPageItems"></div>
                <div id="cartPageEmpty"
                    style="display:none;padding:18px;border:2px dashed var(--border);border-radius:16px;color:var(--text-light);text-align:center;">
                    Your cart is empty. <a href="{{ route('product') }}"
                        style="color:var(--pk);font-weight:800;text-decoration:none;">Shop now</a>
                </div>
            </div>

            <div
                style="background:var(--wh);border:2px solid var(--border);border-radius:20px;padding:18px;box-shadow:0 2px 12px rgba(0,0,0,.04);">
                <h3 style="margin:0 0 10px;font-family:'Nunito',sans-serif;font-weight:900;color:var(--dk);">Summary</h3>
                <div class="text-box"
                    style="display:flex;justify-content:space-between;align-items:center;padding:12px 0;border-bottom:1px solid var(--border);">
                    <h5 style="margin:0;">Subtotal</h5>
                    <span id="cartPageSubtotal">₹0</span>
                </div>
                <div style="margin-top:14px;display:flex;gap:10px;flex-direction:column;">
                    <a href="{{ route('checkout') }}" class="nav-cta"
                        style="text-align:center;text-decoration:none;">Checkout →</a>
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
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

                function money(v) {
                    return `₹${Number(v || 0).toLocaleString('en-IN', { maximumFractionDigits: 0 })}`;
                }

                async function loadCart() {
                    const res = await fetch(cartUrl, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    if (res.status === 401 || res.status === 419) {
                        window.location.href = '/login';
                        return;
                    }
                    if (!res.ok) return;

                    const payload = await res.json();
                    const items = payload.cart?.items || [];
                    const subtotal = payload.pricing?.subtotal || 0;

                    document.getElementById('cartPageCount').textContent = items.length;
                    document.getElementById('cartPageSubtotal').textContent = money(subtotal);

                    const list = document.getElementById('cartPageItems');
                    const empty = document.getElementById('cartPageEmpty');
                    list.innerHTML = '';

                    if (!items.length) {
                        empty.style.display = 'block';
                        return;
                    }
                    empty.style.display = 'none';

                    items.forEach(it => {
                        const name = it.product?.name || 'Product';
                        const qty = it.quantity || 1;
                        const price = it.product_variant ? it.product_variant.price : it.product?.base_price;
                        const row = document.createElement('div');
                        row.className = 'single-cart-box';
                        row.innerHTML = `
          <div class="image-box"><img src="${it.product?.primary_image?.image_path ? ('/storage/' + it.product.primary_image.image_path) : '/img/product2.png'}" alt=""></div>
          <div>
            <h5>${name}</h5>
            <h4>${money(price)} <span style="font-size:.75rem;color:#aaa;font-family:'DM Sans',sans-serif">× ${qty}</span></h4>
          </div>
          <button aria-label="Remove">✕</button>
        `;
                        row.querySelector('button').addEventListener('click', async () => {
                            await fetch(deleteTemplate.replace('__ITEM__', it.id), {
                                method: 'DELETE',
                                headers: {
                                    'Accept': 'application/json',
                                    ...(csrf ? {
                                        'X-CSRF-TOKEN': csrf
                                    } : {})
                                }
                            });
                            loadCart();
                        });
                        list.appendChild(row);
                    });
                }

                document.addEventListener('DOMContentLoaded', loadCart);
            })();
        </script>
    @endpush
@endsection
