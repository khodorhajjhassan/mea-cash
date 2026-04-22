/**
 * MeaCash Product Modal — DailyCard-style purchase flow (Fallback/Legacy)
 *
 * NOTE: Kinetic Noir projects typically use the version in public/js/storefront/product-modal.js
 * This file is maintained for synchronization and fallback purposes.
 */

const currentLocale = () => (isRtl() ? 'ar' : 'en');
const localizedPath = (path) => `/${currentLocale()}${path}`;
const CART_ADD_URL = () => localizedPath('/cart/add');
const API_BASE = () => localizedPath('/api/product/');

let currentProduct = null;
let selectedPackageId = null;
let selectedFormKey = null;
let currentQuantity = 1;
let currentToast = '';

function getModal() { return document.getElementById('sf-product-modal'); }
function getBackdrop() { return document.getElementById('sf-modal-backdrop'); }
function getBody() { return document.getElementById('sf-modal-body'); }

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

function localized(item, key = 'name') {
    return item?.[`${key}_${isRtl() ? 'ar' : 'en'}`] || item?.[key] || item?.name || '';
}

function descriptionOf(item) {
    return localized(item, 'description');
}

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

async function loadProduct(slug) {
    const body = getBody();
    if (body) {
        body.innerHTML = `<div class="sf-modal-loading"><div class="animate-spin h-8 w-8 border-4 border-primary-container/20 border-t-primary-container rounded-full"></div></div>`;
    }
    openModal();

    try {
        const res = await fetch(API_BASE() + slug, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (!res.ok) throw new Error('Product not found');
        currentProduct = await res.json();

        if (currentProduct.packages?.length > 0) {
            selectedPackageId = currentProduct.packages[0].id;
        }
        if (currentProduct.forms?.length > 0) {
            const defaultForm = currentProduct.forms.find(f => f.is_default) || currentProduct.forms[0];
            selectedFormKey = defaultForm.key;
        }
        currentQuantity = currentProduct.min_quantity || 1;

        renderProduct();
    } catch (err) {
        if (body) {
            body.innerHTML = `<div class="sf-modal-error"><p>Failed to load product.</p></div>`;
        }
    }
}

function renderProduct() {
    // Basic rendering logic (simplified for synchronization)
    const p = currentProduct;
    const body = getBody();
    if (!body || !p) return;

    let html = `<div class="sf-modal-content">
        <h2 class="sf-modal-title">${escapeHtml(localized(p))}</h2>
        <div class="sf-modal-summary">
            <span class="sf-modal-total-price">${formatPrice(getCurrentPrice())}</span>
        </div>
        <div class="flex gap-3 mt-6">
            <button id="purchase-now-btn" class="flex-1 bg-primary-container text-on-primary-container py-3 rounded-xl font-bold uppercase transition-all hover:scale-[1.02]">
                ${isRtl() ? 'شراء الآن' : 'Purchase Now'}
            </button>
            <button id="share-btn" type="button" class="w-14 h-14 flex items-center justify-center border border-outline-variant/30 rounded-xl text-outline hover:border-secondary-container hover:text-secondary-container">
                                <span class="material-symbols-outlined">share</span>
            </button>
        </div>
        <div id="sf-modal-toast" class="mt-4"></div>
    </div>`;

    body.innerHTML = html;
    bindModalEvents();
}

function getCurrentPrice() {
    const p = currentProduct;
    if (!p) return 0;
    if (selectedPackageId && p.packages?.length) {
        const pkg = p.packages.find(pk => pk.id === selectedPackageId);
        return pkg ? pkg.selling_price : p.selling_price;
    }
    return p.selling_price;
}

function bindModalEvents() {
    document.getElementById('purchase-now-btn')?.addEventListener('click', handlePurchaseNow);
    document.getElementById('share-btn')?.addEventListener('click', handleShare);
}

async function handleShare() {
    // Note: The resource version usually shares the subcategory if it's a subcategory-based modal
    // but here we deal with currentProduct. For compatibility with the user's request:
    const slug = currentProduct?.slug || '';
    const url = window.location.origin + (isRtl() ? '/ar' : '/en') + '/subcategory/' + slug;
    const title = localized(currentProduct);
    const text = descriptionOf(currentProduct) || title;

    try {
        if (navigator.share) {
            await navigator.share({ title, text, url });
        } else {
            await navigator.clipboard.writeText(url);
            const toast = document.getElementById('sf-modal-toast');
            if (toast) {
                toast.innerHTML = `<div class="p-3 bg-primary-container/10 border border-primary-container/30 text-primary-container rounded-xl text-xs uppercase tracking-widest">${isRtl() ? 'تم نسخ الرابط!' : 'Link copied!'}</div>`;
            }
        }
    } catch (err) {
        if (err.name !== 'AbortError') console.error('Share failed:', err);
    }
}

async function handlePurchaseNow() {
    // Implementation for purchase now...
}

function init() {
    document.addEventListener('click', (e) => {
        const card = e.target.closest('[data-product-slug]');
        if (card) {
            e.preventDefault();
            const slug = card.dataset.productSlug;
            if (slug) loadProduct(slug);
        }
    });
}

document.addEventListener('DOMContentLoaded', init);
