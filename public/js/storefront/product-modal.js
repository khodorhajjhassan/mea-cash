/**
 * MeaCash Kinetic Noir product modal.
 * Renders real subcategory/product/package data and keeps purchase payloads aligned
 * with the Laravel validation rules.
 */

const API_BASE = '/api/subcategory/';
const PURCHASE_URL = '/cart/add';
const CHECKOUT_URL = '/checkout';

let currentSubcategory = null;
let selectedProduct = null;
let selectedPackage = null;
let selectedFormKey = null;
let currentQuantity = 1;
let currentToast = '';
let previousBodyOverflow = '';
let previousBodyOverflowX = '';

const getBackdrop = () => document.getElementById('sf-modal-backdrop');
const getHeaderContent = () => document.getElementById('sf-modal-header-content');
const getBody = () => document.getElementById('sf-modal-body');
const getSummary = () => document.getElementById('sf-modal-summary-content');
const getFooter = () => document.getElementById('sf-modal-footer');
const isRtl = () => document.documentElement.dir === 'rtl';
const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';
const isAuthenticated = () => Boolean(window.isAuthenticated);
const currentLocale = () => (isRtl() ? 'ar' : 'en');
const shareableSubcategoryUrl = () => {
    const url = new URL(`/${currentLocale()}`, window.location.origin);
    if (currentSubcategory?.slug) url.searchParams.set('subcategory', currentSubcategory.slug);
    if (selectedProduct?.id) url.searchParams.set('product', selectedProduct.id);
    return url.toString();
};

const money = (value) => `$${Number(value || 0).toFixed(2)}`;
const localized = (item, key = 'name') => item?.[`${key}_${isRtl() ? 'ar' : 'en'}`] || item?.[key] || item?.name || '';
const imageUrl = (item, fallback = '/meacash-logo.png') => item?.image || fallback;
const descriptionOf = (item) => localized(item, 'description');
const selectedImage = () => selectedPackage?.image || selectedProduct?.image || currentSubcategory?.image || '/meacash-logo.png';
const friendlyType = (product) => {
    const delivery = product?.delivery_type ? String(product.delivery_type).replace(/_/g, ' ') : '';
    if (delivery) return delivery;

    const type = product?.product_type ? String(product.product_type).replace(/_/g, ' ') : '';
    return type === 'fixed package' ? 'Digital asset' : (type || 'Digital asset');
};
const compactNumber = (value) => {
    const number = Number(value);
    if (!Number.isFinite(number) || number <= 0) return '';
    return number % 1 === 0 ? String(number) : number.toFixed(2).replace(/\.?0+$/, '');
};

function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, (char) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;',
    }[char]));
}

function selectedUnitPrice() {
    if (!selectedProduct) return 0;

    if (selectedPackage) return Number(selectedPackage.selling_price || 0);

    if (selectedProduct.product_type === 'custom_quantity') {
        return Number(selectedProduct.price_per_unit || selectedProduct.selling_price || 0) * currentQuantity;
    }

    return Number(selectedProduct.selling_price || 0);
}

function selectableItems() {
    if (!currentSubcategory?.products) return [];

    return currentSubcategory.products.flatMap((product) => {
        if (product.product_type === 'fixed_package' && product.packages?.length) {
            return product.packages.map((pack) => ({
                key: `${product.id}:${pack.id}`,
                product,
                package: pack,
                title: localized(pack),
                subtitle: friendlyType(product),
                price: pack.selling_price,
                image: pack.image || product.image || currentSubcategory.image,
                badge: pack.badge_text || (product.is_featured ? 'HOT' : ''),
            }));
        }

        return [{
            key: `${product.id}:product`,
            product,
            package: null,
            title: localized(product),
            subtitle: friendlyType(product),
            price: product.selling_price,
            image: product.image || currentSubcategory.image,
            badge: product.is_featured ? 'HOT' : product.delivery_type?.toUpperCase(),
        }];
    });
}

function syncProductDefaults(product) {
    selectedProduct = product;
    selectedPackage = null;
    selectedFormKey = (product.forms?.find((form) => form.is_default) || product.forms?.[0])?.key || null;
    currentQuantity = Number(product.min_quantity || 1);
}

