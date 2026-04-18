/**
 * MeaCash Product Modal — DailyCard-style purchase flow
 *
 * Opens a glassmorphic modal when clicking a product card.
 * Adapts UI based on product_type: fixed_package | account_topup | custom_quantity
 * Supports multi-form tabs, AJAX add-to-cart, and live price calculation.
 */

const CART_ADD_URL = '/cart/add';
const API_BASE = '/api/product/';

let currentProduct = null;
let selectedPackageId = null;
let selectedFormKey = null;
let currentQuantity = 1;

// ─── DOM References ───
function getModal() { return document.getElementById('sf-product-modal'); }
function getBackdrop() { return document.getElementById('sf-modal-backdrop'); }
function getBody() { return document.getElementById('sf-modal-body'); }

// ─── Utilities ───
function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str ?? '';
    return div.innerHTML;
}

function formatPrice(price) {
    return '$' + Number(price).toFixed(2);
}

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
}

function isRtl() {
    return document.documentElement.dir === 'rtl';
}

// ─── Modal Open/Close ───
function openModal() {
    const backdrop = getBackdrop();
    if (!backdrop) return;
    backdrop.classList.add('sf-modal-active');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    const backdrop = getBackdrop();
    if (!backdrop) return;
    backdrop.classList.remove('sf-modal-active');
    document.body.style.overflow = '';
    currentProduct = null;
    selectedPackageId = null;
    selectedFormKey = null;
    currentQuantity = 1;
}

// ─── Loading State ───
function showLoading() {
    const body = getBody();
    if (!body) return;
    body.innerHTML = `
        <div class="sf-modal-loading">
            <div class="sf-skeleton" style="height:10rem;border-radius:var(--sf-radius-md);"></div>
            <div class="sf-skeleton" style="height:1.5rem;width:60%;margin-top:1rem;border-radius:8px;"></div>
            <div class="sf-skeleton" style="height:1rem;width:40%;margin-top:0.5rem;border-radius:8px;"></div>
            <div class="sf-skeleton" style="height:4rem;margin-top:1rem;border-radius:var(--sf-radius-sm);"></div>
            <div class="sf-skeleton" style="height:3rem;margin-top:1rem;border-radius:1rem;"></div>
        </div>
    `;
}

