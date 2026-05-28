(() => {
    const config = window.NB_PDP_CONFIG || {};
    if (!config.enabled) return;

    let selectedVariantId = config.selectedVariantId || '';
    const pdpVariants = Array.isArray(config.variants) ? config.variants : [];
    const pdpSelectedAttributes = config.selectedAttributes || {};
    const pdpIsLoggedIn = Boolean(config.isLoggedIn);
    const pdpFallbackCartMeta = config.fallbackCartMeta || {};
    const checkoutUrl = config.checkoutUrl || '/checkout';

    function changePdpImage(el, src) {
        document.getElementById('mainPdpImage').src = src;
        document.querySelectorAll('.thumb').forEach(t => t.classList.remove('active'));
        el.classList.add('active');
    }

    function formatMoney(value) {
        return '\u20B9' + Number(value || 0).toLocaleString('en-IN', {
            maximumFractionDigits: 0
        });
    }

    function pdpVariantLabel(variant) {
        const attributes = variant?.attributes || {};
        const parts = Array.isArray(attributes) ?
            attributes.filter(Boolean).map(value => String(value)) :
            Object.entries(attributes)
                .filter(([, value]) => value !== null && value !== undefined && String(value).trim() !== '')
                .map(([name, value]) => `${name}: ${value}`);

        return parts.join(' / ') || variant?.name || '';
    }

    function sameAttributes(variant, selected) {
        const keys = Object.keys(selected);
        if (!keys.length) return false;
        return keys.every(key => String(variant.attributes?.[key] ?? '') === String(selected[key] ?? ''));
    }

    function findMatchingVariant() {
        return pdpVariants.find(variant => sameAttributes(variant, pdpSelectedAttributes)) || null;
    }

    function updateVariantAvailability() {
        document.querySelectorAll('.pdp-option-btn').forEach(button => {
            const attribute = button.dataset.attribute;
            const value = button.dataset.value;
            const trial = {
                ...pdpSelectedAttributes,
                [attribute]: value
            };
            const possible = pdpVariants.some(variant => {
                return Object.keys(trial).every(key => String(variant.attributes?.[key] ?? '') ===
                    String(trial[key] ?? ''));
            });
            button.disabled = !possible;
        });
    }

    function applyVariantToPage(variant) {
        const addBtn = document.getElementById('pdpAddToCartBtn');
        const buyBtn = document.getElementById('pdpBuyNowBtn');
        const stockEl = document.getElementById('pdpVariantStock');
        const selectedEl = document.getElementById('pdpVariantSelected');
        const skuEl = document.getElementById('pdpVariantSku');
        const priceNow = document.getElementById('pdpPriceNow');
        const priceOld = document.getElementById('pdpPriceOld');
        const priceSave = document.getElementById('pdpPriceSave');
        const discountBadge = document.getElementById('pdpDiscountBadge');
        const cashback = document.getElementById('pdpCashback');

        if (!variant) {
            selectedVariantId = '';
            if (!pdpVariants.length) {
                if (stockEl) {
                    stockEl.textContent = 'Available';
                    stockEl.classList.remove('out');
                }
                if (addBtn) addBtn.disabled = false;
                if (buyBtn) buyBtn.disabled = false;
                return;
            }
            if (stockEl) {
                stockEl.textContent = 'Select available options';
                stockEl.classList.add('out');
            }
            if (addBtn) addBtn.disabled = true;
            if (buyBtn) buyBtn.disabled = true;
            return;
        }

        selectedVariantId = variant.id;
        if (priceNow) priceNow.textContent = formatMoney(variant.price);
        if (priceOld) {
            priceOld.textContent = variant.compare_price > variant.price ? formatMoney(variant.compare_price) : '';
            priceOld.classList.toggle('d-none', !(variant.compare_price > variant.price));
        }
        if (priceSave) {
            priceSave.textContent = variant.compare_price > variant.price ?
                `Save ${formatMoney(variant.save_amount)} (${variant.discount_percent}% Off)` :
                '';
            priceSave.classList.toggle('d-none', !(variant.compare_price > variant.price));
        }
        if (discountBadge) {
            discountBadge.textContent = variant.discount_percent > 0 ? `${variant.discount_percent}% OFF` : '';
            discountBadge.classList.toggle('d-none', !(variant.discount_percent > 0));
        }
        if (cashback) cashback.textContent = `Get ${variant.coins} NB Coins on this purchase!`;
        if (skuEl) skuEl.textContent = `SKU: ${variant.sku}`;
        if (selectedEl) selectedEl.textContent = pdpVariantLabel(variant);
        if (stockEl) {
            stockEl.classList.toggle('out', !variant.available);
            if (variant.available) {
                stockEl.textContent = variant.track_stock ? `${variant.stock_qty} unit piece` : 'Available';

                // Update Quantity Input Max
                const qtyInput = document.getElementById('pdpQtyVal');
                if (qtyInput) {
                    const maxStock = variant.track_stock ? variant.stock_qty : 99;
                    qtyInput.max = maxStock;
                    if (parseInt(qtyInput.value) > maxStock) {
                        qtyInput.value = maxStock;
                    }
                }
            } else {
                stockEl.textContent = 'Out of stock';
            }
        }
        if (addBtn) addBtn.disabled = !variant.available;
        if (buyBtn) buyBtn.disabled = !variant.available;
    }

    function addPdpGuestCartFallback(productId, quantity = 1, variantId = null) {
        const key = 'nb_pending_cart';
        const itemKey = String(Number(productId || 0));
        let items = [];

        try {
            const parsed = JSON.parse(localStorage.getItem(key) || '[]');
            items = Array.isArray(parsed) ? parsed : [];
        } catch (error) {
            items = [];
        }

        const activeVariant = pdpVariants.find(variant => String(variant.id) === String(variantId));
        const meta = {
            ...pdpFallbackCartMeta,
            variant_name: pdpVariantLabel(activeVariant),
            unit_price: Number(activeVariant?.price || pdpFallbackCartMeta.unit_price || 0),
        };
        const variantKey = String(Number(variantId || 0));
        const found = items.find(item =>
            String(Number(item.product_id || 0)) === itemKey &&
            String(Number(item.product_variant_id || 0)) === variantKey
        );

        if (found) {
            found.quantity = Number(found.quantity || 0) + Number(quantity || 1);
            found.product_variant_id = variantId ? Number(variantId) : null;
            found.product_name = meta.product_name;
            found.variant_name = meta.variant_name;
            found.image = meta.image;
            found.unit_price = meta.unit_price;
            found.product_url = meta.product_url;
        } else {
            items.push({
                product_id: Number(productId),
                product_variant_id: variantId ? Number(variantId) : null,
                quantity: Number(quantity || 1),
                ...meta,
            });
        }

        localStorage.setItem(key, JSON.stringify(items));

        const count = items.reduce((sum, item) => sum + Number(item.quantity || 0), 0);
        const cartCount = document.getElementById('cartCount');
        if (cartCount) cartCount.textContent = String(count);
        return true;
    }

    function selectPdpOption(button) {
        const attribute = button.dataset.attribute;
        const value = button.dataset.value;
        pdpSelectedAttributes[attribute] = value;
        document.querySelectorAll('.pdp-option-btn').forEach(item => {
            if (item.dataset.attribute === attribute) {
                item.classList.toggle('active', item === button);
            }
        });
        updateVariantAvailability();
        applyVariantToPage(findMatchingVariant());
    }

    async function handleAddToCart(productId, btn) {
        const variantId = selectedVariantId || null;
        if (pdpVariants.length && !variantId) {
            if (typeof nbToast === 'function') nbToast('Please choose a product option first.', 'error');
            return;
        }

        const qtyInput = document.getElementById('pdpQtyVal');
        const qty = qtyInput ? parseInt(qtyInput.value, 10) || 1 : 1;

        if (typeof window.addToCart === 'function') {
            if (btn) btn.disabled = true;
            const added = await window.addToCart(productId, qty, variantId, btn);
            if (btn) btn.disabled = false;
            if (added && typeof nbToast === 'function') {
                nbToast('Product added to cart.', 'success');
            } else if (!added && !pdpIsLoggedIn && addPdpGuestCartFallback(productId, qty, variantId)) {
                if (typeof nbToast === 'function') nbToast('Product added to cart.', 'success');
            }
        } else {
            console.warn('Global addToCart not found, using fallback');
            if (!pdpIsLoggedIn && addPdpGuestCartFallback(productId, qty, variantId)) {
                if (typeof nbToast === 'function') nbToast('Product added to cart.', 'success');
            } else if (typeof nbToast === 'function') {
                nbToast('Cart is still loading. Please try again.', 'warning');
            }
        }
    }

    async function handleBuyNow(productId, btn) {
        const variantId = selectedVariantId || null;
        if (pdpVariants.length && !variantId) {
            if (typeof nbToast === 'function') nbToast('Please choose a product option first.', 'error');
            return;
        }

        const qtyInput = document.getElementById('pdpQtyVal');
        const qty = qtyInput ? parseInt(qtyInput.value, 10) || 1 : 1;

        if (typeof window.addToCart === 'function') {
            if (btn) btn.disabled = true;
            const added = await window.addToCart(productId, qty, variantId, btn);
            if (btn) btn.disabled = false;
            if (added) {
                window.location.href = checkoutUrl;
            } else if (!pdpIsLoggedIn && addPdpGuestCartFallback(productId, qty, variantId)) {
                window.location.href = checkoutUrl;
            }
        } else {
            if (!pdpIsLoggedIn && addPdpGuestCartFallback(productId, qty, variantId)) {
                window.location.href = checkoutUrl;
            } else if (typeof nbToast === 'function') {
                nbToast('Cart is still loading. Please try again.', 'warning');
            }
        }
    }

    window.changePdpImage = changePdpImage;
    window.handleAddToCart = handleAddToCart;
    window.handleBuyNow = handleBuyNow;

    // Star Rating Interaction
    document.querySelectorAll('.star-opt').forEach(star => {
        star.addEventListener('click', function () {
            const val = this.getAttribute('data-val');
            document.getElementById('ratingValue').value = val;

            // Color stars
            document.querySelectorAll('.star-opt').forEach(s => {
                if (s.getAttribute('data-val') <= val) {
                    s.style.color = '#FFD700'; // Gold
                } else {
                    s.style.color = '#ddd';
                }
            });
        });

        star.addEventListener('mouseover', function () {
            const val = this.getAttribute('data-val');
            document.querySelectorAll('.star-opt').forEach(s => {
                if (s.getAttribute('data-val') <= val) {
                    s.style.color = '#FFD700';
                } else {
                    s.style.color = '#ddd';
                }
            });
        });

        star.addEventListener('mouseout', function () {
            const val = document.getElementById('ratingValue').value;
            document.querySelectorAll('.star-opt').forEach(s => {
                if (s.getAttribute('data-val') <= val) {
                    s.style.color = '#FFD700';
                } else {
                    s.style.color = '#ddd';
                }
            });
        });
    });

    // Default set 5 stars
    window.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.pdp-option-btn').forEach(button => {
            button.addEventListener('click', () => selectPdpOption(button));
        });
        updateVariantAvailability();
        if (pdpVariants.length) {
            applyVariantToPage(findMatchingVariant() || pdpVariants[0] || null);
        } else {
            applyVariantToPage(null);
        }

        const defaultVal = 5;
        document.querySelectorAll('.star-opt').forEach(s => {
            if (s.getAttribute('data-val') <= defaultVal) {
                s.style.color = '#FFD700';
            }
        });

        // PDP Quantity Logic
        const qtyVal = document.getElementById('pdpQtyVal');
        const qtyPlus = document.getElementById('pdpQtyPlus');
        const qtyMinus = document.getElementById('pdpQtyMinus');

        if (qtyVal && qtyPlus && qtyMinus) {
            qtyPlus.addEventListener('click', () => {
                const max = parseInt(qtyVal.max) || 99;
                const current = parseInt(qtyVal.value) || 1;
                if (current < max) {
                    qtyVal.value = current + 1;
                } else if (typeof nbToast === 'function') {
                    nbToast(`Only ${max} units available in stock.`, 'warning');
                }
            });

            qtyMinus.addEventListener('click', () => {
                const current = parseInt(qtyVal.value) || 1;
                if (current > 1) {
                    qtyVal.value = current - 1;
                }
            });
        }

        const featureSlider = document.getElementById('flavorRow');
        const sliderShell = featureSlider?.closest('.feature-slider-shell');
        if (featureSlider && sliderShell) {
            const scrollByCard = direction => {
                const firstCard = featureSlider.querySelector('.flavor-opt');
                const distance = firstCard ? firstCard.getBoundingClientRect().width + 10 : 142;
                featureSlider.scrollBy({
                    left: direction * distance,
                    behavior: 'smooth',
                });
            };

            sliderShell.querySelector('.feature-slider-prev')?.addEventListener('click', () => scrollByCard(-
                1));
            sliderShell.querySelector('.feature-slider-next')?.addEventListener('click', () => scrollByCard(1));
        }
    });
})();