async function openSubcategoryModal(slug, productId = null) {
    const backdrop = getBackdrop();
    if (!backdrop) return;

    backdrop.classList.remove('hidden');
    backdrop.classList.add('flex');
    previousBodyOverflow = document.body.style.overflow;
    previousBodyOverflowX = document.body.style.overflowX;
    document.body.style.overflow = 'hidden';
    document.body.style.overflowX = 'hidden';
    await loadSubcategory(slug, productId);
}

function closeProductModal() {
    const backdrop = getBackdrop();
    if (!backdrop) return;

    backdrop.classList.add('hidden');
    backdrop.classList.remove('flex');
    document.body.style.overflow = previousBodyOverflow;
    document.body.style.overflowX = previousBodyOverflowX;

    currentSubcategory = null;
    selectedProduct = null;
    selectedPackage = null;
    selectedFormKey = null;
    currentQuantity = 1;
    currentToast = '';
}

window.openSubcategoryModal = openSubcategoryModal;
window.closeProductModal = closeProductModal;

async function loadSubcategory(slug, productId = null) {
    const body = getBody();
    const summary = getSummary();
    const footer = getFooter();

    if (body) {
        body.innerHTML = `<div class="flex flex-col items-center justify-center py-20">
            <div class="h-16 w-16 animate-spin rounded-full border-4 border-primary-container/20 border-t-primary-container"></div>
            <p class="mt-4 font-label text-xs uppercase tracking-widest text-outline">Initializing Vault...</p>
        </div>`;
    }
    if (summary) summary.innerHTML = '';
    if (footer) footer.innerHTML = '';

    try {
        const res = await fetch(API_BASE + encodeURIComponent(slug), {
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        });

        if (!res.ok) throw new Error('Subcategory not found');

        currentSubcategory = await res.json();
        const requestedProductId = Number(productId || 0);
        const firstProduct = currentSubcategory.products?.find((product) => Number(product.id) === requestedProductId)
            || currentSubcategory.products?.find((product) => product.is_featured)
            || currentSubcategory.products?.[0]
            || null;

        if (firstProduct) {
            syncProductDefaults(firstProduct);
            if (firstProduct.product_type === 'fixed_package') {
                selectedPackage = firstProduct.packages?.[0] || null;
            }
        }

        render();
    } catch (error) {
        console.error('Modal Load Error:', error);
        if (body) {
            body.innerHTML = `<div class="rounded-3xl border border-error/25 bg-error-container/10 p-10 text-center text-error">
                Failed to load asset data. Please try again.
            </div>`;
        }
    }
}

function render() {
    renderHeader();
    renderBody();
    renderSummary();
    renderFooter();
    bindEvents();
}

function renderHeader() {
    const header = getHeaderContent();
    if (!header || !currentSubcategory) return;

    const name = localized(currentSubcategory);
    const categoryName = currentSubcategory.category?.name || currentSubcategory.category_name || 'Digital Assets';
    const subcategoryDescription = descriptionOf(currentSubcategory);

    header.innerHTML = `
        <div class="h-11 w-1.5 shrink-0 rounded-full bg-primary-container shadow-[0_0_35px_rgba(0,240,255,0.35)]"></div>
        <div class="min-w-0">
            <h1 class="truncate font-headline text-2xl font-black uppercase leading-none tracking-tighter md:text-3xl">
                ${escapeHtml(name)} <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-container to-secondary-container">Vault</span>
            </h1>
            <p class="mt-2 font-label text-[10px] uppercase tracking-[0.24em] text-outline">
                ${escapeHtml(categoryName)} / ${escapeHtml(currentSubcategory.products?.length || 0)} assets
            </p>
            ${subcategoryDescription ? `<p class="mt-2 line-clamp-2 max-w-xl text-xs leading-relaxed text-on-surface-variant/70">${escapeHtml(subcategoryDescription)}</p>` : ''}
        </div>
    `;
}

function renderBody() {
    const body = getBody();
    if (!body || !currentSubcategory) return;

    const items = selectableItems();

    if (!items.length) {
        body.innerHTML = `<div class="rounded-3xl border border-outline-variant/15 bg-surface-container-low/60 p-10 text-center text-on-surface-variant">
            No products are available in this vault yet.
        </div>`;
        return;
    }

    body.innerHTML = `
        <div class="grid grid-cols-3 gap-2 sm:grid-cols-2 sm:gap-3 lg:grid-cols-3">
            ${items.map(renderSelectionCard).join('')}
        </div>
    `;
}

