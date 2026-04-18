{{-- Kinetic Noir Product/Subcategory Modal Shell --}}
<div id="sf-modal-backdrop" class="fixed inset-0 z-[100] hidden items-center justify-center overflow-hidden bg-background/85 p-3 backdrop-blur-xl" role="dialog" aria-modal="true">
    <div class="pointer-events-none absolute top-[-120px] start-[-80px] h-[420px] w-[420px] light-leak-magenta opacity-70 blur-3xl"></div>
    <div class="pointer-events-none absolute bottom-[-120px] end-[-80px] h-[420px] w-[420px] light-leak-cyan opacity-80 blur-3xl"></div>

    <div id="sf-modal-card" class="relative flex max-h-[86vh] min-h-[460px] w-full max-w-[900px] flex-col overflow-hidden rounded-[28px] border border-outline-variant/15 bg-surface-container/90 shadow-[0_26px_100px_rgba(0,0,0,0.72)] backdrop-blur-2xl md:flex-row">
        <button onclick="closeProductModal()" class="absolute top-4 end-4 z-20 flex h-10 w-10 items-center justify-center rounded-full border border-outline-variant/20 bg-surface-container-lowest/80 text-outline transition-all hover:border-secondary-container/60 hover:text-secondary-container hover:shadow-[0_0_20px_rgba(254,0,254,0.2)]">
            <span class="material-symbols-outlined text-xl">close</span>
        </button>

        <section class="flex min-h-0 flex-1 flex-col border-b border-outline-variant/10 p-5 md:border-b-0 md:border-e md:p-7">
            <div id="sf-modal-header-content" class="mb-5 flex items-center gap-3 pe-10">
                <div class="h-11 w-1.5 rounded-full bg-primary-container shadow-[0_0_30px_rgba(0,240,255,0.35)]"></div>
                <div>
                    <div class="h-7 w-48 animate-pulse rounded bg-surface-container-highest"></div>
                    <div class="mt-2 h-3 w-32 animate-pulse rounded bg-surface-container-highest/60"></div>
                </div>
            </div>

            <div id="sf-modal-body" class="min-h-0 flex-1 overflow-y-auto overflow-x-hidden pe-1 no-scrollbar">
                <div class="flex flex-col items-center justify-center py-20">
                    <div class="h-16 w-16 animate-spin rounded-full border-4 border-primary-container/20 border-t-primary-container"></div>
                    <p class="mt-4 font-label text-xs uppercase tracking-widest text-outline">Initializing Vault...</p>
                </div>
            </div>
        </section>

        <aside id="sf-modal-summary" class="flex min-h-0 w-full flex-col justify-between overflow-y-auto overflow-x-hidden bg-surface-container-low/55 p-5 md:w-[340px] md:p-6">
            <div id="sf-modal-summary-content"></div>
            <div id="sf-modal-footer" class="mt-6"></div>
        </aside>
    </div>
</div>
