/**
 * MeaCash Kinetic Noir product modal.
 * Renders real subcategory/product/package data and keeps purchase payloads aligned
 * with the Laravel validation rules.
 */

const localizedPath = (path) => `/${currentLocale()}${path}`;
const apiPath = (path) => localizedPath(path);
const API_BASE = () => apiPath('/api/subcategory/');
const PURCHASE_URL = () => localizedPath('/cart/add');
const CHECKOUT_URL = () => localizedPath('/checkout');
const LOGIN_URL = () => localizedPath('/auth/login');

let currentSubcategory = null;
let selectedProduct = null;
let selectedPackage = null;
let selectedFormKey = null;
let currentQuantity = 1;
let currentToast = '';
let previousBodyOverflow = '';
let previousBodyOverflowX = '';
let expandedContent = {
    description: false,
    redeem: false,
};

const getBackdrop = () => document.getElementById('sf-modal-backdrop');
const getHeaderContent = () => document.getElementById('sf-modal-header-content');
const getBody = () => document.getElementById('sf-modal-body');
const getSummary = () => document.getElementById('sf-modal-summary-content');
const getFooter = () => document.getElementById('sf-modal-footer');
const currentLocale = () => document.documentElement.lang || 'en';
const isRtl = () => document.documentElement.dir === 'rtl' || currentLocale() === 'ar';

const __ = (key, replacements = {}) => {
    const locale = currentLocale();
    const messages = {
        en: {
            min: 'Min: :val',
            max: 'Max: :val',
            required: 'Required',
            processing: 'Processing...',
            link_copied: 'Link copied to clipboard!',
            review_errors: 'Please review the highlighted fields.',
            invalid_value: 'Invalid value',
            select_option: 'Select option',
            custom_quantity: 'Custom Quantity',
            total_price: 'Total Price',
            selected_product: 'Selected Product'
        },
        ar: {
            min: 'الحد الأدنى: :val',
            max: 'الحد الأقصى: :val',
            required: 'مطلوب',
            processing: 'جاري المعالجة...',
            link_copied: 'تم نسخ الرابط إلى الحافظة!',
            review_errors: 'يرجى مراجعة الحقول المميزة.',
            invalid_value: 'قيمة غير صالحة',
            select_option: 'اختر خياراً',
            custom_quantity: 'كمية مخصصة',
            total_price: 'السعر الإجمالي',
            selected_product: 'المنتج المختار'
        }
    };

    let msg = messages[locale]?.[key] || messages['en'][key] || key;
    Object.entries(replacements).forEach(([k, v]) => {
        msg = msg.replace(`:${k}`, v);
    });
    return msg;
};

const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';
const isAuthenticated = () => Boolean(window.isAuthenticated);
const shareableSubcategoryUrl = () => {
    const url = new URL(`/${currentLocale()}`, window.location.origin);
    if (currentSubcategory?.slug) url.searchParams.set('subcategory', currentSubcategory.slug);
    if (selectedProduct?.id) url.searchParams.set('product', selectedProduct.id);
    return url.toString();
};

const money = (value) => `$${Number(value || 0).toFixed(2)}`;
const localizedField = (item, key) => firstNonEmpty(
    item?.[`${key}_${currentLocale()}`],
    item?.[key],
    item?.[`${key}_en`],
    item?.[`${key}_ar`],
);
const localized = (item, key = 'name') => key === 'name'
    ? firstNonEmpty(
        item?.[`${key}_${currentLocale()}`],
        item?.[key],
        item?.name,
        item?.name_en,
        item?.name_ar,
    )
    : localizedField(item, key);
const imageUrl = (item, fallback = '/meacash-logo-64.webp') => item?.image || fallback;
const descriptionOf = (item) => localizedField(item, 'description');
const redeemOf = (item) => localizedField(item, 'how_to_redeem');
const selectedImage = () => selectedPackage?.image || selectedProduct?.image || currentSubcategory?.image || '/meacash-logo-64.webp';
const deliveryLabel = (item) => {
    const type = item?.delivery_type || '';
    if (!type) return '';

    const labels = {
        instant: { en: 'Instant', ar: 'فوري' },
        fast: { en: 'Fast', ar: 'سريع' },
        timed: { en: 'Timed', ar: 'مجدول' },
        slow: { en: 'Slow', ar: 'بطيء' },
        manual: { en: 'Manual', ar: 'يدوي' },
    };
    const label = labels[type]?.[currentLocale()] || String(type).replace(/_/g, ' ');
    const minutes = item?.delivery_time_minutes || '';

    return minutes ? `${label} · ${minutes}m` : label;
};