// --- Return Modal Logic ---
window.initNbReturnModal = function (config) {
    const overlay = document.getElementById("nbRetModalOverlay");
    const openBtn = document.getElementById("nbRetModalOpenBtn");
    const closeBtn = document.getElementById("nbRetModalCloseBtn");
    const form = document.getElementById("returnRequestForm");
    const orderSelect = document.getElementById("returnOrderSelect");

    if (!overlay || !openBtn || !closeBtn || !form || !orderSelect) return;

    let deliveredReturnOrders = [];

    function openModal() {
        overlay.style.display = "flex";
        setTimeout(() => overlay.classList.add("show"), 10);
    }

    function closeModal() {
        overlay.classList.remove("show");
        setTimeout(() => overlay.style.display = "none", 300);
    }

    openBtn.addEventListener("click", openModal);
    closeBtn.addEventListener("click", closeModal);
    overlay.addEventListener("click", (e) => {
        if (e.target === overlay) closeModal();
    });

    function renderReturns(returns) {
        const container = document.getElementById("myReturnsContainer");
        if (!container) return;
        if (!returns.length) {
            container.innerHTML = "<p>No return requests found.</p>";
            return;
        }

        container.innerHTML = returns.map(function (item) {
            const orderNumber = item.order ? item.order.order_number : "-";
            const qty = (item.items || []).reduce(function (sum, line) { return sum + Number(line.quantity || 0); }, 0);
            return `<div style="padding:10px 0;border-bottom:1px solid var(--line,#eee);">
            <p><strong>${item.return_number}</strong> - ${String(item.status || "").toUpperCase()}</p>
            <p style="font-size:.82rem;color:var(--mu,#666)">Order: ${orderNumber}${qty ? ` - Qty: ${qty}` : ""}</p>
            </div>`;
        }).join("");
    }

    function renderDeliveredOrders(orders) {
        const delivered = orders.filter(function (order) {
            return order.status === "delivered" && (order.items || []).some(function (item) {
                return Number(item.returnable_quantity || 0) > 0;
            });
        });
        deliveredReturnOrders = delivered;
        orderSelect.innerHTML = "<option value=\"\">Select Delivered Order</option>";
        delivered.forEach(function (order) {
            const option = document.createElement("option");
            option.value = order.id;
            option.textContent = `${order.order_number} - \u20B9${Number(order.grand_total || 0).toFixed(2)}`;
            orderSelect.appendChild(option);
        });
        renderReturnItemsForOrder("");
    }

    function renderReturnItemsForOrder(orderId) {
        const wrap = document.getElementById("returnItemsContainer");
        if (!wrap) return;

        const order = deliveredReturnOrders.find(function (item) {
            return String(item.id) === String(orderId);
        });

        if (!order) {
            wrap.innerHTML = "";
            return;
        }

        const items = (order.items || []).filter(function (item) {
            return Number(item.returnable_quantity || 0) > 0;
        });

        if (!items.length) {
            wrap.innerHTML = "<p style=\"font-size:.85rem;color:var(--mu,#666);\">No returnable quantity left for this order.</p>";
            return;
        }

        wrap.innerHTML = `
            <div style="font-size:.86rem;font-weight:900;color:var(--dk);margin-bottom:8px;">Select quantity to return</div>
            ${items.map(function (item) {
            const maxQty = Number(item.returnable_quantity || 0);
            const returnedQty = Number(item.returned_quantity || 0);
            return `<div class="nb-ret-item-row">
                <div class="nb-ret-item-details">
                <div class="nb-ret-item-title">${item.product_name || "Product"}</div>
                <div class="nb-ret-item-meta">Purchased: ${item.quantity || 0}${returnedQty ? ` - Already requested: ${returnedQty}` : ""}</div>
                </div>
                <input type="number" class="nb-ret-qty-input" data-order-item-id="${item.id}" min="0" max="${maxQty}" value="0" aria-label="Return quantity for ${item.product_name || "product"}">
            </div>`;
        }).join("")}
        `;
    }

    async function loadReturnData() {
        const [ordersResponse, returnsResponse] = await Promise.all([
            fetch(config.ordersUrl, { headers: { "Accept": "application/json" } }),
            fetch(config.returnsUrl, { headers: { "Accept": "application/json" } })
        ]);

        if (ordersResponse.ok) {
            const ordersPayload = await ordersResponse.json();
            renderDeliveredOrders(ordersPayload.data || []);
        }

        if (returnsResponse.ok) {
            const returnsPayload = await returnsResponse.json();
            renderReturns(returnsPayload.data || []);
        }
    }

    async function submitReturnRequest(event) {
        event.preventDefault();
        const orderId = orderSelect.value;
        const reason = document.getElementById("returnReasonSelect").value;
        const comments = document.getElementById("returnCommentsInput").value.trim();
        const attachments = document.getElementById("returnAttachmentsInput").files;
        const message = document.getElementById("returnFormMessage");
        const selectedItems = Array.from(document.querySelectorAll(".nb-ret-qty-input"))
            .map(function (input) {
                return {
                    order_item_id: input.getAttribute("data-order-item-id"),
                    quantity: Number(input.value || 0),
                    max: Number(input.max || 0)
                };
            })
            .filter(function (item) {
                return item.order_item_id && item.quantity > 0;
            });

        if (!orderId || !reason) {
            message.textContent = "Please select an order and a return reason.";
            return;
        }
        if (!selectedItems.length) {
            message.textContent = "Please enter return quantity for at least one product.";
            return;
        }
        const invalidQty = selectedItems.find(function (item) {
            return item.quantity > item.max;
        });
        if (invalidQty) {
            message.textContent = "Return quantity cannot be greater than purchased quantity left.";
            return;
        }

        const formData = new FormData();
        formData.append("reason", reason);
        if (comments) formData.append("comments", comments);
        selectedItems.forEach(function (item, index) {
            formData.append(`items[${index}][order_item_id]`, item.order_item_id);
            formData.append(`items[${index}][quantity]`, item.quantity);
        });

        for (let i = 0; i < attachments.length; i++) {
            formData.append("attachments[]", attachments[i]);
        }

        message.textContent = "Submitting...";

        const response = await fetch(config.createReturnUrlTemplate.replace("__ORDER_ID__", orderId), {
            method: "POST",
            headers: {
                "Accept": "application/json",
                "X-CSRF-TOKEN": config.csrfToken
            },
            body: formData
        });

        if (!response.ok) {
            const payload = await response.json().catch(function () { return {}; });
            message.textContent = payload.message || "Unable to submit return request.";
            return;
        }

        message.textContent = "Return request submitted successfully.";
        setTimeout(() => {
            closeModal();
            message.textContent = "";
            document.getElementById("returnReasonSelect").value = "";
            document.getElementById("returnCommentsInput").value = "";
            document.getElementById("returnAttachmentsInput").value = "";
            orderSelect.value = "";
            renderReturnItemsForOrder("");
        }, 1500);

        await loadReturnData();
    }

    form.addEventListener("submit", submitReturnRequest);
    orderSelect.addEventListener("change", function () {
        renderReturnItemsForOrder(this.value);
    });

    loadReturnData();
};