function renderSelectionCard(item) {
    const active = selectedProduct?.id === item.product.id && ((selectedPackage?.id || null) === (item.package?.id || null));
    const badge = item.badge ? `<div class="absolute top-2 ${isRtl() ? 'left-2' : 'right-2'} rounded-full bg-secondary-container px-2 py-0.5 font-label text-[8px] font-black uppercase tracking-tight text-on-secondary-container">${escapeHtml(item.badge)}</div>` : '';

    return `
        <button type="button" data-select-product="${item.product.id}" data-select-package="${item.package?.id || ''}"
            class="group relative flex min-h-[132px] flex-col rounded-xl border p-2 text-start transition-all duration-300 sm:min-h-[178px] sm:rounded-2xl sm:p-3 ${active ? 'border-primary-container bg-surface-container-high shadow-[0_0_22px_rgba(0,240,255,0.2)] ring-1 ring-primary-container/70' : 'border-transparent bg-surface-container-low hover:-translate-y-1 hover:border-primary-container/30 hover:bg-surface-container-high'}">
            ${badge}
            ${active ? `<span class="material-symbols-outlined absolute top-2 ${isRtl() ? 'left-2' : 'right-2'} text-lg text-primary-container" style="font-variation-settings: 'FILL' 1;">check_circle</span>` : ''}
            <div class="mb-2 flex h-12 items-center justify-center rounded-xl bg-surface-container-lowest/60 p-1.5 sm:mb-3 sm:h-16 sm:p-2">
                <img src="${escapeHtml(imageUrl({ image: item.image }))}" alt="${escapeHtml(item.title)}" class="h-full w-full object-contain" loading="lazy" decoding="async" onerror="this.src='/meacash-logo.png'">
            </div>
            <div class="line-clamp-2 min-h-[30px] font-headline text-[10px] font-black uppercase leading-tight text-on-surface sm:min-h-[42px] sm:text-[13px]">${escapeHtml(item.title)}</div>
            <div class="mt-1 truncate font-label text-[8px] font-bold uppercase tracking-widest text-outline sm:mt-2 sm:text-[9px]">${escapeHtml(item.subtitle)}</div>
            <div class="mt-auto pt-2 font-headline text-sm font-black text-primary-container sm:pt-3 sm:text-base">${money(item.price)}</div>
        </button>
    `;
}

function renderSummary() {
    const summary = getSummary();
    if (!summary || !selectedProduct) return;

    const title = selectedPackage ? localized(selectedPackage) : localized(selectedProduct);
    const subtitle = descriptionOf(selectedProduct) || descriptionOf(currentSubcategory) || localized(selectedProduct);
    const type = friendlyType(selectedProduct);
    const activeForm = selectedProduct.forms?.find((form) => form.key === selectedFormKey) || selectedProduct.forms?.[0] || null;
    const fields = [
        ...(selectedProduct.fields || []),
        ...(activeForm?.fields || []),
    ];

    summary.innerHTML = `
        <div>
            <h2 class="mb-4 font-label text-xs font-bold uppercase tracking-widest text-outline">Selected Product</h2>
            <div class="mb-5 flex flex-wrap items-center gap-3 rounded-2xl border border-outline-variant/10 bg-surface-container-highest/50 p-3 sm:flex-nowrap">
                <div class="h-16 w-16 shrink-0 overflow-hidden rounded-xl border border-outline-variant/20 bg-surface">
                    <img src="${escapeHtml(selectedImage())}" alt="${escapeHtml(title)}" class="h-full w-full object-contain p-2" loading="lazy" decoding="async" onerror="this.src='/meacash-logo.png'">
                </div>
                <div class="min-w-0 flex-1">
                    <div class="font-headline text-sm font-black uppercase leading-tight text-on-surface">${escapeHtml(title)}</div>
                    <div class="mt-1 font-label text-[9px] uppercase tracking-widest text-primary-container">${escapeHtml(type)}</div>
                </div>
                <div class="w-full text-start sm:w-auto sm:text-end">
                    <div id="modal-live-price" class="font-headline text-base font-black text-primary-container">${money(selectedUnitPrice())}</div>
                    <div class="font-label text-[10px] uppercase tracking-tight text-outline">Total Price</div>
                </div>
            </div>
            ${subtitle ? `<div class="mb-5 rounded-2xl border border-outline-variant/10 bg-surface-container-lowest/35 p-4 text-xs leading-relaxed text-on-surface-variant">${escapeHtml(subtitle)}</div>` : ''}

            ${renderFormTabs()}
            ${renderQuantity()}
            <div class="space-y-4">${fields.map(renderField).join('')}</div>
            ${currentToast}
        </div>
    `;
}