const orderTypeLabel = (product) => {
    const type = product?.product_type ? String(product.product_type) : '';
    if (!type) return '';

    const labels = {
        fixed_package: 'Key',
        custom_quantity: 'Top Up',
        account_topup: 'Account',
        manual_service: 'Manual Service',
    };

    return labels[type] || type.replace(/_/g, ' ');
};

const friendlyType = (product) => {
    return [orderTypeLabel(product), deliveryLabel(product)].filter(Boolean).join(' / ');
};
const normalizedText = (value) => String(value ?? '').trim();
const firstNonEmpty = (...values) => values.map(normalizedText).find((value) => value !== '') || '';
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
        const quantity = Number.isFinite(currentQuantity) && currentQuantity > 0 ? currentQuantity : 0;
        return Number(selectedProduct.selling_price || 0) * quantity;
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
    expandedContent = {
        description: false,
        redeem: false,
    };
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
    expandedContent = {
        description: false,
        redeem: false,
    };

    const url = new URL(window.location.href);
    if (url.searchParams.has('subcategory') || url.searchParams.has('product')) {
        url.searchParams.delete('subcategory');
        url.searchParams.delete('product');
        window.history.replaceState({}, '', url.toString());
    }
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
        const res = await fetch(`${API_BASE()}${encodeURIComponent(slug)}?_=${Date.now()}`, {
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
    const categoryName = currentSubcategory.category?.name || currentSubcategory.category_name || '';
    const subcategoryDescription = descriptionOf(currentSubcategory);
    const subcategoryHowToRedeem = redeemOf(currentSubcategory);

    header.innerHTML = `
        <div class="h-11 w-1.5 shrink-0 rounded-full bg-primary-container shadow-[0_0_35px_rgba(0,240,255,0.35)]"></div>
        <div class="min-w-0">
            <h1 class="truncate font-headline text-2xl font-black uppercase leading-none tracking-tighter md:text-3xl">
                ${escapeHtml(name)} <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-container to-secondary-container">Vault</span>
            </h1>
            <p class="mt-2 font-label text-[10px] uppercase tracking-[0.24em] text-outline">
                ${[categoryName, `${currentSubcategory.products?.length || 0} assets`].filter(Boolean).map(escapeHtml).join(' / ')}
            </p>
            ${renderExpandablePanel('subcategory-description', '', subcategoryDescription, {
                plain: true,
                previewLimit: 180,
                wrapperClass: 'mt-2 max-w-xl',
                bodyClass: 'text-xs leading-relaxed text-on-surface-variant/75',
                buttonClass: 'mt-2 font-label text-[10px] font-black uppercase tracking-widest text-secondary-container hover:text-primary-container',
            })}
            ${renderExpandablePanel('subcategory-redeem', isRtl() ? 'طريقة الاسترداد' : 'How To Redeem', subcategoryHowToRedeem, {
                previewLimit: 140,
                wrapperClass: 'mt-3 max-w-xl rounded-2xl border border-outline-variant/10 bg-surface-container-lowest/35 p-3.5',
                bodyClass: 'text-xs leading-relaxed text-on-surface-variant/80',
            })}
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
            class="group relative flex min-h-[118px] flex-col rounded-xl border p-2 text-start transition-all duration-300 sm:min-h-[178px] sm:rounded-2xl sm:p-3 ${active ? 'border-primary-container bg-surface-container-high shadow-[0_0_22px_rgba(0,240,255,0.2)] ring-1 ring-primary-container/70' : 'border-transparent bg-surface-container-low hover:-translate-y-1 hover:border-primary-container/30 hover:bg-surface-container-high'}">
            ${badge}
            ${active ? `<span class="material-symbols-outlined absolute top-2 ${isRtl() ? 'left-2' : 'right-2'} text-lg text-primary-container" style="font-variation-settings: 'FILL' 1;">check_circle</span>` : ''}
            <div class="mb-2 flex aspect-square max-h-[82px] items-center justify-center overflow-hidden rounded-xl bg-surface-container-lowest/60 sm:mb-3 sm:max-h-none">
                <img src="${escapeHtml(imageUrl({ image: item.image }))}" alt="${escapeHtml(item.title)}" class="h-full w-full object-contain p-1 sm:object-cover sm:p-0" loading="lazy" decoding="async" onerror="this.onerror=null; this.src='/meacash-logo-128.png'">
            </div>
            <div class="line-clamp-2 min-h-[26px] font-headline text-[9px] font-black uppercase leading-tight text-on-surface sm:min-h-[42px] sm:text-[13px]">${escapeHtml(item.title)}</div>
            <div class="mt-1 truncate font-label text-[7px] font-bold uppercase tracking-[0.16em] text-outline sm:mt-2 sm:text-[9px]">${escapeHtml(item.subtitle)}</div>
            <div class="mt-auto pt-1.5 font-headline text-[13px] font-black text-primary-container sm:pt-3 sm:text-base">${money(item.price)}</div>
        </button>
    `;
}

function renderExpandablePanel(type, label, content, options = {}) {
    if (!content) return '';

    const expanded = expandedContent[type] === true;
    const previewLimit = options.previewLimit ?? (type.includes('redeem') ? 120 : 160);
    const normalizedContent = String(content).trim();
    const shouldCollapse = normalizedContent.length > previewLimit;
    const previewText = shouldCollapse
        ? `${normalizedContent.slice(0, previewLimit).trimEnd()}...`
        : normalizedContent;
    const bodyText = expanded || !shouldCollapse ? normalizedContent : previewText;
    const toggleLabel = expanded
        ? (isRtl() ? 'Ø¹Ø±Ø¶ Ø£Ù‚Ù„' : 'Show less')
        : (isRtl() ? 'Ø¹Ø±Ø¶ Ø§Ù„Ù…Ø²ÙŠØ¯' : 'Load more');
    const wrapperClass = options.wrapperClass ?? 'mb-5 rounded-2xl border border-outline-variant/10 bg-surface-container-lowest/35 p-4';
    const labelClass = options.labelClass ?? 'mb-2 font-label text-[10px] font-black uppercase tracking-widest text-primary-container';
    const bodyClass = options.bodyClass ?? 'text-xs leading-relaxed text-on-surface-variant';
    const buttonClass = options.buttonClass ?? 'mt-3 font-label text-[10px] font-black uppercase tracking-widest text-secondary-container hover:text-primary-container';

    if (options.plain) {
        return `
            <div class="${wrapperClass}">
                <div class="${bodyClass}">${escapeHtml(bodyText)}</div>
                ${shouldCollapse ? `
                    <button type="button" data-toggle-panel="${escapeHtml(type)}" class="${buttonClass}">
                        ${toggleLabel}
                    </button>
                ` : ''}
            </div>
        `;
    }

    return `
        <div class="${wrapperClass}">
            <div class="${labelClass}">${escapeHtml(label)}</div>
            <div class="${bodyClass}">${escapeHtml(bodyText)}</div>
            ${shouldCollapse ? `
                <button type="button" data-toggle-panel="${escapeHtml(type)}" class="${buttonClass}">
                    ${toggleLabel}
                </button>
            ` : ''}
        </div>
    `;
}

function renderSummary() {
    const summary = getSummary();
    if (!summary || !selectedProduct) return;

    const title = selectedPackage ? localized(selectedPackage) : localized(selectedProduct);
    const subtitle = firstNonEmpty(
        descriptionOf(selectedProduct),
        descriptionOf(currentSubcategory),
        localized(selectedProduct)
    );
    const productHowToRedeem = redeemOf(selectedProduct);
    const type = friendlyType(selectedProduct);
    const activeForm = selectedProduct.forms?.find((form) => form.key === selectedFormKey) || selectedProduct.forms?.[0] || null;
    const fields = [
        ...(selectedProduct.fields || []),
        ...(activeForm?.fields || []),
    ];

    summary.innerHTML = `
        <div>
            <h2 class="mb-3 font-label text-[11px] font-bold uppercase tracking-widest text-outline">Selected Product</h2>
            <div class="mb-4 rounded-[1.35rem] border border-outline-variant/10 bg-surface-container-highest/55 p-3 shadow-[0_12px_32px_rgba(15,23,42,0.08)]">
                <div class="grid grid-cols-1 items-start gap-3 sm:grid-cols-[72px_minmax(0,1fr)]">
                    <div class="hidden h-[72px] w-[72px] overflow-hidden rounded-[1rem] border border-outline-variant/15 bg-surface sm:block">
                        <img src="${escapeHtml(selectedImage())}" alt="${escapeHtml(title)}" class="h-full w-full object-cover" loading="lazy" decoding="async" onerror="this.onerror=null; this.src='/meacash-logo-128.png'">
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1 pr-2">
                                <div class="line-clamp-2 font-headline text-[14px] font-black uppercase leading-tight text-on-surface sm:text-[15px]">${escapeHtml(title)}</div>
                                <div class="mt-1 font-label text-[8px] uppercase tracking-[0.18em] text-primary-container sm:text-[9px]">${escapeHtml(type)}</div>
                            </div>
                            <div class="shrink-0 text-end">
                                <div id="modal-live-price" class="font-headline text-[17px] font-black leading-none text-primary-container sm:text-[18px]">${money(selectedUnitPrice())}</div>
                                <div class="mt-1 font-label text-[9px] uppercase tracking-tight text-outline">${__('total_price')}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            ${renderExpandablePanel('product-description', isRtl() ? 'الوصف' : 'Description', subtitle)}
            ${renderExpandablePanel('product-redeem', isRtl() ? 'طريقة الاسترداد' : 'How To Redeem', productHowToRedeem)}

            ${renderFormTabs()}
            ${renderQuantity()}
            <div class="space-y-4">${fields.map(renderField).join('')}</div>
            <div id="modal-inline-toast">${currentToast}</div>
        </div>
    `;
}

function closePurchaseConfirmModal() {
    document.getElementById('purchase-confirm-backdrop')?.remove();
}

function openPurchaseConfirmModal(onConfirm) {
    closePurchaseConfirmModal();

    const title = selectedPackage ? localized(selectedPackage) : localized(selectedProduct);
    const quantity = selectedProduct?.product_type === 'custom_quantity' ? currentQuantity : 1;
    const backdrop = document.createElement('div');
    backdrop.id = 'purchase-confirm-backdrop';
    backdrop.className = 'fixed inset-0 z-[120] flex items-center justify-center bg-background/85 p-4 backdrop-blur-xl';
    backdrop.innerHTML = `
        <div class="absolute inset-0" data-close-purchase-confirm="1"></div>
        <div class="relative w-full max-w-md rounded-[28px] border border-outline-variant/20 bg-surface-container p-6 shadow-[0_28px_90px_rgba(0,0,0,0.65)]">
            <button type="button" data-close-purchase-confirm="1" class="absolute end-4 top-4 flex h-9 w-9 items-center justify-center rounded-full bg-surface-container-lowest text-outline transition hover:text-secondary-container">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
            <div class="mb-6 pe-10">
                <p class="font-headline text-xl font-black uppercase tracking-tight text-on-surface">
                    ${escapeHtml(isRtl() ? 'تأكيد الشراء' : 'Confirm Purchase')}
                </p>
                <p class="mt-2 text-xs leading-relaxed text-on-surface-variant">
                    ${escapeHtml(isRtl() ? 'هل أنت متأكد أنك تريد شراء هذا المنتج؟' : 'Are you sure you want to purchase this product?')}
                </p>
            </div>
            <div class="rounded-2xl border border-outline-variant/10 bg-surface-container-lowest/35 p-4">
                <div class="font-headline text-sm font-black uppercase text-on-surface">${escapeHtml(title)}</div>
                <div class="mt-2 flex items-center justify-between gap-3 text-xs text-on-surface-variant">
                    <span>${escapeHtml(isRtl() ? 'الكمية' : 'Quantity')}</span>
                    <span class="font-black text-on-surface">${escapeHtml(String(quantity))}</span>
                </div>
                <div class="mt-2 flex items-center justify-between gap-3 text-xs text-on-surface-variant">
                    <span>${escapeHtml(isRtl() ? 'السعر الإجمالي' : 'Total Price')}</span>
                    <span class="font-headline text-base font-black text-primary-container">${escapeHtml(money(selectedUnitPrice()))}</span>
                </div>
            </div>
            <div class="mt-6 flex gap-3">
                <button type="button" data-close-purchase-confirm="1" class="flex-1 rounded-2xl border border-outline-variant/20 bg-surface-container-low px-4 py-3 font-label text-[11px] font-black uppercase tracking-[0.2em] text-on-surface-variant transition hover:bg-surface-container-high">
                    ${escapeHtml(isRtl() ? 'إلغاء' : 'Cancel')}
                </button>
                <button type="button" id="purchase-confirm-btn" class="flex-1 rounded-2xl bg-gradient-to-r from-primary-container to-secondary-container px-4 py-3 font-headline text-[11px] font-black uppercase tracking-[0.2em] text-on-primary-container transition hover:scale-[1.01] active:scale-[0.99]">
                    ${escapeHtml(isRtl() ? 'تأكيد الشراء' : 'Confirm Purchase')}
                </button>
            </div>
        </div>
    `;

    document.body.appendChild(backdrop);

    backdrop.querySelectorAll('[data-close-purchase-confirm="1"]').forEach((element) => {
        element.addEventListener('click', closePurchaseConfirmModal);
    });

    backdrop.querySelector('#purchase-confirm-btn')?.addEventListener('click', () => {
        closePurchaseConfirmModal();
        onConfirm();
    });
}

function renderExpandablePanelLegacy(type, label, content, options = {}) {
    if (!content) return '';

    const expanded = expandedContent[type] === true;
    const previewLimit = options.previewLimit ?? (type.includes('redeem') ? 120 : 160);
    const normalizedContent = String(content).trim();
    const shouldCollapse = normalizedContent.length > previewLimit;
    const previewText = shouldCollapse
        ? `${normalizedContent.slice(0, previewLimit).trimEnd()}...`
        : normalizedContent;
    const bodyText = expanded || !shouldCollapse ? normalizedContent : previewText;
    const toggleLabel = expanded
        ? (isRtl() ? 'عرض أقل' : 'Show less')
        : (isRtl() ? 'عرض المزيد' : 'Load more');

    return `
        <div class="${wrapperClass}">
            <div class="${labelClass}">${escapeHtml(label)}</div>
            <div class="${bodyClass}">${escapeHtml(bodyText)}</div>
            ${shouldCollapse ? `
                <button type="button" data-toggle-panel="${escapeHtml(type)}" class="${buttonClass}">
                    ${toggleLabel}
                </button>
            ` : ''}
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
                <label class="font-label text-[10px] font-bold uppercase tracking-widest text-outline">${__('custom_quantity')}</label>
                <div class="font-label text-[9px] uppercase tracking-widest text-primary-container">
                    ${__('min', {val: Number(selectedProduct.min_quantity || 1)})} / ${__('max', {val: selectedProduct.max_quantity ? Number(selectedProduct.max_quantity) : '∞'})}
                </div>
            </div>
            <input id="qty-input" type="number" min="${selectedProduct.min_quantity || 1}" max="${selectedProduct.max_quantity || ''}" value="${currentQuantity}"
                class="w-full rounded-xl border border-outline-variant/20 bg-surface-container-lowest px-4 py-3 font-headline text-lg font-black text-secondary-container placeholder:text-outline-variant outline-none transition focus:border-primary-container focus:ring-2 focus:ring-primary-container/15">
            <div id="err-quantity" class="mt-2 px-1 font-label text-[10px] uppercase tracking-widest text-error hidden"></div>
            <p class="mt-2 px-1 font-label text-[10px] uppercase tracking-widest text-outline">Rate: ${money(selectedProduct.price_per_unit || selectedProduct.selling_price)} each</p>
        </div>
    `;
}

function renderField(field) {
    const label = `${escapeHtml(field.label)}${field.required ? ' <span class="text-secondary-container">*</span>' : ''}`;
    const isNumber = field.type === 'number';
    const min = field.min !== null && field.min !== undefined && field.min !== '' ? Number(field.min) : null;
    const max = field.max !== null && field.max !== undefined && field.max !== '' ? Number(field.max) : null;
    const numberAttrs = isNumber
        ? `${Number.isFinite(min) ? ` min="${escapeHtml(min)}"` : ''}${Number.isFinite(max) ? ` max="${escapeHtml(max)}"` : ''} inputmode="decimal"`
        : '';
    const rangeHint = isNumber && (Number.isFinite(min) || Number.isFinite(max))
        ? `<p class="mt-1 ms-1 font-label text-[9px] uppercase tracking-widest text-outline">${[
            Number.isFinite(min) ? `Min: ${escapeHtml(compactNumber(min))}` : '',
            Number.isFinite(max) ? `Max: ${escapeHtml(compactNumber(max))}` : '',
        ].filter(Boolean).join(' / ')}</p>`
        : '';

    if (field.type === 'select') {
        const options = (field.options || []).map((option) => {
            const value = typeof option === 'object' ? (option.value ?? option.label) : option;
            const text = typeof option === 'object' ? (option.label ?? option.value) : option;
            return `<option value="${escapeHtml(value)}">${escapeHtml(text)}</option>`;
        }).join('');

        return `
            <div class="group">
                <label class="mb-2 ms-1 block font-label text-[10px] font-bold uppercase tracking-widest text-outline">${label}</label>
                <select name="form_data[${escapeHtml(field.key)}]" data-field-key="${escapeHtml(field.key)}" class="w-full rounded-xl border border-outline-variant/20 bg-surface-container-lowest px-4 py-3 text-sm text-on-surface outline-none transition focus:border-primary-container focus:ring-2 focus:ring-primary-container/15">
                    <option value="">${escapeHtml(field.placeholder || __('select_option'))}</option>
                    ${options}
                </select>
                <div id="err-${escapeHtml(field.key)}" class="mt-1 hidden font-label text-[10px] uppercase tracking-widest text-error"></div>
            </div>
        `;
    }

    return `
        <div class="group">
            <label class="mb-2 ms-1 block font-label text-[10px] font-bold uppercase tracking-widest text-outline">${label}</label>
            <input type="${escapeHtml(field.type || 'text')}" name="form_data[${escapeHtml(field.key)}]" data-field-key="${escapeHtml(field.key)}" placeholder="${escapeHtml(field.placeholder || '')}"${numberAttrs}
                class="w-full rounded-xl border border-outline-variant/20 bg-surface-container-lowest px-4 py-3 text-sm text-on-surface placeholder:text-outline-variant outline-none transition focus:border-primary-container focus:ring-2 focus:ring-primary-container/15">
            ${rangeHint}
            <div id="err-${escapeHtml(field.key)}" class="mt-1 hidden font-label text-[10px] uppercase tracking-widest text-error"></div>
        </div>
    `;
}

function renderFooter() {
    const footer = getFooter();
    if (!footer || !selectedProduct) return;

    const shareBtn = `
        <button id="share-btn" type="button" class="flex h-[54px] w-[54px] shrink-0 items-center justify-center rounded-[1.15rem] border border-outline-variant/20 bg-surface-container-lowest/75 text-outline transition-all hover:border-secondary-container/50 hover:bg-secondary-container/10 hover:text-secondary-container">
            <span class="material-symbols-outlined text-xl">share</span>
        </button>
    `;

    if (!isAuthenticated()) {
        footer.innerHTML = `
            <div class="grid grid-cols-[minmax(0,1fr)] gap-2.5 sm:grid-cols-[minmax(0,1fr)_54px] sm:gap-3">
                <a href="${LOGIN_URL()}" class="flex min-h-[54px] items-center justify-center gap-2.5 rounded-[1.2rem] border border-primary-container/30 bg-surface-container-high px-4 py-3.5 font-headline text-[11px] font-black uppercase tracking-wider text-primary-container shadow-[0_0_28px_rgba(0,240,255,0.12)] transition-all hover:border-primary-container hover:bg-primary-container hover:text-on-primary-container md:gap-3 md:py-4 md:text-sm md:tracking-widest">
                    <span class="material-symbols-outlined text-lg">lock</span>
                    <span>${isRtl() ? 'سجل الدخول أولاً' : 'Login First'}</span>
                </a>
                <div class="hidden sm:block">${shareBtn}</div>
            </div>
            <p class="mt-3 text-center font-label text-[10px] uppercase tracking-widest text-outline">
                ${isRtl() ? 'يجب تسجيل الدخول لإكمال الشراء' : 'Please login to purchase this product'}
            </p>
        `;
        return;
    }

    footer.innerHTML = `
        <div class="grid grid-cols-[minmax(0,1fr)] gap-2.5 sm:grid-cols-[minmax(0,1fr)_54px] sm:gap-3">
            <button id="purchase-now-btn" type="button" class="flex min-h-[56px] items-center justify-center gap-2.5 rounded-[1.2rem] bg-gradient-to-r from-primary-container via-[#18bfff] to-secondary-container px-4 py-3.5 font-headline text-[11px] font-black uppercase tracking-wider text-on-primary-container shadow-[0_18px_40px_rgba(0,240,255,0.22)] transition-all hover:brightness-105 active:scale-[0.99] disabled:cursor-not-allowed disabled:opacity-60 md:gap-3 md:py-4 md:text-sm md:tracking-widest">
                <span class="whitespace-nowrap">${isRtl() ? 'شراء الآن' : 'Purchase Now'}</span>
                <span class="material-symbols-outlined shrink-0">bolt</span>
            </button>
            <div class="hidden sm:block">${shareBtn}</div>
        </div>
    `;
}

function bindEvents() {
    document.querySelectorAll('[data-select-product]').forEach((button) => {
        button.onclick = () => {
            const productId = Number(button.dataset.selectProduct);
            const packageId = button.dataset.selectPackage ? Number(button.dataset.selectPackage) : null;
            const product = currentSubcategory.products.find((item) => Number(item.id) === productId);
            if (!product) return;

            syncProductDefaults(product);
            selectedPackage = packageId ? product.packages?.find((item) => Number(item.id) === packageId) || null : null;
            currentToast = '';
            render();
        };
    });

    document.querySelectorAll('[data-form-key]').forEach((button) => {
        button.onclick = () => {
            selectedFormKey = button.dataset.formKey;
            currentToast = '';
            renderSummary();
            renderFooter();
            bindEvents();
        };
    });

    document.querySelectorAll('[data-toggle-panel]').forEach((button) => {
        button.onclick = () => {
            const panel = button.dataset.togglePanel;
            expandedContent[panel] = !expandedContent[panel];
            
            if (panel.startsWith('subcategory-')) {
                renderHeader();
            } else {
                renderSummary();
            }
            bindEvents();
        };
    });

    const quantityInput = document.getElementById('qty-input');
    if (quantityInput) {
        quantityInput.oninput = (event) => {
            const rawValue = String(event.target.value ?? '').trim();
            currentQuantity = rawValue === '' ? Number.NaN : Number(rawValue);
            validateQuantityField(false);
            const livePrice = document.getElementById('modal-live-price');
            if (livePrice) livePrice.textContent = money(selectedUnitPrice());
        };

        quantityInput.onblur = () => {
            validateQuantityField(true);
        };
    }

    document.querySelectorAll('[data-field-key]').forEach((input) => {
        const validateCurrentField = () => {
            validateDynamicField(input);
            const livePrice = document.getElementById('modal-live-price');
            if (livePrice) livePrice.textContent = money(selectedUnitPrice());
        };

        input.oninput = validateCurrentField;
        input.onchange = validateCurrentField;
        input.onblur = validateCurrentField;
    });

    const purchaseNowBtn = document.getElementById('purchase-now-btn');
    if (purchaseNowBtn) purchaseNowBtn.onclick = handlePurchaseNow;
    
    const shareBtn = document.getElementById('share-btn');
    if (shareBtn) shareBtn.onclick = handleShare;
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

function setCurrentToast(message = '') {
    currentToast = message;
    const host = document.getElementById('modal-inline-toast');
    if (host) host.innerHTML = message;
}

function activeFields() {
    const activeForm = selectedProduct?.forms?.find((form) => form.key === selectedFormKey) || selectedProduct?.forms?.[0] || null;

    return [
        ...(selectedProduct?.fields || []),
        ...(activeForm?.fields || []),
    ];
}

function hasFieldErrors(errors) {
    return Object.keys(errors || {}).length > 0;
}

function setFieldError(fieldKey, message = '') {
    const el = document.getElementById(`err-${fieldKey}`);
    if (!el) return;

    if (message) {
        el.textContent = message;
        el.classList.remove('hidden');
        return;
    }

    el.textContent = '';
    el.classList.add('hidden');
}

function focusFieldByKey(fieldKey) {
    const input = fieldKey === 'quantity'
        ? document.getElementById('qty-input')
        : document.querySelector(`[data-field-key="${CSS.escape(fieldKey)}"]`);

    if (!input) return;

    input.focus({ preventScroll: true });
    input.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'nearest' });
}