// ─── Fetch & Render ───
async function loadProduct(slug) {
    showLoading();
    openModal();

    try {
        const res = await fetch(API_BASE + slug, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (!res.ok) throw new Error('Product not found');
        currentProduct = await res.json();

        // Default selections
        if (currentProduct.packages?.length > 0) {
            selectedPackageId = currentProduct.packages[0].id;
        }
        if (currentProduct.forms?.length > 0) {
            const defaultForm = currentProduct.forms.find(f => f.is_default) || currentProduct.forms[0];
            selectedFormKey = defaultForm.key;
        } else {
            selectedFormKey = null;
        }
        currentQuantity = currentProduct.min_quantity || 1;

        renderProduct();
    } catch (err) {
        const body = getBody();
        if (body) {
            body.innerHTML = `<div class="sf-modal-error"><p>Failed to load product.</p><button onclick="window.__sfCloseModal()" class="sf-btn-outline" style="margin-top:1rem;">Close</button></div>`;
        }
    }
}

function renderProduct() {
    const p = currentProduct;
    const body = getBody();
    if (!body || !p) return;

    const type = p.product_type;
    const hasPackages = p.packages?.length > 0;
    const hasGlobalFields = p.fields?.length > 0;
    const hasFormTabs = p.forms?.length > 0;
    const hasMultiForms = p.forms?.length > 1;
    const hasAnyFields = hasGlobalFields || hasFormTabs;

    let html = '';

    // ── Header ──
    html += `<div class="sf-modal-header">`;
    if (p.image) {
        html += `<div class="sf-modal-img"><img src="${escapeHtml(p.image)}" alt="${escapeHtml(p.name)}"></div>`;
    } else {
        html += `<div class="sf-modal-img sf-modal-img-placeholder"><span>🎮</span></div>`;
    }
    html += `<div class="sf-modal-info">`;
    html += `<h2 class="sf-modal-title">${escapeHtml(p.name)}</h2>`;
    if (p.category || p.subcategory) {
        html += `<p class="sf-modal-meta">${escapeHtml(p.category?.name ?? '')}${p.subcategory ? ' › ' + escapeHtml(p.subcategory.name) : ''}</p>`;
    }
    if (p.delivery_type === 'instant') {
        html += `<span class="sf-modal-delivery">⚡ ${isRtl() ? 'تسليم فوري' : 'Instant Delivery'}</span>`;
    }
    html += `</div></div>`;

    // ── Description ──
    if (p.description) {
        html += `<p class="sf-modal-desc">${escapeHtml(p.description)}</p>`;
    }

    // ── Packages (fixed_package & account_topup) ──
    if (hasPackages && type !== 'custom_quantity') {
        html += `<div class="sf-modal-section-label">${isRtl() ? 'اختر الباقة' : 'Select Package'}</div>`;
        html += `<div class="sf-modal-packages">`;
        p.packages.forEach((pkg, i) => {
            const isSelected = pkg.id === selectedPackageId;
            html += `<button type="button" class="sf-modal-pkg ${isSelected ? 'selected' : ''}" data-pkg-id="${pkg.id}" data-pkg-price="${pkg.selling_price}">`;
            html += `<span class="sf-modal-pkg-name">${escapeHtml(pkg.name)}</span>`;
            html += `<span class="sf-modal-pkg-price">${formatPrice(pkg.selling_price)}</span>`;
            if (pkg.badge_text) {
                html += `<span class="sf-modal-pkg-badge">${escapeHtml(pkg.badge_text)}</span>`;
            }
            html += `</button>`;
        });
        html += `</div>`;
    }

    // ── Quantity Selector (custom_quantity) ──
    if (type === 'custom_quantity') {
        const min = p.min_quantity || 1;
        const max = p.max_quantity || 10000;
        const price = p.price_per_unit || p.selling_price;
        const total = (currentQuantity * price).toFixed(2);

        html += `<div class="sf-modal-section-label">${isRtl() ? 'اختر الكمية' : 'Select Quantity'}</div>`;
        html += `<div class="sf-modal-quantity">`;

        html += `<button type="button" id="sf-open-qty-input" class="sf-form-tab active">${isRtl() ? 'حسب الكمية' : 'By Quantity'}</button>`;
        html += `<div id="sf-qty-input-wrap" style="display:none;margin-top:0.75rem;">`;
        html += `<input type="number" id="sf-qty-input" class="sf-qty-number" min="${min}" max="${max}" value="${currentQuantity}" step="1" placeholder="${isRtl() ? 'ادخل الكمية' : 'Enter quantity'}">`;
        html += `<p class="hint" style="margin-top:0.4rem;">${isRtl() ? `الحد الأدنى ${min} - الحد الأقصى ${max}` : `Min ${min} - Max ${max}`}</p>`;
        html += `</div>`;

        html += `<div class="sf-qty-info">`;
        html += `<span>${isRtl() ? 'السعر لكل وحدة' : 'Price per unit'}: ${formatPrice(price)}</span>`;
        html += `<span id="sf-qty-total" class="sf-qty-total">${isRtl() ? 'المجموع' : 'Total'}: ${formatPrice(total)}</span>`;
        html += `</div>`;
        html += `</div>`;
    }

    // ── Layer 1: Global Fields (always visible, above tabs) ──
    if (hasGlobalFields) {
        html += `<div id="sf-global-fields" class="sf-modal-fields">`;
        html += renderFieldsArray(p.fields);
        html += `</div>`;
    }

    // ── Layer 2: Tab Switcher (only if multiple forms) ──
    if (hasMultiForms) {
        html += `<div class="sf-form-tabs">`;
        p.forms.forEach(form => {
            const isActive = form.key === selectedFormKey;
            html += `<button type="button" class="sf-form-tab ${isActive ? 'active' : ''}" data-form-key="${escapeHtml(form.key)}">${escapeHtml(form.label)}</button>`;
        });
        html += `</div>`;
    }

    // ── Layer 2: Active Tab Fields (toggled by tab selection) ──
    if (hasFormTabs) {
        html += `<div id="sf-modal-fields" class="sf-modal-fields">`;
        html += renderActiveTabFields();
        html += `</div>`;
    }

    // ── Price Summary ──
    html += `<div class="sf-modal-summary">`;
    html += `<span class="sf-modal-total-label">${isRtl() ? 'المجموع' : 'Total'}</span>`;
    html += `<span id="sf-modal-total" class="sf-modal-total-price">${formatPrice(getCurrentPrice())}</span>`;
    html += `</div>`;

    // ── Add to Cart ──
    html += `<button type="button" id="sf-modal-add-btn" class="sf-btn-gold sf-modal-add-btn">`;
    html += `<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>`;
    html += `${isRtl() ? 'أضف للسلة' : 'Add to Cart'}`;
    html += `</button>`;

    // ── Toast (hidden) ──
    html += `<div id="sf-modal-toast" class="sf-modal-toast hidden"></div>`;

    body.innerHTML = html;
    bindModalEvents();
}

/**
 * Render a flat array of field objects into HTML inputs.
 * Used for both global fields and active-tab fields.
 */
function renderFieldsArray(fields) {
    let html = '';
    (fields || []).forEach(field => {
        html += `<div class="sf-field">`;
        html += `<label for="sf-field-${escapeHtml(field.key)}">${escapeHtml(field.label)}`;
        if (field.required) html += ` <span style="color:var(--sf-hot-red);">*</span>`;
        html += `</label>`;

        if (field.type === 'select') {
            html += `<select name="form_data[${escapeHtml(field.key)}]" id="sf-field-${escapeHtml(field.key)}" ${field.required ? 'required' : ''}>`;
            html += `<option value="">${escapeHtml(field.placeholder) || (isRtl() ? 'اختر...' : 'Select...')}</option>`;
            (field.options || []).forEach(opt => {
                html += `<option value="${escapeHtml(opt)}">${escapeHtml(opt)}</option>`;
            });
            html += `</select>`;
        } else if (field.type === 'textarea') {
            html += `<textarea name="form_data[${escapeHtml(field.key)}]" id="sf-field-${escapeHtml(field.key)}" placeholder="${escapeHtml(field.placeholder)}" ${field.required ? 'required' : ''} rows="3"></textarea>`;
        } else {
            let extraAttrs = '';
            if (field.type === 'number') {
                if (field.min !== undefined && field.min !== null) extraAttrs += ` min="${field.min}"`;
                if (field.max !== undefined && field.max !== null) extraAttrs += ` max="${field.max}"`;
            }
            html += `<input type="${field.type || 'text'}" name="form_data[${escapeHtml(field.key)}]" id="sf-field-${escapeHtml(field.key)}" placeholder="${escapeHtml(field.placeholder)}" ${field.required ? 'required' : ''}${extraAttrs}>`;
        }
        html += `<div class="sf-field-error" id="sf-err-${escapeHtml(field.key)}"></div>`;
        html += `</div>`;
    });
    return html;
}

/**
 * Render only the fields belonging to the currently active tab.
 */
function renderActiveTabFields() {
    const p = currentProduct;
    if (!p?.forms?.length) return '';
    const activeForm = p.forms.find(f => f.key === selectedFormKey) || p.forms[0];
    return renderFieldsArray(activeForm.fields);
}

function getCurrentPrice() {
    const p = currentProduct;
    if (!p) return 0;
    if (p.product_type === 'custom_quantity') {
        return currentQuantity * (p.price_per_unit || p.selling_price);
    }
    if (selectedPackageId && p.packages?.length) {
        const pkg = p.packages.find(pk => pk.id === selectedPackageId);
        return pkg ? pkg.selling_price : p.selling_price;
    }
    return p.selling_price;
}

function getClampedQuantity(value) {
    const p = currentProduct;
    if (!p) return 1;

    const min = p.min_quantity || 1;
    const max = p.max_quantity || 10000;
    const parsed = parseInt(value, 10);
    const normalized = Number.isFinite(parsed) ? parsed : min;

    return Math.max(min, Math.min(max, normalized));
}

function updatePriceDisplay() {
    const totalEl = document.getElementById('sf-modal-total');
    if (totalEl) {
        totalEl.textContent = formatPrice(getCurrentPrice());
        // Trigger price pulse animation
        totalEl.classList.remove('sf-price-updated');
        void totalEl.offsetWidth; // force reflow to restart animation
        totalEl.classList.add('sf-price-updated');
    }

    const qtyTotalEl = document.getElementById('sf-qty-total');
    if (qtyTotalEl) {
        const label = isRtl() ? 'المجموع' : 'Total';
        qtyTotalEl.textContent = `${label}: ${formatPrice(getCurrentPrice())}`;
    }
}

// ─── Events ───
function bindModalEvents() {
    // Package selection with animation
    document.querySelectorAll('.sf-modal-pkg').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.sf-modal-pkg').forEach(b => b.classList.remove('selected'));
            btn.classList.add('selected');
            // Re-trigger selection animation
            btn.style.animation = 'none';
            void btn.offsetWidth;
            btn.style.animation = '';
            selectedPackageId = parseInt(btn.dataset.pkgId);
            updatePriceDisplay();
        });
    });

    // Form tab switching with crossfade + explicit data clearing
    document.querySelectorAll('.sf-form-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.sf-form-tab').forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            selectedFormKey = tab.dataset.formKey;
            const fieldsContainer = document.getElementById('sf-modal-fields');
            if (fieldsContainer) {
                // Clear previous tab's field values explicitly before switching
                fieldsContainer.querySelectorAll('input, select, textarea').forEach(el => { el.value = ''; });
                fieldsContainer.classList.add('sf-switching');
                setTimeout(() => {
                    fieldsContainer.innerHTML = renderActiveTabFields();
                    fieldsContainer.classList.remove('sf-switching');
                }, 200);
            }
        });
    });

    // Quantity direct input (opened by button)
    const openQtyBtn = document.getElementById('sf-open-qty-input');
    const qtyWrap = document.getElementById('sf-qty-input-wrap');
    const qtyInput = document.getElementById('sf-qty-input');
    if (openQtyBtn && qtyWrap && qtyInput) {
        openQtyBtn.addEventListener('click', () => {
            qtyWrap.style.display = 'block';
            qtyInput.focus();
            qtyInput.select();
        });

        const syncQuantity = () => {
            currentQuantity = getClampedQuantity(qtyInput.value);
            qtyInput.value = String(currentQuantity);
            updatePriceDisplay();
        };

        qtyInput.addEventListener('input', syncQuantity);
        qtyInput.addEventListener('blur', syncQuantity);
    }

    // Add to cart
    const addBtn = document.getElementById('sf-modal-add-btn');
    if (addBtn) {
        addBtn.addEventListener('click', handleAddToCart);
    }
}

