/**
 * MeaCash Storefront Modal — Simplified Product Grid (No Packages)
 * 
 * Flow:
 * 1. Home card (Subcategory/Brand) clicked.
 * 2. Modal opens, fetches all Products for that Subcategory.
 * 3. Shows a grid of Products (Diamonds, UC, etc.).
 * 4. User selects a Product -> Details and Form fields update below.
 */

const CART_ADD_URL = '/cart/add';
const API_BASE = '/api/subcategory/';

/** @type {Object|null} */
let currentSubcategory = null;
/** @type {Object|null} */
let selectedProduct = null; 
let selectedFormKey = null;
let currentQuantity = 1;

// ─── DOM References ───
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

// ─── Modal State ───
function openModal() {
    const backdrop = getBackdrop();
    if (backdrop) backdrop.classList.add('sf-modal-active');
    document.body.style.overflow = 'hidden';
}
function closeModal() {
    const backdrop = getBackdrop();
    if (backdrop) backdrop.classList.remove('sf-modal-active');
    document.body.style.overflow = '';
    currentSubcategory = null;
    selectedProduct = null;
    selectedFormKey = null;
    currentQuantity = 1;
}

// ─── Loading / Error UI ───
function showLoading() {
    const body = getBody();
    if (!body) return;
    body.innerHTML = `
        <div class="sf-modal-loading p-8">
            <div class="sf-skeleton h-12 w-1/3 mb-6" style="border-radius:12px;"></div>
            <div class="grid grid-cols-4 gap-4 mb-8">
                <div class="sf-skeleton aspect-square rounded-2xl"></div>
                <div class="sf-skeleton aspect-square rounded-2xl"></div>
                <div class="sf-skeleton aspect-square rounded-2xl"></div>
                <div class="sf-skeleton aspect-square rounded-2xl"></div>
            </div>
            <div class="sf-skeleton h-32 rounded-2xl mb-4"></div>
            <div class="sf-skeleton h-14 rounded-2xl"></div>
        </div>
    `;
}