function validateDynamicField(input) {
    if (!input?.dataset?.fieldKey) return true;

    const field = activeFields().find((item) => item.key === input.dataset.fieldKey);
    if (!field) return true;

    const rawValue = String(input.value ?? '').trim();

    if (field.required && rawValue === '') {
        setFieldError(field.key, __('required'));
        return false;
    }

    if (rawValue === '') {
        setFieldError(field.key);
        return true;
    }

    if (field.type === 'number') {
        const numericValue = Number(rawValue);
        const min = field.min !== null && field.min !== undefined && field.min !== '' ? Number(field.min) : null;
        const max = field.max !== null && field.max !== undefined && field.max !== '' ? Number(field.max) : null;

        if (!Number.isFinite(numericValue)) {
            setFieldError(field.key, __('invalid_value'));
            return false;
        }

        if ((Number.isFinite(min) && numericValue < min) || (Number.isFinite(max) && numericValue > max)) {
            setFieldError(field.key, [
                Number.isFinite(min) ? __('min', { val: compactNumber(min) }) : '',
                Number.isFinite(max) ? __('max', { val: compactNumber(max) }) : '',
            ].filter(Boolean).join(' / '));
            return false;
        }
    }

    setFieldError(field.key);
    return true;
}

function validateQuantityField(showRequired = false) {
    if (selectedProduct?.product_type !== 'custom_quantity') return true;

    const input = document.getElementById('qty-input');
    if (!input) return true;

    const rawValue = String(input.value ?? '').trim();
    const min = Number(selectedProduct.min_quantity || 1);
    const max = Number(selectedProduct.max_quantity || Number.MAX_SAFE_INTEGER);

    if (rawValue === '') {
        setFieldError('quantity', showRequired ? __('required') : '');
        return !showRequired;
    }

    const numericValue = Number(rawValue);
    if (!Number.isFinite(numericValue)) {
        setFieldError('quantity', __('invalid_value'));
        return false;
    }

    if (numericValue < min || numericValue > max) {
        setFieldError('quantity', [
            __('min', { val: compactNumber(min) }),
            Number.isFinite(max) && max !== Number.MAX_SAFE_INTEGER ? __('max', { val: compactNumber(max) }) : '',
        ].filter(Boolean).join(' / '));
        return false;
    }

    setFieldError('quantity');
    return true;
}