/**
 * Client-side validation: checks required, email, numeric rules
 * before hitting the server. Returns true if valid.
 */
function validateClientSide() {
    const p = currentProduct;
    if (!p) return true;

    let isValid = true;
    // Collect all active fields: global + active tab
    const activeFields = [...(p.fields || [])];
    if (p.forms?.length > 0) {
        const activeForm = p.forms.find(f => f.key === selectedFormKey) || p.forms[0];
        activeFields.push(...(activeForm.fields || []));
    }

    activeFields.forEach(field => {
        const el = document.getElementById('sf-field-' + field.key);
        const errEl = document.getElementById('sf-err-' + field.key);
        if (!el || !errEl) return;

        const value = el.value.trim();
        errEl.textContent = '';

        const rules = field.rules || [];

        if (rules.includes('required') && !value) {
            errEl.textContent = isRtl() ? 'هذا الحقل مطلوب' : 'This field is required';
            isValid = false;
            return;
        }
        if (rules.includes('email') && value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
            errEl.textContent = isRtl() ? 'بريد إلكتروني غير صالح' : 'Invalid email address';
            isValid = false;
            return;
        }
        if (rules.includes('numeric') && value) {
            const num = parseFloat(value);
            if (isNaN(num)) {
                errEl.textContent = isRtl() ? 'يجب أن يكون رقمًا' : 'Must be a number';
                isValid = false;
                return;
            }
            if (field.min !== undefined && field.min !== null && num < field.min) {
                errEl.textContent = isRtl() ? `الحد الأدنى هو ${field.min}` : `Minimum value is ${field.min}`;
                isValid = false;
                return;
            }
            if (field.max !== undefined && field.max !== null && num > field.max) {
                errEl.textContent = isRtl() ? `الحد الأقصى هو ${field.max}` : `Maximum value is ${field.max}`;
                isValid = false;
                return;
            }
        }
    });

    return isValid;
}