// ─── Fetch Data ───
async function loadSubcategory(slug) {
    showLoading();
    openModal();

    try {
        const res = await fetch(API_BASE + slug, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (!res.ok) throw new Error('Subcategory not found');
        currentSubcategory = await res.json();

        // Default selection: First featured product, or just first product
        const defaultProduct = currentSubcategory.products.find(p => p.is_featured) || currentSubcategory.products[0];
        selectProduct(defaultProduct);
        render();
    } catch (err) {
        console.error(err);
        const body = getBody();
        if (body) body.innerHTML = `<div class="p-8 text-center"><p class="text-white/60 mb-4">Failed to load brand data.</p><button onclick="window.__sfCloseModal()" class="sf-btn-outline">Close</button></div>`;
    }
}

function selectProduct(product) {
    selectedProduct = product;
    if (!product) return;
    
    // Set default form
    if (product.forms?.length > 0) {
        const defaultForm = product.forms.find(f => f.is_default) || product.forms[0];
        selectedFormKey = defaultForm.key;
    } else {
        selectedFormKey = null;
    }
    
    currentQuantity = product.min_quantity || 1;
}

// ─── Main Render ───
function render() {
    const s = currentSubcategory;
    const body = getBody();
    if (!body || !s) return;

    let html = '';

    // 1. Header
    html += `
        <div class="sf-modal-grid-header">
            <div class="sf-modal-grid-title">${escapeHtml(s.name)}</div>
            <button type="button" class="sf-modal-close text-slate-400 hover:text-white transition-colors" onclick="window.__sfCloseModal()">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    `;

    // 2. Body (Scrollable)
    html += `<div class="sf-modal-grid-body">`;
    
    // Grid Section
    html += `<div class="sf-item-grid">`;
    s.products.forEach(p => {
        const isSelected = selectedProduct && selectedProduct.id === p.id;
        html += renderProductCard(p, isSelected);
    });
    html += `</div>`; // End grid

    // Detail Section (Forms)
    if (selectedProduct) {
        html += renderDetails(selectedProduct);
    }

    html += `</div>`; // End total body

    // 3. Footer (Fixed)
    html += `
        <div class="sf-modal-grid-footer">
            <div class="flex gap-4">
                <button type="button" class="sf-btn-grid-share" id="sf-btn-share">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"></path><polyline points="16 6 12 2 8 6"></polyline><line x1="12" y1="2" x2="12" y2="15"></line></svg>
                </button>
                <button type="button" id="sf-modal-add-btn" class="sf-btn-grid-purchase">
                    <span>${isRtl() ? 'تأكيد الشراء' : 'Purchase Now'}</span>
                    <span class="text-white/40 font-normal">|</span>
                    <span id="sf-footer-total">${formatPrice(calcCurrentPrice())}</span>
                </button>
            </div>
        </div>
    `;

    body.innerHTML = html;
    bindEvents();
}

function renderProductCard(p, isSelected) {
    const image = p.image || currentSubcategory.image;
    const isFeatured = p.is_featured;
    const isInstant = p.delivery_type === 'instant';
    const isCustom = p.product_type === 'custom_quantity';

    return `
        <div class="sf-grid-item ${isSelected ? 'selected' : ''}" data-product-id="${p.id}">
            <div class="sf-grid-check">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div class="sf-grid-badge ${isFeatured ? 'badge-hot' : (isInstant ? 'badge-fast' : (isCustom ? 'badge-custom' : ''))}">
               ${isFeatured ? `
                   <div class="badge-inner">
                      <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                      ${isRtl() ? 'مميز' : 'HOT'}
                   </div>
               ` : (isInstant ? `
                   <div class="badge-inner">
                      <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                      ${isRtl() ? 'سريع' : 'FAST'}
                   </div>
               ` : (isCustom ? `
                   <div class="badge-inner">
                      <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                      ${isRtl() ? 'مخصص' : 'SELECT'}
                   </div>
               ` : ''))}
            </div>
            <div class="sf-grid-img-wrap">
                <img src="${escapeHtml(image)}" alt="">
            </div>
            <div class="sf-grid-item-name">${escapeHtml(p.name)}</div>
            <div class="sf-grid-item-price">${formatPrice(p.selling_price)}</div>
        </div>
    `;
}

function renderDetails(p) {
    const type = p.product_type;
    const hasFormTabs = p.forms?.length > 0;
    const hasMultiForms = p.forms?.length > 1;

    let html = `<div class="sf-grid-form-wrap animate-fade-in">`;
    
    // Selection Summary Card (New)
    html += `
        <div class="sf-selection-summary">
            <div class="sf-summary-img-wrap">
                <img src="${escapeHtml(p.image || currentSubcategory.image)}" alt="">
            </div>
            <div class="sf-summary-info">
                <div class="sf-summary-name">${escapeHtml(p.name)}</div>
                <div class="sf-summary-price">${formatPrice(calcCurrentPrice())}</div>
            </div>
        </div>
    `;

    // Header label
    html += `<div class="text-[0.65rem] text-blue-400 font-bold uppercase tracking-widest mb-4">${isRtl() ? 'تعبئة البيانات' : 'FILL THE DATA'}</div>`;

    // Tabs
    if (hasMultiForms) {
        html += `<div class="flex gap-2 mb-4 bg-black/20 p-1 rounded-xl">`;
        p.forms.forEach(form => {
            const active = form.key === selectedFormKey;
            html += `<button type="button" class="sf-form-tab-mini flex-1 py-2 rounded-lg text-xs font-bold transition-all ${active ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-500 hover:text-slate-300'}" data-form-key="${escapeHtml(form.key)}">${escapeHtml(form.label)}</button>`;
        });
        html += `</div>`;
    }

    // Custom Quantity
    if (type === 'custom_quantity') {
        const min = p.min_quantity || 1;
        html += `
            <div class="mb-4 bg-black/20 rounded-xl p-4 border border-white/5">
               <label class="block text-xs text-slate-500 font-bold mb-2 uppercase">${isRtl() ? 'الكمية' : 'Quantity'}</label>
               <input type="number" id="sf-qty-input" class="w-full bg-transparent text-2xl font-black text-white outline-none border-b-2 border-blue-500 pb-1" value="${currentQuantity}" min="${min}">
            </div>
        `;
    }

    // Form Fields
    html += `<div id="sf-grid-fields-container">`;
    html += renderFields(p.fields);
    if (hasFormTabs) {
        const activeForm = p.forms.find(f => f.key === selectedFormKey) || p.forms[0];
        html += renderFields(activeForm.fields);
    }
    html += `</div>`;

    html += `</div>`;
    return html;
}

function renderFields(fields) {
    if (!fields) return '';
    let html = '';
    fields.forEach(f => {
        html += `
            <div class="mb-4">
                <label class="block text-[0.65rem] text-slate-500 font-bold mb-1.5 uppercase tracking-wider">${escapeHtml(f.label)} ${f.required ? '<span class="text-red-500">*</span>' : ''}</label>
                <input type="${f.type || 'text'}" name="form_data[${escapeHtml(f.key)}]" id="sf-field-${escapeHtml(f.key)}" placeholder="${escapeHtml(f.placeholder)}" class="w-full bg-slate-900/50 border border-white/5 rounded-xl p-3.5 text-sm text-white focus:border-blue-500/50 outline-none transition-all">
                <div class="text-[0.6rem] text-red-500 mt-1 hidden" id="sf-err-${escapeHtml(f.key)}"></div>
            </div>
        `;
    });
    return html;
}

function calcCurrentPrice() {
    if (!selectedProduct) return 0;
    if (selectedProduct.product_type === 'custom_quantity') {
        return currentQuantity * (selectedProduct.price_per_unit || selectedProduct.selling_price);
    }
    return selectedProduct.selling_price || 0;
}

function bindEvents() {
    // Grid Item selection
    document.querySelectorAll('.sf-grid-item').forEach(el => {
        el.addEventListener('click', () => {
            const productId = parseInt(el.dataset.productId);
            const found = currentSubcategory.products.find(p => p.id === productId);
            selectProduct(found);
            render();
        });
    });

    // Form Tab switching
    document.querySelectorAll('.sf-form-tab-mini').forEach(tab => {
        tab.addEventListener('click', () => {
            selectedFormKey = tab.dataset.formKey;
            render();
        });
    });

    // Quantity update
    const qtyInput = document.getElementById('sf-qty-input');
    if (qtyInput) {
        qtyInput.addEventListener('input', () => {
            currentQuantity = parseInt(qtyInput.value) || 1;
            document.getElementById('sf-footer-total').textContent = formatPrice(calcCurrentPrice());
        });
    }

    // Purchase hook
    const buyBtn = document.getElementById('sf-modal-add-btn');
    if (buyBtn) buyBtn.addEventListener('click', handlePurchase);
}

async function handlePurchase() {
    if (!selectedProduct) return;
    const btn = document.getElementById('sf-modal-add-btn');
    
    const fields = document.querySelectorAll('[name^="form_data"]');
    const formData = {};
    fields.forEach(f => {
        const key = f.name.match(/\[(.*?)\]/)[1];
        formData[key] = f.value;
    });

    const payload = {
        product_id: selectedProduct.id,
        package_id: null,
        quantity: selectedProduct.product_type === 'custom_quantity' ? currentQuantity : 1,
        form_data: formData,
        selected_form: selectedFormKey
    };

    btn.disabled = true;
    btn.innerHTML = `<span>Adding...</span>`;

    try {
        const res = await fetch(CART_ADD_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(payload)
        });
        if (res.status === 401) return window.location.href = '/login';
        
        const data = await res.json();
        if (res.ok) {
            document.querySelectorAll('.sf-cart-badge').forEach(el => {
                el.textContent = data.count;
                el.style.display = 'flex';
            });
            closeModal();
        }
    } catch (e) {
        console.error(e);
    } finally {
        btn.disabled = false;
        render();
    }
}

// ─── Global Initializers ───
function init() {
    document.addEventListener('click', e => {
        const card = e.target.closest('[data-subcategory-slug]');
        if (card) {
            e.preventDefault();
            loadSubcategory(card.dataset.subcategorySlug);
        }
    });

    document.addEventListener('click', e => { if (e.target.id === 'sf-modal-backdrop') closeModal(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
}

window.__sfCloseModal = closeModal;
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}