function renderFormTabs() {
    if (!selectedProduct?.forms?.length || selectedProduct.forms.length < 2) return '';

    return `
        <div class="mb-5 grid gap-2 rounded-2xl border border-outline-variant/10 bg-surface-container-lowest/40 p-1" style="grid-template-columns: repeat(${Math.min(selectedProduct.forms.length, 3)}, minmax(0, 1fr));">
            ${selectedProduct.forms.map((form) => `
                <button type="button" data-form-key="${escapeHtml(form.key)}" class="rounded-xl px-3 py-2.5 font-label text-[9px] font-black uppercase tracking-widest transition-all ${form.key === selectedFormKey ? 'bg-primary-container text-on-primary-container' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface'}">
                    ${escapeHtml(form.label)}
                </button>
            `).join('')}
        </div>
    `;
}

function renderQuantity() {
    if (selectedProduct?.product_type !== 'custom_quantity') return '';

    return `
        <div class="group mb-5">
            <div class="mb-2 flex items-center justify-between px-1">
                <label class="font-label text-[10px] font-bold uppercase tracking-widest text-outline">Custom Quantity</label>
                <div class="font-label text-[9px] uppercase tracking-widest text-primary-container">
                    Min: ${Number(selectedProduct.min_quantity || 1)} / Max: ${selectedProduct.max_quantity ? Number(selectedProduct.max_quantity) : '∞'}
                </div>
            </div>
            <div class="relative">
                <input id="qty-input" type="number" min="${selectedProduct.min_quantity || 1}" max="${selectedProduct.max_quantity || ''}" value="${currentQuantity}"
                    class="w-full rounded-xl border-0 bg-surface-container-lowest px-4 py-3 font-headline text-lg font-black text-secondary-container placeholder:text-outline-variant outline-none">
                <div class="absolute bottom-0 start-0 h-[2px] w-0 bg-gradient-to-r from-primary-container to-secondary-container transition-all duration-500 group-focus-within:w-full"></div>
            </div>
            <div id="err-quantity" class="mt-2 px-1 font-label text-[10px] uppercase tracking-widest text-secondary-container hidden"></div>
            <p class="mt-2 px-1 font-label text-[10px] uppercase tracking-widest text-outline">Rate: ${money(selectedProduct.price_per_unit || selectedProduct.selling_price)} each</p>
        </div>
    `;
}

function renderField(field) {
    const label = `${escapeHtml(field.label)}${field.required ? ' <span class="text-secondary-container">*</span>' : ''}`;

    if (field.type === 'select') {
        const options = (field.options || []).map((option) => {
            const value = typeof option === 'object' ? (option.value ?? option.label) : option;
            const text = typeof option === 'object' ? (option.label ?? option.value) : option;
            return `<option value="${escapeHtml(value)}">${escapeHtml(text)}</option>`;
        }).join('');

        return `
            <div class="group">
                <label class="mb-2 ms-1 block font-label text-[10px] font-bold uppercase tracking-widest text-outline">${label}</label>
                <select name="form_data[${escapeHtml(field.key)}]" class="w-full rounded-xl border-0 bg-surface-container-lowest px-4 py-3 text-sm text-on-surface outline-none">
                    <option value="">${escapeHtml(field.placeholder || 'Select option')}</option>
                    ${options}
                </select>
                <div id="err-${escapeHtml(field.key)}" class="mt-1 hidden font-label text-[10px] uppercase tracking-widest text-secondary-container"></div>
            </div>
        `;
    }

    return `
        <div class="group">
            <label class="mb-2 ms-1 block font-label text-[10px] font-bold uppercase tracking-widest text-outline">${label}</label>
            <div class="relative">
                <input type="${escapeHtml(field.type || 'text')}" name="form_data[${escapeHtml(field.key)}]" placeholder="${escapeHtml(field.placeholder || '')}"
                    class="w-full rounded-xl border-0 bg-surface-container-lowest px-4 py-3 text-sm text-on-surface placeholder:text-outline-variant outline-none">
                <div class="absolute bottom-0 start-0 h-[2px] w-0 bg-gradient-to-r from-primary-container to-secondary-container transition-all duration-500 group-focus-within:w-full"></div>
            </div>
            <div id="err-${escapeHtml(field.key)}" class="mt-1 hidden font-label text-[10px] uppercase tracking-widest text-secondary-container"></div>
        </div>
    `;
}