async function handleAddToCart() {
    const btn = document.getElementById('sf-modal-add-btn');
    if (!btn || !currentProduct) return;

    // Clear previous errors
    document.querySelectorAll('.sf-field-error').forEach(el => el.textContent = '');

    // Client-side validation (Gap 3 fix)
    if (!validateClientSide()) return;

    // Collect form data from BOTH global fields and active tab fields
    const formData = {};
    document.querySelectorAll('#sf-global-fields input, #sf-global-fields select, #sf-global-fields textarea, #sf-modal-fields input, #sf-modal-fields select, #sf-modal-fields textarea').forEach(el => {
        const name = el.name;
        if (name && name.startsWith('form_data[')) {
            const key = name.replace('form_data[', '').replace(']', '');
            formData[key] = el.value;
        }
    });

    const payload = {
        product_id: currentProduct.id,
        package_id: selectedPackageId,
        quantity: currentProduct.product_type === 'custom_quantity' ? currentQuantity : 1,
        form_data: formData,
        selected_form: selectedFormKey,
    };

    btn.disabled = true;
    btn.innerHTML = `<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"></circle><path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" class="opacity-75"></path></svg> ${isRtl() ? 'جاري الإضافة...' : 'Adding...'}`;

    try {
        const res = await fetch(CART_ADD_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(payload),
        });

        if (res.status === 401 || res.redirected) {
            window.location.href = '/auth/login';
            return;
        }

        const contentType = res.headers.get('content-type') || '';
        if (!contentType.includes('application/json')) {
            showToast(false);
            return;
        }

        const data = await res.json();

        if (!res.ok) {
            // Validation errors
            if (data.errors) {
                Object.entries(data.errors).forEach(([key, messages]) => {
                    const cleanKey = key.replace('form_data.', '');
                    const errEl = document.getElementById('sf-err-' + cleanKey);
                    if (errEl) errEl.textContent = messages[0];
                });
            }
            btn.disabled = false;
            btn.innerHTML = `<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg> ${isRtl() ? 'أضف للسلة' : 'Add to Cart'}`;
            return;
        }

        // Success — update badge
        document.querySelectorAll('.sf-cart-badge').forEach(el => {
            el.textContent = data.count;
            el.style.display = 'flex';
        });
        // If no badge exists, create one
        if (document.querySelectorAll('.sf-cart-badge').length === 0) {
            // Navbar cart link
            document.querySelectorAll('a[href*="/cart"]').forEach(link => {
                if (!link.querySelector('.sf-cart-badge')) {
                    const badge = document.createElement('span');
                    badge.className = 'sf-cart-badge';
                    badge.textContent = data.count;
                    link.style.position = 'relative';
                    link.appendChild(badge);
                }
            });
        }

        // Show toast
        showToast(true);

    } catch (err) {
        showToast(false);
    } finally {
        btn.disabled = false;
        btn.innerHTML = `<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg> ${isRtl() ? 'أضف للسلة' : 'Add to Cart'}`;
    }
}