window.addEventListener("DOMContentLoaded", () => {
    // --- Order Details Return Modal ---
    const orderRetOverlay = document.getElementById("orderDetailReturnModalOverlay");
    const orderRetForm = document.getElementById("orderDetailReturnForm");
    const orderRetErrorEl = document.getElementById("returnQuantityError");

    // We bind open/close globally if there are triggers.
    window.openOrderDetailReturnModal = function () {
        if (orderRetOverlay) {
            orderRetOverlay.classList.remove("d-none");
            orderRetOverlay.style.display = "flex";
            setTimeout(() => orderRetOverlay.classList.add("show"), 10);
        }
    };

    window.closeOrderDetailReturnModal = function () {
        if (orderRetOverlay) {
            orderRetOverlay.classList.remove("show");
            setTimeout(() => {
                orderRetOverlay.style.display = "none";
                orderRetOverlay.classList.add("d-none");
            }, 300);
        }
    };

    if (orderRetOverlay) {
        orderRetOverlay.addEventListener("click", (e) => {
            if (e.target === orderRetOverlay) window.closeOrderDetailReturnModal();
        });
    }

    if (orderRetForm) {
        orderRetForm.addEventListener("submit", function (event) {
            let hasQuantity = false;

            this.querySelectorAll("[data-return-line]").forEach((line) => {
                const hiddenInput = line.querySelector("[data-return-hidden]");
                const qtyInput = line.querySelector("[data-return-qty]");
                const qty = Number(qtyInput.value || 0);
                const max = Number(qtyInput.max || 0);

                qtyInput.value = Math.max(0, Math.min(qty, max));

                if (Number(qtyInput.value) > 0) {
                    hasQuantity = true;
                    hiddenInput.disabled = false;
                    qtyInput.disabled = false;
                } else {
                    hiddenInput.disabled = true;
                    qtyInput.disabled = true;
                }
            });

            if (!hasQuantity) {
                event.preventDefault();
                this.querySelectorAll("[data-return-hidden], [data-return-qty]").forEach((input) => {
                    input.disabled = false;
                });
                if (orderRetErrorEl) {
                    orderRetErrorEl.style.display = "block";
                }
            }
        });
    }
});