function renderFooter() {
    const footer = getFooter();
    if (!footer || !selectedProduct) return;

    const shareBtn = `
        <button id="share-btn" type="button" class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl border border-outline-variant/30 bg-surface-container-lowest/50 text-outline transition-all hover:border-secondary-container hover:bg-secondary-container/10 hover:text-secondary-container">
            <span class="material-symbols-outlined text-xl">share</span>
        </button>
    `;

    if (!isAuthenticated()) {
        footer.innerHTML = `
            <div class="flex gap-3">
                <a href="/auth/login" class="flex flex-1 items-center justify-center gap-3 rounded-2xl border border-primary-container/30 bg-surface-container-high py-4 font-headline text-sm font-black uppercase tracking-[0.22em] text-primary-container shadow-[0_0_28px_rgba(0,240,255,0.12)] transition-all hover:border-primary-container hover:bg-primary-container hover:text-on-primary-container">
                    <span class="material-symbols-outlined text-lg">lock</span>
                    <span>${isRtl() ? 'سجل الدخول أولاً' : 'Login First'}</span>
                </a>
                ${shareBtn}
            </div>
            <p class="mt-3 text-center font-label text-[10px] uppercase tracking-widest text-outline">
                ${isRtl() ? 'يجب تسجيل الدخول لإكمال الشراء' : 'Please login to purchase this product'}
            </p>
        `;
        return;
    }

    footer.innerHTML = `
        <div class="flex gap-3">
            <button id="purchase-now-btn" type="button" class="flex flex-1 items-center justify-center gap-3 rounded-2xl bg-gradient-to-r from-primary-fixed to-secondary-fixed-dim py-4 font-headline text-sm font-black uppercase tracking-[0.22em] text-on-primary-fixed shadow-[0_0_35px_rgba(0,240,255,0.22)] transition-all hover:scale-[1.02] active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-60">
                <span>${isRtl() ? 'شراء الآن' : 'Purchase Now'}</span>
                <span class="material-symbols-outlined">bolt</span>
            </button>
            ${shareBtn}
        </div>
    `;
}

function bindEvents() {
    document.querySelectorAll('[data-select-product]').forEach((button) => {
        button.addEventListener('click', () => {
            const productId = Number(button.dataset.selectProduct);
            const packageId = button.dataset.selectPackage ? Number(button.dataset.selectPackage) : null;
            const product = currentSubcategory.products.find((item) => Number(item.id) === productId);
            if (!product) return;

            syncProductDefaults(product);
            selectedPackage = packageId ? product.packages?.find((item) => Number(item.id) === packageId) || null : null;
            currentToast = '';
            render();
        });
    });

    document.querySelectorAll('[data-form-key]').forEach((button) => {
        button.addEventListener('click', () => {
            selectedFormKey = button.dataset.formKey;
            currentToast = '';
            renderSummary();
            renderFooter();
            bindEvents();
        });
    });

    const quantityInput = document.getElementById('qty-input');
    if (quantityInput) {
        quantityInput.addEventListener('input', (event) => {
            const min = Number(selectedProduct.min_quantity || 1);
            const max = Number(selectedProduct.max_quantity || Number.MAX_SAFE_INTEGER);
            const next = Number(event.target.value || min);
            currentQuantity = Math.max(min, Math.min(max, next));
            const livePrice = document.getElementById('modal-live-price');
            if (livePrice) livePrice.textContent = money(selectedUnitPrice());
        });
    }

    document.getElementById('purchase-now-btn')?.addEventListener('click', handlePurchaseNow);
    document.getElementById('share-btn')?.addEventListener('click', handleShare);
}