function showToast(success) {
    const toast = document.getElementById('sf-modal-toast');
    if (!toast) return;

    if (success) {
        toast.className = 'sf-modal-toast sf-toast-success';
        toast.innerHTML = `
            <span>✓ ${isRtl() ? 'تمت الإضافة للسلة!' : 'Added to cart!'}</span>
            <div class="sf-toast-actions">
                <a href="/cart" class="sf-toast-link">${isRtl() ? 'عرض السلة' : 'View Cart'}</a>
                <button type="button" data-sf-toast-action="continue" class="sf-toast-link">${isRtl() ? 'متابعة' : 'Continue'}</button>
            </div>
        `;
    } else {
        toast.className = 'sf-modal-toast sf-toast-error';
        toast.innerHTML = `<span>✗ ${isRtl() ? 'حدث خطأ' : 'Something went wrong'}</span>`;
    }
    toast.classList.remove('hidden');

    if (success) {
        const continueBtn = toast.querySelector('[data-sf-toast-action="continue"]');
        if (continueBtn) {
            continueBtn.addEventListener('click', (e) => {
                e.preventDefault();
                toast.classList.add('hidden');
                closeModal();
            });
        }
    }

    if (!success) {
        setTimeout(() => toast.classList.add('hidden'), 3000);
    }
}

// ─── Init ───
function init() {
    // Delegate click on product cards
    document.addEventListener('click', (e) => {
        const card = e.target.closest('[data-product-slug]');
        if (card) {
            e.preventDefault();
            e.stopPropagation();
            const slug = card.dataset.productSlug;
            if (slug) loadProduct(slug);
        }
    });

    // Close on backdrop click
    document.addEventListener('click', (e) => {
        if (e.target.id === 'sf-modal-backdrop') closeModal();
    });

    // Close on X button
    document.addEventListener('click', (e) => {
        if (e.target.closest('.sf-modal-close')) closeModal();
    });

    // Close on Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeModal();
    });
}

// Expose close globally for inline handlers
window.__sfCloseModal = closeModal;

// Auto-init when DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}