function showErrors(errors) {
    let firstFieldKey = null;

    Object.entries(errors || {}).forEach(([key, messages]) => {
        // Handle "form_data.field_key" and flat "quantity" or other keys
        const fieldKey = key.startsWith('form_data.') ? key.split('.').pop() : key;
        if (!firstFieldKey) firstFieldKey = fieldKey;
        setFieldError(fieldKey, messages[0] || __('invalid_value'));
    });

    if (firstFieldKey) {
        focusFieldByKey(firstFieldKey);
    }
}

function validatePurchaseInputs() {
    const errors = {};

    activeFields().forEach((field) => {
        const input = document.querySelector(`[data-field-key="${CSS.escape(field.key)}"]`);
        if (!input) return;

        const rawValue = String(input.value ?? '').trim();

        if (field.required && rawValue === '') {
            errors[field.key] = [__('required')];
            return;
        }

        if (rawValue === '') {
            return;
        }

        if (field.type === 'number') {
            const numericValue = Number(rawValue);
            const min = field.min !== null && field.min !== undefined && field.min !== '' ? Number(field.min) : null;
            const max = field.max !== null && field.max !== undefined && field.max !== '' ? Number(field.max) : null;

            if (!Number.isFinite(numericValue)) {
                errors[field.key] = [__('invalid_value')];
                return;
            }

            if ((Number.isFinite(min) && numericValue < min) || (Number.isFinite(max) && numericValue > max)) {
                errors[field.key] = [[
                    Number.isFinite(min) ? __('min', { val: compactNumber(min) }) : '',
                    Number.isFinite(max) ? __('max', { val: compactNumber(max) }) : '',
                ].filter(Boolean).join(' / ')];
            }
        }
    });

    if (selectedProduct?.product_type === 'custom_quantity') {
        const quantityInput = document.getElementById('qty-input');
        const min = Number(selectedProduct.min_quantity || 1);
        const max = Number(selectedProduct.max_quantity || Number.MAX_SAFE_INTEGER);
        const rawQuantity = String(quantityInput?.value ?? '').trim();

        if (rawQuantity === '') {
            errors.quantity = [__('required')];
            return errors;
        }

        if (!Number.isFinite(currentQuantity) || currentQuantity < min || currentQuantity > max) {
            errors.quantity = [[
                __('min', { val: compactNumber(min) }),
                Number.isFinite(max) && max !== Number.MAX_SAFE_INTEGER ? __('max', { val: compactNumber(max) }) : '',
            ].filter(Boolean).join(' / ')];
        }
    }

    return errors;
}