async function handleShare() {
    if (!currentSubcategory) return;

    const url = shareableSubcategoryUrl();
    const title = localized(currentSubcategory);
    const text = descriptionOf(currentSubcategory) || title;

    try {
        if (navigator.share) {
            await navigator.share({ title, text, url });
        } else {
            await navigator.clipboard.writeText(url);
            currentToast = `<div class="mt-5 rounded-xl border border-primary-container/30 bg-primary-container/10 p-3 font-label text-xs uppercase tracking-widest text-primary-container">${isRtl() ? 'تم نسخ الرابط إلى الحافظة!' : 'Link copied to clipboard!'}</div>`;
            renderSummary();
            renderFooter();
            bindEvents();
        }
    } catch (err) {
        if (err.name !== 'AbortError') {
            console.error('Share failed:', err);
        }
    }
}

function clearErrors() {
    document.querySelectorAll('[id^="err-"]').forEach((el) => {
        el.textContent = '';
        el.classList.add('hidden');
    });
}

function showErrors(errors) {
    Object.entries(errors || {}).forEach(([key, messages]) => {
        // Handle "form_data.field_key" and flat "quantity" or other keys
        const fieldKey = key.startsWith('form_data.') ? key.replace('form_data.', '') : key;
        const el = document.getElementById(`err-${fieldKey}`);
        if (el) {
            el.textContent = messages[0] || 'Invalid value';
            el.classList.remove('hidden');
        }
    });
}

async function handlePurchaseNow() {
    if (!selectedProduct) return;

    const button = document.getElementById('purchase-now-btn');
    const formData = {};

    clearErrors();
    document.querySelectorAll('[name^="form_data"]').forEach((input) => {
        const match = input.name.match(/\[(.*?)\]/);
        if (match) formData[match[1]] = input.value;
    });

    const payload = {
        product_id: selectedProduct.id,
        package_id: selectedPackage?.id || null,
        quantity: selectedProduct.product_type === 'custom_quantity' ? currentQuantity : 1,
        form_data: formData,
        selected_form: selectedFormKey,
        buy_now: true,
    };

    button.disabled = true;
    button.innerHTML = `<span class="animate-pulse">${isRtl() ? 'جاري المعالجة...' : 'Processing...'}</span>`;

    try {
        const res = await fetch(PURCHASE_URL, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(payload),
        });

        if (res.status === 401) {
            window.location.href = '/auth/login';
            return;
        }

        const data = await res.json();

        if (!res.ok) {
            currentToast = `<div class="mt-5 rounded-xl border border-secondary-container/30 bg-secondary-container/10 p-3 font-label text-xs uppercase tracking-widest text-secondary-container">${escapeHtml(data.message || 'Please review the highlighted fields.')}</div>`;
            renderSummary();
            renderFooter();
            bindEvents();
            showErrors(data.errors || {});
            return;
        }

        button.innerHTML = `<span class="animate-pulse">${isRtl() ? 'جاري تأكيد الطلب...' : 'Confirming order...'}</span>`;

        const checkoutRes = await fetch(CHECKOUT_URL, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
                'X-Requested-With': 'XMLHttpRequest',
            },
        });
        const checkoutData = await checkoutRes.json();

        if (!checkoutRes.ok) {
            if (checkoutData.redirect_url) {
                window.location.href = checkoutData.redirect_url;
                return;
            }

            currentToast = `<div class="mt-5 rounded-xl border border-secondary-container/30 bg-secondary-container/10 p-3 font-label text-xs uppercase tracking-widest text-secondary-container">${escapeHtml(checkoutData.message || 'Could not complete purchase.')}</div>`;
            renderSummary();
            renderFooter();
            bindEvents();
            return;
        }

        window.location.href = checkoutData.redirect_url || data.redirect_url || '/checkout';
    } catch (error) {
        console.error('Purchase Error:', error);
        currentToast = `<div class="mt-5 rounded-xl border border-error/30 bg-error-container/10 p-3 font-label text-xs uppercase tracking-widest text-error">Could not start purchase. Please try again.</div>`;
        renderSummary();
    } finally {
        if (button) {
            button.disabled = false;
            renderFooter();
            bindEvents();
        }
    }
}

document.addEventListener('click', (event) => {
    if (event.target.id === 'sf-modal-backdrop') closeProductModal();
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') closeProductModal();
});
