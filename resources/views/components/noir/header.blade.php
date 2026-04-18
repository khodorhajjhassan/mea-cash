<header class="bg-[#111319]/60 backdrop-blur-xl sticky top-0 z-50 border-b border-[#3b494b]/15 shadow-[0_8px_32px_rgba(0,0,0,0.5)]">
    <nav class="flex justify-between items-center w-full max-w-[1440px] mx-auto px-4 md:px-8 h-20">
        <div class="flex items-center gap-4 md:gap-8">
            <a href="{{ route('store.home') }}" class="flex items-center gap-3 group">
                <img src="{{ asset('meacash-logo.png') }}" alt="MeaCash" class="h-10 w-auto group-hover:scale-105 transition-transform">
                <span class="hidden sm:inline-block text-xl md:text-2xl font-black italic tracking-tighter text-transparent bg-clip-text bg-gradient-to-r from-[#00f0ff] to-[#fe00fe]">
                    {{ config('app.name', 'MEACASH') }}
                </span>
            </a>
            
            <div class="hidden md:flex items-center gap-8 font-headline uppercase tracking-widest text-sm">
                <a class="text-[#00f0ff] font-bold border-b-2 border-[#00f0ff] pb-1 hover:text-[#fe00fe] hover:scale-105 transition-all duration-300" href="#">{{ __('Explore') }}</a>
                <a class="text-slate-400 hover:text-[#fe00fe] hover:scale-105 transition-all duration-300" href="#">{{ __('Trending') }}</a>
                <a class="text-slate-400 hover:text-[#fe00fe] hover:scale-105 transition-all duration-300" href="#">{{ __('Collections') }}</a>
            </div>
        </div>

        <div class="flex items-center gap-3 md:gap-6">
            <div class="hidden lg:block relative group/search flex-grow max-w-xl mx-8">
                <form action="{{ route('store.home.locale', ['locale' => app()->getLocale()]) }}" method="GET" class="flex items-center bg-surface-container-highest px-4 py-3 rounded-full border border-outline-variant/30 focus-within:border-primary-container/60 focus-within:bg-surface-container-lowest focus-within:shadow-[0_0_25px_rgba(0,240,255,0.15)] transition-all duration-500 w-full xl:w-80 group-focus-within/search:w-full">
                    <span id="search-icon" class="material-symbols-outlined text-outline text-lg transition-colors group-focus-within/search:text-primary-container">search</span>
                    <span id="search-loader" class="material-symbols-outlined text-primary-container text-lg animate-spin" style="display: none;">refresh</span>
                    <input id="navbar-search" 
                           class="bg-transparent border-none focus:ring-0 text-sm font-label tracking-widest flex-grow uppercase text-on-surface placeholder:text-outline/40 ps-3 outline-none" 
                           placeholder="{{ __('noir.search_placeholder') ?? 'SEARCH THE VAULT...' }}" 
                           type="text"
                           name="q"
                           autocomplete="off"
                           value="{{ request('q') }}">
                    <button type="submit" class="hidden group-focus-within/search:flex items-center gap-1 bg-primary-container text-on-primary-container px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest hover:scale-105 transition-transform ml-2">
                        {{ __('Search') }}
                    </button>
                </form>

                {{-- Live Results Dropdown --}}
                <div id="search-results" class="absolute top-full left-0 right-0 mt-2 glass-panel rounded-2xl overflow-hidden hidden z-[60] shadow-[0_20px_50px_rgba(0,0,0,0.5)] border-primary-container/20">
                    <div id="search-results-list" class="max-h-[400px] overflow-y-auto no-scrollbar">
                        {{-- Results populated via JS --}}
                    </div>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const input = document.getElementById('navbar-search');
                    const results = document.getElementById('search-results');
                    const list = document.getElementById('search-results-list');
                    const icon = document.getElementById('search-icon');
                    const loader = document.getElementById('search-loader');
                    let timeout = null;

                    input.addEventListener('input', function() {
                        const q = this.value.trim();
                        clearTimeout(timeout);

                        if (q.length < 3) {
                            results.classList.add('hidden');
                            icon.style.display = 'inline-block';
                            loader.style.display = 'none';
                            return;
                        }

                        timeout = setTimeout(() => {
                            icon.style.display = 'none';
                            loader.style.display = 'inline-block';

                            fetch(`{{ route('store.search') }}?q=${encodeURIComponent(q)}`, {
                                headers: { 'Accept': 'application/json' }
                            })
                            .then(res => res.json())
                            .then(data => {
                                icon.style.display = 'inline-block';
                                loader.style.display = 'none';
                                
                                if (data.results && data.results.length > 0) {
                                    list.innerHTML = data.results.map(item => `
                                        <div onclick="openSubcategoryModalBySearch('${item.slug}')" class="p-3 border-b border-outline-variant/10 hover:bg-primary-container/10 cursor-pointer flex items-center gap-3 transition-colors">
                                            <div class="w-10 h-10 rounded-lg bg-surface-container-highest overflow-hidden p-1 shrink-0">
                                                <img src="${item.image || '/meacash-logo.png'}" class="w-full h-full object-contain">
                                            </div>
                                            <div class="flex-grow min-w-0">
                                                <div class="text-[10px] font-black uppercase text-primary-container truncate">${item.category_name}</div>
                                                <div class="text-xs font-headline font-bold text-on-surface truncate">${item.name}</div>
                                            </div>
                                            <div class="text-[10px] font-black text-on-surface/50">$${item.price.toFixed(2)}</div>
                                        </div>
                                    `).join('');
                                    results.classList.remove('hidden');
                                } else {
                                    list.innerHTML = `<div class="p-6 text-center text-[10px] uppercase tracking-widest text-outline">No matches found</div>`;
                                    results.classList.remove('hidden');
                                }
                            })
                            .catch(() => {
                                icon.style.display = 'inline-block';
                                loader.style.display = 'none';
                            });
                        }, 500);
                    });

                    // Global helper to link search results to modal
                    window.openSubcategoryModalBySearch = function(slug) {
                        if (window.openSubcategoryModal) {
                            window.openSubcategoryModal(slug);
                            results.classList.add('hidden');
                            input.value = '';
                        } else {
                            window.location.href = `{{ route('store.home') }}?subcategory=${slug}`;
                        }
                    };

                    document.addEventListener('click', (e) => {
                        if (!input.contains(e.target) && !results.contains(e.target)) {
                            results.classList.add('hidden');
                        }
                    });
                });
            </script>

            <div class="flex items-center gap-2 md:gap-4 text-primary-container">
                {{-- Language Switcher --}}
                <div class="flex items-center border-s border-outline-variant/20 ps-4 ms-2 md:ms-0">
                    <a href="{{ route('store.home.locale', ['locale' => app()->getLocale() == 'en' ? 'ar' : 'en']) }}" 
                       class="flex items-center gap-1.5 text-[10px] sm:text-xs font-black font-headline px-3 py-1.5 bg-surface-container-highest rounded-full border border-outline-variant/30 hover:border-primary-container/60 hover:text-primary-container hover:shadow-[0_0_15px_rgba(0,240,255,0.2)] transition-all">
                        <span class="material-symbols-outlined text-[16px]">language</span>
                        <span>{{ app()->getLocale() == 'en' ? 'ARABIC' : 'ENGLISH' }}</span>
                    </a>
                </div>

                @auth
                    <a href="{{ route('store.dashboard') }}" class="scale-95 active:opacity-80 transition-transform">
                        <span class="material-symbols-outlined" data-icon="account_circle">account_circle</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="scale-95 active:opacity-80 transition-transform">
                        <span class="material-symbols-outlined" data-icon="login">login</span>
                    </a>
                @endauth
            </div>
        </div>
    </nav>
</header>