async function handlePurchaseNow() {
    if (!selectedProduct) return;

    clearErrors();
    setCurrentToast('');
    const validationErrors = validatePurchaseInputs();

    if (hasFieldErrors(validationErrors)) {
        showErrors(validationErrors);
        return;
    }

    openPurchaseConfirmModal(() => executePurchaseNow());
}

async function executePurchaseNow() {
    if (!selectedProduct) return;

    const button = document.getElementById('purchase-now-btn');
    const formData = {};

    clearErrors();
    setCurrentToast('');
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
    button.innerHTML = `<span class="animate-pulse">${__('processing')}</span>`;

    try {
        const res = await fetch(PURCHASE_URL(), {
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
            window.location.href = LOGIN_URL();
            return;
        }

        const data = await res.json();

        if (!res.ok) {
            setCurrentToast(hasFieldErrors(data.errors)
                ? ''
                : `<div class="mt-5 rounded-xl border border-error/30 bg-error-container/10 p-3 font-label text-xs uppercase tracking-widest text-error">${escapeHtml(data.message || __('review_errors'))}</div>`);
            showErrors(data.errors || {});
            return;
        }

        button.innerHTML = `<span class="animate-pulse">${isRtl() ? 'جاري تأكيد الطلب...' : 'Confirming order...'}</span>`;

        const checkoutRes = await fetch(CHECKOUT_URL(), {
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

            setCurrentToast(`<div class="mt-5 rounded-xl border border-error/30 bg-error-container/10 p-3 font-label text-xs uppercase tracking-widest text-error">${escapeHtml(checkoutData.message || 'Could not complete purchase.')}</div>`);
            return;
        }

        window.location.href = checkoutData.redirect_url || data.redirect_url || CHECKOUT_URL();
    } catch (error) {
        console.error('Purchase Error:', error);
        setCurrentToast(`<div class="mt-5 rounded-xl border border-error/30 bg-error-container/10 p-3 font-label text-xs uppercase tracking-widest text-error">Could not start purchase. Please try again.</div>`);
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
